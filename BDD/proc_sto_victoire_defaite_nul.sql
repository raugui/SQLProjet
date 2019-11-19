
delimiter $$
Create procedure gagnant (IN ID_eq INT,OUT resultat INT)
BEGIN
 select count(*) into resultat from stats where (ID_eq = ID_eq_dom  and But_dom > But_ext) OR 
	(ID_eq = ID_eq_ext  and But_ext > But_dom);
END $$

call gagnant(56,@resultat);

select @resultat as victoire;

delimiter $$
Create procedure nul (IN ID_eq INT,OUT resultat INT)
BEGIN
 select count(*) into resultat from stats where (ID_eq = ID_eq_dom  and But_dom = But_ext) OR 
	(ID_eq = ID_eq_ext  and But_ext = But_dom);
END $$

delimiter $$
Create procedure defaite (IN ID_eq INT,OUT resultat INT)
BEGIN
 select count(*) into resultat from stats where (ID_eq = ID_eq_dom  and But_dom < But_ext) OR 
	(ID_eq = ID_eq_ext  and But_ext < But_dom);
END $$


/* utilisé : */

delimiter $$
Create procedure NbEquipes (OUT resultat INT)
BEGIN
 select count(*) into resultat from equipes ;
END $$

call test(@resultat);
select @resultat;


delimiter $$
create procedure Classement ()
BEGIN

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

END $$

drop procedure Classement;

call Classement;
