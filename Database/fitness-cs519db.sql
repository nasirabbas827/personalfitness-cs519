-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 06, 2025 at 09:39 AM
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
-- Database: `fitness-cs519db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `daily_meals`
--

CREATE TABLE `daily_meals` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `log_date` date NOT NULL,
  `meal_time` enum('breakfast','lunch','dinner','snack','other') NOT NULL,
  `food_description` text NOT NULL,
  `calories` int(11) DEFAULT 0,
  `protein_g` decimal(6,2) DEFAULT 0.00,
  `carbs_g` decimal(6,2) DEFAULT 0.00,
  `fats_g` decimal(6,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_meals`
--

INSERT INTO `daily_meals` (`id`, `user_id`, `log_date`, `meal_time`, `food_description`, `calories`, `protein_g`, `carbs_g`, `fats_g`, `created_at`) VALUES
(2, 3, '2025-11-27', 'breakfast', 'Oatmeal with banana and almonds', 350, 10.00, 55.00, 8.00, '2025-12-06 07:48:29'),
(3, 3, '2025-11-28', 'lunch', 'Grilled chicken salad with olive oil', 450, 35.00, 20.00, 18.00, '2025-12-06 07:48:29'),
(4, 3, '2025-11-29', 'dinner', 'Baked salmon with quinoa and vegetables', 500, 40.00, 45.00, 20.00, '2025-12-06 07:48:29'),
(5, 3, '2025-11-30', 'snack', 'Greek yogurt with honey', 200, 12.00, 25.00, 4.00, '2025-12-06 07:48:29'),
(6, 3, '2025-12-01', 'breakfast', 'Scrambled eggs with whole wheat toast', 300, 18.00, 20.00, 12.00, '2025-12-06 07:48:29'),
(7, 3, '2025-12-02', 'lunch', 'Turkey sandwich with avocado', 400, 25.00, 35.00, 15.00, '2025-12-06 07:48:29'),
(8, 3, '2025-12-03', 'dinner', 'Vegetable stir-fry with tofu', 450, 20.00, 50.00, 14.00, '2025-12-06 07:48:29'),
(9, 3, '2025-12-04', 'snack', 'Protein shake with almond milk', 250, 25.00, 15.00, 8.00, '2025-12-06 07:48:29'),
(10, 3, '2025-12-05', 'breakfast', 'Smoothie with spinach, banana, and protein powder', 320, 20.00, 40.00, 5.00, '2025-12-06 07:48:29'),
(11, 3, '2025-12-06', 'lunch', 'Grilled fish tacos with salsa', 480, 30.00, 50.00, 18.00, '2025-12-06 07:48:29');

-- --------------------------------------------------------

--
-- Table structure for table `daily_water_intake`
--

CREATE TABLE `daily_water_intake` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `log_date` date NOT NULL,
  `water_ml` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_water_intake`
--

INSERT INTO `daily_water_intake` (`id`, `user_id`, `log_date`, `water_ml`, `created_at`) VALUES
(2, 3, '2025-12-01', 2000, '2025-12-06 07:36:30'),
(3, 3, '2025-12-02', 1800, '2025-12-06 07:36:30'),
(4, 3, '2025-12-03', 2200, '2025-12-06 07:36:30'),
(5, 3, '2025-12-04', 2500, '2025-12-06 07:36:30'),
(6, 3, '2025-12-05', 2100, '2025-12-06 07:36:30'),
(7, 3, '2025-12-06', 2000, '2025-12-06 07:36:30'),
(8, 3, '2025-12-07', 2300, '2025-12-06 07:36:30'),
(9, 3, '2025-12-08', 1900, '2025-12-06 07:36:30'),
(10, 3, '2025-12-09', 2400, '2025-12-06 07:36:30'),
(11, 3, '2025-12-10', 2000, '2025-12-06 07:36:30');

-- --------------------------------------------------------

--
-- Table structure for table `daily_workouts`
--

CREATE TABLE `daily_workouts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `log_date` date NOT NULL,
  `workout_type` varchar(150) DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT 0,
  `calories_burned` int(11) DEFAULT 0,
  `steps` int(11) DEFAULT 0,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `daily_workouts`
--

