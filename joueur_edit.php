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
$joueursid = null;

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

  // On vérifie si on a l'id du joueur
}
if(isset($_GET['id'])){
  $joueursSth = $connexion->prepare("select * from joueurs where ID_joueur=:id");
  $joueursSth->execute([
    'id' => $_GET['id'],
  ]);
  // Si pas de valeur de retour, c'est que l'ID du joueur n'existe pas
  $joueursid = $joueursSth->fetch(PDO::FETCH_ASSOC);
   // var_dump($joueursid);
}

//si le formulaire est envoyé on ajoute les informations en BDD
if($_POST) {
  // echo 'ok';
  if(($_POST['Nom_joueur']!= '')&&($_POST['Prenom_joueur']!= '')&&($_POST['Age_joueur'] !='')){
    $data = [
      'Nom_joueur' => strtoupper($_POST['Nom_joueur']),
      'Prenom_joueur' => strtoupper($_POST['Prenom_joueur']),
      'Age_joueur' => $_POST['Age_joueur'],
    ];
    // var_dump($data);
    //Convertit tous les caractères éligibles en entités HTML
    // echo 'ok';
    $Nom_joueur = htmlspecialchars(strtoupper($_POST['Nom_joueur']));
    $Prenom_joueur = htmlspecialchars(strtoupper($_POST['Prenom_joueur']));
    $Age_joueur = $_POST['Age_joueur'];
    //Si le joueur existe on le met à jour.
    if($_GET['id']) {
        $requetejoueurs = "UPDATE joueurs set Nom_joueur=:Nom_joueur, Prenom_joueur=:Prenom_joueur, Age_joueur=:Age_joueur
        where ID_joueur=:id";
        $data['id'] = $_GET['id'];
          // var_dump($data);
        $message= "Le joueur <b>$Nom_joueur </b>a été modifié<br/>";
    }
    //si le joueur n'existe pas on crée un nouvel enregistrement.
    else {
      // var_dump($_GET['ID_eq']);
      $data['ID_eq'] = $_GET['ID_eq'];
      // var_dump($data);
      //le template de la requête sql
      $requetejoueurs = "INSERT into joueurs (Nom_joueur, Prenom_joueur, Age_joueur, ID_eq)
      values (:Nom_joueur, :Prenom_joueur, :Age_joueur, :ID_eq)";
      $message= "Le joueur <b> $Nom_joueur </b>a été ajouté<br/>";
    }

    //preparation de la requête
    $joueursSth = $connexion->prepare($requetejoueurs);
    //on bin les paramètres directement dans la methode execute
    $joueursSth->execute($data);
    // var_dump($joueursSth->errorInfo());
    if (0 < $joueursSth->rowCount()){
      $_SESSION['message'] =[ 'message' => $message,];
    }
    else{
      $errorMessage = $joueursSth->errorInfo();
      $message =$errorMessage[2];
    }

    if (null !== $message){
      ?><br/><br/><br/><div class='alert alert-success'>
      <?php echo $message ;?>
    </div><?php

    }
    if ($message == null){
      $message = 'Vous n\'avez modifier aucune valeur';
      ?><br/><br/><br/><div class='alert alert-danger'>
      <?php echo $message ;?>
    </div><?php

    }
  }

  else{
    $message ="Vous devez entrée tout les paramètres, cliquez sur le bouton retour pour changer vos données";
    $drap=false;
  }

  // Si les valeurs introduites lors de la création d'équipe ne sont pas correcte, alors on propose de revenir au menu précédent
  if($drap==false){?>
    <br/><br/><br/><div class='alert alert-danger'>
      <?php echo $message ;?>
    </div>
    <a href="joueur_edit.php?ID_eq=<?= $equipes['ID_eq'] ?>">
      <button type="button" class='btn btn-outline-primary'>Retour</button>
    </a><?php
  }
  // Sinon on retourne a l'accueil
  else{ ?>
    <form
    action="joueur_consult.php<?=(isset($equipes['ID_eq'])) ? '?ID_eq='.$equipes['ID_eq'] : '' ?>" method="POST">
    <br/>  <a href="joueur_consult.php?ID_eq=<?= $equipes['ID_eq'] ?>">
      <button class='btn btn-outline-info col-sm-2 offset-sm-2' type="button">Retour liste des joueurs</button>
    </a>
  </form>
  </br><div><a href="joueur_edit.php?ID_eq=<?= $equipes['ID_eq'] ?>">
      <button type="button" class='btn btn-primary col-sm-2 offset-sm-2'>Ajouter un autre joueur</button>
    </a></div> <?php
  }
}
else{
  echo "<main role='main' class='container'>
    <h3 class='mt-5 col-sm-8 offset-sm-4'><u><br/>Insertion d'un joueur du club ".$equipes['Nom_eq'].":</u>
  </main>";
?>
<form

action="joueur_edit.php<?=((isset($equipes['ID_eq']))OR(isset($joueursid['ID_joueur']))) ? '?ID_eq='.$equipes['ID_eq'].'&id='.$joueursid['ID_joueur'] : '' ?>" method="POST">
  <div class="form-control col-sm-2 offset-sm-5">
    <label for="nom" >Nom du joueur</label>
    <div >
      <input  type="text" name="Nom_joueur" value="<?= $joueursid['Nom_joueur'] ?? '' ?>"id="nom">
    </div>
  </div>
  <br/><div class="form-control col-sm-2 offset-sm-5">
    <label for="Prenom" >Prenom du joueur</label>
    <div>
      <input type="text" name="Prenom_joueur" value="<?= $joueursid['Prenom_joueur'] ?? '' ?>" id="prenom">
    </div>
  </div>
<br/><div class="form-control col-sm-2 offset-sm-5">
    <label for="Date">Date de naissance du joueur</label>
    <div>
      <input type="date" name="Age_joueur" value="<?= $joueursid['Age_joueur'] ?? '' ?>" id="age">
    </div>
  </div>

  <br/>
     <div class='col-sm offset-sm'>
      <button type="submit" class='btn btn-secondary col-sm offset-sm'>Enregistrer</button>
    </div>
  </div><br/>
</form>
<form
action="joueur_consult.php<?=(isset($equipes['ID_eq'])) ? '?ID_eq='.$equipes['ID_eq'] : '' ?>" method="POST">
 <div class='col-sm-2 offset-sm-4'>
    <a href="joueur_consult.php?ID_eq=<?= $equipes['ID_eq'] ?>">
      <button class='btn btn-outline-info' type="button">Retour liste des joueurs</button>
    </a>
  </div>
</form>
<?php }
