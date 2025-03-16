-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Feb 14, 2025 alle 10:00
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `leviws-2`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `gruppo`
--

CREATE TABLE `gruppo` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nome` varchar(5) NOT NULL,
    `permessi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`permessi`))
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `gruppo`
--

INSERT INTO `gruppo` (`id`, `nome`, `permessi`) VALUES
(1, 'admins', '{\"manage_users\": true, \"view_dashboard\": true, \"manage_settings\": true}'),
(2, 'users', '{\"view_dashboard\": true}');

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

CREATE TABLE `utente` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `username` varchar(50) UNIQUE NOT NULL,
    `password` varchar(255) UNIQUE NOT NULL,
    `rifGruppo` int UNSIGNED NOT NULL,
    CONSTRAINT `utente_ibfk_1` FOREIGN KEY (`rifGruppo`) REFERENCES `gruppo` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utente`
--

INSERT INTO `utente` (`id`, `username`, `password`, `rifGruppo`) VALUES
(1, 'admin_user', '$2y$10$6s00YYHczdd/MtQu1Ks.8eQVHFVTkfcoVnGfcFAN8adJj6jFLJQp2', 1),
(2, 'standard_user', '$2y$10$1jK1AoMovQO8cApX.Lcc2uYor52rr.2H5QPn.7bEAtyKugNR0e6BC', 2),
(3, 'pippo', '$2y$10$aKrgftaLq0X0H.8gkv3D3eL/cy1l.0bz79lQJ9akoWoSChevmOdu2', 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `docente`
--

CREATE TABLE `docente` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nome` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
    `cognome` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
    `email` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
    `rifUtente` int UNSIGNED NOT NULL,
    CONSTRAINT `docente_ibfk_1` FOREIGN KEY (`rifUtente`) REFERENCES `utente` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `indirizzo`
--

CREATE TABLE `indirizzo` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `descrizione` varchar(10) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `classe`
--

CREATE TABLE `classe` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `descrizione` varchar(2) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
    `numerosita` int NOT NULL,
    `2/3` int NOT NULL,
    `rifDocente` int UNSIGNED NOT NULL,
    `rifIndirizzo` int UNSIGNED NOT NULL,
    CONSTRAINT `classe_ibfk_1` FOREIGN KEY (`rifDocente`) REFERENCES `docente` (`id`),
    CONSTRAINT `classe_ibfk_2` FOREIGN KEY (`rifIndirizzo`) REFERENCES `indirizzo` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `bozza`
--

CREATE TABLE `bozza` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nome` varchar(30) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
    `tipo` varchar(7) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
    `descrizione` varchar(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `tipo`
--

CREATE TABLE `tipo` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `descrizione` varchar(20) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `viaggio`
--

CREATE TABLE `viaggio` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `descrizione` varchar(255) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
    `dataInizio` date NOT NULL,
    `dataFine` date NOT NULL,
    `mezzo` varchar(10) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
    `destinazione` varchar(15) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
    `rifTipo` int UNSIGNED NOT NULL,
    CONSTRAINT `viaggio_ibfk_1` FOREIGN KEY (`rifTipo`) REFERENCES `tipo` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `proposta`
--

CREATE TABLE `proposta` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `descrizione` varchar(255) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
    `dataCreazione` date NOT NULL,
    `rifViaggio` int UNSIGNED NOT NULL,
    CONSTRAINT `proposta_ibfk_1` FOREIGN KEY (`rifViaggio`) REFERENCES `viaggio` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `effettua`
--

CREATE TABLE `effettua` (
    `data` date NOT NULL,
    `ruolo` varchar(10) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
    `rifDocente` int UNSIGNED NOT NULL,
    `rifBozza` int UNSIGNED NOT NULL,
    CONSTRAINT `effettua_ibfk_1` FOREIGN KEY(`rifDocente`) REFERENCES `docente` (`id`),
    CONSTRAINT `effettua_ibfk_2` FOREIGN KEY(`rifBozza`) REFERENCES `bozza` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `partecipa`
--

CREATE TABLE `partecipa` (
    `referenteViaggio` varchar(30) NOT NULL,
    `rifViaggio` int UNSIGNED NOT NULL,
    `rifDocente` int UNSIGNED NOT NULL,
    CONSTRAINT `partecipa_ibfk_1` FOREIGN KEY (`rifViaggio`) REFERENCES `viaggio` (`id`),
    CONSTRAINT `partecipa_ibfk_2` FOREIGN KEY (`rifDocente`) REFERENCES `docente` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `consegue`
--

CREATE TABLE `consegue` (
    `validitaBozza` boolean NOT NULL,
    `rifProposta` int UNSIGNED NOT NULL,
    `rifBozza` int UNSIGNED NOT NULL,
    CONSTRAINT `consegue_ibfk_1` FOREIGN KEY (`rifProposta`) REFERENCES `proposta` (`id`),
    CONSTRAINT `consegue_ibfk_2` FOREIGN KEY (`rifBozza`) REFERENCES `bozza` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `coinvolge`
--

CREATE TABLE `coinvolge` (
    `relazione` blob NOT NULL,
    `rifViaggio` int UNSIGNED NOT NULL,
    `rifClasse` int UNSIGNED NOT NULL,
    CONSTRAINT `coinvolge_ibfk_1` FOREIGN KEY (`rifViaggio`) REFERENCES `viaggio` (`id`),
    CONSTRAINT `coinvolge_ibfk_2` FOREIGN KEY (`rifClasse`) REFERENCES `classe` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `sostituzione`
--

CREATE TABLE `sostituzione` (
    `sostituto` boolean NOT NULL,
    `rifProposta` int UNSIGNED NOT NULL,
    `rifDocente` int UNSIGNED NOT NULL,
    CONSTRAINT `sostituzione_ibfk_1` FOREIGN KEY (`rifProposta`) REFERENCES `proposta` (`id`),
    CONSTRAINT `sostituzione_ibfk_2` FOREIGN KEY (`rifDocente`) REFERENCES `docente` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;