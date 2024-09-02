-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 02, 2024 at 08:39 PM
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
-- Database: `www_wallet_co_za`
--
CREATE DATABASE IF NOT EXISTS `www_wallet_co_za` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `www_wallet_co_za`;

-- --------------------------------------------------------

--
-- Table structure for table `account_roles`
--

DROP TABLE IF EXISTS `account_roles`;
CREATE TABLE IF NOT EXISTS `account_roles` (
  `account_role_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_role_name` varchar(300) NOT NULL,
  `account_role_status` enum('active','suspended','deleted') NOT NULL DEFAULT 'active',
  `account_role_dateCreated` int(11) NOT NULL,
  `account_role_dateModified` int(11) NOT NULL,
  PRIMARY KEY (`account_role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `account_roles`
--

TRUNCATE TABLE `account_roles`;
--
-- Dumping data for table `account_roles`
--

INSERT INTO `account_roles` (`account_role_id`, `account_role_name`, `account_role_status`, `account_role_dateCreated`, `account_role_dateModified`) VALUES
(1, 'Admin', 'active', 1111111, 1111111),
(2, 'Customer', 'active', 1111111, 1111111);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

DROP TABLE IF EXISTS `transactions`;
CREATE TABLE IF NOT EXISTS `transactions` (
  `transaction_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `wallet_id` bigint(20) UNSIGNED NOT NULL,
  `transaction_typeID` int(11) NOT NULL DEFAULT 1,
  `amount` decimal(15,2) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','completed','failed') DEFAULT 'pending',
  `related_wallet_id` bigint(20) UNSIGNED DEFAULT NULL,
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `TransactionTypes`
--

DROP TABLE IF EXISTS `TransactionTypes`;
CREATE TABLE IF NOT EXISTS `TransactionTypes` (
  `transaction_type_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `transaction_type_name` varchar(100) NOT NULL,
  PRIMARY KEY (`transaction_type_id`),
  UNIQUE KEY `transaction_type_name` (`transaction_type_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Truncate table before insert `TransactionTypes`
--

TRUNCATE TABLE `TransactionTypes`;
--
-- Dumping data for table `TransactionTypes`
--

INSERT INTO `TransactionTypes` (`transaction_type_id`, `transaction_type_name`) VALUES
(1, 'Account Creation'),
(2, 'Deposit'),
(3, 'Transfer'),
(4, 'View Wallet');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_names` varchar(300) NOT NULL,
  `user_surname` varchar(300) NOT NULL,
  `user_email` varchar(300) DEFAULT NULL,
  `user_cellphone` varchar(300) NOT NULL,
  `user_status` enum('active','suspended','deleted') NOT NULL DEFAULT 'active',
  `user_genderiD` int(11) DEFAULT NULL,
  `user_dateCreated` int(11) NOT NULL,
  `user_dateModified` int(11) NOT NULL,
  `user_knownAs` varchar(300) DEFAULT NULL,
  `user_thumb` varchar(300) NOT NULL DEFAULT 'user.png',
  `user_key` varchar(300) DEFAULT NULL,
  `user_resetKey` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users_acl`
--

DROP TABLE IF EXISTS `users_acl`;
CREATE TABLE IF NOT EXISTS `users_acl` (
  `users_acl_id` int(11) NOT NULL AUTO_INCREMENT,
  `users_acl_userID` int(11) NOT NULL,
  `users_acl_roleID` int(11) NOT NULL,
  `users_acl_password` varchar(300) NOT NULL,
  `users_acl_status` enum('active','suspended','deleted') NOT NULL DEFAULT 'active',
  `users_acl_dateCreated` int(11) NOT NULL,
  `users_acl_dateModified` int(11) NOT NULL,
  PRIMARY KEY (`users_acl_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

DROP TABLE IF EXISTS `wallets`;
CREATE TABLE IF NOT EXISTS `wallets` (
  `wallet_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) NOT NULL DEFAULT 'ZAR',
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`wallet_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
