-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 21, 2025 at 09:26 AM
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

--
-- Dumping data for table `airports`
--

INSERT INTO `airports` (`id`, `iata`, `name`, `city`, `country`) VALUES
(1, 'SIN', 'Singapore Changi Airport', 'Singapore', 'Singapore'),
(2, 'SYD', 'Sydney Kingsford Smith Airport', 'Sydney', 'Australia'),
(3, 'MEL', 'Melbourne Airport', 'Melbourne', 'Australia'),
(4, 'DXB', 'Dubai International Airport', 'Dubai', 'United Arab Emirates'),
(5, 'CDG', 'Charles de Gaulle Airport', 'Paris', 'France'),
(6, 'LAX', 'Los Angeles International Airport', 'Los Angeles', 'United States'),
(7, 'LHR', 'London Heathrow Airport', 'London', 'United Kingdom');

-- --------------------------------------------------------

--
-- Table structure for table `amenities`
--

CREATE TABLE `amenities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `code` varchar(60) NOT NULL,
  `label` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `amenities`
--

INSERT INTO `amenities` (`id`, `code`, `label`) VALUES
(1, 'WIFI', 'Wi-Fi'),
(2, 'SHOWERS', 'Showers'),
(3, 'DINING', 'Premium Dining'),
(4, 'BAR', 'Bar'),
(5, 'BUSINESS_CENTER', 'Business Center'),
(6, 'COFFEE_BAR', 'Coffee Bar'),
(7, 'CHAMPAGNE_BAR', 'Champagne Bar');

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

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `guest_name`, `guest_email`, `lounge_id`, `flight_number`, `visit_date`, `start_time`, `end_time`, `people_count`, `method`, `unit_price_usd`, `total_usd`, `status`, `qr_code`, `qr_generated_at`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, NULL, 1, 'FD456', '2026-12-15', '14:00:00', '18:00:00', 2, 'membership', NULL, 0.00, 'confirmed', 'QR-SIN-20261215-1400-JS', '2025-10-19 08:20:10', '2025-10-19 08:20:10', '2025-10-19 08:20:10'),
(2, 1, NULL, NULL, 2, 'FD123', '2026-12-20', '09:00:00', '13:00:00', 3, 'membership', NULL, 0.00, 'confirmed', 'QR-SYD-20261220-0900-JS', '2025-10-19 08:20:10', '2025-10-19 08:20:10', '2025-10-19 08:20:10'),
(3, 1, NULL, NULL, 3, NULL, '2026-11-10', '08:00:00', '10:00:00', 1, 'pay_per_use', 55.00, 55.00, 'completed', 'QR-MEL-20261110-0800-JS', '2025-10-19 08:20:10', '2025-10-19 08:20:10', '2025-10-19 08:20:10'),
(4, 1, NULL, NULL, 5, NULL, '2026-10-30', '12:00:00', '14:00:00', 2, 'pay_per_use', 55.00, 0.00, 'cancelled', 'QR-CDG-20261030-1200-JS', '2025-10-19 08:20:10', '2025-10-19 08:20:10', '2025-10-19 08:20:10'),
(5, NULL, 'Guest Traveler', 'guest@example.com', 4, NULL, '2026-12-25', '11:00:00', '13:00:00', 1, 'pay_per_use', 55.00, 55.00, 'cancelled', 'QR-DXB-20261225-1100-GUEST', '2025-10-19 08:20:10', '2025-10-19 08:20:10', '2025-10-19 08:20:10'),
(6, 3, NULL, NULL, 4, 'FD123', '2026-12-20', '12:30:00', '14:00:00', 1, 'pay_per_use', 55.00, 55.00, 'confirmed', '7ff84ab7a6ada5c28a16', '2025-10-19 20:06:57', '2025-10-19 20:06:57', '2025-10-19 20:06:57'),
(7, 3, NULL, NULL, 4, 'FD123', '2026-12-20', '11:30:00', '12:30:00', 1, 'pay_per_use', 55.00, 55.00, 'confirmed', '6774b3cd87e8290697f5', '2025-10-20 00:19:24', '2025-10-20 00:19:24', '2025-10-20 00:19:24'),
(8, 3, 'Mirabel Gold', 'mirabel@example.com', 4, 'FD123', '2026-12-20', '11:30:00', '12:30:00', 4, 'pay_per_use', 55.00, 220.00, 'confirmed', '056d92bac6b40a9f65f7', '2025-10-20 00:46:12', '2025-10-20 00:46:12', '2025-10-20 00:46:12'),
(9, 3, 'Mirabel Gold', 'mirabel@example.com', 3, 'FD123', '2026-12-20', '11:30:00', '12:30:00', 1, 'membership', 0.00, 0.00, 'confirmed', '65b61801cec27b0709c8', '2025-10-20 00:50:00', '2025-10-20 00:50:00', '2025-10-20 00:50:00'),
(10, 2, 'Jessica John Thompson', 'jessica.thompson@example.com', 4, 'AA100', '2026-12-20', '11:30:00', '12:30:00', 1, 'membership', 0.00, 0.00, 'confirmed', '671823a8551e198b10e2', '2025-10-20 00:51:30', '2025-10-20 00:51:30', '2025-10-20 00:51:30'),
(11, 2, 'Jessica John Thompson', 'jessica.thompson@example.com', 1, 'AA100', '2026-12-20', '11:30:00', '12:30:00', 1, 'membership', 0.00, 0.00, 'confirmed', '86c6fed572f865a08d76', '2025-10-20 01:03:11', '2025-10-20 01:03:11', '2025-10-20 01:03:11'),
(12, 2, 'Jessica John Thompson', 'jessica.thompson@example.com', 4, 'FD123', '2026-12-20', '13:00:00', '14:00:00', 4, 'membership', 0.00, 0.00, 'confirmed', '12687435ec1d74e739d0', '2025-10-20 01:04:28', '2025-10-20 01:04:28', '2025-10-20 01:04:28'),
(13, 2, 'Jessica John Thompson', 'jessica.thompson@example.com', 4, 'FD123', '2026-12-20', '11:30:00', '12:30:00', 1, 'membership', 0.00, 0.00, 'confirmed', '345bd2d822e288aef767', '2025-10-20 01:11:12', '2025-10-20 01:11:12', '2025-10-20 01:11:12'),
(14, 2, 'Jessica John Thompson', 'jessica.thompson@example.com', 4, 'FD123', '2026-12-20', '11:30:00', '12:30:00', 1, 'membership', 0.00, 0.00, 'confirmed', '096b519263215cfbf8f2', '2025-10-20 01:20:45', '2025-10-20 01:20:45', '2025-10-20 01:20:45'),
(15, 3, 'Mirabel Gold', 'mirabel@example.com', 5, 'AA100', '2026-12-20', '13:00:00', '14:00:00', 1, 'membership', 0.00, 0.00, 'cancelled', '6393dbcfd17a338a4892', '2025-10-20 01:47:02', '2025-10-20 01:47:02', '2025-10-20 01:47:32'),
(16, 4, 'Brandon George', 'brandon.mitchell@example.com', 4, 'BA400', '2026-12-20', '13:00:00', '14:00:00', 3, 'pay_per_use', 55.00, 165.00, 'cancelled', '9445b7a7a237127a9ed4', '2025-10-20 11:15:43', '2025-10-20 11:15:43', '2025-10-20 11:19:28'),
(17, 4, 'Brandon George', 'brandon.mitchell@example.com', 1, 'FD123', '2026-12-20', '11:30:00', '12:30:00', 1, 'membership', 0.00, 0.00, 'confirmed', '52aab86d5b8da9c1d6b5', '2025-10-20 11:17:56', '2025-10-20 11:17:56', '2025-10-20 11:17:56'),
(18, 4, 'Brandon George', 'brandon.mitchell@example.com', 4, 'FD123', '2026-12-20', '11:30:00', '12:30:00', 4, 'membership', 55.00, 55.00, 'confirmed', 'dfd69ebaa32d3e119839', '2025-10-20 15:52:42', '2025-10-20 15:52:42', '2025-10-20 15:52:42'),
(19, 4, 'Brandon George', 'brandon.mitchell@example.com', 4, 'BA400', '2026-12-20', '12:00:00', '14:00:00', 1, 'membership', 55.00, 0.00, 'confirmed', '3f8be1f73dbdf07e97df', '2025-10-21 06:55:22', '2025-10-21 06:55:22', '2025-10-21 06:55:22'),
(20, 4, 'Brandon George', 'brandon.mitchell@example.com', 4, 'BA400', '2026-12-20', '07:30:00', '13:30:00', 1, 'membership', 55.00, 0.00, 'confirmed', '1103297c35c8941914b8', '2025-10-21 07:40:56', '2025-10-21 07:40:56', '2025-10-21 07:40:56'),
(21, 4, 'Brandon George', 'brandon.mitchell@example.com', 1, 'FD123', '2026-12-20', '12:00:00', '13:00:00', 4, 'membership', 155.00, 155.00, 'confirmed', '8cb26e6e84e08fc6178c', '2025-10-21 07:51:40', '2025-10-21 07:51:40', '2025-10-21 07:51:40'),
(22, 4, 'Brandon George', 'brandon.mitchell@example.com', 3, 'BA400', '2026-12-20', '05:00:00', '06:00:00', 1, 'membership', 55.00, 0.00, 'confirmed', '9564f07a4013d0073713', '2025-10-21 08:16:34', '2025-10-21 08:16:34', '2025-10-21 08:16:34'),
(23, 4, 'Brandon George', 'brandon.mitchell@example.com', 5, 'BA400', '2026-12-20', '05:00:00', '06:00:00', 3, 'membership', 55.00, 0.00, 'confirmed', 'f46a3bed65bc165ee4f7', '2025-10-21 08:24:20', '2025-10-21 08:24:20', '2025-10-21 08:24:20');

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

--
-- Dumping data for table `booking_payments`
--

INSERT INTO `booking_payments` (`id`, `booking_id`, `provider`, `provider_ref`, `amount_usd`, `currency`, `status`, `paid_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'demo', 'FREE-MEMBERSHIP', 0.00, 'USD', 'paid', '2025-10-19 08:20:10', '2025-10-19 08:20:10', '2025-10-19 08:20:10'),
(2, 2, 'demo', 'FREE-MEMBERSHIP', 0.00, 'USD', 'paid', '2025-10-19 08:20:10', '2025-10-19 08:20:10', '2025-10-19 08:20:10'),
(4, 3, 'demo', 'PPU-MEL-1', 55.00, 'USD', 'paid', '2025-10-19 08:20:10', '2025-10-19 08:20:10', '2025-10-19 08:20:10'),
(5, 4, 'demo', 'PPU-CDG-FAIL', 55.00, 'USD', 'failed', NULL, '2025-10-19 08:20:10', '2025-10-19 08:20:10'),
(6, 6, 'demo', 'DEMO-FF943C0A', 55.00, 'USD', 'paid', '2025-10-19 20:06:57', '2025-10-19 20:06:57', '2025-10-19 20:06:57'),
(7, 7, 'demo', 'DEMO-98245799', 55.00, 'USD', 'paid', '2025-10-20 00:19:24', '2025-10-20 00:19:24', '2025-10-20 00:19:24'),
(8, 8, 'demo', 'DEMO-8402C1FA', 220.00, 'USD', 'paid', '2025-10-20 00:46:12', '2025-10-20 00:46:12', '2025-10-20 00:46:12'),
(9, 16, 'demo', 'DEMO-76F44DF3', 165.00, 'USD', 'paid', '2025-10-20 11:15:43', '2025-10-20 11:15:43', '2025-10-20 11:15:43'),
(10, 18, 'demo', 'DEMO-3D614201', 55.00, 'USD', 'paid', '2025-10-20 15:52:42', '2025-10-20 15:52:42', '2025-10-20 15:52:42'),
(11, 21, 'demo', 'DEMO-F8D4AB62', 155.00, 'USD', 'paid', '2025-10-21 07:51:40', '2025-10-21 07:51:40', '2025-10-21 07:51:40');

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

