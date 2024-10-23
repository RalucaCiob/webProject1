-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 23, 2024 at 10:52 AM
-- Server version: 8.3.0
-- PHP Version: 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `myc00289426_`
--

-- --------------------------------------------------------

--
-- Table structure for table `agents`
--

DROP TABLE IF EXISTS `agents`;
CREATE TABLE IF NOT EXISTS `agents` (
  `agentId` int NOT NULL AUTO_INCREMENT,
  `loginName` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `supportLevel` int NOT NULL,
  PRIMARY KEY (`agentId`),
  UNIQUE KEY `emailAddress` (`loginName`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `agents`
--

INSERT INTO `agents` (`agentId`, `loginName`, `password`, `firstName`, `lastName`, `supportLevel`) VALUES
(1, 'admin1@support.com', 'admin1@support.com', 'Ana', 'Aslan', 0),
(2, 'admin2@support.com', 'admin2@support.com', 'Ana', 'Admin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `elevated_tickets`
--

DROP TABLE IF EXISTS `elevated_tickets`;
CREATE TABLE IF NOT EXISTS `elevated_tickets` (
  `elevatedTicketId` int UNSIGNED NOT NULL,
  `reasonForElevation` text NOT NULL,
  `elevatedBy` varchar(255) NOT NULL,
  `assignedSupportLevel` int DEFAULT NULL,
  `dateCreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `openStatus` tinyint DEFAULT NULL,
  `resolvedBy` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`elevatedTicketId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `elevated_tickets`
--

INSERT INTO `elevated_tickets` (`elevatedTicketId`, `reasonForElevation`, `elevatedBy`, `assignedSupportLevel`, `dateCreated`, `openStatus`, `resolvedBy`) VALUES
(12, 'aaa', 'admin1@support.com', 1, '2024-10-23 11:47:48', 0, 'admin2@support.com');

-- --------------------------------------------------------

--
-- Table structure for table `supportcategory`
--

DROP TABLE IF EXISTS `supportcategory`;
CREATE TABLE IF NOT EXISTS `supportcategory` (
  `supportCategoryId` int UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`supportCategoryId`),
  KEY `idx_supportcategory_supportcategoryid` (`supportCategoryId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `supportcategory`
--

INSERT INTO `supportcategory` (`supportCategoryId`, `title`) VALUES
(1, 'Network'),
(2, 'Server'),
(3, 'Laptop/hardware'),
(4, 'Printer'),
(5, 'Other');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

DROP TABLE IF EXISTS `tickets`;
CREATE TABLE IF NOT EXISTS `tickets` (
  `ticketId` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `description` text NOT NULL,
  `resolution` text NOT NULL,
  `openedBy` varchar(255) NOT NULL,
  `dateCreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `openStatus` tinyint(1) NOT NULL DEFAULT '1',
  `assignedTo` varchar(255) DEFAULT NULL,
  `resolvedStatus` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ticketId`),
  KEY `idx_tickets_ticketid` (`ticketId`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`ticketId`, `description`, `resolution`, `openedBy`, `dateCreated`, `openStatus`, `assignedTo`, `resolvedStatus`) VALUES
(11, 'dhdssjsoosidoied', 'rrrrrr', 'raluca@support.com', '2024-10-23 10:46:36', 0, 'admin1@support.com', 1),
(12, 'jjd.siiijfw', 'fff', 'raluca@support.com', '2024-10-23 10:46:48', 0, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ticket_support_category`
--

DROP TABLE IF EXISTS `ticket_support_category`;
CREATE TABLE IF NOT EXISTS `ticket_support_category` (
  `ticketId` int UNSIGNED NOT NULL,
  `supportCategoryId` int UNSIGNED NOT NULL,
  PRIMARY KEY (`ticketId`,`supportCategoryId`),
  KEY `supportCategoryId` (`supportCategoryId`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ticket_support_category`
--

INSERT INTO `ticket_support_category` (`ticketId`, `supportCategoryId`) VALUES
(11, 2),
(12, 4);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `userId` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `loginName` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `firstName` varchar(50) NOT NULL,
  `lastName` varchar(50) NOT NULL,
  PRIMARY KEY (`userId`),
  UNIQUE KEY `loginName` (`loginName`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userId`, `loginName`, `password`, `firstName`, `lastName`) VALUES
(9, 'raluca@support.com', 'raluca', 'Raluca', 'Ciobanu');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ticket_support_category`
--
ALTER TABLE `ticket_support_category`
  ADD CONSTRAINT `ticket_support_category_ibfk_1` FOREIGN KEY (`ticketId`) REFERENCES `tickets` (`ticketId`),
  ADD CONSTRAINT `ticket_support_category_ibfk_2` FOREIGN KEY (`supportCategoryId`) REFERENCES `supportcategory` (`supportCategoryId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
