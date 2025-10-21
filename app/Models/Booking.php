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
    /**
   * Returns:
   *   [
   *     'method' => 'membership' | 'pay_per_use',
   *     'unit'   => float,   // unit price per extra guest / per person
   *     'total'  => float,   // total user must pay
   *     'plan'   => array,   // plan info
   *     // extras (informational)
   *     'extra_guests'   => int, // guests beyond allowance (charged)
   *     'covered_guests' => int, // guests within allowance (free)
   *   ]
   */
  public static function quotePrice(int $userId, array $lounge, int $people): array {
    $plan       = self::userPlan($userId);
    $isPremium  = (int)$lounge['is_premium'] === 1;
    $unit       = (float)$lounge['price_usd'];
    $people     = max(1, $people); // 1 member + N guests
    $allowGuests= (int)($plan['guest_allowance'] ?? 0);

    // Is the lounge entry itself covered by membership?
    $memberFree = $isPremium
      ? (($plan['premium_access'] ?? 'pay_per_use') === 'free')
      : (($plan['normal_access']  ?? 'pay_per_use') === 'free');

    // Default: pay-per-use for everyone
    $method = 'pay_per_use';
    $total  = $unit * $people;
    $extra  = $people; // everyone is effectively "paying" in this branch
    $coveredWithinAllowance = 0;

    if ($memberFree) {
      // Member (1) is free
      $guests = max(0, $people - 1);

      // Requirement: for PREMIUM lounges, only up to plan guest allowance is free;
      // extra guests must pay. (Member remains free.)
      if ($isPremium) {
        $coveredWithinAllowance = min($allowGuests, $guests);
        $extra = $guests - $coveredWithinAllowance; // paying guests
        $total = $unit * max(0, $extra);
      } else {
        // Non-premium lounge covered by plan => treat everyone as free
        // (If you want the allowance rule for normal lounges too, replace this block
        // with the same math as the premium branch.)
        $coveredWithinAllowance = $guests;
        $extra = 0;
        $total = 0.0;
      }

      $method = 'membership';
    }

    return [
      'method'          => $method,
      'unit'            => $unit,
      'total'           => $total,
      'plan'            => $plan,
      'extra_guests'    => (int)$extra,
      'covered_guests'  => (int)$coveredWithinAllowance,
    ];
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

  public static function listUserBookings(int $userId): array {
    $sql = "
      SELECT
        b.*,
        l.name  AS lounge_name,
        l.is_premium,
        a.iata  AS airport_iata,
        a.name  AS airport_name
      FROM bookings b
      JOIN lounges  l ON l.id = b.lounge_id
      JOIN airports a ON a.id = l.airport_id
      WHERE b.user_id = :uid
      ORDER BY b.visit_date DESC, b.start_time DESC
    ";
    $st = self::db()->prepare($sql);
    $st->execute([':uid' => $userId]);
    $rows = $st->fetchAll() ?: [];
    return $rows;
  }


  /** Soft-cancel a booking that belongs to the user */
  public static function cancelBooking(int $bookingId, int $userId): bool {
    $sql = "
      UPDATE bookings
         SET status = 'cancelled'
       WHERE id = :id AND user_id = :uid AND status = 'confirmed'
       LIMIT 1
    ";
    $st = self::db()->prepare($sql);
    $st->execute([':id'=>$bookingId, ':uid'=>$userId]);
    return $st->rowCount() === 1;
  }
  

    /**
   * Return all lounge_slots for a lounge on a given date with occupancy.
   * Output:
   *   [
   *     'open_time'  => 'HH:MM:SS',
   *     'close_time' => 'HH:MM:SS',
   *     'capacity'   => int,
   *     'rows'       => [
   *        ['label'=>'11:00','start'=>'11:00','end'=>'11:30','used'=>42,'cap'=>120,'text'=>'42/120'],
   *        ...
   *     ]
   *   ]
   * Returns null if lounge not found.
   */
  public static function slotsForDate(int $loungeId, string $dateYmd): ?array {
    $pdo = self::db();

    // Get base lounge info (capacity/open/close) and verify lounge exists.
    $L = $pdo->prepare("SELECT id, capacity, open_time, close_time FROM lounges WHERE id = :id LIMIT 1");
    $L->execute([':id'=>$loungeId]);
    $lou = $L->fetch();
    if (!$lou) return null;

    // Join preseeded lounge_slots to confirmed bookings that overlap the slot.
    // Overlap test: booking.start < slot.end AND booking.end > slot.start
    $sql = "
      SELECT
        ls.label,
        TIME_FORMAT(ls.start_time,'%H:%i') AS start_hhmm,
        TIME_FORMAT(ls.end_time,  '%H:%i') AS end_hhmm,
        COALESCE(SUM(
          CASE
            WHEN b.status = 'confirmed'
             AND b.visit_date = :d
             AND b.start_time < ls.end_time
             AND b.end_time   > ls.start_time
            THEN b.people_count ELSE 0
          END
        ),0) AS used
      FROM lounge_slots ls
      LEFT JOIN bookings b
        ON b.lounge_id = ls.lounge_id
       AND b.visit_date = :d
      WHERE ls.lounge_id = :lid
      GROUP BY ls.label, ls.start_time, ls.end_time
      ORDER BY ls.start_time
    ";

    $st = $pdo->prepare($sql);
    $st->execute([':lid'=>$loungeId, ':d'=>$dateYmd]);
    $rows = $st->fetchAll() ?: [];

    $cap = (int)$lou['capacity'];
    $out = [];
    foreach ($rows as $r) {
      $used = (int)$r['used'];
      $out[] = [
        'label' => $r['label'],
        'start' => $r['start_hhmm'],
        'end'   => $r['end_hhmm'],
        'used'  => $used,
        'cap'   => $cap,
        'text'  => "{$used}/{$cap}",
      ];
    }

    return [
      'open_time'  => $lou['open_time'],
      'close_time' => $lou['close_time'],
      'capacity'   => $cap,
      'rows'       => $out,
    ];
  }

}
