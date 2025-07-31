-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 31, 2025 at 03:30 AM
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
-- Database: `login_systemdb`
--
CREATE DATABASE IF NOT EXISTS `login_systemdb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `login_systemdb`;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(1000) NOT NULL,
  `role` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(4, 'testicle', 'test@gmail.com', '$2y$10$f3/FG7VTq2WdUSiKTJUdfOpR8EC1pvicyxqQtGbgynJN4fTTNutxm', 'user', '2025-03-31 01:10:04'),
(7, 'admin', 'admin@gmail.com', '$2y$10$HNczTnVEaPX7yPHfxdN65OqoqtoHKFTzZkdiR0abTM1u2VhTDY0hK', 'admin', '2025-03-30 13:56:18'),
(8, 'test4', 'test4@gmail.com', '$2y$10$Z5cD80azKaPbU/P5vSeEV.IfXQPfkPBgrDh2lQ/lB.oxS6JxouOjW', 'user', '2025-03-30 13:56:53'),
(11, 'test5', 'test5@gmail.com', '$2y$10$PVKgeYn1ueIebeA3Q4KLWu8ivH7fDEzwiVlsBUfBDSX4izcX6rni.', 'user', '2025-03-30 14:13:51'),
(13, 'test6', 'test6@gmail.com', '$2y$10$gL8xOaUInehrU84iYemcQefV2QLFi.4FGEX/z.iBrOmvXH4izn6.G', 'user', '2025-03-30 21:25:08'),
(14, 'test7', 'test7@gmail.com', '$2y$10$9qhzhAPBvHwSWSvmKvFwyeUjDUr2EFKUEM7yZGUVlOg/0Kifa3ZP.', 'user', '2025-03-30 21:26:20'),
(15, 'test8', 'test8@gmail.com', '$2y$10$9jVen6OvxWubInkQbzZog.L78pFnUd9XgIKTDq6XfViSaeZBhvgc6', 'user', '2025-03-30 21:37:05');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
