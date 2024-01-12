<?php session_start();
    require('fonctions.php');
    verifierAuthentification();
    $pdo = creerConnexion();
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="accueil2.css">
    <title> Accueil </title>
</head>
<body>

    <h1> Bienvenue ! </h1>
     
    <div class=divBtns>
        <a href="affichageConsultations.php"> <button class="bouton">Consultations</button> </a> <br>
        <a href="ajoutConsultation.php"> <button class="bouton">Créer une consultation</button> </a> <br>
        <a href="affichageMedecins.php"> <button class="bouton">Médecins</button> </a> <br>
        <a href="ajoutMedecin.php"> <button class="bouton">Créer un médecin</button> </a> <br>
        <a href="affichageUsagers.php"> <button class="bouton">Patients</button> </a> <br>
        <a href="ajoutUsager.php"> <button class="bouton">Créer un patient</button> </a> <br>
    </div>

</body>

</html>