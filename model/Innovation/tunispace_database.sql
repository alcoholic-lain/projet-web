-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : sam. 29 nov. 2025 à 20:34
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `tunispace_database`
--

-- --------------------------------------------------------

--
-- Structure de la table `attachments`
--

CREATE TABLE `attachments` (
  `id` int(11) NOT NULL,
  `comment_id` int(11) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date_creation` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`, `description`, `date_creation`) VALUES
(1, 'Exploration Spatiale', 'Projets liés à l\'exploration de l\'espace et des planètes', '2025-11-17 00:11:40'),
(2, 'Énergie Orbitale', 'Solutions énergétiques pour l\'espace et les satellites', '2025-11-17 00:11:40'),
(3, 'Habitats Lunaires', 'Conception et développement d\'habitats pour la Lune', '2025-11-17 00:11:40'),
(5, 'Propulsion Avancée', 'Nouvelles technologies de propulsion spatiale', '2025-11-17 00:11:40'),
(28, 'TEST', 'TEST', '2025-11-29 20:25:00');

-- --------------------------------------------------------

--
-- Structure de la table `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `innovation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `comments`
--

INSERT INTO `comments` (`id`, `innovation_id`, `user_id`, `content`, `created_at`) VALUES
(1, 43, 66, 'aaaa', '2025-11-25 00:42:31'),
(2, 43, 66, 'qqqqq', '2025-11-25 00:42:35'),
(3, 43, 66, 'asdsfghvjbkl', '2025-11-25 00:43:09'),
(4, 43, 66, 'bravo', '2025-11-25 00:43:16'),
(21, 54, 71, 'test', '2025-11-28 10:14:43'),
(22, 54, 76, 'good', '2025-11-28 23:58:24'),
(23, 54, 76, 'k.kkk;k.', '2025-11-29 00:09:40'),
(24, 43, 76, 'aa', '2025-11-29 14:35:49');

-- --------------------------------------------------------

--
-- Structure de la table `confirmations_email`
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
-- Structure de la table `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `is_group` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `conversations`
--

INSERT INTO `conversations` (`id`, `title`, `is_group`, `created_at`) VALUES
(1, 'Alice & Bob', 0, '2025-11-17 12:18:18'),
(2, 'Alice & Charlie', 0, '2025-11-17 12:18:18'),
(3, 'Alice & David', 0, '2025-11-17 12:18:18'),
(4, 'Alice & Eve', 0, '2025-11-17 12:18:18'),
(5, 'Bob & Charlie', 0, '2025-11-17 12:18:18'),
(6, 'Bob & David', 0, '2025-11-17 12:18:18'),
(7, 'Bob & Eve', 0, '2025-11-17 12:18:18'),
(8, 'Charlie & David', 0, '2025-11-17 12:18:18'),
(9, 'Charlie & Eve', 0, '2025-11-17 12:18:18'),
(10, 'David & Eve', 0, '2025-11-17 12:18:18'),
(11, 'Study Group A', 1, '2025-11-17 12:18:18'),
(12, 'Study Group B', 1, '2025-11-17 12:18:18'),
(13, 'Gaming Squad', 1, '2025-11-17 12:18:18'),
(14, 'Project Team 1', 1, '2025-11-17 12:18:18'),
(15, 'Project Team 2', 1, '2025-11-17 12:18:18'),
(16, 'Family Chat 1', 1, '2025-11-17 12:18:18'),
(17, 'Family Chat 2', 1, '2025-11-17 12:18:18'),
(18, 'Friends Forever', 1, '2025-11-17 12:18:18'),
(19, 'Weekend Plans', 1, '2025-11-17 12:18:18'),
(20, 'Music Lovers', 1, '2025-11-17 12:18:18'),
(21, 'Anime Club', 1, '2025-11-17 12:18:18'),
(22, 'Work Buddies', 1, '2025-11-17 12:18:18'),
(23, 'Esprit G2', 1, '2025-11-17 12:18:18'),
(24, 'Dev Chat', 1, '2025-11-17 12:18:18'),
(25, 'Random', 1, '2025-11-17 12:18:18'),
(26, 'Lab Group', 1, '2025-11-17 12:18:18'),
(27, 'Dorm Chat', 1, '2025-11-17 12:18:18'),
(28, 'Gym Bros', 1, '2025-11-17 12:18:18'),
(29, 'Movie Night', 1, '2025-11-17 12:18:18'),
(30, 'CS Project', 1, '2025-11-17 12:18:18');

-- --------------------------------------------------------

--
-- Structure de la table `conversation_users`
--

CREATE TABLE `conversation_users` (
  `conversation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `conversation_users`
--

