<?php
require_once __DIR__ . '/Model.php';

class Lounge extends Model
{
    public static function allAmenities(): array
    {
        $sql = "SELECT id, code, label FROM amenities ORDER BY label";
        return self::db()->query($sql)->fetchAll();
    }

    public static function countriesWithLounges(): array
    {
        $sql  = "SELECT DISTINCT country FROM lounges WHERE country IS NOT NULL AND country <> '' ORDER BY country";
        $rows = self::db()->query($sql)->fetchAll();
        return array_map(fn($r) => $r['country'], $rows);
    }

    public static function search(?string $q, ?string $country, array $amenityCodes = [], int $limit = 50, int $offset = 0): array
    {
        $db     = self::db();
        $params = [];
        $where  = [];

        $sql = "
            SELECT
                l.id, l.name, l.terminal, l.is_premium, l.open_time, l.close_time,
                l.capacity, l.price_usd, l.image_url, l.city, l.country,
                a.name AS airport_name, a.iata,
                (
                    SELECT COALESCE(SUM(
                        CASE
                            WHEN b.status = 'confirmed'
                             AND b.visit_date = CURDATE()
                             AND CONCAT(b.visit_date,' ',b.start_time) <= NOW()
                             AND CONCAT(b.visit_date,' ',b.end_time)   > NOW()
                            THEN b.people_count ELSE 0
                        END
                    ),0)
                    FROM bookings b
                    WHERE b.lounge_id = l.id
                ) AS used_now
            FROM lounges l
            JOIN airports a ON a.id = l.airport_id
        ";

        if ($q !== null && $q !== '') {
            $where[]        = "(l.name LIKE :q OR l.city LIKE :q OR l.country LIKE :q OR a.name LIKE :q OR a.iata LIKE :q)";
            $params[':q']   = '%' . $q . '%';
        }

        if ($country !== null && $country !== '' && $country !== 'All Countries') {
            $where[]            = "l.country = :country";
            $params[':country'] = $country;
        }

        if (!empty($amenityCodes)) {
            $placeholders = [];
            foreach ($amenityCodes as $i => $code) {
                $ph               = ":am{$i}";
                $placeholders[]   = $ph;
                $params[$ph]      = $code;
            }

            $sql .= "
                JOIN lounge_amenities la ON la.lounge_id = l.id
                JOIN amenities am ON am.id = la.amenity_id
            ";

            $where[] = "am.code IN (" . implode(',', $placeholders) . ")";
            $sql    .= " WHERE " . implode(' AND ', $where ?: ['1=1']);
            $sql    .= "
                GROUP BY l.id
                HAVING COUNT(DISTINCT am.code) = " . count($amenityCodes) . "
                ORDER BY l.is_premium DESC, l.city, l.name
                LIMIT :limit OFFSET :offset
            ";
        } else {
            if (!empty($where)) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }
            $sql .= "
                ORDER BY l.is_premium DESC, l.city, l.name
                LIMIT :limit OFFSET :offset
            ";
        }

        $stmt = $db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        if (!$rows) {
            return [];
        }

        $ids  = array_column($rows, 'id');
        $in   = implode(',', array_fill(0, count($ids), '?'));
        $aSQL = "
            SELECT l.id AS lounge_id, am.code, am.label
              FROM lounges l
              JOIN lounge_amenities la ON la.lounge_id = l.id
              JOIN amenities am ON am.id = la.amenity_id
             WHERE l.id IN ($in)
             ORDER BY am.label
        ";
        $aStmt = $db->prepare($aSQL);
        $aStmt->execute($ids);

        $amap = [];
        while ($r = $aStmt->fetch()) {
            $amap[$r['lounge_id']][] = ['code' => $r['code'], 'label' => $r['label']];
        }

        foreach ($rows as &$r) {
            $r['used_now']  = (int) ($r['used_now'] ?? 0);
            $r['capacity']  = (int) ($r['capacity'] ?? 0);
            $r['amenities'] = $amap[$r['id']] ?? [];
        }

        return $rows;
    }
}
