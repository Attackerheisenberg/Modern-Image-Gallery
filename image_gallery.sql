-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 18, 2025 at 07:42 AM
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
-- Database: `image_gallery`
--

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `views` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `images`
--

INSERT INTO `images` (`id`, `filename`, `title`, `description`, `category`, `uploaded_at`, `views`) VALUES
(1, 'nature_1.jpg', 'Mountain Landscape', 'Beautiful mountain view at sunrise', 'Nature', '2025-12-18 06:28:48', 42),
(2, 'nature_2.webp', 'Forest Path', 'Peaceful forest walking path', 'Nature', '2025-12-18 06:28:48', 28),
(3, 'travel_1.jpg', 'Paris Eiffel Tower', 'Iconic Eiffel Tower in Paris', 'Travel', '2025-12-18 06:28:48', 56),
(4, 'travel_2.jpg', 'Tokyo Street', 'Busy street in Tokyo at night', 'Travel', '2025-12-18 06:28:48', 31),
(5, 'art_1.jpg', 'Abstract Painting', 'Colorful abstract art painting', 'Art', '2025-12-18 06:28:48', 19),
(6, 'food_1.jpg', 'Delicious Pizza', 'Homemade pepperoni pizza', 'Food', '2025-12-18 06:28:48', 67),
(7, 'food_2.jpeg', 'Fresh Salad', 'Healthy vegetable salad bowl', 'Food', '2025-12-18 06:28:48', 24),
(8, 'people_1.jpg', 'Portrait Photography', 'Professional portrait photo', 'People', '2025-12-18 06:28:48', 15),
(9, 'city_1.webp', 'New York Skyline', 'New York city skyline at dusk', 'City', '2025-12-18 06:28:48', 89),
(10, 'animals_1.jpg', 'Cute Puppy', 'Golden retriever puppy playing', 'Animals', '2025-12-18 06:28:48', 112);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
