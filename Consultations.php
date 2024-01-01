<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="style.css">
    <title> Consultations </title>
</head>

<body>
    <main class="main_affichage">
        <h1> Liste des consultations </h1>

        <form class="formulaire_table" method="post" action="Consultations.php">
            <div class="colonne_formulaire large">
                Médecin <input type="text" name="medecin" value="">
            </div>
            <div class="colonne_formulaire large">
                Patient <input type="text" name="patient" value="">
            </div>
            <div class="colonne_formulaire petit">
                Date consultation <input type="date" name="date" value="">
            </div>
            <div class="conteneur_boutons">
                <input type="reset" value="Vider" name="vider">
                <input type="submit" value="Rechercher" name="valider">
            </div>

        </form>

        <?php

        try {
            $pdo = new PDO('mysql:host=localhost;dbname=cabinetmed;charset=utf8', 'root', '');
        } catch (Exception $e) {
            echo ("Erreur " . $e);
        }

        $reqConsultations = "SELECT CONCAT(m.nom, ' ', m.prenom) as nomMed, 
                                    CONCAT(u.nom, ' ', u.prenom) as nomUsager, 
                                    CONCAT(c.idMedecin, '$', c.dateConsultation, '$', c.heureDebut) as cle,
                                    dateConsultation, heureDebut, duree
                                FROM medecin m, usager u, consultation c 
                                WHERE c.idMedecin = m.idMedecin AND c.idUsager = u.idUsager ";

        $arguments = array();
        if (isset($_POST["valider"])) {
            $medecin = $_POST["medecin"];
            $patient = $_POST["patient"];
            $date = $_POST["date"];
            $reqConsultations = $reqConsultations . "AND lower(CONCAT(' ', m.nom, ' ', m.prenom)) LIKE lower(?) 
                                                        AND lower(CONCAT(' ', u.nom, ' ', u.prenom)) LIKE lower(?)
                                                        AND (dateConsultation = ? OR ? = '') ";
            $arguments = ["%$medecin%", "%$patient%", "$date", "$date"];
        }
        $reqConsultations = $reqConsultations . "ORDER BY dateConsultation DESC, heureDebut DESC;";

        $stmt = $pdo->prepare($reqConsultations);
        if (!$stmt) { echo "Erreur lors d'un prepare statement : " . $stmt->errorInfo(); }

        if ($stmt->execute($arguments)) {
            // On affiche toutes les lignes renvoyées ou un message si rien n'a été trouvé
            if ($stmt->rowCount() > 0) {
                echo '<div class="nombre_lignes"><strong>' . $stmt->rowCount() . '</strong> consultation(s) trouvée(s)</div>';
                echo '<table id="table_affichage">
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
                    $elementsDate = explode('-', $dataConsultation['dateConsultation']);
                    $dateFormatee = $elementsDate[2] . '/' . $elementsDate[1] . '/' . $elementsDate[0];
                    echo '<tr><td>' . $dataConsultation['nomMed'] . '</td>' .
                        '<td>' . $dataConsultation['nomUsager'] . '</td>' .
                        '<td>' . $dateFormatee . '</td>' .
                        '<td>' . str_replace(':', 'H', substr($dataConsultation['heureDebut'], 0, 5)) . '</td>' .
                        '<td>' . str_replace(':', 'H', substr($dataConsultation['duree'], 0, 5)) . '</td>' .
                        '<td>' . '<a href = \'modificationConsultation.php?id=' . $dataConsultation['cle'] . '\'><img src="Images/modifier.png" alt=""width=30px></a></td>' .
                        '<td>' . '<a href = \'suppression.php?id=' . $dataConsultation['cle'] . '&type=consultation\'><img src="Images/supprimer.png" alt=""width=30px></a></td></tr>';
                }
                echo '</tbody></table>';
            } else {
                echo '<div class="nombre_lignes" style="color: red;"><strong>Aucune</strong> consultation trouvée</div>';
            }
        } else {
            echo "Erreur lors d'un execute statement : " . $stmt->errorInfo();
        }
        ?>
    </main>
</body>

</html>