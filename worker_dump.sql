-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Aug 12, 2026 at 09:48 AM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `worker`
--

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

DROP TABLE IF EXISTS `booking`;
CREATE TABLE IF NOT EXISTS `booking` (
  `bid` int NOT NULL AUTO_INCREMENT,
  `cid` int NOT NULL,
  `jid` int NOT NULL,
  `crid` int NOT NULL,
  `wid` int NOT NULL,
  `bdate` date NOT NULL,
  `cbdate` date NOT NULL,
  `bstatus` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`bid`)
) ENGINE=MyISAM AUTO_INCREMENT=63 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`bid`, `cid`, `jid`, `crid`, `wid`, `bdate`, `cbdate`, `bstatus`) VALUES
(42, 0, 62, 7, 9, '2026-07-06', '2026-07-05', 3),
(43, 0, 62, 7, 9, '2026-07-23', '2026-07-05', 6),
(44, 0, 79, 8, 14, '2026-07-08', '2026-07-07', 0),
(46, 0, 79, 8, 10, '2026-07-09', '2026-07-08', 0),
(47, 0, 79, 8, 10, '2026-07-08', '2026-07-08', 0),
(41, 0, 62, 7, 9, '2026-07-05', '2026-07-05', 6),
(48, 0, 79, 9, 11, '2027-01-08', '2026-07-08', 0),
(55, 0, 79, 13, 11, '2026-08-12', '2026-08-05', 0),
(50, 0, 61, 10, 40, '2026-08-05', '2026-08-03', 0),
(53, 0, 79, 10, 11, '2026-08-05', '2026-08-04', 0),
(52, 0, 79, 10, 11, '2026-08-13', '2026-08-04', 0),
(54, 0, 79, 7, 11, '2026-08-14', '2026-08-05', 0),
(56, 0, 84, 10, 53, '2026-08-06', '2026-08-05', 2),
(57, 0, 79, 10, 11, '2026-08-20', '2026-08-10', 0),
(58, 0, 79, 10, 11, '2026-08-28', '2026-08-10', 0),
(59, 0, 62, 10, 50, '2026-08-12', '2026-08-12', 6),
(60, 0, 62, 10, 50, '2026-08-14', '2026-08-12', 2),
(61, 0, 62, 10, 50, '2026-08-20', '2026-08-12', 0),
(62, 0, 62, 10, 9, '2026-08-13', '2026-08-12', 0);

-- --------------------------------------------------------

--
-- Table structure for table `cancellation`
--

