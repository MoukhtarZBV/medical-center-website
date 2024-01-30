<?php session_start();
    require('fonctions.php');
    verifierAuthentification();
    $pdo = creerConnexion();
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="header.css">
    <title> Accueil </title>
</head>
<body id="body_accueil">
    <?php include 'header.html' ?>
    
    <h1 id="titre_accueil"> Accueil </h1>
     
    <div class="divBtns">
        <div class="btn-group">
            <a href="affichageConsultations.php"><button class="bouton">Consultations</button></a>
            <a href="ajoutConsultation.php"><button class="bouton bouton-secondary">Créer une consultation</button></a> 
        </div>
        <div class="btn-group">
            <a href="affichageMedecins.php"><button class="bouton">Médecins</button></a>
            <a href="ajoutMedecin.php"><button class="bouton bouton-secondary">Créer un médecin</button></a> 
        </div>
        <div class="btn-group">
            <a href="affichageUsagers.php"><button class="bouton">Patients</button></a>
            <a href="ajoutUsager.php"><button class="bouton bouton-secondary">Créer un patient</button></a> 
        </div>
    </div>

</body>
</html>