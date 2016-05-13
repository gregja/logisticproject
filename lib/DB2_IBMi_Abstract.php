<?php

namespace Logistic;

abstract class DB2_IBMi_Abstract
{
    protected $_dbinstance = null;
    protected $_options = null;
    protected $_sql_separator = '.';
    protected $_system = null;
    protected $_user = null;
    protected $_profiler = false;
    protected $_autocommit = true;
    protected $_persistent = false;

    public function __construct($system, $user, $password, $options = array(), $persistent = false) {

        $this->_system = $system;
        $this->_user = $user;
        $this->_options = $options;
        if ($persistent === true) {
            $this->_persistent = true;
        } else {
            $this->_persistent = false;
        }
        if ($this->_persistent === true) {
            $this->_dbinstance = db2_pconnect($system, $user, $password, $options);
        } else {
            $this->_dbinstance = db2_connect($system, $user, $password, $options);
        }
        if (!is_resource($this->_dbinstance)) {
            throw new \Exception('Anomalie sur connexion DB2');
        }
        if (isset($options ['i5_naming']) && $options ['i5_naming'] == true) {
            $this->_sql_separator = '/';
        }
    }

    public function getResource() {
        return $this->_dbinstance;
    }

    public function getSqlSeparator() {
        return $this->_sql_separator ;
    }

    /*
     * La fonction db2_connect permet l'utilisation de la syntaxe IBM avec
     * le "/" au lieu du "."
     * comme séparateur entre nom de bibliothèque et nom de table. Donc pour
     * savoir si le "/" ou le "."
     * doit être utilisé, on se base sur le contenu du paramètre "i5_naming"
     * défini dans les options de db2_connect.
     */
    public function changeSeparator($sql) {
        $pos = strpos($sql, '{SEPARATOR}');
        if ($pos !== false) {
            $sql = str_replace('{SEPARATOR}', $this->getSqlSeparator(), $sql);
        }
        return $sql;
    }

    public function selectOne($sql, $args = array(), $fetch_mode_num = false) {
        $sql = $this->changeSeparator($sql);
        $result = array();
        if (!is_array($args)) {
            if (trim($args) != '') {
                $args = array($args);
            } else {
                $args = array();
            }
        }
        try {
            $st = db2_prepare($this->_dbinstance, $sql);
            if (!$st) {
                $this->MyDBError('selectOne/db2_prepare', $sql, $args);
                $result = null;
            } else {
                if (!db2_execute($st, $args)) {
                    $this->MyDBError('selectOne/db2_execute', $sql, $args);
                    $result = null;
                } else {
                    /*
                     * par défaut c'est le mode "fetch array" qui est utilisé,
                     * mais dans certains cas le mode "fetch num" peut être utile
                     */
                    if ($fetch_mode_num === true) {
                        $result = db2_fetch_array($st);
                    } else {
                        $result = db2_fetch_assoc($st);
                    }
                }
                db2_free_stmt($st);
            }
            unset($st);
            return $result;
        } catch (\Exception $e) {
            $this->MyDBException($e, $sql, $args);
        }
    }

    public function selectBlock($sql, $args = array()) {
        $sql = $this->changeSeparator($sql);
        $rows = array();
        if (!is_array($args)) {
            if (trim($args) != '') {
                $args = array($args);
            } else {
                $args = array();
            }
        }
        try {
            $st = db2_prepare($this->_dbinstance, $sql);
            if (!$st) {
                $this->MyDBError('selectBlock/db2_prepare', $sql, $args);
                $rows = null;
            } else {
                if (!db2_execute($st, $args)) {
                    $this->MyDBError('selectBlock/db2_execute', $sql, $args);
                    $rows = null;
                } else {
                    $row = db2_fetch_assoc($st);
                    while ($row != false) {
                        $rows [] = $row;
                        $row = db2_fetch_assoc($st);
                    }
                }
                db2_free_stmt($st);
            }
            unset($st);
            return $rows;
        } catch (\Exception $e) {
            $this->MyDBException($e, $sql, $args);
        }
    }

