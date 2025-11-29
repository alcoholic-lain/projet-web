-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 29, 2025 at 07:12 PM
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
-- Database: `tunispace`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date_creation` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `description`, `date_creation`) VALUES
(1, 'Exploration Spatiale', 'Projets liés à l\'exploration de l\'espace et des planètes', '2025-11-17 00:11:40'),
(2, 'Énergie Orbitale', 'Solutions énergétiques pour l\'espace et les satellites', '2025-11-17 00:11:40'),
(3, 'Habitats Lunaires', 'Conception et développement d\'habitats pour la Lune', '2025-11-17 00:11:40'),
(4, 'Robotique Spatiale', 'Robots et systèmes automatisés pour l\'espace', '2025-11-17 00:11:40'),
(5, 'Propulsion Avancée', 'Nouvelles technologies de propulsion spatiale', '2025-11-17 00:11:40');

-- --------------------------------------------------------

--
-- Table structure for table `commentaires`
--

CREATE TABLE `commentaires` (
  `id` int(11) NOT NULL,
  `innovation_id` int(11) NOT NULL,
  `auteur` varchar(100) NOT NULL,
  `contenu` text NOT NULL,
  `date_creation` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `confirmations_email`
--

CREATE TABLE `confirmations_email` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `date_envoi` datetime DEFAULT current_timestamp(),
  `confirme` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `is_group` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conversations`
--

INSERT INTO `conversations` (`id`, `title`, `is_group`, `created_at`) VALUES
(1, 'Alice & Bob', 0, '2025-11-17 12:21:45'),
(2, 'Alice & Charlie', 0, '2025-11-17 12:21:45'),
(3, 'Alice & David', 0, '2025-11-17 12:21:45'),
(4, 'Alice & Eve', 0, '2025-11-17 12:21:45'),
(5, 'Bob & Charlie', 0, '2025-11-17 12:21:45'),
(6, 'Bob & David', 0, '2025-11-17 12:21:45'),
(7, 'Bob & Eve', 0, '2025-11-17 12:21:45'),
(8, 'Charlie & David', 0, '2025-11-17 12:21:45'),
(9, 'Charlie & Eve', 0, '2025-11-17 12:21:45'),
(10, 'David & Eve', 0, '2025-11-17 12:21:45'),
(11, 'Study Group A', 1, '2025-11-17 12:21:45'),
(12, 'Study Group B', 1, '2025-11-17 12:21:45'),
(13, 'Gaming Squad', 1, '2025-11-17 12:21:45'),
(14, 'Project Team 1', 1, '2025-11-17 12:21:45'),
(15, 'Project Team 2', 1, '2025-11-17 12:21:45'),
(16, 'Family Chat 1', 1, '2025-11-17 12:21:45'),
(17, 'Family Chat 2', 1, '2025-11-17 12:21:45'),
(18, 'Friends Forever', 1, '2025-11-17 12:21:45'),
(19, 'Weekend Plans', 1, '2025-11-17 12:21:45'),
(20, 'Music Lovers', 1, '2025-11-17 12:21:45'),
(21, 'Anime Club', 1, '2025-11-17 12:21:45'),
(22, 'Work Buddies', 1, '2025-11-17 12:21:45'),
(23, 'Esprit G2', 1, '2025-11-17 12:21:45'),
(24, 'Dev Chat', 1, '2025-11-17 12:21:45'),
(25, 'Random', 1, '2025-11-17 12:21:45'),
(26, 'Lab Group', 1, '2025-11-17 12:21:45'),
(27, 'Dorm Chat', 1, '2025-11-17 12:21:45'),
(28, 'Gym Bros', 1, '2025-11-17 12:21:45'),
(29, 'Movie Night', 1, '2025-11-17 12:21:45'),
(30, 'CS Project', 1, '2025-11-17 12:21:45'),
(31, '', 0, '2025-11-28 15:30:34'),
(32, 'the goats', 1, '2025-11-29 17:34:06');

-- --------------------------------------------------------

--
-- Table structure for table `conversation_users`
--

