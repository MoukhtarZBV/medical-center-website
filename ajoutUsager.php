<?php session_start();
    require('fonctions.php');
    verifierAuthentification();
    $pdo = creerConnexion();

    if (isset($_POST["Confirmer"]) && !empty($_POST["Confirmer"])) {
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

        $stmt = $pdo->prepare("INSERT INTO usager (civilite, nom, prenom, adresse, ville, codePostal, numeroSecuriteSociale, dateNaissance, lieuNaissance, medecinReferent)
                                VALUES (?,?,?,?,?,?,?,?,?,?)");
        verifierPrepare($stmt);
        verifierExecute($stmt->execute([$civ, $nom, $prenom, $adr, $ville, $cp, $nss, $date, $lieu, $idMed]));

        $message = '';
        $classeMessage = '';
        try {
            $stmt->execute();
            $message = 'L\'usager <strong>' . $nom . ' ' . $prenom . '</strong> a été ajouté !';
            $classeMessage = 'succes';
        } catch (PDOException $e) {
            $codeErreur = $e->getCode();
            // Si le code vaut 23000, alors la contrainte d'unicité du numéro de sécurité sociale a été violée
            if ($codeErreur == '23000') {
                $message = 'Un usager avec le numéro de sécurité sociale <strong>' . $nss . '</strong> existe déjà.';
            } else {
                $message = 'Une erreur s\'est produite : ' . $e->getMessage();
            }
            $classeMessage = 'erreur';
        }

        // Affichage de la popup d'erreur ou de succés
        if (!empty($message)){
            $popup = '<div class="popup ' . $classeMessage . '">' . $message .'</div>';
        }
    }
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <title> Ajout d'un usager </title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="header.css">
</head>

<body id="body_fond">
    <?php include 'header.html' ?>

    <?php if (!empty($popup)) { echo $popup; } ?>

    <div class="titre_formulaire">
        <h1>Ajout d'un usager</h1>
    </div>

    <form class="formulaire" action="ajoutUsager.php" method="post">

        <div class="conteneur_civilite">
            Civilité
            <div class="choix_civilite">
                <input type="radio" id="civM" name="civ" value="M" checked />
                <label for="civM">M</label>
                <img src="Images/homme.png" alt="Homme" class="image_civilite">
            </div>
            <div class="choix_civilite">
                <input type="radio" id="civMme" name="civ" value="Mme" />
                <label for="civMme">Mme</label>
                <img src="Images/femme.png" alt="Femme" class="image_civilite">
            </div>
        </div>
        <div class="ligne_formulaire">
            <div class="colonne_formulaire moitie">
                Nom <input type="text" name="nom" value="" maxlength=50 required>
            </div>
            <div class="colonne_formulaire moitie">
                Prénom <input type="text" name="prenom" value="" maxlength=50 required>
            </div>
        </div>
        <div class="ligne_formulaire">
            <div class="colonne_formulaire moitie">
                Adresse <input type="text" name="adr" value="" maxlength=100 class="espaces_permis" required>
            </div>
        </div>
        <div class="ligne_formulaire">
            <div class="colonne_formulaire large">
                Ville <input type="text" name="ville" value="" maxlength=50 required>
            </div>
            <div class="colonne_formulaire petit">
                Code postal <input type="text" name="cp" value="" minlength=5 maxlength=5 oninput="chiffresUniquement(event)" required>
            </div>
        </div>
        <div class="ligne_formulaire">
            <div class="colonne_formulaire moitie">
                N° Sécurité sociale <input type="text" name="nss" value="" minlength=15 maxlength=15 oninput="chiffresUniquement(event)" required>
            </div>
        </div>
        <div class="ligne_formulaire">
            <div class="colonne_formulaire moitie">
                Date de naissance <input type="date" name="date" value="" required>
            </div>
            <div class="colonne_formulaire moitie">
                Lieu de naissance <input type="text" name="lieu" value="" maxlength=50 required>
            </div>
        </div>
        Médecin référent <?php creerComboboxMedecins($pdo, null, 'Aucun médecin référent'); ?>
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