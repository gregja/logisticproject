<?php

namespace Logistic;

use Logistic;

if ((php_uname('s') == 'OS400' || PHP_OS == "AIX" || PHP_OS == "OS400")) {

    class DB2Layer extends Logistic\DB2_IBMi_Abstract implements Logistic\DB2InstanceInterface {

    }

} else {

    class DB2Layer extends Logistic\PDO_IBMi_DB2_Abstract implements Logistic\DB2InstanceInterface {

    }

}