INSERT INTO `daily_workouts` (`id`, `user_id`, `log_date`, `workout_type`, `duration_minutes`, `calories_burned`, `steps`, `notes`, `created_at`) VALUES
(2, 3, '2025-12-01', 'Running', 30, 250, 4000, 'Morning jog in the park', '2025-12-06 07:29:59'),
(3, 3, '2025-12-02', 'Gym', 45, 400, 2000, 'Weight training session', '2025-12-06 07:29:59'),
(4, 3, '2025-12-03', 'Yoga', 60, 200, 1500, 'Evening yoga for flexibility', '2025-12-06 07:29:59'),
(5, 3, '2025-12-04', 'HIIT', 25, 300, 3000, 'High intensity interval training', '2025-12-06 07:29:59'),
(6, 3, '2025-12-05', 'Cycling', 50, 450, 5000, 'Road cycling session', '2025-12-06 07:29:59'),
(7, 3, '2025-12-06', 'Swimming', 40, 350, 1000, 'Lap swimming', '2025-12-06 07:29:59'),
(8, 3, '2025-12-07', 'Running', 35, 280, 4200, 'Morning run in neighborhood', '2025-12-06 07:29:59'),
(9, 3, '2025-12-08', 'Gym', 55, 450, 2200, 'Upper body workout', '2025-12-06 07:29:59'),
(10, 3, '2025-12-09', 'Yoga', 50, 180, 1300, 'Relaxing yoga session', '2025-12-06 07:29:59'),
(11, 3, '2025-12-10', 'HIIT', 30, 320, 3100, 'Evening HIIT circuit', '2025-12-06 07:29:59');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `routine_id` int(11) DEFAULT NULL,
  `rating` tinyint(4) DEFAULT NULL CHECK (`rating` between 1 and 5),
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `user_id`, `routine_id`, `rating`, `comment`, `created_at`) VALUES
(1, 3, 8, 2, 'drf', '2025-12-06 08:00:53');

-- --------------------------------------------------------

--
-- Table structure for table `fitness_tips`
--

CREATE TABLE `fitness_tips` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fitness_tips`
--

INSERT INTO `fitness_tips` (`id`, `title`, `content`, `image_url`, `created_at`) VALUES
(3, 'Full Body Stretch Routine', 'A simple 10-minute stretching routine designed to improve flexibility and reduce muscle tension.', 'uploads/1765004237_exercise.jpg', '2025-12-06 07:01:19'),
(4, 'Beginner Home Workout', 'A no-equipment workout suitable for beginners, focusing on strength, mobility, and endurance.', 'uploads/1765004237_exercise.jpg', '2025-12-06 07:01:19'),
(5, 'Healthy Breakfast Ideas', 'Start your day with high-protein and high-fiber meals to stay energized and improve metabolism.', 'uploads/1765004237_exercise.jpg', '2025-12-06 07:01:19'),
(6, 'Hydration Tips', 'Drink at least 8–10 glasses of water per day. Staying hydrated improves performance and recovery.', 'uploads/1765004237_exercise.jpg', '2025-12-06 07:01:19'),
(7, 'Fat-Burning Cardio Routine', 'A 20-minute high-intensity cardio session designed to burn calories and improve stamina.', 'uploads/1765004237_exercise.jpg', '2025-12-06 07:01:19'),
(8, 'Strength Training for Beginners', 'Focus on slow and controlled movements with proper form to build strength safely.', 'uploads/1765004237_exercise.jpg', '2025-12-06 07:01:19'),
(9, 'Daily Walking Benefits', 'Walking 30 minutes a day improves heart health, reduces stress, and boosts mood.', 'uploads/1765004237_exercise.jpg', '2025-12-06 07:01:19'),
(10, 'Core Strength Workout', 'A beginner-friendly core routine to improve stability and reduce lower back pain.', 'uploads/1765004237_exercise.jpg', '2025-12-06 07:01:19'),
(11, 'Improve Your Sleep', 'Good sleep enhances muscle recovery, mood, and workout performance. Aim for 7–8 hours.', 'uploads/1765004237_exercise.jpg', '2025-12-06 07:01:19'),
(12, 'Balanced Diet Tips', 'Include proteins, healthy fats, and complex carbs in your meals to support your fitness goals.', 'uploads/1765004237_exercise.jpg', '2025-12-06 07:01:19');

-- --------------------------------------------------------

--
-- Table structure for table `public_routines`
--

CREATE TABLE `public_routines` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `difficulty` enum('beginner','intermediate','advanced') DEFAULT 'beginner',
  `duration_minutes` int(11) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `instructions` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `trainer_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `public_routines`
--

