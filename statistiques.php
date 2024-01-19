<?php session_start();
    require('fonctions.php');
    verifierAuthentification();
    $pdo = creerConnexion();

    $reqHommes = 'SELECT
                    SUM(CASE WHEN civilite = \'M\' AND DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), dateNaissance)), \'%Y\') < 25 THEN 1 ELSE 0 END) AS hommesMoins25,
                    SUM(CASE WHEN civilite = \'M\' AND DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), dateNaissance)), \'%Y\') BETWEEN 25 AND 50 THEN 1 ELSE 0 END) AS hommesEntre25et50,
                    SUM(CASE WHEN civilite = \'M\' AND DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), dateNaissance)), \'%Y\') > 50 THEN 1 ELSE 0 END) AS hommesPlus50
                    FROM usager';
    $reqFemmes = 'SELECT
                    SUM(CASE WHEN civilite = \'Mme\' AND DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), dateNaissance)), \'%Y\') < 25 THEN 1 ELSE 0 END) AS femmesMoins25,
                    SUM(CASE WHEN civilite = \'Mme\' AND DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), dateNaissance)), \'%Y\') BETWEEN 25 AND 50 THEN 1 ELSE 0 END) AS femmesEntre25et50,
                    SUM(CASE WHEN civilite = \'Mme\' AND DATE_FORMAT(FROM_DAYS(DATEDIFF(NOW(), dateNaissance)), \'%Y\') > 50 THEN 1 ELSE 0 END) AS femmesPlus50
                    FROM usager';
    $statsHommes = $pdo->query($reqHommes)->fetch();
    $statsFemmes = $pdo->query($reqFemmes)->fetch();

?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="header.css">
    <link rel="stylesheet" href="style.css">
    <title> Statistiques </title>
</head>
<body>
    <?php include 'header.html' ?>
    
    <main class="main_affichage">
    <h1> Les statistiques </h1>
    
        <table class="tableFiltres">
            <tr>
                <th>Tranche d'âge</th>
                <th>Nombre d'hommes</th>
                <th>Nombre de femmes</th>
            </tr>
            <tr>
                <td>Moins de 25 ans</td>
                <td><?php echo $statsHommes['hommesMoins25'] ?></td>
                <td><?php echo $statsFemmes['femmesMoins25'] ?></td>
            </tr>   
            <tr>
                <td>Entre 25 et 50 ans</td>
                <td><?php echo $statsHommes['hommesEntre25et50'] ?></td>
                <td><?php echo $statsFemmes['femmesEntre25et50'] ?></td>
            </tr>    
            <tr>
                <td>Plus de 50 ans</td>
                <td><?php echo $statsHommes['hommesPlus50'] ?></td>
                <td><?php echo $statsFemmes['femmesPlus50'] ?></td>
            </tr>     
        </table>
        
        <br><br>
        <table id="table_affichage"> 
        <thead>
            <tr>
                <th>Civilite</th>
                <th>Nom</th>
                <th>Prenom</th>
                <th>Durée totale des consultations</th>
            </tr>
        </thead><tbody>
    <?php
        $reqDureeTotale = $pdo->query('SELECT civilite, nom, prenom, TIME_FORMAT(SEC_TO_TIME(SUM(TIME_TO_SEC(duree))), \'%kh%i\') as duree FROM medecin m, consultation c WHERE m.idMedecin = c.idMedecin GROUP BY nom, prenom, civilite');
        while ($donnees = $reqDureeTotale->fetch()){
            echo '<tr>
                    <td>'.$donnees['civilite'].'</td>
                    <td>'.$donnees['nom'].'</td>
                    <td>'.$donnees['prenom'].'</td>
                    <td>'.$donnees['duree'].'</td>
                 </tr>';
        }
    ?>
    </tbody></table>
    </main>
</body>
</html>