CREATE TABLE `conversation_users` (
  `conversation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `conversation_users`
--

INSERT INTO `conversation_users` (`conversation_id`, `user_id`, `is_admin`, `joined_at`) VALUES
(1, 1001, 1, '2025-11-17 12:21:45'),
(1, 1002, 0, '2025-11-17 12:21:45'),
(2, 1001, 1, '2025-11-17 12:21:45'),
(2, 1003, 0, '2025-11-17 12:21:45'),
(3, 1001, 1, '2025-11-17 12:21:45'),
(3, 1004, 0, '2025-11-17 12:21:45'),
(4, 1001, 1, '2025-11-17 12:21:45'),
(4, 1005, 0, '2025-11-17 12:21:45'),
(5, 1002, 1, '2025-11-17 12:21:45'),
(5, 1003, 0, '2025-11-17 12:21:45'),
(6, 1002, 1, '2025-11-17 12:21:45'),
(6, 1004, 0, '2025-11-17 12:21:45'),
(7, 1002, 1, '2025-11-17 12:21:45'),
(7, 1005, 0, '2025-11-17 12:21:45'),
(8, 1003, 1, '2025-11-17 12:21:45'),
(8, 1004, 0, '2025-11-17 12:21:45'),
(9, 1003, 1, '2025-11-17 12:21:45'),
(9, 1005, 0, '2025-11-17 12:21:45'),
(10, 1004, 1, '2025-11-17 12:21:45'),
(10, 1005, 0, '2025-11-17 12:21:45'),
(11, 1001, 1, '2025-11-17 12:21:45'),
(11, 1002, 0, '2025-11-17 12:21:45'),
(11, 1003, 0, '2025-11-17 12:21:45'),
(12, 1004, 1, '2025-11-17 12:21:45'),
(12, 1005, 0, '2025-11-17 12:21:45'),
(12, 1006, 0, '2025-11-17 12:21:45'),
(13, 1002, 1, '2025-11-17 12:21:45'),
(13, 1003, 0, '2025-11-17 12:21:45'),
(13, 1004, 0, '2025-11-17 12:21:45'),
(13, 1005, 0, '2025-11-17 12:21:45'),
(14, 1001, 1, '2025-11-17 12:21:45'),
(14, 1004, 0, '2025-11-17 12:21:45'),
(14, 1007, 0, '2025-11-17 12:21:45'),
(14, 1008, 0, '2025-11-17 12:21:45'),
(15, 1002, 1, '2025-11-17 12:21:45'),
(15, 1005, 0, '2025-11-17 12:21:45'),
(15, 1008, 0, '2025-11-17 12:21:45'),
(15, 1009, 0, '2025-11-17 12:21:45'),
(16, 1001, 1, '2025-11-17 12:21:45'),
(16, 1006, 0, '2025-11-17 12:21:45'),
(16, 1007, 0, '2025-11-17 12:21:45'),
(17, 1003, 1, '2025-11-17 12:21:45'),
(17, 1008, 0, '2025-11-17 12:21:45'),
(17, 1009, 0, '2025-11-17 12:21:45'),
(18, 1004, 1, '2025-11-17 12:21:45'),
(18, 1005, 0, '2025-11-17 12:21:45'),
(18, 1006, 0, '2025-11-17 12:21:45'),
(18, 1007, 0, '2025-11-17 12:21:45'),
(19, 1002, 1, '2025-11-17 12:21:45'),
(19, 1003, 0, '2025-11-17 12:21:45'),
(19, 1008, 0, '2025-11-17 12:21:45'),
(19, 1010, 0, '2025-11-17 12:21:45'),
(20, 1001, 1, '2025-11-17 12:21:45'),
(20, 1003, 0, '2025-11-17 12:21:45'),
(20, 1005, 0, '2025-11-17 12:21:45'),
(20, 1007, 0, '2025-11-17 12:21:45'),
(20, 1009, 0, '2025-11-17 12:21:45'),
(21, 1001, 1, '2025-11-17 12:21:45'),
(21, 1008, 0, '2025-11-17 12:21:45'),
(21, 1009, 0, '2025-11-17 12:21:45'),
(21, 1010, 0, '2025-11-17 12:21:45'),
(22, 1002, 1, '2025-11-17 12:21:45'),
(22, 1004, 0, '2025-11-17 12:21:45'),
(22, 1006, 0, '2025-11-17 12:21:45'),
(22, 1008, 0, '2025-11-17 12:21:45'),
(23, 1003, 1, '2025-11-17 12:21:45'),
(23, 1005, 0, '2025-11-17 12:21:45'),
(23, 1007, 0, '2025-11-17 12:21:45'),
(23, 1009, 0, '2025-11-17 12:21:45'),
(24, 1001, 1, '2025-11-17 12:21:45'),
(24, 1002, 0, '2025-11-17 12:21:45'),
(24, 1003, 0, '2025-11-17 12:21:45'),
(24, 1004, 0, '2025-11-17 12:21:45'),
(24, 1005, 0, '2025-11-17 12:21:45'),
(25, 1006, 1, '2025-11-17 12:21:45'),
(25, 1007, 0, '2025-11-17 12:21:45'),
(25, 1008, 0, '2025-11-17 12:21:45'),
(25, 1009, 0, '2025-11-17 12:21:45'),
(25, 1010, 0, '2025-11-17 12:21:45'),
(26, 1001, 1, '2025-11-17 12:21:45'),
(26, 1006, 0, '2025-11-17 12:21:45'),
(26, 1009, 0, '2025-11-17 12:21:45'),
(27, 1002, 1, '2025-11-17 12:21:45'),
(27, 1007, 0, '2025-11-17 12:21:45'),
(27, 1010, 0, '2025-11-17 12:21:45'),
(28, 1004, 1, '2025-11-17 12:21:45'),
(28, 1006, 0, '2025-11-17 12:21:45'),
(28, 1010, 0, '2025-11-17 12:21:45'),
(29, 1003, 1, '2025-11-17 12:21:45'),
(29, 1005, 0, '2025-11-17 12:21:45'),
(29, 1008, 0, '2025-11-17 12:21:45'),
(30, 1001, 1, '2025-11-17 12:21:45'),
(30, 1003, 0, '2025-11-17 12:21:45'),
(30, 1006, 0, '2025-11-17 12:21:45'),
(30, 1010, 0, '2025-11-17 12:21:45'),
(31, 1001, 0, '2025-11-28 15:30:34'),
(31, 1011, 1, '2025-11-28 15:30:34'),
(32, 80, 0, '2025-11-29 17:34:06'),
(32, 1001, 0, '2025-11-29 17:34:06'),
(32, 1002, 1, '2025-11-29 17:34:06');

-- --------------------------------------------------------

--
-- Table structure for table `innovations`
--

CREATE TABLE `innovations` (
  `id` int(11) NOT NULL,
  `titre` varchar(200) NOT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `date_creation` datetime NOT NULL,
  `statut` varchar(50) DEFAULT 'En attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logs_connexions`
--

CREATE TABLE `logs_connexions` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `adresse_ip` varchar(50) DEFAULT NULL,
  `date_connexion` datetime DEFAULT current_timestamp(),
  `succes` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `user_id`, `content`, `created_at`) VALUES
