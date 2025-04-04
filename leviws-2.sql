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
-- Struttura della tabella `bozza`
--

CREATE TABLE `bozza` (
  `id_bozza` bigint(10) UNSIGNED NOT NULL,
  `descrizione` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `classe`
--

CREATE TABLE `classe` (
  `id_classe` bigint(3) UNSIGNED NOT NULL,
  `descrizione` varchar(2) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
  `numerosita` int(2) NOT NULL,
  `num_minimo_studenti` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `coinvolge`
--

CREATE TABLE `coinvolge` (
  `relazione` blob NOT NULL
  `rif_proposta` bigint(20) REFERENCES viaggio(id_viaggio) NOT NULL,
  `rif_proposta` bigint(20) REFERENCES classe(id_classe) NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `consegue`
--

CREATE TABLE `consegue` (
  `validita_bozza` tinyint(1) NOT NULL,
  `rif_proposta` bigint(20) REFERENCES proposta(id_proposta) NOT NULL,
  `rif_bozza` bigint(20) REFERENCES bozza(id_bozza) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `docente`
--

CREATE TABLE `docente` (
  `id_docente` bigint(250) UNSIGNED NOT NULL,
  `nome` varchar(30) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
  `cognome` varchar(30) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `effettua`
--

CREATE TABLE `effettua` (
  `data` date DEFAULT NULL,
  `ruolo` varchar(20) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL
  `rif_docente` bigint(20) REFERENCES docente(id_docente) NOT NULL,
  `rif_bozza` bigint(20) REFERENCES bozza(id_bozza) NOT NULL,
  
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `effettua`
--

INSERT INTO `effettua` (`data`, `ruolo`, `rif_docente`, `rif_bozza`) VALUES
(NULL, 0, 0, ''),
(NULL, 0, 0, '');

-- --------------------------------------------------------

--
-- Struttura della tabella `gruppo`
--

CREATE TABLE `gruppo` (
  `id` bigint(11) UNSIGNED NOT NULL,
  `name` varchar(50) NOT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`permissions`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `gruppo`
--

INSERT INTO `gruppo` (`id`, `name`, `permissions`) VALUES
(1, 'admins', '{\"manage_users\": true, \"view_dashboard\": true, \"manage_settings\": true}'),
(2, 'users', '{\"view_dashboard\": true}');

-- --------------------------------------------------------

--
-- Struttura della tabella `indirizzo`
--

CREATE TABLE `indirizzo` (
  `id_indirizzo` bigint(1) UNSIGNED NOT NULL,
  `descrizione` varchar(10) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `partecipa`
--

CREATE TABLE `partecipa` (
  `relazione` varchar(300) DEFAULT NULL,
  `rif_viaggio` bigint(20) REFERENCES viaggio(id_viaggio) NOT NULL,
  `rif_docente` bigint(20) REFERENCES docente(id_docente) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `partecipa`
--

INSERT INTO `partecipa` (`relazione`, `rif_viaggio`, `rif_classe`) VALUES
(NULL, 0, 0);

-- --------------------------------------------------------

--
-- Struttura della tabella `proposta`
--

CREATE TABLE `proposta` (
  `id_proposta` bigint(10) UNSIGNED NOT NULL,
  `descrizione` varchar(100) CHARACTER SET armscii8 COLLATE armscii8_general_ci DEFAULT NULL,
  `data_creazione` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `sotituzione`
--

CREATE TABLE `sotituzione` (
  `sostituto` tinyint(1) NOT NULL
  `rif_proposta` bigint(20) REFERENCES proposta(id_proposta) NOT NULL,
  `rif_docente` bigint(20) REFERENCES docente(id_docente) NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `tipo`
--

CREATE TABLE `tipo` (
  `id_tipo` bigint(20) UNSIGNED NOT NULL,
  `descrizione` varchar(30) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struttura della tabella `utente`
--

CREATE TABLE `utente` (
  `id` bigint(11) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `group_id` bigint(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `utente`
--

INSERT INTO `utente` (`id`, `username`, `password_hash`, `group_id`) VALUES
(1, 'admin_user', '$2y$10$6s00YYHczdd/MtQu1Ks.8eQVHFVTkfcoVnGfcFAN8adJj6jFLJQp2', 1),
(2, 'standard_user', '$2y$10$1jK1AoMovQO8cApX.Lcc2uYor52rr.2H5QPn.7bEAtyKugNR0e6BC', 2),
(3, 'pippo', '$2y$10$aKrgftaLq0X0H.8gkv3D3eL/cy1l.0bz79lQJ9akoWoSChevmOdu2', 2);

-- --------------------------------------------------------

--
-- Struttura della tabella `viaggio`
--

CREATE TABLE `viaggio` (
  `id_viaggio` bigint(10) UNSIGNED NOT NULL,
  `descrizione` varchar(100) CHARACTER SET armscii8 COLLATE armscii8_general_ci DEFAULT NULL,
  `data_inizio` date NOT NULL,
  `data_fine` date NOT NULL,
  `mezzo` varchar(15) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL,
  `destinazione` varchar(20) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indici per le tabelle scaricate
--

--
-- Indici per le tabelle `bozza`
--
ALTER TABLE `bozza`
  ADD PRIMARY KEY (`id_bozza`);

--
-- Indici per le tabelle `classe`
--
ALTER TABLE `classe`
  ADD PRIMARY KEY (`id_classe`);

--
-- Indici per le tabelle `docente`
--
ALTER TABLE `docente`
  ADD PRIMARY KEY (`id_docente`);

--
-- Indici per le tabelle `gruppo`
--
ALTER TABLE `gruppo`
  ADD PRIMARY KEY (`id`);

--
-- Indici per le tabelle `indirizzo`
--
ALTER TABLE `indirizzo`
  ADD PRIMARY KEY (`id_indirizzo`);

--
-- Indici per le tabelle `proposta`
--
ALTER TABLE `proposta`
  ADD PRIMARY KEY (`id_proposta`);

--
-- Indici per le tabelle `tipo`
--
ALTER TABLE `tipo`
  ADD PRIMARY KEY (`id_tipo`);

--
-- Indici per le tabelle `utente`
--
ALTER TABLE `utente`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `group_id` (`group_id`);

--
-- Indici per le tabelle `viaggio`
--
ALTER TABLE `viaggio`
  ADD PRIMARY KEY (`id_viaggio`);

--
-- AUTO_INCREMENT per le tabelle scaricate
--

--
-- AUTO_INCREMENT per la tabella `gruppo`
--
ALTER TABLE `gruppo`
  MODIFY `id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT per la tabella `utente`
--
ALTER TABLE `utente`
  MODIFY `id` bigint(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `utente`
--
ALTER TABLE `utente`
  ADD CONSTRAINT `utente_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `gruppo` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
