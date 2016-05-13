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
 * (pas de connexion BD dans ce cas)
 */
if ($test == true) {
    echo <<<BLOC_JSON
[{"nochpl":"4153","novy":"1170","dtdpvy":"2016-09-08","hhdpvy":"800","dtarvy":"0001-01-01","hharvy":"0","id":"T1-0001170-04153"},{"nochpl":"3524","novy":"1171","dtdpvy":"2016-12-08","hhdpvy":"800","dtarvy":"2016-12-09","hharvy":"1200","id":"T1-0001171-03524"},{"nochpl":"313","novy":"1171","dtdpvy":"2016-12-08","hhdpvy":"800","dtarvy":"2016-12-09","hharvy":"1200","id":"T1-0001171-00313"},{"nochpl":"10","novy":"1176","dtdpvy":"2016-01-19","hhdpvy":"1200","dtarvy":"2016-01-22","hharvy":"1730","id":"T1-0001176-00010"},{"nochpl":"1629","novy":"1177","dtdpvy":"2016-01-20","hhdpvy":"7","dtarvy":"2016-01-21","hharvy":"0","id":"T1-0001177-01629"}]
BLOC_JSON;
    exit();
}

if (isset($_GET['date'])) {
    $date = Logistic\CleanInput::get('date');
} else {
    // date par défaut si non précisé
    $date = Logistic\Misc::dateDef() ;
}

require_once __DIR__ . '/../../config/opendb01.php';

$sql = Logistic\AppDatas::VoyagesByDateQuery();

$result = $db01->selectBlock($sql, array($date)) ;

if (!is_array($result)) {
    $result = array();
}

echo json_encode($result) ;
