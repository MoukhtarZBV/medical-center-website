<?php session_start();
    require('fonctions.php');
    verifierAuthentification();
    $pdo = creerConnexion();

    // Début de la requête, on sélectionne tous les usages et leur potentiel médecin référent
    $reqMedecins = ' SELECT * FROM Medecin';
                    
    // Si un nom et/ou un prénom ont été saisis
    $arguments = array();
    if (isset($_POST["valider"]) && !empty($_POST["valider"])) {
        $nom = $_POST["nom"];
        $prenom = $_POST["prenom"];
        $reqMedecins = $reqMedecins . " WHERE lower(nom) LIKE lower(?)
                                        AND lower(prenom) LIKE lower(?)";
        $arguments = ["%$nom%", "%$prenom%"];
    } 
    $stmt = $pdo->prepare($reqMedecins);
    verifierPrepare($stmt);
    verifierExecute($stmt->execute($arguments));

    // On affiche toutes les lignes renvoyées ou un message si rien n'a été trouvé
    $table = '';
    $nombreLignes = '';
    if ($stmt->rowCount() > 0){
        $nombreLignes ='<div class="nombre_lignes"><strong>'.$stmt->rowCount().'</strong> médecin(s) trouvé(s)</div>';
        $table ='<div class="conteneur_table_affichage">
                <table id="table_affichage">
                <thead>
                    <tr>
                        <th>Civilite </th>
                        <th>Nom </th>
                        <th>Prenom </th>
                    </tr>
                </thead><tbody>';
        while ($dataMedecin = $stmt->fetch()){
            $table = $table . '<tr><td>'.$dataMedecin['civilite'].'</td>'. 
                    '<td>'.$dataMedecin['nom'].'</td>'.
                    '<td>'.$dataMedecin['prenom'].'</td>'.                    
                    '<td>'.'<a href = \'modificationMedecin.php?idMedecin='.$dataMedecin[0].'\'><img src="Images/modifier.png" alt=""width=30px></img></a>'.'</td>'.
                    '<td>'.'<a href = \'suppression.php?id='.$dataMedecin[0].'&type=medecin\'><img src="Images/supprimer.png" alt=""width=30px></img></a>'.'</td>'.'</tr>';
        }
        $table =$table . '</tbody></table></div>';
    } else {
        $nombreLignes = '<div class="nombre_lignes" style="color: red;"><strong>Aucun</strong> médecin trouvé</div>';
    }
?>
<!DOCTYPE HTML> 
<html>

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="header.css">
    <title> Médecins </title>
</head>
<body>
    <?php include 'header.html' ?>
    
    <main class="main_affichage">
        <h1> Liste des médecins </h1>
        <div class="conteneur_table_recherche">
            <form method="post" action="affichageMedecins.php" class="formulaire_table">
                <div class="colonne_formulaire large">
                    Nom <input type="text" name="nom" value="">
                </div>
                <div class="colonne_formulaire large">
                    Prénom <input type="text" name="prenom" value="">
                </div>
                <div class="conteneur_boutons">
                    <input type="submit" value="Rechercher" name="valider">
                    <a href="ajoutMedecin.php" class="lien_ajouter">
                        <div class="bouton_ajouter"><img src="Images/ajouter.png" width="20px"/>Ajouter</div>
                    </a>
                </div>
            </form>
            <?php echo $nombreLignes; if (!empty($table)) { echo $table; } ?>
        </div>
    </main>
</body>

</html>