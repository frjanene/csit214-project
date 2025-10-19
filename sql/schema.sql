-- FlyDreamAir — Database Schema (MySQL 8+)
-- Engine/charset choices for consistent collations and FK support
SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE DATABASE IF NOT EXISTS flydreamair
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;
USE flydreamair;

-- =============== USERS & AUTH ===============
CREATE TABLE users (
  id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  first_name      VARCHAR(80)  NOT NULL,
  last_name       VARCHAR(80)  NOT NULL,
  email           VARCHAR(191) NULL UNIQUE,        -- NULL allowed for anonymous/guest-only rows if ever needed
  password_hash   VARCHAR(255) NULL,               -- bcrypt; NULL for pure guest checkout
  phone           VARCHAR(40)  NULL,
  dob             DATE         NULL,
  city            VARCHAR(120) NULL,
  country         VARCHAR(120) NULL,
  role            ENUM('member','admin') NOT NULL DEFAULT 'member',
  created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT chk_pw_email CHECK (
    (password_hash IS NULL AND email IS NULL) OR  -- fully anonymous (rare)
    (password_hash IS NULL AND email IS NOT NULL) OR
    (password_hash IS NOT NULL AND email IS NOT NULL)
  )
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_resets (
  id          BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id     BIGINT UNSIGNED NOT NULL,
  token       CHAR(64)        NOT NULL UNIQUE,
  expires_at  DATETIME        NOT NULL,
  used_at     DATETIME        NULL,
  created_at  DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============== AIRPORTS & LOUNGES ===============
CREATE TABLE airports (
  id      BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  iata    CHAR(3)        NOT NULL UNIQUE,
  name    VARCHAR(160)   NOT NULL,
  city    VARCHAR(120)   NOT NULL,
  country VARCHAR(120)   NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE lounges (
  id             BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  name           VARCHAR(200)  NOT NULL,
  airport_id     BIGINT UNSIGNED NOT NULL,
  terminal       VARCHAR(80)   NULL,
  is_premium     TINYINT(1)    NOT NULL DEFAULT 0,
  address        VARCHAR(200)  NULL,
  city           VARCHAR(120)  NULL,   -- denorm for display
  country        VARCHAR(120)  NULL,   -- denorm for display
  open_time      TIME          NOT NULL,
  close_time     TIME          NOT NULL,
  capacity       INT UNSIGNED  NOT NULL,
  price_usd      DECIMAL(10,2) NOT NULL, -- per person pay-per-use
  image_url      VARCHAR(255)  NULL,
  created_at     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (airport_id) REFERENCES airports(id) ON DELETE RESTRICT,
  INDEX (airport_id, is_premium),
  INDEX (city, country)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Amenities
CREATE TABLE amenities (
  id    BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  code  VARCHAR(60)   NOT NULL UNIQUE,   -- e.g., WIFI, SHOWERS
  label VARCHAR(120)  NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE lounge_amenities (
  lounge_id   BIGINT UNSIGNED NOT NULL,
  amenity_id  BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (lounge_id, amenity_id),
  FOREIGN KEY (lounge_id) REFERENCES lounges(id)  ON DELETE CASCADE,
  FOREIGN KEY (amenity_id) REFERENCES amenities(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============== MEMBERSHIPS ===============
CREATE TABLE membership_plans (
  id                BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  slug              VARCHAR(40)   NOT NULL UNIQUE,             -- basic, silver, gold, platinum
  name              VARCHAR(80)   NOT NULL,
  monthly_fee_usd   DECIMAL(10,2) NOT NULL,
  guest_allowance   INT UNSIGNED  NOT NULL DEFAULT 0,
  normal_access     ENUM('pay_per_use','free') NOT NULL DEFAULT 'pay_per_use',
  premium_access    ENUM('pay_per_use','free') NOT NULL DEFAULT 'pay_per_use',
  benefits_json     JSON          NULL,
  created_at        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_memberships (
  id          BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id     BIGINT UNSIGNED NOT NULL,
  plan_id     BIGINT UNSIGNED NOT NULL,
  status      ENUM('active','canceled','expired','trial') NOT NULL DEFAULT 'active',
  started_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  renews_at   DATETIME NULL,
  canceled_at DATETIME NULL,
  created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (plan_id) REFERENCES membership_plans(id) ON DELETE RESTRICT,
  INDEX (user_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =============== BOOKINGS & PAYMENTS ===============
CREATE TABLE bookings (
  id              BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  user_id         BIGINT UNSIGNED NULL,                   -- NULL when guest checkout with only name/email
  guest_name      VARCHAR(160) NULL,
  guest_email     VARCHAR(191) NULL,
  lounge_id       BIGINT UNSIGNED NOT NULL,
  flight_number   VARCHAR(20)  NULL,
  visit_date      DATE         NOT NULL,                  -- local to airport
  start_time      TIME         NOT NULL,                  -- local to airport
  end_time        TIME         NOT NULL,                  -- local to airport
  people_count    INT UNSIGNED NOT NULL DEFAULT 1,
  method          ENUM('membership','pay_per_use') NOT NULL,
  unit_price_usd  DECIMAL(10,2) NULL,                     -- NULL if membership covered/free
  total_usd       DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  status          ENUM('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  qr_code         VARCHAR(64)  NOT NULL UNIQUE,           -- text code; image can be generated on-the-fly
  qr_generated_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id)   REFERENCES users(id)   ON DELETE SET NULL,
  FOREIGN KEY (lounge_id) REFERENCES lounges(id) ON DELETE RESTRICT,
  INDEX (user_id, visit_date),
  INDEX (lounge_id, visit_date, start_time),
  INDEX (status, visit_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE booking_payments (
  id             BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  booking_id     BIGINT UNSIGNED NOT NULL,
  provider       VARCHAR(40)     NOT NULL DEFAULT 'demo', -- keep scaffolded; can be 'stripe','paypal' later
  provider_ref   VARCHAR(120)    NULL,
  amount_usd     DECIMAL(10,2)   NOT NULL DEFAULT 0.00,
  currency       CHAR(3)         NOT NULL DEFAULT 'USD',
  status         ENUM('requires_payment','paid','refunded','failed') NOT NULL DEFAULT 'paid',
  paid_at        DATETIME        NULL,
  created_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  INDEX (booking_id, status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Helpful view: expected occupancy per lounge/time window (optional)
-- This counts confirmed bookings overlapping a given time.
-- CREATE VIEW v_expected_occupancy AS
-- SELECT
--   l.id AS lounge_id,
--   b.visit_date,
--   SUM(b.people_count) AS used
-- FROM bookings b
-- JOIN lounges l ON l.id = b.lounge_id
-- WHERE b.status = 'confirmed'
-- GROUP BY l.id, b.visit_date;

