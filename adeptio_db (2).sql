-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 21, 2026 at 11:02 PM
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
-- Database: `adeptio_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id_admin` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `date_creation` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id_admin`, `nom`, `email`, `mot_de_passe`, `date_creation`) VALUES
(1, 'Administrateur', 'admin@adeptio.com', '$2y$12$xiPBvzXtxi5uWHbFQb02hOageOGWTiYXHsrB203zz/EruH0lL5FXe', '2026-06-10 01:42:45'),
(2, '3aym l\'admin', 'admin3aym@adeptio.com', '$2y$12$Fvn0y/vKqsZfiTTiuf1d0ey3gLnsXe65oV2SgDLT5CTqI71yhJ5Ya', '2026-06-10 10:59:54');

-- --------------------------------------------------------

--
-- Table structure for table `demandes`
--

CREATE TABLE `demandes` (
  `id_demande` int(11) NOT NULL,
  `type_demande` enum('etudiant','partenaire') DEFAULT NULL,
  `nom` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `sujet` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `statut` enum('en_attente','acceptee','rejetee') DEFAULT 'en_attente',
  `date_demande` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `demandes`
--

INSERT INTO `demandes` (`id_demande`, `type_demande`, `nom`, `email`, `telephone`, `sujet`, `message`, `statut`, `date_demande`) VALUES
(3, 'etudiant', 'Med Taha', NULL, '0624248921', NULL, 'bonjour je suis intéressé par l\'angleterre', 'en_attente', '2026-06-21 19:04:05'),
(5, 'partenaire', 'Ahmed Mohamed', 'droka@gmail.ma', '0537393817', 'Etablissement: DROKA — Besoin: Implantation au Maroc', 'bonjour nous somme intéressés', 'acceptee', '2026-06-21 20:24:30');

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE `emails` (
  `id_email` int(11) NOT NULL,
  `id_demande` int(11) DEFAULT NULL,
  `sujet` varchar(255) DEFAULT NULL,
  `contenu` text DEFAULT NULL,
  `date_envoi` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id_log` int(11) NOT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `date_action` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id_log`, `id_admin`, `action`, `date_action`) VALUES
(1, 2, 'Connexion admin', '2026-06-10 11:03:15'),
(2, 2, 'Connexion admin', '2026-06-11 00:39:49'),
(3, 2, 'Connexion admin', '2026-06-11 12:44:58'),
(4, 2, 'Connexion admin', '2026-06-16 00:03:33'),
(5, 2, 'Connexion admin', '2026-06-21 18:54:23'),
(6, 2, 'Rendez-vous programme pour la demande #5 le 2026-06-22 a 22:00', '2026-06-21 20:25:17'),
(7, 2, 'Rendez-vous #1 modifie: programme', '2026-06-21 20:25:29'),
(8, 2, 'Rendez-vous #1 modifie: effectue', '2026-06-21 20:27:46');

-- --------------------------------------------------------

--
-- Table structure for table `page_visits`
--

CREATE TABLE `page_visits` (
  `id` int(11) NOT NULL,
  `page_url` varchar(500) DEFAULT NULL,
  `visited_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `referrer` varchar(500) DEFAULT NULL,
  `session_id` varchar(64) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `page_visits`
--

INSERT INTO `page_visits` (`id`, `page_url`, `visited_at`, `referrer`, `session_id`) VALUES
(1, '/ADEPTIO/etudiant.html', '2026-06-21 19:03:29', 'http://localhost/ADEPTIO/politique-confidentialite.html', 'c29970472dcbe468c41338a9f97592a8d801706eea1ef782dae2737fdb13e2d8'),
(2, '/ADEPTIO/index.html', '2026-06-21 19:33:12', 'http://localhost/ADEPTIO/etudiant.html', 'c29970472dcbe468c41338a9f97592a8d801706eea1ef782dae2737fdb13e2d8'),
(3, '/ADEPTIO/professionnel.html', '2026-06-21 20:12:58', 'http://localhost/ADEPTIO/index.html', 'c29970472dcbe468c41338a9f97592a8d801706eea1ef782dae2737fdb13e2d8');

-- --------------------------------------------------------

--
-- Table structure for table `rendez_vous`
--

CREATE TABLE `rendez_vous` (
  `id_rdv` int(11) NOT NULL,
  `id_demande` int(11) DEFAULT NULL,
  `date_rdv` date DEFAULT NULL,
  `heure_rdv` time DEFAULT NULL,
  `statut` enum('programme','effectue','annule') DEFAULT 'programme'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rendez_vous`
--

INSERT INTO `rendez_vous` (`id_rdv`, `id_demande`, `date_rdv`, `heure_rdv`, `statut`) VALUES
(1, 5, '2026-06-22', '22:00:00', 'effectue');

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(40) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `source_page` varchar(255) DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`id`, `name`, `email`, `phone`, `message`, `source_page`, `submitted_at`) VALUES
(2, 'Med Taha', NULL, '0624248921', 'bonjour je suis intéressé par l\'angleterre', '/ADEPTIO/etudiant.html', '2026-06-21 19:04:05'),
(6, 'Ahmed Mohamed', 'droka@gmail.ma', '0537393817', 'bonjour nous somme intéressés', '/ADEPTIO/professionnel.html', '2026-06-21 20:24:30');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `demandes`
--
ALTER TABLE `demandes`
  ADD PRIMARY KEY (`id_demande`);

--
-- Indexes for table `emails`
--
ALTER TABLE `emails`
  ADD PRIMARY KEY (`id_email`),
  ADD KEY `id_demande` (`id_demande`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id_log`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indexes for table `page_visits`
--
ALTER TABLE `page_visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_visited` (`visited_at`),
  ADD KEY `idx_session` (`session_id`);

--
-- Indexes for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD PRIMARY KEY (`id_rdv`),
  ADD KEY `id_demande` (`id_demande`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_submitted` (`submitted_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `demandes`
--
ALTER TABLE `demandes`
  MODIFY `id_demande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `emails`
--
ALTER TABLE `emails`
  MODIFY `id_email` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id_log` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `page_visits`
--
ALTER TABLE `page_visits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  MODIFY `id_rdv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `emails`
--
ALTER TABLE `emails`
  ADD CONSTRAINT `emails_ibfk_1` FOREIGN KEY (`id_demande`) REFERENCES `demandes` (`id_demande`);

--
-- Constraints for table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `admins` (`id_admin`);

--
-- Constraints for table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD CONSTRAINT `rendez_vous_ibfk_1` FOREIGN KEY (`id_demande`) REFERENCES `demandes` (`id_demande`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
