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
(1, 'OSolEOMarNapoli', '$2y$10$DEBJB8kPHTrSlQjMrPXsYubhcO5VSqqsfT6Fhbvne7TlhetF.vyAi', 1),
(2, 'emaThreaddaJava', '$2y$10$U4bDPJopPVTJmNX2CpVQhOOoAfARGm9jsrFMH.Bqu7nIeV91C9CIy', 1),
(3, 'InfoGep2006', '$2y$10$Ix3U6UhI2dG8IEHpNnx8Qer9lACaLhv3a72YSQqkm.a5omuxcMNYy', 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `docente`
--

CREATE TABLE `docente` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nome` varchar(50) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
    `email` varchar(120) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
    `rifUtente` int UNSIGNED NOT NULL,
    CONSTRAINT `docente_ibfk_1` FOREIGN KEY (`rifUtente`) REFERENCES `utente` (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `docente`
--

INSERT INTO `docente` (`id`, `nome`, `email`, `rifUtente`) VALUES
(1, 'Mario Sorvillo', 'sorvillo.mario@istitutolevi.edu.it', 1),
(2, 'Emanuele Gnoni', 'gnoni.emanuele@istitutolevi.edu.it', 2),
(3, 'Giuseppe Carlino', 'carlino.giuseppe@istitutolevi.edu.it', 3);

-- --------------------------------------------------------

--
-- Struttura della tabella `indirizzo`
--

CREATE TABLE `indirizzo` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `descrizione` varchar(4) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `indirizzo`
--

INSERT INTO `indirizzo` (`id`, `descrizione`) VALUES
(1, 'LSSA'),
(2, 'ITT'),
(3, 'IPIA'),
(4, 'IPSC');

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
    `descrizione` varchar(255) NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `tipo`
--

CREATE TABLE `tipo` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `descrizione` varchar(7) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `tipo`
--

INSERT INTO `tipo` (`id`, `descrizione`) VALUES
(1, 'Viaggio'),
(2, 'Uscita');

-- --------------------------------------------------------

--
-- Struttura della tabella `viaggio`
--

CREATE TABLE `viaggio` (
    `id` int UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `nome` varchar(30) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
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
    `data` char(19) NOT NULL,
    `ruolo` varchar(20) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
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
    `referenteViaggio` varchar(50) NOT NULL,
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
    `validitaBozza` char(2) NOT NULL,
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