INSERT INTO `conversation_users` (`conversation_id`, `user_id`, `is_admin`, `joined_at`) VALUES
(1, 1, 1, '2025-11-17 12:18:18'),
(1, 2, 0, '2025-11-17 12:18:18'),
(2, 1, 1, '2025-11-17 12:18:18'),
(2, 3, 0, '2025-11-17 12:18:18'),
(3, 1, 1, '2025-11-17 12:18:18'),
(3, 4, 0, '2025-11-17 12:18:18'),
(4, 1, 1, '2025-11-17 12:18:18'),
(4, 5, 0, '2025-11-17 12:18:18'),
(5, 2, 1, '2025-11-17 12:18:18'),
(5, 3, 0, '2025-11-17 12:18:18'),
(6, 2, 1, '2025-11-17 12:18:18'),
(6, 4, 0, '2025-11-17 12:18:18'),
(7, 2, 1, '2025-11-17 12:18:18'),
(7, 5, 0, '2025-11-17 12:18:18'),
(8, 3, 1, '2025-11-17 12:18:18'),
(8, 4, 0, '2025-11-17 12:18:18'),
(9, 3, 1, '2025-11-17 12:18:18'),
(9, 5, 0, '2025-11-17 12:18:18'),
(10, 4, 1, '2025-11-17 12:18:18'),
(10, 5, 0, '2025-11-17 12:18:18'),
(11, 1, 1, '2025-11-17 12:18:18'),
(11, 2, 0, '2025-11-17 12:18:18'),
(11, 3, 0, '2025-11-17 12:18:18'),
(12, 4, 1, '2025-11-17 12:18:18'),
(12, 5, 0, '2025-11-17 12:18:18'),
(12, 6, 0, '2025-11-17 12:18:18'),
(13, 2, 1, '2025-11-17 12:18:18'),
(13, 3, 0, '2025-11-17 12:18:18'),
(13, 4, 0, '2025-11-17 12:18:18'),
(13, 5, 0, '2025-11-17 12:18:18'),
(14, 1, 1, '2025-11-17 12:18:18'),
(14, 4, 0, '2025-11-17 12:18:18'),
(14, 7, 0, '2025-11-17 12:18:18'),
(14, 8, 0, '2025-11-17 12:18:18'),
(15, 2, 1, '2025-11-17 12:18:18'),
(15, 5, 0, '2025-11-17 12:18:18'),
(15, 8, 0, '2025-11-17 12:18:18'),
(15, 9, 0, '2025-11-17 12:18:18'),
(16, 1, 1, '2025-11-17 12:18:18'),
(16, 6, 0, '2025-11-17 12:18:18'),
(16, 7, 0, '2025-11-17 12:18:18'),
(17, 3, 1, '2025-11-17 12:18:18'),
(17, 8, 0, '2025-11-17 12:18:18'),
(17, 9, 0, '2025-11-17 12:18:18'),
(18, 4, 1, '2025-11-17 12:18:18'),
(18, 5, 0, '2025-11-17 12:18:18'),
(18, 6, 0, '2025-11-17 12:18:18'),
(18, 7, 0, '2025-11-17 12:18:18'),
(19, 2, 1, '2025-11-17 12:18:18'),
(19, 3, 0, '2025-11-17 12:18:18'),
(19, 8, 0, '2025-11-17 12:18:18'),
(19, 10, 0, '2025-11-17 12:18:18'),
(20, 1, 1, '2025-11-17 12:18:18'),
(20, 3, 0, '2025-11-17 12:18:18'),
(20, 5, 0, '2025-11-17 12:18:18'),
(20, 7, 0, '2025-11-17 12:18:18'),
(20, 9, 0, '2025-11-17 12:18:18'),
(21, 1, 1, '2025-11-17 12:18:18'),
(21, 8, 0, '2025-11-17 12:18:18'),
(21, 9, 0, '2025-11-17 12:18:18'),
(21, 10, 0, '2025-11-17 12:18:18'),
(22, 2, 1, '2025-11-17 12:18:18'),
(22, 4, 0, '2025-11-17 12:18:18'),
(22, 6, 0, '2025-11-17 12:18:18'),
(22, 8, 0, '2025-11-17 12:18:18'),
(23, 3, 1, '2025-11-17 12:18:18'),
(23, 5, 0, '2025-11-17 12:18:18'),
(23, 7, 0, '2025-11-17 12:18:18'),
(23, 9, 0, '2025-11-17 12:18:18'),
(24, 1, 1, '2025-11-17 12:18:18'),
(24, 2, 0, '2025-11-17 12:18:18'),
(24, 3, 0, '2025-11-17 12:18:18'),
(24, 4, 0, '2025-11-17 12:18:18'),
(24, 5, 0, '2025-11-17 12:18:18'),
(25, 6, 1, '2025-11-17 12:18:18'),
(25, 7, 0, '2025-11-17 12:18:18'),
(25, 8, 0, '2025-11-17 12:18:18'),
(25, 9, 0, '2025-11-17 12:18:18'),
(25, 10, 0, '2025-11-17 12:18:18'),
(26, 1, 1, '2025-11-17 12:18:18'),
(26, 6, 0, '2025-11-17 12:18:18'),
(26, 9, 0, '2025-11-17 12:18:18'),
(27, 2, 1, '2025-11-17 12:18:18'),
(27, 7, 0, '2025-11-17 12:18:18'),
(27, 10, 0, '2025-11-17 12:18:18'),
(28, 4, 1, '2025-11-17 12:18:18'),
(28, 6, 0, '2025-11-17 12:18:18'),
(28, 10, 0, '2025-11-17 12:18:18'),
(29, 3, 1, '2025-11-17 12:18:18'),
(29, 5, 0, '2025-11-17 12:18:18'),
(29, 8, 0, '2025-11-17 12:18:18'),
(30, 1, 1, '2025-11-17 12:18:18'),
(30, 3, 0, '2025-11-17 12:18:18'),
(30, 6, 0, '2025-11-17 12:18:18'),
(30, 10, 0, '2025-11-17 12:18:18');

-- --------------------------------------------------------

--
-- Structure de la table `innovations`
--

