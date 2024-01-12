<?php session_start();
    if (isset($_SESSION['utilisateur']) && !empty($_SESSION['utilisateur'])){
        header('Location: index.php'); 
        exit();
    }
    if (isset($_POST['connexion']) && isset($_POST['nom']) && isset($_POST['motDePasse'])) {
        $nomUtilisateur = $_POST['nom'];
        $motDePasse = $_POST['motDePasse'];
    
        $messageErreur = '';
        if ($nomUtilisateur == 'CABINET' && $motDePasse == 'CABINET') {
            $_SESSION['utilisateur'] = $nomUtilisateur;
            header('Location: index.php');
            exit();
        } else {
            // Affichage de la popup d'erreur
            $messageErreur = '<div class="popup erreur" style="margin-top:0px">'.
                                'Nom d\'utilisateur ou mot de passe incorrect.'.
                             '</div>';
        }
    }
?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8" />
    <title> Connexion </title>
    <link rel="stylesheet" href="style.css">
</head>
<body id='body_fond'>
    <div id='conteneur_connexion'>
            <div id='header_connexion'>
                <h2>Connexion au cabinet</h2>
            </div>
            <div id='corps_connexion'>
                <?php if (isset($messageErreur)){ echo $messageErreur; } ?>
                <form method='post' action='authentification.php'>
                    <input type='text' name='nom' placeholder="Nom d'utilisateur" autocomplete="off" required>
                    <input type='password' name='motDePasse' placeholder="Mot de passe" autocomplete="off" required>
                    <input type='submit' name='connexion' value='Connexion'>
                </form>
            </div>
            <div id='footer_connexion'>
                <p>Footer de la page ©copyright</p>
            </div>
    </div>    
</body>
</html>