(1, 1, 1001, 'Hi Bob, this is Alice in conversation 1', '2025-11-17 12:21:45'),
(2, 1, 1002, 'Hey Alice! Nice to chat.', '2025-11-17 12:21:45'),
(3, 2, 1001, 'Hi Charlie, Alice here in convo 2', '2025-11-17 12:21:45'),
(4, 2, 1003, 'Hi Alice!', '2025-11-17 12:21:45'),
(6, 3, 1004, 'Sure, tell me.', '2025-11-17 12:21:45'),
(7, 4, 1001, 'Hi Eve, how are you?', '2025-11-17 12:21:45'),
(8, 4, 1005, 'I am good, thanks Alice.', '2025-11-17 12:21:45'),
(9, 5, 1002, 'Yo Charlie, it\'s Bob', '2025-11-17 12:21:45'),
(10, 5, 1003, 'Hey Bob!', '2025-11-17 12:21:45'),
(11, 6, 1002, 'David, are you coming later?', '2025-11-17 12:21:45'),
(12, 6, 1004, 'Yes, I will.', '2025-11-17 12:21:45'),
(13, 7, 1002, 'Eve, did you see this?', '2025-11-17 12:21:45'),
(14, 7, 1005, 'Not yet, send it.', '2025-11-17 12:21:45'),
(15, 8, 1003, 'David, ready for the exam?', '2025-11-17 12:21:45'),
(16, 8, 1004, 'Almost, still revising.', '2025-11-17 12:21:45'),
(17, 9, 1003, 'Eve, group homework?', '2025-11-17 12:21:45'),
(18, 9, 1005, 'Working on it.', '2025-11-17 12:21:45'),
(19, 10, 1004, 'Eve, let\'s meet after class.', '2025-11-17 12:21:45'),
(20, 10, 1005, 'Ok David.', '2025-11-17 12:21:45'),
(21, 11, 1001, 'Welcome to Study Group A!', '2025-11-17 12:21:45'),
(22, 11, 1002, 'Thanks for the invite.', '2025-11-17 12:21:45'),
(23, 11, 1003, 'Let\'s ace this.', '2025-11-17 12:21:45'),
(24, 12, 1004, 'Study Group B starting.', '2025-11-17 12:21:45'),
(25, 12, 1005, 'Hi everyone.', '2025-11-17 12:21:45'),
(26, 12, 1006, 'Hello!', '2025-11-17 12:21:45'),
(27, 13, 1002, 'Gaming squad, tonight?', '2025-11-17 12:21:45'),
(28, 13, 1003, 'I\'m in.', '2025-11-17 12:21:45'),
(29, 13, 1004, 'Me too.', '2025-11-17 12:21:45'),
(30, 14, 1001, 'Project Team 1 kickoff.', '2025-11-17 12:21:45'),
(31, 14, 1004, 'Let\'s go.', '2025-11-17 12:21:45'),
(32, 14, 1007, 'I\'m here.', '2025-11-17 12:21:45'),
(33, 14, 1008, 'Hi all.', '2025-11-17 12:21:45'),
(34, 15, 1002, 'Project Team 2 meeting.', '2025-11-17 12:21:45'),
(35, 15, 1005, 'Ok.', '2025-11-17 12:21:45'),
(36, 15, 1008, 'Got it.', '2025-11-17 12:21:45'),
(37, 15, 1009, 'Cool.', '2025-11-17 12:21:45'),
(38, 16, 1001, 'Family Chat 1, hello!', '2025-11-17 12:21:45'),
(39, 16, 1006, 'Hi there.', '2025-11-17 12:21:45'),
(40, 16, 1007, 'Hey!', '2025-11-17 12:21:45'),
(41, 17, 1003, 'Family Chat 2 checking in.', '2025-11-17 12:21:45'),
(42, 17, 1008, 'Hi!', '2025-11-17 12:21:45'),
(43, 17, 1009, 'Hello.', '2025-11-17 12:21:45'),
(44, 18, 1004, 'Friends Forever ❤️', '2025-11-17 12:21:45'),
(45, 18, 1005, 'Always.', '2025-11-17 12:21:45'),
(46, 18, 1006, 'For sure.', '2025-11-17 12:21:45'),
(47, 18, 1007, 'Yesss.', '2025-11-17 12:21:45'),
(48, 19, 1002, 'Weekend plans?', '2025-11-17 12:21:45'),
(49, 19, 1003, 'Let\'s go out.', '2025-11-17 12:21:45'),
(50, 19, 1008, 'I\'m in.', '2025-11-17 12:21:45'),
(51, 19, 1010, 'Same.', '2025-11-17 12:21:45'),
(52, 20, 1001, 'Music Lovers, new playlist.', '2025-11-17 12:21:45'),
(53, 20, 1003, 'Drop the link.', '2025-11-17 12:21:45'),
(54, 20, 1005, 'Nice tracks.', '2025-11-17 12:21:45'),
(55, 20, 1007, 'Love it.', '2025-11-17 12:21:45'),
(56, 20, 1009, 'Fire.', '2025-11-17 12:21:45'),
(57, 21, 1001, 'Anime Club tonight.', '2025-11-17 12:21:45'),
(58, 21, 1008, 'Can\'t wait.', '2025-11-17 12:21:45'),
(59, 21, 1009, 'Same here.', '2025-11-17 12:21:45'),
(60, 21, 1010, 'Let\'s go.', '2025-11-17 12:21:45'),
(61, 22, 1002, 'Work buddies, standup in 10.', '2025-11-17 12:21:45'),
(62, 22, 1004, 'Ok.', '2025-11-17 12:21:45'),
(63, 22, 1006, 'On my way.', '2025-11-17 12:21:45'),
(64, 22, 1008, 'Got it.', '2025-11-17 12:21:45'),
(65, 23, 1003, 'Esprit G2 lecture starts now.', '2025-11-17 12:21:45'),
(66, 23, 1005, 'Thanks.', '2025-11-17 12:21:45'),
(67, 23, 1007, 'Here.', '2025-11-17 12:21:45'),
(68, 23, 1009, 'Joining.', '2025-11-17 12:21:45'),
(69, 24, 1001, 'Dev Chat: merge conflict again. lalal', '2025-11-17 12:21:45'),
(70, 24, 1002, 'haha.', '2025-11-17 12:21:45'),
(71, 24, 1003, 'classic.', '2025-11-17 12:21:45'),
(72, 24, 1004, 'lol.', '2025-11-17 12:21:45'),
(73, 24, 1005, 'We fix it.', '2025-11-17 12:21:45'),
(74, 25, 1006, 'Random chat time.', '2025-11-17 12:21:45'),
(75, 25, 1007, 'What\'s up.', '2025-11-17 12:21:45'),
(76, 25, 1008, 'All good.', '2025-11-17 12:21:45'),
(77, 25, 1009, 'Same.', '2025-11-17 12:21:45'),
(78, 25, 1010, 'Nice.', '2025-11-17 12:21:45'),
(79, 26, 1001, 'Lab Group meeting at 2 PM.', '2025-11-17 12:21:45'),
(80, 26, 1006, 'Ok.', '2025-11-17 12:21:45'),
(81, 26, 1009, 'Fine by me.', '2025-11-17 12:21:45'),
(82, 27, 1002, 'Dorm Chat noise complaints.', '2025-11-17 12:21:45'),
(83, 27, 1007, 'Oops.', '2025-11-17 12:21:45'),
(84, 27, 1010, 'Sorry.', '2025-11-17 12:21:45'),
(85, 28, 1004, 'Gym Bros, leg day.', '2025-11-17 12:21:45'),
(86, 28, 1006, 'Nooo.', '2025-11-17 12:21:45'),
(87, 28, 1010, 'Let\'s do it.', '2025-11-17 12:21:45'),
(88, 29, 1003, 'Movie Night picks?', '2025-11-17 12:21:45'),
(89, 29, 1005, 'Action.', '2025-11-17 12:21:45'),
(90, 29, 1008, 'Comedy.', '2025-11-17 12:21:45'),
(91, 30, 1001, 'CS Project deadline soon.', '2025-11-17 12:21:45'),
(92, 30, 1003, 'We\'re close.', '2025-11-17 12:21:45'),
(93, 30, 1006, 'Need more coffee.', '2025-11-17 12:21:45'),
(94, 30, 1010, 'Same 😂', '2025-11-17 12:21:45'),
(95, 20, 1001, 'dsdsdsdsd', '2025-11-28 15:23:50'),
(96, 31, 1011, 'hey alice am charlie krick', '2025-11-28 15:30:52'),
(97, 30, 1001, 'hello', '2025-11-29 16:59:15'),
(98, 26, 1001, 'hey', '2025-11-29 17:26:01'),
(99, 26, 1001, 'k', '2025-11-29 17:26:04'),
(100, 26, 1001, 'k', '2025-11-29 17:26:07'),
(101, 26, 1001, 'k', '2025-11-29 17:26:08'),
(102, 26, 1001, 'k', '2025-11-29 17:26:10'),
(103, 26, 1001, 'k', '2025-11-29 17:26:12'),
(104, 24, 1002, 'yayy', '2025-11-29 17:27:48'),
(105, 32, 1002, 'hey', '2025-11-29 17:34:15'),
(106, 32, 1001, 'hey there', '2025-11-29 17:35:22'),
(107, 32, 1001, 'hello', '2025-11-29 17:36:02');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('nournour2862005@gmail.com', 'a8a5f3bfc63af5870a5af940f92c3d16a4c7f5494eb97ddcebdad6f344b75309', '2025-11-17 00:19:32'),
('nournour2862005@gmail.com', 'f77ac0ed42f606e0ffcc345b46339a6d3290c1bf209535b75caebbe5f9f766cd', '2025-11-17 12:59:30'),
('nournour2862005@gmail.com', '187d2a8d075907032d361425a17f16b85c8c91b40ce899c6c9a2e13cbada4177', '2025-11-17 14:45:29'),
('nournour2862005@gmail.com', '4cd9a3c93ab5003bcfa25d1bb55ebc94c1f6c8fdbc7408367ac3a0bb745ba0c1', '2025-11-24 15:03:59'),
('nournour2862005@gmail.com', '9e371418bd8a8a267feb50f8236e99ba4387d77011a0877bc55441385d025e5f', '2025-11-24 15:16:52'),
('nournour2862005@gmail.com', '35fdc8f1393fd8d0d1de4365aa0f37c6dc71121a6ac34fde2a602052c62b069b', '2025-11-24 15:19:56'),
('nournour2862005@gmail.com', 'e3d4019768374611e86a0023594a2e71be30794314861e0581cec4f7cc9168fd', '2025-11-27 09:23:52'),
('nournour2862005@gmail.com', '8ef5b697e7ea956e662e7fd1eb5e9aefeb242957d2305ebc259e587a267e5f68', '2025-11-27 09:24:26'),
('nournour2862005@gmail.com', 'faa29dd6e56a2c5f8bc3ed52441fc9df4f4bbe6bd740704f82d38a52488074e4', '2025-11-27 09:24:36'),
('nournour2862005@gmail.com', 'e48de794faef9342366cbf7f51faeb3d932368252d11279237a759a4c47f3c71', '2025-11-27 09:25:02'),
('nournour2862005@gmail.com', '62f176c1358869a427ace5268d47c660fea4683c12126e27d3c88dcd965d9513', '2025-11-27 12:28:38'),
('nournour2862005@gmail.com', 'db1c5101304ff2a00778f9803493ebc06466a943ccbc0442c080ede80c109bb1', '2025-11-27 12:28:52'),
('nournour2862005@gmail.com', '8d829750c0a131176e345c5aeea4afa81a7dd275002967eebd7fe4c6874fad29', '2025-11-27 12:29:04'),
('nournour2862005@gmail.com', 'c5d732d97a271f4c7f8eb769f1b88ce4ba59a8f425997bfce1d5425faf7d2520', '2025-11-27 14:29:01'),
('nournour2862005@gmail.com', 'd2b7b66e6068b31a819420ced1856d52b79f66aa2498ef9877f8e0cc88abf7c0', '2025-11-27 14:33:24');