CREATE TABLE `innovations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `titre` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `date_creation` datetime NOT NULL,
  `statut` varchar(50) DEFAULT 'En attente',
  `category_id` int(11) NOT NULL,
  `file` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `innovations`
--

INSERT INTO `innovations` (`id`, `user_id`, `titre`, `description`, `date_creation`, `statut`, `category_id`, `file`) VALUES
(42, 66, 'ddddd', 'asdfghjkl', '2025-11-24 14:54:02', 'Validée', 3, NULL),
(43, 66, '12345678', '12345678123456781234567812345678123456781234567812345678123456781234567812345678123456781234567812345678', '2025-11-24 23:48:38', 'Validée', 1, NULL),
(44, 66, 'abdefghi', 'abdefghiabdefghiabdefghiabdefghiabdefghiabdefghiabdefghiabdefghiabdefghiabdefghiabdefghiabdefghiabdefghiabdefghiabdefghiabdefghi', '2025-11-24 23:48:59', 'Rejetée', 1, NULL),
(54, 71, 'Hichem_test', 'TEST_TEST', '2025-11-28 11:12:38', 'Validée', 2, NULL),
(55, 76, 'testtest2', 'sdfghjkd', '2025-11-29 01:01:47', 'En attente', 2, NULL),
(56, 76, 'ásdfghjk', 'asdfghjkl;', '2025-11-29 01:10:51', 'Validée', 1, NULL),
(60, 76, 'TEST1 modify modify', 'testest', '2025-11-29 14:55:12', 'Validée', 1, 'view/Client/Innovation/uploads/innovation_692afb40e88916.69750043.pdf');

-- --------------------------------------------------------

--
-- Doublure de structure pour la vue `innovation_with_category`
-- (Voir ci-dessous la vue réelle)
--
CREATE TABLE `innovation_with_category` (
`id` int(11)
,`titre` varchar(200)
,`description` text
,`date_creation` datetime
,`statut` varchar(50)
,`categorie_nom` varchar(100)
);

-- --------------------------------------------------------

--
-- Structure de la table `logs_connexions`
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
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `user_id`, `content`, `created_at`) VALUES
(1, 1, 1, 'Hi Bob, this is Alice in conversation 1', '2025-11-17 12:18:18'),
(2, 1, 2, 'Hey Alice! Nice to chat.', '2025-11-17 12:18:18'),
(3, 2, 1, 'Hi Charlie, Alice here in convo 2', '2025-11-17 12:18:18'),
(4, 2, 3, 'Hi Alice!', '2025-11-17 12:18:18'),
(5, 3, 1, 'Hi David, we need to talk about project.', '2025-11-17 12:18:18'),
(6, 3, 4, 'Sure, tell me.', '2025-11-17 12:18:18'),
(7, 4, 1, 'Hi Eve, how are you?', '2025-11-17 12:18:18'),
(8, 4, 5, 'I am good, thanks Alice.', '2025-11-17 12:18:18'),
(9, 5, 2, 'Yo Charlie, it\'s Bob', '2025-11-17 12:18:18'),
(10, 5, 3, 'Hey Bob!', '2025-11-17 12:18:18'),
(11, 6, 2, 'David, are you coming later?', '2025-11-17 12:18:18'),
(12, 6, 4, 'Yes, I will.', '2025-11-17 12:18:18'),
(13, 7, 2, 'Eve, did you see this?', '2025-11-17 12:18:18'),
(14, 7, 5, 'Not yet, send it.', '2025-11-17 12:18:18'),
(15, 8, 3, 'David, ready for the exam?', '2025-11-17 12:18:18'),
(16, 8, 4, 'Almost, still revising.', '2025-11-17 12:18:18'),
(17, 9, 3, 'Eve, group homework?', '2025-11-17 12:18:18'),
(18, 9, 5, 'Working on it.', '2025-11-17 12:18:18'),
(19, 10, 4, 'Eve, let\'s meet after class.', '2025-11-17 12:18:18'),
(20, 10, 5, 'Ok David.', '2025-11-17 12:18:18'),
(21, 11, 1, 'Welcome to Study Group A!', '2025-11-17 12:18:18'),
(22, 11, 2, 'Thanks for the invite.', '2025-11-17 12:18:18'),
(23, 11, 3, 'Let\'s ace this.', '2025-11-17 12:18:18'),
(24, 12, 4, 'Study Group B starting.', '2025-11-17 12:18:18'),
(25, 12, 5, 'Hi everyone.', '2025-11-17 12:18:18'),
(26, 12, 6, 'Hello!', '2025-11-17 12:18:18'),
(27, 13, 2, 'Gaming squad, tonight?', '2025-11-17 12:18:18'),
(28, 13, 3, 'I\'m in.', '2025-11-17 12:18:18'),
(29, 13, 4, 'Me too.', '2025-11-17 12:18:18'),
(30, 14, 1, 'Project Team 1 kickoff.', '2025-11-17 12:18:18'),
(31, 14, 4, 'Let\'s go.', '2025-11-17 12:18:18'),
(32, 14, 7, 'I\'m here.', '2025-11-17 12:18:18'),
(33, 14, 8, 'Hi all.', '2025-11-17 12:18:18'),
(34, 15, 2, 'Project Team 2 meeting.', '2025-11-17 12:18:18'),
(35, 15, 5, 'Ok.', '2025-11-17 12:18:18'),
(36, 15, 8, 'Got it.', '2025-11-17 12:18:18'),
(37, 15, 9, 'Cool.', '2025-11-17 12:18:18'),
(38, 16, 1, 'Family Chat 1, hello!', '2025-11-17 12:18:18'),
(39, 16, 6, 'Hi there.', '2025-11-17 12:18:18'),
(40, 16, 7, 'Hey!', '2025-11-17 12:18:18'),
(41, 17, 3, 'Family Chat 2 checking in.', '2025-11-17 12:18:18'),
(42, 17, 8, 'Hi!', '2025-11-17 12:18:18'),
(43, 17, 9, 'Hello.', '2025-11-17 12:18:18'),
(44, 18, 4, 'Friends Forever ❤️', '2025-11-17 12:18:18'),
(45, 18, 5, 'Always.', '2025-11-17 12:18:18'),
(46, 18, 6, 'For sure.', '2025-11-17 12:18:18'),
(47, 18, 7, 'Yesss.', '2025-11-17 12:18:18'),
(48, 19, 2, 'Weekend plans?', '2025-11-17 12:18:18'),
(49, 19, 3, 'Let\'s go out.', '2025-11-17 12:18:18'),
(50, 19, 8, 'I\'m in.', '2025-11-17 12:18:18'),
(51, 19, 10, 'Same.', '2025-11-17 12:18:18'),
(52, 20, 1, 'Music Lovers, new playlist.', '2025-11-17 12:18:18'),
(53, 20, 3, 'Drop the link.', '2025-11-17 12:18:18'),
(54, 20, 5, 'Nice tracks.', '2025-11-17 12:18:18'),
(55, 20, 7, 'Love it.', '2025-11-17 12:18:18'),
(56, 20, 9, 'Fire.', '2025-11-17 12:18:18'),
(57, 21, 1, 'Anime Club tonight.', '2025-11-17 12:18:18'),
(58, 21, 8, 'Can\'t wait.', '2025-11-17 12:18:18'),
(59, 21, 9, 'Same here.', '2025-11-17 12:18:18'),
(60, 21, 10, 'Let\'s go.', '2025-11-17 12:18:18'),
(61, 22, 2, 'Work buddies, standup in 10.', '2025-11-17 12:18:18'),
(62, 22, 4, 'Ok.', '2025-11-17 12:18:18'),
(63, 22, 6, 'On my way.', '2025-11-17 12:18:18'),
(64, 22, 8, 'Got it.', '2025-11-17 12:18:18'),
(65, 23, 3, 'Esprit G2 lecture starts now.', '2025-11-17 12:18:18'),
(66, 23, 5, 'Thanks.', '2025-11-17 12:18:18'),
(67, 23, 7, 'Here.', '2025-11-17 12:18:18'),
(68, 23, 9, 'Joining.', '2025-11-17 12:18:18'),
(69, 24, 1, 'Dev Chat: merge conflict again.', '2025-11-17 12:18:18'),
(70, 24, 2, 'haha.', '2025-11-17 12:18:18'),
(71, 24, 3, 'classic.', '2025-11-17 12:18:18'),
(72, 24, 4, 'lol.', '2025-11-17 12:18:18'),
(73, 24, 5, 'We fix it.', '2025-11-17 12:18:18'),
(74, 25, 6, 'Random chat time.', '2025-11-17 12:18:18'),
(75, 25, 7, 'What\'s up.', '2025-11-17 12:18:18'),
(76, 25, 8, 'All good.', '2025-11-17 12:18:18'),
(77, 25, 9, 'Same.', '2025-11-17 12:18:18'),
(78, 25, 10, 'Nice.', '2025-11-17 12:18:18'),
(79, 26, 1, 'Lab Group meeting at 2 PM.', '2025-11-17 12:18:18'),
(80, 26, 6, 'Ok.', '2025-11-17 12:18:18'),
(81, 26, 9, 'Fine by me.', '2025-11-17 12:18:18'),
(82, 27, 2, 'Dorm Chat noise complaints.', '2025-11-17 12:18:18'),
(83, 27, 7, 'Oops.', '2025-11-17 12:18:18'),
(84, 27, 10, 'Sorry.', '2025-11-17 12:18:18'),
(85, 28, 4, 'Gym Bros, leg day.', '2025-11-17 12:18:18'),
(86, 28, 6, 'Nooo.', '2025-11-17 12:18:18'),
(87, 28, 10, 'Let\'s do it.', '2025-11-17 12:18:18'),
(88, 29, 3, 'Movie Night picks?', '2025-11-17 12:18:18'),
(89, 29, 5, 'Action.', '2025-11-17 12:18:18'),
(90, 29, 8, 'Comedy.', '2025-11-17 12:18:18'),
(91, 30, 1, 'CS Project deadline soon.', '2025-11-17 12:18:18'),
(92, 30, 3, 'We\'re close.', '2025-11-17 12:18:18'),
(93, 30, 6, 'Need more coffee.', '2025-11-17 12:18:18'),
(94, 30, 10, 'Same 😂', '2025-11-17 12:18:18'),
(95, 30, 1, 'uyyyug', '2025-11-24 13:03:03');

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `password_resets`
--

INSERT INTO `password_resets` (`email`, `token`, `created_at`) VALUES
('zak.thea@gmail.com', '320fba26a14c665501aec0f1ace32486d677d74c75c7193536842c0cc2cca44b', '2025-11-14 14:01:29'),
('zak.thea@gmail.com', '2abc6a3dd9f0fec0c52cb88ab6ed4aa6d64f2c00636f345c0d9c7458fd2aac51', '2025-11-14 14:12:24'),
('zak.thea@gmail.com', 'ed861bfc76277131d900f855f7cf24b31f6db4885e7f75b5de8f14ac35d17a74', '2025-11-14 14:12:25'),
('zak.thea@gmail.com', '8ec4e93029ee189170af50380b2bded5cd485d878536e148462230af5d17917b', '2025-11-14 14:12:25'),
('zak.thea@gmail.com', '59b4b80da38f1a8bdb8db19d267328073bb229c382e67cd43d12d9be1c864640', '2025-11-14 14:12:58'),
('zak.thea@gmail.com', '709c9b096099e43302ecea5e9ffd6b369b2df7027a44b81d09d8e668b25616dc', '2025-11-14 14:12:59'),
('zak.thea@gmail.com', '7cff45e671454cecae44d0d4fce99c381b3e887b691ebeeb9331a6ba9e5204c9', '2025-11-14 14:12:59'),
('zak.thea@gmail.com', 'da3fc004c04d320ab8e96cba8f4ec7ebb4b86632ba90603f8c2bdc74a24b881f', '2025-11-14 14:13:00'),
('zak.thea@gmail.com', '58900a40d6541cd55800989965d0eadbe6e108b0baa117fe1a0913a6a79a39aa', '2025-11-14 14:13:00'),
('zak.thea@gmail.com', '9e432007abb8d6c0e639cdff16b05515a399a96834e4e5eb6c29c4cc86295fb8', '2025-11-14 14:13:29'),
('zak.thea@gmail.com', '66c0acafe1dbbfd2d16fbcbeaac8cd7b5484e581bd275c3d3db0dd29e90ba773', '2025-11-14 14:13:30'),
('zak.thea@gmail.com', '3324a87ec9e970df710d0d9082ca429cdddab35047efdc6c12d17565fe22399b', '2025-11-14 14:13:30'),
('zak.thea@gmail.com', '01d8e9caec5a8bfbfd0abcd80f9603e92387a3224155e140173c3bea2df7a7e7', '2025-11-14 14:15:54'),
('zak.thea@gmail.com', '2156e4988d4c54111e40d0b744a05b95edbc35d5263bf6fdd0a60123eca1404c', '2025-11-14 14:15:55'),
('zak.thea@gmail.com', 'ac447de95df0d56992a83e2f0c746f9a386d38bb1ba61cb64e9e8317e524fecb', '2025-11-14 14:16:14'),
('zak.thea@gmail.com', '799fab2641f15018eb2603e892a4f820d9affe4de9b330f46a8f21c96b188ce8', '2025-11-14 14:16:15'),
('zak.thea@gmail.com', '0f950f6ecee5864f9d62f6516e2083ba9f5fa1c7dde06057a4f1af497722b61e', '2025-11-14 14:16:15'),
('zak.thea@gmail.com', '1f7bc55654a342c7dfdb2b9da88bc5dd8e6886b4aeae74bdb5b9c1b9d91462b0', '2025-11-14 14:17:27'),
('zak.thea@gmail.com', 'c362c6c6e0b91e6dab663fe85f39e6de8153304b9c7ef6635c16863d078129d2', '2025-11-14 14:19:02'),
('zak.thea@gmail.com', 'ba950752a80105c877dd6676b898c9764f5317382e1214f805698c075700c7e2', '2025-11-14 14:19:02'),
('zak.thea@gmail.com', 'a7a35dda7601c4f25ede8c3e92ef86c8e23a3e38b77d23b0f4f6243ce24b427d', '2025-11-14 14:19:03'),
('zak.thea@gmail.com', 'd463cdfdb385171a687629aa9b6da9b949b5f2258148bd112955061a224d73e1', '2025-11-14 14:19:03'),
('zak.thea@gmail.com', 'bcaa25ddfea273804ff9e5c4efc6acdb6897aa52ddf241ed5a5a95d4a74e8b0a', '2025-11-14 14:19:03'),
('zak.thea@gmail.com', '8a359c24613601762ad9b0032fcafb27486a81ab2dedfa1d4ddf1ee95520499d', '2025-11-14 14:19:03'),
('zak.thea@gmail.com', '36d3f36eebc4a34d6c43d313623a6fba91fa8d20bcd41f6cf3907bf64791af00', '2025-11-14 14:19:03'),
('zak.thea@gmail.com', 'e1dac904192db3bc5271d152c994fdb793bbedc8cec19fa7fb243fbffccc52d0', '2025-11-14 14:23:05'),
('zak.thea@gmail.com', '2f0fab1b48bc54c283c1ea44ff49fc2cb4a55385d36ab67ba0eca0bed695f0a5', '2025-11-14 14:24:06'),
('zak.thea@gmail.com', 'd99dbf38b8dfdccb5c27ed647265002c6518d60278abdef5658890260d95ba53', '2025-11-14 14:24:19'),
('zak.thea@gmail.com', '40193b89e2167f2f3ea9f58f3d6ef300dcacb0c9ea037eb73208fb6bdd44f563', '2025-11-14 14:24:32'),
('zakaria.benouirsane@esprit.tn', 'aab4e4f901f8fb7189f04ff5e83dd68ee1502038a3536e6e092c13f4084f1749', '2025-11-14 14:24:46'),
('zak.thea@gmail.com', '2e43ebe675f83ac0ba0b74542732f3efad5e32c153423480b64d527af4b76466', '2025-11-14 14:25:07'),
('zak.thea@gmail.com', '1f30d2e13b97f260f286982631f7700c21887b2f14e076b8c5492226c9a08a9d', '2025-11-14 14:26:42'),
('zak.thea@gmail.com', '5538105207dd9faf2adca05fcc6bdc8dfb55e501938071470c5e830ca3427420', '2025-11-14 14:28:43'),
('zak.thea@gmail.com', '1f13411942a0ff2b416baf9d143a064c6dde755f53fe64214e3d7fde14c453be', '2025-11-14 14:29:11'),
('zak.thea@gmail.com', '3c291cf52e57d1c33d46a6168392352c0768c4c2d270512a84d202ec59c06014', '2025-11-14 14:30:49'),
('zak.thea@gmail.com', 'ed7e60954d9e920f0717bd3ac7200c21fd0d2c8f32b8437d05c21850d3700786', '2025-11-14 14:36:43'),
('zak.thea@gmail.com', 'b710c98d072dcd951d787c4f5509cc41489278f852ce3a08134ff1ea17c3dc81', '2025-11-14 14:37:57'),
('zak.thea@gmail.com', '7435bc17ab0dfcdbb733c0f2522034627d39ea76758aa7f5cd9bb87b65c2a9a0', '2025-11-14 14:40:18'),
('zak.thea@gmail.com', '8bbfee983d1f16841727b2a961729f1f05e87f55d2fb6e750582e3b0fc9cc72f', '2025-11-14 14:41:11'),
('zak.thea@gmail.com', '8e0cdcb1be40895330c3ae953068feed719b83e7a92b796625a97808b45b6b51', '2025-11-14 14:44:12'),
('zak.thea@gmail.com', '6d06bd0b74ae680124e2acb9bef113bf4d0f565db359ac3a28ea508831da07bd', '2025-11-14 14:51:16'),
('zak.thea@gmail.com', '2c1893b0d435c70737f84767cf2d6ef08d2ee7fb8d50c6c1f77710c43bae5ef7', '2025-11-14 14:53:52'),
('zak.thea@gmail.com', '22b3d46942bd8bb6002cf6a69fa42044cdeaf4a42fcbbc08ee7127439a39ecd9', '2025-11-14 14:54:32'),
('zak.thea@gmail.com', '48053aaf8ae41d6bcdc4fab126a837424981316c924e59da8f26d4d6a381424e', '2025-11-14 14:54:44'),
('zak.thea@gmail.com', 'ec2c54fd86ca56d483b4248250011d8dc69eab75d0119df15767222a966783f7', '2025-11-14 14:55:40'),
('zak.thea@gmail.com', '2a7a6803a4315bd5c921427d8d0febe04171a5e4eafd02dff254e5b4b1faa7b8', '2025-11-14 14:56:39'),
('zak.thea@gmail.com', 'cdb689b4c37af666b5b549c34e1f1632a78960e0bcc7d2a8849a0597d7d24226', '2025-11-14 14:57:19'),
('zak.thea@gmail.com', 'e558aa8338549f256623ee5eb506909b630c99540d3c392037ea74096715febd', '2025-11-14 14:58:29'),
('zak.thea@gmail.com', '8c67294be72a9e7a3c440d99cedee0ac957ad1468ed327742cc3551cd31d5ee2', '2025-11-14 15:01:21'),
('zak.thea@gmail.com', '2917df6f8294e1494703633ff077409ace7dd01cc2a0dfa7fa6412f7c2374981', '2025-11-14 15:06:00'),
('zak.thea@gmail.com', '4a1da9c055853e551e2404da429a8367da2846ede7980e7dd31b5c42db6be207', '2025-11-14 15:07:13'),
('zak.thea@gmail.com', 'e4eb204ee13096c42feeac7b6a25d20021c4fff88694bbfd22791ca949d8f5d4', '2025-11-14 15:13:25'),
('zak.thea@gmail.com', '8ba667e0782d858cd081c11091c89820d96bf3793754e6aa070e9ea157fb582a', '2025-11-15 01:49:05'),
('nournour2862005@gmail.com', '7f839fd7c8da3657fc485e8667883d4581b976725f5c321cc065111fe0bf98e1', '2025-11-15 23:48:47'),
('zak.thea@gmail.com', 'dbb6f331682d5b6cc57bebe2b1cc56805cdbddd2bd460a5a703211e743acd40c', '2025-11-16 00:02:01'),
('zak.thea@gmail.com', 'ea7a9b4485eba4e62f183c02e742a30c7c684560214a5271ee3927b035b561db', '2025-11-16 00:03:17'),
('nournour2862005@gmail.com', 'b02bc2dc5903e6c2d2b38b51ecc5be0edec5ba44f8460bcb08607a513fc08e0d', '2025-11-16 00:03:52'),
('zak.thea@gmail.com', '8083e2b6a95b2a8ec1718226f9d9ea2b2d08c5ba97cf8a06f879da8b822cc9a0', '2025-11-16 00:04:26'),
('nournour2862005@gmail.com', 'a8a5f3bfc63af5870a5af940f92c3d16a4c7f5494eb97ddcebdad6f344b75309', '2025-11-17 00:19:32'),
('nournour2862005@gmail.com', 'f77ac0ed42f606e0ffcc345b46339a6d3290c1bf209535b75caebbe5f9f766cd', '2025-11-17 12:59:30'),
('nournour2862005@gmail.com', '187d2a8d075907032d361425a17f16b85c8c91b40ce899c6c9a2e13cbada4177', '2025-11-17 14:45:29'),
('nournour2862005@gmail.com', 'a8a5f3bfc63af5870a5af940f92c3d16a4c7f5494eb97ddcebdad6f344b75309', '2025-11-17 00:19:32'),
('nournour2862005@gmail.com', 'f77ac0ed42f606e0ffcc345b46339a6d3290c1bf209535b75caebbe5f9f766cd', '2025-11-17 12:59:30'),
('nournour2862005@gmail.com', '187d2a8d075907032d361425a17f16b85c8c91b40ce899c6c9a2e13cbada4177', '2025-11-17 14:45:29');

-- --------------------------------------------------------

--
-- Structure de la table `reclamations`
--

CREATE TABLE `reclamations` (
  `id` int(11) NOT NULL,
  `user` varchar(100) NOT NULL,
  `sujet` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `date` datetime NOT NULL,
  `statut` enum('en attente','confirmé','rejeté') DEFAULT 'en attente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reclamations`
