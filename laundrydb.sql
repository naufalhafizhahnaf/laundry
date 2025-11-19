-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20251104.8b43d270dd
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 19, 2025 at 03:42 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `laundrydb`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role` enum('admin','staff','user','customer') NOT NULL DEFAULT 'customer',
  `alamat` varchar(255) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `photo` varchar(255) DEFAULT 'default.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `account`
--

INSERT INTO `account` (`id`, `username`, `password`, `nama`, `role`, `alamat`, `no_hp`, `created_at`, `photo`) VALUES
(5, 'admin', '$2y$10$2qgROfX0U7gDkIxz06pb7e/Pvz60w8L7KgJcX7qZmtRc3DTvilxl.', 'ADMIN', 'admin', '', '', '2025-11-19 10:30:23', NULL),
(8, 'user', '$2y$10$Duzn0IremS6fTtWDCoxfq.giglet7q2WhNxRIKU2/CQlm4lmh3GzC', 'USER', 'user', 'J1 Kalimalang', '1122334455', '2025-11-19 10:41:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'admin',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `nama`, `role`, `created_at`) VALUES
(1, 'Tiyo', '$2y$10$a8arYRxX5yQr9pbb9k2kiObJRIjmjLZugNUzAoC5D9glXEkCW6c/m', 'Tiyo', 'admin', '2025-11-11 08:13:33'),
(2, 'Daffa', '$2y$10$0i2S3kOOlbME0nJGPIyiW.HavXzaH2e/uOvoNEEHITksw/e7MFz4q', 'Daffa', 'admin', '2025-11-11 08:19:35'),
(3, 'Admin', '$2y$10$2bEKvjgEwLlpzW..sYhC4eEYYbPLJb0t5dRl/8eQ0EsMc.T1HCsXG', 'Admin', 'admin', '2025-11-11 08:21:23'),
(4, 'Naufal', '$2y$10$Prfd0VZXRwBQYRpyoXDwdO1nfCNmkls4nF7J9rPnCc72LwMN7j/Ce', 'Naufal', 'admin', '2025-11-11 08:25:13'),
(9, 'Tita', '$2y$10$h2ej6ufLDWRlyJfUp3GzV.7TKwfOhdD.ssfx45GG2qhOKKrjhumNa', 'Tita', 'user', '2025-11-11 10:17:27');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `order_code` varchar(50) NOT NULL,
  `va_id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `account_id` int NOT NULL,
  `received_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `due_date` datetime DEFAULT NULL,
  `status` enum('pending','process','done','taken','cancelled') DEFAULT 'pending',
  `service_type` varchar(50) DEFAULT NULL,
  `weight` decimal(6,2) DEFAULT '0.00',
  `total_amount` decimal(12,2) DEFAULT '0.00',
  `payment_status` enum('unpaid','paid') DEFAULT 'unpaid',
  `payment_method` enum('cash','transfer','qris') DEFAULT 'cash',
  `note` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_code`, `va_id`, `account_id`, `received_date`, `due_date`, `status`, `service_type`, `weight`, `total_amount`, `payment_status`, `payment_method`, `note`, `created_at`, `updated_at`) VALUES
(58, 'LDR-2025-0001', '691d3c6f8f6ea', 8, '2025-11-19 03:41:35', '2025-11-21 03:41:35', 'process', 'baju', 40.00, 320000.00, 'paid', 'qris', '', '2025-11-19 03:41:35', '2025-11-19 03:42:14');

-- --------------------------------------------------------

--
-- Table structure for table `ss`
--

CREATE TABLE `ss` (
  `id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `order_code` (`order_code`),
  ADD KEY `account_id` (`account_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `account` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
