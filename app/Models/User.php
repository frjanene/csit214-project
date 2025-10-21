<?php
require_once __DIR__ . '/Model.php';

class User extends Model
{
    public static function findById(int $id): ?array
    {
        $st = self::db()->prepare("
            SELECT id, first_name, last_name, email, phone, dob, city, country, address, role
              FROM users
             WHERE id = :id
             LIMIT 1
        ");
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function findAuthById(int $id): ?array
    {
        $st = self::db()->prepare("
            SELECT id, email, password_hash
              FROM users
             WHERE id = :id
             LIMIT 1
        ");
        $st->execute([':id' => $id]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $st = self::db()->prepare("SELECT * FROM users WHERE email = ?");
        $st->execute([$email]);
        $row = $st->fetch();
        return $row ?: null;
    }

    public static function emailExists(string $email, int $excludeId = 0): bool
    {
        $sql    = "SELECT id FROM users WHERE email = :email";
        $params = [':email' => $email];

        if ($excludeId > 0) {
            $sql               .= " AND id <> :id";
            $params[':id'] = $excludeId;
        }

        $sql .= " LIMIT 1";

        $st = self::db()->prepare($sql);
        $st->execute($params);
        return (bool) $st->fetch();
    }

    public static function create(array $data): int
    {
        $st = self::db()->prepare("
            INSERT INTO users (first_name, last_name, email, password_hash, phone, city, country, role)
            VALUES (?,?,?,?,?,?,?, 'member')
        ");
        $st->execute([
            $data['first_name'],
            $data['last_name'],
            $data['email'],
            $data['password_hash'],
            $data['phone']   ?? null,
            $data['city']    ?? null,
            $data['country'] ?? null,
        ]);
        return (int) self::db()->lastInsertId();
    }

    public static function updateProfile(int $id, array $data): void
    {
        $st = self::db()->prepare("
            UPDATE users SET
                first_name = :first_name,
                last_name  = :last_name,
                email      = :email,
                phone      = :phone,
                dob        = :dob,
                city       = :city,
                country    = :country,
                address    = :address
             WHERE id = :id
        ");
        $st->execute([
            ':first_name' => $data['first_name'],
            ':last_name'  => $data['last_name'],
            ':email'      => $data['email'],
            ':phone'      => $data['phone']   ?: null,
            ':dob'        => $data['dob']     ?: null,
            ':city'       => $data['city']    ?: null,
            ':country'    => $data['country'] ?: null,
            ':address'    => $data['address'] ?: null,
            ':id'         => $id,
        ]);
    }

    public static function updatePassword(int $id, string $hash): void
    {
        $st = self::db()->prepare("UPDATE users SET password_hash = :h WHERE id = :id");
        $st->execute([':h' => $hash, ':id' => $id]);
    }

    public static function giveBasicMembership(int $userId): void
    {
        $db     = self::db();
        $planId = $db->query("SELECT id FROM membership_plans WHERE slug='basic'")->fetchColumn();

        if ($planId) {
            $st = $db->prepare("
                INSERT INTO user_memberships (user_id, plan_id, status, started_at)
                VALUES (:uid, :pid, 'active', NOW())
            ");
            $st->execute([':uid' => $userId, ':pid' => $planId]);
        }
    }

    public static function getOrCreatePreferences(int $userId): array
    {
        $pdo = self::db();

        $st = $pdo->prepare("SELECT * FROM user_preferences WHERE user_id = :uid LIMIT 1");
        $st->execute([':uid' => $userId]);
        $row = $st->fetch();
        if ($row) {
            return $row;
        }

        $pdo->prepare("INSERT INTO user_preferences (user_id) VALUES (:uid)")
            ->execute([':uid' => $userId]);

        $st->execute([':uid' => $userId]);
        $row = $st->fetch();

        return $row ?: [
            'user_id'       => $userId,
            'language'      => 'en',
            'currency'      => 'USD',
            'notif_booking' => 1,
            'notif_account' => 1,
            'notif_promos'  => 1,
            'notif_sms'     => 0,
            'notif_push'    => 1,
            'weekly_digest' => 0,
        ];
    }

    public static function savePreferences(int $userId, array $prefs): void
    {
        $st = self::db()->prepare("
            UPDATE user_preferences SET
                language = :language,
                currency = :currency,
                notif_booking = :nb,
                notif_account = :na,
                notif_promos  = :np,
                notif_sms     = :ns,
                notif_push    = :npu,
                weekly_digest = :wd
             WHERE user_id = :uid
        ");
        $st->execute([
            ':language' => $prefs['language'],
            ':currency' => $prefs['currency'],
            ':nb'       => (int) $prefs['notif_booking'],
            ':na'       => (int) $prefs['notif_account'],
            ':np'       => (int) $prefs['notif_promos'],
            ':ns'       => (int) $prefs['notif_sms'],
            ':npu'      => (int) $prefs['notif_push'],
            ':wd'       => (int) $prefs['weekly_digest'],
            ':uid'      => $userId,
        ]);
    }
}
