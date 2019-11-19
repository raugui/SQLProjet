<?php include("header.php"); ?>
<html>
<body>
  <main role="main" class="container">
    <h1 class="mt-5 col-sm-6 offset-sm-3"><p><u>Classement :</u></h1></p>
  </main>
  <div class="container">
    <div class="row">

  <?php

// On initialise les variables que l'on utilisera plus tard
$place = 1;
$equipes = null;
$count = 5 ;
session_start();
include('connect.php');
$message = null;
if(isset($_SESSION['message'])) {
  $message = $_SESSION['message'];
  unset($_SESSION['message']);
}
if(isset($_GET['ID_eq'])) {
  $equipesSth = $connexion->prepare("select * from equipes where ID_eq=:id");
  $equipesSth->execute([
    'id' => $_GET['ID_eq'],
  ]);
    $equipes = $equipesSth->fetch(PDO::FETCH_ASSOC);
}
/* REQUETE PREPAREE */
/////////////////////////////////////////////////////////////////////////////////////////////////////
  // Requete nombre de victoire ( Requete preparée)
  $RequeteGagnant = $connexion->prepare("call Classement");
  $RequeteGagnant->execute();

  $rq = $RequeteGagnant->fetchAll(PDO::FETCH_ASSOC);
  $RequeteGagnant->closeCursor();

  // On compte le nombre d'équipes ( requete preparee ):

  $NbEquipe = $connexion->prepare("call NbEquipes(@resultat);");
  $NbEquipe->execute();
  $NbEquipe = $connexion->prepare("select @resultat;");
  $NbEquipe->execute();
  $count = $NbEquipe->fetch();



// Resultat final pour une equipe :

  echo "<table border=3 class='table table-striped table-dark col-sm-12 '>
          <thead>
            <tr>
              <th>Place</th>
              <th>Nom de l'équipe</th>
              <th>Matchs joués</th>
              <th>Victoire</th>
              <th>Defaite</th>
              <th>Nul</th>
              <th>But Pour</th>
              <th>But contre</th>
              <th>Différence de buts</th>
              <th>Points</th>
            </tr>
          </thead>
  ";
  foreach ($rq as $classement){
    echo "<tr";
    // Si la place de l'equipe est egale a la 1e place, alors on la met en bleu 
    if($place == 1 ): echo " class='bg-primary' ";
    endif;
    // Si la place de l'equipe est egale a la derniere place, alors on la met en danger ( en rouge )
    if($place == $count[0] ): echo " class='bg-danger' ";
    endif;
    echo ">
        <td";
        if($equipes['Nom_eq'] == $classement['Nom_eq']):
          echo " style='background:green' ";
        endif;
          echo "><center>".($place++)."</center></td>
        <td ";
        if($equipes['Nom_eq'] == $classement['Nom_eq']):
          echo " style='background:green' ";
        endif;
          echo "><center>".$classement['Nom_eq']."</center></td>
        <td";
        if($equipes['Nom_eq'] == $classement['Nom_eq']):
          echo " style='background:green' ";
        endif;
          echo "><center>".$classement['Matchs_joues']."</center></td>
        <td";
        if($equipes['Nom_eq'] == $classement['Nom_eq']):
          echo " style='background:green' ";
        endif;
          echo "><center>".$classement['Victoires']."</center></td>
        <td";
        if($equipes['Nom_eq'] == $classement['Nom_eq']):
          echo " style='background:green' ";
        endif;
          echo "><center>".$classement['Defaites']."</center></td>
        <td";
        if($equipes['Nom_eq'] == $classement['Nom_eq']):
          echo " style='background:green' ";
        endif;
          echo "><center>".$classement['Match_nul']."</center></td>
        <td";
        if($equipes['Nom_eq'] == $classement['Nom_eq']):
          echo " style='background:green' ";
        endif;
          echo "><center>".$classement['BM']."</center></td>
        <td";
        if($equipes['Nom_eq'] == $classement['Nom_eq']):
          echo " style='background:green' ";
        endif;
          echo "><center>".$classement['BE']."</center></td>
        <td";
        if($equipes['Nom_eq'] == $classement['Nom_eq']):
          echo " style='background:green' ";
        endif;
          echo "><center>".$classement['+/-']."</center></td>
        <td";
        if($equipes['Nom_eq'] == $classement['Nom_eq']):
          echo " style='background:green' ";
        endif;
          echo "><center>".$classement['Points']."</center></td>
      </tr>";

  }



  echo "</table>";

if ($equipes['ID_eq']):
?></div></div>
  <div>
    <a href="equipe_edit.php?ID_eq=<?= $equipes['ID_eq'] ?>">
      <button type="button" class='btn btn-primary btn-lg btn-block'>Retour à l'équipe</button>
    </a>
  </div>

<?php endif;?>
  <h4> Propriété du tableau </h4><br/>
  <div class='btn btn-primary'> Le montant </div>
  <div class='btn btn-danger'> Le descendant </div>
  <div class='btn btn-success'> Votre équipe </div>
