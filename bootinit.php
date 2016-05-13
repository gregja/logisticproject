<?php
require __DIR__ . '/vendor/autoload.php';

define('CONFIG_IP', '127.0.0.1') ;
define('PATH_CLIENT', 'localhost');

/*
  La couche d'accès à la base de données DB2 est court-circuitée au profit
  de données stockées sur le serveur au format JSON.
  Pour réactiver la base de données, il faut mettre la constante ci-dessous
  à blanc, et il faut aussi configurer le profil de connexion à la base de
  données DB2 dans le fichier config/configdb01.php.
*/
define('MODE_EXE_BD', 'test');   // valeurs possibles : "test" ou ""
