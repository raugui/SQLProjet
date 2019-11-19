<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="../../../../favicon.ico">

    <title>Sticky Footer Navbar Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="sticky-footer-navbar.css" rel="stylesheet">
  </head>

  <body>

    <header>
      <!-- Fixed navbar -->
      <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
        <a class="navbar-brand" href="index.php">Accueil</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarCollapse">
          <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
              <a class="nav-link" href="classement.php<?= (isset($_GET['ID_eq'])) ? '?ID_eq='.$_GET['ID_eq'] : '' ?>">Classement
                </a>
            </li>
            <li class="nav-item active">
              <a class="nav-link" href="histo_match.php<?= (isset($_GET['ID_eq'])) ? '?ID_eq='.$_GET['ID_eq'] : '' ?>">Historique de match</a>
            </li>
            <li class="nav-item success ">
              <a class="nav-link" href="equipe_edit.php">Ajouter une équipe</a>
            </li>
          </ul>
          <form class="form-inline mt-2 mt-md-0" action="index.php" method="GET">
            <label for="search"></label>
            <input class="form-control mr-sm-2" value="<?php echo $_GET['search'] ?? '' ?>" type="text" placeholder="Search" id="search" name="search" aria-label="Search">
            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Rechercher</button>
          </form>
        </div>
      </nav>
    </header>
