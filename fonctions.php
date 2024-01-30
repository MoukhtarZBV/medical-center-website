<?php
    function verifierAuthentification(){
        if (!isset($_SESSION['utilisateur']) || empty($_SESSION['utilisateur'])){
            header('Location: authentification.php'); exit();
        }
    }

    function creerConnexion(){
        $pdo = null;
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=cabinetmed;charset=utf8', 'root', '');
        } catch (Exception $e) {
            echo ("Erreur ".$e);
            exit();
        }
        return $pdo;
    }

    function verifierPrepare($stmt){
        if (!$stmt) { 
            echo "Erreur lors d'un prepare statement : " . $stmt->errorInfo(); exit(); 
        }
    }

    function verifierExecute($stmt){
        if (!$stmt) {
            echo "Erreur lors d'un execute statement : " . $stmt->errorInfo(); exit(); 
        }
    }

    function formaterDate($date){
        $elementsDate = explode('-', $date);
        $dateFormatee = $elementsDate[2] . '/' . $elementsDate[1] . '/' . $elementsDate[0];
        return $dateFormatee;
    }

    function consultationsChevauchantes($heureDebutC1, $dureeC1, $heureDebutC2, $dureeC2) {
        // On crée les dates de début et de fin des deux consultations
        $debutC1 = DateTime::createFromFormat('H:i', $heureDebutC1);
        $finC1 = clone $debutC1;
        list($hours, $minutes) = explode(':', $dureeC1);
        $finC1->add(new DateInterval("PT{$hours}H{$minutes}M"));

        $debutC2 = DateTime::createFromFormat('H:i', $heureDebutC2);
        $finC2 = clone $debutC2;
        list($hours, $minutes) = explode(':', $dureeC2);
        $finC2->add(new DateInterval("PT{$hours}H{$minutes}M"));

        // On vérifie si les consultations se chevauchent
        if (($debutC1 >= $debutC2 AND $debutC1 < $finC2) ||
            ($finC1 > $debutC2 AND $finC1 <= $finC2) || 
            ($debutC2 >= $debutC1 AND $debutC2 < $finC1)) {
                return true;
        }
        return false;
    }

    function creerComboboxUsagers($pdo, $idUsager, $message) {
        $stmt = $pdo->prepare("SELECT idUsager, numeroSecuriteSociale, civilite, nom, prenom, medecinReferent FROM usager ORDER BY nom, prenom ASC");
        verifierPrepare($stmt);
        verifierExecute($stmt->execute());
        echo '<select name="idUsager" id="combobox_usagers">';
        if ($message != null){
            echo '<option value="">' . $message . '</option>';
        }
        while ($dataUsager = $stmt->fetch()) {
            $id = $dataUsager["idUsager"];
            $titre = str_pad($dataUsager["civilite"] . '. ', 5, ' ') . $dataUsager["nom"] . ' ' . $dataUsager["prenom"] . ' (' . $dataUsager["numeroSecuriteSociale"] . ')';
            $selected = $idUsager == $id ? 'selected' : '';
            echo '<option value=' . $id . ' ' . $selected . ' data-idMedecinRef=' . $dataUsager["medecinReferent"] . '> ' . $titre . '</option>';
        }
        echo '</select>';
    } 

    function creerComboboxMedecins($pdo, $idMedecin, $message) {
        $stmt = $pdo->prepare("SELECT idMedecin, civilite, nom, prenom FROM medecin");
        verifierPrepare($stmt);
        verifierExecute($stmt->execute());
        echo '<select name="idMedecin" id="combobox_medecins">';
        if ($message != null){
            echo '<option value="">' . $message . '</option>';
        }
        while ($dataMedecin = $stmt->fetch()) {
            $id = $dataMedecin["idMedecin"];
            $titre = $dataMedecin["civilite"] . '. ' . $dataMedecin["nom"] . ' ' . $dataMedecin["prenom"];
            $selected = $idMedecin == $id ? 'selected' : '';
            echo '<option value=' . $id . ' ' . $selected . '> ' . $titre . '</option>';
        }
        echo '</select>';
    } 

?>