    public function executeCommand($sql, $args = array(), $count_nb_rows = true) {
        $sql = $this->changeSeparator($sql);
        if (!is_array($args)) {
            if (trim($args) != '') {
                $args = array($args);
            } else {
                $args = array();
            }
        }
        $nbrows = 0;
        try {
            $st = db2_prepare($this->_dbinstance, $sql);
            if (!$st) {
                $this->MyDBError('executeCommand/db2_prepare', $sql, $args);
                $nbrows = null;
            } else {
                if (!db2_execute($st, $args)) {
                    $this->MyDBError('executeCommand/db2_execute', $sql, $args);
                    $nbrows = null;
                } else {
                    if ($count_nb_rows === true) {
                        $nbrows = db2_num_rows($st);
                    }
                }
                db2_free_stmt($st);
            }
            unset($st);
            return $nbrows;
        } catch (\Exception $e) {
            $this->MyDBException($e, $sql, $args);
        }
    }

    public function executeSysCommand($cmd) {
        $cmd = trim($cmd);
        $cmd_length = strlen($cmd);
        $cmd2 = "CALL QCMDEXC ('{$cmd}', {$cmd_length})";
        return $this->executeCommand($cmd2);
    }

    public function callProcedure($proc_name, $proc_schema,
            &$args = array(), $return_resultset = false) {
        $proc_name = trim($proc_name);
        $proc_schema = trim($proc_schema);
        if ($proc_schema == '') {
            $proc_sql = $proc_name;
        } else {
            $proc_sql = $proc_schema . '{SEPARATOR}' . $proc_name;
        }
        $sql = 'CALL ' . $proc_sql;
        $sql = $this->changeSeparator($sql);
        if (is_array($args) && count($args) > 0) {
            $jokers = array();
            $args_inc = 0;
            foreach ($args as $key => $arg) {
                $jokers [] = '?';
                $args_inc++;
            }
            $sql .= ' ( ' . implode(', ', $jokers) . ' ) ';
        }
        try {
            $resultset = array();

            $st = db2_prepare($this->_dbinstance, $sql);
            if (!$st) {
                $this->MyDBError('executeCommand/db2_prepare', $sql, $args);
            } else {
                $args_inc = 0;
                foreach ($args as $key => $arg) {
                    /*
                     * crée artificiellement une variable ayant pour nom
                     * le contenu de la variable $key
                     */
                    $$key = $args[$key]['value'];
                    $args_inc++;
                    $arg['type'] = isset($arg['type'])?strtolower($arg['type']):'in';

                    switch ($arg['type']) {
                        case 'out': {
                                db2_bind_param($st, $args_inc, $key, DB2_PARAM_OUT);
                                break;
                            }
                        case 'inout': {
                                db2_bind_param($st, $args_inc, $key, DB2_PARAM_INOUT);
                                break;
                            }
                        default: {
                                db2_bind_param($st, $args_inc, $key, DB2_PARAM_IN);
                                break;
                            }
                    }
                }
                if (!db2_execute($st)) {
                    $this->MyDBError('executeCommand/db2_execute', $sql, $args);
                } else {
                    foreach ($args as $key => $arg) {
                        $arg['type'] = strtolower($arg['type']);
                        if ($arg['type'] == 'out' || $arg['type'] == 'inout') {
                            $args[$key]['value'] = $$key;
                        }
                    }
                    if ($return_resultset === true) {
                        $row = db2_fetch_assoc($st);
                        while ($row != false) {
                            $resultset [] = $row;
                            $row = db2_fetch_assoc($st);
                        }
                    }
                }
                db2_free_stmt($st);
            }
            unset($st);
            return $resultset;
        } catch (\Exception $e) {
            $this->MyDBException($e, $sql, $args);
        }
    }

    public function getStatement($sql, $args = array()) {
        $sql = $this->changeSeparator($sql);
        if (!is_array($args)) {
            if (trim($args) != '') {
                $args = array($args);
            } else {
                $args = array();
            }
        }
        try {
            $st = db2_prepare($this->_dbinstance, $sql);
            if (!$st) {
                $this->MyDBError('getStatement/db2_prepare', $sql, $args);
            } else {
                if (!db2_execute($st, $args)) {
                    $this->MyDBError('getStatement/db2_execute', $sql, $args);
                    $st = false;
                }
            }
            return $st;
        } catch (\Exception $e) {
            $this->MyDBException($e, $sql, $args);
        }
    }

