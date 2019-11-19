-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  sam. 16 juin 2018 à 13:47
-- Version du serveur :  5.7.19
-- Version de PHP :  7.0.23

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `football`
--

DELIMITER $$
--
-- Procédures
--
DROP PROCEDURE IF EXISTS `Classement`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `Classement` ()  BEGIN

select eq.Nom_eq , 

SUM(CASE (eq.ID_eq = st.ID_eq_ext) OR (eq.ID_eq = st.ID_eq_dom) WHEN eq.ID_eq != 0 Then 1 ELSE 0 END) as Matchs_joues,

SUM(CASE WHEN st.ID_eq_dom = eq.ID_eq  THEN st.But_dom ELSE 0 END) + SUM(CASE WHEN st.ID_eq_ext = eq.ID_eq  THEN st.But_ext ELSE 0 END) AS BM,
	
SUM(CASE WHEN st.ID_eq_dom = eq.ID_eq  THEN st.But_ext ELSE 0 END) + SUM(CASE WHEN st.ID_eq_ext = eq.ID_eq  THEN st.But_dom ELSE 0 END) AS BE,

((SUM(CASE WHEN st.ID_eq_dom = eq.ID_eq  THEN st.But_dom ELSE 0 END) + SUM(CASE WHEN st.ID_eq_ext = eq.ID_eq  THEN st.But_ext ELSE 0 END)) - 
(SUM(CASE WHEN st.ID_eq_dom = eq.ID_eq  THEN st.But_ext ELSE 0 END) + SUM(CASE WHEN st.ID_eq_ext = eq.ID_eq  THEN st.But_dom ELSE 0 END))) AS '+/-',

SUM(CASE (eq.ID_eq = st.ID_eq_dom) or (eq.ID_eq = st.ID_eq_ext) WHEN ((eq.ID_eq = st.ID_eq_dom) AND (st.But_dom > st.But_ext)) Then 1 
WHEN ((eq.ID_eq = st.ID_eq_ext) AND (st.But_dom < st.But_ext)) Then 1
ELSE 0 END) AS Victoires,

SUM(CASE (eq.ID_eq = st.ID_eq_dom) or (eq.ID_eq = st.ID_eq_ext) WHEN ((eq.ID_eq = st.ID_eq_dom) AND (st.But_dom < st.But_ext)) Then 1
WHEN ((eq.ID_eq = st.ID_eq_ext) AND (st.BUT_ext < st.But_dom)) Then 1
ELSE 0 END)
AS Defaites,

SUM(CASE (eq.ID_eq = st.ID_eq_ext) OR (eq.ID_eq = st.ID_eq_dom) WHEN st.But_ext = st.But_dom Then 1 ELSE 0 END) AS Match_nul,

( SUM(CASE (eq.ID_eq = st.ID_eq_dom) or (eq.ID_eq = st.ID_eq_ext) WHEN ((eq.ID_eq = st.ID_eq_dom) AND (st.But_dom > st.But_ext)) Then 3 
WHEN ((eq.ID_eq = st.ID_eq_ext) AND (st.But_dom < st.But_ext)) Then 3 
ELSE 0 END) ) +
( SUM(CASE (eq.ID_eq = st.ID_eq_ext) OR (eq.ID_eq = st.ID_eq_dom) WHEN st.But_ext = st.But_dom Then 1 ELSE 0 END)) AS Points

from equipes as eq left join stats as st ON
(eq.ID_eq = st.ID_eq_dom) OR
(eq.ID_eq = st.ID_eq_ext)
group by ID_eq
order by Points DESC;

END$$

DROP PROCEDURE IF EXISTS `defaite`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `defaite` (IN `ID_eq` INT, OUT `resultat` INT)  BEGIN
 select count(*) into resultat from stats where (ID_eq = ID_eq_dom  and But_dom < But_ext) OR 
	(ID_eq = ID_eq_ext  and But_ext < But_dom);
END$$

DROP PROCEDURE IF EXISTS `gagnant`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `gagnant` (IN `ID_eq` INT, OUT `resultat` INT)  BEGIN
 select count(*) into resultat from stats where (ID_eq = ID_eq_dom  and But_dom > But_ext) OR 
	(ID_eq = ID_eq_ext  and But_ext > But_dom);
END$$

