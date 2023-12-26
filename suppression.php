<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <title> Suppression de contact </title>
    </head>
    <body>

    <h2> Suppression d'un contact </h2>

            <?php

                if ($_SERVER["REQUEST_METHOD"] == "GET") {
                    if ($_GET['type']=='usager') {
                        echo '<p> Êtes vous sûr(e) de vouloir supprimer cet usager? </p>';
                    } if ($_GET['type']=='medecin') {
                        echo '<p> Êtes vous sûr(e) de vouloir supprimer ce médecin? </p>';
                    } else {
                        echo '<p> Êtes vous sûr(e) de vouloir supprimer cette consultation? </p>';
                    }
                    echo '<form action="suppression.php" method="post">';
                    echo '<input type="hidden" name="type" value="'.$_GET['type'].'">';
                    echo '<input type="hidden" name="id" value="'.$_GET['id'].'">';
                    echo '<input type="button" onclick="history.back();" value="Non">';
                    echo '<input type="submit" name ="valider" value="Oui">';
                    echo '</form>';
                }
                
                if ($_SERVER["REQUEST_METHOD"] == "POST") {

                    try {
                        $pdo = new PDO("mysql:host=localhost;dbname=cabinetmed", 'root', '');
                    } catch (Exception $e) {
                        echo ("Erreur : ".$e);
                    }
                    
                    if ($_REQUEST['type'] == 'usager') {
                        $stmt = $pdo->prepare("DELETE FROM usager WHERE idUsager=".$_REQUEST['id']);
                    } else if ($_REQUEST['type'] == 'medecin') {
                        $stmt = $pdo->prepare("UPDATE usager SET medecinReferent = NULL WHERE medecinReferent=".$_REQUEST['id']);
                        $stmt->execute();
                        $stmt = $pdo->prepare("DELETE FROM medecin WHERE idMedecin=".$_REQUEST['id']);
                    } else {
                        $stmt = $pdo->prepare("DELETE FROM consultation WHERE CONCAT(idMedecin, dateConsultation) LIKE '".$_REQUEST['id']."'");
                    }

                    $stmt->execute();
                    
                    if ($stmt) {
                        echo 'Suppression effectuée!';
                    } else {
                        echo 'PREPARE ERROR';
                    }
                    
                }

            ?>

    </body>
</html>