    public function getFetchAssoc($st) {
        if (!$st) {
            return false;
        } else {
            return db2_fetch_assoc($st);
        }
    }

    public function getPagination($sql, $args, $offset,
            $nbl_by_page, $order_by = '') {
        if (!is_array($args)) {
            $args = array();
        }
        $offset = intval($offset);
        if ($offset <= 0) {
            $offset = 1;
        }
        $nbl_by_page = intval($nbl_by_page);
        if ($nbl_by_page <= 0) {
            $nbl_by_page = 10;
        }
        $limit_max = $offset + $nbl_by_page - 1;
        $sql = trim($sql);
        $order_by = trim($order_by);
        if ($order_by != '') {
            $order_by = 'ORDER BY ' . $order_by;
        }
        // on recherche la position du 1er SELECT pour le compléter
        $pos = stripos($sql, 'select');
        if ($pos !== false) {
            $temp = 'select row_number() over (' . $order_by . ') as rn, ';
            $sql = substr_replace($sql, $temp, $pos, 6);
        } else {
            // pagination impossible si requête ne contient pas un SELECT
            return false;
        }
        $sql = <<<BLOC_SQL
select foo.* from (
{$sql}
) as foo
where foo.rn between ? and ?
BLOC_SQL;
        /*
         * Ajout des paramètres du "between" dans le tableau des arguments
         * transmis à la requête
         */
        $args [] = $offset;
        $args [] = $limit_max;

        return $this->selectBlock($sql, $args);
    }

    /*
     * Pagination via la technique du Scroll Cursor telle qu'elle est
     * implémentée dans db2_connect
     */
    public function getScrollCursor($sql, $args, $offset,
            $nbl_by_page, $order_by = '') {
        if (!is_array($args)) {
            $args = array();
        }
        $offset = intval($offset);
        if ($offset <= 0) {
            $offset = 1;
        }
        $nbl_by_page = intval($nbl_by_page);
        if ($nbl_by_page <= 0) {
            $nbl_by_page = 10;
        }
        $sql = $this->changeSeparator($sql);
        $order_by = trim($order_by);
        if ($order_by != '') {
            $sql .= ' ORDER BY ' . $order_by;
        }
        $rows = array();
        try {
            $st = db2_prepare($this->_dbinstance, $sql,
                    array('cursor' => DB2_SCROLLABLE));
            if (!$st) {
                $this->MyDBError('selectBlock/db2_prepare', $sql, $args);
                $rows = null;
            } else {
                if (!db2_execute($st, $args)) {
                    $this->MyDBError('selectBlock/db2_execute', $sql, $args);
                    $rows = null;
                } else {
                    for (
                    $tofetch = $nbl_by_page,
                    $row = db2_fetch_assoc($st, $offset);
                    $row !== false && $tofetch-- > 0;
                    $row = db2_fetch_assoc($st)
                    ) {
                        $rows [] = $row;
                    }
                }
                db2_free_stmt($st);
            }
            unset($st);
            return $rows;
        } catch (\Exception $e) {
            $this->MyDBException($e, $sql, $args);
        }
    }

    public function export2CSV($sql, $args = array()) {
        $st = $this->getStatement($sql, $args);
        $top_header_file = true;
        $csv = '';
        $row = $this->getFetchAssoc($st);
        while ($row != false) {
            if ($top_header_file) {
                $csv .= join(';', array_keys($row)) . PHP_EOL;
                $top_header_file = false;
            }
            $row2 = array();
            foreach ($row as $key => $col) {
                if (is_int($col) || is_float($col)) {
                    $row2 [] = $col;
                } else {
                    $col = str_replace(array("\n", "\r\n"), '', $col);
                    $col = str_replace(array('"', "''"), '', $col);
                    $row2 [] = '"' . trim($col) . '"';
                }
            }

            $csv .= join(';', $row2) . PHP_EOL;
            $row = $this->getFetchAssoc($st);
        }
        return $csv;
    }

