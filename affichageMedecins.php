<?php session_start();
    require('fonctions.php');
    verifierAuthentification();
    $pdo = creerConnexion();
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
    <header id="menu_navigation">
        <div id="logo_site">
            <a href="accueil.html"><img src="Images/logo.png" width="250"></a>
        </div>
        <nav id="navigation">
            <label for="hamburger_defiler" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </label>
            <input class="defiler" type="checkbox" id="hamburger_defiler" role="button" aria-pressed="true">
            <ul class="headings">
                <li><a class="lien_header" href="affichageUsagers.php">Usagers</a></li>
                <li><a class="lien_header" href="affichageMedecins.php">Médecins</a></li>
                <li><a class="lien_header" href="affichageConsultations.php">Consultations</a></li>
                <li><a class="lien_header" href="statistiques.php">Statistiques</a></li>
            </ul>
        </nav>
    </header>
    
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
            </div>
                <?php
                    // Début de la requête, on sélectionne tous les usages et leur potentiel médecin référent
                    $reqMedecins = ' SELECT * FROM Medecin';
                    
                    // Si un nom et/ou un prénom ont été saisis
                    $arguments = array();
                    if (isset($_POST["valider"])) {
                        $nom = $_POST["nom"];
                        $prenom = $_POST["prenom"];
                        $reqMedecins = $reqMedecins . " WHERE lower(nom) LIKE lower(?)
                                                        AND lower(prenom) LIKE lower(?)";
                        $arguments = ["%$nom%", "%$prenom%"];
                    } 
                    $stmt = $pdo->prepare($reqMedecins);
                    if ($stmt == false) { echo "Erreur lors d'un prepare statement : " . $stmt->errorInfo(); }

                    // On execute la requête 
                    if ($stmt->execute($arguments)) {
                        // On affiche toutes les lignes renvoyées ou un message si rien n'a été trouvé
                        if ($stmt->rowCount() > 0){
                            echo '<div class="nombre_lignes"><strong>'.$stmt->rowCount().'</strong> médecin(s) trouvé(s)</div>';
                            echo '<table id="table_affichage">
                                    <thead>
                                        <tr>
                                            <th onclick="sortTable(0)">Civilite </th>
                                            <th onclick="sortTable(1)">Nom </th>
                                            <th onclick="sortTable(2)">Prenom </th>
                                        </tr>
                                    </thead>';
                            while ($dataMedecin = $stmt->fetch()){
                                echo '<tr><td>'.$dataMedecin['civilite'].'</td>'. 
                                        '<td>'.$dataMedecin['nom'].'</td>'.
                                        '<td>'.$dataMedecin['prenom'].'</td>'.                    
                                        '<td>'.'<a href = \'modificationMedecin.php?idMedecin='.$dataMedecin[0].'\'><img src="Images/modifier.png" alt=""width=30px></img></a>'.'</td>'.
                                        '<td>'.'<a href = \'suppression.php?id='.$dataMedecin[0].'&type=medecin\'><img src="Images/supprimer.png" alt=""width=30px></img></a>'.'</td>'.'</tr>';
                            }
                        } else {
                            echo '<div class="nombre_lignes" style="color: red;"><strong>Aucun</strong> médecin trouvé</div>';
                        }
                    } else {
                        echo "Erreur lors d'un execute statement : " . $stmt->errorInfo();
                    }
                    ?>
        </div>
    </main>
    <!-- Script pour trier une table en cliquant sur une colonne -->
    <script src="tri-tableau.js"></script>
</body>

</html>