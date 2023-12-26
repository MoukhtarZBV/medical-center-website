<!DOCTYPE HTML>
<html>
    <head>
        <meta charset="utf-8">
        <title> Modification d'usager </title>
    </head>
    <?php
        try {
            $pdo = new PDO("mysql:host=localhost;dbname=cabinetmed", 'root', '');
        } catch (Exception $e) {
            echo ("Erreur : ".$e);
        }
            if (isset($_GET['idUsager'])){
                $sql = 'SELECT * FROM usager WHERE idUsager = '.$_GET['idUsager'];
                $stmt = $pdo->prepare($sql);
                if ($stmt == false){
                    echo 'ERREUR';
                }
                $stmt->execute();
                $result = $stmt->fetchAll();

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

            if (isset($_POST['valider'])){
                $sql = 'UPDATE usager  SET nom = \''.$_POST['nom'].'\',
                                            prenom = \''.$_POST['prenom'].'\',
                                            civilite = \''.$_POST['civilte'].'\',
                                            adresse = \''.$_POST['adresse'].'\',
                                            codePostal = \''.$_POST['codePostal'].'\',
                                            ville = \''.$_POST['ville'].'\',
                                            numeroSecuriteSociale = \''.$_POST['numeroSecuriteSociale'].'\',
                                            dateNaissance = \''.$_POST['dateNaissance'].'\',
                                            lieuNaissance = \''.$_POST['lieuNaissance'].'\'
                                        WHERE id = \''.$_GET['idContact'].'\';';
                $bdd->query($sql);
            }
    ?>
    <body>

            <h1> Modification d'un usager </h1>

            <form action=<?php echo 'modificationusager.php?idUsager='.$_GET['idUsager'].'\''; ?> method="post">
                Civilité    <input type="radio" id="civM" name="civ" value="M" <?php if ($civilite == 'M'){ echo 'checked';} ?> />
                            <label for="civM">M</label>
                            <input type="radio" id="civMme" name="civ" value="Mme" <?php if ($civilite == 'Mme'){ echo 'checked';} ?> />
                            <label for="civMme">Mme</label><br><br>
                Nom <input type="text" name="nom" maxlength=50 value='<?php echo $nom ?>'><br><br>
                Prénom <input type="text" name="prenom" maxlength=50 value='<?php echo $prenom ?>'><br><br>
                Adresse <input type="text" name="adresse" maxlength=100 value='<?php echo $adresse ?>'><br><br>
                Ville <input type="text" name="ville" maxlength=50 value='<?php echo $ville ?>'><br><br>
                Code postal <input type="text" name="codePostal" maxlength=5 value='<?php echo $codePostal ?>'><br><br>
                N° Sécurité sociale <input type="text" name="numeroSecuriteSociale" maxlength=15 value='<?php echo $numeroSecuriteSociale ?>'><br><br>
                Date de naissance <input type="date" name="dateNaissance" value='<?php echo $dateNaissance ?>'><br><br>
                Lieu de naissance <input type="text" name="lieuNaissance" value='<?php echo $lieuNaissance ?>'><br><br>
                Médecin reférent <select name="medecinReferent" id="medRef">
                    <?php
                    
                        try {
                            $pdo = new PDO("mysql:host=localhost;dbname=cabinetmed", 'root', '');
                        } catch (Exception $e) {
                            echo ("Erreur : ".$e);
                        }

                        $stmt = $pdo->prepare("SELECT idMedecin, medecin.civilite, medecin.nom, medecin.prenom FROM medecin, usager WHERE idMedecin = medecinReferent AND idUsager=".$_GET['idUsager']);
                        if ($stmt == false) {
                            echo "PREPARE ERROR"; 
                        } else {
                            $stmt->execute();
                            if ($row = $stmt->fetch()) {
                                $id = $row["idMedecin"];
                                $titre = $row["civilite"].'. '.$row["nom"].' '.$row["prenom"];
                                echo '<option value='.$id.'> '.$titre.'</option>';
                            } else {
                                echo '<option value="">--Veuillez choisir un médecin reférent</option>';
                            }
                        }
                       

                        $stmt = $pdo->prepare(" SELECT idMedecin, civilite, nom, prenom 
                                                FROM medecin 
                                                WHERE idMedecin NOT IN (SELECT medecinReferent
                                                                        FROM usager
                                                                        WHERE idUsager = ".$_GET['idUsager'].')');
                        if ($stmt == false) {
                            echo "PREPARE ERROR";
                        } else {
                            $stmt -> execute();
                            while ($row = $stmt->fetch()) {
                                $id = $row["idMedecin"];
                                $titre = $row["civilite"].'. '.$row["nom"].' '.$row["prenom"];
                                echo '<option value='.$id.'> '.$titre.'</option>';
                            }
                        }
                    ?>
                </select> 
                <input type="submit" name="Valider" value="Confirmer">
                <input type="reset" name="Reinitialiser" value ="Réinitialiser">
            </form>
<style>
    body {
    font-family: 'Arial', sans-serif;
    background-color: #f4f4f4;
    padding: 20px;
}

form {
    max-width: 600px;
    margin: 20px auto;
    background: white;
    padding: 20px;
    box-shadow: 2px 5px 10px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

input[type="text"],
input[type="date"],
input[type="submit"],
input[type="reset"],
select {
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

label, input, select {
    cursor: pointer;
}

select {
    width: 100%;
    display: block;
}
</style>
    </body>
</html>