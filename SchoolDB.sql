-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 19, 2024 at 04:06 PM
-- Server version: 8.0.37-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `SchoolDB`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `ID` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`ID`, `username`, `email`, `password`, `approved`) VALUES
(6, 'admin', 'admin@admin.com', '$2y$10$kLMm.TsNejyM8xRm1cXYe.D3bzuHRShoPUjjWuzKTdZQmWi00YYu2', 1),
(9, 'ZAIN', 'Zzainsheraz@gmail.com', '$2y$10$AZp0X3XnxntUBlPA2xataub8hueT6vPtK9oD2j4zrv/Fu3TfOzZru', 1);

-- --------------------------------------------------------

--
-- Table structure for table `Classes`
--

CREATE TABLE `Classes` (
  `ID` int NOT NULL,
  `ClassType` enum('Reception Year','Year One','Year Two','Year Three','Year Four','Year Five','Year Six') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `Name` varchar(255) DEFAULT NULL,
  `Capacity` int NOT NULL,
  `PupilAmount` int NOT NULL,
  `TeacherID` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Classes Table';

-- --------------------------------------------------------

--
-- Table structure for table `ParentGuardian`
--

CREATE TABLE `ParentGuardian` (
  `ID` int NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Address` varchar(522) NOT NULL,
  `Email` varchar(255) NOT NULL,
  `PhoneNumber` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Parent/Guardian Table';

--
-- Dumping data for table `ParentGuardian`
--

INSERT INTO `ParentGuardian` (`ID`, `Name`, `Address`, `Email`, `PhoneNumber`) VALUES
(5, 'zain', '1092a', 'sherazzain555@gmail.com', '03081746226');

-- --------------------------------------------------------

--
-- Table structure for table `Pupil`
--

CREATE TABLE `Pupil` (
  `ID` int NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Address` varchar(522) NOT NULL,
  `Age` int NOT NULL,
  `Height` double NOT NULL,
  `Weight` double NOT NULL,
  `BloodGroup` varchar(10) NOT NULL,
  `ClassEnrolledID` int NOT NULL,
  `Parent_Guardian_1_ID` int DEFAULT NULL,
  `Parent_Guardian_2_ID` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Pupil Table';

-- --------------------------------------------------------

--
-- Table structure for table `Teacher`
--

CREATE TABLE `Teacher` (
  `ID` int NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Address` varchar(522) NOT NULL,
  `PhoneNumber` varchar(255) NOT NULL,
  `AnnualSalary` int NOT NULL,
  `BackgroundCheck` tinyint(1) NOT NULL DEFAULT '0',
  `BackgroundSummary` varchar(522) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Teacher`
--

INSERT INTO `Teacher` (`ID`, `Name`, `Address`, `PhoneNumber`, `AnnualSalary`, `BackgroundCheck`, `BackgroundSummary`) VALUES
(6, 'asad', 'Multan', '03081746226', 500, 1, 'lorem25');

-- --------------------------------------------------------

--
-- Table structure for table `TeachingAssistant`
--

CREATE TABLE `TeachingAssistant` (
  `ID` int NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `Phone` varchar(25) NOT NULL,
  `Salary` int NOT NULL,
  `AssignedToTeacherID` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `Classes`
--
ALTER TABLE `Classes`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `TeacherID` (`TeacherID`);

--
-- Indexes for table `ParentGuardian`
--
ALTER TABLE `ParentGuardian`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `Pupil`
--
ALTER TABLE `Pupil`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `Parent/Guardian 1 ID` (`Parent_Guardian_1_ID`),
  ADD KEY `Parent/Guardian 2 ID` (`Parent_Guardian_2_ID`),
  ADD KEY `Class Enrolled ID` (`ClassEnrolledID`);

--
-- Indexes for table `Teacher`
--
ALTER TABLE `Teacher`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `TeachingAssistant`
--
ALTER TABLE `TeachingAssistant`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `AssignedToTeacherID` (`AssignedToTeacherID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `Classes`
--
ALTER TABLE `Classes`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ParentGuardian`
--
ALTER TABLE `ParentGuardian`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `Pupil`
--
ALTER TABLE `Pupil`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `Teacher`
--
ALTER TABLE `Teacher`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `TeachingAssistant`
--
ALTER TABLE `TeachingAssistant`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Classes`
--
ALTER TABLE `Classes`
  ADD CONSTRAINT `TeacherID` FOREIGN KEY (`TeacherID`) REFERENCES `Teacher` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `Pupil`
--
ALTER TABLE `Pupil`
  ADD CONSTRAINT `Class Enrolled ID` FOREIGN KEY (`ClassEnrolledID`) REFERENCES `Classes` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `Parent/Guardian 1 ID` FOREIGN KEY (`Parent_Guardian_1_ID`) REFERENCES `ParentGuardian` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `Parent/Guardian 2 ID` FOREIGN KEY (`Parent_Guardian_2_ID`) REFERENCES `ParentGuardian` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Constraints for table `TeachingAssistant`
--
ALTER TABLE `TeachingAssistant`
  ADD CONSTRAINT `AssignedToTeacherID` FOREIGN KEY (`AssignedToTeacherID`) REFERENCES `Teacher` (`ID`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