DROP TABLE IF EXISTS `cancellation`;
CREATE TABLE IF NOT EXISTS `cancellation` (
  `canid` int NOT NULL AUTO_INCREMENT,
  `bid` int NOT NULL,
  `wid` int NOT NULL,
  `crid` int NOT NULL,
  `candate` date NOT NULL,
  `canstatus` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`canid`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cancellation`
--

INSERT INTO `cancellation` (`canid`, `bid`, `wid`, `crid`, `candate`, `canstatus`) VALUES
(5, 40, 9, 7, '2026-06-29', 1),
(6, 45, 41, 8, '2026-07-07', 1),
(7, 51, 11, 10, '2026-08-04', 1),
(8, 49, 29, 7, '2026-08-05', 1);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `cid` int NOT NULL AUTO_INCREMENT,
  `cname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cdescription` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cimage` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cstatus` int DEFAULT '1',
  PRIMARY KEY (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`cid`, `cname`, `cdescription`, `cimage`, `cstatus`) VALUES
(24, 'Cleaning Services', 'Professionals responsible for cleaning, sanitizing, and maintaining homes, offices, hospitals, and public spaces', '6d3d5a72c4cdd6b164f248d97ce376d8_191aa4d6a1a64d75bc.jpg', 1),
(21, 'Plumbing & Maintenance', 'Workers who install and repair water supply systems, drainage, sanitary fittings, and general maintenance.', '7d045de7b22f58b0b6511fa1e10269fe_3359996cd6ebbbd3aee.jpg', 1),
(19, 'Home Services', 'Professionals who provide household assistance, cleaning, cooking, caregiving, and home maintenance.', '7efea255bc1b17755c8a8410ade4ea57_469df454f85a2c6c.jpg', 1),
(23, 'Agriculture Works', 'Workers involved in farming, crop cultivation, harvesting, irrigation, dairy, and poultry activities.', '720d8b610adbeab317004eb0573941d4_fa0ff5bbe2b381.jpg', 1),
(20, 'Electrical Services', 'Experts who install, repair, and maintain electrical systems and household electrical appliances.', 'd4bd8a92a867c109585006ecc274f695_0dcfa1dc4bd.jpg', 1),
(18, 'Construction Works', 'Skilled workers who build, repair, and renovate residential and commercial buildings.', 'ca06e1e26c3da82e54d7bba1d554f2ed_e45a3443e7db9f.jpg', 1),
(25, 'Manufacturing & Factory Works', 'Workers engaged in production, assembly, packaging, quality inspection, and warehouse operations.', '1386e6e93c08a240ada5c9016d54416c_7bc9505d9301.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `cregistration`
--

DROP TABLE IF EXISTS `cregistration`;
CREATE TABLE IF NOT EXISTS `cregistration` (
  `crid` int NOT NULL AUTO_INCREMENT,
  `cname` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cage` int NOT NULL,
  `caddress` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cgender` char(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cphone` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cgmail` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cpassword` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cstatus` int NOT NULL DEFAULT '1',
  `c_reg_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `c_plan_type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'none',
  `c_plan_expires` date DEFAULT NULL,
  PRIMARY KEY (`crid`),
  UNIQUE KEY `cgmail` (`cgmail`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cregistration`
--

INSERT INTO `cregistration` (`crid`, `cname`, `cage`, `caddress`, `cgender`, `cphone`, `cgmail`, `cpassword`, `cstatus`, `c_reg_date`, `c_plan_type`, `c_plan_expires`) VALUES
(6, 'ashwin', 22, 'aaa', 'm', '1010101010', 'ashwin@gmail.com', 'ashwin2004', 1, '2026-08-05 09:36:16', 'none', NULL),
(7, 'alby', 22, 'dvdg', 'm', '1111111111', 'alby@gmail.com', 'alby2004', 1, '2026-08-05 09:36:16', 'monthly', '2026-09-05'),
(8, 'ashik', 23, 'xyt', 'm', '2345678912', 'ashik@gmail.com', 'ashik2004', 1, '2026-08-05 09:36:16', 'none', NULL),
(9, 'qwerty', 1, 'qwerty', 'f', '1234567891', 'q@gmail.com', 'qwerty', 1, '2026-08-05 09:36:16', 'none', NULL),
(10, 'Alen shaju', 19, 'qwerty', 'm', '1234567891', 'shajualen96@gmail.com', 'alen2005', 1, '2026-08-05 09:36:16', 'annual', '2027-08-10'),
(11, 'amal', 21, 'qwerty', 'm', '9072041139', 'amalamaljoy2004@gmail.com', 'amal2004', 1, '2026-08-05 09:36:16', 'none', NULL),
(12, 'ashik', 18, 'qwerty', 'm', '9072041133', 'shajualen96@depaul.edu.in', 'alen1234', 1, '2026-08-05 09:36:16', 'none', NULL),
(13, 'amal', 25, 'qwerty', 'm', '9072041133', 'amal@gmail.com', 'amalamal', 1, '2026-08-05 09:54:06', 'none', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `employee`
--

DROP TABLE IF EXISTS `employee`;
CREATE TABLE IF NOT EXISTS `employee` (
  `eid` int NOT NULL AUTO_INCREMENT,
  `eage` int NOT NULL,
  `ename` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`eid`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee`
--

INSERT INTO `employee` (`eid`, `eage`, `ename`) VALUES
(1, 20, 'alen');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

DROP TABLE IF EXISTS `feedback`;
CREATE TABLE IF NOT EXISTS `feedback` (
  `fid` int NOT NULL AUTO_INCREMENT,
  `crid` int NOT NULL,
  `wid` int NOT NULL,
  `bid` int NOT NULL,
  `fdate` date NOT NULL,
  `frating` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fmessage` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `fstatus` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`fid`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`fid`, `crid`, `wid`, `bid`, `fdate`, `frating`, `fmessage`, `fstatus`) VALUES
(8, 7, 9, 42, '2026-07-07', '5', 'good', 1),
(9, 10, 40, 50, '2026-08-05', '5', 'hloo', 1),
(10, 7, 9, 42, '2026-08-05', '5', 'hloo', 1),
(11, 10, 40, 50, '2026-08-10', '5', 'f,gbafj', 1),
(12, 10, 40, 50, '2026-08-12', '5', 'good', 1),
(13, 10, 50, 59, '2026-08-12', '5', 'good\r\n', 1);

-- --------------------------------------------------------

--
-- Table structure for table `job`
--

DROP TABLE IF EXISTS `job`;
CREATE TABLE IF NOT EXISTS `job` (
  `jid` int NOT NULL AUTO_INCREMENT,
  `cid` int NOT NULL,
  `jname` varchar(50) NOT NULL,
  `jdescription` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `jimage` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `jstatus` int NOT NULL DEFAULT '1',
  PRIMARY KEY (`jid`)
) ENGINE=MyISAM AUTO_INCREMENT=88 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `job`
--

INSERT INTO `job` (`jid`, `cid`, `jname`, `jdescription`, `jimage`, `jstatus`) VALUES
(84, 25, 'Warehouse Worker', 'Organizes and stores goods in warehouses', '0e4061f55e48012f04e68430689914e3_b5aee4e1eb34f4dc4.jpg', 1),
(83, 25, 'Assembly Worker', 'Assembles parts into finished products.', '77a8da39838e66b8d9ee8940c7256d94_cb0357907170558eaa.jpg', 1),
(82, 25, 'Production Worker', 'Assists in manufacturing products.', '68aa0918baefe1681a859cdb723b19ef_f09642281ff2345cd9.jpg', 1),
(79, 24, 'Waste Collector', 'Collects and disposes of waste safely.', '31b382009a1508e7ebcbd0aa51c5800d_37a7080ebf2fd5.jpg', 1),
(78, 24, 'Hospital Cleaner', 'Sanitizes hospital rooms and equipment.', 'd5467cce6f468c645d637f8f38c5f832_25e8fbb17bbb412ee4f.jpg', 1),
(68, 21, 'Plumber', 'Fixes water pipes and plumbing fixtures.', '3a7818827f4f5ee65b5f3a06cc0e6276_d78878e2c1f0646a.jpg', 1),
(69, 21, 'Water Tank Cleaner', 'Cleans and disinfects water tanks.', '84099ecd8de54f5013b272504409703c_60499ec13d84f.jpg', 1),
(70, 21, 'Pump Technician', 'Repairs water pumps and motors.', 'e316d613fc113a3de9258c84ebfcc2a6_da891db4a6659243ec9.jpg', 1),
(71, 21, 'Sanitary Installer', 'Installs toilets, sinks, and sanitary fittings.', '8facd960869507affbcc59a0c5cc1c27_456934d797d.jpg', 1),
(72, 21, 'Maintenance Worker', 'Performs general building maintenance tasks.', '557fc2d42f2fa461b32c5d17a73728b5_5a93a1dd30.jpg', 1),
(73, 23, 'Farmer', 'Grows crops and manages farmland.', 'abf82894c42764c62febfe4723e99e1b_691fb65bee1f668ad5b.jpg', 1),
(74, 23, 'Dairy Worker', 'Cares for dairy animals and milk production.', '956901d42ebf25a373a414eba26e00d7_641adc49d10c23411.jpg', 1),
(75, 23, 'Irrigation Worker', 'Operates farm irrigation systems.', 'b9bedc2a22dfcd26e869d875c9796607_488a883cd2e5f94a.jpg', 1),
(76, 23, 'Greenhouse Worker', 'Maintains crops inside greenhouses.', '3561388ce88d9e262455d0bb855ec427_115e7d6639.jpg', 1),
(77, 24, 'Office Cleaner', 'Cleans office spaces and work areas.', '0ef662da711cb1645f0dbb33b39844b0_4b10c97f8ee11a5a9.jpg', 1),
(62, 20, 'AC Technician', 'Services and repairs air conditioners.', '73edcecfb7f30a1f4e3d9c161b8554bb_342703f58536cde8dc07.jpg', 1),
(61, 20, 'Electrician', 'Repairs and installs electrical systems.', '30be11cac946e620f7eaac42bb474777_75159f069076a7818.jpg', 1),
(60, 19, 'Laundry Worker', 'Washes, dries, and irons clothes.', '6d88275ba87d91418e0132373ec7fbab_99faed63972cee70.jpg', 1),
(52, 18, 'Painter', 'Paints walls, ceilings, and building surfaces.', '0c99bd9eeb4c26ce3d4d554475e47b7a_6f97f4d8942a.jpg', 1),
(53, 18, 'Welder', 'Joins metal parts using welding equipment.', '7dd07d3686ecceb3cf726d5b53ea3cd9_cb2b4fa7710.jpg', 1),
(54, 18, 'Wood Cutter (Tree Cutter)', 'Cuts trees and trims branches safely.', 'd73d32ebb8e51b6f1898a0a642db917e_ee5bfa7937032c20fb70.jpg', 1),
(55, 18, 'Tile Installer', 'Installs floor and wall tiles accurately.', '03d2ecc8a70439c850b428ecee21852f_2ec4aa3ba16e63c1.jpg', 1),
(56, 19, 'House Cleaner', 'Cleans homes and keeps rooms tidy.', 'd061f99fe4d7d2ac583aaca7e9ea6b23_a4ad22977a7d.jpg', 1),
(57, 19, 'Housekeeper', 'Manages household cleaning and daily chores.', '01f4412d793a8c5608679d384020c950_532445538a9ea90.jpg', 1),
(58, 19, 'Cook', 'Prepares meals for homes and families.', 'fe801f79382263955d730903a8ef6a58_cc9f5226ca0ff27a9.jpg', 1),
(81, 25, 'Machine Operator', 'Operates industrial production machines.', '0a09a7ec772fc7bd0b3c6c7419d9c0d5_c2b301de6b96.jpg', 1),
(80, 24, 'Floor Polisher', 'Cleans and polishes hard floors.', '1a015bb6492aa1e299ebda985d9a7a88_7042a121917c0.jpg', 1),
(63, 20, 'Refrigerator Technician', 'Fixes refrigerator cooling problems.', '94f44601588b23bc1510b2fe3cc7cf8d_961572b5e7.jpg', 1),
(64, 20, 'TV Repair Technician', 'Repairs televisions and display systems.', '58862c3f8e8fada189d97452dd752342_8e549da46d602ec692.jpg', 1),
(65, 20, 'CCTV Installer', 'Installs and configures CCTV cameras.', '7069a7f29a1fdd358ba05250afc09df9_38f522f225941.jpg', 1),
(66, 20, 'Solar Panel Technician', 'Installs and maintains solar power systems.', '01e442ef95c9595a45fa023346fb7cb6_468316cfebda753ada.jpg', 1),
(67, 20, 'Inverter Technician', 'Repairs power inverters and battery systems.', '4ade8109c8e833e3f4f7f6e64d0d924b_4930c905418bc0.jpg', 1),
(59, 19, 'Elder Caregiver', 'Assists elderly people with daily activities.', 'b930cc28b0985541e4481126a73383ed_bae86df0479.jpg', 1),
(50, 18, 'Mason', 'Builds walls and structures using bricks and concrete.', 'b6d41927e970e776cfeeda5932692154_ea925a98cf155fe89eb.jpg', 1),
(51, 18, 'Carpenter', 'Creates and repairs wooden furniture and structures.', '8cf59d346289af06336f0c0a6b2af75d_553241e349710f.jpg', 1);

-- --------------------------------------------------------

--
-- Table structure for table `platform_payments`
--

DROP TABLE IF EXISTS `platform_payments`;
CREATE TABLE IF NOT EXISTS `platform_payments` (
  `pid` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `user_type` varchar(20) NOT NULL,
  `plan_type` varchar(20) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_id` varchar(100) NOT NULL,
  `payment_date` datetime NOT NULL,
  `expiry_date` date NOT NULL,
  `status` varchar(20) DEFAULT 'completed',
  PRIMARY KEY (`pid`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `platform_payments`
--

INSERT INTO `platform_payments` (`pid`, `user_id`, `user_type`, `plan_type`, `amount`, `payment_method`, `transaction_id`, `payment_date`, `expiry_date`, `status`) VALUES
(1, 10, 'customer', 'monthly', 199.00, 'UPI (GPay / PhonePe / Paytm)', 'TXN267137E5E5', '2026-08-05 09:44:52', '2026-09-05', 'completed'),
(2, 7, 'customer', 'monthly', 199.00, 'UPI (GPay / PhonePe / Paytm)', 'TXN6E2E6549A2', '2026-08-05 09:48:25', '2026-09-05', 'completed'),
(3, 7, 'customer', 'annual', 1499.00, 'Credit/Debit Card', 'TXNB7180B6D7C', '2026-08-05 09:52:47', '2027-08-05', 'completed'),
(4, 7, 'customer', 'monthly', 199.00, 'UPI (GPay / PhonePe / Paytm)', 'TXN3DAF28E44B', '2026-08-05 09:53:05', '2026-09-05', 'completed'),
(5, 9, 'worker', 'monthly', 299.00, 'UPI (GPay / PhonePe / Paytm)', 'WTXN8860FFE0AA', '2026-08-05 10:00:30', '2026-09-05', 'completed'),
(6, 53, 'worker', 'monthly', 299.00, 'UPI (GPay / PhonePe / Paytm)', 'WTXN58C55F0118', '2026-08-05 19:10:15', '2026-09-05', 'completed'),
(7, 55, 'worker', 'annual', 2499.00, 'Net Banking', 'WTXNBFDC71B938', '2026-08-06 18:40:53', '2027-08-06', 'completed'),
(8, 10, 'customer', 'annual', 1499.00, 'Credit/Debit Card', 'TXN124BCE8A75', '2026-08-10 14:01:55', '2027-08-10', 'completed'),
(9, 10, 'customer', 'monthly', 199.00, 'Credit/Debit Card', 'TXN67ACF97E0B', '2026-08-10 14:24:34', '2026-09-10', 'completed'),
(10, 10, 'customer', 'annual', 1499.00, 'Net Banking', 'TXN4E960AA5D7', '2026-08-10 14:24:39', '2027-08-10', 'completed'),
(11, 50, 'worker', 'monthly', 299.00, 'Credit/Debit Card', 'WTXNBB895944AC', '2026-08-10 18:48:03', '2026-09-10', 'completed');

-- --------------------------------------------------------

--
-- Table structure for table `wregistration`
--

DROP TABLE IF EXISTS `wregistration`;
CREATE TABLE IF NOT EXISTS `wregistration` (
  `wid` int NOT NULL AUTO_INCREMENT,
  `jid` int NOT NULL,
  `wname` varchar(50) NOT NULL,
  `wgmail` varchar(50) NOT NULL,
  `wage` int NOT NULL,
  `wgender` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `wdescription` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `wpass` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `wphone` char(10) NOT NULL,
  `wstatus` int NOT NULL DEFAULT '1',
  `w_reg_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `w_plan_type` varchar(20) DEFAULT 'none',
  `w_plan_expires` date DEFAULT NULL,
  PRIMARY KEY (`wid`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `wregistration`
--

INSERT INTO `wregistration` (`wid`, `jid`, `wname`, `wgmail`, `wage`, `wgender`, `wdescription`, `wpass`, `wphone`, `wstatus`, `w_reg_date`, `w_plan_type`, `w_plan_expires`) VALUES
(10, 79, 'amal devis', 'amal123@gmail.com', 22, 'm', 'Collects and disposes of household and commercial waste safely and efficiently.Service Charge: ₹350', 'amal123', '1234567890', 3, '2026-08-05 09:36:16', 'none', NULL),
(11, 79, 'ram kumar', 'ram@gmail.com', 21, 'm', 'Collects packed household and commercial waste for safe disposal.  Service Charge: ₹250 per hour.', 'ramkumar', '0987654321', 2, '2026-08-05 09:36:16', 'none', NULL),
(12, 79, 'sahil', 'sahil@gmail.com', 35, 'm', 'Collects packed household and commercial waste for safe disposal.  Service Charge: ₹250 per hour.', '1234567890', '8976564578', 3, '2026-08-05 09:36:16', 'none', NULL),
(9, 62, 'adhul', 'adhul@gmail.com', 22, 'm', 'Provides AC installation, repair, and maintenance services.\r\n\r\nService Charge: ₹500 per hour. Additional charges may apply for spare parts.', 'adhul2004', '9988776655', 2, '2026-08-05 09:36:16', 'monthly', '2026-09-05'),
(13, 79, 'sanjay', 'sanjay@gmail.com', 30, 'm', 'Collects all household or commercial waste directly from the premises and ensures safe disposal.  Service Charge: ₹400 per hour.', '1234567890', '8976564578', 2, '2026-08-05 09:36:16', 'none', NULL),
(14, 79, 'roy', 'roy@gmail.com', 39, 'm', 'Collects food and kitchen waste from homes or commercial spaces for proper disposal.Service Charge ₹500', 'roy12345', '9675424678', 2, '2026-08-05 09:36:16', 'none', NULL),
(15, 78, 'midhun', 'midhun@gmail.com', 36, 'm', 'Cleans and sanitizes hospital rooms, corridors, and other healthcare areas to maintain hygiene.  Service Charge: ₹300 per hour.', 'midhun12345', '0987654324', 2, '2026-08-05 09:36:16', 'none', NULL),
(16, 78, 'bony', 'bony@gmail.com', 40, 'm', 'Cleans and sanitizes hospital rooms.  Service Charge: ₹350 per hour.', 'bony12345', '3245678902', 2, '2026-08-05 09:36:16', 'none', NULL),
(17, 78, 'joy', 'joy@gmail.com', 39, 'm', 'Professionally cleans and sanitizes hospital equipment to ensure safety and hygiene.  Service Charge: ₹600 per hour.', 'joy12345', '3245673456', 2, '2026-08-05 09:36:16', 'none', NULL),
(18, 78, 'celine', 'celine@gmail.com', 35, 'f', 'Cleans and sanitizes hospital beds to maintain a safe and hygienic environment.  Service Charge: ₹300 per hour.', 'celine12345', '3245673343', 2, '2026-08-05 09:36:16', 'none', NULL),
(19, 68, 'manoj', 'manoj@gmail.com', 37, 'm', 'Provides professional plumbing services, including pipe repairs, leak fixing, and installation of plumbing fixtures.  Service Charge: ₹1,500 per day.', 'manoj12345', '3245674543', 2, '2026-08-05 09:36:16', 'none', NULL),
(20, 68, 'sreekumar', 'sreekumar@gmail.com', 38, 'm', 'Installs and repairs PVC pipes and fittings for homes and commercial buildings.  Service Charge: ₹800 per day.', 'sreekumar12345', '8765463728', 2, '2026-08-05 09:36:16', 'none', NULL),
(21, 68, 'bijoy', 'bijoy@gmail.com', 46, 'm', 'Installs and repairs PVC pipes and fittings for homes and commercial buildings.  Service Charge: ₹800 per day.', 'bijoy12345', '1234563452', 2, '2026-08-05 09:36:16', 'none', NULL),
(22, 69, 'nithin', 'nithin@gmail.com', 26, 'm', 'Cleans and sanitizes water tanks to ensure safe and hygienic water storage.  Service Charge: ₹400 per hour.', 'nithin12345', '5678349034', 2, '2026-08-05 09:36:16', 'none', NULL),
(23, 69, 'praveen', 'praveen@gmail.com', 24, 'm', 'Cleans water tanks and repairs leaks to ensure safe and efficient water storage.  Service Charge: ₹350 per hour.', 'praveen12345', '5677339054', 2, '2026-08-05 09:36:16', 'none', NULL),
(24, 69, 'shaji', 'shaji@gmail.com', 46, 'm', 'Installs new water tanks and connects all required water supply fittings professionally.  Service Charge: ₹1,100 per day.', 'shaji12345', '5677333356', 2, '2026-08-05 09:36:16', 'none', NULL),
(25, 60, 'deepa', 'deepa@gmail.com', 34, 'f', 'Washes, dries, and folds clothes with care for homes and businesses.  Service Charge: ₹700 per day.', 'deepa12345', '1234567897', 2, '2026-08-05 09:36:16', 'none', NULL),
(26, 60, 'rama', 'rama@gmail.com', 46, 'f', 'Washes clothes with care for homes .  Service Charge: ₹600 per day.', 'rama12345', '1734467897', 2, '2026-08-05 09:36:16', 'none', NULL),
(27, 60, 'meenakshi', 'meenakshi@gmail.com', 37, 'f', 'Washes clothes .  Service Charge: ₹500 per day.', 'meenakshi12345', '1734867897', 2, '2026-08-05 09:36:16', 'none', NULL),
(28, 56, 'jicy', 'jicy@gmail.com', 41, 'm', 'Cleans homes, including rooms, kitchens, bathrooms, and floors, to maintain a neat and hygienic environment.  Service Charge: ₹800 per day.', 'jicy12345', '1234563245', 2, '2026-08-05 09:36:16', 'none', NULL),
(29, 56, 'haritha', 'haritha@gmail.com', 43, 'f', 'Provides general home cleaning services to keep your living space clean and tidy.  Service Charge: ₹700 per day.', 'haritha12345', '1234563243', 2, '2026-08-05 09:36:16', 'none', NULL),
(30, 56, 'liji', 'liji@gmail.com', 49, 'f', 'Cleans home windows, doors, and wooden surfaces professionally for a spotless finish.  Service Charge: ₹750 per hour.', 'liji12345', '1234593253', 2, '2026-08-05 09:36:16', 'none', NULL),
(31, 73, 'thomas', 'thomas@gmail.com', 67, 'm', 'Performs farming activities such as planting, watering, harvesting, and general field maintenance.  Service Charge: ₹1,000 per day.', 'thomas12345', '3214593254', 2, '2026-08-05 09:36:16', 'none', NULL),
(32, 73, 'paul', 'paul@gmail.com', 58, 'm', 'Provides small-scale planting, grass cleaning, and general farm maintenance services.  Service Charge: ₹1,100 per day.', 'paul12345', '7214193251', 2, '2026-08-05 09:36:16', 'none', NULL),
(33, 73, 'johnson', 'johnson@gmail.com', 60, 'm', 'Provides modern farming services using advanced techniques for planting, crop care, and efficient field maintenance.  Service Charge: ₹1,200 per day.', 'johnson12345', '7214193251', 2, '2026-08-05 09:36:16', 'none', NULL),
(34, 74, 'santhosh', 'santhosh@gmail.com', 60, 'm', 'Provides professional dairy farm services, including milking, feeding, and caring for livestock.  Service Charge: ₹800 per hour.', 'sathosh12345', '7214193251', 2, '2026-08-05 09:36:16', 'none', NULL),
(35, 74, 'george', 'george@gmail.com', 66, 'm', 'Provides  dairy farm services, including milking, feeding  Service Charge: ₹650 per hour.', 'george12345', '7214193261', 2, '2026-08-05 09:36:16', 'none', NULL),
(36, 74, 'martin', 'martin@gmail.com', 45, 'm', 'Provides professional milking services while ensuring proper hygiene and animal care.  Service Charge: ₹700 per hour.', 'martin12345', '3245678961', 2, '2026-08-05 09:36:16', 'none', NULL),
(37, 62, 'jeswin', 'jeswin@gmail.com', 28, 'm', 'Installs, services, and repairs air conditioners to ensure efficient cooling and performance.  Service Charge: ₹600 per hour.', 'jeswin12345', '7768890423', 2, '2026-08-05 09:36:16', 'none', NULL),
(38, 62, 'jobal', 'jobal@gmail.com', 26, 'm', 'services, and repairs air conditioners to ensure efficient cooling and performance.  Service Charge: ₹650 per hour.', 'jobal12345', '7768790526', 2, '2026-08-05 09:36:16', 'none', NULL),
(39, 61, 'Abin', 'abin@gmail.com', 25, 'm', 'Installs, repairs, and maintains electrical wiring, switches, lights, and other electrical systems.  Service Charge: ₹700 per hour.', 'abin12345', '7468797526', 2, '2026-08-05 09:36:16', 'none', NULL),
(40, 61, 'Arjun', 'Arjun@gmail.com', 27, 'm', 'Performs electrical inspections, troubleshooting, and maintenance for homes and commercial buildings.  Service Charge: ₹650 per hour.', 'arjun12345', '7448797521', 2, '2026-08-05 09:36:16', 'none', NULL),
(41, 52, 'jinto johny', 'jinto@gmail.com', 32, 'm', 'Provides professional interior and exterior painting services for homes and commercial buildings.  Service Charge: ₹1,300 per day.', 'jinto12345', '7448797576', 2, '2026-08-05 09:36:16', 'none', NULL),
(42, 52, 'jithin', 'jithin@gmail.com', 39, 'm', 'Paints and coats steel gates, railings, grills, and other metal surfaces to protect against rust and improve appearance.  Service Charge: ₹1,100 per day.', 'jithin12345', '7448597546', 2, '2026-08-05 09:36:16', 'none', NULL),
(43, 52, 'jeena', 'jeena@gmail.com', 36, 'f', 'Creates professional wall paintings and decorative murals for homes, offices, and commercial spaces.  Service Charge: ₹1,500 per day.', 'jeena12345', '8848597544', 2, '2026-08-05 09:36:16', 'none', NULL),
(44, 53, 'jibin', 'jibin@gmail.com', 47, 'm', 'Performs professional welding, metal fabrication, and repair services for homes and commercial projects.  Service Charge: ₹1,200 per day.', 'jibin12345', '7567345644', 2, '2026-08-05 09:36:16', 'none', NULL),
(45, 53, 'Dilin', 'dilin@gmail.com', 29, 'm', 'Repairs roofs and performs welding work for metal structures with professional quality.  Service Charge: ₹1,400 per day.', 'dilin12345', '9567345645', 2, '2026-08-05 09:36:16', 'none', NULL),
(46, 84, 'edwin', 'edwin@gmail.com', 28, 'm', 'Loads, unloads, organizes, and handles goods safely in warehouses and storage facilities.  Service Charge: ₹900 per day.', 'edwin12345', '9567945646', 2, '2026-08-05 09:36:16', 'none', NULL),
(47, 84, 'Anson', 'anson@gmail.com', 27, 'm', 'Packs, labels, and organizes products for safe storage and shipment.  Service Charge: ₹800 per day.', 'anson12345', '8567945646', 2, '2026-08-05 09:36:16', 'none', NULL),
(48, 83, 'Alfin', 'alfin@gmail.com', 25, 'm', 'Assembles products and components accurately while ensuring quality and safety standards.  Service Charge: ₹900 per day.', 'alfin12345', '6567985645', 2, '2026-08-05 09:36:16', 'none', NULL),
(49, 83, 'Adhil', 'adhil@gmail.com', 27, 'm', 'Performs product assembly, inspection, and packaging efficiently on the production line.  Service Charge: ₹950 per day.', 'adhil12345', '9567985647', 2, '2026-08-05 09:36:16', 'none', NULL),
(50, 62, 'alen', 'shajualen96@depaul.edu.in', 28, 'm', 'Installs, services, and repairs air conditioners to ensure efficient cooling and performance. Service Charge: ₹1000 per hour.', 'alen1234', '9072041139', 2, '2026-08-05 09:36:16', 'monthly', '2026-09-10'),
(51, 73, 'ashik', 'worldcupworldcup100000@gmail.com', 21, 'm', 'doing all jobs', 'ashik2000', '9072041139', 2, '2026-08-05 09:36:16', 'none', NULL),
(52, 61, 'alby', 'alen2005shaju@gmail.com', 18, 'm', 'Collects and disposes of household and commercial waste safely and efficiently.  Service Charge: ₹350 per hour.', 'alby1234', '1234567891', 2, '2026-08-05 09:36:16', 'none', NULL),
(53, 84, 'robin', 'robin@gmail.com', 25, 'm', 'Collects and disposes of household and commercial waste safely and efficiently.  Service Charge: ₹350 per hour.', 'robin1234', '1234567891', 2, '2026-08-05 18:58:18', 'monthly', '2026-09-05'),
(54, 73, 'jomy', 'jomy@gmail.com', 25, 'm', 'Collects and disposes of household and commercial waste safely and efficiently.  Service Charge: ₹350 per hour.', 'jomy1234', '0907204113', 2, '2026-08-05 19:25:13', 'none', NULL),
(55, 83, 'joji', 'joji@gmail.com', 22, 'm', 'Collects and disposes of household and commercial waste safely and efficiently.  Service Charge: ₹350 per hour.', 'joji1234', '9072041133', 2, '2026-08-06 18:36:43', 'annual', '2027-08-06');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
