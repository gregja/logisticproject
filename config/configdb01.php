<?php
/**
 * Ce projet est conçu pour s'interfacer avec une base de données DB2 pour IBM i
 * Les paramètres définis ci-dessous ne sont pris en compte que dans le cas
 * où la constante MODE_EXE_BD est définie à blanc dans bootinit.php.
 */
if ((php_uname('s') == 'OS400' || PHP_OS == "AIX" || PHP_OS == "OS400")) {
    // cas où PHP se trouve installé sur un IBM i
    $system = '*LOCAL';
    $envir_IBMi = true ;
} else {
    // cas où PHP se trouve sur un serveur Linux ou Windows
    // la connexion se fait dans ce cas via PDO, et il faut alors configurer
    // l'adresse IP du serveur IBM i
    $system = 'xxx.xxx.xxx.xxx';
    $envir_IBMi = false ;
}

// profil de connexion à la base de données DB2
$user = 'XXXXXXXX' ;
$password = 'YYYYYYYY' ;

$options = array() ;
$options['i5_naming'] = true ;

// définir ici les bibliothèques IBM i à utiliser dans le cadre de l'application
$options['i5_libl'] = 'TESTFIC TESTCOM' ;

if ($envir_IBMi) {
    $options['DB2_ATTR_CASE'] = DB2_CASE_LOWER  ;
} else {
    $options['DB2_ATTR_CASE'] = 'LOWER'  ;
}

$persistent = false ;
