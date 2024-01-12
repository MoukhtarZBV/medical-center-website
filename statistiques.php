<?php session_start();
    require('fonctions.php');
    verifierAuthentification();
    $pdo = creerConnexion();
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
    <header id="menu_navigation">
        <div id="logo_site">
            <a href="accueil.html"><img src="Images/logo.png" width="250"></a>
        </div>
        <nav id="navigation">
            <label for="hamburger_defiler" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </label>
            <input class="defiler" type="checkbox" id="hamburger_defiler" role="button" aria-pressed="true">
            <ul class="headings">
                <li><a class="lien_header" href="affichageUsagers.php">Usagers</a></li>
                <li><a class="lien_header" href="affichageMedecins.php">Médecins</a></li>
                <li><a class="lien_header" href="affichageConsultations.php">Consultations</a></li>
                <li><a class="lien_header" href="statistiques.php">Statistiques</a></li>
            </ul>
        </nav>
    </header>
    
    <main class="main_affichage">
    <h1> Les statistiques </h1>
    <?php
        creerConnexion();

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
    </main>
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