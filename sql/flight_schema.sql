/* ============================================
   FlyDreamAir — Flight details for bookings
   ============================================ */

-- 1) Table to store flight details looked up/entered during booking
CREATE TABLE IF NOT EXISTS `flight_details` (
  `id`               BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,

  -- e.g., "FD" + "123"
  `airline_code`     VARCHAR(3)  NOT NULL,
  `flight_number`    VARCHAR(6)  NOT NULL,

  -- Link to existing airports table (already used by lounges)
  `dep_airport_id`   BIGINT UNSIGNED NOT NULL,
  `arr_airport_id`   BIGINT UNSIGNED NOT NULL,

  `dep_terminal`     VARCHAR(10)  NULL,
  `arr_terminal`     VARCHAR(10)  NULL,
  `dep_gate`         VARCHAR(10)  NULL,
  `arr_gate`         VARCHAR(10)  NULL,

  `equipment`        VARCHAR(50)  NULL,   -- e.g., "Boeing 777-300ER"
  `status`           ENUM('scheduled','on_time','delayed','cancelled')
                    NOT NULL DEFAULT 'scheduled',

  `sched_dep`        DATETIME     NOT NULL,
  `sched_arr`        DATETIME     NOT NULL,

  -- For uniq-by-day constraint (MySQL functional idx portability)
  `flight_date`      DATE         AS (DATE(`sched_dep`)) STORED,

  `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `fk_fd_dep_airport` FOREIGN KEY (`dep_airport_id`) REFERENCES `airports`(`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_fd_arr_airport` FOREIGN KEY (`arr_airport_id`) REFERENCES `airports`(`id`) ON DELETE RESTRICT,

  INDEX `idx_fd_dep` (`dep_airport_id`, `sched_dep`),
  INDEX `idx_fd_arr` (`arr_airport_id`, `sched_arr`),

  -- Prevent duplicate entries for the same flight on the same day
  UNIQUE KEY `uq_fd_flight_day` (`airline_code`, `flight_number`, `flight_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2) Seed a few flights used in the UI (assumes airports table contains these IATA codes)

-- FD123 SIN -> LAX on 2026-12-20 (matches your demo)
INSERT INTO `flight_details` (
  airline_code, flight_number, dep_airport_id, arr_airport_id,
  dep_terminal, arr_terminal, dep_gate, arr_gate,
  equipment, status, sched_dep, sched_arr
)
SELECT
  'FD','123',
  (SELECT id FROM airports WHERE iata='SIN'),
  (SELECT id FROM airports WHERE iata='LAX'),
  'T1','T2','A12',NULL,
  'Boeing 777-300ER','on_time','2026-12-20 14:30:00','2026-12-20 23:45:00'
WHERE NOT EXISTS (
  SELECT 1 FROM flight_details
  WHERE airline_code='FD' AND flight_number='123' AND flight_date='2026-12-20'
);

-- FD456 SYD -> SIN on 2026-12-20
INSERT INTO `flight_details` (
  airline_code, flight_number, dep_airport_id, arr_airport_id,
  dep_terminal, arr_terminal, dep_gate, arr_gate,
  equipment, status, sched_dep, sched_arr
)
SELECT
  'FD','456',
  (SELECT id FROM airports WHERE iata='SYD'),
  (SELECT id FROM airports WHERE iata='SIN'),
  'T1','T1','B07',NULL,
  'Airbus A350-900','scheduled','2026-12-20 09:00:00','2026-12-20 15:05:00'
WHERE NOT EXISTS (
  SELECT 1 FROM flight_details
  WHERE airline_code='FD' AND flight_number='456' AND flight_date='2026-12-20'
);

-- FD789 MEL -> SIN on 2026-12-15
INSERT INTO `flight_details` (
  airline_code, flight_number, dep_airport_id, arr_airport_id,
  dep_terminal, arr_terminal, dep_gate, arr_gate,
  equipment, status, sched_dep, sched_arr
)
SELECT
  'FD','789',
  (SELECT id FROM airports WHERE iata='MEL'),
  (SELECT id FROM airports WHERE iata='SIN'),
  'T2','T1','C03',NULL,
  'Boeing 787-9','on_time','2026-12-15 10:00:00','2026-12-15 15:30:00'
WHERE NOT EXISTS (
  SELECT 1 FROM flight_details
  WHERE airline_code='FD' AND flight_number='789' AND flight_date='2026-12-15'
);

-- AA100 SIN -> LHR on 2026-12-20 (example third-party carrier)
INSERT INTO `flight_details` (
  airline_code, flight_number, dep_airport_id, arr_airport_id,
  dep_terminal, arr_terminal, dep_gate, arr_gate,
  equipment, status, sched_dep, sched_arr
)
SELECT
  'AA','100',
  (SELECT id FROM airports WHERE iata='SIN'),
  (SELECT id FROM airports WHERE iata='LHR'),
  'T3','T3','D21','E14',
  'Boeing 777-200ER','scheduled','2026-12-20 23:10:00','2026-12-21 05:55:00'
WHERE NOT EXISTS (
  SELECT 1 FROM flight_details
  WHERE airline_code='AA' AND flight_number='100' AND flight_date='2026-12-20'
);

-- BA400 LAX -> SIN on 2026-12-20 (example third-party carrier)
INSERT INTO `flight_details` (
  airline_code, flight_number, dep_airport_id, arr_airport_id,
  dep_terminal, arr_terminal, dep_gate, arr_gate,
  equipment, status, sched_dep, sched_arr
)
SELECT
  'BA','400',
  (SELECT id FROM airports WHERE iata='LAX'),
  (SELECT id FROM airports WHERE iata='SIN'),
  'T2','T1','52A',NULL,
  'Airbus A380-800','delayed','2026-12-20 20:15:00','2026-12-22 06:10:00'
WHERE NOT EXISTS (
  SELECT 1 FROM flight_details
  WHERE airline_code='BA' AND flight_number='400' AND flight_date='2026-12-20'
);

-- Optional: another FD SIN -> SYD on 2026-12-20 morning
INSERT INTO `flight_details` (
  airline_code, flight_number, dep_airport_id, arr_airport_id,
  dep_terminal, arr_terminal, dep_gate, arr_gate,
  equipment, status, sched_dep, sched_arr
)
SELECT
  'FD','212',
  (SELECT id FROM airports WHERE iata='SIN'),
  (SELECT id FROM airports WHERE iata='SYD'),
  'T1','T1','A05',NULL,
  'Airbus A321neo','scheduled','2026-12-20 07:30:00','2026-12-20 17:35:00'
WHERE NOT EXISTS (
  SELECT 1 FROM flight_details
  WHERE airline_code='FD' AND flight_number='212' AND flight_date='2026-12-20'
);