-- --------------------------------------------------------

--
-- Table structure for table `pieces_jointes`
--

CREATE TABLE `pieces_jointes` (
  `id` int(11) NOT NULL,
  `innovation_id` int(11) NOT NULL,
  `nom_fichier` varchar(255) NOT NULL,
  `chemin` varchar(500) NOT NULL,
  `type_fichier` varchar(100) DEFAULT NULL,
  `date_upload` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `nom`, `description`) VALUES
(1, 'Admin', 'Peut gérer tous les utilisateurs et accéder au tableau de bord.'),
(2, 'Utilisateur', 'Peut se connecter, modifier son profil et changer son mot de passe.'),
(4, 'Chef', 'chef departement'),
(15, 'visiteur', ''),
(16, 'Monster', 'monster'),
(17, 'alien', ''),
(18, 'kiki', ''),
(19, 'ko', ''),
(20, 'am', '');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `statut` enum('actif','inactif','banni') DEFAULT 'actif',
  `role_id` int(11) DEFAULT 2,
  `planet` enum('terra','mars','venus','jupiter') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `chat_role` enum('front','back') NOT NULL DEFAULT 'front',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `google_id`, `password`, `statut`, `role_id`, `planet`, `is_active`, `chat_role`, `created_at`, `avatar`) VALUES
