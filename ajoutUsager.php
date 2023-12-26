<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8">
    <title> Ajout d'un usager </title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="header.css">
</head>

<body id="body_fond">
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
                        <li><a class="lien_header" href="ajoutUsager.php">Un usager</a></li>
                        <li><a class="lien_header" href="ajoutMedecin.php">Un médecin</a></li>
                        <li><a class="lien_header" href="creationconsultation.php">Une consultation</a></li>
                    </ul>
                </li>
                <li class="deroulant"><a class="lien_header">Consulter</a>
                    <ul class="liste_deroulante">
                        <li><a class="lien_header" href="affichageUsagers.php">Les usagers</a></li>
                        <li><a class="lien_header" href="affichageMedecins.php">Les médecins</a></li>
                        <li><a class="lien_header" href="affichageConsultations.php">Les consultations</a></li>
                    </ul>
                </li>
                <li><a class="lien_header" href="statistiques.php">Statistiques</a></li>
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
        $adr = $_POST['adr'];
        $ville = $_POST['ville'];
        $cp = $_POST['cp'];
        $nss = $_POST['nss'];
        $date = $_POST['date'];
        $lieu = $_POST['lieu'];
        $idMed = $_POST['idMed'];

        $stmt = $pdo->prepare("INSERT INTO usager (civilite, nom, prenom, adresse, ville, codePostal, numeroSecuriteSociale, dateNaissance, lieuNaissance, medecinReferent)
                                VALUES (?,?,?,?,?,?,?,?,?,?)");
        $stmt->bindParam(1, $civ, PDO::PARAM_STR);
        $stmt->bindParam(2, $nom, PDO::PARAM_STR);
        $stmt->bindParam(3, $prenom, PDO::PARAM_STR);
        $stmt->bindParam(4, $adr, PDO::PARAM_STR);
        $stmt->bindParam(5, $ville, PDO::PARAM_STR);
        $stmt->bindParam(6, $cp, PDO::PARAM_STR);
        $stmt->bindParam(7, $nss, PDO::PARAM_STR);
        $stmt->bindParam(8, $date, PDO::PARAM_STR);
        $stmt->bindParam(9, $lieu, PDO::PARAM_STR);
        if ($idMed == "") {
            $idMed = null;
            $stmt->bindParam(10, $idMed, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(10, $idMed, PDO::PARAM_INT);
        }

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
        echo '<div class="popup '.$classeMessage.'">'.
                $message.
             '</div>';
    }
    ?>

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
                Adresse <input type="text" class="input-large" name="adr" value="" maxlength=100 require>
            </div>
        </div>
        <div class="ligne_formulaire">
            <div class="colonne_formulaire large">
                Ville <input type="text" name="ville" value="" maxlength=50 required>
            </div>
            <div class="colonne_formulaire petit">
                Code postal <input type="text" class="input-petit" name="cp" value="" minlength=5 maxlength=5 required>
            </div>
        </div>
        <div class="ligne_formulaire">
            <div class="colonne_formulaire moitie">
                N° Sécurité sociale <input type="text" name="nss" value="" minlength=15 maxlength=15 required>
            </div>
        </div>
        <div class="ligne_formulaire">
            <div class="colonne_formulaire moitie">
                Date de naissance <input type="date" class="input-moitie" name="date" value="" required>
            </div>
            <div class="colonne_formulaire moitie">
                Lieu de naissance <input type="text" class="input-moitie" name="lieu" value="" maxlength=50 required>
            </div>
        </div>
        Médecin reférent <select name="idMed" id="selecteur_medecin_referent">
            <option value="">-- Choisissez un médecin reférent --</option>
            <?php
            if (!isset($pdo)) {
                try {
                    $pdo = new PDO("mysql:host=localhost;dbname=cabinetmed", 'root', '');
                } catch (Exception $e) {
                    echo ("Erreur : " . $e);
                }
            }
            $stmt = $pdo->prepare("SELECT idMedecin, civilite, nom, prenom FROM medecin");
            if ($stmt == false) {
                echo "PREPARE ERROR";
            } else {
                $stmt->execute();
                while ($ligne_formulaire = $stmt->fetch()) {
                    $id = $ligne_formulaire["idMedecin"];
                    $titre = $ligne_formulaire["civilite"] . '. ' . $ligne_formulaire["nom"] . ' ' . $ligne_formulaire["prenom"];
                    echo '<option value=' . $id . '> ' . $titre . '</option>';
                }
            }
            $pdo = null;
            ?>
        </select>
        <div class="conteneur_boutons">
            <input type="reset" name="Vider" value="Vider">
            <input type="submit" name="Confirmer" value="Confirmer">
        </div>
    </form>
</body>

</html>