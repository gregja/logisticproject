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

/*
 * Si test alors on renvoie un jeu de données "en dur" au format JSON
 * et on s'arrête là
 * (pas de connexion BD dans ce cas)
 */
if ($test == true) {
    require '../tmpdata/tmp_dataset_ot_json_noheader.php' ;
    echo $jsondata ;
    exit();
}

if (isset($_GET['date'])) {
    $date = Logistic\CleanInput::get('date');
} else {
    // date par défaut si non précisé
    $date = Logistic\Misc::dateDef() ;
}

require_once __DIR__ . '/../../config/opendb01.php';

$sql = Logistic\AppDatas::OTByDateQuery();

$result = $db01->selectBlock($sql, array($date)) ;

if (!is_array($result)) {
    $result = array();
}

echo json_encode($result) ;
