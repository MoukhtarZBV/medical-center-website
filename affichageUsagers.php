<?php session_start();
    require('fonctions.php');
    verifierAuthentification();
    $pdo = creerConnexion();

    // Début de la requête, on sélectionne tous les usagers et leur potentiel médecin référent
    $reqUsagers = ' SELECT u.*, m.nom as nomMedecin, m.prenom as prenomMedecin
    FROM usager u
    LEFT JOIN medecin m ON u.medecinReferent = m.idMedecin';

    $tropDeCriteres = false;
    $stmt = null;
    // Si des mots-clés/critères on été saisis
    if (!empty($_POST["criteres"])) {
        // On sépare les critères saisis avec les espaces
        $criteres = trim($_POST['criteres']);
        $listeCriteres = explode(' ', $criteres);

        $nombreCriteres = count($listeCriteres);

        // S'il y a trop de critères, on annule la recherche
        if ($nombreCriteres > 5){
            $tropDeCriteres = true;
        }

        // On vérifie, pour chacune des colonnes, si elle correspond à un des critère
        $listeColonnes = array('u.civilite', 'u.nom', 'u.prenom', 'u.ville', 'u.codePostal');
        if (!$tropDeCriteres) {
            $reqUsagers = $reqUsagers . ' WHERE ';
            for ($i = 0; $i < count($listeColonnes); $i++) {
                for ($j = 0; $j < $nombreCriteres; $j++) {
                    $reqUsagers = $reqUsagers . $listeColonnes[$i] . ' LIKE :critere' . $j . ' OR ';
                }
            }
            // Pour enlever le dernier 'OR'
            $reqUsagers = substr($reqUsagers, 0, -4);

            // On remplace les ':critereX' avec un prepared statement
            $stmt = $pdo->prepare($reqUsagers);
            verifierPrepare($stmt);
            for ($i = 0; $i < $nombreCriteres; $i++) {
                $stmt->bindParam(':critere' . $i, $listeCriteres[$i]);
            }
        }
    } else { // Sinon on prépare simplement la requête
        $stmt = $pdo->prepare($reqUsagers);
        verifierPrepare($stmt);
    }

    // Si la recherche est annulée, on affiche un message d'erreur
    if ($tropDeCriteres){
        $nombreLignes = '<div class="nombre_lignes" style="color: red;"> Veuillez saisir au plus <strong>5</strong> mots-clés</div>';
    } else { // Sinon on procède à la recherche
        // On execute la requête 
        verifierExecute($stmt->execute());

        // On affiche toutes les lignes renvoyées ou un message si rien n'a été trouvé
        $table = '';
        $nombreLignes = '';
        if ($stmt->rowCount() > 0){
            $nombreLignes = '<div class="nombre_lignes"><strong>'.$stmt->rowCount().'</strong> usager(s) trouvé(s)</div>';
            $table = '<div class="conteneur_table_affichage">
                    <table id="table_affichage">
                    <thead>
                        <tr>
                            <th>Civilite </th>
                            <th>Nom </th>
                            <th>Prenom </th>
                            <th>Adresse </th>
                            <th>Ville </th>
                            <th>Code postal </th>
                            <th>Numéro sécurité sociale </th>
                            <th>Date de naissance </th>
                            <th>Ville de naissance </th>
                            <th>Médecin référent </th>
                        </tr>
                    </thead><tbody>';
            while ($dataUsager = $stmt->fetch()){
                $dateFormatee = formaterDate($dataUsager['dateNaissance']);
                $table = $table . '<tr><td>'.$dataUsager['civilite'].'</td>'.
                        '<td>'.$dataUsager['nom'].'</td>'.
                        '<td>'.$dataUsager['prenom'].'</td>'.                          
                        '<td>'.$dataUsager['adresse'].'</td>'.
                        '<td>'.$dataUsager['ville'].'</td>'.
                        '<td>'.$dataUsager['codePostal'].'</td>'.
                        '<td>'.$dataUsager['numeroSecuriteSociale'].'</td>'.
                        '<td>'.$dateFormatee.'</td>'.
                        '<td>'.$dataUsager['lieuNaissance'].'</td>'.
                        '<td>'.$dataUsager['nomMedecin'].' '.$dataUsager['prenomMedecin'].'</td>'.
                        '<td>'.'<a href = \'modificationusager.php?idUsager='.$dataUsager[0].'\'><img src="Images/modifier.png" alt=""width=30px></img></a></td>'.
                        '<td>'.'<a href = \'suppression.php?id='.$dataUsager[0].'&type=usager\'><img src="Images/supprimer.png" alt=""width=30px></img></a></td></tr>';
            }
            $table = $table . '</tbody></table></div>';
        } else {
            $nombreLignes = '<div class="nombre_lignes" style="color: red;"><strong>Aucun</strong> usager trouvé</div>';
        }
    }
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="header.css">
    <title> Usagers </title>
</head>

<body>
    <?php include 'header.html' ?>
    
    <main class="main_affichage">
        <h1> Liste des usagers </h1>
        <div class="conteneur_table_recherche">
            <form method="post" action="affichageUsagers.php" class="formulaire_table">
                <input type="text" name="criteres" class="espaces_permis" placeholder="Entrez des mots-clés séparés par un espace" value="<?php if (isset($_POST['criteres'])) echo $_POST['criteres'] ?>">
                <input type="submit" value="Rechercher">
                <a href="ajoutusager.php" class="lien_ajouter">
                    <div class="bouton_ajouter"><img src="Images/ajouter.png" width="20px"/>Ajouter</div>
                </a>
            </form>
            <?php echo $nombreLignes; if (!empty($table)) { echo $table; } ?>
        </div>
    </main>
    <!-- Script pour formater les inputs -->
    <script src="format-texte-input.js"></script>
</body>
</html>