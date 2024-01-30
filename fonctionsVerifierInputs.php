<?php   
    define("TAILLE_NOM", 50);
    define("TAILLE_PRENOM", 50);
    define("TAILLE_ADRESSE", 100);
    define("TAILLE_VILLE", 50);
    define("TAILLE_CODE_POSTAL", 5);
    define("TAILLE_NUMERO_SECU", 15);

    function inputSansEspacesCorrect($input, $taille){
        if (!empty($input)) {
            if (!str_contains($input, ' ') && strlen($input) <= $taille && ctype_alpha($input)){
                return true;
            }
        }
        return false;
    }

    function inputAvecUnEspaceCorrect($input, $taille){
        if (!empty($input)) {
            if (!str_contains($input, '  ')  && strlen($input) <= $taille){
                return true;
            }
        }
        return false;
    }

    function tailleInputRespectee($input, $taille){
        if (strlen($input) == $taille) {
            return true;
        }
        return false;
    }

    function inputChiffresUniquementCorrect($input, $taille){
        if (!empty($input)) {
            if (!str_contains($input, ' ') && strlen($input) <= $taille && preg_match('/^\d+$/', $input)){
                return true;
            }
        }
        return false;
    }

    function dateApresLe($dateInput, $dateMin){
        if (!empty($dateInput)) {
            $dateInputConvertie = new DateTime($dateInput);
            $dateMinConvertie = new DateTime($dateMin);
            if ($dateInputConvertie >= $dateMinConvertie){
                return true;
            }
        }
        return false;
    }

    function heureApres8HeureAvant20Heure($dateInput){
        if (!empty($dateInput)) {
            $dateInputConvertie = DateTime::createFromFormat('H:i', $dateInput);
            $date8Heure = DateTime::createFromFormat('H:i', '08:00');
            $date20Heure = DateTime::createFromFormat('H:i', '20:00');
            if ($dateInputConvertie >= $date8Heure && $dateInputConvertie <= $date20Heure) {
                return true;
            }
        }
        return false;
    }

    function dureeSuperieure15MinutesInferieur60Minutes($dateInput){
        if (!empty($dateInput)) {
            $dateInputConvertie = DateTime::createFromFormat('H:i', $dateInput);
            $dureeMin = DateTime::createFromFormat('H:i', '00:05');
            $dureeMax = DateTime::createFromFormat('H:i', '01:00');
            if ($dateInputConvertie >= $dureeMin && $dateInputConvertie <= $dureeMax) {
                return true;
            }
        }
        return false;
    }
?>