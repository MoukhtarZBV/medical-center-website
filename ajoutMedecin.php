<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <title> Ajout d'un médecin </title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="header.css">
</head>

<body id='body_fond'>

    <header id="menu_navigation">
        <div id="logo_site">
            <img src="delete.png" width="50">
        </div>
        <nav id="navigation">
            <label for="hamburger_defiler" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </label>
            <input class="defiler" type="checkbox" id="hamburger_defiler" role="button" aria-pressed="true">
            <ul class="headings">
                <li><a class="lien_header" href="Accueil.html">Accueil</a></li>
                <li class="deroulant"><a class="lien_header">Ajouter</a>
                    <ul class="liste_deroulante">
                        <li><a class="lien_header" href="creationusager.php">Un usager</a></li>
                        <li><a class="lien_header" href="creationmedecin.php">Un médecin</a></li>
                        <li><a class="lien_header" href="creationconsultation.php">Une consultation</a></li>
                    </ul>
                </li>
                <li class="deroulant"><a class="lien_header">Consulter</a>
                    <ul class="liste_deroulante">
                        <li><a class="lien_header" href="Competence1.html">Les usagers</a></li>
                        <li><a class="lien_header" href="Competence2.html">Les médecins</a></li>
                        <li><a class="lien_header" href="Competence3.html">Les consultations</a></li>
                    </ul>
                </li>
                <li><a class="lien_header" href="Contact.html">Statistiques</a></li>
            </ul>
        </nav>
    </header>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["Confirmer"])) {

        try {
            $pdo = new PDO("mysql:host=localhost;dbname=cabinetmed", 'root', '');
        } catch (Exception $e) {
            echo ("Erreur : " . $e);
        }

        $civ = $_POST['civ'];
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];

        $stmt = $pdo->prepare("INSERT INTO medecin (civilite, nom, prenom)
                 VALUES (?,?,?)");
        $stmt->bindParam(1, $civ, PDO::PARAM_STR);
        $stmt->bindParam(2, $nom, PDO::PARAM_STR);
        $stmt->bindParam(3, $prenom, PDO::PARAM_STR);

        $message = '';
        $classeMessage = '';
        try {
            $stmt->execute();
            $message = 'Le médecin <strong>' . $nom . ' ' . $prenom . '</strong> a été ajouté !';
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

        // Affichage de la popup d'erreur ou de succés
        echo '<div class="popup ' . $classeMessage . '">' .
            $message .
            '</div>';
    }
    ?>

    <div class="titre_formulaire">
        <h1>Ajout d'un médecin</h1>
    </div>

    <form class="formulaire" action="ajoutMedecin.php" method="post">

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
        <div class="conteneur_boutons">
            <input type="reset" name="Vider" value="Vider">
            <input type="submit" name="Confirmer" value="Confirmer">
        </div>
    </form>

</body>

</html>