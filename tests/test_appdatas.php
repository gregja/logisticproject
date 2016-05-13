<?php
require __DIR__ . '/../vendor/autoload.php';

//use Logistic ;

echo '<pre>';

$origine = array('{SEPARATOR}', '?');
$destina = array('/', "'2015-01-25'") ;


$sql = Logistic\AppDatas::ChauffeursByDateQuery();
echo str_replace($origine, $destina, $sql) ;
echo ';<br>'.PHP_EOL ;

$sql =  Logistic\AppDatas::OTByDateQuery();
echo str_replace($origine, $destina, $sql) ;
echo ';<br>'.PHP_EOL ;

$sql =  Logistic\AppDatas::VoyagesByDateQuery();
echo str_replace($origine, $destina, $sql) ;
echo ';<br>'.PHP_EOL ;