(66, 'zak12', 'zakaria.benouirsane@esprit.tn', NULL, '$2y$10$DbExiga4ecL1SfXPfPUx..FOcq/LbIV473rNff2RKVcBCgLbuaVA.', 'actif', 1, 'mars', 1, 'back', '2025-11-28 14:49:26', NULL),
(68, 'Zak', 'zakariabenouirane@esprit.tn', NULL, '$2y$10$jN95QpSpsLUturuobt.qqef4prcpYH6jI8uONPfCh/BTHTPetVx02', 'actif', 1, 'jupiter', 1, 'back', '2025-11-28 14:49:26', NULL),
(69, 'zaktheastroAaa', 'zak.thea@gmail.com', NULL, '$2y$10$eZEcuHxVhmDdrExhohNgC.H2pPtQZQ6IBnAcHKCQ3FDGXGm.rNMLC', 'actif', 4, 'mars', 1, 'front', '2025-11-28 14:49:26', NULL),
(70, 'Nour___', 'nournour2862005@gmail.com', NULL, '$2y$10$KZWsAQQksJrgUX.869jEuuQWgQAbcNWlV2WEmnRbHIgHF.RpqD1vC', 'actif', 4, 'mars', 1, 'front', '2025-11-28 14:49:26', 'uploads/avatars/69246a24e79b2.png'),
(75, 'hichem', 'hichem@gmail.com', NULL, '$2y$10$tGIAaSIjwN2pIBk/TuMcGenh3JyGUSiUIbJ3C.sn/K.HjUeMHRAD2', 'actif', 1, 'mars', 1, 'back', '2025-11-28 14:49:26', '../../../view/Client/login/uploads/avatars/69243f4a38730controllerpng'),
(78, 'Zak_2', 'zakariabenwirane@gmail.com', '103984994944042248288', NULL, 'actif', 2, 'terra', 1, 'front', '2025-11-28 14:49:26', 'uploads/avatars/692835bebb25c.png'),
(79, 'Zak123', 'zakaria.benouirane@esprit.tn', NULL, '$2y$10$LZXB.3hKhgwGHT8dqPpVCuwpsaUsWwKZ/P3qKujHEUBNjClIFfLyK', 'actif', 2, 'mars', 1, 'front', '2025-11-28 14:49:26', 'uploads/avatars/6927faa03382f.png'),
(80, 'lain', 'iwakura.lain.pa@gmail.com', NULL, '$2y$10$Exrhl9FUDsaKIL5zJFkfCe6xFsLAVI0WgLmO1jhUT2nqRWYjV/bZK', 'actif', 1, 'terra', 1, 'back', '2025-11-28 14:49:26', NULL),
(1001, 'alice', 'alice@example.com', NULL, 'pass123', 'actif', 1, 'terra', 1, 'back', '2025-11-17 12:21:45', NULL),
(1002, 'bob', 'bob@example.com', NULL, 'pass123', 'actif', 2, 'terra', 1, 'front', '2025-11-17 12:21:45', NULL),
(1003, 'charlie', 'charlie@example.com', NULL, 'pass123', 'actif', 2, 'terra', 1, 'front', '2025-11-17 12:21:45', NULL),
(1004, 'david', 'david@example.com', NULL, 'pass123', 'actif', 2, 'terra', 1, 'front', '2025-11-17 12:21:45', NULL),
(1005, 'eve', 'eve@example.com', NULL, 'pass123', 'actif', 2, 'terra', 1, 'front', '2025-11-17 12:21:45', NULL),
(1006, 'frank', 'frank@example.com', NULL, 'pass123', 'actif', 2, 'terra', 1, 'front', '2025-11-17 12:21:45', NULL),
(1007, 'grace', 'grace@example.com', NULL, 'pass123', 'actif', 2, 'terra', 1, 'front', '2025-11-17 12:21:45', NULL),
(1008, 'heidi', 'heidi@example.com', NULL, 'pass123', 'actif', 2, 'terra', 1, 'front', '2025-11-17 12:21:45', NULL),
(1009, 'ivan', 'ivan@example.com', NULL, 'pass123', 'actif', 2, 'terra', 1, 'front', '2025-11-17 12:21:45', NULL),
(1010, 'judy', 'judy@example.com', NULL, 'pass123', 'actif', 2, 'terra', 1, 'front', '2025-11-17 12:21:45', NULL),
(1011, 'krick', 'krick@example.com', NULL, '123', 'actif', 1, 'terra', 1, 'back', '2025-11-28 15:29:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_activity`
--

CREATE TABLE `user_activity` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_activity`
--

INSERT INTO `user_activity` (`id`, `user_id`, `action`, `created_at`) VALUES
(1, 68, 'connexion', '2025-11-23 18:21:54'),
(2, 70, 'connexion', '2025-11-23 18:22:23'),
(3, 68, 'connexion', '2025-11-23 18:22:34'),
(4, 68, 'connexion', '2025-11-23 18:27:29'),
(5, 68, 'connexion', '2025-11-23 18:27:55'),
(6, 70, 'connexion', '2025-11-23 18:30:52'),
(7, 68, 'connexion', '2025-11-23 18:31:14'),
(8, 70, 'connexion', '2025-11-23 18:34:16'),
(9, 68, 'connexion', '2025-11-23 18:34:37'),
(10, 70, 'connexion', '2025-11-23 18:39:38'),
(11, 70, 'modification', '2025-11-23 18:39:45'),
(12, 70, 'modification', '2025-11-23 18:39:49'),
(13, 68, 'connexion', '2025-11-23 18:39:58'),
(14, 68, 'connexion', '2025-11-23 18:58:06'),
(15, 70, 'connexion', '2025-11-23 19:04:20'),
(16, 70, 'modification', '2025-11-23 19:04:30'),
(17, 68, 'connexion', '2025-11-23 19:04:38'),
(18, 68, 'connexion', '2025-11-24 09:30:09'),
(19, 70, 'connexion', '2025-11-24 09:31:40'),
(20, 70, 'modification', '2025-11-24 09:32:17'),
(21, 70, 'modification', '2025-11-24 09:32:28'),
(22, 70, 'connexion', '2025-11-24 09:33:59'),
(23, 70, 'connexion', '2025-11-24 09:34:27'),
(24, 68, 'connexion', '2025-11-24 09:37:28'),
(25, 68, 'connexion', '2025-11-24 10:04:07'),
(26, 70, 'connexion', '2025-11-24 10:06:59'),
(27, 70, 'connexion', '2025-11-24 11:26:16'),
(28, 70, 'modification', '2025-11-24 11:26:31'),
(29, 70, 'modification', '2025-11-24 11:27:38'),
(30, 70, 'modification', '2025-11-24 11:28:33'),
(31, 70, 'connexion', '2025-11-24 11:30:12'),
(32, 70, 'modification', '2025-11-24 11:30:36'),
(33, 70, 'modification', '2025-11-24 11:31:04'),
(34, 70, 'modification', '2025-11-24 11:32:23'),
(35, 70, 'modification', '2025-11-24 11:34:09'),
(36, 70, 'modification', '2025-11-24 11:35:19'),
(37, 68, 'connexion', '2025-11-24 11:36:31'),
(38, 70, 'connexion', '2025-11-24 11:36:48'),
(39, 70, 'modification', '2025-11-24 11:36:54'),
(40, 70, 'modification', '2025-11-24 11:38:50'),
(41, 70, 'modification', '2025-11-24 11:41:04'),
(42, 70, 'modification', '2025-11-24 11:43:39'),
(43, 70, 'modification', '2025-11-24 11:49:46'),
(44, 70, 'modification', '2025-11-24 11:50:41'),
(45, 70, 'modification', '2025-11-24 11:54:37'),
(46, 70, 'modification', '2025-11-24 11:58:54'),
(47, 68, 'connexion', '2025-11-24 11:59:59'),
(48, 68, 'connexion', '2025-11-24 12:01:55'),
(49, 68, 'connexion', '2025-11-24 12:03:42'),
(50, 68, 'connexion', '2025-11-24 12:04:13'),
(51, 70, 'connexion', '2025-11-24 12:16:51'),
(52, 70, 'modification', '2025-11-24 12:17:00'),
(53, 75, 'connexion', '2025-11-24 12:18:28'),
(54, 75, 'connexion', '2025-11-24 12:19:30'),
(55, 75, 'modification', '2025-11-24 12:19:38'),
(56, 75, 'connexion', '2025-11-24 12:20:07'),
(57, 68, 'connexion', '2025-11-24 13:53:49'),
(58, 70, 'connexion', '2025-11-24 13:53:59'),
(59, 70, 'modification', '2025-11-24 13:54:12'),
(60, 70, 'connexion', '2025-11-24 13:54:20'),
(61, 70, 'modification', '2025-11-24 13:54:30'),
(62, 70, 'modification', '2025-11-24 13:55:54'),
(63, 70, 'modification', '2025-11-24 13:56:01'),
(64, 68, 'connexion', '2025-11-24 13:56:40'),
(65, 70, 'connexion', '2025-11-24 14:04:50'),
(66, 70, 'modification', '2025-11-24 14:05:02'),
(67, 68, 'connexion', '2025-11-24 14:08:33'),
(68, 68, 'connexion', '2025-11-24 14:12:46'),
(69, 70, 'connexion', '2025-11-24 14:14:17'),
(70, 68, 'connexion', '2025-11-24 14:14:34'),
(71, 70, 'connexion', '2025-11-24 14:15:55'),
(72, 70, 'modification', '2025-11-24 14:16:01'),
(73, 70, 'modification', '2025-11-24 14:16:05'),
(74, 68, 'connexion', '2025-11-24 14:16:28'),
(75, 70, 'connexion', '2025-11-24 14:24:02'),
(76, 70, 'connexion', '2025-11-24 14:31:27'),
(77, 68, 'connexion', '2025-11-24 14:31:47'),
(78, 70, 'connexion', '2025-11-24 14:44:53'),
(79, 70, 'modification', '2025-11-24 14:46:08'),
(80, 70, 'connexion', '2025-11-24 14:47:10'),
(81, 70, 'modification', '2025-11-24 14:47:30'),
(82, 70, 'modification', '2025-11-24 14:47:37'),
(83, 68, 'connexion', '2025-11-24 14:48:32'),
(84, 70, 'connexion', '2025-11-24 14:49:09'),
(85, 70, 'connexion', '2025-11-24 14:50:05'),
(86, 70, 'connexion', '2025-11-24 14:51:08'),
(87, 68, 'connexion', '2025-11-24 14:54:09'),
(88, 68, 'connexion', '2025-11-24 15:05:55'),
(89, 70, 'connexion', '2025-11-24 15:07:40'),
(90, 70, 'modification', '2025-11-24 15:07:48'),
(91, 70, 'connexion', '2025-11-24 15:08:02'),
(92, 70, 'modification', '2025-11-24 15:08:07'),
(93, 70, 'modification', '2025-11-24 15:08:16'),
(94, 68, 'connexion', '2025-11-24 15:09:18'),
(95, 70, 'connexion', '2025-11-24 15:11:42'),
(96, 70, 'modification', '2025-11-24 15:11:57'),
(97, 68, 'connexion', '2025-11-24 15:12:12'),
(98, 70, 'connexion', '2025-11-24 15:22:14'),
(99, 70, 'modification', '2025-11-24 15:22:28'),
(100, 68, 'connexion', '2025-11-24 15:22:49'),
(101, 70, 'connexion', '2025-11-24 16:03:38'),
(102, 78, 'modification', '2025-11-26 13:33:03'),
(103, 70, 'connexion', '2025-11-26 13:36:37'),
(104, 70, 'modification', '2025-11-26 13:36:43'),
(105, 79, 'connexion', '2025-11-27 08:15:32'),
(106, 79, 'modification', '2025-11-27 08:15:44'),
(107, 68, 'connexion', '2025-11-27 08:19:12'),
(108, 68, 'connexion', '2025-11-27 08:22:50'),
(109, 68, 'connexion', '2025-11-27 08:25:55'),
(110, 68, 'connexion', '2025-11-27 08:32:19'),
(111, 70, 'connexion', '2025-11-27 08:37:25'),
(112, 68, 'connexion', '2025-11-27 08:40:20'),
(113, 70, 'connexion', '2025-11-27 08:46:55'),
(114, 68, 'connexion', '2025-11-27 08:50:10'),
(115, 70, 'connexion', '2025-11-27 08:50:33'),
(116, 70, 'connexion', '2025-11-27 08:50:46'),
(117, 70, 'connexion', '2025-11-27 08:51:45'),
(118, 68, 'connexion', '2025-11-27 08:52:47'),
(119, 68, 'connexion', '2025-11-27 08:53:26'),
(120, 68, 'connexion', '2025-11-27 08:56:17'),
(121, 68, 'connexion', '2025-11-27 08:56:41'),
(122, 70, 'connexion', '2025-11-27 08:57:50'),
(123, 70, 'connexion', '2025-11-27 08:58:02'),
(124, 68, 'connexion', '2025-11-27 08:58:11'),
(125, 78, 'modification', '2025-11-27 12:27:58'),
(126, 70, 'connexion', '2025-11-27 15:46:14'),
(127, 70, 'connexion', '2025-11-27 15:47:10'),
(128, 70, 'connexion', '2025-11-27 15:47:33'),
(129, 68, 'connexion', '2025-11-27 15:48:29'),
(130, 70, 'connexion', '2025-11-27 15:49:14'),
(131, 70, 'connexion', '2025-11-27 15:59:34'),
(132, 70, 'connexion', '2025-11-27 16:02:42'),
(133, 68, 'connexion', '2025-11-27 16:12:47'),
(134, 80, 'connexion', '2025-11-28 14:46:08');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `innovation_id` int(11) NOT NULL,
  `type_vote` enum('up','down') NOT NULL,
  `date_vote` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nom` (`nom`),
  ADD KEY `idx_date` (`date_creation`);

--
-- Indexes for table `commentaires`
--
ALTER TABLE `commentaires`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_innovation` (`innovation_id`),
  ADD KEY `idx_date` (`date_creation`);

--
-- Indexes for table `confirmations_email`
--
ALTER TABLE `confirmations_email`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Indexes for table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `conversation_users`
--
ALTER TABLE `conversation_users`
  ADD PRIMARY KEY (`conversation_id`,`user_id`),
  ADD KEY `fk_conv_user_user` (`user_id`);

--
-- Indexes for table `innovations`
--
ALTER TABLE `innovations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categorie_id` (`categorie_id`),
  ADD KEY `idx_titre` (`titre`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_date` (`date_creation`);

--
-- Indexes for table `logs_connexions`
--
ALTER TABLE `logs_connexions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_messages_conversation` (`conversation_id`),
  ADD KEY `fk_messages_user` (`user_id`);

--
-- Indexes for table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_innovation` (`innovation_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_role` (`role_id`);

--
-- Indexes for table `user_activity`
--
ALTER TABLE `user_activity`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_innovation` (`innovation_id`),
  ADD KEY `idx_type` (`type_vote`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `commentaires`
--
ALTER TABLE `commentaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `confirmations_email`
--
ALTER TABLE `confirmations_email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `innovations`
--
ALTER TABLE `innovations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs_connexions`
--
ALTER TABLE `logs_connexions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- AUTO_INCREMENT for table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1012;

--
-- AUTO_INCREMENT for table `user_activity`
--
ALTER TABLE `user_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `commentaires`
--
ALTER TABLE `commentaires`
  ADD CONSTRAINT `commentaires_ibfk_1` FOREIGN KEY (`innovation_id`) REFERENCES `innovations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `confirmations_email`
--
ALTER TABLE `confirmations_email`
  ADD CONSTRAINT `confirmations_email_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `conversation_users`
--
ALTER TABLE `conversation_users`
  ADD CONSTRAINT `fk_conv_user_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conversation_users_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `innovations`
--
ALTER TABLE `innovations`
  ADD CONSTRAINT `innovations_ibfk_1` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `logs_connexions`
--
ALTER TABLE `logs_connexions`
  ADD CONSTRAINT `logs_connexions_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `user` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_messages_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  ADD CONSTRAINT `pieces_jointes_ibfk_1` FOREIGN KEY (`innovation_id`) REFERENCES `innovations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`innovation_id`) REFERENCES `innovations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
