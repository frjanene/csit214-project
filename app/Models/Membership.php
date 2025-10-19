<?php
require_once __DIR__ . '/Model.php';

class Membership extends Model {

  public static function allPlans(): array {
    $sql = "SELECT id, slug, name, monthly_fee_usd, guest_allowance,
                   normal_access, premium_access, benefits_json
            FROM membership_plans
            ORDER BY monthly_fee_usd ASC";
    $rows = self::db()->query($sql)->fetchAll();

    $bySlug = [];
    foreach ($rows as $r) {
      // normalize
      $r['slug'] = strtolower($r['slug'] ?? 'basic');

      // benefits
      $r['benefits'] = [];
      if (!empty($r['benefits_json'])) {
        $arr = json_decode($r['benefits_json'], true);
        if (is_array($arr)) $r['benefits'] = $arr;
      }

      $bySlug[$r['slug']] = $r;
    }
    return $bySlug;
  }

  public static function userCurrent(?int $userId): ?array {
    if (!$userId) return null;
    $sql = "SELECT mp.*, um.id AS user_membership_id
              FROM user_memberships um
              JOIN membership_plans mp ON mp.id = um.plan_id
             WHERE um.user_id = :uid AND um.status = 'active'
             ORDER BY um.started_at DESC
             LIMIT 1";
    $st = self::db()->prepare($sql);
    $st->execute([':uid'=>$userId]);
    $row = $st->fetch();
    if (!$row) return null;

    // normalize
    $row['slug'] = strtolower($row['slug'] ?? 'basic');

    // benefits
    $row['benefits'] = [];
    if (!empty($row['benefits_json'])) {
      $arr = json_decode($row['benefits_json'], true);
      if (is_array($arr)) $row['benefits'] = $arr;
    }
    return $row;
  }

  public static function setUserPlan(int $userId, string $planSlug): void {
    $planSlug = strtolower($planSlug); // normalize input
    $pdo = self::db();
    $pdo->beginTransaction();
    try {
      $st = $pdo->prepare("SELECT id FROM membership_plans WHERE LOWER(slug) = :slug LIMIT 1");
      $st->execute([':slug'=>$planSlug]);
      $plan = $st->fetch();
      if (!$plan) throw new RuntimeException('Plan not found');

      $pdo->prepare("UPDATE user_memberships
                        SET status='canceled', canceled_at=NOW()
                      WHERE user_id=:uid AND status='active'")
          ->execute([':uid'=>$userId]);

      $pdo->prepare("INSERT INTO user_memberships (user_id, plan_id, status, started_at)
                     VALUES (:uid, :pid, 'active', NOW())")
          ->execute([':uid'=>$userId, ':pid'=>$plan['id']]);

      $pdo->commit();
    } catch (\Throwable $e) {
      $pdo->rollBack();
      throw $e;
    }
  }
}