    public function export2XML($sql, $args = array(),
            $tag_line = '', $gen_header = true) {
        $st = $this->getStatement($sql, $args);
        if ($tag_line == '') {
            $tag_line = 'row';
        }
        $tag_open = "<{$tag_line}>";
        $tag_close = "</{$tag_line}>";

        $xml = '';
        if ($gen_header === true) {
            $xml .= '<?xml version="1.0" encoding="UTF-8"?>';
        }
        $row = $this->getFetchAssoc($st);
        while ($row != false) {
            $xml .= $tag_open;
            foreach ($row as $key => $col) {
                $key = trim(strtolower($key));
                if (is_int($col) || is_float($col)) {
                    $xml .= '<' . $key . '>' .
                            htmlspecialchars($col, ENT_QUOTES, "UTF-8") .
                            '</' . $key . '>';
                } else {
                    $col = str_replace(array("\n", "\r\n"), '', $col);
                    $col = str_replace(array('"', "''"), '', $col);
                    $col = trim($col);
                    if (strlen($col) > 0) {
                        $xml .= '<' . $key . '>' .
                                htmlspecialchars($col, ENT_QUOTES, "UTF-8") .
                                '</' . $key . '>';
                    } else {
                        $xml .= '<' . $key . ' />';
                    }
                }
            }
            $xml .= $tag_close;
            $row = $this->getFetchAssoc($st);
        }
        return $xml;
    }

    public function export2insertSQL($sql, $args = array()) {
        $st = $this->getStatement($sql, $args);
        $top_header_file = true;
        $sql_insert = '';
        $row = $this->getFetchAssoc($st);
        $nb_lig = 0 ;
        while ($row != false) {
            if ($nb_lig > 1000) {
                $nb_lig = 0 ;
                $top_header_file = true ;
                $sql_insert .= ';' . PHP_EOL;
            }
            if ($top_header_file) {
                $sql_insert .= 'INSERT INTO tableDestinataire ' . PHP_EOL;
                $sql_insert .= '( ' . join(', ', array_keys($row)) . ' ) ' . PHP_EOL;
                $sql_insert .= 'VALUES ' . PHP_EOL;
                $top_header_file = false;
            }
            $row2 = array();
            $sql_insert .= '( ';
            foreach ($row as $key => $col) {
                if (is_int($col) || is_float($col)) {
                    $row2 [] = $col;
                } else {
                    /*
                     * le test ci-dessous ne sera pas pris en compte avec PDO
                     * qui renvoie des données de type string
                     *  par contre il fonctionnera avec DB2_Connect qui renvoie
                     * des données correctement typées
                     */
                    if (is_int($col) || is_float($col)) {
                        $row2 [] = trim($col);
                    } else {
                        $col = str_replace(array("\n", "\r\n"), '', $col);
                        $col = str_replace(array("'", "''"), '', $col);
                        $col = str_replace(array('"', "''"), '', $col);
                        $row2 [] = "'" . trim($col) . "'";
                    }
                }
            }
            $sql_insert .= join(', ', $row2) . ' )';
            $row = $this->getFetchAssoc($st);
            if ($row != false) {
                $sql_insert .= ',' . PHP_EOL;
            } else {
                $sql_insert .= PHP_EOL;
            }
            $nb_lig++ ;
        }
        if ($sql_insert != '') {
            $sql_insert .= ';' . PHP_EOL;
        }
        return $sql_insert;
    }

    public function countNbRowsFromTable($table, $schema = '') {
        if (trim($schema) == '') {
            $sql = 'SELECT COUNT(*) AS NB_ROWS FROM ' . trim($table);
        } else {
            $sql = 'SELECT COUNT(*) AS NB_ROWS FROM ' . trim($schema) .
                    '{SEPARATOR}' . trim($table);
        }
        $data = $this->selectOne($sql, array(), true);
        if (is_array($data) && isset($data[0])) {
            return $data[0];
        } else {
            return 0;
        }
    }

    public function countNbRowsFromSQL($sql, $args = array()) {
        $sql = 'SELECT COUNT(*) AS NB_ROWS FROM (' . trim($sql) . ') AS FOO';
        $data = $this->selectOne($sql, $args, true);
        if (is_array($data) && isset($data[0])) {
            return $data[0];
        } else {
            return 0;
        }
    }

