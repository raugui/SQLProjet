<?php
$dsn = 'mysql:host=localhost;dbname=football;charset=UTF8';
$user = 'root';
$password = 'gui7784@@';

//attrape une exception potentielle
try{
  //connexion à la BDD
  $connexion = new PDO($dsn, $user, $password);
}
//retourne un message d'erreur lorsqu'une
// exception est levée
catch (\Exception $e){
 echo $e->getMessage();
}
