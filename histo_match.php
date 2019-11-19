<?php include("header.php"); ?>
<html>
<body>
  <?php

// On initialise les variables que l'on utilisera plus tard
$place = 1;
$equipes = null;
$ct = 0;
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

$statsD = $connexion->prepare("SELECT * FROM stats as st JOIN equipes as eqd ON st.ID_eq_dom = eqd.ID_eq order by ID_stats");
$statsE = $connexion->prepare("SELECT * FROM `stats` as st JOIN equipes as eqe on st.ID_eq_ext = eqe.ID_eq order by ID_stats");

$statsD->execute();
$statsDO = $statsD->fetchAll(PDO::FETCH_ASSOC);
$statsE->execute();
$statsEX = $statsE->fetchAll(PDO::FETCH_ASSOC);

/* REQUETE PREPAREE */
/////////////////////////////////////////////////////////////////////////////////////////////////////
  // Requete nombre de victoire ( Requete preparée)

$cpt = $connexion->prepare("SELECT count(*) from stats");
$cpt->execute();
$cptt = $cpt->fetch(PDO::FETCH_ASSOC);

$ct = $cptt;
$ct = implode($ct);

// Resultat final pour une equipe :
  echo "<br/><br/><h1> Historique des matchs : </h1>";
  echo "<table border=1 class='table table-striped table-dark col-sm-2 offset-sm'>
          <thead>
            <tr>
              <th>Matchs</th>
              <th>Nom équipe domicile</th>
              <th>But équipe domicile</th>
              <th>But équipe extérieur</th>
              <th>Nom équipe extérieur</th>
            </tr>
          </thead>
  ";
for ($i=0;$i<$ct;$i++){
  echo "<tr>";
    echo "
        <td";
        if($equipes['Nom_eq'] == $statsDO[$i]['Nom_eq']):
          echo " style='background:green' ";
        endif;
          echo "><center>".($place++)."</center></td>
        <td";

        if($equipes['Nom_eq'] == $statsDO[$i]['Nom_eq']):
          echo " style='background:green' ";
        endif;
          echo "><center>".$statsDO[$i]['Nom_eq']."</center></td>
        <td";

        if($equipes['Nom_eq'] == $statsDO[$i]['Nom_eq']):
          echo " style='background:green' ";
        endif;
          echo "><center>".$statsDO[$i]['But_dom']."</center></td>";

      echo "<td";
        if($equipes['Nom_eq'] == $statsEX[$i]['Nom_eq']):
          echo " style='background:green' ";
        endif;
          echo "><center>".$statsEX[$i]['But_ext']."</center></td>
        <td";

        if($equipes['Nom_eq'] == $statsEX[$i]['Nom_eq']):
          echo " style='background:green' ";
        endif;
          echo "><center>".$statsEX[$i]['Nom_eq']."</center></td>";

  echo "</tr>";
}
  echo "</table>";

if ($equipes['ID_eq']):
?>
  <br/><div>
    <a href="equipe_edit.php?ID_eq=<?= $equipes['ID_eq'] ?>">
      <button type="button" class='btn btn-primary'>Retour à l'équipe</button>
    </a>
  </div>
<?php endif;?>
  <br/><div>
    <a href="index.php">
      <button type="button" class='btn btn-outline-primary'>Retour à l'accueil</button>
    </a>
  </div>

  <?php
