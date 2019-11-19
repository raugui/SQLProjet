<?php include("header.php"); ?>
<html>
<body>
  <?php
//démarre une nouvelle session ou reprend une session existante
session_start();
//recuperation de la connexion à la BDD
include('connect.php');
$message='';
$id = $_GET['id'] ?? null;

if(isset($_GET['ID_eq'])) {
  $equipesSth = $connexion->prepare("select * from equipes where ID_eq=:id");
  $equipesSth->execute([
    'id' => $_GET['ID_eq'],
  ]);
  $equipes = $equipesSth->fetch(PDO::FETCH_ASSOC);
}
//si l'id du joueur n'est pas null, on va le supprimer
if(null !== $id) {
//le template de la requête sql
$requeteSuppjoueurs = "delete from joueurs where ID_joueur = :id";
//preparation de la requête
$suppressionSth =
$connexion->prepare($requeteSuppjoueurs);
//liaison du nom ':id' à la variable id
$suppressionSth->bindParam('id', $id, PDO::PARAM_INT);

//execution de la requête
$suppressionSth->execute();

//Retourne le nombre de lignes affectées par le dernier appel à la fonction PDOStatement::execute()
$rowCount = $suppressionSth->rowCount();

//s'il y a au moins une ligne qui a été supprimée on indique un message
if(0 < $rowCount) {
  $message = "Le joueur a été supprimé";
}
?><br/><br/><br/><div class='alert alert-success'>
<?php echo $message ;?>
</div>
<div>
  <a href="joueur_consult.php?ID_eq=<?= $equipes['ID_eq'] ?>">
    <button type="button" class='btn btn-primary'>Retour</button>
  </a>
</div>
<?php
}
