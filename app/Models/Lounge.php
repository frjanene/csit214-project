<?php
require_once __DIR__ . '/Model.php';

class Lounge extends Model
{
  /**
   * Fetch all amenities (for the filter checklist)
   * @return array [ ['id'=>..., 'code'=>..., 'label'=>...], ... ]
   */
  public static function allAmenities(): array {
    $sql = "SELECT id, code, label FROM amenities ORDER BY label";
    return self::db()->query($sql)->fetchAll();
  }

  /**
   * Fetch distinct countries that actually have lounges (for the country dropdown)
   * Uses denormalized lounges.city/country (seed should fill).
   */
  public static function countriesWithLounges(): array {
    $sql = "SELECT DISTINCT country FROM lounges WHERE country IS NOT NULL AND country <> '' ORDER BY country";
    $rows = self::db()->query($sql)->fetchAll();
    return array_map(fn($r) => $r['country'], $rows);
  }

  /**
   * Search lounges with optional filters.
   * @param string|null $q          Free text (lounge name, airport, city, iata)
   * @param string|null $country    Exact country name
   * @param array $amenityCodes     Array of amenity codes (['WIFI','SHOWERS',...])
   * @param int $limit
   * @param int $offset
   * @return array Each lounge row with:
   *   - lounge fields (id,name,terminal,is_premium,open_time,close_time,capacity,price_usd,image_url,city,country)
   *   - airport_name, iata
   *   - amenities: array of ['code','label']
   */
  public static function search(?string $q, ?string $country, array $amenityCodes = [], int $limit = 50, int $offset = 0): array
  {
    $db = self::db();
    $params = [];
    $where  = [];

    // Base join
    $sql = "
      SELECT
        l.id, l.name, l.terminal, l.is_premium, l.open_time, l.close_time,
        l.capacity, l.price_usd, l.image_url, l.city, l.country,
        a.name AS airport_name, a.iata
      FROM lounges l
      JOIN airports a ON a.id = l.airport_id
    ";

    // Text search
    if ($q !== null && $q !== '') {
      $where[] = "(l.name LIKE :q OR l.city LIKE :q OR l.country LIKE :q OR a.name LIKE :q OR a.iata LIKE :q)";
      $params[':q'] = '%' . $q . '%';
    }

    // Country filter
    if ($country !== null && $country !== '' && $country !== 'All Countries') {
      $where[] = "l.country = :country";
      $params[':country'] = $country;
    }

    // Amenities filter: must have ALL selected amenities
    if (!empty($amenityCodes)) {
      // Join to filter, then GROUP BY/HAVING count = number selected
      $placeholders = [];
      foreach ($amenityCodes as $i => $code) {
        $ph = ":am{$i}";
        $placeholders[] = $ph;
        $params[$ph] = $code;
      }

      $sql .= "
        JOIN lounge_amenities la ON la.lounge_id = l.id
        JOIN amenities am ON am.id = la.amenity_id
      ";

      $where[] = "am.code IN (" . implode(',', $placeholders) . ")";
      $sql .= " WHERE " . implode(' AND ', $where ?: ['1=1']);
      $sql .= "
        GROUP BY l.id
        HAVING COUNT(DISTINCT am.code) = " . count($amenityCodes) . "
        ORDER BY l.is_premium DESC, l.city, l.name
        LIMIT :limit OFFSET :offset
      ";
    } else {
      // No amenity filter — normal WHERE/ORDER/LIMIT
      if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
      }
      $sql .= " ORDER BY l.is_premium DESC, l.city, l.name
                LIMIT :limit OFFSET :offset";
    }

    $stmt = $db->prepare($sql);
    foreach ($params as $k => $v) $stmt->bindValue($k, $v);
    $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll();

    if (!$rows) return [];

    // Pull amenities per lounge in one go
    $ids = array_column($rows, 'id');
    $in  = implode(',', array_fill(0, count($ids), '?'));
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
    $amap = []; // lounge_id => [ ['code'=>...,'label'=>...], ... ]
    while ($r = $aStmt->fetch()) {
      $amap[$r['lounge_id']][] = ['code' => $r['code'], 'label' => $r['label']];
    }

    // Attach amenities array
    foreach ($rows as &$r) {
      $r['amenities'] = $amap[$r['id']] ?? [];
    }
    return $rows;
  }
}
