<?php
header('Content-Type: application/json');
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

require 'tmp_dataset_ot_json_noheader.php' ;

echo trim($jsondata) ;
