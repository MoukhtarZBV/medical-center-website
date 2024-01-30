<?php session_start();
    require('fonctions.php');
    require('fonctionsVerifierInputs.php');
    verifierAuthentification();
    $pdo = creerConnexion();

    if (!empty($_POST['Confirmer'])) {
        $civ = $_POST['civ'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $adr = $_POST['adr'];
        $ville = $_POST['ville'];
        $cp = $_POST['cp'];
        $nss = $_POST['nss'];
        $date = $_POST['date'];
        $lieu = $_POST['lieu'];
        $idMed = !empty($_POST['idMedecin']) ? $_POST['idMedecin'] : null;

        $message = '';
        $classeMessage = '';
        if (!inputSansEspacesCorrect($nom, TAILLE_NOM)){
            $message = 'Le nom n\'est pas correctement saisi';
            $classeMessage = 'erreur';
        } else if (!inputSansEspacesCorrect($prenom, TAILLE_PRENOM)){
            $message = 'Le prénom n\'est pas correctement saisi';
            $classeMessage = 'erreur';
        } else if (!inputAvecUnEspaceCorrect($adr, TAILLE_ADRESSE)){
            $message = 'L\'adresse n\'est pas correctement saisie';
            $classeMessage = 'erreur';
        } else if (!inputSansEspacesCorrect($ville, TAILLE_VILLE)){
            $message = 'La ville n\'est pas correctement saisie';
            $classeMessage = 'erreur';
        } else if (!inputChiffresUniquementCorrect($cp, TAILLE_CODE_POSTAL) || !tailleInputRespectee($cp, TAILLE_CODE_POSTAL)){
            $message = 'Le code postal n\'est pas correctement saisi';
            $classeMessage = 'erreur';
        } else if (!inputChiffresUniquementCorrect($nss, TAILLE_NUMERO_SECU) || !tailleInputRespectee($nss, TAILLE_NUMERO_SECU)){
            $message = 'Le numéro de sécurité sociale n\'est pas correctement saisi';
            $classeMessage = 'erreur';
        } else if (!inputSansEspacesCorrect($lieu, TAILLE_VILLE)){
            $message = 'La ville de naissance n\'est pas correctement saisie';
            $classeMessage = 'erreur';
        } else {
            $stmt = $pdo->prepare('UPDATE usager SET nom = ?, prenom = ?, civilite = ?, adresse = ?, codePostal = ?, 
                                                    ville = ?, numeroSecuriteSociale = ?, dateNaissance = ?, lieuNaissance = ?, medecinReferent = ?
                                                WHERE idUsager = ?');
            verifierPrepare($stmt);
            verifierExecute($stmt->execute([$nom, $prenom, $civ, $adr, $cp, $ville, $nss, $date, $lieu, $idMed, $_GET['idUsager']]));
            $message = 'L\'usager <strong>'.$nom.' '.$prenom.'</strong> a été mis à jour !';
            $classeMessage = 'succes';
        }

        // Affichage de la popup d'erreur ou de succés
        if (!empty($message)){
            $popup = '<div class="popup ' . $classeMessage . '">' . $message .'</div>';
        }
    }
    
    if (isset($_GET['idUsager'])) {
        $sql = 'SELECT * FROM usager WHERE idUsager = ?';
        $stmt = $pdo->prepare($sql);
        verifierPrepare($stmt);
        verifierExecute($stmt->execute([$_GET['idUsager']]));
        $result = $stmt->fetchAll();
    
        $idMedecinRef = array_column($result, 'medecinReferent')[0];
        $civilite = array_column($result, 'civilite')[0];
        $nom = array_column($result, 'nom')[0];
        $prenom = array_column($result, 'prenom')[0];
        $adresse = array_column($result, 'adresse')[0];
        $ville = array_column($result, 'ville')[0];
        $codePostal = array_column($result, 'codePostal')[0];
        $numeroSecuriteSociale = array_column($result, 'numeroSecuriteSociale')[0];
        $dateNaissance = array_column($result, 'dateNaissance')[0];
        $lieuNaissance = array_column($result, 'lieuNaissance')[0];
    }
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="style.css">
    <title> Modification d'un usager </title>
</head>

<body id="body_fond">
    <?php include 'header.html' ?>

    <?php if (!empty($popup)) { echo $popup; } ?>

    <div class="titre_formulaire">
        <h1> Modification d'un usager </h1>
    </div>

    <form class="formulaire" action="modificationUsager.php?idUsager=<?php echo $_GET['idUsager']; ?>" method="post">
        <div class="conteneur_civilite">
            Civilité
            <div class="choix_civilite">
                <input type="radio" id="civM" name="civ" value="M" <?php if ($civilite == 'M') { echo 'checked';} ?> />
                <label for="civM">M</label>
                <img src="Images/homme.png" alt="Homme" class="image_civilite">
            </div>
            <div class="choix_civilite">
                <input type="radio" id="civMme" name="civ" value="Mme" <?php if ($civilite == 'Mme') { echo 'checked';} ?> />
                <label for="civMme">Mme</label>
                <img src="Images/femme.png" alt="Femme" class="image_civilite">
            </div>
        </div>
        <div class="ligne_formulaire">
            <div class="colonne_formulaire moitie">
                Nom <input type="text" name="nom" value="<?php echo $nom ?>" maxlength=50 required>
            </div>
            <div class="colonne_formulaire moitie">
                Prénom <input type="text" name="prenom" value="<?php echo $prenom ?>" maxlength=50 required>
            </div>
        </div>
        <div class="ligne_formulaire">
            <div class="colonne_formulaire moitie">
                Adresse <input type="text" name="adr" value="<?php echo $adresse ?>" maxlength=100 class="espaces_permis" required>
            </div>
        </div>
        <div class="ligne_formulaire">
            <div class="colonne_formulaire large">
                Ville <input type="text" name="ville" value="<?php echo $ville ?>" maxlength=50 required>
            </div>
            <div class="colonne_formulaire petit">
                Code postal <input type="text" name="cp" value="<?php echo $codePostal ?>" minlength=5 maxlength=5 class="chiffres_uniquement" required>
            </div>
        </div>
        <div class="ligne_formulaire">
            <div class="colonne_formulaire moitie">
                N° Sécurité sociale <input type="text" name="nss" value="<?php echo $numeroSecuriteSociale ?>" minlength=15 maxlength=15 class="chiffres_uniquement" required>
            </div>
        </div>
        <div class="ligne_formulaire">
            <div class="colonne_formulaire moitie">
                Date de naissance <input type="date" name="date" value="<?php echo $dateNaissance ?>" required>
            </div>
            <div class="colonne_formulaire moitie">
                Lieu de naissance <input type="text" name="lieu" value="<?php echo $lieuNaissance ?>" maxlength=50 required>
            </div>
        </div>
        Médecin reférent <?php creerComboboxMedecins($pdo, $idMedecinRef, 'Aucun médecin référent'); ?>
        </select>
        <div class="conteneur_boutons">
            <input type="reset" name="Vider" value="Vider">
            <input type="submit" name="Confirmer" value="Confirmer">
        </div>
    </form>
    <!-- Script pour formater les inputs -->
    <script src="format-texte-input.js"></script>
</body>

</html>