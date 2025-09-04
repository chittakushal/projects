-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2025 at 06:21 AM
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
-- Database: `hackathon`
--

-- --------------------------------------------------------

--
-- Table structure for table `document_submission_scores`
--

CREATE TABLE `document_submission_scores` (
  `user_id` int(11) NOT NULL,
  `hackathon_id` int(11) NOT NULL,
  `round_id` int(11) NOT NULL,
  `score` decimal(5,2) NOT NULL,
  `feedback` text DEFAULT NULL,
  `evaluated_by` int(11) DEFAULT NULL,
  `evaluated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_type`
--

CREATE TABLE `document_type` (
  `id` int(11) NOT NULL,
  `round_id` int(11) NOT NULL,
  `hackathon_id` int(11) NOT NULL,
  `statement` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_type_submissions`
--

CREATE TABLE `document_type_submissions` (
  `user_id` int(11) NOT NULL,
  `hackathon_id` int(11) NOT NULL,
  `round_id` int(11) NOT NULL,
  `filepath` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hackathons`
--

CREATE TABLE `hackathons` (
  `hackathon_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `type` enum('online','offline') NOT NULL,
  `venue` varchar(200) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `description` text NOT NULL,
  `content` text NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `org_id` int(11) NOT NULL,
  `is_certificates_issued` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `message_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `organizers`
--

CREATE TABLE `organizers` (
  `org_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `role` enum('organizer') DEFAULT 'organizer',
  `company_name_or_college_name` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `organizers`
--

INSERT INTO `organizers` (`org_id`, `name`, `email`, `role`, `company_name_or_college_name`, `password`) VALUES
(3, 'org1', 'org1@gmail.com', 'organizer', 'necn', '$2y$10$fnN/i.XBLJT9Mllj8Fs6oet/qKxurFTAz2HExP4xSthbM/19qOKhK');

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `user_id` int(11) NOT NULL,
  `hackathon_id` int(11) NOT NULL,
  `current_round` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_type`
--

CREATE TABLE `quiz_type` (
  `id` int(11) NOT NULL,
  `round_id` int(11) NOT NULL,
  `hackathon_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `op1` varchar(200) NOT NULL,
  `op2` varchar(200) NOT NULL,
  `op3` varchar(200) NOT NULL,
  `op4` varchar(200) NOT NULL,
  `correct_answer` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `quiz_type_score`
--

CREATE TABLE `quiz_type_score` (
  `user_id` int(11) NOT NULL,
  `hackathon_id` int(11) NOT NULL,
  `round_id` int(11) NOT NULL,
  `score` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rounds`
--

CREATE TABLE `rounds` (
  `id` int(11) NOT NULL,
  `sequence` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('quiztype','documenttype') NOT NULL,
  `hackathon_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `round_timers`
--

CREATE TABLE `round_timers` (
  `id` int(11) NOT NULL,
  `round_id` int(11) NOT NULL,
  `hackathon_id` int(11) NOT NULL,
  `timer` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `round_winners`
--

CREATE TABLE `round_winners` (
  `round_id` int(11) NOT NULL,
  `hackathon_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `team_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teams`
--

CREATE TABLE `teams` (
  `team_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `hackathon_id` int(11) NOT NULL,
  `team_leader_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team_members`
--

CREATE TABLE `team_members` (
  `team_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team_requests`
--

CREATE TABLE `team_requests` (
  `request_id` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `status` enum('pending','accepted','declined') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `role` enum('student') DEFAULT 'student',
  `college` varchar(200) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `role`, `college`, `password`) VALUES
(8, 'kushal', 'kushalch32@gmail.com', 'student', 'necn', '$2y$10$lHdnJxX2UGHeBHYRR0.jP.udFtQi2fIrvPj7gmfnOP1AnneXIKK.u');

-- --------------------------------------------------------

--
-- Table structure for table `winners`
--

CREATE TABLE `winners` (
  `user_id` int(11) NOT NULL,
  `hackathon_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `document_submission_scores`
--
ALTER TABLE `document_submission_scores`
  ADD PRIMARY KEY (`user_id`,`hackathon_id`,`round_id`),
  ADD KEY `hackathon_id` (`hackathon_id`),
  ADD KEY `round_id` (`round_id`),
  ADD KEY `evaluated_by` (`evaluated_by`);

--
-- Indexes for table `document_type`
--
ALTER TABLE `document_type`
  ADD PRIMARY KEY (`id`),
  ADD KEY `round_id` (`round_id`),
  ADD KEY `hackathon_id` (`hackathon_id`);

--
-- Indexes for table `document_type_submissions`
--
ALTER TABLE `document_type_submissions`
  ADD PRIMARY KEY (`user_id`,`hackathon_id`,`round_id`),
  ADD KEY `hackathon_id` (`hackathon_id`),
  ADD KEY `round_id` (`round_id`);

--
-- Indexes for table `hackathons`
--
ALTER TABLE `hackathons`
  ADD PRIMARY KEY (`hackathon_id`),
  ADD KEY `org_id` (`org_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `team_id` (`team_id`),
  ADD KEY `sender_id` (`sender_id`);

--
-- Indexes for table `organizers`
--
ALTER TABLE `organizers`
  ADD PRIMARY KEY (`org_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`user_id`,`hackathon_id`),
  ADD KEY `hackathon_id` (`hackathon_id`);

--
-- Indexes for table `quiz_type`
--
ALTER TABLE `quiz_type`
  ADD PRIMARY KEY (`id`),
  ADD KEY `round_id` (`round_id`),
  ADD KEY `hackathon_id` (`hackathon_id`);

--
-- Indexes for table `quiz_type_score`
--
ALTER TABLE `quiz_type_score`
  ADD PRIMARY KEY (`user_id`,`hackathon_id`,`round_id`),
  ADD KEY `hackathon_id` (`hackathon_id`),
  ADD KEY `round_id` (`round_id`);

--
-- Indexes for table `rounds`
--
ALTER TABLE `rounds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hackathon_id` (`hackathon_id`);

--
-- Indexes for table `round_timers`
--
ALTER TABLE `round_timers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `round_id` (`round_id`),
  ADD KEY `hackathon_id` (`hackathon_id`);

--
-- Indexes for table `round_winners`
--
ALTER TABLE `round_winners`
  ADD PRIMARY KEY (`round_id`,`hackathon_id`,`user_id`),
  ADD KEY `hackathon_id` (`hackathon_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `team_id` (`team_id`);

--
-- Indexes for table `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`team_id`),
  ADD KEY `hackathon_id` (`hackathon_id`),
  ADD KEY `team_leader_id` (`team_leader_id`);

--
-- Indexes for table `team_members`
--
ALTER TABLE `team_members`
  ADD PRIMARY KEY (`team_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `team_requests`
--
ALTER TABLE `team_requests`
  ADD PRIMARY KEY (`request_id`),
  ADD KEY `team_id` (`team_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `winners`
--
ALTER TABLE `winners`
  ADD PRIMARY KEY (`user_id`,`hackathon_id`),
  ADD KEY `hackathon_id` (`hackathon_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `document_type`
--
ALTER TABLE `document_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `hackathons`
--
ALTER TABLE `hackathons`
  MODIFY `hackathon_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `message_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `organizers`
--
ALTER TABLE `organizers`
  MODIFY `org_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `quiz_type`
--
ALTER TABLE `quiz_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `rounds`
--
ALTER TABLE `rounds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `round_timers`
--
ALTER TABLE `round_timers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `teams`
--
ALTER TABLE `teams`
  MODIFY `team_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `team_requests`
--
ALTER TABLE `team_requests`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `document_submission_scores`
--
ALTER TABLE `document_submission_scores`
  ADD CONSTRAINT `document_submission_scores_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_submission_scores_ibfk_2` FOREIGN KEY (`hackathon_id`) REFERENCES `hackathons` (`hackathon_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_submission_scores_ibfk_3` FOREIGN KEY (`round_id`) REFERENCES `rounds` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_submission_scores_ibfk_4` FOREIGN KEY (`evaluated_by`) REFERENCES `organizers` (`org_id`) ON DELETE CASCADE;

--
-- Constraints for table `document_type`
--
ALTER TABLE `document_type`
  ADD CONSTRAINT `document_type_ibfk_1` FOREIGN KEY (`round_id`) REFERENCES `rounds` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_type_ibfk_2` FOREIGN KEY (`hackathon_id`) REFERENCES `hackathons` (`hackathon_id`) ON DELETE CASCADE;

--
-- Constraints for table `document_type_submissions`
--
ALTER TABLE `document_type_submissions`
  ADD CONSTRAINT `document_type_submissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_type_submissions_ibfk_2` FOREIGN KEY (`hackathon_id`) REFERENCES `hackathons` (`hackathon_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `document_type_submissions_ibfk_3` FOREIGN KEY (`round_id`) REFERENCES `rounds` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hackathons`
--
ALTER TABLE `hackathons`
  ADD CONSTRAINT `hackathons_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `organizers` (`org_id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `participants_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `participants_ibfk_2` FOREIGN KEY (`hackathon_id`) REFERENCES `hackathons` (`hackathon_id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_type`
--
ALTER TABLE `quiz_type`
  ADD CONSTRAINT `quiz_type_ibfk_1` FOREIGN KEY (`round_id`) REFERENCES `rounds` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_type_ibfk_2` FOREIGN KEY (`hackathon_id`) REFERENCES `hackathons` (`hackathon_id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_type_score`
--
ALTER TABLE `quiz_type_score`
  ADD CONSTRAINT `quiz_type_score_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_type_score_ibfk_2` FOREIGN KEY (`hackathon_id`) REFERENCES `hackathons` (`hackathon_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `quiz_type_score_ibfk_3` FOREIGN KEY (`round_id`) REFERENCES `rounds` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rounds`
--
ALTER TABLE `rounds`
  ADD CONSTRAINT `rounds_ibfk_1` FOREIGN KEY (`hackathon_id`) REFERENCES `hackathons` (`hackathon_id`) ON DELETE CASCADE;

--
-- Constraints for table `round_timers`
--
ALTER TABLE `round_timers`
  ADD CONSTRAINT `round_timers_ibfk_1` FOREIGN KEY (`round_id`) REFERENCES `rounds` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `round_timers_ibfk_2` FOREIGN KEY (`hackathon_id`) REFERENCES `hackathons` (`hackathon_id`) ON DELETE CASCADE;

--
-- Constraints for table `round_winners`
--
ALTER TABLE `round_winners`
  ADD CONSTRAINT `round_winners_ibfk_1` FOREIGN KEY (`round_id`) REFERENCES `rounds` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `round_winners_ibfk_2` FOREIGN KEY (`hackathon_id`) REFERENCES `hackathons` (`hackathon_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `round_winners_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `round_winners_ibfk_4` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`) ON DELETE SET NULL;

--
-- Constraints for table `teams`
--
ALTER TABLE `teams`
  ADD CONSTRAINT `teams_ibfk_1` FOREIGN KEY (`hackathon_id`) REFERENCES `hackathons` (`hackathon_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teams_ibfk_2` FOREIGN KEY (`team_leader_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `team_members`
--
ALTER TABLE `team_members`
  ADD CONSTRAINT `team_members_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `team_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `team_requests`
--
ALTER TABLE `team_requests`
  ADD CONSTRAINT `team_requests_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams` (`team_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `team_requests_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `team_requests_ibfk_3` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `winners`
--
ALTER TABLE `winners`
  ADD CONSTRAINT `winners_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `winners_ibfk_2` FOREIGN KEY (`hackathon_id`) REFERENCES `hackathons` (`hackathon_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