--

INSERT INTO `reclamations` (`id`, `user`, `sujet`, `description`, `date`, `statut`) VALUES
(1, 'John Doe', 'Problème de connexion', 'Impossible de se connecter au système', '2025-11-17 10:20:08', 'en attente'),
(2, 'Jane Smith', 'Bug interface', 'Le bouton sauvegarder ne fonctionne pas', '2025-11-17 10:20:08', 'confirmé'),
(3, 'Bob Wilson', 'Suggestion amélioration', 'Ajouter un export PDF', '2025-11-17 10:20:08', 'rejeté'),
(4, 'Marie Dubois', 'Problème de connexion', 'Impossible de me connecter à mon compte depuis ce matin. Message d\'erreur : \"Identifiants incorrects\" alors qu\'ils sont bons.', '2025-01-15 09:30:00', 'en attente'),
(5, 'Pierre Martin', 'Interface lente', 'L\'interface administrateur met plus de 10 secondes à charger certaines pages, particulièrement le tableau de bord.', '2025-01-15 10:15:00', 'en attente'),
(6, 'Sophie Lambert', 'Données manquantes', 'Les statistiques du mois de décembre n\'apparaissent pas dans les rapports. Il manque les données du 15 au 31 décembre.', '2025-01-15 11:45:00', 'en attente'),
(7, 'Thomas Moreau', 'Erreur export PDF', 'Lorsque j\'essaie d\'exporter un rapport en PDF, le fichier généré est corrompu et ne s\'ouvre pas.', '2025-01-16 08:20:00', 'en attente'),
(8, 'Laura Petit', 'Bouton sauvegarder défectueux', 'Le bouton \"Sauvegarder\" dans le formulaire d\'édition des réclamations ne fonctionne pas. La page se recharge mais les modifications ne sont pas enregistrées.', '2025-01-10 14:30:00', 'confirmé'),
(9, 'Marc Bernard', 'Problème de notification', 'Les notifications par email pour les nouvelles réclamations ne sont pas envoyées aux administrateurs.', '2025-01-11 16:45:00', 'confirmé'),
(10, 'Nathalie Roux', 'Erreur 404 sur page statistiques', 'La page \"/admin/statistiques\" retourne une erreur 404 depuis la dernière mise à jour.', '2025-01-12 10:20:00', 'confirmé'),
(11, 'David Leroy', 'Problème de timezone', 'Les dates des réclamations affichent un décalage de 2 heures. Il semble que le fuseau horaire ne soit pas correctement configuré.', '2025-01-13 15:10:00', 'confirmé'),
(12, 'Julie Simon', 'Changement de couleur demandé', 'Je souhaiterais que la couleur du thème passe du bleu au vert. Le bleu actuel ne me plaît pas.', '2025-01-08 11:30:00', 'rejeté'),
(13, 'Kevin Dupont', 'Ajout fonctionnalité personnelle', 'Pourriez-vous ajouter un module de chat en direct entre administrateurs ? Ce serait pratique pour communiquer.', '2025-01-09 13:45:00', 'rejeté'),
(14, 'Amélie Laurent', 'Demande de suppression de limite', 'La limite de 100 réclamations affichées est trop restrictive. Pourriez-vous la supprimer ?', '2025-01-14 09:15:00', 'rejeté'),
(15, 'Christophe Michel', 'Orthographe dans le titre', 'Il y a une faute d\'orthographe dans le titre de la page : \"Réclamation\" avec un \"c\" manquant.', '2025-01-05 14:20:00', ''),
(16, 'Sandrine Garcia', 'Problème de responsive', 'Sur mobile, le tableau des réclamations dépasse de l\'écran et nécessite un défilement horizontal.', '2025-01-06 16:35:00', ''),
(17, 'Alexandre Robert', 'Optimisation des performances', 'Les requêtes de recherche dans les réclamations sont très lentes lorsqu\'il y a beaucoup de données.', '2025-01-07 10:50:00', ''),
(18, 'Isabelle Fournier', 'Problème de tri', 'Le tri par date ne fonctionne pas correctement. Les dates ne sont pas dans l\'ordre chronologique.', '2025-01-17 08:45:00', 'en attente'),
(19, 'Patrick Mercier', 'Export Excel', 'L\'export Excel des réclamations ne inclut pas la colonne \"Description\".', '2025-01-17 10:30:00', 'confirmé'),
(20, 'Céline Blanc', 'Double authentification', 'Pourriez-vous ajouter une option de double authentification pour sécuriser davantage l\'accès admin ?', '2025-01-16 14:20:00', 'en attente'),
(21, 'Romain David', 'Sauvegarde automatique', 'Le système devrait sauvegarder automatiquement les brouillons de réclamations en cours de rédaction.', '2025-01-16 16:15:00', 'confirmé'),
(22, 'Valérie Bertrand', 'Recherche avancée', 'La fonction de recherche ne permet pas de chercher par plage de dates. Ce serait utile d\'ajouter cette fonctionnalité.', '2025-01-15 13:40:00', ''),
(25, 'zakaria ', 'Compte bloqué', 'aszdz', '2025-11-17 14:37:15', 'en attente'),
(26, 'ayarimed', 'Problème technique', 'wlh man3ref', '2025-11-17 15:04:29', 'en attente'),
(28, 'hicheem', 'Contenu incorrect', '00000000', '2025-11-17 21:18:56', 'confirmé'),
(29, ',m,.m.', 'Compte bloqu�', '.m.mk.', '2025-11-24 07:04:33', 'en attente'),
(30, ';kj;kj;kj', 'Compte bloqu�', ';lkl;kk;', '2025-11-24 07:04:40', 'en attente'),
(31, 'aaaaaaa', 'Compte bloqu�', 'aaaaa', '2025-11-24 07:04:51', 'en attente'),
(32, 'jkhkjjk', 'Compte bloqu�', 'hkbjj,m', '2025-11-24 07:06:20', 'en attente'),
(33, 'hichem', 'Compte bloqu�', 'hichem', '2025-11-24 07:07:03', 'en attente'),
(34, 'hichem', 'Compte bloqu�', 'hichem', '2025-11-24 07:07:24', 'en attente'),
(35, 'hichem', 'Compte bloqu�', 'hichem', '2025-11-24 07:07:44', 'en attente'),
(36, 'aaaaaaaaaaaa', 'Compte bloqu�', 'aaaaaaaaaaa', '2025-11-24 11:36:27', 'en attente'),
(37, 'qss', 'Contenu incorrect', 'eqs', '2025-11-24 14:56:14', 'en attente'),
(38, 'asdfghjkl', 'Probleme_de_securite', 'ascdvgbm,', '2025-11-24 23:08:01', 'en attente'),
(39, 'zcvbm', 'Probleme_de_securit�', 'asdfghjk', '2025-11-29 12:12:25', 'en attente');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `nom`, `description`) VALUES
(1, 'Admin', 'Peut gérer tous les utilisateurs et accéder au tableau de bord.'),
(2, 'Utilisateur', 'Peut se connecter, modifier son profil et changer son mot de passe.'),
(3, 'Visiteur', 'Accès limité avant connexion.');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `pseudo` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `statut` enum('actif','inactif','banni') DEFAULT 'actif',
  `role_id` int(11) DEFAULT 2,
  `planet` enum('terra','mars','venus','jupiter') NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `pseudo`, `email`, `password`, `statut`, `role_id`, `planet`, `avatar`, `created_at`) VALUES