--
-- Dumping data for table `flight_details`
--

INSERT INTO `flight_details` (`id`, `airline_code`, `flight_number`, `dep_airport_id`, `arr_airport_id`, `dep_terminal`, `arr_terminal`, `dep_gate`, `arr_gate`, `equipment`, `status`, `sched_dep`, `sched_arr`, `created_at`, `updated_at`) VALUES
(3, 'FD', '123', 1, 6, 'T1', 'T2', 'A12', NULL, 'Boeing 777-300ER', 'on_time', '2026-12-20 14:30:00', '2026-12-20 23:45:00', '2025-10-19 16:16:38', '2025-10-19 16:16:38'),
(4, 'FD', '456', 2, 1, 'T1', 'T1', 'B07', NULL, 'Airbus A350-900', 'scheduled', '2026-12-20 09:00:00', '2026-12-20 15:05:00', '2025-10-19 16:16:38', '2025-10-19 16:16:38'),
(5, 'FD', '789', 3, 1, 'T2', 'T1', 'C03', NULL, 'Boeing 787-9', 'on_time', '2026-12-15 10:00:00', '2026-12-15 15:30:00', '2025-10-19 16:16:38', '2025-10-19 16:16:38'),
(6, 'AA', '100', 1, 7, 'T3', 'T3', 'D21', 'E14', 'Boeing 777-200ER', 'scheduled', '2026-12-20 23:10:00', '2026-12-21 05:55:00', '2025-10-19 16:16:38', '2025-10-19 16:16:38'),
(7, 'BA', '400', 6, 1, 'T2', 'T1', '52A', NULL, 'Airbus A380-800', 'delayed', '2026-12-20 20:15:00', '2026-12-22 06:10:00', '2025-10-19 16:16:38', '2025-10-19 16:16:38'),
(8, 'FD', '212', 1, 2, 'T1', 'T1', 'A05', NULL, 'Airbus A321neo', 'scheduled', '2026-12-20 07:30:00', '2026-12-20 17:35:00', '2025-10-19 16:16:38', '2025-10-19 16:16:38');

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
  `slot_interval_min` smallint(5) UNSIGNED NOT NULL DEFAULT 30,
  `capacity` int(10) UNSIGNED NOT NULL,
  `price_usd` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lounges`
--

INSERT INTO `lounges` (`id`, `name`, `airport_id`, `terminal`, `is_premium`, `address`, `city`, `country`, `open_time`, `close_time`, `slot_interval_min`, `capacity`, `price_usd`, `image_url`, `created_at`, `updated_at`) VALUES
(1, 'FlyDreamAir Premium Lounge', 1, 'Terminal 1', 1, NULL, 'Singapore', 'Singapore', '05:00:00', '23:00:00', 30, 120, 155.00, 'assets/img/lounge-1.jpg', '2025-10-19 08:20:10', '2025-10-20 15:34:32'),
(2, 'FlyDreamAir Sydney Lounge', 2, 'Terminal 1', 0, NULL, 'Sydney', 'Australia', '04:30:00', '23:30:00', 30, 150, 55.00, 'assets/img/lounge-2.jpg', '2025-10-19 08:20:10', '2025-10-19 08:20:10'),
(3, 'FlyDreamAir Melbourne Lounge', 3, 'Terminal 2', 0, NULL, 'Melbourne', 'Australia', '05:00:00', '22:30:00', 30, 140, 55.00, 'assets/img/lounge-3.jpg', '2025-10-19 08:20:10', '2025-10-19 08:20:10'),
(4, 'FlyDreamAir Dubai Lounge', 4, 'Terminal 3', 1, NULL, 'Dubai', 'United Arab Emirates', '05:00:00', '23:00:00', 30, 140, 55.00, 'assets/img/lounge-3.jpg', '2025-10-19 08:20:10', '2025-10-19 08:20:10'),
(5, 'FlyDreamAir Paris Lounge', 5, 'Terminal 2', 0, NULL, 'Paris', 'France', '05:00:00', '22:00:00', 30, 120, 55.00, 'assets/img/lounge-2.jpg', '2025-10-19 08:20:10', '2025-10-19 08:20:10');

-- --------------------------------------------------------

--
-- Table structure for table `lounge_amenities`
--

CREATE TABLE `lounge_amenities` (
  `lounge_id` bigint(20) UNSIGNED NOT NULL,
  `amenity_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `lounge_amenities`
