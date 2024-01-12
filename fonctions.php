<?php
    function verifierAuthentification(){
        if (!isset($_SESSION['utilisateur']) || empty($_SESSION['utilisateur'])){
            header('Location: authentification.php'); exit();
        }
    }

    function creerConnexion(){
        $pdo = null;
        try {
            $pdo = new PDO('mysql:host=localhost;dbname=id21781824_cabinet;charset=utf8', 'id21781824_rsp4626a', 'Pizza4Frangipane_');
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

    function creerComboboxUsagers($pdo, $idUsager) {
        $stmt = $pdo->prepare("SELECT idUsager, numeroSecuriteSociale, civilite, nom, prenom FROM usager ORDER BY nom, prenom ASC");
        verifierPrepare($stmt);
        verifierExecute($stmt->execute());
        echo '<select name="idUsager">
        <option value="">Tous les usagers</option>';
        while ($dataUsager = $stmt->fetch()) {
            $id = $dataUsager["idUsager"];
            $titre = str_pad($dataUsager["civilite"] . '. ', 5, ' ') . $dataUsager["nom"] . ' ' . $dataUsager["prenom"] . ' (' . $dataUsager["numeroSecuriteSociale"] . ')';
            $selected = $idUsager == $id ? 'selected' : '';
            echo '<option value=' . $id . ' ' . $selected . '> ' . $titre . '</option>';
        }
        echo '</select>';
    } 

    function creerComboboxMedecins($pdo, $idMedecin, $message) {
        $stmt = $pdo->prepare("SELECT idMedecin, civilite, nom, prenom FROM medecin");
        verifierPrepare($stmt);
        verifierExecute($stmt->execute());
        echo '<select name="idMedecin">
        <option value="">'.$message.'</option>';
        while ($dataMedecin = $stmt->fetch()) {
            $id = $dataMedecin["idMedecin"];
            $titre = $dataMedecin["civilite"] . '. ' . $dataMedecin["nom"] . ' ' . $dataMedecin["prenom"];
            $selected = $idMedecin == $id ? 'selected' : '';
            echo '<option value=' . $id . ' ' . $selected . '> ' . $titre . '</option>';
        }
        echo '</select>';
    } 

?>