    /*
     * Méthode permettant de récupérer le dernier ID créé dans la BD
     * Le dernier ID est soit l'ID interne DB2, soit une séquence DB2 dont
     * le code est transmis en paramètre
     */
    public function getLastInsertId($sequence = '') {
        $sequence = trim($sequence);
        if ($sequence == '') {
            $sql = "SELECT IDENTITY_VAL_LOCAL() FROM SYSIBM{SEPARATOR}SYSDUMMY1";
        } else {
            $sql = "SELECT NEXT VALUE FOR {$sequence} FROM SYSIBM{SEPARATOR}SYSDUMMY1";
        }
        $data = $this->selectOne($sql, array(), true);
        if (is_array($data) && isset($data[0])) {
            return $data[0];
        } else {
            return false;
        }
    }

    /*
     * Méthode permettant de vérifier si une valeur existe bien dans une colonne
     * peut également être utilisé pour vérifier la non existence d'une valeur
     * avant son insertion dans une table (cas des colonnes en "clé unique"
     * par exemple
     */
    public function valueIsExisting($table, $nomcol, $valcol,
            $where_optionnel = '') {
        $where_sql = " WHERE {$nomcol} = ? ";
        if ($where_optionnel != '') {
            $where_sql .= ' and ' . $where_optionnel;
        }
        $query = "SELECT count(*) FROM {$table} {$where_sql} fetch first 1 row only";
        $data = $this->selectOne($query, array($valcol), true);
        if (is_array($data) && isset($data[0]) && $data[0] == 1) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * Méthode permettant de vérifier si une valeur existe bien dans une colonne
     * mais sur une autre ligne que la ligne en cours de traitement
     * on peut l'utiliser par exemple en modification d'enregistrement, pour
     * empêcher qu'un code existant sur une autre ligne ne puisse être utilisé
     * sur la ligne en cours de modification.
     */
    public function valueIsExistingOnOtherRecord($table, $nomcol,
            $valcol, $idencours, $where_optionnel = '') {
        $where_sql = " WHERE {$nomcol} = ? and id <> ? ";
        if ($where_optionnel != '') {
            $where_sql .= ' and ' . $where_optionnel;
        }
        $query = "SELECT count(*) FROM {$table} {$where_sql} fetch first 1 row only ";
        $data = $this->selectOne($query, array($valcol, $idencours), true);
        if (is_array($data) && isset($data[0]) && $data[0] == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function getInfoDatabase() {
        $client = db2_client_info($this->_dbinstance);
        $infos = array();
        if ($client) {
            $key = 'DB2_CLIENT_INFO';
            $infos[$key]['DRIVER_NAME'] = $client->DRIVER_NAME;
            $infos[$key]['DRIVER_VER'] = $client->DRIVER_VER;
            $infos[$key]['DATA_SOURCE_NAME'] = $client->DATA_SOURCE_NAME;
            $infos[$key]['DRIVER_ODBC_VER'] = $client->DRIVER_ODBC_VER;
            $infos[$key]['ODBC_VER'] = $client->ODBC_VER;
            $infos[$key]['ODBC_SQL_CONFORMANCE'] = $client->ODBC_SQL_CONFORMANCE;
            $infos[$key]['APPL_CODEPAGE'] = $client->APPL_CODEPAGE;
            $infos[$key]['CONN_CODEPAGE'] = $client->CONN_CODEPAGE;
        }
        $server = db2_server_info($this->_dbinstance);
        if ($server) {
            $key = 'DB2_SERVER_INFO';
            $infos[$key]['DBMS_NAME'] = $server->DBMS_NAME;
            $infos[$key]['DBMS_VER'] = $server->DBMS_VER;
            $infos[$key]['DB_CODEPAGE'] = $server->DB_CODEPAGE;
            $infos[$key]['DB_NAME'] = $server->DB_NAME;
            $infos[$key]['INST_NAME'] = $server->INST_NAME;
            $infos[$key]['SPECIAL_CHARS'] = $server->SPECIAL_CHARS;
            $infos[$key]['KEYWORDS'] = $server->KEYWORDS;
            $infos[$key]['DFT_ISOLATION'] = $server->DFT_ISOLATION;
            $il = array();
            foreach ($server->ISOLATION_OPTION as $opt) {
                $il [] = $opt;
            }
            $infos[$key]['ISOLATION_OPTION'] = implode(' ', $il);
            $infos[$key]['SQL_CONFORMANCE'] = $server->SQL_CONFORMANCE;
            $infos[$key]['PROCEDURES'] = $server->PROCEDURES;
            $infos[$key]['IDENTIFIER_QUOTE_CHAR'] = $server->IDENTIFIER_QUOTE_CHAR;
            $infos[$key]['LIKE_ESCAPE_CLAUSE'] = $server->LIKE_ESCAPE_CLAUSE;
            $infos[$key]['MAX_COL_NAME_LEN'] = $server->MAX_COL_NAME_LEN;
            $infos[$key]['MAX_ROW_SIZE'] = $server->MAX_ROW_SIZE;
            $infos[$key]['MAX_IDENTIFIER_LEN'] = $server->MAX_IDENTIFIER_LEN;
            $infos[$key]['MAX_INDEX_SIZE'] = $server->MAX_INDEX_SIZE;
            $infos[$key]['MAX_PROC_NAME_LEN'] = $server->MAX_PROC_NAME_LEN;
            $infos[$key]['MAX_SCHEMA_NAME_LEN'] = $server->MAX_SCHEMA_NAME_LEN;
            $infos[$key]['MAX_STATEMENT_LEN'] = $server->MAX_STATEMENT_LEN;
            $infos[$key]['MAX_TABLE_NAME_LEN'] = $server->MAX_TABLE_NAME_LEN;
            $infos[$key]['NON_NULLABLE_COLUMNS'] = $server->NON_NULLABLE_COLUMNS;
        }
        return $infos;
    }

    /**
     * fonction utilisée avec la classe DBException permettant un meilleur
     * contrôle sur la présentation des erreurs renvoyées par cette classe
     * @param type $db
     * @param type $fonction_db2
     * @param type $reqsql
     * @param type $args
     */
    private function MyDBError($fonction_db2, $reqsql, $args = array()) {
        $getmessage = 'Erreur sur ' . $fonction_db2 . ' : ' . PHP_EOL;
        $getmessage .= 'DB2 Error Code : ' . db2_stmt_error() . ' // ' . PHP_EOL;
        $getmessage .= 'DB2 Error Msg : ' . db2_stmt_errormsg() . ' // ' . PHP_EOL;
        ob_start();
        var_dump($args);
        $dump_args = ob_get_clean();
        if (isset($GLOBALS['sixaxe_sql_debug']) && $GLOBALS['sixaxe_sql_debug'] === true) {
            echo "<table border=\"1\">
       <tr>
        <td> Code SQL </td>
        <td> {$reqsql} </td>
       </tr>
       <tr>
        <td> Msg  </td>
        <td> {$getmessage} </td>
       </tr>
       <tr>
        <td> Arguments  </td>
        <td> {$dump_args} </td>
       </tr>

       </table><br />";
        }
        error_log("code SQL   -> " . $reqsql);
        error_log("getMessage -> " . $getmessage);
        error_log("arguments -> " . $dump_args);
    }

    /**
     * Fonction utilisée avec la classe DBException permettant un meilleur
     * contrôle sur la présentation des erreurs renvoyées par cette classe
     * @param type $db
     * @param type $DBexc
     * @param type $reqsql
     * @param type $args
     */
    public function MyDBException($DBexc, $reqsql = '', $args = array()) {
        $tab_log = array();
        $tab_log ['SQL_code'] = $reqsql;
        $tab_log ['Message'] = $DBexc->getMessage();
        $tab_log ['Trace'] = $DBexc->getTraceAsString();
        $tab_log ['Code'] = $DBexc->getCode();
        $tab_log ['File'] = $DBexc->getFile();
        $tab_log ['Line'] = $DBexc->getLine();
        if (is_array($args) && count($args) > 0) {
            $tab_log ['Arguments'] = var_export($args, true);
        } else {
            $tab_log ['Arguments'] = '';
        }
        $dump = var_export($tab_log);
        error_log($dump);
    }

}
