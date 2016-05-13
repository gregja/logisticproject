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
[{"noch":"4153","morh":"MD      ","rsti":"ANDRE Martin","coempl":" ","id":"4153"},{"noch":"3524","morh":"AJ      ","rsti":"DANIEL André ","coempl":" ","id":"3524"},{"noch":"313","morh":"LM      ","rsti":"MICHAUD Henri","coempl":" ","id":"313"},{"noch":"10","morh":"BS ","rsti":"RIAD Hassan","coempl":"  ","id":"10"},{"noch":"1629","morh":"TM      ","rsti":"ANDRE Martin","coempl":" ","id":"1629"}]
BLOC_JSON;
    exit();
}

require_once __DIR__ . '/../../config/opendb01.php';

if (isset($_GET['date'])) {
    $date = Logistic\CleanInput::get('date');
} else {
    // date par défaut si non précisé
    $date = Logistic\Misc::dateDef() ;
}

$sql = Logistic\AppDatas::ChauffeursByDateQuery();

$result = $db01->selectBlock($sql, array($date)) ;

if (!is_array($result)) {
    $result = array();
}

echo json_encode($result) ;
