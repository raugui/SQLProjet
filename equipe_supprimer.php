<?php include("header.php"); ?>
<html>
<body>
  <?php
//démarre une nouvelle session ou reprend une session existante
session_start();
//recuperation de la connexion à la BDD
include('connect.php');
$message='';

if(isset($_GET['ID_eq'])) {
  $_SESSION['id'] = $_GET['ID_eq'];
  ?>
<br/><br/><br/><div class='alert alert-primary'><b>


Etes-vous sur de vouloir supprimer ?</div></b>
  <br/><a href="equipe_supprimer.php">
    <button class='btn btn-danger col-sm-2 offset-sm-4' type="submit">Confirmer</button>
  </a><br/>
  <?php
}
else{
  $id = $_SESSION['id'];
//si l'id n'est pas null, on va supprimer l'enregistrement
if(null !== $id) {
//le template de la requête sql
$requeteSuppequipes = "delete from equipes where ID_eq = :id";
//preparation de la requête
$suppressionSth =
$connexion->prepare($requeteSuppequipes);
//liaison du nom ':id' à la variable id
$suppressionSth->bindParam('id', $id, PDO::PARAM_INT);

//execution de la requête
$suppressionSth->execute();

//Retourne le nombre de lignes affectées par le dernier appel à la fonction PDOStatement::execute()
$rowCount = $suppressionSth->rowCount();

//s'il y a au moins une ligne qui a été supprimée on indique un message
if(0 < $rowCount) {
  $message = "L'équipe a été supprimé";
  ?><br/><br/><br/><div class='alert alert-success'>
  <?php echo $message ;?>
</div><?php
}
else{
  ?><br/><br/><br/><div class='alert alert-danger'>
  <?php echo $message ;?>
</div><?php
} }
}?>

<br/><br/><br/><div>
  <a href="index.php">
    <button type="button" class='btn btn-primary col-sm-2 offset-sm-4'>Retour</button>
  </a>
</div>
<?php
