-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 24, 2026 at 04:33 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `homes_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accommodations`
--

CREATE TABLE `accommodations` (
  `accom_id` int(11) NOT NULL,
  `accom_slug` varchar(100) NOT NULL,
  `accom_name` varchar(200) NOT NULL,
  `accom_location` varchar(100) NOT NULL,
  `accom_price` decimal(10,2) NOT NULL,
  `accom_description` text DEFAULT NULL,
  `accom_bedrooms` int(11) DEFAULT 1,
  `accom_bathrooms` int(11) DEFAULT 1,
  `accom_max_guests` int(11) DEFAULT 2,
  `accom_amenities` text DEFAULT NULL,
  `accom_image_url` varchar(500) DEFAULT NULL,
  `accom_rating` decimal(2,1) DEFAULT 4.5,
  `accom_reviews` int(11) DEFAULT 0,
  `accom_tagline` varchar(200) DEFAULT NULL,
  `accom_security` varchar(200) DEFAULT NULL,
  `accom_featured` tinyint(1) DEFAULT 0,
  `accom_created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accommodations`
--

INSERT INTO `accommodations` (`accom_id`, `accom_slug`, `accom_name`, `accom_location`, `accom_price`, `accom_description`, `accom_bedrooms`, `accom_bathrooms`, `accom_max_guests`, `accom_amenities`, `accom_image_url`, `accom_rating`, `accom_reviews`, `accom_tagline`, `accom_security`, `accom_featured`, `accom_created_at`) VALUES
(1, 'hillcrest-nook', 'Hillcrest Nook', 'Mukono', 41.00, 'Cozy retreat in Kyetume lane', 1, 1, 2, 'WiFi,Kitchen,TV,Parking', NULL, 4.6, 128, 'Sink into cloud-like comfort after a day of adventure', '24/7 security with CCTV', 0, '2026-03-17 15:07:33'),
(2, 'goma-springs', 'Goma Springs Cottage', 'Mukono', 63.00, 'Luxury cottage on Mukono hill', 2, 2, 4, 'WiFi,Kitchen,Parking,Air Conditioning', NULL, 5.0, 256, 'Wake up in a bed so comfortable, you\'ll never want to leave', 'Gated community + night guard', 0, '2026-03-17 15:07:33'),
(3, 'seeta-silver', 'Seeta Silver Studio', 'Mukono', 38.00, 'Modern studio in Seeta', 1, 1, 2, 'WiFi,Kitchen,Smart TV,Parking', NULL, 5.0, 189, 'Luxury bedding that feels like sleeping on a cloud', 'Electronic safe + secure parking', 0, '2026-03-17 15:07:33'),
(4, 'victoria-lakefront', 'Victoria Lakefront Villa', 'Entebbe', 95.00, 'Luxury villa with lake views', 3, 3, 6, 'WiFi,Kitchen,Parking,Pool,Beach Access', NULL, 5.0, 312, 'Fall asleep to the gentle lapping of Lake Victoria\'s waves', 'Gated estate + 24/7 security patrol', 0, '2026-03-17 15:07:33'),
(5, 'airport-view', 'Airport View Gardens', 'Entebbe', 82.00, 'Convenient location near airport', 2, 2, 4, 'WiFi,Kitchen,Parking,Airport Shuttle', NULL, 4.8, 267, 'Luxury bedding awaits your arrival - just 5 minutes from the airport', 'Electronic gates + CCTV surveillance', 0, '2026-03-17 15:07:33'),
(6, 'botanical-beach', 'Botanical Beach House', 'Entebbe', 105.00, 'Tropical garden paradise', 3, 2, 6, 'WiFi,Kitchen,Garden,Parking', NULL, 4.9, 198, 'Where comfortable beds meet tropical garden paradise', 'Secure compound + night guard', 0, '2026-03-17 15:07:33'),
(7, 'kagera-river', 'Kagera River Lounge', 'Jinja', 81.00, 'Riverside retreat', 1, 1, 2, 'WiFi,Kitchen,River View,Deck', NULL, 4.8, 156, 'Drift off to sleep to the gentle lullaby of the river', '24/7 on-site caretaker + secure parking', 0, '2026-03-17 15:07:33'),
(8, 'rippon-falls', 'Rippon Falls View', 'Jinja', 110.00, 'Panoramic Nile views', 2, 2, 4, 'WiFi,Kitchen,Terrace,Panoramic View', NULL, 5.0, 203, 'Sleep on clouds while watching the sun rise over the Nile', 'Gated property + security cameras', 0, '2026-03-17 15:07:33'),
(9, 'masese-palm', 'Masese Palm Grove', 'Jinja', 68.00, 'Garden oasis', 3, 2, 6, 'WiFi,Kitchen,Garden,BBQ', NULL, 4.7, 98, 'Where comfortable beds meet tropical garden serenity', 'Enclosed compound + night watchman', 0, '2026-03-17 15:07:33'),
(10, 'cozy-corner', 'Cozy Corner', 'Mukono', 85.00, 'A charming cottage in the heart of Mukono', 2, 1, 4, 'WiFi,Kitchen,Parking', NULL, 4.8, 45, '\"A peaceful escape with a touch of luxury\"', '24/7 security with CCTV', 0, '2026-03-24 03:23:41');

-- --------------------------------------------------------

--
-- Table structure for table `configuration`
--

CREATE TABLE `configuration` (
  `config_id` int(11) NOT NULL,
  `config_key` varchar(100) NOT NULL,
  `config_value` text DEFAULT NULL,
  `config_updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `configuration`
--

INSERT INTO `configuration` (`config_id`, `config_key`, `config_value`, `config_updated_at`) VALUES
(1, 'site_title', 'HOMES DB', '2026-03-17 15:07:33'),
(2, 'contact_email', 'info@homes.com', '2026-03-17 15:07:33'),
(3, 'contact_phone', '+256 700 000000', '2026-03-17 15:07:33'),
(4, 'contact_address', 'Kampala, Uganda', '2026-03-17 15:07:33'),
(5, 'mobile_money_number', '+256 700 000000', '2026-03-17 15:07:33'),
(6, 'bank_name', 'Stanbic Bank Uganda', '2026-03-17 15:07:33'),
(7, 'bank_account_name', 'HOMES DB LTD', '2026-03-17 15:07:33'),
(8, 'bank_account_number', '9030012345678', '2026-03-17 15:07:33'),
(9, 'bank_branch', 'Kampala Main', '2026-03-17 15:07:33'),
(10, 'bank_swift_code', 'SBICUGKX', '2026-03-17 15:07:33'),
(11, 'service_fee_amount', '15', '2026-03-17 15:07:33');

-- --------------------------------------------------------

--
-- Table structure for table `inquiries`
--

CREATE TABLE `inquiries` (
  `inquiry_id` int(11) NOT NULL,
  `inquiry_name` varchar(100) NOT NULL,
  `inquiry_email` varchar(100) NOT NULL,
  `inquiry_phone` varchar(50) DEFAULT NULL,
  `inquiry_subject` varchar(200) DEFAULT NULL,
  `inquiry_message` text NOT NULL,
  `inquiry_status` enum('new','read','replied') DEFAULT 'new',
  `inquiry_created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `res_id` int(11) NOT NULL,
  `res_reference` varchar(50) NOT NULL,
  `res_property_slug` varchar(100) NOT NULL,
  `res_property_name` varchar(200) NOT NULL,
  `res_location` varchar(100) NOT NULL,
  `res_guest_name` varchar(100) NOT NULL,
  `res_guest_email` varchar(100) NOT NULL,
  `res_guest_phone` varchar(50) NOT NULL,
  `res_checkin` date NOT NULL,
  `res_checkout` date NOT NULL,
  `res_guests` int(11) NOT NULL,
  `res_nights` int(11) NOT NULL,
  `res_price_per_night` decimal(10,2) NOT NULL,
  `res_subtotal` decimal(10,2) NOT NULL,
  `res_service_fee` decimal(10,2) NOT NULL,
  `res_total` decimal(10,2) NOT NULL,
  `res_payment_method` enum('mobile_money','bank_transfer','card') NOT NULL,
  `res_payment_status` enum('pending','paid','failed') DEFAULT 'pending',
  `res_special_requests` text DEFAULT NULL,
  `res_booking_date` datetime NOT NULL,
  `res_status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `res_created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_content`
--

CREATE TABLE `tbl_content` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `location` varchar(100) DEFAULT 'Mukono',
  `price` decimal(10,2) DEFAULT 50.00,
  `slug` varchar(100) DEFAULT NULL,
  `tagline` varchar(200) DEFAULT NULL,
  `amenities` varchar(300) DEFAULT 'WiFi,Kitchen,Parking',
  `rating` decimal(2,1) DEFAULT 4.5,
  `reviews` int(11) DEFAULT 0,
  `security` varchar(200) DEFAULT '24/7 Security',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_content`
--

INSERT INTO `tbl_content` (`id`, `title`, `description`, `image_url`, `location`, `price`, `slug`, `tagline`, `amenities`, `rating`, `reviews`, `security`, `created_at`) VALUES
(1, 'Hillcrest Nook', 'Cozy retreat in Kyetume lane', NULL, 'Mukono', 41.00, 'hillcrest-nook', 'Sink into cloud-like comfort after a day of adventure', 'WiFi,Kitchen,TV,Parking', 4.6, 128, '24/7 security with CCTV', '2026-03-24 15:16:30'),
(2, 'Victoria Lakefront Villa', 'Luxury villa with breathtaking Lake Victoria views', NULL, 'Entebbe', 95.00, 'victoria-lakefront', 'Fall asleep to the gentle lapping of Lake Victoria\'s waves', 'WiFi,Kitchen,Parking,Pool,Beach Access', 5.0, 312, 'Gated estate + 24/7 security patrol', '2026-03-24 15:16:30'),
(3, 'Rippon Falls View', 'Panoramic Nile views in the heart of Jinja', NULL, 'Jinja', 110.00, 'rippon-falls', 'Sleep on clouds while watching the sun rise over the Nile', 'WiFi,Kitchen,Terrace,Panoramic View', 5.0, 203, 'Gated property + security cameras', '2026-03-24 15:16:30');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `user_fullname` varchar(100) NOT NULL,
  `user_email` varchar(100) NOT NULL,
  `user_password` varchar(255) NOT NULL,
  `user_role` enum('guest','admin') DEFAULT 'guest',
  `user_created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_fullname`, `user_email`, `user_password`, `user_role`, `user_created_at`) VALUES
(1, 'System Admin', 'admin@homes.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2026-03-17 15:07:33'),
(2, 'angella ', 'adminhomes@gmail.com', '$2y$10$4Fhg4Zp4Ydm2C5awLpEfy.sjE7k5YykCLW6jIQJlMLy.kBxW4IDVm', 'guest', '2026-03-17 15:51:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accommodations`
--
ALTER TABLE `accommodations`
  ADD PRIMARY KEY (`accom_id`),
  ADD UNIQUE KEY `accom_slug` (`accom_slug`);

--
-- Indexes for table `configuration`
--
ALTER TABLE `configuration`
  ADD PRIMARY KEY (`config_id`),
  ADD UNIQUE KEY `config_key` (`config_key`);

--
-- Indexes for table `inquiries`
--
ALTER TABLE `inquiries`
  ADD PRIMARY KEY (`inquiry_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`res_id`),
  ADD UNIQUE KEY `res_reference` (`res_reference`);

--
-- Indexes for table `tbl_content`
--
ALTER TABLE `tbl_content`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_email` (`user_email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accommodations`
--
ALTER TABLE `accommodations`
  MODIFY `accom_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `configuration`
--
ALTER TABLE `configuration`
  MODIFY `config_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `inquiries`
--
ALTER TABLE `inquiries`
  MODIFY `inquiry_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `res_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_content`
--
ALTER TABLE `tbl_content`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