(66, 'zak12', 'zakaria.benouirsane@esprit.tn', '$2y$10$DbExiga4ecL1SfXPfPUx..FOcq/LbIV473rNff2RKVcBCgLbuaVA.', 'actif', 1, 'mars', NULL, '2025-11-27 00:23:17'),
(68, 'zikou', 'zakariabenouirane@esprit.tn', '$2y$10$jN95QpSpsLUturuobt.qqef4prcpYH6jI8uONPfCh/BTHTPetVx02', 'actif', 1, 'jupiter', '../../../view/Client/login/uploads/avatars/6928142713cdfcontrollerjpg', '2025-11-27 00:23:17'),
(69, 'zaktheastroA', 'zak.thea@gmail.com', '$2y$10$eZEcuHxVhmDdrExhohNgC.H2pPtQZQ6IBnAcHKCQ3FDGXGm.rNMLC', 'banni', 2, 'mars', NULL, '2025-11-27 00:23:17'),
(70, 'Nour___', 'nournour2862005@gmail.com', '$2y$10$/lIqkE12ly./qzuRkErao.uTyxjnI61K4NGb8c.3JoxiQxP/DGFfS', 'actif', 2, 'mars', NULL, '2025-11-27 00:23:17'),
(71, 'hichem', 'hichem@gmail.com', '$2y$10$mUSIGyj5mm9L4uBgyuH8I.zvJdbmw6VQxFQ/LsrpNyvfcoYrD7Zwm', 'actif', 1, 'mars', 'view/Client/login/uploads/avatars/69282b60c29c9controller.jpg', '2025-11-27 00:23:17'),
(72, 'hichem1', 'hichem1@gmail.com', '$2y$10$GUdwLaQctJxavMIh7fwkj.2pUgc62tEkPinXWsIhDoDtTiSLvZcaa', 'actif', 2, 'mars', '../../../view/Client/login/uploads/avatars/6927abe55c338controllerpng', '2025-11-27 02:23:22'),
(73, 'gass', 'gass@gmail.com', '$2y$10$.6BNZ6gFl8MZUDL/Z9UOtuI.yP5PxFLY1ZXcSlRnhc5YYaQmLyohW', 'actif', 1, 'mars', '../../../view/Client/login/uploads/avatars/6927aea543ac7controllerjpg', '2025-11-27 02:48:32'),
(74, 'toutou', 'toutou@gmail.com', '$2y$10$MITlfVF2otWIgV9KuQn7H.zHW8GPjxWKPnCeOfW8a38CximpLMGTS', 'actif', 1, 'mars', '../../../view/Client/login/uploads/avatars/6928146fc8c54controllerjpg', '2025-11-27 10:01:15'),
(75, 'anas', 'anas@gmail.com', '$2y$10$2CpYASG87vVe3P7uy6Rl2.8mCX/DiyIo32jqg8.V00hp99wyB4AnS', 'actif', 1, 'mars', '../../../view/Client/login/uploads/avatars/69281abf08c32controllerjpg', '2025-11-27 10:31:33'),
(76, 'test', 'test@gmail.com', '$2y$10$rd/f47TfKlr5SsLHWsfGde2Y/dCiFfVByVXoIWE4GnBNQ348kqXpG', 'actif', 1, 'mars', NULL, '2025-11-29 00:57:20');