--

INSERT INTO `lounge_amenities` (`lounge_id`, `amenity_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 7),
(2, 1),
(2, 4),
(2, 5),
(3, 1),
(3, 5),
(3, 6),
(4, 1),
(4, 2),
(5, 1),
(5, 3);

-- --------------------------------------------------------

--
-- Table structure for table `lounge_slots`
--

CREATE TABLE `lounge_slots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `lounge_id` bigint(20) UNSIGNED NOT NULL,
  `label` varchar(20) DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lounge_slots`
--

INSERT INTO `lounge_slots` (`id`, `lounge_id`, `label`, `start_time`, `end_time`, `is_active`) VALUES
(1, 1, '05:00', '05:00:00', '05:30:00', 1),
(2, 2, '04:30', '04:30:00', '05:00:00', 1),
(3, 3, '05:00', '05:00:00', '05:30:00', 1),
(4, 4, '05:00', '05:00:00', '05:30:00', 1),
(5, 5, '05:00', '05:00:00', '05:30:00', 1),
(6, 1, '05:30', '05:30:00', '06:00:00', 1),
(7, 2, '05:00', '05:00:00', '05:30:00', 1),
(8, 3, '05:30', '05:30:00', '06:00:00', 1),
(9, 4, '05:30', '05:30:00', '06:00:00', 1),
(10, 5, '05:30', '05:30:00', '06:00:00', 1),
(11, 1, '06:00', '06:00:00', '06:30:00', 1),
(12, 2, '05:30', '05:30:00', '06:00:00', 1),
(13, 3, '06:00', '06:00:00', '06:30:00', 1),
(14, 4, '06:00', '06:00:00', '06:30:00', 1),
(15, 5, '06:00', '06:00:00', '06:30:00', 1),
(16, 1, '06:30', '06:30:00', '07:00:00', 1),
(17, 2, '06:00', '06:00:00', '06:30:00', 1),
(18, 3, '06:30', '06:30:00', '07:00:00', 1),
(19, 4, '06:30', '06:30:00', '07:00:00', 1),
(20, 5, '06:30', '06:30:00', '07:00:00', 1),
(21, 1, '07:00', '07:00:00', '07:30:00', 1),
(22, 2, '06:30', '06:30:00', '07:00:00', 1),
(23, 3, '07:00', '07:00:00', '07:30:00', 1),
(24, 4, '07:00', '07:00:00', '07:30:00', 1),
(25, 5, '07:00', '07:00:00', '07:30:00', 1),
(26, 1, '07:30', '07:30:00', '08:00:00', 1),
(27, 2, '07:00', '07:00:00', '07:30:00', 1),
(28, 3, '07:30', '07:30:00', '08:00:00', 1),
(29, 4, '07:30', '07:30:00', '08:00:00', 1),
(30, 5, '07:30', '07:30:00', '08:00:00', 1),
(31, 1, '08:00', '08:00:00', '08:30:00', 1),
(32, 2, '07:30', '07:30:00', '08:00:00', 1),
(33, 3, '08:00', '08:00:00', '08:30:00', 1),
(34, 4, '08:00', '08:00:00', '08:30:00', 1),
(35, 5, '08:00', '08:00:00', '08:30:00', 1),
(36, 1, '08:30', '08:30:00', '09:00:00', 1),
(37, 2, '08:00', '08:00:00', '08:30:00', 1),
(38, 3, '08:30', '08:30:00', '09:00:00', 1),
(39, 4, '08:30', '08:30:00', '09:00:00', 1),
(40, 5, '08:30', '08:30:00', '09:00:00', 1),
(41, 1, '09:00', '09:00:00', '09:30:00', 1),
(42, 2, '08:30', '08:30:00', '09:00:00', 1),
(43, 3, '09:00', '09:00:00', '09:30:00', 1),
(44, 4, '09:00', '09:00:00', '09:30:00', 1),
(45, 5, '09:00', '09:00:00', '09:30:00', 1),
(46, 1, '09:30', '09:30:00', '10:00:00', 1),
(47, 2, '09:00', '09:00:00', '09:30:00', 1),
(48, 3, '09:30', '09:30:00', '10:00:00', 1),
(49, 4, '09:30', '09:30:00', '10:00:00', 1),
(50, 5, '09:30', '09:30:00', '10:00:00', 1),
(51, 1, '10:00', '10:00:00', '10:30:00', 1),
(52, 2, '09:30', '09:30:00', '10:00:00', 1),
(53, 3, '10:00', '10:00:00', '10:30:00', 1),
(54, 4, '10:00', '10:00:00', '10:30:00', 1),
(55, 5, '10:00', '10:00:00', '10:30:00', 1),
(56, 1, '10:30', '10:30:00', '11:00:00', 1),
(57, 2, '10:00', '10:00:00', '10:30:00', 1),
(58, 3, '10:30', '10:30:00', '11:00:00', 1),
(59, 4, '10:30', '10:30:00', '11:00:00', 1),
(60, 5, '10:30', '10:30:00', '11:00:00', 1),
(61, 1, '11:00', '11:00:00', '11:30:00', 1),
(62, 2, '10:30', '10:30:00', '11:00:00', 1),
(63, 3, '11:00', '11:00:00', '11:30:00', 1),
(64, 4, '11:00', '11:00:00', '11:30:00', 1),
(65, 5, '11:00', '11:00:00', '11:30:00', 1),
(66, 1, '11:30', '11:30:00', '12:00:00', 1),
(67, 2, '11:00', '11:00:00', '11:30:00', 1),
(68, 3, '11:30', '11:30:00', '12:00:00', 1),
(69, 4, '11:30', '11:30:00', '12:00:00', 1),
(70, 5, '11:30', '11:30:00', '12:00:00', 1),
(71, 1, '12:00', '12:00:00', '12:30:00', 1),
(72, 2, '11:30', '11:30:00', '12:00:00', 1),
(73, 3, '12:00', '12:00:00', '12:30:00', 1),
(74, 4, '12:00', '12:00:00', '12:30:00', 1),
(75, 5, '12:00', '12:00:00', '12:30:00', 1),
(76, 1, '12:30', '12:30:00', '13:00:00', 1),
(77, 2, '12:00', '12:00:00', '12:30:00', 1),
(78, 3, '12:30', '12:30:00', '13:00:00', 1),
(79, 4, '12:30', '12:30:00', '13:00:00', 1),
(80, 5, '12:30', '12:30:00', '13:00:00', 1),
(81, 1, '13:00', '13:00:00', '13:30:00', 1),
(82, 2, '12:30', '12:30:00', '13:00:00', 1),
(83, 3, '13:00', '13:00:00', '13:30:00', 1),
(84, 4, '13:00', '13:00:00', '13:30:00', 1),
(85, 5, '13:00', '13:00:00', '13:30:00', 1),
(86, 1, '13:30', '13:30:00', '14:00:00', 1),
(87, 2, '13:00', '13:00:00', '13:30:00', 1),
(88, 3, '13:30', '13:30:00', '14:00:00', 1),
(89, 4, '13:30', '13:30:00', '14:00:00', 1),
(90, 5, '13:30', '13:30:00', '14:00:00', 1),
(91, 1, '14:00', '14:00:00', '14:30:00', 1),
(92, 2, '13:30', '13:30:00', '14:00:00', 1),
(93, 3, '14:00', '14:00:00', '14:30:00', 1),
(94, 4, '14:00', '14:00:00', '14:30:00', 1),
(95, 5, '14:00', '14:00:00', '14:30:00', 1),
(96, 1, '14:30', '14:30:00', '15:00:00', 1),
(97, 2, '14:00', '14:00:00', '14:30:00', 1),
(98, 3, '14:30', '14:30:00', '15:00:00', 1),
(99, 4, '14:30', '14:30:00', '15:00:00', 1),
(100, 5, '14:30', '14:30:00', '15:00:00', 1),
(101, 1, '15:00', '15:00:00', '15:30:00', 1),
(102, 2, '14:30', '14:30:00', '15:00:00', 1),
(103, 3, '15:00', '15:00:00', '15:30:00', 1),
(104, 4, '15:00', '15:00:00', '15:30:00', 1),
(105, 5, '15:00', '15:00:00', '15:30:00', 1),
(106, 1, '15:30', '15:30:00', '16:00:00', 1),
(107, 2, '15:00', '15:00:00', '15:30:00', 1),
(108, 3, '15:30', '15:30:00', '16:00:00', 1),
(109, 4, '15:30', '15:30:00', '16:00:00', 1),
(110, 5, '15:30', '15:30:00', '16:00:00', 1),
(111, 1, '16:00', '16:00:00', '16:30:00', 1),
(112, 2, '15:30', '15:30:00', '16:00:00', 1),
(113, 3, '16:00', '16:00:00', '16:30:00', 1),
(114, 4, '16:00', '16:00:00', '16:30:00', 1),
(115, 5, '16:00', '16:00:00', '16:30:00', 1),
(116, 1, '16:30', '16:30:00', '17:00:00', 1),
(117, 2, '16:00', '16:00:00', '16:30:00', 1),
(118, 3, '16:30', '16:30:00', '17:00:00', 1),
(119, 4, '16:30', '16:30:00', '17:00:00', 1),
(120, 5, '16:30', '16:30:00', '17:00:00', 1),
(121, 1, '17:00', '17:00:00', '17:30:00', 1),
(122, 2, '16:30', '16:30:00', '17:00:00', 1),
(123, 3, '17:00', '17:00:00', '17:30:00', 1),
(124, 4, '17:00', '17:00:00', '17:30:00', 1),
(125, 5, '17:00', '17:00:00', '17:30:00', 1),
(126, 1, '17:30', '17:30:00', '18:00:00', 1),
(127, 2, '17:00', '17:00:00', '17:30:00', 1),
(128, 3, '17:30', '17:30:00', '18:00:00', 1),
(129, 4, '17:30', '17:30:00', '18:00:00', 1),
(130, 5, '17:30', '17:30:00', '18:00:00', 1),
(131, 1, '18:00', '18:00:00', '18:30:00', 1),
(132, 2, '17:30', '17:30:00', '18:00:00', 1),
(133, 3, '18:00', '18:00:00', '18:30:00', 1),
(134, 4, '18:00', '18:00:00', '18:30:00', 1),
(135, 5, '18:00', '18:00:00', '18:30:00', 1),
(136, 1, '18:30', '18:30:00', '19:00:00', 1),
(137, 2, '18:00', '18:00:00', '18:30:00', 1),
(138, 3, '18:30', '18:30:00', '19:00:00', 1),
(139, 4, '18:30', '18:30:00', '19:00:00', 1),
(140, 5, '18:30', '18:30:00', '19:00:00', 1),
(141, 1, '19:00', '19:00:00', '19:30:00', 1),
(142, 2, '18:30', '18:30:00', '19:00:00', 1),
(143, 3, '19:00', '19:00:00', '19:30:00', 1),
(144, 4, '19:00', '19:00:00', '19:30:00', 1),
(145, 5, '19:00', '19:00:00', '19:30:00', 1),
(146, 1, '19:30', '19:30:00', '20:00:00', 1),
(147, 2, '19:00', '19:00:00', '19:30:00', 1),
(148, 3, '19:30', '19:30:00', '20:00:00', 1),
(149, 4, '19:30', '19:30:00', '20:00:00', 1),
(150, 5, '19:30', '19:30:00', '20:00:00', 1),
(151, 1, '20:00', '20:00:00', '20:30:00', 1),
(152, 2, '19:30', '19:30:00', '20:00:00', 1),
(153, 3, '20:00', '20:00:00', '20:30:00', 1),
(154, 4, '20:00', '20:00:00', '20:30:00', 1),
(155, 5, '20:00', '20:00:00', '20:30:00', 1),
(156, 1, '20:30', '20:30:00', '21:00:00', 1),
(157, 2, '20:00', '20:00:00', '20:30:00', 1),
(158, 3, '20:30', '20:30:00', '21:00:00', 1),
(159, 4, '20:30', '20:30:00', '21:00:00', 1),
(160, 5, '20:30', '20:30:00', '21:00:00', 1),
(161, 1, '21:00', '21:00:00', '21:30:00', 1),
(162, 2, '20:30', '20:30:00', '21:00:00', 1),
(163, 3, '21:00', '21:00:00', '21:30:00', 1),
(164, 4, '21:00', '21:00:00', '21:30:00', 1),
(165, 5, '21:00', '21:00:00', '21:30:00', 1),
(166, 1, '21:30', '21:30:00', '22:00:00', 1),
(167, 2, '21:00', '21:00:00', '21:30:00', 1),
(168, 3, '21:30', '21:30:00', '22:00:00', 1),
(169, 4, '21:30', '21:30:00', '22:00:00', 1),
(170, 5, '21:30', '21:30:00', '22:00:00', 1),
(171, 1, '22:00', '22:00:00', '22:30:00', 1),
(172, 2, '21:30', '21:30:00', '22:00:00', 1),
(173, 3, '22:00', '22:00:00', '22:30:00', 1),
(174, 4, '22:00', '22:00:00', '22:30:00', 1),
(175, 1, '22:30', '22:30:00', '23:00:00', 1),
(176, 2, '22:00', '22:00:00', '22:30:00', 1),
(177, 4, '22:30', '22:30:00', '23:00:00', 1),
(178, 2, '22:30', '22:30:00', '23:00:00', 1),
(179, 2, '23:00', '23:00:00', '23:30:00', 1);

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

--
-- Dumping data for table `membership_plans`
--

INSERT INTO `membership_plans` (`id`, `slug`, `name`, `monthly_fee_usd`, `guest_allowance`, `normal_access`, `premium_access`, `benefits_json`, `created_at`, `updated_at`) VALUES
(1, 'basic', 'Basic (Free)', 0.00, 0, 'pay_per_use', 'pay_per_use', '[\"Free membership signup\", \"Buy single-visit pass for all lounges, including premium lounges\", \"Wi-Fi access\"]', '2025-10-19 08:20:10', '2025-10-19 08:20:10'),
(2, 'silver', 'Silver', 299.00, 1, 'free', 'pay_per_use', '[\"Free access to normal lounges\", \"Pay-per-use for premium lounges\", \"Wi-Fi & printing\", \"Light refreshments\"]', '2025-10-19 08:20:10', '2025-10-19 08:20:10'),
(3, 'gold', 'Gold', 499.00, 2, 'free', 'free', '[\"Free access to all lounges, incl. premium\", \"Unlimited time\", \"Premium amenities\", \"Full dining\"]', '2025-10-19 08:20:10', '2025-10-19 08:20:10'),
(4, 'platinum', 'Platinum', 699.00, 3, 'free', 'free', '[\"Free access to all lounges, incl. premium\", \"Unlimited time\", \"Concierge service\", \"Private meeting rooms\"]', '2025-10-19 08:20:10', '2025-10-19 08:20:10');

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

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `password_hash`, `phone`, `dob`, `city`, `country`, `address`, `role`, `created_at`, `updated_at`) VALUES
(1, 'John', 'Smith', 'john.smith@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1 (555) 123-4567', NULL, 'New York', 'United States', NULL, 'member', '2025-10-19 08:20:10', '2025-10-19 08:20:10'),
(2, 'Jessica John', 'Thompson', 'jessica.thompson@example.com', '$2y$12$M..X8Lfc82hOYDZJnGhwPuFFsvRHCLHYBoyqQU2w6.F1HPzQJE2EG', '123456789', '2001-05-01', 'Springfield', 'Nigeria', '456 Elm Street', 'member', '2025-10-19 08:57:25', '2025-10-19 12:38:48'),
(3, 'Mirabel', 'Gold', 'mirabel@example.com', '$2y$12$XEJ4oB.0ozTpOUExIChEf.t/.56mYjUq1s58z2rxPyW2i.7R5dtge', '08169266879', '2001-05-01', 'Hillside', 'Nigeria', '651 N Broad Street Middletown Delaware US', 'member', '2025-10-19 12:40:39', '2025-10-19 13:16:40'),
(4, 'Brandon', 'George', 'brandon.mitchell@example.com', '$2y$12$0D5IT6gjnb9S5sEDjCH/7O9Vt7wpGaIZWhdlUYNcnLdo01Erfbdsy', '08169266879', NULL, 'Lagos', 'Nigeria', '473 Mundet Place', 'member', '2025-10-20 11:00:11', '2025-10-20 11:27:00'),
(5, 'Andrew', 'Balogun', 'balogunandrew001@gmail.com', '$2y$12$a6RXoMSZv1MufyY1qa.j5eHZt7J7sC1N4P2.qRkTVUGPJjjj1dJwG', NULL, NULL, NULL, NULL, NULL, 'member', '2025-10-20 11:29:40', '2025-10-20 11:29:40');

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

--
-- Dumping data for table `user_memberships`
--

INSERT INTO `user_memberships` (`id`, `user_id`, `plan_id`, `status`, `started_at`, `renews_at`, `canceled_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'active', '2025-10-19 08:20:10', NULL, NULL, '2025-10-19 08:20:10', '2025-10-19 08:20:10'),
(2, 2, 1, 'canceled', '2025-10-19 08:57:25', NULL, '2025-10-19 10:16:54', '2025-10-19 08:57:25', '2025-10-19 10:16:54'),
(3, 2, 2, 'canceled', '2025-10-19 10:16:54', NULL, '2025-10-19 11:09:18', '2025-10-19 10:16:54', '2025-10-19 11:09:18'),
(4, 2, 3, 'active', '2025-10-19 11:09:18', NULL, NULL, '2025-10-19 11:09:18', '2025-10-19 11:09:18'),
(5, 3, 1, 'canceled', '2025-10-19 12:40:39', NULL, '2025-10-19 13:01:48', '2025-10-19 12:40:39', '2025-10-19 13:01:48'),
(6, 3, 2, 'active', '2025-10-19 13:01:48', NULL, NULL, '2025-10-19 13:01:48', '2025-10-19 13:01:48'),
(7, 4, 1, 'canceled', '2025-10-20 11:00:11', NULL, '2025-10-20 11:17:10', '2025-10-20 11:00:11', '2025-10-20 11:17:10'),
(8, 4, 3, 'canceled', '2025-10-20 11:17:10', NULL, '2025-10-21 08:16:16', '2025-10-20 11:17:10', '2025-10-21 08:16:16'),
(9, 5, 1, 'active', '2025-10-20 11:29:40', NULL, NULL, '2025-10-20 11:29:40', '2025-10-20 11:29:40'),
(10, 4, 2, 'active', '2025-10-21 08:16:16', NULL, NULL, '2025-10-21 08:16:16', '2025-10-21 08:16:16');

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

