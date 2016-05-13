<?php
require __DIR__ . '/configdb01.php';

$db01 = new Logistic\DB2Layer($system, $user, $password, $options, $persistent);
