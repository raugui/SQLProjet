<?php include("header.php"); ?>
<html>
<body>
  <?php
session_start();
include('connect.php');
$drap= true;
$message = null;
if(isset($_SESSION['message'])) {
  $message = $_SESSION['message'];
  unset($_SESSION['message']);
}
$equipes = null;
$joueurs=null;

//verifier si l'id d'une equipe est envoyé
if(isset($_GET['ID_eq'])) {
  $equipesSth = $connexion->prepare("select * from equipes where ID_eq=:id");
  $equipesSth->execute([
    'id' => $_GET['ID_eq'],
  ]);
  $joueursSth = $connexion->prepare("select * from joueurs where ID_eq=:id");
  $joueursSth->execute([
    'id' => $_GET['ID_eq'],
  ]);
  // Si pas de valeur de retour, c'est que l'ID d'équipe n'existe pas
  if(0 === $equipesSth->rowCount()) {
    header('location: index.php');
  }
  $equipes = $equipesSth->fetch(PDO::FETCH_ASSOC);
  $joueurs = $joueursSth->fetchAll(PDO::FETCH_ASSOC);
}


  echo "<main role='main' class='container'>
    <h1 class='mt-5 col-sm offset-sm'><u>Liste des joueurs du club ".$equipes['Nom_eq'].":</u></h1>
  </main>";

  echo "<table class='table table-striped table-dark'>
          <thead class='thead-inverse'>
            <tr>
              <th>Nom</th>
              <th>Prenom</th>
              <th>Date de naissance</th>
              <th>Action</th>
            </tr>
          </thead>
  ";

  foreach($joueurs as $joueur){
    echo   "<tr>
        <td>".htmlspecialchars($joueur['Nom_joueur'])."</td>
        <td>".htmlspecialchars($joueur['Prenom_joueur'])."</td>
  			<td>".htmlspecialchars($joueur['Age_joueur'])."</td>
        <td>
          <a href='joueur_supprimer.php?ID_eq=".$equipes['ID_eq']."&id=".$joueur['ID_joueur']."'>
            <button class='btn btn-danger'>Supprimer</button>
          </a>
          <a href='joueur_edit.php?ID_eq=".$equipes['ID_eq']."&id=".$joueur['ID_joueur']."'>
            <button class='btn btn-primary'>Editer</button>
          </a>
        </td>
      </tr>";

}
  echo "</table>";

?>
<form
action="joueur_edit.php<?= (isset($equipes['ID_eq'])) ? '?ID_eq='.$equipes['ID_eq'] : '' ?>" method="post">

<!-- // Si l'ID de l'equipe est envoyé, alors on peut ajouter un joueur -->
<?php if (isset($equipes['ID_eq'])): ?>
  <br/><div>
    <div>
      <a href="joueur_edit.php?ID_eq=<?= $equipes['ID_eq'] ?>">
      <button type="button" class='btn btn-secondary col-sm offset-sm'>Ajouter un joueur</button>
      </a>
    </div>
<?php endif; ?>

  <br/><div>
    <a href="equipe_edit.php?ID_eq=<?= $equipes['ID_eq'] ?>">
      <button class='btn btn-outline-info' type="button">Retour à l'équipe</button>
    </a>
  </div>
</form>
