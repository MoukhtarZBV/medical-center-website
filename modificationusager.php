<?php session_start();
    require('fonctions.php');
    verifierAuthentification();
    $pdo = creerConnexion();

    if (!empty($_POST['Confirmer'])) {
        $stmt = $pdo->prepare('UPDATE usager SET nom = ?, prenom = ?, civilite = ?, adresse = ?, codePostal = ?, 
                                                ville = ?, numeroSecuriteSociale = ?, dateNaissance = ?, lieuNaissance = ?, medecinReferent = ?
                                            WHERE idUsager = ?');
        verifierPrepare($stmt);
        $medecinReferent = empty($_POST['idMedecin']) ? null : $_POST['idMedecin'];
        verifierExecute($stmt->execute([$_POST['nom'], $_POST['prenom'], $_POST['civ'], $_POST['adr'], $_POST['cp'], 
                                        $_POST['ville'], $_POST['nss'], $_POST['date'],
                                        $_POST['lieu'], $medecinReferent, $_GET['idUsager']]));
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
    <title> Modification d'usager </title>
</head>

<body id="body_fond">
    <?php include 'header.html' ?>

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
                Code postal <input type="text" name="cp" value="<?php echo $codePostal ?>" minlength=5 maxlength=5 oninput="chiffresUniquement(event)" required>
            </div>
        </div>
        <div class="ligne_formulaire">
            <div class="colonne_formulaire moitie">
                N° Sécurité sociale <input type="text" name="nss" value="<?php echo $numeroSecuriteSociale ?>" minlength=15 maxlength=15 oninput="chiffresUniquement(event)" required>
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
</body>

</html>