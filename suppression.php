<?php session_start();
    require('fonctions.php');
    verifierAuthentification();
    $pdo = creerConnexion();

    $message = '';
    $titre = '';
    if (!empty($_GET['id']) && !empty($_GET['type'])) {
        if ($_GET['type'] == 'usager') {
            $titre = 'Suppression d\'un usager';
            $message = 'Êtes vous sûr(e) de vouloir supprimer cet usager ?';
        } else if ($_GET['type'] == 'medecin') {
            $titre = 'Suppression d\'un médecin';
            $message = 'Êtes vous sûr(e) de vouloir supprimer ce médecin ?';
        } else {
            $titre = 'Suppression d\'une consultation';
            $message = 'Êtes vous sûr(e) de vouloir supprimer cette consultation ?';
        }
    }
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $arguments = array();
        if ($_POST['type'] == 'usager') {
            $stmt = $pdo->prepare("DELETE FROM consultation WHERE idUsager = ?");
            verifierPrepare($stmt);
            verifierExecute($stmt->execute([$_POST['id']]));
            $stmt = $pdo->prepare("DELETE FROM usager WHERE idUsager = ?");
            array_push($arguments, $_POST['id']);
        } else if ($_POST['type'] == 'medecin') {
            $stmt = $pdo->prepare("UPDATE usager SET medecinReferent = NULL WHERE medecinReferent = ?");
            verifierPrepare($stmt);
            verifierExecute($stmt->execute([$_POST['id']]));
            $stmt = $pdo->prepare("DELETE FROM consultation WHERE idMedecin = ?");
            verifierPrepare($stmt);
            verifierExecute($stmt->execute([$_POST['id']]));
            $stmt = $pdo->prepare("DELETE FROM medecin WHERE idMedecin = ?");
            array_push($arguments, $_POST['id']);
        } else {
            $stmt = $pdo->prepare("DELETE FROM consultation WHERE idMedecin = ? AND dateConsultation = ? AND heureDebut = ?");
            $arguments = explode('$', $_POST["id"]);
        }
        verifierPrepare($stmt);
        verifierExecute($stmt->execute($arguments));
        $message = 'Suppression effectuée !';
        header('Refresh: 1;URL=index.php');
    }

?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
    <title> Suppression </title>
</head>

<body id="body_fond">

    <div class="titre_formulaire">
        <h1> <?php echo empty($titre) ? 'Suppression effectuée' : $titre ?> </h1>
    </div>

    <form class="formulaire" action="suppression.php" method="post">
        <div class="ligne_formulaire">
            <?php echo $message ?>
        </div>
        <?php 
            if (!isset($_POST['valider'])){
                echo '<input type="hidden" name="type" value="'.$_GET['type'].'">
                <input type="hidden" name="id" value="'.$_GET['id'].'">
                <div class="conteneur_boutons">
                    <input type="button" onclick="history.back();" value="Non" id="bouton_annuler_suppression">
                    <input type="submit" name="valider" value="Oui" id="bouton_confirmer_suppression">
                </div>';
            }
        ?>
    </form>
</body>

</html>