<?php include("header.php"); ?>
<html>
<body>
    <!-- Begin page content -->
    <main role="main" class="container">
      <h1 class="mt-5 col-sm-6 offset-sm-3"><u>Liste des équipes inscrites :</u></h1>
    </main>
    <div class="container">
      <div class="row">



<?php
session_start();
include('connect.php');
$message = null;
if(isset($_SESSION['message'])) {
  $message = $_SESSION['message'];
  unset($_SESSION['message']);
}

//Vérifie si la propriété page existe, si elle existe on la renvoie
// SI le get de recherche nest pas vide , alors on recupere la valeur dans l'url
$search = !empty($_GET['search']) ? $_GET['search'] : null;
// Les données sont en majuscule dans la BDD, donc on converti pour être sur de la recherche
$search = strtoupper($search);
//requête pour récupérer les equipes
$requeteEquipes = "select * from equipes";
$recherche = '';
if(null !== $search)
{
  $recherche .= " where Nom_eq like :search";
}

//ajoute les conditions à la requête
$requeteEquipes .= $recherche;

//preparation d'une requête (on récupère un object PDOStatement)
$equipesSth = $connexion->prepare($requeteEquipes);
if(null !== $search)
{
  $equipesSth->bindValue('search',  "%" . $search. "%");
}
//on execute la requête
$equipesSth->execute();
?>
<?php if(null !== $message): ?>
  <?php $color = $message['color'] ?? 'primary' ?>
  <div -<?php echo $color ?>
  <?php echo $message['message']; ?>
  </div>
<?php endif; ?>

<br>

<br>
<?php
$equipes = $equipesSth->fetch(PDO::FETCH_ASSOC);
if ($search == $equipes['Nom_eq']){
  echo "<table class='table table-striped table-dark col-sm-6 offset-sm-3'>
          <thead>
            <tr>
              <th>Nom</th>
              <th>Action</th>
            </tr>
          </thead>
  ";


    echo   "<tr>
        <td>".htmlspecialchars($equipes['Nom_eq'])."</td>
          <td><a href='equipe_supprimer.php?ID_eq=".$equipes['ID_eq'] ."'>
            <button class='btn btn-danger'>Supprimer</button>
          </a>
          <a href='equipe_edit.php?ID_eq=".$equipes['ID_eq']."'>
            <button class='btn btn-primary'>Consulter</button>
          </a>
        </td>
      </tr>";

  echo "</table>";
}

// Si l'équipe n'existe pas on lui affiche un message
else if($search != (($equipes['Nom_eq'])&&(''))){
  echo 'L\'équipe n\'éxiste pas.';
}
else{
  $requeteEquipes1 = "select * from equipes";
  $equipesSth = $connexion->prepare($requeteEquipes1);
  $equipesSth->execute();
  $equipes = $equipesSth->fetchAll(PDO::FETCH_ASSOC);

echo "<table class='table table-striped table-dark col-sm-6 offset-sm-3'>
        <thead class='thead-inverse'>
          <tr>
            <th>Nom</th>
            <th>Action</th>
          </tr>
        </thead>
";

foreach($equipes as $equipe){
  echo   "<div><tr>
      <td>".htmlspecialchars($equipe['Nom_eq'])."</td>
      <td>
        <a href='equipe_supprimer.php?ID_eq=".$equipe['ID_eq']."'>
          <button class='btn btn-danger'>Supprimer</button>
        </a>
        <a href='equipe_edit.php?ID_eq=".$equipe['ID_eq']."'>
          <button class='btn btn-primary'>Consulter</button>
        </a>
      </td>
    </tr>";

}
echo "</table>";}?>

</div></div>
</body>
</html>
