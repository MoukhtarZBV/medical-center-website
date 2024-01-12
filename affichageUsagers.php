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
    <title> Liste des usagers </title>
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
        <h1> Liste des usagers </h1>
        <div class="conteneur_table_recherche">
            <form method="post" action="affichageUsagers.php" class="formulaire_table">
                <input type="text" name="criteres" placeholder="Entrez des mots-clés séparés par un espace" value="<?php if (isset($_POST['criteres'])) echo $_POST['criteres'] ?>">
                <input type="submit" value="Rechercher">
                <a href="ajoutUsager.php" class="lien_ajouter">
                    <div class="bouton_ajouter"><img src="Images/ajouter.png" width="20px"/>Ajouter</div>
                </a>
            </form>
            </div>
                <?php
                    try {
                        $pdo = new PDO('mysql:host=localhost;dbname=cabinetmed;charset=utf8', 'root', '');
                    } catch (Exception $e) {
                        echo ("Erreur " . $e);
                    }

                    // Début de la requête, on sélectionne tous les usagers et leur potentiel médecin référent
                    $reqUsagers = ' SELECT u.*, m.nom as nomMedecin, m.prenom as prenomMedecin
                                FROM usager u
                                LEFT JOIN medecin m ON u.medecinReferent = m.idMedecin';
                    
                    $tropDeCriteres = false;
                    // Si des mots-clés/critères on été saisis
                    if (!empty($_POST["criteres"])) {
                        // On sépare les critères saisis avec les espaces
                        $listeCriteres = preg_split('/\s+/', $_POST['criteres']);

                        $nombreCriteres = count($listeCriteres);
                        // Si le dernier critère est simplement un espace, on retire un au nombre de critères
                        if ($listeCriteres[count($listeCriteres) - 1] == '') {
                            $nombreCriteres--;
                        }

                        // S'il y a trop de critères, on annule la recherche
                        if ($nombreCriteres > 5){
                            $tropDeCriteres = true;
                        }

                        // On vérifie, pour chacune des colonnes, si elle correspond à un des critère
                        $listeColonnes = array('u.civilite', 'u.nom', 'u.prenom', 'u.ville', 'u.codePostal');
                        if ($nombreCriteres > 0 && !$tropDeCriteres) {
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
                            for ($i = 0; $i < $nombreCriteres; $i++) {
                                $stmt->bindParam(':critere' . $i, $listeCriteres[$i]);
                            }
                        }
                    } else { // Sinon on prépare simplement la requête
                        $stmt = $pdo->prepare($reqUsagers);
                    }

                    // Si la recherche est annulée, on affiche un message d'erreur
                    if ($tropDeCriteres){
                        echo '<div class="nombre_lignes" style="color: red;"> Veuillez saisir au plus <strong>5</strong> mots-clés</div>';
                    } else { // Sinon on procède à la recherche
                        // On execute la requête 
                        if (!$stmt->execute()) { print_r($stmt->errorInfo()); }

                        // On affiche toutes les lignes renvoyées ou un message si rien n'a été trouvé
                        if ($stmt->rowCount() > 0){
                            echo '<div class="nombre_lignes"><strong>'.$stmt->rowCount().'</strong> usager(s) trouvé(s)</div>';
                            echo '<table id="table_affichage">
                                    <thead>
                                        <tr>
                                            <th onclick="sortTable(0)">Civilite </th>
                                            <th onclick="sortTable(1)">Nom </th>
                                            <th onclick="sortTable(2)">Prenom </th>
                                            <th onclick="sortTable(3)">Adresse </th>
                                            <th onclick="sortTable(4)">Code postal </th>
                                            <th onclick="sortTable(5)">Ville </th>
                                            <th onclick="sortTable(6)">Numéro sécurité sociale </th>
                                            <th onclick="sortTable(7)">Date de naissance </th>
                                            <th onclick="sortTable(8)">Ville de naissance </th>
                                            <th onclick="sortTable(9)">Médecin référent </th>
                                        </tr>
                                    </thead><tbody>';
                            while ($dataUsager = $stmt->fetch()){
                                echo '<tr><td>'.$dataUsager['civilite'].'</td>'.
                                        '<td>'.$dataUsager['nom'].'</td>'.
                                        '<td>'.$dataUsager['prenom'].'</td>'.                          
                                        '<td>'.$dataUsager['adresse'].'</td>'.
                                        '<td>'.$dataUsager['ville'].'</td>'.
                                        '<td>'.$dataUsager['codePostal'].'</td>'.
                                        '<td>'.$dataUsager['numeroSecuriteSociale'].'</td>'.
                                        '<td>'.$dataUsager['dateNaissance'].'</td>'.
                                        '<td>'.$dataUsager['lieuNaissance'].'</td>'.
                                        '<td>'.$dataUsager['nomMedecin'].' '.$dataUsager['prenomMedecin'].'</td>'.
                                        '<td>'.'<a href = \'modificationusager.php?idUsager='.$dataUsager[0].'\'><img src="Images/modifier.png" alt=""width=30px></img></a></td>'.
                                        '<td>'.'<a href = \'suppression.php?id='.$dataUsager[0].'&type=usager\'><img src="Images/supprimer.png" alt=""width=30px></img></a></td></tr>';
                            }
                            echo '</tbody></table>';
                        } else {
                            echo '<div class="nombre_lignes" style="color: red;"><strong>Aucun</strong> usager trouvé</div>';
                        }
                    }
                    ?>
        </div>
    </main>
    <!-- Script pour trier une table en cliquant sur une colonne -->
    <script src="tri-tableau.js"></script>
</body>
</html>