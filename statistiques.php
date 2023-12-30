<?php session_start();
    

?>
<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="style.css">
    <title> Statistiques </title>
</head>
<body>
    <style>
        table, th, td{
            border : solid 1px black;
            border-collapse: collapse;
            padding: 15px;
        }
    </style>
    <h1> Parcourir </h1>
    <?php
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=cabinetmed;charset=utf8', 'root', '');
        } catch (Exception $e) {
            echo ("Erreur ".$e);
        }

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
                <th onclick="sortTable(0)">Civilite</th>
                <th onclick="sortTable(1)">Nom</th>
                <th onclick="sortTable(2)">Prenom</th>
                <th onclick="sortTable(3)">Durée totale des consultations</th>
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
    <!-- Script pour trier une table en cliquant sur une colonne -->
    <script src="tri-tableau.js"></script>
</body>
<style>
    .tableFiltres {
  border-collapse: collapse; /* Collapse borders */
  margin-top: 1em; /* Space from the preceding content */
}

.tableFiltres th,
.tableFiltres td {
  border: 1px solid #ddd; /* Adding a border */
  padding: 8px 16px; /* Adding some padding */
  text-align: center; /* Aligning text to the left */
}

/* Header row and column styling */
.tableFiltres tr:first-child th,
.tableFiltres tr th:first-child,
.tableFiltres tr td:first-child {
  background-color: #f4f4f4; /* A different bg color for the header cells */
  font-weight: bold; /* Make the font bold */
  text-align: left;
}

/* First column styling */
.tableFiltres tr td:first-child {
  background-color: #e9ecef; /* A different bg color for the first column */
  color: #333;
}

/* Hover effect for rows */
.tableFiltres td:hover {
  background-color: #f5f5f5; /* This will highlight the row on hover */
}


</style>

</html>