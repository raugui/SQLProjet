/* On garde un historique des nouveaux joueurs inséré */
/* a l'insertion */
DELIMITER $$
CREATE TRIGGER histo_joueurs_insert
	AFTER INSERT ON `joueurs`FOR EACH ROW
    BEGIN
		INSERT INTO `Histo_joueurs`(Statut,date_insertion,Nom_joueur,Prenom_joueur,Age_joueur,ID_eq)
        VALUES ('INSERT',now(),NEW.Nom_joueur,NEW.Prenom_joueur,NEW.Age_joueur,NEW.ID_eq);
	END;
$$

drop trigger histo_joueurs_insert

/* a la modification */

DELIMITER $$
CREATE TRIGGER histo_joueurs_update
	AFTER UPDATE ON `joueurs`FOR EACH ROW
    BEGIN
		INSERT INTO `Histo_joueurs`(Statut,date_insertion,Nom_joueur,Prenom_joueur,Age_joueur,ID_eq)
        VALUES ('UPDATE',now(),NEW.Nom_joueur,NEW.Prenom_joueur,NEW.Age_joueur,NEW.ID_eq);
	END;
$$

/* a la suppression */ 

DELIMITER $$
CREATE TRIGGER histo_joueurs_delete
	AFTER delete ON `joueurs`FOR EACH ROW
    BEGIN
		INSERT INTO `Histo_joueurs`(Statut,date_insertion,Nom_joueur,Prenom_joueur,Age_joueur,ID_eq)
        VALUES ('DELETE',now(),OLD.Nom_joueur,OLD.Prenom_joueur,OLD.Age_joueur,OLD.ID_eq);
	END;
$$

drop trigger histo_joueurs;
/* On crée la table d'historisation */
CREATE TABLE Histo_joueurs(
ID_histo INT NOT NULL AUTO_INCREMENT,
date_insertion datetime not null,
Nom_joueur varchar(100) not null,
Prenom_joueur varchar(100) not null,
Age_joueur DATE not null,
ID_eq int not null,
primary key(ID_histo));

drop table Histo_joueurs;


/* On verifie que le nom d'équipe n'existe pas avant de l'ajouter */
DELIMITER $$
CREATE TRIGGER verif_equipe
	BEFORE INSERT ON `equipes` FOR EACH ROW
    BEGIN
		if (SELECT count(*) from `equipes` where Nom_eq = NEW.Nom_eq)
        then SIGNAL SQLSTATE VALUE '45000' SET MESSAGE_TEXT = 'Ce nom d\'equipe existe deja';
        end if;
	END
$$

drop trigger verif_equipe

/* On vérifie le nombre max de joueurs que l'équipe possède */

      DELIMITER $$
CREATE TRIGGER verif_joueurs
	BEFORE INSERT ON `joueurs` FOR EACH ROW
    BEGIN
		if (SELECT count(*)>10 from `joueurs` where ID_eq= NEW.ID_eq)
        then SIGNAL SQLSTATE VALUE '45000' SET MESSAGE_TEXT = 'Vous avez atteint la limite maximum de joueurs';
        end if;
	END
$$

drop trigger verif_joueurs

/* On vérifie si tout éléments sont envoyé */
/* joueurs */
DELIMITER $$
CREATE TRIGGER `joueurs_update`
	BEFORE UPDATE ON `joueurs` FOR EACH ROW
	BEGIN
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
				SET MESSAGE_TEXT = 'l\'age est obligatoire.';
		END IF;
	END
$$

drop trigger joueurs_update

/* equipes */
DELIMITER $$
CREATE TRIGGER `equipes_update`
	BEFORE UPDATE ON `equipes` FOR EACH ROW
	BEGIN
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

drop trigger equipes_update


/* donnée de match a verifier */

DELIMITER $$
CREATE TRIGGER `insert_match`
	BEFORE INSERT ON `stats` FOR EACH ROW
	BEGIN
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

drop trigger insert_match

/* On affiche le dernier match enregistré */ 

DELIMITER $$
Create trigger supp_joueurs
	AFTER DELETE ON joueurs FOR EACH ROW
    BEGIN
		DECLARE ID_eq_dom int;
		DELETE * from stats 
        ORDER BY ID_stats DESC 
        LIMIT 1 ;
    END 
$$
		
		