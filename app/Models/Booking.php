<?php
require_once __DIR__ . '/Model.php';

class Booking extends Model {

  public static function findFlight(string $airline, string $number, string $dateYmd): ?array {
    $sql = "
      SELECT fd.*,
             a1.iata AS dep_iata, a1.name AS dep_name,
             a2.iata AS arr_iata, a2.name AS arr_name
      FROM flight_details fd
      JOIN airports a1 ON a1.id = fd.dep_airport_id
      JOIN airports a2 ON a2.id = fd.arr_airport_id
      WHERE fd.airline_code = :ac
        AND fd.flight_number = :fn
        AND fd.flight_date   = :fd
      LIMIT 1
    ";
    $st = self::db()->prepare($sql);
    $st->execute([':ac'=>$airline, ':fn'=>$number, ':fd'=>$dateYmd]);
    $row = $st->fetch();
    return $row ?: null;
  }

  public static function loungeById(int $id): ?array {
    $sql = "
      SELECT l.*, a.iata, a.name AS airport_name
      FROM lounges l
      JOIN airports a ON a.id = l.airport_id
      WHERE l.id = :id
      LIMIT 1
    ";
    $st = self::db()->prepare($sql);
    $st->execute([':id'=>$id]);
    $row = $st->fetch();
    return $row ?: null;
  }

  public static function userPlan(int $userId): ?array {
    // minimal plan info for pricing decisions
    $sql = "
      SELECT mp.slug, mp.name, mp.normal_access, mp.premium_access, mp.guest_allowance
      FROM user_memberships um
      JOIN membership_plans mp ON mp.id = um.plan_id
      WHERE um.user_id = :uid AND um.status = 'active'
      ORDER BY um.started_at DESC
      LIMIT 1
    ";
    $st = self::db()->prepare($sql);
    $st->execute([':uid'=>$userId]);
    $row = $st->fetch();
    return $row ?: ['slug'=>'basic','name'=>'Basic','normal_access'=>'pay_per_use','premium_access'=>'pay_per_use','guest_allowance'=>0];
  }

  /**
   * Returns ['method' => 'membership'|'pay_per_use', 'unit' => float, 'total' => float]
   */
  public static function quotePrice(int $userId, array $lounge, int $people): array {
    $plan = self::userPlan($userId);
    $isPremium = (int)$lounge['is_premium'] === 1;

    $covered = false;
    if ($isPremium) {
      $covered = ($plan['premium_access'] ?? 'pay_per_use') === 'free';
    } else {
      $covered = ($plan['normal_access'] ?? 'pay_per_use') === 'free';
    }

    if ($covered) {
      return ['method'=>'membership', 'unit'=>0.0, 'total'=>0.0, 'plan'=>$plan];
    }

    $unit = (float)$lounge['price_usd'];
    $total = $unit * max(1, $people);
    return ['method'=>'pay_per_use', 'unit'=>$unit, 'total'=>$total, 'plan'=>$plan];
  }

    public static function createBooking(array $data): int {
    // accepts: user_id, guest_name, guest_email, lounge_id, flight_number, visit_date, start_time, end_time, people_count, method, unit_price_usd, total_usd
    $qr  = bin2hex(random_bytes(10));
    $pdo = self::db();

    $pdo->beginTransaction();
    try {
      $st = $pdo->prepare("
        INSERT INTO bookings
          (user_id, guest_name, guest_email, lounge_id, flight_number, visit_date, start_time, end_time, people_count,
           method, unit_price_usd, total_usd, status, qr_code, qr_generated_at)
        VALUES
          (:user_id, :guest_name, :guest_email, :lounge_id, :flight_number, :visit_date, :start_time, :end_time, :people_count,
           :method, :unit_price_usd, :total_usd, 'confirmed', :qr, NOW())
      ");
      $st->execute([
        ':user_id'        => $data['user_id'] ?: null,
        ':guest_name'     => $data['guest_name'] ?? null,   // <-- NEW
        ':guest_email'    => $data['guest_email'] ?? null,  // <-- NEW
        ':lounge_id'      => $data['lounge_id'],
        ':flight_number'  => $data['flight_number'] ?: null,
        ':visit_date'     => $data['visit_date'],
        ':start_time'     => $data['start_time'],
        ':end_time'       => $data['end_time'],
        ':people_count'   => $data['people_count'],
        ':method'         => $data['method'],
        ':unit_price_usd' => $data['unit_price_usd'],
        ':total_usd'      => $data['total_usd'],
        ':qr'             => $qr,
      ]);
      $bookingId = (int)$pdo->lastInsertId();

      if ((float)$data['total_usd'] > 0) {
        $st2 = $pdo->prepare("
          INSERT INTO booking_payments (booking_id, provider, provider_ref, amount_usd, currency, status, paid_at)
          VALUES (:bid, 'demo', :pref, :amt, 'USD', 'paid', NOW())
        ");
        $st2->execute([
          ':bid'  => $bookingId,
          ':pref' => 'DEMO-' . strtoupper(bin2hex(random_bytes(4))),
          ':amt'  => $data['total_usd'],
        ]);
      }

      $pdo->commit();
      return $bookingId;
    } catch (\Throwable $e) {
      $pdo->rollBack();
      throw $e;
    }
  }

  public static function getBookingSummary(int $bookingId): ?array {
    $sql = "
      SELECT b.*, l.name AS lounge_name, l.is_premium, l.price_usd,
             a.iata, a.name AS airport_name,
             -- prefer explicit contact saved on booking; fallback to user profile
             COALESCE(b.guest_name, CONCAT(u.first_name,' ',u.last_name))  AS contact_name,
             COALESCE(b.guest_email, u.email)                               AS contact_email
      FROM bookings b
      JOIN lounges  l ON l.id = b.lounge_id
      JOIN airports a ON a.id = l.airport_id
      LEFT JOIN users u ON u.id = b.user_id
      WHERE b.id = :id
      LIMIT 1
    ";
    $st = self::db()->prepare($sql);
    $st->execute([':id'=>$bookingId]);
    $row = $st->fetch();
    return $row ?: null;
  }

  public static function findNearestFlight(string $airline, string $number): ?array {
  // prefer nearest future; fallback to latest past
  $sql = "
    (SELECT fd.*, a1.iata AS dep_iata, a1.name AS dep_name,
            a2.iata AS arr_iata, a2.name AS arr_name
       FROM flight_details fd
       JOIN airports a1 ON a1.id = fd.dep_airport_id
       JOIN airports a2 ON a2.id = fd.arr_airport_id
      WHERE fd.airline_code = :ac AND fd.flight_number = :fn
        AND fd.flight_date >= CURDATE()
      ORDER BY fd.flight_date ASC
      LIMIT 1)
    UNION ALL
    (SELECT fd.*, a1.iata AS dep_iata, a1.name AS dep_name,
            a2.iata AS arr_iata, a2.name AS arr_name
       FROM flight_details fd
       JOIN airports a1 ON a1.id = fd.dep_airport_id
       JOIN airports a2 ON a2.id = fd.arr_airport_id
      WHERE fd.airline_code = :ac AND fd.flight_number = :fn
        AND fd.flight_date < CURDATE()
      ORDER BY fd.flight_date DESC
      LIMIT 1)
    LIMIT 1
  ";
  $st = self::db()->prepare($sql);
  $st->execute([':ac'=>$airline, ':fn'=>$number]);
  $row = $st->fetch();
  return $row ?: null;
}

}
