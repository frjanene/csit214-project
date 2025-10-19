-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 20, 2025 at 01:31 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `flydreamair`
--

-- --------------------------------------------------------

--
-- Table structure for table `airports`
--

CREATE TABLE `airports` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `iata` char(3) NOT NULL,
  `name` varchar(160) NOT NULL,
  `city` varchar(120) NOT NULL,
  `country` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `amenities`
--

CREATE TABLE `amenities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(60) NOT NULL,
  `label` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `guest_name` varchar(160) DEFAULT NULL,
  `guest_email` varchar(191) DEFAULT NULL,
  `lounge_id` bigint(20) UNSIGNED NOT NULL,
  `flight_number` varchar(20) DEFAULT NULL,
  `visit_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `people_count` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `method` enum('membership','pay_per_use') NOT NULL,
  `unit_price_usd` decimal(10,2) DEFAULT NULL,
  `total_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `status` enum('pending','confirmed','cancelled','completed') NOT NULL DEFAULT 'pending',
  `qr_code` varchar(64) NOT NULL,
  `qr_generated_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_payments`
--

CREATE TABLE `booking_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `provider` varchar(40) NOT NULL DEFAULT 'demo',
  `provider_ref` varchar(120) DEFAULT NULL,
  `amount_usd` decimal(10,2) NOT NULL DEFAULT 0.00,
  `currency` char(3) NOT NULL DEFAULT 'USD',
  `status` enum('requires_payment','paid','refunded','failed') NOT NULL DEFAULT 'paid',
  `paid_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flight_details`
--

CREATE TABLE `flight_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `airline_code` varchar(3) NOT NULL,
  `flight_number` varchar(6) NOT NULL,
  `dep_airport_id` bigint(20) UNSIGNED NOT NULL,
  `arr_airport_id` bigint(20) UNSIGNED NOT NULL,
  `dep_terminal` varchar(10) DEFAULT NULL,
  `arr_terminal` varchar(10) DEFAULT NULL,
  `dep_gate` varchar(10) DEFAULT NULL,
  `arr_gate` varchar(10) DEFAULT NULL,
  `equipment` varchar(50) DEFAULT NULL,
  `status` enum('scheduled','on_time','delayed','cancelled') NOT NULL DEFAULT 'scheduled',
  `sched_dep` datetime NOT NULL,
  `sched_arr` datetime NOT NULL,
  `flight_date` date GENERATED ALWAYS AS (cast(`sched_dep` as date)) STORED,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lounges`
--

CREATE TABLE `lounges` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `airport_id` bigint(20) UNSIGNED NOT NULL,
  `terminal` varchar(80) DEFAULT NULL,
  `is_premium` tinyint(1) NOT NULL DEFAULT 0,
  `address` varchar(200) DEFAULT NULL,
  `city` varchar(120) DEFAULT NULL,
  `country` varchar(120) DEFAULT NULL,
  `open_time` time NOT NULL,
  `close_time` time NOT NULL,
  `capacity` int(10) UNSIGNED NOT NULL,
  `price_usd` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lounge_amenities`
--

CREATE TABLE `lounge_amenities` (
  `lounge_id` bigint(20) UNSIGNED NOT NULL,
  `amenity_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `membership_plans`
--

