<?php
require_once __DIR__ . '/Model.php';

class User extends Model {
  public static function findByEmail(string $email): ?array {
    $st = self::db()->prepare("SELECT * FROM users WHERE email = ?");
    $st->execute([$email]);
    $row = $st->fetch();
    return $row ?: null;
  }

  public static function create(array $data): int {
    $st = self::db()->prepare(
      "INSERT INTO users (first_name,last_name,email,password_hash,phone,city,country,role)
       VALUES (?,?,?,?,?,?,?, 'member')"
    );
    $st->execute([
      $data['first_name'], $data['last_name'], $data['email'],
      $data['password_hash'], $data['phone'] ?? null,
      $data['city'] ?? null, $data['country'] ?? null
    ]);
    return (int) self::db()->lastInsertId();
  }

  public static function giveBasicMembership(int $userId): void {
    $db = self::db();
    $planId = $db->query("SELECT id FROM membership_plans WHERE slug='basic'")->fetchColumn();
    if ($planId) {
      $st = $db->prepare("INSERT INTO user_memberships (user_id, plan_id, status) VALUES (?,?, 'active')");
      $st->execute([$userId, $planId]);
    }
  }
}