-- --------------------------------------------------------

--
-- Structure de la table `users`
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
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `role`, `is_active`, `created_at`) VALUES
(1, 'alice', 'alice@example.com', 'pass123', 'back', 1, '2025-11-17 12:18:18'),
(2, 'bob', 'bob@example.com', 'pass123', 'front', 1, '2025-11-17 12:18:18'),
(3, 'charlie', 'charlie@example.com', 'pass123', 'front', 1, '2025-11-17 12:18:18'),
(4, 'david', 'david@example.com', 'pass123', 'front', 1, '2025-11-17 12:18:18'),
(5, 'eve', 'eve@example.com', 'pass123', 'front', 1, '2025-11-17 12:18:18'),
(6, 'frank', 'frank@example.com', 'pass123', 'front', 1, '2025-11-17 12:18:18'),
(7, 'grace', 'grace@example.com', 'pass123', 'front', 1, '2025-11-17 12:18:18'),
(8, 'heidi', 'heidi@example.com', 'pass123', 'front', 1, '2025-11-17 12:18:18'),
(9, 'ivan', 'ivan@example.com', 'pass123', 'front', 1, '2025-11-17 12:18:18'),
(10, 'judy', 'judy@example.com', 'pass123', 'front', 1, '2025-11-17 12:18:18');

