<?php include("header.php"); ?>
<html>
<body>
  <?php
$equipes2 = null;
$equipes = null;
$drap= true;
session_start();
include('connect.php');
$message = null;
if(isset($_SESSION['message'])) {
  $message = $_SESSION['message'];
  unset($_SESSION['message']);
}
// on récupère info de l'equipe domicile
if(isset($_GET['ID_eq'])) {
  $equipesSth = $connexion->prepare("select * from equipes where ID_eq=:id");
  $equipesSth->execute([
    'id' => $_GET['ID_eq'],
  ]);
  // Si pas de valeur de retour, c'est que l'ID d'équipe n'existe pas
  if(0 === $equipesSth->rowCount()) {
    header('location: index.php');
  }
  $equipes = $equipesSth->fetch(PDO::FETCH_ASSOC);
}
// On récupère info de l'équipe exterieur
if(isset($_GET['ID_eq2'])) {
  $equipes2Sth = $connexion->prepare("select * from equipes where ID_eq=:id");
  $equipes2Sth->execute([
    'id' => $_GET['ID_eq2'],
  ]);
  $equipes2 = $equipes2Sth->fetch(PDO::FETCH_ASSOC);
}

// Si  le nom de la deuxieme equipe est selectionné
if(isset($_POST['Nom_eq2'])){
  // On récupère l' ID de l'équipe a domicile
  $equipes1Sth = $connexion->prepare("select * from equipes where ID_eq=:id");
  $equipes1Sth->execute([
    'id' => $_GET['ID_eq'],
  ]);
  $equipes1 = $equipes1Sth->fetch(PDO::FETCH_ASSOC);
  // On récupère l'ID de l'équipe a lexterieur
  $equipes2Sth = $connexion->prepare("select * from equipes where Nom_eq=:Nom_eq2");
  $equipes2Sth->execute([
    'Nom_eq2' => $_POST['Nom_eq2'],
  ]);
  $equipes2 = $equipes2Sth->fetch(PDO::FETCH_ASSOC);


  echo '
      <br/><br/><h1> Score du match : </h1>';?>
    <form action="match.php<?= (isset($equipes['ID_eq'])) ? '?ID_eq='.$equipes['ID_eq'].'&ID_eq2='.$equipes2['ID_eq'] : '' ?>" method="POST"><?php
  echo '<table class="table-dark"><tr><td><b>'.$equipes1['Nom_eq'].'</b>: </td><td><input type="text" name="equipeD" size="3"></td></tr>
    <tr><td><b>'.$equipes2['Nom_eq'].'</b>: </td><td><input type="text" name="equipeE" size="3"></tr></td>
    <tr><td colspan="2"><input type="submit" class="btn btn-primary" name="resultat" value="Go">
    </form>';

}

// Si le résultat est envoyé
else if(isset($_POST['resultat'])){
  // echo 'ok';
  $equipes2['ID_eq2'] = $_GET['ID_eq2'];
  $equipes1 = $equipes;
  //  equipe 1
  $butD = htmlspecialchars($_POST['equipeD']);
  // equipe 2
  $butE = htmlspecialchars($_POST['equipeE']);

  if((($_POST['equipeD'])!='')&&(($_POST['equipeE'])!='')){
  // equipê 1 informations du statut du stats
  if ($equipes['ID_eq']){

    $data = [
      'But_dom' => $butD,
      'But_ext' => $butE,
      'ID_eq_dom' => $equipes1['ID_eq'],
      'ID_eq_ext' => $equipes2['ID_eq2'],
    ];
    $insertionEq = "INSERT into stats (But_dom, But_ext, ID_eq_dom, ID_eq_ext)
    values (:But_dom, :But_ext, :ID_eq_dom, :ID_eq_ext)";
    $message = '<h2>Vos données de match sont enregistrée</h2><br/>';
  }

  //preparation de la requête
  $Equipe = $connexion->prepare($insertionEq);
  $Equipe->execute($data);
  if (0 < $Equipe->rowCount()){
    $_SESSION['message'] =[ 'message' => $message,];
  }
  else{
    $errorMessage = $Equipe->errorInfo();
    $message =$errorMessage[2];
  }

}

  else{
    $message ="<br/>Vous n'avez pas indiqué correctement le score.";
    $drap=false;
  }
  if($drap==false){?>
    ?><br/><br/><br/><div class='alert alert-danger'>
    <?php echo $message ;?>
    <a href="match.php?ID_eq=<?= $equipes['ID_eq']?>">
      <button type="button" class='btn btn-outline-info'>Retour</button>
    </a><?php
  }
  // Sinon on retourne a l'accueil
  else{
    ?><br/><br/><br/><div class='alert alert-success'>
    <?php echo $message ;?>

<form action="classement.php<?= (isset($equipes['ID_eq'])) ? '?ID_eq='.$equipes['ID_eq'] : '' ?>" method="post">
  <div>
    <div>
      <a href="classement.php?ID_eq=<?= $equipes['ID_eq'] ?>">
        <button type="submit" class='btn btn-outline-info'>Classement</button>
      </a>
    </div>
  </div>
  <div></br>
    <a href="index.php">
      <button type="button" class='btn btn-outline-info'>Retour Accueil</button>
    </a>
  </div>


  <?php
}


}

else{
  // on prépare la requete et on classe part ordre alphabétique
  $equipesSth2 = $connexion->prepare("select Nom_eq from equipes Order by Nom_eq");
  //on execute la requête
  $equipesSth2->execute();
  // Titre de la page
  echo "
  <main role='main' class='container'>
    <h3 class='mt-5 col-sm-8 offset-sm-4'><u><br/>Match :</u>
  </main>";
?>
  <form action="match.php<?= (isset($equipes['ID_eq'])) ? '?ID_eq='.$equipes['ID_eq'] : '' ?>" method="POST">
  <div class='btn btn-primary btn-lg'>
    <label for="nomeq" >Nom de votre équipe : <b></label> <?= $equipes['Nom_eq']?></b>
  </div></br></br>
  <div class='btn btn-info dropdown-toggle'>
    <!-- Menu déroulant choix équipe adverse -->
    <label for="nomeq" >Choisissez le nom de l'équipe adverse :
      <select name="Nom_eq2">
 <?php // Tant qu'il y'a des equipes dans la base de données, on les affiches dans la liste déroulante
 while ($equipes2 = $equipesSth2 -> fetch()) {
            ?>
                <option class="dropdown-item">
               <?php
               if ($equipes2['Nom_eq']== $equipes['Nom_eq']){

               }
               else{
               echo $equipes2['Nom_eq'];}?>
               <?php } ?>

             </option>
      </select>
    </label>
  </div>
      <br/><br/><div>
        <a href="match.php?Nom_eq<?=$equipes['Nom_eq']?>& Nom_eq2<?=$equipes2['Nom_eq']?>">
          <button type="submit" value="Envoi" name="Choix" class='btn btn-primary btn-sm'>Envoi</button>
        </a>
      </div>
  </form>
<?php } ?>

  <br/><div>
    <a href="equipe_edit.php?ID_eq=<?= $equipes['ID_eq'] ?>">
      <button type="button" class='btn btn-outline-info'>Retour à l'équipe</button>
    </a>
  </div><?php
