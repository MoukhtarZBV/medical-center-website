<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <title> Modification d'un médecin </title>
    </head>
    <?php
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=cabinetmed", 'root', '');
        } catch (Exception $e) {
            echo ("Erreur : ".$e);
        }
            if (isset($_GET['idMedecin'])){

                $sql = 'SELECT * FROM medecin WHERE idMedecin = '.$_GET['idMedecin'];
                $stmt = $pdo->prepare($sql);
                if ($stmt == false){
                    echo 'ERREUR';
                }
                $stmt->execute();
                $result = $stmt->fetchAll();

                $civilite = array_column($result, 'civilite')[0];
                $nom = array_column($result, 'nom')[0];
                $prenom = array_column($result, 'prenom')[0];
            }

            if (isset($_POST['valider'])){
                $sql = 'UPDATE medecin  SET nom = \''.$_POST['nom'].'\',
                                            prenom = \''.$_POST['prenom'].'\',
                                            civilite = \''.$_POST['civilite'].'\'
                                        WHERE id = \''.$_GET['idMedecin'].'\';';
                $bdd->query($sql);
            }
    ?>
    <body>

            <h1> Modification d'un usager </h1>

            <form action="modificationmedecin.php" method="post">
                Civilité    <input type="radio" id="civM" name="civ" value="M" <?php if ($civilite == 'M'){ echo 'checked';} ?> />
                            <label for="civM">M</label>
                            <input type="radio" id="civMme" name="civ" value="Mme" <?php if ($civilite == 'Mme'){ echo 'checked';} ?> />
                            <label for="civMme">Mme</label><br><br>
                Nom <input type="text" name="nom" maxlength=50 value='<?php echo $nom ?>'><br><br>
                Prénom <input type="text" name="prenom" maxlength=50 value='<?php echo $prenom ?>'><br><br>
                <input type="submit" name="Valider" value="Confirmer">
                <input type="reset" name="Vider" value ="Vider">
            </form>

    </body>
    <style>
        body {
    font-family: Arial, sans-serif;
    background-color: #f4f4f4;
    padding: 20px;
}

form {
    max-width: 400px;
    margin: 0 auto;
    background: white;
    padding: 20px;
    box-shadow: 2px 5px 10px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

input[type="text"],
input[type="submit"],
input[type="reset"] {
    width: 95%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ddd;
    box-sizing: border-box;
    border-radius: 4px;
}

input[type="submit"],
input[type="reset"] {
    width: auto;
    background-color: #5cb85c;
    color: white;
    cursor: pointer;
    border: none;
    transition: background-color 0.3s;
}

input[type="reset"] {
    background-color: #f0ad4e;
}

input[type="submit"]:hover,
input[type="reset"]:hover {
    opacity: 0.9;
}

input[type="radio"] {
    margin-right: 5px;
}

label {
    margin-right: 15px;
}

label, input {
    cursor: pointer;
}
</style>
</html>