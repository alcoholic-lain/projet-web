-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 11, 2025 at 01:34 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tunispace_database`
--

-- --------------------------------------------------------

--
-- Table structure for table `badges`
--

CREATE TABLE `badges` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `badges`
--

INSERT INTO `badges` (`id`, `name`, `description`, `icon`) VALUES
(1, 'Nouveau', 'Inscription récente', 'new.png'),
(2, 'Actif', '10 connexions effectuées', 'active.jpeg'),
(3, 'Super Actif', '50 connexions effectuées', 'superactive.png'),
(4, 'Ancien', 'Compte âgé de 1 an', 'old.png'),
(5, 'Premier Post', 'A publié son premier post', 'firstpost.png');

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
(30, 'CS Project', 1, '2025-11-17 12:21:45');

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
(1, 1, 1, '2025-11-17 12:21:45'),
(1, 2, 0, '2025-11-17 12:21:45'),
(2, 1, 1, '2025-11-17 12:21:45'),
(2, 3, 0, '2025-11-17 12:21:45'),
(3, 1, 1, '2025-11-17 12:21:45'),
(3, 4, 0, '2025-11-17 12:21:45'),
(4, 1, 1, '2025-11-17 12:21:45'),
(4, 5, 0, '2025-11-17 12:21:45'),
(5, 2, 1, '2025-11-17 12:21:45'),
(5, 3, 0, '2025-11-17 12:21:45'),
(6, 2, 1, '2025-11-17 12:21:45'),
(6, 4, 0, '2025-11-17 12:21:45'),
(7, 2, 1, '2025-11-17 12:21:45'),
(7, 5, 0, '2025-11-17 12:21:45'),
(8, 3, 1, '2025-11-17 12:21:45'),
(8, 4, 0, '2025-11-17 12:21:45'),
(9, 3, 1, '2025-11-17 12:21:45'),
(9, 5, 0, '2025-11-17 12:21:45'),
(10, 4, 1, '2025-11-17 12:21:45'),
(10, 5, 0, '2025-11-17 12:21:45'),
(11, 1, 1, '2025-11-17 12:21:45'),
(11, 2, 0, '2025-11-17 12:21:45'),
(11, 3, 0, '2025-11-17 12:21:45'),
(12, 4, 1, '2025-11-17 12:21:45'),
(12, 5, 0, '2025-11-17 12:21:45'),
(12, 6, 0, '2025-11-17 12:21:45'),
(13, 2, 1, '2025-11-17 12:21:45'),
(13, 3, 0, '2025-11-17 12:21:45'),
(13, 4, 0, '2025-11-17 12:21:45'),
(13, 5, 0, '2025-11-17 12:21:45'),
(14, 1, 1, '2025-11-17 12:21:45'),
(14, 4, 0, '2025-11-17 12:21:45'),
(14, 7, 0, '2025-11-17 12:21:45'),
(14, 8, 0, '2025-11-17 12:21:45'),
(15, 2, 1, '2025-11-17 12:21:45'),
(15, 5, 0, '2025-11-17 12:21:45'),
(15, 8, 0, '2025-11-17 12:21:45'),
(15, 9, 0, '2025-11-17 12:21:45'),
(16, 1, 1, '2025-11-17 12:21:45'),
(16, 6, 0, '2025-11-17 12:21:45'),
(16, 7, 0, '2025-11-17 12:21:45'),
(17, 3, 1, '2025-11-17 12:21:45'),
(17, 8, 0, '2025-11-17 12:21:45'),
(17, 9, 0, '2025-11-17 12:21:45'),
(18, 4, 1, '2025-11-17 12:21:45'),
(18, 5, 0, '2025-11-17 12:21:45'),
(18, 6, 0, '2025-11-17 12:21:45'),
(18, 7, 0, '2025-11-17 12:21:45'),
(19, 2, 1, '2025-11-17 12:21:45'),
(19, 3, 0, '2025-11-17 12:21:45'),
(19, 8, 0, '2025-11-17 12:21:45'),
(19, 10, 0, '2025-11-17 12:21:45'),
(20, 1, 1, '2025-11-17 12:21:45'),
(20, 3, 0, '2025-11-17 12:21:45'),
(20, 5, 0, '2025-11-17 12:21:45'),
(20, 7, 0, '2025-11-17 12:21:45'),
(20, 9, 0, '2025-11-17 12:21:45'),
(21, 1, 1, '2025-11-17 12:21:45'),
(21, 8, 0, '2025-11-17 12:21:45'),
(21, 9, 0, '2025-11-17 12:21:45'),
(21, 10, 0, '2025-11-17 12:21:45'),
(22, 2, 1, '2025-11-17 12:21:45'),
(22, 4, 0, '2025-11-17 12:21:45'),
(22, 6, 0, '2025-11-17 12:21:45'),
(22, 8, 0, '2025-11-17 12:21:45'),
(23, 3, 1, '2025-11-17 12:21:45'),
(23, 5, 0, '2025-11-17 12:21:45'),
(23, 7, 0, '2025-11-17 12:21:45'),
(23, 9, 0, '2025-11-17 12:21:45'),
(24, 1, 1, '2025-11-17 12:21:45'),
(24, 2, 0, '2025-11-17 12:21:45'),
(24, 3, 0, '2025-11-17 12:21:45'),
(24, 4, 0, '2025-11-17 12:21:45'),
(24, 5, 0, '2025-11-17 12:21:45'),
(25, 6, 1, '2025-11-17 12:21:45'),
(25, 7, 0, '2025-11-17 12:21:45'),
(25, 8, 0, '2025-11-17 12:21:45'),
(25, 9, 0, '2025-11-17 12:21:45'),
(25, 10, 0, '2025-11-17 12:21:45'),
(26, 1, 1, '2025-11-17 12:21:45'),
(26, 6, 0, '2025-11-17 12:21:45'),
(26, 9, 0, '2025-11-17 12:21:45'),
(27, 2, 1, '2025-11-17 12:21:45'),
(27, 7, 0, '2025-11-17 12:21:45'),
(27, 10, 0, '2025-11-17 12:21:45'),
(28, 4, 1, '2025-11-17 12:21:45'),
(28, 6, 0, '2025-11-17 12:21:45'),
(28, 10, 0, '2025-11-17 12:21:45'),
(29, 3, 1, '2025-11-17 12:21:45'),
(29, 5, 0, '2025-11-17 12:21:45'),
(29, 8, 0, '2025-11-17 12:21:45'),
(30, 1, 1, '2025-11-17 12:21:45'),
(30, 3, 0, '2025-11-17 12:21:45'),
(30, 6, 0, '2025-11-17 12:21:45'),
(30, 10, 0, '2025-11-17 12:21:45');

-- --------------------------------------------------------

--
-- Table structure for table `email_verification`
--

CREATE TABLE `email_verification` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` varchar(6) NOT NULL,
  `expires_at` datetime NOT NULL,
  `last_sent` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `email_verification`
