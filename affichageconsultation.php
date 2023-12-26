<!DOCTYPE HTML>
<html>

<head>
    <meta charset="utf-8" />
    <link rel="stylesheet" href="accueil.css">
    <title> Consultations </title>
</head>
<body>
    <h1> Parcourir </h1>

    <form method="post" action="affichageconsultation.php">
        <table class="tableFiltres">
            <tr>
                <th>Médecin</th>
                <th>Patient</th>
                <th>Date de consultation</th>
           </tr>
            <tr>
                <td><input type="text" name="medecin" value=' '></td>
                <td><input type="text" name="patient" value=' '></td>
                <td><input type="date" name="date" value=' '></td>
            </tr>   
        </table>
        <input type="reset" value="Vider" name="vider">
        <input type="submit" value="Rechercher" name="valider">
    </form>
    <br><br>

    <table class="tableResultats"> 
        <tr>
            <th>Médecin</th>
            <th>Patient</th>
            <th>Date de consultation</th>
            <th>Heure de consultation</th>
            <th>Durée de consultation</th>
        </tr>

        <?php

            if ($_SERVER["REQUEST_METHOD"] == "POST") {

                $medecin = $_REQUEST["medecin"];
                $patient = $_REQUEST["patient"];
                $date = $_REQUEST["date"];

                try {
                    $pdo = new PDO('mysql:host=localhost;dbname=cabinetmed;charset=utf8', 'root', '');
                } catch (Exception $e) {
                    echo ("Erreur ".$e);
                }
    
                $stmt = $pdo->prepare(  "SELECT CONCAT(m.nom, ' ', m.prenom) as nomMed, CONCAT(u.nom, ' ', u.prenom)
                                        as nomUsager, c.dateConsultation AS dateCons, c.heureDebut AS heure, c.duree
                                        AS duree, CONCAT(c.idMedecin,c.dateConsultation) AS cle
                                        FROM medecin m, usager u, consultation c
                                        WHERE c.idMedecin = m.idMedecin AND c.idUsager = u.idUsager AND 
                                        lower(CONCAT(' ', m.nom, ' ', m.prenom)) LIKE lower(?) AND 
                                        lower(CONCAT(' ', u.nom, ' ', u.prenom)) LIKE lower(?) AND 
                                        (dateConsultation = ? OR ? = '') ORDER BY dateConsultation DESC"    );
    
                if ($stmt == false) {
                    echo "PREPARE ERROR";
                } else {
                    $stmt->execute(["%$medecin%", "%$patient%", "$date", "$date"]);
                    while($row = $stmt->fetch()) {
                        echo '<tr>';
                        echo '<td>'.$row['nomMed'].'</td>';
                        echo '<td>'.$row['nomUsager'].'</td>';
                        echo '<td>'.$row['dateCons'].'</td>';
                        echo '<td>'.$row['heure'].'</td>';
                        echo '<td>'.$row['duree'].'</td>';
                        echo '<td>'.'<a href = \'suppression.php?id='.$row['cle'].'&type=consultation\'> Supprimer </a>'.'</td>'.'</tr>';
                        echo '</tr>';
                    }
                }

            }

        ?>
</body>

</html>       