-- --------------------------------------------------------

--
-- Structure de la table `user_activity`
--

CREATE TABLE `user_activity` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` varchar(255) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user_activity`
--

INSERT INTO `user_activity` (`id`, `user_id`, `action`, `timestamp`) VALUES
(1, 71, 'connexion', '2025-11-27 00:17:18'),
(2, 71, 'connexion', '2025-11-27 00:27:41'),
(3, 68, 'connexion', '2025-11-27 00:27:52'),
(4, 71, 'connexion', '2025-11-27 00:31:21'),
(5, 71, 'modification', '2025-11-27 00:31:25'),
(6, 71, 'connexion', '2025-11-27 00:31:32'),
(7, 68, 'connexion', '2025-11-27 00:31:41'),
(8, 71, 'connexion', '2025-11-27 01:26:32'),
(9, 68, 'connexion', '2025-11-27 01:26:42'),
(10, 68, 'connexion', '2025-11-27 01:52:07'),
(11, 68, 'connexion', '2025-11-27 01:54:04'),
(12, 68, 'connexion', '2025-11-27 01:56:01'),
(13, 71, 'connexion', '2025-11-27 01:59:38'),
(14, 68, 'connexion', '2025-11-27 01:59:48'),
(15, 71, 'connexion', '2025-11-27 02:03:28'),
(16, 71, 'connexion', '2025-11-27 02:12:22'),
(17, 72, 'connexion', '2025-11-27 02:23:43'),
(18, 72, 'modification', '2025-11-27 02:39:49'),
(19, 73, 'connexion', '2025-11-27 02:48:39'),
(20, 73, 'connexion', '2025-11-27 02:51:18'),
(21, 73, 'modification', '2025-11-27 02:51:33'),
(22, 71, 'connexion', '2025-11-27 02:51:44'),
(23, 73, 'connexion', '2025-11-27 02:53:04'),
(24, 74, 'connexion', '2025-11-27 10:02:49'),
(25, 68, 'connexion', '2025-11-27 10:04:01'),
(26, 68, 'modification', '2025-11-27 10:04:39'),
(27, 74, 'connexion', '2025-11-27 10:04:50'),
(28, 74, 'modification', '2025-11-27 10:05:50'),
(29, 74, 'modification', '2025-11-27 10:05:51'),
(30, 74, 'modification', '2025-11-27 10:05:53'),
(31, 74, 'modification', '2025-11-27 10:06:00'),
(32, 71, 'connexion', '2025-11-27 10:10:07'),
(33, 68, 'connexion', '2025-11-27 10:12:35'),
(34, 68, 'modification', '2025-11-27 10:12:55'),
(35, 71, 'connexion', '2025-11-27 10:28:43'),
(36, 74, 'connexion', '2025-11-27 10:29:33'),
(37, 75, 'connexion', '2025-11-27 10:31:40'),
(38, 75, 'modification', '2025-11-27 10:32:47'),
(39, 71, 'connexion', '2025-11-27 10:32:55'),
(40, 75, 'connexion', '2025-11-27 10:33:58'),
(41, 71, 'connexion', '2025-11-27 11:09:24'),
(42, 71, 'modification', '2025-11-27 11:20:45'),
(43, 71, 'connexion', '2025-11-27 11:33:48'),
(44, 71, 'modification', '2025-11-27 11:43:44'),
(45, 71, 'connexion', '2025-11-27 11:54:03'),
(46, 71, 'connexion', '2025-11-27 21:11:39'),
(47, 71, 'connexion', '2025-11-28 10:05:29'),
(48, 76, 'connexion', '2025-11-29 00:57:36'),
(49, 71, 'connexion', '2025-11-29 01:00:03'),
(50, 76, 'connexion', '2025-11-29 01:00:49'),
(51, 76, 'connexion', '2025-11-29 12:08:00'),
(52, 76, 'connexion', '2025-11-29 16:57:57'),
(53, 76, 'connexion', '2025-11-29 16:58:05'),
(54, 76, 'connexion', '2025-11-29 17:01:46'),
(55, 76, 'connexion', '2025-11-29 18:03:42'),
(56, 76, 'connexion', '2025-11-29 18:06:56'),
(57, 76, 'connexion', '2025-11-29 18:08:46'),
(58, 71, 'connexion', '2025-11-29 19:35:15'),
(59, 71, 'connexion', '2025-11-29 20:24:07');