INSERT INTO `public_routines` (`id`, `title`, `description`, `difficulty`, `duration_minutes`, `category`, `instructions`, `image_url`, `created_by`, `trainer_id`, `created_at`) VALUES
(3, 'Full Body Stretch', '10-minute full body stretch to improve flexibility and reduce muscle tension.', 'beginner', 10, 'Flexibility', 'Follow each stretch slowly and hold for 20 seconds.', 'uploads/1765005076_exercise.jpg', 1, 1, '2025-12-06 07:13:24'),
(4, 'Beginner Cardio', 'Low-intensity cardio routine for beginners.', 'beginner', 20, 'Cardio', 'Start with light jogging or brisk walking for 20 minutes.', 'uploads/1765005076_exercise.jpg', 1, 1, '2025-12-06 07:13:24'),
(5, 'Core Strength', 'Strengthen your core muscles with these exercises.', 'intermediate', 15, 'Strength', 'Do planks, sit-ups, and leg raises in 3 sets each.', 'uploads/1765005076_exercise.jpg', 1, 1, '2025-12-06 07:13:24'),
(6, 'Upper Body Workout', 'Workout focusing on chest, shoulders, and arms.', 'intermediate', 25, 'Strength', 'Perform push-ups, dumbbell presses, and shoulder raises.', 'uploads/1765005076_exercise.jpg', 1, 1, '2025-12-06 07:13:24'),
(7, 'HIIT Routine', 'High-intensity interval training for fat burning.', 'advanced', 20, 'Cardio', 'Alternate 30 seconds sprint with 30 seconds rest for 20 minutes.', 'uploads/1765005076_exercise.jpg', 1, 1, '2025-12-06 07:13:24'),
(8, 'Leg Day', 'Strength routine focusing on lower body muscles.', 'intermediate', 30, 'Strength', 'Include squats, lunges, and calf raises in 3 sets each.', 'uploads/1765005076_exercise.jpg', 1, 1, '2025-12-06 07:13:24'),
(9, 'Morning Yoga', 'Relaxing yoga session to start your day.', 'beginner', 15, 'Flexibility', 'Follow the sequence of sun salutations and breathing exercises.', 'uploads/1765005076_exercise.jpg', 1, 1, '2025-12-06 07:13:24'),
(10, 'Full Body HIIT', 'High-intensity total body workout.', 'advanced', 25, 'Cardio', 'Combine jumping jacks, burpees, push-ups, and mountain climbers.', 'uploads/1765005076_exercise.jpg', 1, 1, '2025-12-06 07:13:24'),
(11, 'Posture Correction', 'Exercises to improve posture and reduce back pain.', 'beginner', 15, 'Flexibility', 'Include shoulder stretches, chest openers, and back extensions.', 'uploads/1765005076_exercise.jpg', 1, 1, '2025-12-06 07:13:24'),
(12, 'Endurance Run', 'Increase stamina and cardiovascular endurance.', 'intermediate', 30, 'Cardio', 'Run at a moderate pace continuously for 30 minutes.', 'uploads/1765005076_exercise.jpg', 1, 1, '2025-12-06 07:13:24');

-- --------------------------------------------------------

--
-- Table structure for table `trainer_responses`
--

CREATE TABLE `trainer_responses` (
  `id` int(11) NOT NULL,
  `feedback_id` int(11) NOT NULL,
  `trainer_id` int(11) NOT NULL,
  `response_text` text NOT NULL,
  `suggested_modifications` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainer_responses`
--

INSERT INTO `trainer_responses` (`id`, `feedback_id`, `trainer_id`, `response_text`, `suggested_modifications`, `created_at`) VALUES
(1, 1, 1, 'fd', 'fds', '2025-12-06 08:07:41');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `name` varchar(150) NOT NULL,
  `role` enum('trainer','user') NOT NULL DEFAULT 'user',
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `height_cm` decimal(5,2) DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `name`, `role`, `date_of_birth`, `gender`, `height_cm`, `weight_kg`, `created_at`, `updated_at`) VALUES
(1, 'trainer@gmail.com', '$2y$10$pA/pyjkAM/sPzpc1p50DX.2P1sRaBxuzNun5GnlBU/3cim6wCuDlG', 'NASIR ABBAS', 'trainer', '2025-12-06', 'male', 23.00, 78.00, '2025-12-06 06:48:20', '2025-12-06 06:49:19'),
(3, 'user@gmail.com', '$2y$10$6hi1xIZPAjhgvGfl5fQ1..E7ynrt5AcEf8T6N7Cym2PydwerqAIWi', 'Fitness User', 'user', '2025-12-06', 'male', 33.00, 33.00, '2025-12-06 07:16:58', '2025-12-06 07:16:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `daily_meals`
--
ALTER TABLE `daily_meals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `daily_water_intake`
--
ALTER TABLE `daily_water_intake`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `daily_workouts`
--
ALTER TABLE `daily_workouts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `routine_id` (`routine_id`);

--
-- Indexes for table `fitness_tips`
--
ALTER TABLE `fitness_tips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `public_routines`
--
ALTER TABLE `public_routines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `trainer_id` (`trainer_id`);

--
-- Indexes for table `trainer_responses`
--
ALTER TABLE `trainer_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `feedback_id` (`feedback_id`),
  ADD KEY `trainer_id` (`trainer_id`);

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
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `daily_meals`
--
ALTER TABLE `daily_meals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `daily_water_intake`
--
ALTER TABLE `daily_water_intake`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `daily_workouts`
--
ALTER TABLE `daily_workouts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fitness_tips`
--
ALTER TABLE `fitness_tips`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `public_routines`
--
ALTER TABLE `public_routines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `trainer_responses`
--
ALTER TABLE `trainer_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `daily_meals`
--
ALTER TABLE `daily_meals`
  ADD CONSTRAINT `daily_meals_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `daily_water_intake`
--
ALTER TABLE `daily_water_intake`
  ADD CONSTRAINT `daily_water_intake_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `daily_workouts`
--
ALTER TABLE `daily_workouts`
  ADD CONSTRAINT `daily_workouts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`routine_id`) REFERENCES `public_routines` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `public_routines`
--
ALTER TABLE `public_routines`
  ADD CONSTRAINT `public_routines_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `admins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `public_routines_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `trainer_responses`
--
ALTER TABLE `trainer_responses`
  ADD CONSTRAINT `trainer_responses_ibfk_1` FOREIGN KEY (`feedback_id`) REFERENCES `feedback` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `trainer_responses_ibfk_2` FOREIGN KEY (`trainer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
