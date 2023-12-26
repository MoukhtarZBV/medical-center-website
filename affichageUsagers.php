<?php session_start();
    

?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="accueil.css">
    <title> Liste des usagers </title>
</head>
<body>
    <main>
    <h1> Liste des usagers </h1>
    <div class="conteneurCentre">
    <form method="post" action="affichageUsagers.php">
  
            <td><input type="text" name="criteres" value='<?php if (isset($_POST['criteres'])) echo $_POST['criteres'] ?>'></td>
            
            <input type="reset" value="Vider" name="vider">
            <input type="submit" value="Rechercher" name="valider">
        </form>
        <br><br>
        <table class="tableResultats"> 
                    <tr>
                    <th>Nom</th>
                    <th>Prenom</th>
                    <th>Civilite</th>
                    <th>Adresse</th>
                    <th>Ville</th>
                    <th>Code postal</th>
                    <th>Numéro sécurité sociale</th>
                    <th>Date de naissance</th>
                    <th>Lieu de naissance</th>
                    <th>Médecin référent</th>
                    </tr>
        <?php
            
            try {
                $pdo = new PDO('mysql:host=localhost;dbname=cabinetmed;charset=utf8', 'root', '');
            } catch (Exception $e) {
                echo ("Erreur ".$e);
            }

            // Début de la requête, on sélectionne tous les usages et leur potentiel médecin référent
            $reqUsagers = ' SELECT u.*, m.nom as nomMedecin, m.prenom as prenomMedecin
                            FROM usager u
                            LEFT JOIN medecin m ON u.medecinReferent = m.idMedecin';

            // On sépare les critères saisis avec les espaces
            $listeCriteres = preg_split('/\s+/', $_POST['criteres']);

            $nombreCriteres = count($listeCriteres);
            // Si le dernier critère est simplement un espace, on retire un au nombre de critères
            if ($listeCriteres[count($listeCriteres) - 1] == ''){
              $nombreCriteres--;
            }
            
            // On vérifie, pour chacune des colonnes, si elle correspond à un des critère
            $listeColonnes = array('u.nom', 'u.prenom', 'u.civilite', 'u.ville', 'u.codePostal');
            if ($nombreCriteres > 0){
                $reqUsagers = $reqUsagers.' WHERE ';
                for ($i = 0; $i < count($listeColonnes); $i++) {
                    for ($j = 0; $j < $nombreCriteres; $j++) {
                        $reqUsagers = $reqUsagers.$listeColonnes[$i].' LIKE :critere'.$j.' OR ';
                    }
                }
                // Pour enlever le dernier 'OR'
                $reqUsagers = substr($reqUsagers, 0, -4);
            }

            // On remplace les ':critereX' avec un prepared statement
            $stmt = $pdo->prepare($reqUsagers);
            for ($i = 0; $i < $nombreCriteres; $i++){
              $stmt->bindParam(':critere'.$i, $listeCriteres[$i]);
            }

            // On execute la requête et on affiche toutes les lignes renvoyées
            if (!$stmt->execute()) { print_r($stmt->errorInfo()); }
            while ($dataUsager = $stmt->fetch()){
                echo '<tr><td>'.$dataUsager['nom'].'</td>'.
                        '<td>'.$dataUsager['prenom'].'</td>'.
                        '<td>'.$dataUsager['civilite'].'</td>'.                            
                        '<td>'.$dataUsager['adresse'].'</td>'.
                        '<td>'.$dataUsager['ville'].'</td>'.
                        '<td>'.$dataUsager['codePostal'].'</td>'.
                        '<td>'.$dataUsager['numeroSecuriteSociale'].'</td>'.
                        '<td>'.$dataUsager['dateNaissance'].'</td>'.
                        '<td>'.$dataUsager['lieuNaissance'].'</td>'.
                        '<td>'.$dataUsager['nomMedecin'].' '.$dataUsager['prenomMedecin'].'</td>'.
                        '<td>'.'<a href = \'modificationusager.php?idUsager='.$dataUsager[0].'\'><img src="Images/modifier.png" alt=""width=30px></img></a>'.'</td>'.
                        '<td>'.'<a href = \'suppression.php?id='.$dataUsager[0].'&type=usager\'><img src="Images/supprimer.png" alt=""width=30px></img></a>'.'</td>'.'</tr>';
            }
        ?>
        </table> 
          </div>
          </main>
</body>

</html>