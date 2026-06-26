-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 26, 2026 at 02:06 PM
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
-- Database: `final_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `expense_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `created_by` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `expense_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`expense_id`, `group_id`, `created_by`, `title`, `category`, `amount`, `expense_date`, `created_at`) VALUES
(1, 1, 1, 'Clutch Bag', 'shopping', 1899.00, '2026-06-25', '2026-06-25 21:10:28'),
(2, 2, 2, 'Taxi to Hotel', 'transportation', 376.00, '2026-06-25', '2026-06-25 21:17:14'),
(3, 3, 1, 'McDonald', 'food', 367.00, '2026-06-26', '2026-06-25 23:52:04'),
(4, 6, 1, 'Bag', 'shopping', 2999.00, '2026-06-26', '2026-06-26 06:52:00');

-- --------------------------------------------------------

--
-- Table structure for table `groups_tb`
--

CREATE TABLE `groups_tb` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(100) NOT NULL,
  `invite_code` varchar(20) NOT NULL,
  `created_by` int(11) NOT NULL,
  `status` enum('active','archived') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `groups_tb`
--

INSERT INTO `groups_tb` (`group_id`, `group_name`, `invite_code`, `created_by`, `status`, `created_at`) VALUES
(1, 'Ella\'s Birthday Gift', 'SP-8ED562', 1, 'active', '2026-06-25 21:09:44'),
(2, 'Taipei Trip', 'SP-44427A', 2, 'active', '2026-06-25 21:13:10'),
(3, 'Dinner', 'SP-3DD5DB', 1, 'active', '2026-06-25 23:51:31'),
(4, 'Lunch', 'SP-BFD98A', 1, 'active', '2026-06-26 01:45:50'),
(5, 'Mom\'s Birthday Dinner', 'SP-3C1F70', 1, 'active', '2026-06-26 06:49:09'),
(6, 'Mom\'s Birthday Gift', 'SP-8BF071', 1, 'archived', '2026-06-26 06:51:27'),
(7, 'New Year Dinner', 'SP-B0249C', 1, 'active', '2026-06-26 06:56:07');

-- --------------------------------------------------------

--
-- Table structure for table `group_members`
--

CREATE TABLE `group_members` (
  `member_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_members`
--

INSERT INTO `group_members` (`member_id`, `group_id`, `user_id`, `joined_at`) VALUES
(1, 1, 1, '2026-06-25 21:09:44'),
(2, 1, 2, '2026-06-25 21:09:53'),
(3, 2, 2, '2026-06-25 21:13:10'),
(4, 2, 1, '2026-06-25 21:13:19'),
(5, 2, 3, '2026-06-25 21:16:07'),
(6, 3, 1, '2026-06-25 23:51:31'),
(7, 3, 2, '2026-06-25 23:51:49'),
(8, 4, 1, '2026-06-26 01:45:50'),
(9, 5, 1, '2026-06-26 06:49:09'),
(10, 5, 2, '2026-06-26 06:49:21'),
(11, 6, 1, '2026-06-26 06:51:27'),
(12, 6, 2, '2026-06-26 06:51:35'),
(13, 7, 1, '2026-06-26 06:56:07');

-- --------------------------------------------------------

--
-- Table structure for table `group_notes`
--

CREATE TABLE `group_notes` (
  `note_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `group_notes`
--

INSERT INTO `group_notes` (`note_id`, `group_id`, `user_id`, `message`, `created_at`) VALUES
(1, 1, 2, 'clarissa joined this group.', '2026-06-25 21:09:53'),
(2, 2, 1, '家詩 joined this group.', '2026-06-25 21:13:19'),
(3, 2, 3, 'Olivia joined this group.', '2026-06-25 21:16:07'),
(4, 3, 2, 'clarissa joined this group.', '2026-06-25 23:51:49'),
(5, 5, 2, 'clarissa joined this group.', '2026-06-26 06:49:21'),
(6, 6, 2, 'clarissa joined this group.', '2026-06-26 06:51:35');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `expense_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `status` enum('unpaid','pending','confirmed') DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `expense_id`, `user_id`, `amount`, `status`, `created_at`) VALUES
(1, 1, 1, 949.50, 'confirmed', '2026-06-25 21:10:28'),
(2, 1, 2, 949.50, 'confirmed', '2026-06-25 21:10:28'),
(3, 2, 2, 125.33, 'unpaid', '2026-06-25 21:17:14'),
(4, 2, 1, 125.33, 'unpaid', '2026-06-25 21:17:14'),
(5, 2, 3, 125.33, 'unpaid', '2026-06-25 21:17:14'),
(6, 3, 1, 183.50, 'confirmed', '2026-06-25 23:52:04'),
(7, 3, 2, 183.50, 'confirmed', '2026-06-25 23:52:04'),
(8, 4, 1, 1499.50, 'confirmed', '2026-06-26 06:52:00'),
(9, 4, 2, 1499.50, 'confirmed', '2026-06-26 06:52:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `role`, `created_at`) VALUES
(1, '家詩', 'a1133329@mail.nuk.edu.tw', '$2y$10$7O4sLsNg2ITSZ6uPYb5cOuBAejSdr4A2RWDedLGHuO9RzgsWJuzXi', 'user', '2026-06-25 21:08:23'),
(2, 'clarissa', 'clarissa.fredline06@gmail.com', '$2y$10$lDMZQD9rbcOk4fC2wjhRsOBeWAY9CPpqRNwaFVUv7zP7NHZPye4am', 'user', '2026-06-25 21:09:01'),
(3, 'Olivia', 'gesh.lili0000@gmail.com', '$2y$10$xR.RZEK0oh0DRkntsn0LS.E9BbPoxqGxIRwovxmNiusMPgU796t.6', 'user', '2026-06-25 21:15:44'),
(4, 'admin', 'adminproject71@gmail.com', '$2y$10$gJDg0cRdsVFIjDyJEzMbR.JuMeuACdH/j3s6CE5twFI/.x.BfG.l2', 'admin', '2026-06-26 01:25:34'),
(5, '111', '111@gmail.com', '$2y$10$t.3NdzAkl/T2G58drxhEpumyr7BxbeDNLBDLtX.eeOjt2zQ8e1wci', 'user', '2026-06-26 10:52:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `groups_tb`
--
ALTER TABLE `groups_tb`
  ADD PRIMARY KEY (`group_id`),
  ADD UNIQUE KEY `invite_code` (`invite_code`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `group_members`
--
ALTER TABLE `group_members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `group_id` (`group_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `group_notes`
--
ALTER TABLE `group_notes`
  ADD PRIMARY KEY (`note_id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `expense_id` (`expense_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `groups_tb`
--
ALTER TABLE `groups_tb`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `group_members`
--
ALTER TABLE `group_members`
  MODIFY `member_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `group_notes`
--
ALTER TABLE `group_notes`
  MODIFY `note_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups_tb` (`group_id`),
  ADD CONSTRAINT `expenses_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `groups_tb`
--
ALTER TABLE `groups_tb`
  ADD CONSTRAINT `groups_tb_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `group_members`
--
ALTER TABLE `group_members`
  ADD CONSTRAINT `group_members_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups_tb` (`group_id`),
  ADD CONSTRAINT `group_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `group_notes`
--
ALTER TABLE `group_notes`
  ADD CONSTRAINT `group_notes_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups_tb` (`group_id`),
  ADD CONSTRAINT `group_notes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`expense_id`) REFERENCES `expenses` (`expense_id`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
