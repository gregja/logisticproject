<?php

header('Content-Type: application/json');
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

require __DIR__ . '/../../vendor/autoload.php';

if (isset($_GET['test'])) {
    $test = true ;
} else {
    // date système par défaut
    $test = false ;
}

if (isset($_GET['format'])) {
    $format = Logistic\CleanInput::get('format');
    // format "jquerygantt" pour obtenir en sortie un JSON formaté comme suit
    if ($format != 'jquerygantt') {
        $format = '' ;
    }
} else {
    $format = '' ;
}

/*
 * Si test alors on renvoie un jeu de données "en dur" au format JSON
 * (pas de connexion BD dans ce cas)
 */
if ($test == true) {
    if ($format != 'jquerygantt') {
    echo <<<BLOC_JSON
[{"id_chf":"92","nom_chf":"ANDRE Martin","id_voy":"1158","datdep":"2016-01-27","heudep":"800","datfin":"2016-01-27","heufin":"1800"},
{"id_chf":"4153","nom_chf":"DANIEL André","id_voy":"1170","datdep":"2016-09-08","heudep":"800","datfin":"2016-09-08","heufin":"1800"},
{"id_chf":"313","nom_chf":"MICHAUD Henri","id_voy":"1171","datdep":"2016-12-08","heudep":"800","datfin":"2016-12-09","heufin":"1200"},
{"id_chf":"3524","nom_chf":"RIAD Hassan","id_voy":"1171","datdep":"2016-12-08","heudep":"800","datfin":"2016-12-09","heufin":"1200"},
{"id_chf":"10","nom_chf":"ANDRE Martin","id_voy":"1176","datdep":"2016-01-19","heudep":"1200","datfin":"2016-01-22","heufin":"1730"},
{"id_chf":"1629","nom_chf":"SCHMIDT Heinrik","id_voy":"1177","datdep":"2016-01-20","heudep":"7","datfin":"2016-01-21","heufin":"1800"}]
BLOC_JSON;
    } else {
        echo <<<BLOC_JSON
[{"id":"92","name":"92","desc":"ANDRE Martin","values":[{"id":"1158","datdep":"2016-01-27","heudep":"800","datfin":"2016-01-27","heufin":"1800"}]},
{"id":"4153","name":"4153","desc":"DANIEL André","values":[{"id":"1170","datdep":"2016-09-08","heudep":"800","datfin":"2016-09-08","heufin":"1800"}]},
{"id":"313","name":"313","desc":"MICHAUD Henri","values":[{"id":"1171","datdep":"2016-12-08","heudep":"800","datfin":"2016-12-09","heufin":"1200"}]},
{"id":"3524","name":"3524","desc":"RIAD Hassan","values":[{"id":"1171","datdep":"2016-12-08","heudep":"800","datfin":"2016-12-09","heufin":"1200"}]},
{"id":"10","name":"10","desc":"ANDRE Martin","values":[{"id":"1176","datdep":"2016-01-19","heudep":"1200","datfin":"2016-01-22","heufin":"1730"}]},
{"id":"1629","name":"1629","desc":"SCHMIDT Heinrik","values":[{"id":"1177","datdep":"2016-01-20","heudep":"7","datfin":"2016-01-21","heufin":"1800"}]}]
BLOC_JSON;

    }
    exit();
}

require_once __DIR__ . '/../../config/opendb01.php';

if (isset($_GET['date'])) {
    $date = Logistic\CleanInput::get('date');
} else {
    // date par défaut si non précisé
    $date = Logistic\Misc::dateDef() ;
}

$sql = Logistic\AppDatas::VoyagesByChauffeurByDate();

$result = $db01->selectBlock($sql, array($date)) ;

if (!is_array($result)) {
    $result = array();
}

if ($format != 'jquerygantt') {
    echo json_encode($result) ;

}

if ($format == 'jquerygantt') {
    /*
     * Formatage du JSON selon le format attendu par jQuery Gantt
     */
    $result2 = array();
    $rupture_chauffeur = '' ;
    foreach($result as $reskey => $resvalue) {
        $current_chauff = trim($resvalue['id_chf']).trim($resvalue['nom_chf']) ;
        $detect_rupture = false ;
        $current_id = $resvalue['id_chf'];
        $current_name = $resvalue['id_chf'];
        $current_desc = $resvalue['nom_chf'];
        if ($rupture_chauffeur == '') {
            $liste_voy = array();
            $detect_rupture = true ;
            $rupture_chauffeur = trim($resvalue['id_chf']).trim($resvalue['nom_chf']) ;
        } else {
            if ($rupture_chauffeur != $current_chauff) {
                $rupture_chauffeur = trim($resvalue['id_chf']).trim($resvalue['nom_chf']) ;
                $detect_rupture = true ;
            }
        }
        $tmp_voyage= array();
        $tmp_voyage['id'] = $resvalue['id_voy'];
        $tmp_voyage['datdep'] = $resvalue['datdep'];
        $tmp_voyage['heudep'] = $resvalue['heudep'];
        $tmp_voyage['datfin'] = $resvalue['datfin'];
        $tmp_voyage['heufin'] = $resvalue['heufin'];
        $liste_voy[] = $tmp_voyage;

        if ($detect_rupture) {
            $tmp_result = array();
            $tmp_result['id'] = $current_id ;
            $tmp_result['name'] = $current_name;
            $tmp_result['desc'] = $current_desc;
            $tmp_result['values'] = $liste_voy;
            $result2[] = $tmp_result;
            $liste_voy = array();
        }
    }
    // injection de la dernière occurrence dans le tableau final
 /*   $tmp_result = array();
    $tmp_result['id'] = $current_id ;
    $tmp_result['name'] = $current_name;
    $tmp_result['desc'] = $current_desc;
    $tmp_result['values'] = $liste_voy;
    $result2[] = $tmp_result;
*/
    echo json_encode($result2) ;
}
