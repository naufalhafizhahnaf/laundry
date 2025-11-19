-- phpMyAdmin SQL Dump
-- version 6.0.0-dev+20251104.8b43d270dd
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 19, 2025 at 03:21 AM
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
(1, 'Tiyo', '$2y$10$zba7n5Li2pLxPxDMv3kg2.FxcO0QRTuPa830XlY.M2ZwVffxTJmeS', 'Lubentiyo', 'admin', 'jl.arjuna 2,no 34,bekasi timur', '089620238080', '2025-11-11 20:05:12', '1763518638_Logo Grexs G Ori.jpeg'),
(2, 'Naufal', '$2y$10$fAFX2hFBxRUdfn7sKs48H.c76s4CjuoV6JMVTe/P6V9bY/87tWbiK', 'Naufal', 'admin', NULL, NULL, '2025-11-12 15:11:49', NULL),
(4, 'Daffa', '$2y$10$SV0jkoi4JMeEsWh5fxnT5uLw4NP2N2kGds4dFgoEdOfTtFr/VBzsu', 'Daffa', 'user', 'Jl.fufufafa', '08233332111', '2025-11-15 11:57:43', '1763522485_Logo Grexs G Ori.jpeg');

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
(44, 'LDR-2025-0001', '6918091ed744a', 4, '2025-11-15 05:01:18', '2025-11-17 05:01:18', 'done', 'baju', 20.00, 160000.00, 'paid', 'qris', 'Express yaa mass', '2025-11-15 05:01:18', '2025-11-15 05:01:59'),
(45, 'LDR-2025-0045', '69180b66b1c3a', 4, '2025-11-15 05:11:02', '2025-11-17 05:11:02', 'cancelled', 'baju', 100.00, 800000.00, 'paid', 'qris', 'www', '2025-11-15 05:11:02', '2025-11-15 05:12:21'),
(46, 'LDR-2025-0046', '69180cb4178c3', 4, '2025-11-15 05:16:35', '2025-11-17 05:16:35', 'pending', 'selimut', 10.00, 100000.00, 'paid', 'qris', 'hhhh', '2025-11-15 05:16:35', '2025-11-15 05:16:47'),
(47, 'LDR-2025-0047', '69180ee7549d6', 4, '2025-11-15 05:25:59', '2025-11-17 05:25:59', 'pending', 'baju', 20.00, 160000.00, 'unpaid', 'qris', 'wkwkwk', '2025-11-15 05:25:59', '2025-11-15 05:25:59'),
(48, 'LDR-2025-0048', '69180f5338655', 4, '2025-11-15 05:27:47', '2025-11-17 05:27:47', 'pending', 'baju', 20.00, 160000.00, 'unpaid', 'qris', 'ss', '2025-11-15 05:27:47', '2025-11-15 05:27:47'),
(49, 'LDR-2025-0049', '69180fab29389', 4, '2025-11-15 05:29:15', '2025-11-17 05:29:15', 'pending', 'baju', 20.00, 160000.00, 'unpaid', 'qris', 'ss', '2025-11-15 05:29:15', '2025-11-15 05:29:15'),
(50, 'LDR-2025-0050', '69180fbaf06f1', 4, '2025-11-15 05:29:30', '2025-11-17 05:29:30', 'pending', 'baju', 20.00, 160000.00, 'unpaid', 'qris', 'ss', '2025-11-15 05:29:30', '2025-11-15 05:29:30'),
(51, 'LDR-2025-0051', '691810b5e2552', 4, '2025-11-15 05:33:41', '2025-11-17 05:33:41', 'pending', 'baju', 200.00, 1600000.00, 'unpaid', 'qris', 'ss', '2025-11-15 05:33:41', '2025-11-15 05:33:41'),
(52, 'LDR-2025-0052', '69181149d17f0', 4, '2025-11-15 05:36:09', '2025-11-17 05:36:09', 'pending', 'baju', 22.00, 176000.00, 'unpaid', 'qris', '', '2025-11-15 05:36:09', '2025-11-15 05:36:09'),
(53, 'LDR-2025-0053', '691811d57f8f3', 4, '2025-11-15 05:38:29', '2025-11-17 05:38:29', 'pending', 'baju', 20.00, 160000.00, 'unpaid', 'qris', '', '2025-11-15 05:38:29', '2025-11-15 05:38:29'),
(54, 'LDR-2025-0054', '691812dda4656', 4, '2025-11-15 05:42:53', '2025-11-17 05:42:53', 'pending', 'baju', 30.00, 240000.00, 'paid', 'qris', 'FUFUFAFA', '2025-11-15 05:42:53', '2025-11-15 05:43:07'),
(55, 'LDR-2025-0055', '691813303eff4', 4, '2025-11-15 05:44:16', '2025-11-17 05:44:16', 'pending', 'baju', 30.00, 240000.00, 'paid', 'qris', 'FUFUFAFA', '2025-11-15 05:44:16', '2025-11-15 05:44:26'),
(56, 'LDR-2025-0056', '69181a00eeeb7', 4, '2025-11-15 06:13:20', '2025-11-17 06:13:20', 'pending', 'baju', 15.00, 120000.00, 'paid', 'qris', '', '2025-11-15 06:13:20', '2025-11-15 06:14:09'),
(57, 'LDR-2025-0057', '691aca46baae9', 4, '2025-11-17 07:09:58', '2025-11-19 07:09:58', 'cancelled', 'karpet', 2000.00, 40000000.00, 'paid', 'qris', '', '2025-11-17 07:09:58', '2025-11-17 07:14:21');

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

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
