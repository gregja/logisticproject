<?php

namespace Logistic ;

abstract class Misc {

	/**
	 * Renvoie "true" si le serveur est un IBM i, "false" dans le cas contraire
	 */
	public static function isIBMiPlatform () {

		return (php_uname('s') == 'OS400' || PHP_OS == "AIX" || PHP_OS == "OS400") ? true : false;

	}

	public static function dateDef () {
	    return '2016-01-25' ;
	}

}
