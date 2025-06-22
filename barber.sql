-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 21, 2025 at 10:03 AM
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
-- Database: `barber`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `service` varchar(255) NOT NULL,
  `barber` varchar(255) NOT NULL,
  `appointment_time` datetime NOT NULL,
  `status` enum('Scheduled','Finished','Cancelled','Confirmed','Ongoing') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `customer_name`, `email`, `phone`, `service`, `barber`, `appointment_time`, `status`, `created_at`) VALUES
(37, 'Nestlie Pangan', 'pangan@gmail.com', '09762192312', 'haircut', 'Angelo Pangan', '2025-03-21 16:00:00', 'Finished', '2025-03-21 06:07:14'),
(39, 'Angelo Pangan', 'angelopengen@gmail.com', '09219413963', 'haircut', 'John Froilan', '2025-03-21 16:00:00', 'Finished', '2025-03-21 07:40:37'),
(42, 'Angelo Pangan', 'angelopengen@gmail.com', '09219413963', 'haircut', 'John Froilan', '2025-03-21 16:00:00', 'Finished', '2025-03-21 07:46:42');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `role`) VALUES
(1, 'Angelo Pangan', 'angelopengen@gmail.com', '09219413963', '$2y$10$pVlyw9IcEv/Um6ldQZreqO1jD2aGn8nkYNcNwtEk4JN.wkyCRMXMe', 'customer'),
(2, 'Admin', 'admin@example.com', '12341415', '$2y$10$/0.YrxmfrNVwVWiSzwsBVOd2g1Z1D2oh637wp529GJ8R9aSgXs5D.', 'admin'),
(3, 'Francheska Alonzo', 'alonzocheskam@gmail.com', '0921312414', '$2y$10$9BhPnQsaCpz3L7dETWIJqOzKrg07Iqko6rjwbX4y5vEYYrOPhDmoW', 'customer'),
(4, 'Nestlie Pangan', 'pangan@gmail.com', '09762192312', '$2y$10$OKUVkIDpotwEAqR1JSjInu4U/YsbL1ygj1u86E3Hf8MRN9ZHVCBYi', 'customer'),
(5, 'Fred Fred', 'frederickgarcias@gmail.com', '0921312414', '$2y$10$Qbkfw1w1sfVcJ7APesw4K.DsPnGNKZ/94edSds8JtijuvX.is70IC', 'customer'),
(6, 'LJ Labonete', 'lj@gmail.com', '0921312414', '$2y$10$9DyP9HS3DpHNbT9j3FgUJ.Ba18G2wgYANkjGksOAIdfR1IL24C4U6', 'customer');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
