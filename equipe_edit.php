<?php include("header.php");
session_start();
include('connect.php');
$drap= true;
$message = null;
if(isset($_SESSION['message'])) {
  $message = $_SESSION['message'];
  unset($_SESSION['message']);
}
$equipes = null;

//verifier si l'id d'une equipe est envoyé

if(isset($_GET['ID_eq'])) {
  $equipesSth = $connexion->prepare("select * from equipes where ID_eq=:id");
  $equipesSth->execute([
    'id' => $_GET['ID_eq'],
  ]);
  // Si pas de valeur de retour, c'est que l'ID d'équipe n'existe pas, on le renvoi a l'accueil
  if(0 === $equipesSth->rowCount()) {
    header('location: index.php');
  }
  $equipes = $equipesSth->fetch(PDO::FETCH_ASSOC);
} ?>

<html>
<body>
<?php



//si le formulaire est envoyé on ajoute les informations en BDD
if($_POST) {
  $data = [
    'Nom_eq' => strtoupper($_POST['Nom_eq']),
    'Adresse_eq' => strtoupper($_POST['Adresse_eq']),
    'Tel_eq' => $_POST['Tel_eq'],
  ];
  // var_dump($data);
  if(($_POST['Nom_eq'])&&($_POST['Adresse_eq'])&&($_POST['Tel_eq'])){
    // var_dump($data);
    //Convertit tous les caractères éligibles en entités HTML
    $Nom_eq = htmlspecialchars($_POST['Nom_eq']);
    $Adresse_eq = htmlspecialchars($_POST['Adresse_eq']);
    $Tel_eq = $_POST['Tel_eq'];
    //Si l'equipe existe on le met à jour.
    if($equipes) {
        $requeteequipes = "update equipes set Nom_eq=:Nom_eq, Adresse_eq=:Adresse_eq, Tel_eq=:Tel_eq
        where ID_eq=:id";
        $data['id'] = $equipes['ID_eq'];
        $message= "L'équipe<b> $Nom_eq </b>a été modifié";
    }
    //si l'equipe' n'existe pas on crée un nouvel enregistrement.
    else {
      $requeteequipes = "insert into equipes (Nom_eq, Adresse_eq, Tel_eq)
      values (:Nom_eq, :Adresse_eq, :Tel_eq)";
      $message= "L'équipe<b> $Nom_eq </b>a été ajouté";
    }

      //preparation de la requête
      $equipesSth = $connexion->prepare($requeteequipes);
      //on bin les paramètres directement dans la methode execute
      $equipesSth->execute($data);
      if (0 < $equipesSth->rowCount()){
        $_SESSION['message'] =[ 'message' => $message,];
      }
      // on recupère le message derreur de SQL
      else{
        $errorMessage = $equipesSth->errorInfo();
        $message =$errorMessage[2];
      }

      if (null !== $message){
        ?><br/><br/><br/><div class='alert alert-primary'><b><?php
        echo $message;
        ?></div><?php
      }
      if ($message == null){
        $message = 'Vous n\'avez modifier aucune valeur';
          ?><br/><br/><div class='alert alert-danger'><?php
        echo $message;
          ?></div><?php
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
    <a href="equipe_edit.php">
      <button type="button" class='btn btn-primary col-sm-2 offset-sm-4'>Retour</button>
    </a>
  <?php
  }
  // Sinon on retourne a l'accueil
  else{ ?>
    <a href="equipe_edit.php?ID_eq=<?= $equipes['ID_eq'] ?>">
      <button type="button" class='btn btn-primary col-sm-2 offset-sm-4'>Retour</button>
    </a> <?php
  }
}
else{
  echo "
  <main role='main' class='container'>
    <h3 class='mt-5 col-sm-8 offset-sm-4'><u><br/>Les informations du club ".$equipes['Nom_eq']." :</u>
  </main>";
?>
<!-- Formulaire -->
<form
action="equipe_edit.php<?= (isset($equipes['ID_eq'])) ? '?ID_eq='.$equipes['ID_eq'] : '' ?>" method="post">
  <div>
    <label for="nom" class='validationCustom01 col-sm-2 offset-sm-4' >Nom de l'équipe : </label>
    <div>
      <input  type="text" name="Nom_eq" class="form-control col-sm-2 offset-sm-4" value="<?= $equipes['Nom_eq'] ?? '' ?>" id="nom" autofocus requiered>
    </div>
  </div>
  <br/><div>
    <label for="Adresse" class='validationCustom02 col-sm-2 offset-sm-4'>Adresse : </label>
    <div>
      <input type="text" name="Adresse_eq" class="form-control col-sm-2 offset-sm-4" value="<?= $equipes['Adresse_eq'] ?? '' ?>" id="adresse" requiered>
    </div>
  </div>
<br/><div >
    <label for="telephone" class='col-sm-2 offset-sm-4'>Telephone :</label>
    <div>
      <input type="text" name="Tel_eq" class="form-control col-sm-2 offset-sm-4" value="<?= $equipes['Tel_eq'] ?? '' ?>" id="telephone" requiered>
    </div>
  </div><br/>
<table class='col-sm-2 offset-sm-3'><td>
 <div class='col-sm-2 offset-sm-4'>
      <a href="equipe_edit.php?ID_eq=<?= $equipes['ID_eq'] ?>">
        <button class='btn btn-primary ' type="submit">Enregistrer les modifications</button>
      </a>
  <?php if (isset($equipes['ID_eq'])): ?>
  </div></form>
<br/>
  <div class='col-sm-2 offset-sm-4'>
        <a href="joueur_consult.php?ID_eq=<?= $equipes['ID_eq'] ?>">
          <button class='btn btn-info ' type="button">Consulter les joueurs</button>
        </a>
  </div>

<br/>
  <form
  action="match.php<?= (isset($equipes['ID_eq'])) ? '?ID_eq='.$equipes['ID_eq'] : '' ?>" method="post">
  <div class='col-sm-2 offset-sm-4'>
      <a href="match.php?ID_eq=<?= $equipes['ID_eq'] ?>">
        <button type="button" class='btn btn-primary '>Prochain match</button>
      </a>
    </div>
</form>
</td>
<td>

  <div class='col-sm-2 offset-sm-4'>
      <a href="classement.php?ID_eq=<?= $equipes['ID_eq'] ?>">
        <button type="button" class='btn btn-info '>Classement</button>
      </a>
    </div>
    <br/>
      <div class='col-sm-2 offset-sm-4'>
    <a href="histo_match.php?ID_eq=<?= $equipes['ID_eq'] ?>">
      <button type="button" class='btn btn-info '>Consulter l'historique de matchs</button>
    </a>
  </div>
<?php endif; ?>
  <br/><?php if(!$equipes['ID_eq']): echo '<br/>'; endif;?>
  <div class='col-sm-2 offset-sm-4'>
    <a href="index.php">
      <button type="button" class='btn btn-primary '>Retour Accueil</button>
    </a>
  </div>
</div>
</td></table>
<?php }