--

INSERT INTO `email_verification` (`id`, `email`, `code`, `expires_at`, `last_sent`) VALUES
(1, 'Zak.bo@outlook.fr', '288772', '2025-12-07 11:34:53', '2025-12-07 11:29:53'),
(2, 'hdhdzzzghd@gmail.com', '832770', '2025-12-07 12:13:28', '2025-12-07 12:08:28'),
(3, 'hdhdzzzgh@gmail.com', '657724', '2025-12-07 12:14:59', '2025-12-07 12:09:59');

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
(1, 1, 1, 'Hi Bob, this is Alice in conversation 1', '2025-11-17 12:21:45'),
(2, 1, 2, 'Hey Alice! Nice to chat.', '2025-11-17 12:21:45'),
(3, 2, 1, 'Hi Charlie, Alice here in convo 2', '2025-11-17 12:21:45'),
(4, 2, 3, 'Hi Alice!', '2025-11-17 12:21:45'),
(5, 3, 1, 'Hi David, we need to talk about project.', '2025-11-17 12:21:45'),
(6, 3, 4, 'Sure, tell me.', '2025-11-17 12:21:45'),
(7, 4, 1, 'Hi Eve, how are you?', '2025-11-17 12:21:45'),
(8, 4, 5, 'I am good, thanks Alice.', '2025-11-17 12:21:45'),
(9, 5, 2, 'Yo Charlie, it\'s Bob', '2025-11-17 12:21:45'),
(10, 5, 3, 'Hey Bob!', '2025-11-17 12:21:45'),
(11, 6, 2, 'David, are you coming later?', '2025-11-17 12:21:45'),
(12, 6, 4, 'Yes, I will.', '2025-11-17 12:21:45'),
(13, 7, 2, 'Eve, did you see this?', '2025-11-17 12:21:45'),
(14, 7, 5, 'Not yet, send it.', '2025-11-17 12:21:45'),
(15, 8, 3, 'David, ready for the exam?', '2025-11-17 12:21:45'),
(16, 8, 4, 'Almost, still revising.', '2025-11-17 12:21:45'),
(17, 9, 3, 'Eve, group homework?', '2025-11-17 12:21:45'),
(18, 9, 5, 'Working on it.', '2025-11-17 12:21:45'),
(19, 10, 4, 'Eve, let\'s meet after class.', '2025-11-17 12:21:45'),
(20, 10, 5, 'Ok David.', '2025-11-17 12:21:45'),
(21, 11, 1, 'Welcome to Study Group A!', '2025-11-17 12:21:45'),
(22, 11, 2, 'Thanks for the invite.', '2025-11-17 12:21:45'),
(23, 11, 3, 'Let\'s ace this.', '2025-11-17 12:21:45'),
(24, 12, 4, 'Study Group B starting.', '2025-11-17 12:21:45'),
(25, 12, 5, 'Hi everyone.', '2025-11-17 12:21:45'),
(26, 12, 6, 'Hello!', '2025-11-17 12:21:45'),
(27, 13, 2, 'Gaming squad, tonight?', '2025-11-17 12:21:45'),
(28, 13, 3, 'I\'m in.', '2025-11-17 12:21:45'),
(29, 13, 4, 'Me too.', '2025-11-17 12:21:45'),
(30, 14, 1, 'Project Team 1 kickoff.', '2025-11-17 12:21:45'),
(31, 14, 4, 'Let\'s go.', '2025-11-17 12:21:45'),
(32, 14, 7, 'I\'m here.', '2025-11-17 12:21:45'),
(33, 14, 8, 'Hi all.', '2025-11-17 12:21:45'),
(34, 15, 2, 'Project Team 2 meeting.', '2025-11-17 12:21:45'),
(35, 15, 5, 'Ok.', '2025-11-17 12:21:45'),
(36, 15, 8, 'Got it.', '2025-11-17 12:21:45'),
(37, 15, 9, 'Cool.', '2025-11-17 12:21:45'),
(38, 16, 1, 'Family Chat 1, hello!', '2025-11-17 12:21:45'),
(39, 16, 6, 'Hi there.', '2025-11-17 12:21:45'),
(40, 16, 7, 'Hey!', '2025-11-17 12:21:45'),
(41, 17, 3, 'Family Chat 2 checking in.', '2025-11-17 12:21:45'),
(42, 17, 8, 'Hi!', '2025-11-17 12:21:45'),
(43, 17, 9, 'Hello.', '2025-11-17 12:21:45'),
(44, 18, 4, 'Friends Forever ❤️', '2025-11-17 12:21:45'),
(45, 18, 5, 'Always.', '2025-11-17 12:21:45'),
(46, 18, 6, 'For sure.', '2025-11-17 12:21:45'),
(47, 18, 7, 'Yesss.', '2025-11-17 12:21:45'),
(48, 19, 2, 'Weekend plans?', '2025-11-17 12:21:45'),
(49, 19, 3, 'Let\'s go out.', '2025-11-17 12:21:45'),
(50, 19, 8, 'I\'m in.', '2025-11-17 12:21:45'),
(51, 19, 10, 'Same.', '2025-11-17 12:21:45'),
(52, 20, 1, 'Music Lovers, new playlist.', '2025-11-17 12:21:45'),
(53, 20, 3, 'Drop the link.', '2025-11-17 12:21:45'),
(54, 20, 5, 'Nice tracks.', '2025-11-17 12:21:45'),
(55, 20, 7, 'Love it.', '2025-11-17 12:21:45'),
(56, 20, 9, 'Fire.', '2025-11-17 12:21:45'),
(57, 21, 1, 'Anime Club tonight.', '2025-11-17 12:21:45'),
(58, 21, 8, 'Can\'t wait.', '2025-11-17 12:21:45'),
(59, 21, 9, 'Same here.', '2025-11-17 12:21:45'),
(60, 21, 10, 'Let\'s go.', '2025-11-17 12:21:45'),
(61, 22, 2, 'Work buddies, standup in 10.', '2025-11-17 12:21:45'),
(62, 22, 4, 'Ok.', '2025-11-17 12:21:45'),
(63, 22, 6, 'On my way.', '2025-11-17 12:21:45'),
(64, 22, 8, 'Got it.', '2025-11-17 12:21:45'),
(65, 23, 3, 'Esprit G2 lecture starts now.', '2025-11-17 12:21:45'),
(66, 23, 5, 'Thanks.', '2025-11-17 12:21:45'),
(67, 23, 7, 'Here.', '2025-11-17 12:21:45'),
(68, 23, 9, 'Joining.', '2025-11-17 12:21:45'),
(69, 24, 1, 'Dev Chat: merge conflict again.', '2025-11-17 12:21:45'),
(70, 24, 2, 'haha.', '2025-11-17 12:21:45'),
(71, 24, 3, 'classic.', '2025-11-17 12:21:45'),
(72, 24, 4, 'lol.', '2025-11-17 12:21:45'),
(73, 24, 5, 'We fix it.', '2025-11-17 12:21:45'),
(74, 25, 6, 'Random chat time.', '2025-11-17 12:21:45'),
(75, 25, 7, 'What\'s up.', '2025-11-17 12:21:45'),
(76, 25, 8, 'All good.', '2025-11-17 12:21:45'),
(77, 25, 9, 'Same.', '2025-11-17 12:21:45'),
(78, 25, 10, 'Nice.', '2025-11-17 12:21:45'),
(79, 26, 1, 'Lab Group meeting at 2 PM.', '2025-11-17 12:21:45'),
(80, 26, 6, 'Ok.', '2025-11-17 12:21:45'),
(81, 26, 9, 'Fine by me.', '2025-11-17 12:21:45'),
(82, 27, 2, 'Dorm Chat noise complaints.', '2025-11-17 12:21:45'),
(83, 27, 7, 'Oops.', '2025-11-17 12:21:45'),
(84, 27, 10, 'Sorry.', '2025-11-17 12:21:45'),
(85, 28, 4, 'Gym Bros, leg day.', '2025-11-17 12:21:45'),
(86, 28, 6, 'Nooo.', '2025-11-17 12:21:45'),
(87, 28, 10, 'Let\'s do it.', '2025-11-17 12:21:45'),
(88, 29, 3, 'Movie Night picks?', '2025-11-17 12:21:45'),
(89, 29, 5, 'Action.', '2025-11-17 12:21:45'),
(90, 29, 8, 'Comedy.', '2025-11-17 12:21:45'),
(91, 30, 1, 'CS Project deadline soon.', '2025-11-17 12:21:45'),
(92, 30, 3, 'We\'re close.', '2025-11-17 12:21:45'),
(93, 30, 6, 'Need more coffee.', '2025-11-17 12:21:45'),
(94, 30, 10, 'Same 😂', '2025-11-17 12:21:45');

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
('nournour2862005@gmail.com', 'd2b7b66e6068b31a819420ced1856d52b79f66aa2498ef9877f8e0cc88abf7c0', '2025-11-27 14:33:24'),
('nournour2862005@gmail.com', 'd04ed0e275f8e0c6e2797e8176dfef9b6599cfd89abb9e8cc717c66f1c1c7c5e', '2025-11-29 01:16:45'),
('nournour2862005@gmail.com', '61e170e35f614b2964dba3569c177ee161411c2ee8bd30b0be75a4caea0c1578', '2025-11-29 01:18:45'),
('nournour2862005@gmail.com', 'b053ef1d0f4f25dff62df0fc463691889b94504c222e6ae00b28b34015a47cf4', '2025-11-29 01:19:09'),
('nournour2862005@gmail.com', '99205d9d2bef7e79e0b29cf4f0b63bd99e590bc36b7048ca6a0ef9514238dddf', '2025-11-29 01:19:55'),
('zakariabenwirane@gmail.com', 'b85041b1631d695a59b90e1020305c0e0e50b8525b7288ff25bd29fa9a054aa4', '2025-11-29 01:21:35'),
('nournour2862005@gmail.com', '618c3a48fca709cb6d5d3f2c5a17330fe71ef248f32254e180ffceb6ea8c596c', '2025-11-29 10:09:25'),
('zakariabenwirane@gmail.com', '1e9fe8bd3d6c715d05f69d742a5e2466f2facbcac679c33741f160b3f038887e', '2025-11-29 10:09:54'),
('zakariabenwirane@gmail.com', '6c7e5c8aab2d8a76a2123134e2bf919736cc27e00e6f987c3bbbf3f6373e3a62', '2025-11-29 10:20:53'),
('zakariabenwirane@gmail.com', '88ba7dc9c5fa6b0ca6f5aac7a244f181b854b3c7683273bdb6a8efe141c71dff', '2025-11-29 10:22:38'),
('zico21023@gmail.com', '4d663080555bddb74c482c938c7bddd08d83a0a28de33c948c7816e0280b27c6', '2025-11-29 10:29:35'),
('nournour2862005@gmail.com', 'a45359fd3ddb083ad0068781bd59b3c9ca6453d876c3c5041101c1aeffe48c2e', '2025-11-29 10:42:16'),
('nournour2862005@gmail.com', '10db9f4a58930ea607da9f2def35a5b8c1fdfb808c0f51dbdb30d1f518ac1ecb', '2025-11-29 10:43:07'),
('nournour2862005@gmail.com', '97bdff8ede9b0297d668c719e2d12e496bcfdd9465acd1f4ef5307de277b339d', '2025-11-29 10:48:24'),
('nournour2862005@gmail.com', '78ab4fd2ab04ace5f0bf3fa20be57b740025272371ad021a1a5a3f8b568ce866', '2025-11-29 10:57:14'),
('nournour2862005@gmail.com', 'e74d15220fe6cf310a739090aa38cde6e20568f92b2fa7d233a508c3c4b397b5', '2025-11-29 10:59:00'),
('nournour2862005@gmail.com', '1aa15696468ac8439a1aba7491c74b747129a78526b6f0f0852d333db2881223', '2025-11-29 11:00:17'),
('nournour2862005@gmail.com', 'f7ece9811c0e15a7df0ed153930c90fb675ab5fa41c7d50f29da2ae8b261b832', '2025-11-29 11:04:43'),
('nournour2862005@gmail.com', '646eaccb8d979e5bacc93678acc2ab59cc55d049159a768ff493bd6d5afd0b1d', '2025-11-29 11:04:45'),
('nournour2862005@gmail.com', '8803021442d747662580412e6a7a5605b3acef43d8d3bfb925d938fff4c3ce12', '2025-11-29 11:04:46'),
('nournour2862005@gmail.com', '42d6b64931cdfe5e305ef8f43005c3fc5958482eacc36c8f3bedb2c443327918', '2025-11-29 11:04:46'),
('nournour2862005@gmail.com', 'bcc6a4a1db997d1229294dfe9122d3323f5f5b614fecd5e42aa2e7681fc3cb84', '2025-11-29 11:04:51'),
('nournour2862005@gmail.com', 'ff6b93800e4212062d689e73dad1b14c5d502decfef5a135eba584a95fdeac58', '2025-11-29 11:13:33'),
('nournour2862005@gmail.com', '281d73f6b9f63a959f3dddd0e696a07edf96e574836ebcaa930542de957857e7', '0000-00-00 00:00:00'),
('nournour2862005@gmail.com', 'd8fc3d6241b33d36db3f394ad908c9d1a54c4e631654d109d2b94cbeee8b15ed', '0000-00-00 00:00:00'),
('nournour2862005@gmail.com', '39a2b9d66bbdd0886f77004b5a586d06554021767be35effb7b45b779ffb4684', '0000-00-00 00:00:00'),
('nournour2862005@gmail.com', '67e695b1d65dd0a47a1ea7d3d4ed8ca22ea6954f9683be5018a4b76800fedb97', '2025-11-29 11:28:42'),
('nournour2862005@gmail.com', 'a5eb17cd99f15dd6b4989d774997653a1c5cc166bb0dc6d06bf6f810147a186f', '2025-11-29 11:30:07'),
('nournour2862005@gmail.com', '55ee41eb8b16112e4d656e46a8b6545739766266c574280dca2bbad1e41a13bf', '2025-11-29 11:30:08'),
('nournour2862005@gmail.com', 'b043600557b44002923b45450cdf77975bcd53521ca32f8baa9303e2ebd17d79', '2025-11-29 11:30:08'),
('nournour2862005@gmail.com', '1c65097822eb30a307a85e78cc5f051b3f6a3fb856c1077dc56da52564837a0b', '2025-11-29 11:31:09'),
('nournour2862005@gmail.com', '32dad5cd67a24594d36e017a74cf81cf93a3b19376dcba036ff0c8cb165f96cb', '2025-11-29 11:31:28'),
('nournour2862005@gmail.com', 'e0625df56c7d40b4edfb890b4c4ab8f481044104665a6ae786a2f3a4947e0e26', '2025-11-29 11:48:33'),
('zak.thea@gmail.com', '7e290eb3e529d2c0835ac3a5a0e0521c8261fb69f6b24f2d30cc52e3a11e72c9', '2025-11-29 11:48:42'),
('nournour2862005@gmail.com', 'bdd7cc2418617c7c5f2a2f01ef9e07d2abbbeeb586cc7509a1cc3a6c92581873', '2025-11-29 11:48:55'),
('nournour2862005@gmail.com', 'fb53daffd3c019b17bccb3e70c540d4f3d4310da6f87de2d00f73397cd62dbcc', '2025-11-29 11:49:05'),
('nournour2862005@gmail.com', '22ccc23d6d79e36901a02d83aa913f1dda9cc8f1b0c84595bddd50721e44fbf4', '2025-11-29 11:49:07'),
('nournour2862005@gmail.com', 'ea1a8e32388d293ec09ee8b1e05d1e457ac5a3879d25723d1821a019570d41fd', '2025-11-29 11:51:17'),
('nournour2862005@gmail.com', 'a538e5ef5da29b279b26fd6c92dce9759b6be66f21d9cd618943f77935835505', '2025-11-29 11:51:27'),
('nournour2862005@gmail.com', '2509775dbb6234e4f34f2fefadcee8f86d45790353a01f9775ee5bf53ed2d796', '2025-11-29 11:53:32'),
('zakariabenwirane@gmail.com', '027b51c79a5dbf47692741cc6e10a5540ce29439b4716ba6ab9a43fa71ccff37', '2025-11-29 11:53:46'),
('zakariabenwirane@gmail.com', '66e62fc0024ef86e267bf2f29091d04a5d9e8e1e52a13a8ee3fd445e965ed5fe', '2025-11-29 11:53:54'),
('nournour2862005@gmail.com', 'f91c20367cc83dfccbf9dfaa5ee444b4b46509f3f7537c885bf4f8f3884630c5', '2025-11-29 11:58:45'),
('zak.thea@gmail.com', '647e1e56f4d78db389edc8efffa4c8e648e3709e4ffbb054b2eea9f3369eebf5', '2025-11-29 11:59:13'),
('zico21023@gmail.com', 'b676071bc92106fdadb2b16673ca94cbc36920f481f5698f650fcf951de0639c', '2025-11-29 11:59:27'),
('nournour2862005@gmail.com', '5b656dfb8734f07712e24f52727c8f53e144ffe2ad15080fd455aca385e63177', '2025-11-29 12:01:30'),
('nournour2862005@gmail.com', '71aa4b7f643b2e37e5de3efb66ccd342dcb9ea70c8d1fc6f5ed18371125e0453', '2025-11-29 12:09:47'),
('nournour2862005@gmail.com', '6e62abaea413fcddd66931205404dbce44e22fd25ee6ab8665375f12ba149c2e', '2025-11-29 12:09:53'),
('nournour2862005@gmail.com', '5e124ac58b408d84613f2ac525f434852fb65d8ec7da08355f7a583525592bec', '2025-11-29 12:10:21'),
('nournour2862005@gmail.com', 'c1b50285746696077c71b3c35983fe11ae06ed124b19e48a4609ef124542486d', '2025-11-29 12:13:32'),
('nournour2862005@gmail.com', '5f01693a100953efd77284004ad56c9f180f0a2ecd387931a5ded55019d19b97', '2025-11-29 12:13:43'),
('nournour2862005@gmail.com', '1a6f94182d0453d856b859e5849410f1d5566e2a736dc9f1cf3a79fbc88b1f54', '2025-11-29 12:17:30'),
('zico21023@gmail.com', '7cc9302e79284cc8f0f9389b330e851110ccef2f198686a5a2c26e8a4d74ace5', '2025-11-29 12:17:44'),
('zico21023@gmail.com', 'ae9ae8e9b547f46894f1e06aa7b1564f3e2010af222c91dc6e38dcc94ba8482f', '2025-11-29 12:18:45'),
('dsqsdqd@dsqd.com', 'c3abe617e378e20788dd37ef54058099a25ed46d911917d86c91f673a28fdb13', '2025-11-29 12:19:06'),
('nournour2862005@gmail.com', 'eb1e96af18445852ae6c6cfbe3a8d5f8c6d7ceff91f5c99568d81a358c70ce2f', '2025-11-29 12:23:28'),
('zico21023@gmail.com', '683e0a2a05edb07abc8e2f76bd65cc36de225f7dc19191bc642f8a2f36c35b11', '2025-11-29 12:23:40'),
('zico21023@gmail.com', '0d7395b9073ee408a529b4edf67bc09fc41b15f413c62a61bcb8fe2755d7f38b', '2025-11-29 12:23:41'),
('zico21023@gmail.com', '48e53f6fd96e5db592ccc669e43d995b1e8a65cde9abeeb2a93b10b2f8a4f495', '2025-11-29 12:23:54'),
('zico21023@gmail.com', '39ecd35354aa2c05a42696f6060eb3cb4e56656a22028ceb7e7dd8b1dce8d580', '2025-11-29 12:24:17'),
('nournour2862005@gmail.com', 'ecad9a87bcbf2b4f59db451c4b17a6b7f7a4f17360d8e55879f6304f290b80bb', '2025-11-29 12:27:53'),
('nournour2862005@gmail.com', 'de79d65b59d08d646943c7ac749bc494792237d1586b1f30bca2440910669622', '2025-11-29 12:28:05'),
('nournour2862005@gmail.com', 'e04c99e2b3e0fb4600335d5b50c725665df5121a6cb8e022982420e3828af4f5', '2025-11-29 12:30:23'),
('zico21023@gmail.com', 'f48314666e38d08b215def747b7061fb9d6a2f595b9d24bb28f57b2d8e802cfe', '2025-11-29 12:30:31'),
('zico21023@gmail.com', '7b2627ac81ceeb269c04e4046ebc845ca5053574244dfd85f53aaf428a6f72a6', '2025-11-29 12:35:49'),
('zico21023@gmail.com', '3007a77c60b2bebe814a6e469b22547fa6c0c510d1134c3565c86322d74a0809', '2025-11-29 12:39:29'),
('zico21023@gmail.com', 'f34e0f579126726fdbc2174b1d7255514a3dd772a794851958090e27c30e9484', '2025-11-29 12:41:38'),
('zico21023@gmail.com', 'a17c3bcbb2d1f53112bbf74b86e6eeedae9513d955878322af3a917cf85360c7', '2025-11-29 12:44:24'),
('zico21023@gmail.com', 'b87ca037cdb02cc053b774d70cf8e265e779ea679475fe1d80b6c16e32c71baa', '2025-11-29 12:47:09'),
('zico21023@gmail.com', '73a75b00d1de432932f26bf9c39068fe3597f2b0093e779b8f059eb8ee94f853', '2025-11-29 13:03:44'),
('zico21023@gmail.com', '759fff319c7c6b5fc9d83e1bbd8490ec3780af851b895c311394fb377f4f1a89', '2025-11-29 13:05:02'),
('zico21023@gmail.com', '17b0195f35fafbd319c988c1caa5740252a4ed0c5418400f4add2dffe05fdfcd', '2025-11-29 13:48:23'),
('nournour2862005@gmail.com', '970c49fad361cd22fdbb73978d6ee68ea4e743e31acc2f7787d16ba53ac4f393', '2025-11-30 11:52:57'),
('zico21023@gmail.com', '46db1421b091f1dad90f81ac2dfda6146ac4a43318b10ef30b937eb71cd9dcbc', '2025-11-30 11:53:04'),
('zico21023@gmail.com', '7ea7ef0056e2926137a0e6e1a8d5c14989e839a9cfbe38a0e8e80536273a401a', '2025-11-30 12:02:55'),
('zico21023@gmail.com', '8c3e1fd3028a4ad1294fcb3daea74dcf4225112b8dbac76e5d3412e3bbb7863c', '2025-11-30 12:05:42'),
('zico21023@gmail.com', '39cd935c3dcef6137566ccb8dba89aa5eda76cafc66d7d33c83c9ad05bb8c9a9', '2025-11-30 12:08:12'),
('zico21023@gmail.com', '2c1b4d35be171208be852ca016d3d6ac9bf653ee7aed9220b81f5387bd02bab9', '2025-11-30 12:10:03'),
('zico21023@gmail.com', 'a635dcf2f4a7f4450331ea54fabce1703e71461c5a234b49e7d52af10b09e63b', '2025-11-30 12:14:26'),
('zico21023@gmail.com', '7295097be0900e10b9549660f90eca5b107572c7f8011e68e05680012d7d4451', '2025-11-30 12:21:16'),
('zico21023@gmail.com', '6a67edc1526f29a324e280cc7cecdb4a6928f0d86bea5fdd8c5bd0363b5d0d8d', '2025-12-01 11:43:22'),
('zico21023@gmail.com', 'e30e427f0aeaf24af133905dfa0cd1be7dc6c487750cfeb2789dacb3ac46d45c', '2025-12-01 11:48:43'),
('zico21023@gmail.com', 'bdd2e8907279edc7af08589c5786de1c765bfa1a98d2ae3dfbb2246c830ca794', '2025-12-02 09:24:40'),
('zakariabenwirane@gmail.com', '0f1d0a60fd94882907318697b500452b4bdccf30b23b67c72df516457d460a68', '2025-12-05 12:56:47'),
('zico21023@gmail.com', '3d56e5f43180adebbec2cb18d374f7882579efd8af4b120d85d4ac8b73a38d2c', '2025-12-05 13:58:41'),
('zico21023@gmail.com', '76b7755bde71ec5a534ff9ccd27369b4dfc9a050848923f435e94cef7eed20ac', '2025-12-07 01:01:05'),
('hdhdzzzgh@gmail.com', '3ba1f28055ce494bea0018659556110513597b3de258f4a7d121abcc5d0953c5', '2025-12-07 12:12:34'),
('hdhdzzzghd@gmail.com', 'dc741bdd7d35f2809fba7de7c2dca082955d31e338decfa210a911725b28e2f7', '2025-12-07 12:13:35'),
('zico21023@gmail.com', '4f03f58db68c4e123e8caeb62a824e5126d0bac80ea76b737396f72eeb0a8ffb', '2025-12-07 23:25:17');

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
  `pseudo` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `statut` varchar(20) NOT NULL DEFAULT 'actif',
  `role_id` int(11) DEFAULT 2,
  `planet` enum('terra','mars','venus','jupiter') NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `login_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `pseudo`, `email`, `google_id`, `password`, `statut`, `role_id`, `planet`, `avatar`, `login_count`) VALUES
(66, 'zak12', 'zakaria.benouirsane@esprit.tn', NULL, '$2y$10$DbExiga4ecL1SfXPfPUx..FOcq/LbIV473rNff2RKVcBCgLbuaVA.', 'actif', 1, 'mars', NULL, 0),
(68, 'Zak', 'zakariabenouirane@esprit.tn', NULL, '$2y$10$jN95QpSpsLUturuobt.qqef4prcpYH6jI8uONPfCh/BTHTPetVx02', 'actif', 1, 'jupiter', NULL, 0),
(69, 'zaktheastroAaa', 'zak.thea@gmail.com', NULL, '$2y$10$eZEcuHxVhmDdrExhohNgC.H2pPtQZQ6IBnAcHKCQ3FDGXGm.rNMLC', 'banni', 4, 'mars', NULL, 0),
(70, 'Nour___124', 'nournour2862005@gmail.com', NULL, '$2y$10$KZWsAQQksJrgUX.869jEuuQWgQAbcNWlV2WEmnRbHIgHF.RpqD1vC', 'inactif', 4, 'mars', 'uploads/avatars/6934c6d41a931.png', 2),
(75, 'hichem', 'hichem@gmail.com', NULL, '$2y$10$tGIAaSIjwN2pIBk/TuMcGenh3JyGUSiUIbJ3C.sn/K.HjUeMHRAD2', 'actif', 1, 'mars', '../../../view/Client/login/uploads/avatars/69243f4a38730controllerpng', 0),
(79, 'Zak123', 'zakaria.benouirane@esprit.tn', NULL, '$2y$10$LZXB.3hKhgwGHT8dqPpVCuwpsaUsWwKZ/P3qKujHEUBNjClIFfLyK', 'actif', 2, 'mars', 'uploads/avatars/6927faa03382f.png', 0),
(80, 'testtest', 'zico21023@gmail.com', '105730328103449541564', '$2y$10$m0ZD6PdEiULVEn0SCnjm3OFFqXWXEOO4WMthHLZ6pFvo3g6izGrpe', 'inactif', 2, 'mars', 'uploads/avatars/6934c2d12a464.png', 0),
(84, 'elGass', 'elGass123@gmail.com', NULL, '$2y$10$JPFBI8aSU4PSnqQevSDB6ermNkLjT.LjrlTis4f3s2DwhP1tAMpWe', 'actif', 2, 'mars', 'uploads/avatars/6930561943477.jpg', 0),
(85, 'Zak Bo', 'salwabahroun120@gmail.com', '114260841652645378628', NULL, 'actif', 2, 'terra', 'https://lh3.googleusercontent.com/a/ACg8ocIYXUXGKW8OzwlovznFwa58kj6mw1NeGG5kBKeYqNXmJ_rpLs0=s96-c', 0),
(86, 'Zaaaaaaaaak', 'zakariabenwirane@gmail.com', '103984994944042248288', '$2y$10$xbK2fbM/wiZ7iClWH5L6Mevb2E3xjPl2cHNbeBH6f34.Dtlpp3hFC', 'inactif', 2, 'terra', 'uploads/avatars/6934ca8715039.png', 0),
(87, 'zizou', 'hdhdzzzgh@gmail.com', NULL, '$2y$10$OpGlgz14E0taQru23fz/e.Z5glx7hTJPNE4gV.XZKVJOMdB3UiOuW', 'actif', 2, 'terra', NULL, 0),
(88, 'hicchhhh', 'hdhdzzzghd@gmail.com', NULL, '$2y$10$LFtigtB3r5XGryEzCPaaveCBGkC0IvWjTaUBXvE.btsUxYaddyCgC', 'inactif', 2, 'terra', 'uploads/avatars/6935ff2986fe9.jpg', 0),
(89, 'Ahmed__1', 'ahmed@gmail.com', NULL, '$2y$10$zet7FBKntX09GMe2MxoiPuQcusEgS6OtxUNyOGKSGU379XZt8dw7C', 'actif', 2, 'terra', 'uploads/avatars/693a7c3521917.png', 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('front','back') NOT NULL DEFAULT 'front',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `is_active`, `created_at`) VALUES
(1, 'alice', 'alice@example.com', 'pass123', 'back', 1, '2025-11-17 12:21:45'),
(2, 'bob', 'bob@example.com', 'pass123', 'front', 1, '2025-11-17 12:21:45'),
(3, 'charlie', 'charlie@example.com', 'pass123', 'front', 1, '2025-11-17 12:21:45'),
(4, 'david', 'david@example.com', 'pass123', 'front', 1, '2025-11-17 12:21:45'),
(5, 'eve', 'eve@example.com', 'pass123', 'front', 1, '2025-11-17 12:21:45'),
(6, 'frank', 'frank@example.com', 'pass123', 'front', 1, '2025-11-17 12:21:45'),
(7, 'grace', 'grace@example.com', 'pass123', 'front', 1, '2025-11-17 12:21:45'),
(8, 'heidi', 'heidi@example.com', 'pass123', 'front', 1, '2025-11-17 12:21:45'),
(9, 'ivan', 'ivan@example.com', 'pass123', 'front', 1, '2025-11-17 12:21:45'),
(10, 'judy', 'judy@example.com', 'pass123', 'front', 1, '2025-11-17 12:21:45');

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
(216, 84, 'connexion', '2025-12-03 16:23:07'),
(217, 68, 'connexion', '2025-12-03 16:23:45'),
(218, 84, 'connexion', '2025-12-03 16:23:59'),
(219, 84, 'modification', '2025-12-03 16:24:09'),
(220, 68, 'connexion', '2025-12-03 16:24:24'),
(221, 84, 'connexion', '2025-12-03 16:24:35'),
(222, 84, 'connexion', '2025-12-03 16:27:22'),
(223, 84, 'connexion', '2025-12-03 16:27:55'),
(224, 84, 'connexion', '2025-12-03 16:28:00'),
(225, 68, 'connexion', '2025-12-03 16:28:25'),
(226, 68, 'connexion', '2025-12-03 16:31:30'),
(227, 68, 'connexion', '2025-12-03 16:32:22'),
(228, 68, 'connexion', '2025-12-03 16:32:40'),
(229, 68, 'connexion', '2025-12-03 17:17:34'),
(230, 68, 'connexion', '2025-12-05 11:44:01'),
(231, 70, 'connexion', '2025-12-05 11:45:23'),
(232, 70, 'connexion', '2025-12-05 12:17:38'),
(233, 70, 'connexion', '2025-12-05 12:19:13'),
(234, 86, 'connexion', '2025-12-05 12:36:19'),
(235, 86, 'connexion', '2025-12-05 12:36:32'),
(236, 86, 'modification mot de passe', '2025-12-05 12:36:50'),
(237, 86, 'connexion', '2025-12-05 12:37:22'),
(238, 86, 'connexion', '2025-12-05 12:39:47'),
(239, 86, 'modification mot de passe', '2025-12-05 12:40:24'),
(240, 86, 'connexion', '2025-12-05 12:40:46'),
(241, 86, 'connexion', '2025-12-05 12:45:16'),
(242, 86, 'modification mot de passe', '2025-12-05 12:45:44'),
(243, 86, 'connexion', '2025-12-05 12:45:51'),
(244, 86, 'connexion', '2025-12-05 12:55:31'),
(245, 70, 'connexion', '2025-12-05 13:58:09'),
(246, 68, 'connexion', '2025-12-05 13:59:22'),
(247, 68, 'connexion', '2025-12-05 14:00:55'),
(248, 68, 'connexion', '2025-12-05 14:01:12'),
(249, 68, 'connexion', '2025-12-05 14:01:38'),
(250, 70, 'connexion', '2025-12-05 14:01:44'),
(251, 70, 'modification', '2025-12-05 14:01:57'),
(252, 68, 'connexion', '2025-12-06 12:44:28'),
(253, 68, 'connexion', '2025-12-06 12:45:02'),
(254, 68, 'connexion', '2025-12-06 12:45:26'),
(255, 68, 'connexion', '2025-12-06 12:46:52'),
(256, 68, 'connexion', '2025-12-06 12:51:21'),
(257, 68, 'connexion', '2025-12-06 12:52:14'),
(258, 70, 'connexion', '2025-12-06 12:56:59'),
(259, 70, 'connexion', '2025-12-06 12:57:53'),
(260, 70, 'connexion', '2025-12-06 12:58:18'),
(261, 70, 'connexion', '2025-12-06 12:58:30'),
(262, 70, 'connexion', '2025-12-06 13:01:11'),
(263, 70, 'connexion', '2025-12-06 13:04:03'),
(264, 70, 'connexion', '2025-12-06 13:04:17'),
(265, 70, 'connexion', '2025-12-06 13:04:57'),
(266, 70, 'connexion', '2025-12-06 13:08:24'),
(267, 70, 'connexion', '2025-12-06 13:14:17'),
(268, 68, 'connexion', '2025-12-06 13:17:21'),
(269, 68, 'connexion', '2025-12-06 13:20:02'),
(270, 68, 'connexion', '2025-12-06 13:20:18'),
(271, 68, 'connexion', '2025-12-06 13:20:50'),
(272, 68, 'connexion', '2025-12-06 13:21:05'),
(273, 68, 'connexion', '2025-12-06 13:24:01'),
(274, 68, 'connexion', '2025-12-06 13:24:19'),
(275, 70, 'connexion', '2025-12-06 13:48:27'),
(276, 70, 'connexion', '2025-12-06 13:49:02'),
(277, 70, 'connexion', '2025-12-06 13:49:43'),
(278, 68, 'connexion', '2025-12-06 13:50:43'),
(279, 68, 'connexion', '2025-12-06 13:51:07'),
(280, 70, 'connexion', '2025-12-06 13:51:42'),
(281, 70, 'modification', '2025-12-06 13:51:47'),
(282, 70, 'connexion', '2025-12-06 13:53:58'),
(283, 70, 'connexion', '2025-12-06 13:56:57'),
(284, 70, 'connexion', '2025-12-06 13:57:30'),
(285, 70, 'connexion', '2025-12-06 14:00:26'),
(286, 70, 'connexion', '2025-12-06 14:01:17'),
(287, 70, 'connexion', '2025-12-06 14:01:34'),
(288, 70, 'connexion', '2025-12-06 14:18:13'),
(289, 70, 'connexion', '2025-12-06 14:20:02'),
(290, 70, 'connexion', '2025-12-06 14:22:27'),
(291, 70, 'connexion', '2025-12-06 14:22:54'),
(292, 68, 'connexion', '2025-12-06 14:23:16'),
(293, 68, 'connexion', '2025-12-06 14:28:21'),
(294, 68, 'connexion', '2025-12-06 14:31:09'),
(295, 68, 'connexion', '2025-12-06 14:31:49'),
(296, 68, 'connexion', '2025-12-06 14:32:09'),
(297, 68, 'connexion', '2025-12-06 14:34:08'),
(298, 68, 'connexion', '2025-12-06 14:35:26'),
(299, 70, 'connexion', '2025-12-06 14:37:36'),
(300, 70, 'connexion', '2025-12-06 14:46:32'),
(301, 70, 'connexion', '2025-12-06 15:18:34'),
(302, 70, 'connexion', '2025-12-07 00:53:34'),
(303, 70, 'connexion', '2025-12-07 00:54:04'),
(304, 68, 'connexion', '2025-12-07 00:54:32'),
(305, 68, 'connexion', '2025-12-07 00:55:12'),
(306, 70, 'connexion', '2025-12-07 00:56:09'),
(307, 70, 'modification', '2025-12-07 00:56:15'),
(308, 80, 'modification', '2025-12-07 00:57:05'),
(309, 68, 'connexion', '2025-12-07 00:59:01'),
(310, 68, 'connexion', '2025-12-07 00:59:30'),
(311, 70, 'connexion', '2025-12-07 01:00:17'),
(312, 70, 'connexion', '2025-12-07 01:00:39'),
(313, 68, 'connexion', '2025-12-07 01:02:49'),
(314, 68, 'connexion', '2025-12-07 01:03:56'),
(315, 70, 'connexion', '2025-12-07 01:08:00'),
(316, 70, 'connexion', '2025-12-07 01:08:32'),
(317, 68, 'connexion', '2025-12-07 01:09:28'),
(318, 68, 'connexion', '2025-12-07 01:11:38'),
(319, 68, 'connexion', '2025-12-07 01:12:41'),
(320, 70, 'connexion', '2025-12-07 01:13:57'),
(321, 70, 'modification', '2025-12-07 01:14:12'),
(322, 86, 'connexion', '2025-12-07 01:26:09'),
(323, 86, 'connexion', '2025-12-07 01:26:55'),
(324, 68, 'connexion', '2025-12-07 01:27:44'),
(325, 86, 'modification', '2025-12-07 01:29:59'),
(326, 68, 'connexion', '2025-12-07 10:56:14'),
(327, 87, 'connexion', '2025-12-07 12:12:18'),
(328, 70, 'connexion', '2025-12-07 12:19:05'),
(329, 70, 'connexion', '2025-12-07 12:39:40'),
(330, 70, 'connexion', '2025-12-07 12:43:27'),
(331, 70, 'connexion', '2025-12-07 12:47:02'),
(332, 70, 'connexion', '2025-12-07 12:48:17'),
(333, 70, 'connexion', '2025-12-07 12:55:38'),
(334, 70, 'connexion', '2025-12-07 12:58:58'),
(335, 70, 'connexion', '2025-12-07 12:59:21'),
(336, 70, 'connexion', '2025-12-07 12:59:36'),
(337, 88, 'connexion', '2025-12-07 23:19:20'),
(338, 88, 'connexion', '2025-12-07 23:22:53'),
(339, 68, 'connexion', '2025-12-07 23:23:43'),
(340, 88, 'connexion', '2025-12-07 23:26:42'),
(341, 88, 'modification', '2025-12-07 23:26:49'),
(342, 88, 'connexion', '2025-12-07 23:44:41'),
(343, 68, 'connexion', '2025-12-08 12:15:23'),
(344, 68, 'connexion', '2025-12-08 12:40:01'),
(345, 68, 'connexion', '2025-12-08 13:05:08'),
(346, 68, 'connexion', '2025-12-08 13:06:21'),
(347, 70, 'connexion', '2025-12-08 13:07:55'),
(348, 68, 'connexion', '2025-12-08 13:25:52'),
(349, 70, 'connexion', '2025-12-08 13:34:01'),
(350, 70, 'connexion', '2025-12-08 13:41:17'),
(351, 68, 'connexion', '2025-12-08 13:43:31'),
(352, 70, 'connexion', '2025-12-08 14:29:06'),
(353, 70, 'connexion', '2025-12-08 14:30:35'),
(354, 68, 'connexion', '2025-12-08 14:37:50'),
(355, 88, 'connexion', '2025-12-08 14:38:27'),
(356, 70, 'connexion', '2025-12-08 14:54:55'),
(357, 80, 'connexion', '2025-12-08 14:57:48'),
(358, 68, 'connexion', '2025-12-08 14:58:07'),
(359, 68, 'connexion', '2025-12-08 15:00:22'),
(360, 70, 'connexion', '2025-12-08 15:00:40'),
(361, 68, 'connexion', '2025-12-09 17:15:06'),
(362, 68, 'connexion', '2025-12-09 17:15:07'),
(363, 70, 'connexion', '2025-12-10 13:00:43'),
(364, 70, 'connexion', '2025-12-10 13:32:01'),
(365, 70, 'connexion', '2025-12-10 13:50:46'),
(366, 70, 'modification', '2025-12-10 14:14:35'),
(367, 70, 'connexion', '2025-12-11 08:22:07'),
(368, 70, 'connexion', '2025-12-11 08:23:07'),
(369, 70, 'connexion', '2025-12-11 08:43:39'),
(370, 89, 'connexion', '2025-12-11 08:47:14'),
(371, 89, 'modification', '2025-12-11 08:47:26'),
(372, 89, 'connexion', '2025-12-11 08:47:35'),
(373, 89, 'connexion', '2025-12-11 08:54:59'),
(374, 89, 'connexion', '2025-12-11 08:57:34'),
(375, 89, 'modification', '2025-12-11 09:03:53'),
(376, 89, 'modification', '2025-12-11 09:09:25'),
(377, 89, 'connexion', '2025-12-11 09:14:59'),
(378, 89, 'connexion', '2025-12-11 13:03:58'),
(379, 89, 'connexion', '2025-12-11 13:03:59'),
(380, 89, 'connexion', '2025-12-11 13:03:59'),
(381, 89, 'connexion', '2025-12-11 13:04:01'),
(382, 89, 'connexion', '2025-12-11 13:04:31'),
(383, 89, 'connexion', '2025-12-11 13:06:31'),
(384, 89, 'connexion', '2025-12-11 13:12:22'),
(385, 89, 'connexion', '2025-12-11 13:12:23'),
(386, 89, 'connexion', '2025-12-11 13:18:32'),
(387, 89, 'connexion', '2025-12-11 13:18:32'),
(388, 89, 'connexion', '2025-12-11 13:18:32'),
(389, 89, 'connexion', '2025-12-11 13:18:33'),
(390, 89, 'connexion', '2025-12-11 13:18:33'),
(391, 89, 'connexion', '2025-12-11 13:18:34'),
(392, 89, 'connexion', '2025-12-11 13:18:34'),
(393, 89, 'connexion', '2025-12-11 13:23:31'),
(394, 89, 'connexion', '2025-12-11 13:23:32'),
(395, 89, 'connexion', '2025-12-11 13:24:49'),
(396, 89, 'connexion', '2025-12-11 13:30:52');

-- --------------------------------------------------------

--
-- Table structure for table `user_badges`
--

CREATE TABLE `user_badges` (
  `user_id` int(11) NOT NULL,
  `badge_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_badges`
--

INSERT INTO `user_badges` (`user_id`, `badge_id`) VALUES
(70, 1),
(70, 2),
(70, 3),
(89, 1),
(89, 2);

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
-- Indexes for table `badges`
--
ALTER TABLE `badges`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `email_verification`
--
ALTER TABLE `email_verification`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_activity`
--
ALTER TABLE `user_activity`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_badges`
--
ALTER TABLE `user_badges`
  ADD PRIMARY KEY (`user_id`,`badge_id`),
  ADD KEY `badge_id` (`badge_id`);

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
-- AUTO_INCREMENT for table `badges`
--
ALTER TABLE `badges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
-- AUTO_INCREMENT for table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `email_verification`
--
ALTER TABLE `email_verification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `user_activity`
--
ALTER TABLE `user_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=397;

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
-- Constraints for table `conversation_users`
--
ALTER TABLE `conversation_users`
  ADD CONSTRAINT `fk_conv_user_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conv_user_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

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
  ADD CONSTRAINT `fk_messages_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  ADD CONSTRAINT `pieces_jointes_ibfk_1` FOREIGN KEY (`innovation_id`) REFERENCES `innovations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_badges`
--
ALTER TABLE `user_badges`
  ADD CONSTRAINT `user_badges_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_badges_ibfk_2` FOREIGN KEY (`badge_id`) REFERENCES `badges` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`innovation_id`) REFERENCES `innovations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