DROP PROCEDURE IF EXISTS `NbEquipes`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `NbEquipes` (OUT `resultat` INT)  BEGIN
 select count(*) into resultat from equipes ;
END$$

DROP PROCEDURE IF EXISTS `nul`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `nul` (IN `ID_eq` INT, OUT `resultat` INT)  BEGIN
 select count(*) into resultat from stats where (ID_eq = ID_eq_dom  and But_dom = But_ext) OR 
	(ID_eq = ID_eq_ext  and But_ext = But_dom);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `equipes`
--

DROP TABLE IF EXISTS `equipes`;
CREATE TABLE IF NOT EXISTS `equipes` (
  `ID_eq` int(11) NOT NULL AUTO_INCREMENT,
  `Nom_eq` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Adresse_eq` text COLLATE utf8_unicode_ci NOT NULL,
  `Tel_eq` int(11) NOT NULL,
  PRIMARY KEY (`ID_eq`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `equipes`
--

INSERT INTO `equipes` (`ID_eq`, `Nom_eq`, `Adresse_eq`, `Tel_eq`) VALUES
(56, 'JESPO', 'CHEMIN DU MOULIN DU BOEUF 1', 56557808),
(57, 'PLOEGSTEERT', '157 RUE D\'ARMENTIERES ', 56588856),
(58, 'HOUTHEM', 'CORNET DEN HAUT 17', 56558252),
(59, 'WERVIK', 'VIROVIOSLAAN 17', 55555555),
(60, 'MENIN', 'RUE DE LA CHOCOLATERIE', 5555555);

--
-- Déclencheurs `equipes`
--
DROP TRIGGER IF EXISTS `equipes_update`;
DELIMITER $$
CREATE TRIGGER `equipes_update` BEFORE UPDATE ON `equipes` FOR EACH ROW BEGIN
		IF NEW.`Nom_eq` is null or NEW.`Nom_eq`='' THEN
			SIGNAL SQLSTATE VALUE '42S22'
				SET MESSAGE_TEXT = 'Un champ doit être modifié obligatoirement pour mettre à jour.';
		ELSEIF NEW.`Adresse_eq` is null or NEW.`Adresse_eq`='' THEN
			SIGNAL SQLSTATE VALUE '42S22'
				SET MESSAGE_TEXT = 'Un champ doit être modifié obligatoirement pour mettre à jour.';
		ELSEIF NEW.`Tel_eq` is null or NEW.`Tel_eq`='' THEN
			SIGNAL SQLSTATE VALUE '42S22'
				SET MESSAGE_TEXT = 'Un champ doit être modifié obligatoirement pour mettre à jour.';
		END IF;
	END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `verif_equipe`;
DELIMITER $$
CREATE TRIGGER `verif_equipe` BEFORE INSERT ON `equipes` FOR EACH ROW BEGIN 	
		if (SELECT count(*) from `equipes` where Nom_eq = NEW.Nom_eq)
        then SIGNAL SQLSTATE VALUE '45000' SET MESSAGE_TEXT = "Ce nom d'equipe existe deja";
        end if;
	END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `histo_joueurs`
--

DROP TABLE IF EXISTS `histo_joueurs`;
CREATE TABLE IF NOT EXISTS `histo_joueurs` (
  `ID_histo` int(11) NOT NULL AUTO_INCREMENT,
  `Statut` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `date_insertion` datetime NOT NULL,
  `Nom_joueur` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Prenom_joueur` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Age_joueur` date NOT NULL,
  `ID_eq` int(11) NOT NULL,
  PRIMARY KEY (`ID_histo`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `histo_joueurs`
--

INSERT INTO `histo_joueurs` (`ID_histo`, `Statut`, `date_insertion`, `Nom_joueur`, `Prenom_joueur`, `Age_joueur`, `ID_eq`) VALUES
(2, '', '2018-06-03 17:31:11', 'DURIBREUX', 'GUILLAUME', '1990-12-31', 25),
(3, '', '2018-06-03 23:11:27', 'DURIBREUX', 'WILLIAM', '1111-11-11', 3),
(4, '', '2018-06-03 23:36:56', 'LALAL', 'ZZZ', '0333-03-31', 3),
(6, '', '2018-06-04 10:59:13', 'DURIBREUX', 'GUILLAUME', '1990-12-31', 25),
(7, '', '2018-06-08 14:09:43', 'DURIBREUX', 'GUILLAUME', '1990-12-31', 56),
(8, '', '2018-06-08 14:10:22', 'DESMARETZ', 'NICOLAS', '1983-04-21', 56),
(9, '', '2018-06-08 14:10:48', 'BOTERMAN', 'TIDJY', '1995-10-20', 56),
(10, '', '2018-06-08 14:11:06', 'BURGO', 'FLAVIEN', '1990-02-16', 56),
(11, '', '2018-06-08 14:11:40', 'KAESTEKER', 'WILLIAM', '1991-02-06', 56),
(12, '', '2018-06-08 14:12:19', 'GEVAERT', 'FABRICE', '1991-02-11', 57),
(13, '', '2018-06-08 14:12:53', 'HEYTE', 'FLORIAN', '1996-08-30', 57),
(14, '', '2018-06-08 14:13:13', 'ZAGULA', 'QUENTIN', '1993-09-28', 57),
(15, '', '2018-06-08 14:13:32', 'DUCHENE', 'KEVIN', '1991-01-07', 57),
(16, '', '2018-06-08 14:13:58', 'ZAGULA', 'EMMERIK', '1995-02-06', 57),
(17, '', '2018-06-08 14:14:29', 'RAMON', 'JEAN-BAPTISE', '1990-07-02', 58),
(18, '', '2018-06-08 14:14:55', 'TAVERNIER', 'DIMITRI', '1988-06-04', 58),
(19, '', '2018-06-08 14:15:09', 'LEMOINE', 'JEREMY', '1988-11-04', 58),
(20, '', '2018-06-08 14:15:34', 'DESMARETZ', 'SEBASTIEN', '1984-11-11', 58),
(21, '', '2018-06-08 14:16:08', 'VASSEUR', 'MAXIME', '1996-11-14', 58),
(22, '', '2018-06-08 14:16:25', 'SAIGOT', 'RUDY', '1996-03-24', 59),
(23, '', '2018-06-08 14:16:39', 'THOMAS', 'NOAH', '1999-11-11', 59),
(24, '', '2018-06-08 14:17:16', 'CHUIN', 'NOLAN', '1997-12-14', 59),
(25, '', '2018-06-08 14:17:37', 'JOYE', 'FLORIAN', '1992-08-14', 59),
(26, '', '2018-06-08 14:18:07', 'DUYCK', 'NICOLAS', '1999-11-11', 59),
(27, '', '2018-06-08 14:34:55', 'OUYOUMÃ¨D', 'HALAH', '1993-03-21', 60),
(28, '', '2018-06-08 14:35:07', 'AMNE', 'SIA', '1998-11-11', 60),
(29, '', '2018-06-08 14:35:30', 'SARAH', 'KROCHE', '1994-07-24', 60),
(30, '', '2018-06-08 14:35:46', 'BENJA', 'MAINS', '1998-11-14', 60),
(31, '', '2018-06-08 14:36:07', 'KEVIN', 'NOOB', '1999-07-31', 60),
(32, '', '2018-06-15 18:40:46', 'LULU', 'BERLU', '0222-02-22', 57),
(33, '', '2018-06-16 00:04:42', 'LULU', 'BERLU', '0222-02-22', 57),
(34, '', '2018-06-16 00:06:14', 'LULU', 'BERLU', '0222-02-22', 57),
(35, '', '2018-06-16 00:06:57', 'LULU', 'BERLU', '0222-02-22', 57),
(36, '', '2018-06-16 00:07:11', 'LULU', 'BERLU', '0222-02-22', 57),
(37, '', '2018-06-16 00:07:22', 'LULU', 'BERLU', '0222-02-22', 57),
(38, '', '2018-06-16 00:14:21', 'NON', 'JO', '2222-02-22', 57),
(39, 'UPDATE', '2018-06-16 15:01:18', 'DUCHENE', 'KEVIN', '1991-02-07', 57),
(40, 'UPDATE', '2018-06-16 15:02:01', 'DUCHENE', 'KEVIN', '1991-02-07', 57),
(41, 'UPDATE', '2018-06-16 15:02:13', 'DUCHENE', 'KEVIN', '1991-01-07', 57),
(42, 'INSERT', '2018-06-16 15:04:10', 'OLA', 'LAO', '1945-03-12', 57),
(43, 'INSERT', '2018-06-16 15:06:48', 'OUA', 'AEER', '0315-03-31', 58),
(44, 'DELETE', '2018-06-16 15:14:20', 'OUA', 'AEER', '0315-03-31', 58);

-- --------------------------------------------------------

--
-- Structure de la table `joueurs`
--

DROP TABLE IF EXISTS `joueurs`;
CREATE TABLE IF NOT EXISTS `joueurs` (
  `ID_joueur` int(11) NOT NULL AUTO_INCREMENT,
  `Nom_joueur` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Prenom_joueur` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Age_joueur` date NOT NULL,
  `ID_eq` int(11) DEFAULT NULL,
  PRIMARY KEY (`ID_joueur`),
  KEY `ID_eq_idx` (`ID_eq`)
) ENGINE=InnoDB AUTO_INCREMENT=110 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `joueurs`
--

INSERT INTO `joueurs` (`ID_joueur`, `Nom_joueur`, `Prenom_joueur`, `Age_joueur`, `ID_eq`) VALUES
(75, 'DURIBREUX', 'GUILLAUME', '1990-12-31', 56),
(76, 'DESMARETZ', 'NICOLAS', '1983-04-21', 56),
(77, 'BOTERMAN', 'TIDJY', '1995-10-20', 56),
(78, 'BURGO', 'FLAVIEN', '1990-02-16', 56),
(79, 'KAESTEKER', 'WILLIAM', '1991-02-06', 56),
(80, 'GEVAERT', 'FABRICE', '1991-02-11', 57),
(81, 'HEYTE', 'FLORIAN', '1996-08-30', 57),
(82, 'ZAGULA', 'QUENTIN', '1993-09-28', 57),
(83, 'DUCHENE', 'KEVIN', '1991-01-07', 57),
(84, 'ZAGULA', 'EMMERIK', '1995-02-06', 57),
(85, 'RAMON', 'JEAN-BAPTISTE', '1990-07-02', 58),
(86, 'TAVERNIER', 'DIMITRI', '1988-06-04', 58),
(87, 'LEMOINE', 'JEREMY', '1988-11-04', 58),
(88, 'DESMARETZ', 'SEBASTIEN', '1984-11-11', 58),
(89, 'VASSEUR', 'MAXIME', '1996-11-14', 58),
(90, 'SAIGOT', 'RUDY', '1996-03-24', 59),
(91, 'THOMAS', 'NOAH', '1999-11-11', 59),
(92, 'CHUIN', 'NOLAN', '1997-12-14', 59),
(93, 'JOYE', 'FLORIAN', '1992-08-14', 59),
(94, 'DUYCK', 'NICOLAS', '1999-11-11', 59),
(95, 'OUYOUMÃ¨D', 'HALAH', '1993-03-21', 60),
(96, 'AMNE', 'SIA', '1998-11-11', 60),
(97, 'SARAH', 'KROCHE', '1994-07-24', 60),
(98, 'BENJA', 'MAINS', '1998-11-14', 60),
(99, 'KEVIN', 'NOOB', '1999-07-31', 60),
(108, 'OLA', 'LAO', '1945-03-12', 57);

--
-- Déclencheurs `joueurs`
--
DROP TRIGGER IF EXISTS `histo_joueurs_delete`;
DELIMITER $$
CREATE TRIGGER `histo_joueurs_delete` AFTER DELETE ON `joueurs` FOR EACH ROW BEGIN
		INSERT INTO `Histo_joueurs`(Statut,date_insertion,Nom_joueur,Prenom_joueur,Age_joueur,ID_eq)
        VALUES ('DELETE',now(),OLD.Nom_joueur,OLD.Prenom_joueur,OLD.Age_joueur,OLD.ID_eq);
	END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `histo_joueurs_insert`;
DELIMITER $$
CREATE TRIGGER `histo_joueurs_insert` AFTER INSERT ON `joueurs` FOR EACH ROW BEGIN
		INSERT INTO `Histo_joueurs`(Statut,date_insertion,Nom_joueur,Prenom_joueur,Age_joueur,ID_eq)
        VALUES ('INSERT',now(),NEW.Nom_joueur,NEW.Prenom_joueur,NEW.Age_joueur,NEW.ID_eq);
	END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `histo_joueurs_update`;
DELIMITER $$
CREATE TRIGGER `histo_joueurs_update` AFTER UPDATE ON `joueurs` FOR EACH ROW BEGIN
		INSERT INTO `Histo_joueurs`(Statut,date_insertion,Nom_joueur,Prenom_joueur,Age_joueur,ID_eq)
        VALUES ('UPDATE',now(),NEW.Nom_joueur,NEW.Prenom_joueur,NEW.Age_joueur,NEW.ID_eq);
	END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `joueurs_update`;
DELIMITER $$
CREATE TRIGGER `joueurs_update` BEFORE UPDATE ON `joueurs` FOR EACH ROW BEGIN
		IF NEW.`Nom_joueur` is null or NEW.`Nom_joueur`='' THEN
			SIGNAL SQLSTATE VALUE '22007'
				SET MESSAGE_TEXT = 'le Nom est obligatoire.';
		END IF;
		IF NEW.`Prenom_joueur` is null or NEW.`Prenom_joueur`='' THEN
			SIGNAL SQLSTATE VALUE '22007'
				SET MESSAGE_TEXT = 'le Prenom est obligatoire.';
		END IF;
		IF NEW.`Age_joueur` is null  THEN
			SIGNAL SQLSTATE VALUE '22007'
				SET MESSAGE_TEXT = "l'age est obligatoire.";
		END IF;
	END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `verif_joueurs`;
DELIMITER $$
CREATE TRIGGER `verif_joueurs` BEFORE INSERT ON `joueurs` FOR EACH ROW BEGIN 	
		if (SELECT count(*)>10 from `joueurs` where ID_eq= NEW.ID_eq)
        then SIGNAL SQLSTATE VALUE '45000' SET MESSAGE_TEXT = 'Vous avez atteint la limite maximum de joueurs';
        end if;
	END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `stats`
--

DROP TABLE IF EXISTS `stats`;
CREATE TABLE IF NOT EXISTS `stats` (
  `ID_stats` int(11) NOT NULL AUTO_INCREMENT,
  `But_dom` int(11) NOT NULL,
  `But_ext` int(11) NOT NULL,
  `ID_eq_dom` int(11) NOT NULL,
  `ID_eq_ext` int(11) NOT NULL,
  PRIMARY KEY (`ID_stats`),
  KEY `ID_eq_idx` (`ID_eq_dom`),
  KEY `ID_eq_ext_idx` (`ID_eq_ext`)
) ENGINE=InnoDB AUTO_INCREMENT=117 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `stats`
--

INSERT INTO `stats` (`ID_stats`, `But_dom`, `But_ext`, `ID_eq_dom`, `ID_eq_ext`) VALUES
(105, 3, 2, 56, 58),
(106, 2, 2, 57, 60),
(107, 2, 1, 60, 59),
(108, 3, 3, 57, 56),
(109, 2, 3, 58, 59),
(110, 1, 3, 56, 60),
(111, 0, 2, 59, 56),
(112, 4, 0, 60, 58),
(113, 3, 2, 59, 57),
(114, 1, 2, 58, 57);

--
-- Déclencheurs `stats`
--
DROP TRIGGER IF EXISTS `insert_match`;
DELIMITER $$
CREATE TRIGGER `insert_match` BEFORE INSERT ON `stats` FOR EACH ROW BEGIN
		IF NEW.`But_dom` < 0 THEN
			SIGNAL SQLSTATE VALUE '45000'
				SET MESSAGE_TEXT = 'le nombre de buts est obligatoire pour les deux equipes.';
		END IF;
		IF NEW.`But_ext` < 0 THEN
			SIGNAL SQLSTATE VALUE '45000'
				SET MESSAGE_TEXT = 'le nombre de buts est obligatoire pour les deux equipes.';
		END IF;
	END
$$
DELIMITER ;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `joueurs`
--
ALTER TABLE `joueurs`
  ADD CONSTRAINT `ID_eq` FOREIGN KEY (`ID_eq`) REFERENCES `equipes` (`ID_eq`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `stats`
--
ALTER TABLE `stats`
  ADD CONSTRAINT `ID_eq_dom` FOREIGN KEY (`ID_eq_dom`) REFERENCES `equipes` (`ID_eq`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ID_eq_ext` FOREIGN KEY (`ID_eq_ext`) REFERENCES `equipes` (`ID_eq`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
