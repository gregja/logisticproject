<?php
require __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../config/opendb01.php';


$persistent = false ;
$db01 = new Logistic\DB2Layer($system, $user, $password, $options, $persistent);

echo '<pre>'.PHP_EOL;

$sql = 'select * from sysibm/sysdummy1' ;
$result = $db01->selectOne($sql) ;

if (is_array($result) && isset($result['ibmreqd']) && $result['ibmreqd']=='Y') {
    echo 'test 1 OK'.PHP_EOL ;
} else {
    echo 'test 1 KO'.PHP_EOL ;
}

$result = $db01->selectBlock($sql) ;
if (is_array($result) && isset($result[0]) && isset($result[0]['ibmreqd']) && $result[0]['ibmreqd']=='Y') {
    echo 'test 2 OK'.PHP_EOL ;
} else {
    echo 'test 2 KO'.PHP_EOL ;
}

$result = $db01->export2CSV($sql) ;
$result2 = var_export($result, true) ;
$assert = <<<BLOC
'ibmreqd
"Y"
'
BLOC;
if (trim($result2) != trim($assert)) {
    echo 'test 3 KO'.PHP_EOL ;
} else {
    echo 'test 3 OK'.PHP_EOL ;
}

$result = $db01->export2XML($sql) ;
$result2 = var_export($result, true) ;
$assert = <<<BLOC
'<?xml version="1.0" encoding="UTF-8"?><row><ibmreqd>Y</ibmreqd></row>'
BLOC;
if (trim($result2) != trim($assert)) {
    echo 'test 4 KO'.PHP_EOL ;
} else {
    echo 'test 4 OK'.PHP_EOL ;
}


$result = $db01->export2insertSQL($sql) ;
$result2 = var_export($result, true) ;
$assert = <<<BLOC
'INSERT INTO tableDestinataire 
( ibmreqd ) 
VALUES 
( \'Y\' )
;
'
BLOC;
if (trim($result2) != trim($assert)) {
    echo 'test 5 KO'.PHP_EOL ;
} else {
    echo 'test 5 OK'.PHP_EOL ;
}



