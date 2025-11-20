
--
-- Base de données : `tunispace_database`
--

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
(3, 'Habitats Lunaires', 'Conception et développement d\'habitats pour la Lune', '2025-11-12 23:48:06'),
(4, 'Robotique Spatial', 'Robots et systèmes automatisés pour l\'espace', '2025-11-12 23:48:06'),
(10, 'Robotique Spatiale', 'Robots et systèmes automatisés pour l\'espace', '2025-11-13 00:52:53'),
(15, 'TOUTOU2', 'TEST_1', '2025-11-16 13:41:30'),
(21, 'Exploration Spatiale', 'Projets liés à l\'exploration de l\'espace et des planètes', '2025-11-17 00:10:21'),
(22, 'Énergie Orbitale', 'Solutions énergétiques pour l\'espace et les satellites', '2025-11-17 00:10:21'),
(23, 'Habitats Lunaires', 'Conception et développement d\'habitats pour la Lune', '2025-11-17 00:10:21'),
(24, 'Robotique Spatiale', 'Robots et systèmes automatisés pour l\'espace', '2025-11-17 00:10:21'),
(25, 'Propulsion Avancée', 'Nouvelles technologies de propulsion spatiale', '2025-11-17 00:10:21'),
(26, 'Exploration Spatiale', 'Projets liés à l\'exploration de l\'espace et des planètes', '2025-11-17 00:11:38'),
(27, 'Énergie Orbitale', 'Solutions énergétiques pour l\'espace et les satellites', '2025-11-17 00:11:38'),
(28, 'Habitats Lunaires', 'Conception et développement d\'habitats pour la Lune', '2025-11-17 00:11:38'),
(29, 'Robotique Spatiale', 'Robots et systèmes automatisés pour l\'espace', '2025-11-17 00:11:38'),
(30, 'Propulsion Avancée', 'Nouvelles technologies de propulsion spatiale', '2025-11-17 00:11:38'),
(31, 'Exploration Spatiale', 'Projets liés à l\'exploration de l\'espace et des planètes', '2025-11-17 00:12:59'),
(32, 'Énergie Orbitale', 'Solutions énergétiques pour l\'espace et les satellites', '2025-11-17 00:12:59'),
(33, 'Habitats Lunaires', 'Conception et développement d\'habitats pour la Lune', '2025-11-17 00:12:59'),
(34, 'Robotique Spatiale', 'Robots et systèmes automatisés pour l\'espace', '2025-11-17 00:12:59'),
(35, 'Propulsion Avancée', 'Nouvelles technologies de propulsion spatiale', '2025-11-17 00:12:59'),
(36, 'Exploration Spatiale', 'Projets liés à l\'exploration de l\'espace et des planètes', '2025-11-17 00:24:33'),
(37, 'Énergie Orbitale', 'Solutions énergétiques pour l\'espace et les satellites', '2025-11-17 00:24:33'),
(38, 'Habitats Lunaires', 'Conception et développement d\'habitats pour la Lune', '2025-11-17 00:24:33'),
(39, 'Robotique Spatiale', 'Robots et systèmes automatisés pour l\'espace', '2025-11-17 00:24:33'),
(40, 'Propulsion Avancée', 'Nouvelles technologies de propulsion spatiale', '2025-11-17 00:24:33'),
(41, 'TOUTOU_!', 'TEST_1', '2025-11-17 00:38:23');

-- --------------------------------------------------------

--
-- Structure de la table `commentaires`
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
-- Structure de la table `innovations`
--

CREATE TABLE `innovations` (
  `id` int(11) NOT NULL,
  `titre` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `date_creation` datetime NOT NULL,
  `statut` varchar(50) DEFAULT 'En attente',
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `innovations`
--

INSERT INTO `innovations` (`id`, `titre`, `description`, `date_creation`, `statut`, `category_id`) VALUES
(11, 'TOUTOU2', 'TOUTOU2', '0000-00-00 00:00:00', 'Validée', 15),
(22, 'abcdef', 'weeweew', '2025-11-16 20:44:27', 'Rejetée', 18),
(26, 'TOUTOU1', 'TEST_1', '2025-11-17 00:38:55', 'Validée', 41);

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
('zak.thea@gmail.com', '8083e2b6a95b2a8ec1718226f9d9ea2b2d08c5ba97cf8a06f879da8b822cc9a0', '2025-11-16 00:04:26');

-- --------------------------------------------------------

--
-- Structure de la table `pieces_jointes`
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
  `password` varchar(255) NOT NULL,
  `statut` enum('actif','inactif','banni') DEFAULT 'actif',
  `role_id` int(11) DEFAULT 2,
  `planet` enum('terra','mars','venus','jupiter') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `pseudo`, `email`, `password`, `statut`, `role_id`, `planet`) VALUES
(66, 'zak12', 'zakaria.benouirsane@esprit.tn', '$2y$10$DbExiga4ecL1SfXPfPUx..FOcq/LbIV473rNff2RKVcBCgLbuaVA.', 'actif', 1, 'mars'),
(68, 'Zak', 'zakariabenouirane@esprit.tn', '$2y$10$jN95QpSpsLUturuobt.qqef4prcpYH6jI8uONPfCh/BTHTPetVx02', 'actif', 1, 'jupiter'),
(69, 'zaktheastroA', 'zak.thea@gmail.com', '$2y$10$eZEcuHxVhmDdrExhohNgC.H2pPtQZQ6IBnAcHKCQ3FDGXGm.rNMLC', 'banni', 2, 'mars'),
(70, 'Nour___', 'nournour2862005@gmail.com', '$2y$10$/lIqkE12ly./qzuRkErao.uTyxjnI61K4NGb8c.3JoxiQxP/DGFfS', 'actif', 2, 'mars');

-- --------------------------------------------------------

--
-- Structure de la table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `innovation_id` int(11) NOT NULL,
  `type_vote` enum('up','down') NOT NULL,
  `date_vote` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_nom` (`nom`),
  ADD KEY `idx_date` (`date_creation`);

--
-- Index pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_innovation` (`innovation_id`),
  ADD KEY `idx_date` (`date_creation`);

--
-- Index pour la table `confirmations_email`
--
ALTER TABLE `confirmations_email`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `innovations`
--
ALTER TABLE `innovations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_titre` (`titre`),
  ADD KEY `idx_statut` (`statut`),
  ADD KEY `idx_date` (`date_creation`),
  ADD KEY `fk_category_innov` (`category_id`);

--
-- Index pour la table `logs_connexions`
--
ALTER TABLE `logs_connexions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_innovation` (`innovation_id`);

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
-- Index pour la table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_innovation` (`innovation_id`),
  ADD KEY `idx_type` (`type_vote`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT pour la table `commentaires`
--
ALTER TABLE `commentaires`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `confirmations_email`
--
ALTER TABLE `confirmations_email`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `innovations`
--
ALTER TABLE `innovations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `logs_connexions`
--
ALTER TABLE `logs_connexions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `pieces_jointes`
--
ALTER TABLE `pieces_jointes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT pour la table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `commentaires`
--
ALTER TABLE `commentaires`
  ADD CONSTRAINT `commentaires_ibfk_1` FOREIGN KEY (`innovation_id`) REFERENCES `innovations` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `confirmations_email`
--
ALTER TABLE `confirmations_email`
  ADD CONSTRAINT `confirmations_email_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `logs_connexions`
--
ALTER TABLE `logs_connexions`
  ADD CONSTRAINT `logs_connexions_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `user` (`id`) ON DELETE SET NULL;

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