-- --------------------------------------------------------

--
-- Structure de la table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `innovation_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vote_type` enum('up','down') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `votes`
--

INSERT INTO `votes` (`id`, `innovation_id`, `user_id`, `vote_type`, `created_at`) VALUES
(10, 43, 1, 'up', '2025-11-27 01:48:59'),
(29, 54, 71, 'up', '2025-11-28 10:14:39'),
(37, 54, 76, 'up', '2025-11-29 00:10:06'),
(39, 43, 76, 'up', '2025-11-29 14:35:52');

-- --------------------------------------------------------

--
-- Structure de la vue `innovation_with_category`
--
DROP TABLE IF EXISTS `innovation_with_category`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `innovation_with_category`  AS SELECT `i`.`id` AS `id`, `i`.`titre` AS `titre`, `i`.`description` AS `description`, `i`.`date_creation` AS `date_creation`, `i`.`statut` AS `statut`, `c`.`nom` AS `categorie_nom` FROM (`innovations` `i` left join `categories` `c` on(`i`.`category_id` = `c`.`id`)) ;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `attachments`
--
ALTER TABLE `attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_id` (`comment_id`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nom` (`nom`),
  ADD KEY `idx_date` (`date_creation`);

--
-- Index pour la table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `innovation_id` (`innovation_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Index pour la table `confirmations_email`
--
ALTER TABLE `confirmations_email`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `conversation_users`
--
ALTER TABLE `conversation_users`
  ADD PRIMARY KEY (`conversation_id`,`user_id`),
  ADD KEY `fk_conv_user_user` (`user_id`);

--
-- Index pour la table `innovations`
--
ALTER TABLE `innovations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_titre` (`titre`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_date` (`date_creation`),
  ADD KEY `fk_innov_user` (`user_id`),
  ADD KEY `fk_categories_innovations` (`category_id`);

--
-- Index pour la table `logs_connexions`
--
ALTER TABLE `logs_connexions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_messages_conversation` (`conversation_id`),
  ADD KEY `fk_messages_user` (`user_id`);

--
-- Index pour la table `reclamations`
--
ALTER TABLE `reclamations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pseudo` (`pseudo`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_role` (`role_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `user_activity`
--
ALTER TABLE `user_activity`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_vote` (`innovation_id`,`user_id`),
  ADD KEY `idx_innovation` (`innovation_id`),
  ADD KEY `idx_type` (`vote_type`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `attachments`
--
ALTER TABLE `attachments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT pour la table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT pour la table `confirmations_email`
--
ALTER TABLE `confirmations_email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT pour la table `innovations`
--
ALTER TABLE `innovations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT pour la table `logs_connexions`
--
ALTER TABLE `logs_connexions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;

--
-- AUTO_INCREMENT pour la table `reclamations`
--
ALTER TABLE `reclamations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `user_activity`
--
ALTER TABLE `user_activity`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT pour la table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `attachments`
--
ALTER TABLE `attachments`
  ADD CONSTRAINT `attachments_ibfk_1` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`innovation_id`) REFERENCES `innovations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `confirmations_email`
--
ALTER TABLE `confirmations_email`
  ADD CONSTRAINT `confirmations_email_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `conversation_users`
--
ALTER TABLE `conversation_users`
  ADD CONSTRAINT `fk_conv_user_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conv_user_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `innovations`
--
ALTER TABLE `innovations`
  ADD CONSTRAINT `fk_categories_innovations` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_innov_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `logs_connexions`
--
ALTER TABLE `logs_connexions`
  ADD CONSTRAINT `logs_connexions_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `user` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_messages_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_messages_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `fk_role` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Contraintes pour la table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`innovation_id`) REFERENCES `innovations` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
