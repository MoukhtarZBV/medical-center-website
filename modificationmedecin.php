<?php session_start();
    require('fonctions.php');
    require('fonctionsVerifierInputs.php');
    verifierAuthentification();
    $pdo = creerConnexion();

    if (!empty($_POST['Confirmer'])) {
        $civ = $_POST['civ'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];

        $message = '';
        $classeMessage = '';
        $popup = '';
        if (!inputSansEspacesCorrect($nom, TAILLE_NOM)){
            $message = 'Le nom n\'est pas correctement saisi';
            $classeMessage = 'erreur';
        } else if (!inputSansEspacesCorrect($prenom, TAILLE_PRENOM)){
            $message = 'Le prénom n\'est pas correctement saisi';
            $classeMessage = 'erreur';
        } else {
            $stmt = $pdo->prepare('UPDATE medecin SET nom = ?, prenom = ?, civilite = ? WHERE idMedecin = ?');
            verifierPrepare($stmt);
            try {
                verifierExecute($stmt->execute([$nom, $prenom, $civ, $_GET['idMedecin']]));
                $message = 'Le médecin <strong>' . $nom . ' ' . $prenom . '</strong> a été modifié !';
                $classeMessage = 'succes';
            } catch (PDOException $e) {
                $codeErreur = $e->getCode();
                // Si le code vaut 23000, alors la contrainte d'unicité du nom et prénom a été violée
                if ($codeErreur == '23000') {
                    $message = 'Le médecin <strong>' . $nom . ' ' . $prenom . '</strong> existe déjà.';
                } else {
                    $message = 'Une erreur s\'est produite : ' . $e->getMessage();
                }
                $classeMessage = 'erreur';
            }
        }

        // Affichage de la popup d'erreur ou de succés
        if (!empty($message)){
            $popup = '<div class="popup ' . $classeMessage . '">' . $message .'</div>';
        }
    }

    if (isset($_GET['idMedecin'])) {
        $sql = 'SELECT * FROM medecin WHERE idMedecin = ?';
        $stmt = $pdo->prepare($sql);
        verifierPrepare($stmt);
        verifierExecute($stmt->execute([$_GET['idMedecin']]));
        $result = $stmt->fetchAll();
    
        $civilite = array_column($result, 'civilite')[0];
        $nom = array_column($result, 'nom')[0];
        $prenom = array_column($result, 'prenom')[0];
    }
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="style.css">
    <title> Modification d'un médecin </title>
</head>

<body id="body_fond">
    <?php include 'header.html' ?>

    <?php if (!empty($popup)) { echo $popup; } ?>

    <div class="titre_formulaire">
        <h1> Modification d'un médecin </h1>
    </div>

    <form class="formulaire" action="modificationMedecin.php?idMedecin=<?php echo $_GET['idMedecin']; ?>" method="post">
        <div class="conteneur_civilite">
            Civilité
            <div class="choix_civilite">
                <input type="radio" id="civM" name="civ" value="M" <?php if ($civilite == 'M') { echo 'checked'; } ?>/>
                <label for="civM">M</label>
                <img src="Images/homme.png" alt="Homme" class="image_civilite">
            </div>
            <div class="choix_civilite">
                <input type="radio" id="civMme" name="civ" value="Mme" <?php if ($civilite == 'Mme') { echo 'checked'; } ?> />
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
        <div class="conteneur_boutons">
            <input type="reset" name="Vider" value="Vider">
            <input type="submit" name="Confirmer" value="Confirmer">
        </div>
    </form>
    <!-- Script pour formater les inputs -->
    <script src="format-texte-input.js"></script>
</body>
</html>