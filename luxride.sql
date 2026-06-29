-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2026 at 05:51 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `luxride`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `user_email`, `action`, `description`, `ip_address`, `created_at`) VALUES
(1, NULL, 'admin@luxride.com', 'login', 'Admin logged in', '127.0.0.1', '2026-03-13 09:09:02'),
(2, NULL, 'test@example.com', 'register', 'New user registered', '192.168.1.1', '2026-03-13 09:09:02'),
(3, NULL, 'manishyadanapuri@gmail.com', 'payment', 'Payment of ₹5863 via upi for booking LUXBQW8OTXOR', '::1', '2026-04-08 10:34:13'),
(4, NULL, 'manishyadanapuri@gmail.com', 'payment', 'Payment ₹5665 via upi — Booking LUXFEWC04B0P — TXN: LUX-TXN-69D6361B-BB1B4214', '::1', '2026-04-08 11:03:55'),
(5, NULL, 'saif@gmail.com', 'payment', 'Payment ₹2655 via upi — Booking LUX8YMX2EF73 — TXN: LUX-TXN-69E1B318-466B4C0A', '::1', '2026-04-17 04:12:08'),
(6, NULL, 'saif@gmail.com', 'payment', 'Payment ₹3546 via upi — Booking LUX4GGNHA5VR — TXN: LUX-TXN-69E1DE9D-C3687D63', '::1', '2026-04-17 07:17:49'),
(7, 1, 'manishyadanapuri@gmail.com', 'login', 'User logged in', '::1', '2026-04-23 10:33:26'),
(8, NULL, 'saif@gmail.com', 'payment', 'Payment ₹4460 via upi — Booking LUXVA4BY38UN — TXN: LUX-TXN-69EB27F7-CEEC5654', '::1', '2026-04-24 08:21:12'),
(9, 1, 'manishyadanapuri@gmail.com', 'login', 'User logged in', '::1', '2026-04-24 08:21:17'),
(10, 2, 'saifkhan@gmail.com', 'login', 'User logged in', '::1', '2026-04-24 08:21:52'),
(11, NULL, 'saifkhan@gmail.com', 'payment', 'Payment ₹5848 via upi — Booking LUXUO6ZH5N5U — TXN: LUX-TXN-69EB2887-61877056', '::1', '2026-04-24 08:23:35'),
(12, 2, 'saifkhan@gmail.com', 'login', 'User logged in', '::1', '2026-04-24 08:23:57'),
(13, 8, 'k@gmail.com', 'register', 'New user registered', '::1', '2026-04-25 11:15:45'),
(14, 8, 'k@gmail.com', 'login', 'User logged in', '::1', '2026-04-25 11:16:32'),
(15, 9, 'yadanapurimanish@gmail.com', 'register', 'New user registered', '::1', '2026-04-26 10:15:31'),
(16, NULL, 'yadanapurimanish@gmail.com', 'payment', 'Payment ₹2970 via upi — Booking LUXA0W8U0M9P — TXN: LUX-TXN-69EDE69D-2D7D8E74', '::1', '2026-04-26 10:19:09'),
(17, 9, 'yadanapurimanish@gmail.com', 'login', 'User logged in', '::1', '2026-04-26 10:20:33'),
(18, 9, 'yadanapurimanish@gmail.com', 'login', 'User logged in', '::1', '2026-04-26 10:20:39'),
(19, 9, 'yadanapurimanish@gmail.com', 'login', 'User logged in', '::1', '2026-04-26 10:20:49'),
(20, 9, 'yadanapurimanish@gmail.com', 'login', 'User logged in', '::1', '2026-04-26 10:21:11'),
(21, 10, 'mahesh@gmail.com', 'register', 'New user registered', '::1', '2026-04-26 10:24:34'),
(22, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-04-26 10:25:04'),
(23, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-04-26 10:25:19'),
(24, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-04-26 10:50:31'),
(25, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-04-26 10:50:51'),
(26, 9, 'yadanapurimanish@gmail.com', 'login', 'User logged in', '::1', '2026-04-26 10:51:19'),
(27, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-04-26 10:53:16'),
(28, NULL, 'yadanapurimanish@gmail.com', 'payment', 'Payment ₹4188 via upi — Booking LUX8KBNZQAF2 — TXN: LUX-TXN-69EDEEF9-7F99983B', '::1', '2026-04-26 10:54:49'),
(29, 2, 'saifkhan@gmail.com', 'login', 'User logged in', '::1', '2026-04-26 10:58:30'),
(30, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-04-26 10:59:06'),
(31, NULL, 'manishyadanapuri@gmail.com', 'payment', 'Payment ₹5414 via upi — Booking LUX2BZMBOPJC — TXN: LUX-TXN-69EDF038-340E257B', '::1', '2026-04-26 11:00:08'),
(32, 1, 'manishyadanapuri@gmail.com', 'login', 'User logged in', '::1', '2026-04-26 11:00:33'),
(33, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-04-29 10:22:13'),
(34, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-04-29 10:23:09'),
(35, 2, 'saifkhan@gmail.com', 'login', 'User logged in', '::1', '2026-04-29 10:23:30'),
(36, NULL, 'saif@gmail.com', 'payment', 'Payment ₹3341 via upi — Booking LUXNSHF3C4TZ — TXN: LUX-TXN-69F1DCF0-B09E6C93', '::1', '2026-04-29 10:26:56'),
(37, NULL, 'saif@gmail.com', 'payment', 'Payment ₹2548 via upi — Booking LUXPMDXY2RC5 — TXN: LUX-TXN-69F1DDC4-229CE7DC', '::1', '2026-04-29 10:30:28'),
(38, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-05-01 11:36:15'),
(39, NULL, 'manishyadanapuri@gmail.com', 'payment', 'Payment ₹2886 via upi — Booking LUXBAH1X14LG — TXN: LUX-TXN-69F4906E-07D874E3', '::1', '2026-05-01 11:37:18'),
(40, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-05-01 13:31:07'),
(41, 11, 'narshyya@gmail.com', 'register', 'New user registered', '::1', '2026-05-01 13:32:14'),
(42, NULL, 'narshyya@2004', 'payment', 'Payment ₹4826 via upi — Booking LUX5XIJ1OKN0 — TXN: LUX-TXN-69F4ABBD-29801FDD', '::1', '2026-05-01 13:33:49'),
(43, 11, 'narshyya@gmail.com', 'login', 'User logged in', '::1', '2026-05-01 13:34:18'),
(44, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-05-01 17:15:59'),
(45, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-05-01 17:16:44'),
(46, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-05-01 17:40:46'),
(47, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-05-01 17:41:24'),
(48, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-05-01 17:42:24'),
(49, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-05-01 17:46:02'),
(50, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-05-01 17:46:47'),
(51, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-05-01 17:48:09'),
(52, 11, 'narshyya@gmail.com', 'login', 'User logged in', '::1', '2026-05-01 17:49:45'),
(53, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-05-02 04:41:45'),
(54, NULL, 'mahesh@gmail.com', 'payment', 'Payment ₹3980 via upi — Booking LUXPUVUCFO7U — TXN: LUX-TXN-69F6D2D0-B3F03418', '::1', '2026-05-03 04:45:04'),
(55, 11, 'narshyya@gmail.com', 'login', 'User logged in', '::1', '2026-05-03 04:45:25'),
(56, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-05-03 04:48:27'),
(57, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-05-03 04:48:38'),
(58, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-05-03 04:54:38'),
(59, 11, 'narshyya@gmail.com', 'login', 'User logged in', '::1', '2026-05-03 04:56:03'),
(60, 2, 'saifkhan@gmail.com', 'login', 'User logged in', '::1', '2026-05-03 05:05:28'),
(61, 9, 'yadanapurimanish@gmail.com', 'login', 'User logged in', '::1', '2026-05-04 08:18:13'),
(62, NULL, 'yadanapurimanish@gmail.com', 'payment', 'Payment ₹12224 via upi — Booking LUXFV97U459R — TXN: LUX-TXN-69F856B3-1656F700', '::1', '2026-05-04 08:20:03'),
(63, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-05-05 11:02:59'),
(64, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-05-05 15:04:52'),
(65, 10, 'mahesh@gmail.com', 'login', 'User logged in', '::1', '2026-05-06 05:30:40'),
(66, 12, 'devesh@gmail.com', 'login', 'User logged in', '::1', '2026-05-06 05:42:46'),
(67, NULL, 'yadanapurimanish@gmail.com', 'cancel', 'Booking LUXFV97U459R cancelled', '::1', '2026-05-06 05:47:19'),
(68, 1, 'manishyadanapuri@gmail.com', 'login', 'User logged in', '::1', '2026-05-06 12:00:23'),
(69, 9, 'yadanapurimanish@gmail.com', 'login', 'User logged in', '::1', '2026-05-07 16:42:19'),
(70, NULL, 'yadanapurimanish@gmail.com', 'payment', 'Payment ₹12243 via upi — Booking LUXKRNARSM51 — TXN: LUX-TXN-69FCC236-D838F4BF', '::1', '2026-05-07 16:47:50'),
(71, 11, 'narshyya@gmail.com', 'login', 'User logged in', '::1', '2026-05-07 16:48:44'),
(72, NULL, 'narshyya@gmail.com', 'payment', 'Payment ₹8432 via upi — Booking LUXZLF04AZMQ — TXN: LUX-TXN-69FCC2F3-2DEC721F', '::1', '2026-05-07 16:50:59'),
(73, NULL, 'yadanapurimanish@gmail.com', 'payment', 'Payment ₹16489 via upi — Booking LUXTKPMQ1ODN — TXN: LUX-TXN-69FE167F-AE3050A2', '::1', '2026-05-08 16:59:43'),
(74, 9, 'yadanapurimanish@gmail.com', 'login', 'User logged in', '::1', '2026-05-09 03:04:47');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `booking_ref` varchar(20) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `user_name` varchar(100) DEFAULT NULL,
  `user_email` varchar(100) DEFAULT NULL,
  `car_id` int(11) DEFAULT NULL,
  `car_name` varchar(100) DEFAULT NULL,
  `car_brand` varchar(100) DEFAULT NULL,
  `pickup_location` varchar(200) DEFAULT NULL,
  `pickup_datetime` datetime DEFAULT NULL,
  `return_datetime` datetime DEFAULT NULL,
  `rate_type` enum('hourly','daily') DEFAULT 'daily',
  `duration` int(11) DEFAULT 1,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `booking_type` enum('self','chauffeur') DEFAULT 'self',
  `aadhar_path` varchar(255) DEFAULT NULL,
  `license_path` varchar(255) DEFAULT NULL,
  `dl_number` varchar(50) DEFAULT NULL,
  `cust_phone` varchar(20) DEFAULT NULL,
  `status` enum('pending','confirmed','active','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_ref`, `user_id`, `user_name`, `user_email`, `car_id`, `car_name`, `car_brand`, `pickup_location`, `pickup_datetime`, `return_datetime`, `rate_type`, `duration`, `total_amount`, `booking_type`, `aadhar_path`, `license_path`, `dl_number`, `cust_phone`, `status`, `created_at`) VALUES
(6, 'LUXBAH1X14LG', 10, 'Manish Yadanapuri', 'manishyadanapuri@gmail.com', 17, 'Nissan GT-R', 'Nissan', 'Mumbai', NULL, NULL, 'daily', 1, 2886.00, 'self', NULL, NULL, NULL, NULL, 'completed', '2026-05-01 11:37:06'),
(7, 'LUX5XIJ1OKN0', 11, 'Narshyya', 'narshyya@2004', 3, 'E-Class', 'Mercedes', 'Mumbai', NULL, NULL, 'daily', 1, 4826.00, 'self', NULL, NULL, NULL, NULL, 'completed', '2026-05-01 13:33:31'),
(8, 'LUXPUVUCFO7U', NULL, 'Mahesh', 'mahesh@gmail.com', 29, 'Model X', 'Tesla', 'Mumbai', NULL, NULL, 'daily', 1, 3980.00, 'self', NULL, NULL, NULL, NULL, 'completed', '2026-05-03 04:44:15'),
(9, 'LUXFV97U459R', 9, 'Manish Yadanapuri', 'yadanapurimanish@gmail.com', 41, 'Ferrari F8', 'Ferrari', 'Mumbai', NULL, NULL, 'daily', 3, 12224.00, 'self', NULL, NULL, NULL, NULL, 'completed', '2026-05-04 08:19:57');

-- --------------------------------------------------------

--
-- Table structure for table `cars`
--

CREATE TABLE `cars` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `type` varchar(50) NOT NULL,
  `price` int(11) NOT NULL,
  `image` varchar(255) NOT NULL,
  `badge` varchar(50) DEFAULT '',
  `rating` decimal(2,1) NOT NULL,
  `seats` int(11) NOT NULL,
  `transmission` varchar(20) NOT NULL,
  `fuel` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cars`
--

INSERT INTO `cars` (`id`, `name`, `brand`, `type`, `price`, `image`, `badge`, `rating`, `seats`, `transmission`, `fuel`) VALUES
(1, 'Model S Plaid', 'Tesla', 'electric', 100, 'img/electric/modelS.jpeg', 'new', 4.9, 5, 'Auto', 'Electric'),
(2, '911 Carrera', 'Porsche', 'luxury', 1009, 'img/luxury/119.webp', 'premium', 5.0, 2, 'PDK', 'Petrol'),
(3, 'E-Class', 'Mercedes', 'sedan', 149, 'img/sedan/Eclass.jpeg', 'premium', 4.8, 5, 'Auto', 'Hybrid'),
(4, 'X5 M Sport', 'BMW', 'suv', 179, 'img/suv/x5.jpeg', '', 4.7, 7, 'Auto', 'Diesel'),
(5, 'Urus', 'Lamborghini', 'luxury', 599, 'img/luxury/urus.webp', 'premium', 5.0, 5, 'Auto', 'Petrol'),
(6, 'Model 3', 'Tesla', 'electric', 89, 'img/electric/model3.jpeg', 'sale', 4.6, 5, 'Auto', 'Electric'),
(7, 'Range Rover', 'Land Rover', 'suv', 249, 'img/suv/rangerover.jpeg', 'premium', 4.8, 5, 'Auto', 'Diesel'),
(8, 'S-Class', 'Mercedes', 'luxury', 449, 'img/luxury/sclass.jpeg', 'premium', 4.9, 5, 'Auto', 'Hybrid'),
(9, 'Camry Hybrid', 'Toyota', 'sedan', 59, 'img/sedan/camry.jpeg', 'sale', 4.5, 5, 'Auto', 'Hybrid'),
(11, 'Ghost', 'Rolls-Royce', 'luxury', 999, 'img/luxury/ghost.jpeg', 'premium', 5.0, 5, 'Auto', 'Petrol'),
(12, 'GV80', 'Genesis', 'suv', 129, 'img/suv/gv80.jpeg', 'premiumcar', 4.6, 5, 'Auto', 'Petrol'),
(13, 'Volkswagen Atlas', 'Volkswagen', 'family', 160, 'img/family/volk.jpeg', '', 4.6, 7, 'Auto', 'Petrol'),
(14, 'Subaru Outback', 'Subaru', 'family', 135, 'img/family/subaru.jpeg', '', 4.5, 5, 'Auto', 'Petrol'),
(15, 'Accord', 'Honda', 'sedan', 79, 'img/sedan/acc.jpeg', '', 4.6, 5, 'Auto', 'Petrol'),
(16, 'Toyota Supra', 'Toyota', 'sports', 220, 'img/sports/toyota.jpg', '', 4.8, 2, 'Auto', 'Petrol'),
(17, 'Nissan GT-R', 'Nissan', 'sports', 500, 'img/sports/gtr.jpg', 'premium', 5.0, 2, 'Auto', 'Petrol'),
(18, 'BMW M4', 'BMW', 'sports', 280, 'img/sports/bmw.jpg', '', 4.8, 4, 'Auto', 'Petrol'),
(20, 'Nissan Navara', 'Nissan', 'pickup', 155, 'img/pickup/nissan.jpg', '', 4.5, 5, 'Manual', 'Diesel'),
(21, 'Isuzu D-Max', 'Isuzu', 'pickup', 150, 'img/pickup/isu.jpg', '', 4.4, 5, 'Manual', 'Diesel'),
(22, 'Toyota Tacoma', 'Toyota', 'pickup', 170, 'img/pickup/toyota.jpg', '', 4.6, 5, 'Auto', 'Petrol'),
(23, 'Ford Ranger', 'Ford', 'pickup', 165, 'img/pickup/ford.jpg', '', 4.6, 5, 'Auto', 'Diesel'),
(24, 'Lamborghini Huracan', 'Lamborghini', 'supercar', 780, 'img/supercar/lamb.jpeg', 'premium', 4.9, 2, 'Auto', 'Petrol'),
(25, 'Aston Martin DBS', 'Aston Martin', 'supercar', 760, 'img/supercar/aston.jpg', 'premium', 4.9, 2, 'Auto', 'Petrol'),
(26, 'Koenigsegg Jesko', 'Koenigsegg', 'supercar', 2000, 'img/supercar/koen.jpg', 'ultra', 5.0, 2, 'Auto', 'Petrol'),
(27, 'Rimac Nevera', 'Rimac', 'supercar', 1800, 'img/supercar/Rimac.jpg', 'electric', 5.0, 2, 'Auto', 'Electric'),
(28, '7 Series', 'BMW', 'luxury', 420, 'img/luxury/7series.jpeg', 'premium', 4.9, 5, 'Auto', 'Hybrid'),
(29, 'Model X', 'Tesla', 'electric', 299, 'img/electric/modelS.jpeg', 'premium', 4.9, 7, 'Auto', 'Electric'),
(30, 'Mustang Mach-E', 'Ford', 'electric', 210, 'img/electric/mustang.jpeg', '', 4.6, 5, 'Auto', 'Electric'),
(31, 'Volkswagen Beetle Classic', 'Volkswagen', 'vintage', 150, 'img/vintage/volk.jpg', 'classic', 4.6, 4, 'Manual', 'Petrol'),
(32, 'Pontiac GTO 1964', 'Pontiac', 'vintage', 260, 'img/vintage/pontiac.jpg', 'classic', 4.8, 4, 'Manual', 'Petrol'),
(33, 'Toyota 2000GT', 'Toyota', 'vintage', 420, 'img/vintage/toyota.jpg', 'rare', 4.9, 2, 'Manual', 'Petrol'),
(34, 'A4', 'Audi', 'sedan', 159, 'img/sedan/audi.jpeg', 'premium', 4.7, 5, 'Auto', 'Petrol'),
(35, 'Ford F-150', 'Ford', 'pickup', 180, 'img/pickup/for.jpg', '', 4.7, 5, 'Auto', 'Diesel'),
(36, 'Toyota Hilux', 'Toyota', 'pickup', 160, 'img/pickup/toyo.jpg', '', 4.6, 5, 'Manual', 'Diesel'),
(37, 'Chevrolet Silverado', 'Chevrolet', 'pickup', 190, 'img/pickup/chev.jpg', '', 4.7, 5, 'Auto', 'Diesel'),
(38, 'RAM 1500', 'RAM', 'pickup', 200, 'img/pickup/ram.jpeg', 'premium', 4.8, 5, 'Auto', 'Petrol'),
(39, 'Civic', 'Honda', 'sedan', 69, 'img/sedan/altima.jpeg', '', 4.5, 5, 'Auto', 'Petrol'),
(40, 'Elantra', 'Hyundai', 'sedan', 65, 'img/sedan/elantra.jpeg', '', 4.4, 5, 'Auto', 'Petrol'),
(41, 'Ferrari F8', 'Ferrari', 'supercar', 799, 'img/supercar/ferrari.jpeg', 'premium', 5.0, 2, 'Auto', 'Petrol'),
(42, 'Lamborghini Aventador', 'Lamborghini', 'supercar', 850, 'img/supercar/lambo.jpeg', 'premium', 5.0, 2, 'Auto', 'Petrol'),
(43, 'McLaren 720S', 'McLaren', 'supercar', 820, 'img/supercar/mcl.jpeg', 'premium', 5.0, 2, 'Auto', 'Petrol'),
(44, 'RAV4', 'Toyota', 'suv', 115, 'img/suv/rava.jpeg', '', 4.7, 5, 'Auto', 'Hybrid'),
(45, 'Tucson', 'Hyundai', 'suv', 105, 'img/suv/tuc.jpeg', '', 4.5, 5, 'Auto', 'Petrol'),
(47, 'Sportage', 'Kia', 'suv', 102, 'img/suv/sportage.jpeg', '', 4.4, 5, 'Auto', 'Diesel'),
(48, 'GMC Sierra', 'GMC', 'pickup', 210, 'img/pickup/gmc.jpeg', 'premium', 4.8, 5, 'Auto', 'Petrol'),
(49, 'Tesla Cybertruck', 'Tesla', 'pickup', 300, 'img/pickup/cyber.jpg', 'electric', 4.9, 5, 'Auto', 'Electric'),
(50, 'Bugatti Chiron', 'Bugatti', 'supercar', 1500, 'img/supercar/bugatti.jpeg', 'ultra', 5.0, 2, 'Auto', 'Petrol'),
(51, 'Porsche 918 Spyder', 'Porsche', 'supercar', 900, 'img/supercar/porshe.jpg', 'hybrid', 5.0, 2, 'Auto', 'Hybrid'),
(52, 'Ferrari SF90', 'Ferrari', 'supercar', 870, 'img/supercar/sf90.jpeg', 'new', 5.0, 2, 'Auto', 'Hybrid'),
(54, 'Audi R8', 'Audi', 'sports', 450, 'img/sports/audi.webp', 'premium', 5.0, 2, 'Auto', 'Petrol'),
(55, 'Corvette', 'Chevrolet', 'sports', 300, 'img/sports/corvette.jpg', '', 4.9, 2, 'Auto', 'Petrol'),
(56, 'AMG GT', 'Mercedes', 'sports', 480, 'img/sports/amg.jpg', 'premium', 4.9, 2, 'Auto', 'Petrol'),
(57, 'LS', 'Lexus', 'luxury', 380, 'img/luxury/lexus.jpeg', 'premium', 4.8, 5, 'Auto', 'Hybrid'),
(58, 'Porsche 356', 'Porsche', 'vintage', 330, 'img/vintage/porsche.jpg', 'classic', 4.9, 2, 'Manual', 'Petrol'),
(59, 'Cadillac DeVille 1965', 'Cadillac', 'vintage', 280, 'img/vintage/cadillac.jpg', 'classic', 4.7, 5, 'Auto', 'Petrol'),
(60, 'Mercedes 280SL 1969', 'Mercedes', 'vintage', 310, 'img/vintage/mercedes.webp', 'premium', 4.8, 2, 'Manual', 'Petrol'),
(61, 'Aston Martin DB5', 'Aston Martin', 'vintage', 500, 'img/vintage/aston.jpg', 'premium', 5.0, 2, 'Manual', 'Petrol'),
(62, 'Panamera', 'Porsche', 'luxury', 450, 'img/luxury/pors.jpeg', 'premium', 4.9, 4, 'Auto', 'Petrol'),
(63, 'Bentayga', 'Bentley', 'luxury', 599, 'img/luxury/bent.jpeg', 'premium', 5.0, 5, 'Auto', 'Petrol'),
(64, 'Ioniq 5', 'Hyundai', 'electric', 180, 'img/electric/ioniq.jpeg', '', 4.7, 5, 'Auto', 'Electric'),
(65, 'Wrangler', 'Jeep', 'offroad', 210, 'img/offroad/jeep.jpeg', '', 4.8, 5, 'Auto', 'Petrol'),
(66, 'Toyota Land Cruiser', 'Toyota', 'offroad', 350, 'img/offroad/toyota.jpeg', 'premium', 4.9, 7, 'Auto', 'Diesel'),
(67, 'Ford Bronco', 'Ford', 'offroad', 230, 'img/offroad/ford.jpeg', 'new', 4.7, 5, 'Auto', 'Petrol'),
(68, 'Land Rover Defender', 'Land Rover', 'offroad', 320, 'img/offroad/land.jpg', 'premium', 4.9, 7, 'Auto', 'Diesel'),
(69, 'EV6', 'Kia', 'electric', 185, 'img/electric/ev6.jpg', '', 4.6, 5, 'Auto', 'Electric'),
(70, 'i4', 'BMW', 'electric', 240, 'img/electric/bmw.jpg', 'premium', 4.8, 5, 'Auto', 'Electric'),
(71, 'EQE', 'Mercedes', 'electric', 260, 'img/electric/mer.jpeg', 'premium', 4.8, 5, 'Auto', 'Electric'),
(72, 'Passat', 'Volkswagen', 'sedan', 120, 'img/sedan/pass.jpeg', '', 4.6, 5, 'Auto', 'Diesel'),
(73, 'Toyota Highlander', 'Toyota', 'family', 165, 'img/family/toyo.jpeg', '', 4.6, 7, 'Auto', 'Hybrid'),
(74, 'Ford Explorer', 'Ford', 'family', 155, 'img/family/ford.jpeg', '', 4.6, 7, 'Auto', 'Petrol'),
(75, 'Chevrolet Traverse', 'Chevrolet', 'family', 150, 'img/family/cher.jpeg', '', 4.5, 7, 'Auto', 'Petrol'),
(76, 'Mazda CX-9', 'Mazda', 'family', 145, 'img/family/mazda.jpeg', '', 4.5, 7, 'Auto', 'Petrol'),
(77, 'Verna', 'Hyundai', 'sedan', 60, 'img/sedan/acc.jpeg', '', 4.3, 5, 'Auto', 'Petrol'),
(78, 'S60', 'Volvo', 'sedan', 170, 'img/sedan/c class.jpeg', 'premium', 4.8, 5, 'Auto', 'Hybrid'),
(79, 'Mazda 6', 'Mazda', 'sedan', 75, 'img/sedan/maz.jpeg', '', 4.5, 5, 'Auto', 'Petrol'),
(80, 'Quattroporte', 'Maserati', 'luxury', 480, 'img/luxury/maser.jpg', 'premium', 4.8, 5, 'Auto', 'Petrol'),
(81, 'Genesis G90', 'Genesis', 'luxury', 350, 'img/luxury/g90.jpeg', '', 4.7, 5, 'Auto', 'Petrol'),
(82, 'Explorer', 'Ford', 'suv', 150, 'img/suv/ford.jpeg', '', 4.6, 7, 'Auto', 'Petrol'),
(83, 'Jaguar F-Type', 'Jaguar', 'sports', 350, 'img/sports/jaguar.jpg', '', 4.8, 2, 'Auto', 'Petrol'),
(84, 'Lexus RC F', 'Lexus', 'sports', 270, 'img/sports/lexus.jpg', '', 4.7, 4, 'Auto', 'Petrol'),
(85, 'Mazda RX-7', 'Mazda', 'sports', 240, 'img/sports/mazda.jpg', 'classic', 4.8, 2, 'Manual', 'Petrol'),
(86, 'Mercedes G-Class', 'Mercedes', 'offroad', 500, 'img/offroad/mer.jpeg', 'premium', 5.0, 5, 'Auto', 'Petrol'),
(87, 'Nissan Patrol', 'Nissan', 'offroad', 300, 'img/offroad/nissan.jpg', '', 4.7, 7, 'Auto', 'Petrol'),
(88, 'Suzuki Jimny', 'Suzuki', 'offroad', 140, 'img/offroad/suzuki.jpeg', '', 4.5, 4, 'Manual', 'Petrol'),
(91, 'CX-5', 'Mazda', 'suv', 108, 'img/suv/mazda.jpeg', '', 4.5, 5, 'Auto', 'Petrol'),
(92, 'Ford Mustang 1965', 'Ford', 'vintage', 220, 'img/vintage/ford.jpg', 'classic', 4.9, 4, 'Manual', 'Petrol'),
(93, 'Chevrolet Bel Air', 'Chevrolet', 'vintage', 240, 'img/vintage/chvrolet.jpg', 'classic', 4.8, 5, 'Manual', 'Petrol'),
(94, 'Jaguar E-Type', 'Jaguar', 'vintage', 350, 'img/vintage/jaguar.jpg', 'premium', 5.0, 2, 'Manual', 'Petrol'),
(95, 'Polestar 2', 'Polestar', 'electric', 220, 'img/electric/polestar.jpeg', '', 4.7, 5, 'Auto', 'Electric'),
(96, 'ID.4', 'Volkswagen', 'electric', 175, 'img/electric/volk.jpeg', '', 4.5, 5, 'Auto', 'Electric'),
(100, 'Highlander', 'Toyota', 'suv', 160, 'img/suv/toyo.jpeg', '', 4.6, 7, 'Auto', 'Hybrid');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `txn_id` varchar(60) NOT NULL,
  `booking_id` varchar(30) NOT NULL,
  `car_name` varchar(150) DEFAULT '',
  `cust_name` varchar(100) DEFAULT '',
  `cust_email` varchar(100) DEFAULT '',
  `cust_phone` varchar(20) DEFAULT '',
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('card','upi','netbanking') NOT NULL,
  `method_detail` varchar(150) DEFAULT '',
  `status` enum('success','failed','pending') DEFAULT 'success',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `txn_id`, `booking_id`, `car_name`, `cust_name`, `cust_email`, `cust_phone`, `amount`, `payment_method`, `method_detail`, `status`, `created_at`) VALUES
(1, 'LUX-TXN-69D6361B-BB1B4214', 'LUXFEWC04B0P', 'Porsche 911 Carrera', 'Manish Yadanapuri', 'manishyadanapuri@gmail.com', '9004700418', 5665.00, 'upi', 'UPI: manishyadanapuri@okhdfcbank', 'success', '2026-04-08 11:03:55'),
(2, 'LUX-TXN-69E1B318-466B4C0A', 'LUX8YMX2EF73', 'Porsche 911 Carrera', 'Saif', 'saif@gmail.com', '9867454336', 2655.00, 'upi', 'UPI: QR/App → yadanapurimanish@okhdfcbank', 'success', '2026-04-17 04:12:08'),
(3, 'LUX-TXN-69E1DE9D-C3687D63', 'LUX4GGNHA5VR', 'Porsche 911 Carrera', 'Saif', 'saif@gmail.com', '9867454336', 3546.00, 'upi', 'UPI: QR/App → yadanapurimanish@okhdfcbank', 'success', '2026-04-17 07:17:49'),
(4, 'LUX-TXN-69EB27F7-CEEC5654', 'LUXVA4BY38UN', 'Rimac Rimac Nevera', 'Saif', 'saif@gmail.com', '9867454336', 4460.00, 'upi', 'UPI QR/App → yadanapurimanish@okhdfcbank', 'success', '2026-04-24 08:21:11'),
(5, 'LUX-TXN-69EB2887-61877056', 'LUXUO6ZH5N5U', 'Tesla Model S Plaid', 'Saif', 'saifkhan@gmail.com', '9867454336', 5848.00, 'upi', 'UPI QR/App → yadanapurimanish@okhdfcbank', 'success', '2026-04-24 08:23:35'),
(6, 'LUX-TXN-69EDE69D-2D7D8E74', 'LUXA0W8U0M9P', 'Mercedes E-Class', 'Manish Yadanapuri', 'yadanapurimanish@gmail.com', '9004886092', 2970.00, 'upi', 'UPI QR/App → yadanapurimanish@okhdfcbank', 'success', '2026-04-26 10:19:09'),
(7, 'LUX-TXN-69EDEEF9-7F99983B', 'LUX8KBNZQAF2', 'Mercedes E-Class', 'Manish Yadanapuri', 'yadanapurimanish@gmail.com', '9004886092', 4188.00, 'upi', 'UPI QR/App → yadanapurimanish@okhdfcbank', 'success', '2026-04-26 10:54:49'),
(8, 'LUX-TXN-69EDF038-340E257B', 'LUX2BZMBOPJC', 'Tesla Model S Plaid', 'Manish Yadanapuri', 'manishyadanapuri@gmail.com', '9004886092', 5414.00, 'upi', 'UPI QR/App → yadanapurimanish@okhdfcbank', 'success', '2026-04-26 11:00:08'),
(9, 'LUX-TXN-69F1DCF0-B09E6C93', 'LUXNSHF3C4TZ', 'Mercedes S-Class', 'saif', 'saif@gmail.com', '9867454336', 3341.00, 'upi', 'UPI QR/App → yadanapurimanish@okhdfcbank', 'success', '2026-04-29 10:26:56'),
(10, 'LUX-TXN-69F1DDC4-229CE7DC', 'LUXPMDXY2RC5', 'Tesla Model 3', 'Saif', 'saif@gmail.com', '09004886092', 2548.00, 'upi', 'UPI QR/App → yadanapurimanish@okhdfcbank', 'success', '2026-04-29 10:30:28'),
(11, 'LUX-TXN-69F4906E-07D874E3', 'LUXBAH1X14LG', 'Nissan Nissan GT-R', 'Manish Yadanapuri', 'manishyadanapuri@gmail.com', '09004886092', 2886.00, 'upi', 'UPI QR/App → yadanapurimanish@okhdfcbank', 'success', '2026-05-01 11:37:18'),
(12, 'LUX-TXN-69F4ABBD-29801FDD', 'LUX5XIJ1OKN0', 'Mercedes E-Class', 'Narshyya', 'narshyya@2004', '123659845', 4826.00, 'upi', 'UPI QR/App → yadanapurimanish@okhdfcbank', 'success', '2026-05-01 13:33:49'),
(13, 'LUX-TXN-69F6D2D0-B3F03418', 'LUXPUVUCFO7U', 'Tesla Model X', 'Mahesh', 'mahesh@gmail.com', '1236549878', 3980.00, 'upi', 'UPI QR/App → yadanapurimanish@okhdfcbank', 'success', '2026-05-03 04:45:04'),
(14, 'LUX-TXN-69F856B3-1656F700', 'LUXFV97U459R', 'Ferrari Ferrari F8', 'Manish Yadanapuri', 'yadanapurimanish@gmail.com', '9004886092', 12224.00, 'upi', 'UPI QR/App → yadanapurimanish@okhdfcbank', 'success', '2026-05-04 08:20:03'),
(15, 'LUX-TXN-69FCC236-D838F4BF', 'LUXKRNARSM51', 'Audi A4', 'Manish Yadanapuri', 'yadanapurimanish@gmail.com', '9004886092', 12243.00, 'upi', 'UPI QR/App → yadanapurimanish@okhdfcbank', 'success', '2026-05-07 16:47:50'),
(16, 'LUX-TXN-69FCC2F3-2DEC721F', 'LUXZLF04AZMQ', 'Rolls-Royce Ghost', 'Narshyya', 'narshyya@gmail.com', '9324440414', 8432.00, 'upi', 'UPI QR/App → yadanapurimanish@okhdfcbank', 'success', '2026-05-07 16:50:59'),
(17, 'LUX-TXN-69FE167F-AE3050A2', 'LUXTKPMQ1ODN', 'Pontiac Pontiac GTO 1964', 'Manish Yadanapuri', 'yadanapurimanish@gmail.com', '9004886092', 16489.00, 'upi', 'UPI QR/App → yadanapurimanish@okhdfcbank', 'success', '2026-05-08 16:59:43');

-- --------------------------------------------------------

--
-- Table structure for table `recently_viewed`
--

CREATE TABLE `recently_viewed` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `viewed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','suspended') DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `login_count` int(11) DEFAULT 0,
  `avatar_url` varchar(255) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `created_at`, `status`, `last_login`, `login_count`, `avatar_url`) VALUES
(1, 'Manish Yadanapuri', 'manishyadanapuri@gmail.com', '9004886092', '$2y$10$t3.g3CbbOMuYF5kcjCbulOz659a4ekV.7jTgeaqP/cY.o3hnbIuKq', '2026-03-12 14:35:51', 'active', '2026-05-06 17:30:23', 4, ''),
(2, 'Saif Khan', 'saifkhan@gmail.com', '9324440424', '$2y$10$Ij3QLyNrbAMMXa4aaDeL7OdgxYU7OX3NDcgRxJaQzJQmXyJ.A/UQO', '2026-03-12 14:36:59', 'active', '2026-05-03 10:35:28', 5, ''),
(3, 'Sajid Chandanoor', 'sajid@gmail.com', '8767581494', '$2y$10$Jg63wIHT1BLaBMOVOZC6mu3pxHWC4bKkPAQh.K18DITQmVaVmFQai', '2026-03-12 14:44:47', 'active', NULL, 0, ''),
(5, 'Izhan Khan', 'izhan@gmail.coom', '9234560987', '$2y$10$nw32v1ue4AvKUh18VzCbLef10SetpMFai/qYVQ1N5cMBZYofMoxRi', '2026-03-13 09:51:29', 'active', NULL, 0, ''),
(6, 'Yadanapuri Lavanya', 'yadanapurilavanya@gmail.com', '9324440414', '$2y$10$nkkAExl.J8plcvrKJsLPie5OY8DpAnbaLdk15eMGd6frlee4DVG16', '2026-04-08 04:50:47', 'active', NULL, 0, ''),
(7, 'mateen', 'mateen@gmail.com', '9769676969', '$2y$10$drceS3CCL.L57SpbTQ65neZXyEQc8zZg2BU2Asii521ut2fNWIVgu', '2026-04-17 04:13:47', 'active', NULL, 0, ''),
(8, 'kushey', 'khushey@gmail.com', '6524742552', 'khushey@2004', '2026-04-25 11:15:45', 'active', '2026-04-25 16:46:32', 1, ''),
(9, 'Manish Yadanapuri', 'yadanapurimanish@gmail.com', '9004886092', '$2y$10$/secw.m2H69C.T5ySeAyju.wgUsyfQzvJFN6WCzSN5hWjwEdIv9WC', '2026-04-26 10:15:31', 'active', '2026-05-09 08:34:47', 8, ''),
(10, 'mahesh', 'mahesh@gmail.com', '9867727292', '$2y$10$PnY.Kh.maex8/pppcLPcTet6.L7DgK6bOtDVaAQ.KL9NTtQeztBGO', '2026-04-26 10:24:34', 'active', '2026-05-06 11:00:40', 25, ''),
(11, 'Narshyya', 'narshyya@gmail.com', '231564895', '$2y$10$VTpEo59Efoz4Tc3p8z6E1.1Gt08n3AQHRnvzCpUVHRaIzJF3ZKJNa', '2026-05-01 13:32:14', 'active', '2026-05-07 22:18:44', 5, ''),
(12, 'Devesh', 'devesh@gmail.com', '9867727292', '$2y$10$O1Y3u4PelGCfQ8wBO2FNtuXDOeICCE3rwKi.yH20.gge045MrVIRe', '2026-05-06 05:42:10', 'active', '2026-05-06 11:12:46', 1, '');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `car_id` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id`, `user_id`, `car_id`, `added_at`) VALUES
(7, 10, 2, '2026-05-06 05:30:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_ref` (`booking_ref`);

--
-- Indexes for table `cars`
--
ALTER TABLE `cars`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `txn_id` (`txn_id`);

--
-- Indexes for table `recently_viewed`
--
ALTER TABLE `recently_viewed`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_user_car` (`user_id`,`car_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_user_car` (`user_id`,`car_id`),
  ADD KEY `car_id` (`car_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `recently_viewed`
--
ALTER TABLE `recently_viewed`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`car_id`) REFERENCES `cars` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
