<?php session_start();
    require('fonctions.php');
    require('fonctionsVerifierInputs.php');
    verifierAuthentification();
    $pdo = creerConnexion();

    
    if (isset($_GET["id"])){
        $cleConsultation = explode('$', $_GET["id"]);
        $popup = '';
        if (isset($_POST["Confirmer"])) {
            $today = gmdate('Y-m-d', time());
            $date = $_POST['date'];
            $heure = substr($_POST['heureD'], 0, 5);
            $duree = substr($_POST['duree'], 0, 5);
            if (!dateApresLe($date, $today)) {
                $message = 'La date de la consultation ne peut pas être infèrieure à la date du jour';
                $classeMessage = 'erreur';
            } else if (!heureApres8HeureAvant20Heure($heure)) {
                $message = 'La consultation doit avoir lieu entre 8 heures et 20 heures';
                $classeMessage = 'erreur';
            } else if (!dureeSuperieure15MinutesInferieur60Minutes($duree)) {
                $message = 'La consultation doit durer entre 5 minutes et une heure';
                $classeMessage = 'erreur';
            } else {
                $stmt = $pdo->prepare(" SELECT heureDebut, duree 
                                        FROM Consultation c, Medecin m 
                                        WHERE c.idMedecin = m.idMedecin 
                                        AND c.idMedecin = ?
                                        AND c.dateConsultation = ?
                                        AND c.heureDebut <> ?");
                verifierPrepare($stmt);
                verifierExecute($stmt->execute($cleConsultation));

                $consulationsChevauchantes = false;
                while (!$consulationsChevauchantes && $consultation = $stmt->fetch()){
                    if (consultationsChevauchantes($heure, $duree, substr($consultation['heureDebut'], 0, 5), substr($consultation['duree'], 0, 5))) {
                        $consulationsChevauchantes = true;
                    }
                }
                
                $message = '';
                $classeMessage = '';
                if (!$consulationsChevauchantes) {
                    $stmt = $pdo->prepare(" UPDATE Consultation
                                            SET heureDebut = ?, duree = ?
                                            WHERE idMedecin = ?
                                            AND dateConsultation = ?
                                            AND heureDebut = ?");
                    verifierPrepare($stmt);
                    verifierExecute($stmt->execute([$heure, $duree, $cleConsultation[0], $cleConsultation[1], $cleConsultation[2]]));

                    $dateFormatee = formaterDate($date);
                    $message = 'La consultation a été modifiée ! Elle a lieu le <strong>' . $dateFormatee . '</strong> à <strong>' . str_replace(':', 'H', $heure) . '.';
                    $classeMessage = 'succes';
                    $cleConsultation[2] = $heure;
                } else {
                    $message = 'La consultation chevauche avec un autre créneau pour ce médecin';
                    $classeMessage = 'erreur';
                }
                
                // Affichage de la popup d'erreur ou de succés
                if (!empty($message)){
                    $popup = '<div class="popup ' . $classeMessage . '">' . $message .'</div>';
                }
            }
        }

        // Récupération du médecin et de l'usager concernés par la consultation
        // ainsi que de la date, l'heure et la durée de la consultation
        $medecin = '';
        $usagerActuel = '';
        $idUsagerActuel = -1;
        $stmt = $pdo->prepare(" SELECT m.civilite AS civM, m.nom AS nomM, m.prenom AS prenomM,
                                       u.civilite AS civU, u.nom AS nomU, u.prenom AS prenomU, numeroSecuriteSociale, u.idUsager AS idUsager,
                                       dateConsultation, heureDebut, duree
                                FROM medecin m, consultation c, usager u
                                WHERE m.idMedecin = c.idMedecin 
                                AND u.idUsager = c.idUsager
                                AND c.idMedecin = ?
                                AND c.dateConsultation = ?
                                AND c.heureDebut = ?");
        verifierPrepare($stmt);
        verifierExecute($stmt->execute($cleConsultation));
            
        $resultat = $stmt->fetch();
        $medecin = $resultat["civM"] . '. ' . $resultat["nomM"] . ' ' . $resultat["prenomM"];
        $usager = $resultat["civU"] . '. ' . $resultat["nomU"] . ' ' . $resultat["prenomU"] . ' (' . $resultat["numeroSecuriteSociale"] . ')';
        $date = $resultat["dateConsultation"];
        $heure = $resultat["heureDebut"];
        $duree = $resultat["duree"];
        $id = $cleConsultation[0].'$'.$cleConsultation[1].'$'.$cleConsultation[2];
    }
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="style.css">
    <title> Modification d'une consultation </title>
</head>

<body id="body_fond">
    <?php include 'header.html' ?>

    <?php if (!empty($popup)) { echo $popup; } ?>

    <div class="titre_formulaire">
        <h1> Modification d'une consultation </h1>
    </div>

    <form class="formulaire" action="modificationConsultation.php?id=<?php echo $id ?>" method="post">
        <div class="ligne_formulaire">
            <div class="input_lecture">
                Médecin <input type="text" name="medecin" value="<?php echo $medecin ?>" readonly>
            </div>
        </div>
        <div class="ligne_formulaire">
            <div class="input_lecture">
                Médecin <input type="text" name="usager" value="<?php echo $usager ?>" readonly>
            </div>
        </div>
        <div class="ligne_formulaire temps_consultation">
            <div class="colonne_formulaire moitie">
                Date de consultation <input type="date" name="date" value="<?php echo $date ?>" min="<?php echo $today ?>" readonly>
            </div>
            <div class="colonne_formulaire moitie">
                Horaire de consultation <input type="time" name="heureD" min="08:00" max="20:00" value="<?php echo $heure ?>" required>
            </div>
            <div class="colonne_formulaire petit">
                Durée de consultation <input type="time" name="duree" min="00:05" max="02:00" value="<?php echo $duree ?>" required>
            </div>
        </div>
        <div class="conteneur_boutons">
            <input type="reset" name="Vider" value="Réiniatiliser">
            <input type="submit" name="Confirmer" value="Confirmer">
        </div>
    </form>
</body>
</html>