CREATE TABLE `membership_plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `slug` varchar(40) NOT NULL,
  `name` varchar(80) NOT NULL,
  `monthly_fee_usd` decimal(10,2) NOT NULL,
  `guest_allowance` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `normal_access` enum('pay_per_use','free') NOT NULL DEFAULT 'pay_per_use',
  `premium_access` enum('pay_per_use','free') NOT NULL DEFAULT 'pay_per_use',
  `benefits_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`benefits_json`)),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `token` char(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(80) NOT NULL,
  `last_name` varchar(80) NOT NULL,
  `email` varchar(191) DEFAULT NULL,
  `password_hash` varchar(255) DEFAULT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `city` varchar(120) DEFAULT NULL,
  `country` varchar(120) DEFAULT NULL,
  `address` varchar(200) DEFAULT NULL,
  `role` enum('member','admin') NOT NULL DEFAULT 'member',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ;

-- --------------------------------------------------------

--
-- Table structure for table `user_memberships`
--

CREATE TABLE `user_memberships` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `plan_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('active','canceled','expired','trial') NOT NULL DEFAULT 'active',
  `started_at` datetime NOT NULL DEFAULT current_timestamp(),
  `renews_at` datetime DEFAULT NULL,
  `canceled_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_preferences`
--

CREATE TABLE `user_preferences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `language` varchar(10) NOT NULL DEFAULT 'en',
  `currency` char(3) NOT NULL DEFAULT 'USD',
  `notif_booking` tinyint(1) NOT NULL DEFAULT 1,
  `notif_account` tinyint(1) NOT NULL DEFAULT 1,
  `notif_promos` tinyint(1) NOT NULL DEFAULT 1,
  `notif_sms` tinyint(1) NOT NULL DEFAULT 0,
  `notif_push` tinyint(1) NOT NULL DEFAULT 1,
  `weekly_digest` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_lounge_daily_usage`
-- (See below for the actual view)
--
CREATE TABLE `v_lounge_daily_usage` (
`lounge_id` bigint(20) unsigned
,`visit_date` date
,`used_people` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_lounge_slot_usage`
-- (See below for the actual view)
--
CREATE TABLE `v_lounge_slot_usage` (
`lounge_id` bigint(20) unsigned
,`visit_date` date
,`start_time` time
,`end_time` time
,`used_people` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Structure for view `v_lounge_daily_usage`
--
DROP TABLE IF EXISTS `v_lounge_daily_usage`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_lounge_daily_usage`  AS SELECT `b`.`lounge_id` AS `lounge_id`, `b`.`visit_date` AS `visit_date`, sum(`b`.`people_count`) AS `used_people` FROM `bookings` AS `b` WHERE `b`.`status` = 'confirmed' GROUP BY `b`.`lounge_id`, `b`.`visit_date` ;

-- --------------------------------------------------------

--
-- Structure for view `v_lounge_slot_usage`
--
DROP TABLE IF EXISTS `v_lounge_slot_usage`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_lounge_slot_usage`  AS SELECT `b`.`lounge_id` AS `lounge_id`, `b`.`visit_date` AS `visit_date`, `b`.`start_time` AS `start_time`, `b`.`end_time` AS `end_time`, sum(`b`.`people_count`) AS `used_people` FROM `bookings` AS `b` WHERE `b`.`status` = 'confirmed' GROUP BY `b`.`lounge_id`, `b`.`visit_date`, `b`.`start_time`, `b`.`end_time` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `airports`
--
ALTER TABLE `airports`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `iata` (`iata`);

--
-- Indexes for table `amenities`
--
ALTER TABLE `amenities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `qr_code` (`qr_code`),
  ADD KEY `user_id` (`user_id`,`visit_date`),
  ADD KEY `lounge_id` (`lounge_id`,`visit_date`,`start_time`),
  ADD KEY `status` (`status`,`visit_date`);

--
-- Indexes for table `booking_payments`
--
ALTER TABLE `booking_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`,`status`);

--
-- Indexes for table `flight_details`
--
ALTER TABLE `flight_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_fd_flight_day` (`airline_code`,`flight_number`,`flight_date`),
  ADD KEY `idx_fd_dep` (`dep_airport_id`,`sched_dep`),
  ADD KEY `idx_fd_arr` (`arr_airport_id`,`sched_arr`);

--
-- Indexes for table `lounges`
--
ALTER TABLE `lounges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `airport_id` (`airport_id`,`is_premium`),
  ADD KEY `city` (`city`,`country`);

--
-- Indexes for table `lounge_amenities`
--
ALTER TABLE `lounge_amenities`
  ADD PRIMARY KEY (`lounge_id`,`amenity_id`),
  ADD KEY `amenity_id` (`amenity_id`);

--
-- Indexes for table `membership_plans`
--
ALTER TABLE `membership_plans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_memberships`
--
ALTER TABLE `user_memberships`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plan_id` (`plan_id`),
  ADD KEY `user_id` (`user_id`,`status`);

--
-- Indexes for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `airports`
--
ALTER TABLE `airports`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `booking_payments`
--
ALTER TABLE `booking_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `flight_details`
--
ALTER TABLE `flight_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lounges`
--
ALTER TABLE `lounges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `membership_plans`
--
ALTER TABLE `membership_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_memberships`
--
ALTER TABLE `user_memberships`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`lounge_id`) REFERENCES `lounges` (`id`);

--
-- Constraints for table `booking_payments`
--
ALTER TABLE `booking_payments`
  ADD CONSTRAINT `booking_payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `flight_details`
--
ALTER TABLE `flight_details`
  ADD CONSTRAINT `fk_fd_arr_airport` FOREIGN KEY (`arr_airport_id`) REFERENCES `airports` (`id`),
  ADD CONSTRAINT `fk_fd_dep_airport` FOREIGN KEY (`dep_airport_id`) REFERENCES `airports` (`id`);

--
-- Constraints for table `lounges`
--
ALTER TABLE `lounges`
  ADD CONSTRAINT `lounges_ibfk_1` FOREIGN KEY (`airport_id`) REFERENCES `airports` (`id`);

--
-- Constraints for table `lounge_amenities`
--
ALTER TABLE `lounge_amenities`
  ADD CONSTRAINT `lounge_amenities_ibfk_1` FOREIGN KEY (`lounge_id`) REFERENCES `lounges` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `lounge_amenities_ibfk_2` FOREIGN KEY (`amenity_id`) REFERENCES `amenities` (`id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_memberships`
--
ALTER TABLE `user_memberships`
  ADD CONSTRAINT `user_memberships_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_memberships_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `membership_plans` (`id`);

--
-- Constraints for table `user_preferences`
--
ALTER TABLE `user_preferences`
  ADD CONSTRAINT `user_preferences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