--
-- Dumping data for table `user_preferences`
--

INSERT INTO `user_preferences` (`id`, `user_id`, `language`, `currency`, `notif_booking`, `notif_account`, `notif_promos`, `notif_sms`, `notif_push`, `weekly_digest`, `created_at`, `updated_at`) VALUES
(1, 2, 'es', 'USD', 0, 0, 0, 0, 0, 0, '2025-10-19 12:34:43', '2025-10-19 12:37:23'),
(2, 3, 'en', 'NGN', 1, 1, 0, 0, 1, 1, '2025-10-19 12:46:00', '2025-10-19 13:16:21'),
(3, 4, 'es', 'EUR', 0, 1, 1, 0, 0, 0, '2025-10-20 11:23:25', '2025-10-20 11:26:39');

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
-- Stand-in structure for view `v_lounge_now_usage`
-- (See below for the actual view)
--
CREATE TABLE `v_lounge_now_usage` (
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
-- Structure for view `v_lounge_now_usage`
--
DROP TABLE IF EXISTS `v_lounge_now_usage`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_lounge_now_usage`  AS SELECT `l`.`id` AS `lounge_id`, curdate() AS `visit_date`, coalesce(sum(case when `b`.`status` = 'confirmed' and `b`.`visit_date` = curdate() and concat(`b`.`visit_date`,' ',`b`.`start_time`) <= current_timestamp() and concat(`b`.`visit_date`,' ',`b`.`end_time`) > current_timestamp() then `b`.`people_count` else 0 end),0) AS `used_people` FROM (`lounges` `l` left join `bookings` `b` on(`b`.`lounge_id` = `l`.`id`)) GROUP BY `l`.`id` ;

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
-- Indexes for table `lounge_slots`
--
ALTER TABLE `lounge_slots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_slot` (`lounge_id`,`start_time`,`end_time`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `amenities`
--
ALTER TABLE `amenities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `booking_payments`
--
ALTER TABLE `booking_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `flight_details`
--
ALTER TABLE `flight_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `lounges`
--
ALTER TABLE `lounges`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `lounge_slots`
--
ALTER TABLE `lounge_slots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=256;

--
-- AUTO_INCREMENT for table `membership_plans`
--
ALTER TABLE `membership_plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_preferences`
--
ALTER TABLE `user_preferences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- Constraints for table `lounge_slots`
--
ALTER TABLE `lounge_slots`
  ADD CONSTRAINT `fk_slots_lounge` FOREIGN KEY (`lounge_id`) REFERENCES `lounges` (`id`) ON DELETE CASCADE;

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
