-- phpMyAdmin SQL Dump
-- version 5.2.3-1.fc42
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 06, 2025 at 11:58 AM
-- Server version: 10.11.11-MariaDB
-- PHP Version: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `criminalinfo`
--

-- --------------------------------------------------------

--
-- Table structure for table `info`
--

CREATE TABLE `info` (
  `id` int(5) NOT NULL,
  `name` varchar(25) NOT NULL,
  `offname` varchar(25) NOT NULL,
  `crime` varchar(25) NOT NULL,
  `dob` date NOT NULL,
  `arrDate` date NOT NULL,
  `crimeDate` date NOT NULL,
  `sex` varchar(1) NOT NULL,
  `address` varchar(50) NOT NULL,
  `img` blob DEFAULT NULL,
  `more` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `info`
--

INSERT INTO `info` (`id`, `name`, `offname`, `crime`, `dob`, `arrDate`, `crimeDate`, `sex`, `address`, `img`, `more`) VALUES
(1002, 'John Doe', 'Mr. Smith', 'Robbery', '1985-05-15', '2023-01-10', '2023-01-05', 'M', '123 Main St, New York', 0x696d616765732f646f776e6c6f61642e6a7067, 'Section 392'),
(1003, 'Jane Smith', 'Detective Brown', 'Fraud', '1990-11-22', '2023-02-18', '2023-02-10', 'F', '456 Oak Ave, Los Angeles', 0x696d616765732f64656661756c742e6a7067, 'Section 420'),
(1004, 'Robert Johnson', 'Officer Davis', 'Murder', '1978-07-08', '2023-03-22', '2023-03-15', 'M', '789 Pine Rd, Chicago', 0x696d616765732f646f776e6c6f61642e6a7067, 'Section 302'),
(1005, 'Emily Williams', 'Detective Wilson', 'Kidnapping', '1992-03-30', '2023-04-12', '2023-04-05', 'F', '321 Elm St, Houston', 0x696d616765732f64656661756c742e6a7067, 'Section 363');

-- --------------------------------------------------------

--
-- Table structure for table `officer`
--

CREATE TABLE `officer` (
  `offName` varchar(25) NOT NULL,
  `offID` int(5) NOT NULL,
  `ID` int(5) NOT NULL,
  `contact` bigint(20) NOT NULL,
  `gender` varchar(1) NOT NULL,
  `weapon` varchar(20) NOT NULL,
  `role` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `officer`
--

INSERT INTO `officer` (`offName`, `offID`, `ID`, `contact`, `gender`, `weapon`, `role`) VALUES
('isah', 0, 5050, 8167928397, 'M', 'M107', 'API'),
('Mr.Peater', 1091, 1001, 9787414066, 'M', 'Pistol Auto 9mm 1A', 'API'),
('Detective Brown', 2001, 1003, 9876543210, 'M', 'Glock Pistol', 'API'),
('Officer Davis', 2002, 1004, 8765432109, 'M', 'S&W M&P', 'PSI'),
('Detective Wilson', 2003, 1005, 7654321098, 'F', 'Auto 9mm 1A', 'API'),
('Officer Taylor', 2004, 1002, 6543210987, 'F', 'M4', 'C'),
('Sgt. Miller', 2005, 1001, 5432109876, 'M', 'MP5 SMG', 'Sr.PI');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `uname` varchar(10) NOT NULL,
  `pass` varchar(255) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`uname`, `pass`, `role`) VALUES
('admin', 'admin', 'admin'),
('officer', '1234', 'user'),
('johndoe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('janesmit', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'user'),
('detectkim', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `info`
--
ALTER TABLE `info`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `officer`
--
ALTER TABLE `officer`
  ADD PRIMARY KEY (`offID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
