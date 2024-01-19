<?php session_start();
    require('fonctions.php');
    verifierAuthentification();
    $pdo = creerConnexion();

    $idMedecin = '';
    $idUsager = '';
    if (isset($_POST["valider"])) {
        $idMedecin = $_POST["idMedecin"];
        $idUsager = $_POST["idUsager"];
        $date = $_POST["date"];
    }

    $reqConsultations = "SELECT CONCAT(m.nom, ' ', m.prenom) as nomMed, 
                                    CONCAT(u.nom, ' ', u.prenom) as nomUsager, 
                                    CONCAT(c.idMedecin, '$', c.dateConsultation, '$', c.heureDebut) as cle,
                                    dateConsultation, heureDebut, duree
                                FROM medecin m, usager u, consultation c 
                                WHERE c.idMedecin = m.idMedecin 
                                AND c.idUsager = u.idUsager ";

    $arguments = array();

    // Recherche en fonction des filtres
    if (!empty($idMedecin)) {
        $reqConsultations = $reqConsultations . "AND c.idMedecin = ? ";
        array_push($arguments, $idMedecin);
    }
    if (!empty($idUsager)) {
        $reqConsultations = $reqConsultations . "AND c.idUsager = ? ";
        array_push($arguments, $idUsager);
    }
    if (!empty($date)) {
        $reqConsultations = $reqConsultations . "AND dateConsultation = ? ";
        array_push($arguments, $date);
    }

    $reqConsultations = $reqConsultations . "ORDER BY dateConsultation DESC, heureDebut DESC;";
    
    $stmt = $pdo->prepare($reqConsultations);
    verifierPrepare($stmt);
    verifierExecute($stmt->execute($arguments));

    // On affiche toutes les lignes renvoyées ou un message si rien n'a été trouvé
    $table = '';
    $nombreLignes = '';
    if ($stmt->rowCount() > 0) {
        $nombreLignes = '<div class="nombre_lignes"><strong>' . $stmt->rowCount() . '</strong> consultation(s) trouvée(s)</div>';
        $table = '<div class="conteneur_table_affichage">
                    <table id="table_affichage">
                                <thead>
                                    <tr>
                                        <th>Médecin</th>
                                        <th>Patient</th>
                                        <th>Date de consultation</th>
                                        <th>Heure de consultation</th>
                                        <th>Durée de consultation</th>
                                    </tr>
                                </thead><tbody>';
        while ($dataConsultation = $stmt->fetch()) {
            $dateFormatee = formaterDate($dataConsultation['dateConsultation']);
            $table = $table . '<tr><td>' . $dataConsultation['nomMed'] . '</td>' .
                '<td>' . $dataConsultation['nomUsager'] . '</td>' .
                '<td>' . $dateFormatee . '</td>' .
                '<td>' . str_replace(':', 'H', substr($dataConsultation['heureDebut'], 0, 5)) . '</td>' .
                '<td>' . str_replace(':', 'H', substr($dataConsultation['duree'], 0, 5)) . '</td>' .
                '<td>' . '<a href = \'modificationConsultation.php?id=' . $dataConsultation['cle'] . '\'><img src="Images/modifier.png" alt=""width=30px></a></td>' .
                '<td>' . '<a href = \'suppression.php?id=' . $dataConsultation['cle'] . '&type=consultation\'><img src="Images/supprimer.png" alt=""width=30px></a></td></tr>';
        }
        $table = $table . '</tbody></table></div>';
    } else {
        $nombreLignes = '<div class="nombre_lignes" style="color: red;"><strong>Aucune</strong> consultation trouvée</div>';
    }
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="style.css">
    <title> Consultations </title>
</head>

<body>
    <?php include 'header.html' ?>
    
    <main class="main_affichage">
        <h1> Liste des consultations </h1>

        <form class="formulaire_table" method="post" action="affichageConsultations.php">
            <div class="colonne_formulaire large">
                Médecin <?php creerComboboxMedecins($pdo, $idMedecin, 'Tous les médecins'); ?>
            </div>
            <div class="colonne_formulaire large">
                Patient 
                    <?php creerComboboxUsagers($pdo, $idUsager, 'Tous les usagers'); ?>
            </div>
            <div class="colonne_formulaire petit">
                Date consultation <input type="date" name="date" value="<?php echo $date ?>">
            </div>
            <div class="conteneur_boutons">
                <input type="submit" value="Rechercher" name="valider">
                <a href="ajoutConsultation.php" class="lien_ajouter">
                    <div class="bouton_ajouter"><img src="Images/ajouter.png" width="20px"/>Ajouter</div>
                </a>
            </div>
        </form>
        <?php echo $nombreLignes; if (!empty($table)) { echo $table; } ?>
    </main>
</body>
</html>