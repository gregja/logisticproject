<?php
namespace Logistic;

abstract class PDO_IBMi_DB2_Abstract
{

    protected $_dbinstance = null;

    protected $_options = null;

    protected $_sql_separator = '.';

    protected $_system = null;

    protected $_user = null;

    protected $_profiler = false;

    protected $_autocommit = true;

    protected $_persistent = false;

    public function __construct($system, $user, $password, $options = array(), $persistent = false)
    {
        $this->_system = $system;
        $this->_user = $user;
        if ($persistent === true) {
            $this->_persistent = true;
        } else {
            $this->_persistent = false;
        }

        $dsn = 'odbc:DRIVER={iSeries Access ODBC Driver};SYSTEM=' . $system;

        // Attention à ne pas ajouter de ";" inutile à la fin d'un DSN, car PDO
        // n'apprécie pas du tout
        $dsn_temp = $this->generate_dsn($options);
        if ($dsn_temp != '') {
            $dsn .= ';' . $dsn_temp;
        }

        /*
         * Permet d'activer le mode Prepare/Execute qui par défaut est émulé
         * par PDO (si le SGBD ne renvoie pas à PDO l'information comme quoi il
         *  gère lui même la préparation des requêtes)
         * Ne sachant pas si le driver "iSeries Access ODBC Driver" renvoie
         * cette information à PDO, la désactivation
         * effectuée ici est une mesure préventive.
         */
        $options_cnx = array(
            //PDO::ATTR_EMULATE_PREPARES => FALSE,
        );
        if ($persistent === true) {
            $options_cnx[] = PDO::ATTR_PERSISTENT;
        }
        try {
            $this->_dbinstance = new \PDO($dsn, $user, $password, $options_cnx);
            if (strtoupper($options ['DB2_ATTR_CASE']) == 'LOWER') {
                $this->_dbinstance->setAttribute ( \PDO::ATTR_CASE, \PDO::CASE_LOWER );
            } else {
                $this->_dbinstance->setAttribute ( \PDO::ATTR_CASE, \PDO::CASE_UPPER );
            }

        } catch (\PDOException $e) {
            error_log('FATAL ERROR : PDOException sur connexion DB dans la méthode ' . __METHOD__ . ' de la classe ' . __CLASS__);
            error_log('FATAL ERROR : DSN= ' . $dsn);
            error_log('FATAL ERROR : ' . $e->getMessage());
            return false;
        } catch (\Exception $e) {
            error_log('FATAL ERROR : Exception sur connexion DB dans la méthode ' . __METHOD__ . ' de la classe ' . __CLASS__);
            error_log('FATAL ERROR : DSN= ' . $dsn);
            error_log('FATAL ERROR : ' . $e->getMessage());
            return false;
        }

        if (isset($this->_options['i5_naming']) && $this->_options['i5_naming'] === true) {
            $this->_sql_separator = '/';
        }
    }

    public function changeSeparator($sql)
    {
        $pos = strpos($sql, '{SEPARATOR}');
        if ($pos !== false) {
            $sql = str_replace('{SEPARATOR}', $this->getSqlSeparator(), $sql);
        }
        return $sql;
    }

    public function getSqlSeparator()
    {
        return $this->_sql_separator;
    }

    public function getResource()
    {
        return $this->_dbinstance;
    }

    public function selectOne($sql, $args = array(), $fetch_mode_num = false)
    {
        $sql = $this->changeSeparator($sql);

        $result = array();
        if (! is_array($args)) {
            if (trim($args) > 0) {
                /*
                 * si $args n'est pas de type "array" alors on le transforme
                 * en type "array" car il s'agit probablement d'un oubli du
                 * développeur
                 */
                $args = array(
                    $args
                );
            } else {
                $args = array();
            }
        }

        try {
            $res = $this->getResource();
            $st = $res->prepare($sql);
            $ok = $st->execute($args);
            if ($ok) {
                /*
                 * par défaut c'est le mode "fetch array" qui est utilisé,
                 * mais dans certains cas le mode "fetch column" peut être utile
                 * notamment quand on ne souhaite récupérer qu'une seule colonne
                 */
                if ($fetch_mode_num === true) {
                    $result = $st->fetch(\PDO::FETCH_NUM);
                } else {
                    $result = $st->fetch(\PDO::FETCH_ASSOC);
                }
            } else {
                $result = null;
            }
            unset($st);

            return $result;
        } catch (\Exception $e) {
            $this->MyDBException($e, $sql, $args);
        }
    }

    public function selectBlock($sql, $args = array())
    {
        $sql = $this->changeSeparator($sql);

        $rows = array();
        if (! is_array($args)) {
            if (trim($args) > 0) {
                /*
                 * si $args n'est pas de type "array" alors on le transforme
                 * en type "array" car il s'agit probablement d'un oubli du
                 * développeur
                 */
                $args = array(
                    $args
                );
            } else {
                $args = array();
            }
        }

        try {
            $res = $this->getResource();
            $st = $res->prepare($sql);
            $ok = $st->execute($args);
            if ($ok) {
                $row = $st->fetch(\PDO::FETCH_ASSOC);
                while ($row != false) {
                    $rows[] = $row;
                    $row = $st->fetch(\PDO::FETCH_ASSOC);
                }
            } else {
                $rows = null;
            }
            unset($st);
            return $rows;
        } catch (\Exception $e) {
            $this->MyDBException($e, $sql, $args);
        }
    }

    public function executeCommand($sql, $args = array(), $count_nb_rows = true)
    {
        $sql = $this->changeSeparator($sql);

        if (! is_array($args)) {
            if (trim($args) > 0) {
                /*
                 * si $args n'est pas de type "array" alors on le transforme
                 * en type "array" car il s'agit probablement d'un oubli du
                 * dÃ©veloppeur
                 */
                $args = array(
                    $args
                );
            } else {
                $args = array();
            }
        }
        $nbrows = 0;

        try {
            $res = $this->getResource();
            $st = $res->prepare($sql);
            $ok = $st->execute($args);
            if ($ok && $count_nb_rows === true) {
                $nbrows = $st->rowcount();
            } else {
                $nbrows = 0;
            }
            unset($st);
            return $nbrows;
        } catch (\Exception $e) {
            $this->MyDBException($e, $sql, $args);
        }
    }

    public function executeSysCommand($cmd)
    {
        $cmd = trim($cmd);
        $cmd_length = strlen($cmd);
        $cmd2 = "CALL QCMDEXC ('{$cmd}', {$cmd_length})";
        return $this->executeCommand($cmd2, array(), false);
    }

    function callProcedure($proc_name, $proc_schema, &$args = array(), $return_resultset = false)
    {
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
            foreach ($args as $arg) {
                $jokers[] = '?';
            }
            $sql .= ' ( ' . implode(', ', $jokers) . ' ) ';
        }

        try {
            $resultset = array();

            $res = $this->getResource();
            $st = $res->prepare($sql);
            $args_inc = 0;
            $args_val = array();
            foreach ($args as $key => $arg) {
                $args_val[] = $arg['value'];
                $args_inc ++;
                $arg['type'] = isset($arg['type']) ? strtolower($arg['type']) : 'in';
                $tmp_key = $key;
                switch ($arg['type']) {
                    case 'out':
                        {
                            $st->bindParam($args_inc, $tmp_key, PDO::PARAM_STR, 4000);
                            break;
                        }
                    case 'inout':
                        {
                            $st->bindParam($args_inc, $tmp_key, PDO::PARAM_STR | PDO::PARAM_INPUT_OUTPUT, 4000);
                            break;
                        }
                    default:
                        {
                            $st->bindParam($args_inc, $tmp_key);
                        }
                }
            }
            $ok = $st->execute($args_val);
            if ($ok) {
                if ($return_resultset === true) {
                    do {
                        $row_data = $st->fetchAll(\PDO::FETCH_ASSOC);
                        if ($row_data) {
                            foreach ($row_data as $data) {
                                $resultset[] = $data;
                            }
                        }
                    } while ($st->nextRowset());
                }
            }
            unset($st);
            return $resultset;
        } catch (\Exception $e) {
            $this->MyDBException($e, $sql, $args);
        }
    }

    public function getStatement($sql, $args = array())
    {
        $sql = $this->changeSeparator($sql);

        if (! is_array($args)) {
            if (trim($args) != '') {
                $args = array(
                    $args
                );
            } else {
                $args = array();
            }
        }

        try {
            $res = $this->getResource();
            $st = $res->prepare($sql);
            $ok = $st->execute($args);
            if (! $ok) {
                $st = false;
            }
            return $st;
        } catch (\Exception $e) {
            $this->MyDBException($e, $sql, $args);
        }
    }

    public function getFetchAssoc($st)
    {
        if (! $st) {
            return false;
        } else {
            return $st->fetch(\PDO::FETCH_ASSOC);
        }
    }

    /*
     * Méthode à redéfinir dans chaque classe fille, la technique de pagination
     * étant différente selon la base de données utilisée
     */
    public function getPagination($sql, $args, $offset, $nbl_by_page, $order_by = '')
    {
        return $this->getScrollCursor($sql, $args, $offset, $nbl_by_page, $order_by);
    }

    /*
     * Pagination via la technique du Scroll Cursor telle qu'elle est implémentée
     * dans PDO
     */
    public function getScrollCursor($sql, $args, $offset, $nbl_by_page, $order_by = '')
    {
        if (! is_array($args)) {
            $args = array();
        }
        $offset = intval($offset);
        if ($offset <= 0) {
            $offset = 1;
        }
        /*
         * L'affichage doit démarrer sur l'offset -1, sinon on "rate" la première ligne
         */
        $offset --;

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
            $res = $this->getResource();
            $st = $res->prepare($sql, array(
                PDO::ATTR_CURSOR => PDO::CURSOR_SCROLL
            ));
            $st->execute($args);

            if ($offset > 0) {
                /*
                 * Un bug d'origine inconnu oblige à effectuer un premier
                 * positionnement
                 * sur la ligne n° 0, quand on affiche les offsets > 0
                 * Dans le cas où on affiche l'offset 0, il ne faut surtout pas faire
                 * ce premier positionnement, car il interfère avec celui qui est
                 * effecuté par la boucle d'affichage (for).
                 */
                $lost = $st->fetch(\PDO::FETCH_ASSOC, PDO::FETCH_ORI_REL, 0);
            }

            for ($tofetch = $nbl_by_page, $row = $st->fetch(\PDO::FETCH_ASSOC, PDO::FETCH_ORI_REL, $offset); $row !== false && $tofetch -- > 0; $row = $st->fetch(\PDO::FETCH_ASSOC)) {
                $rows[] = $row;
            }

            unset($st);
            return $rows;
        } catch (\Exception $e) {
            $this->MyDBException($e, $sql, $args);
        }
    }

    public function export2CSV($sql, $args = array())
    {
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
                    $row2[] = $col;
                } else {
                    $col = str_replace(array(
                        "\n",
                        "\r\n"
                    ), '', $col);
                    $col = str_replace(array(
                        '"',
                        "''"
                    ), '', $col);
                    $row2[] = '"' . trim($col) . '"';
                }
            }

            $csv .= join(';', $row2) . PHP_EOL;
            $row = $this->getFetchAssoc($st);
        }
        return $csv;
    }

    public function export2XML($sql, $args = array(), $tag_line = '', $gen_header = true)
    {
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
                    $xml .= '<' . $key . '>' . htmlspecialchars($col, ENT_QUOTES, "UTF-8") . '</' . $key . '>';
                } else {
                    $col = str_replace(array(
                        "\n",
                        "\r\n"
                    ), '', $col);
                    $col = str_replace(array(
                        '"',
                        "''"
                    ), '', $col);
                    $col = trim($col);
                    if (strlen($col) > 0) {
                        $xml .= '<' . $key . '>' . htmlspecialchars($col, ENT_QUOTES, "UTF-8") . '</' . $key . '>';
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

    public function export2insertSQL($sql, $args = array())
    {
        $st = $this->getStatement($sql, $args);
        $top_header_file = true;
        $sql_insert = '';
        $row = $this->getFetchAssoc($st);
        $nb_lig = 0;
        while ($row != false) {
            if ($nb_lig > 1000) {
                $nb_lig = 0;
                $top_header_file = true;
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
                    $row2[] = $col;
                } else {
                    /*
                     * le test ci-dessous ne sera pas pris en compte avec PDO
                     * qui renvoie des données de type string,
                     * par contre il fonctionnera avec DB2_Connect qui renvoie
                     * des données correctement typées
                     */
                    if (is_int($col) || is_float($col)) {
                        $row2[] = trim($col);
                    } else {
                        $col = str_replace(array(
                            "\n",
                            "\r\n"
                        ), '', $col);
                        $col = str_replace(array(
                            "'",
                            "''"
                        ), '', $col);
                        $col = str_replace(array(
                            '"',
                            "''"
                        ), '', $col);
                        $row2[] = "'" . trim($col) . "'";
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
            $nb_lig ++;
        }
        if ($sql_insert != '') {
            $sql_insert .= ';' . PHP_EOL;
        }
        return $sql_insert;
    }

    public function countNbRowsFromTable($table, $schema = '')
    {
        if (trim($schema) == '') {
            $sql = 'SELECT COUNT(*) AS NB_ROWS FROM ' . trim($table);
        } else {
            $sql = 'SELECT COUNT(*) AS NB_ROWS FROM ' . trim($schema) . '{SEPARATOR}' . trim($table);
        }
        $data = $this->selectOne($sql, array(), true);
        if (is_array($data) && isset($data[0])) {
            return $data[0];
        } else {
            return 0;
        }
    }

    public function countNbRowsFromSQL($sql, $args = array())
    {
        $sql = 'SELECT COUNT(*) AS NB_ROWS FROM (' . trim($sql) . ') AS FOO';

        $data = $this->selectOne($sql, $args, true);
        if (is_array($data) && isset($data[0])) {
            return $data[0];
        } else {
            return 0;
        }
    }

    public function valueIsExisting($table, $nomcol, $valcol, $where_optionnel = '')
    {
        $where_sql = " WHERE {$nomcol} = ? ";
        if ($where_optionnel != '') {
            $where_sql .= ' and ' . $where_optionnel;
        }
        $query = "SELECT '1' as good FROM {$table} {$where_sql} limit 1";
        $data = $this->selectOne($query, array(
            $valcol
        ), true);
        if (is_array($data) && isset($data[0]) && $data[0] == '1') {
            return true;
        } else {
            return false;
        }
    }

    public function valueIsExistingOnOtherRecord($table, $nomcol, $valcol, $idencours, $where_optionnel = '')
    {
        $where_sql = " WHERE {$nomcol} = ? and id <> ? ";
        if ($where_optionnel != '') {
            $where_sql .= ' and ' . $where_optionnel;
        }
        $query = "SELECT '1' as good FROM {$table} {$where_sql} limit 1 ";
        $data = $this->selectOne($query, array(
            $valcol,
            $idencours
        ), true);
        if (is_array($data) && isset($data[0]) && $data[0] == '1') {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Fonction utilisée avec la classe DBException permettant un meilleur
     * contrôle sur la présentation des erreurs renvoyées par cette classe
     *
     * @param type $db
     * @param type $DBexc
     * @param type $reqsql
     * @param type $args
     */
    public function MyDBException($DBexc, $reqsql = '', $args = array())
    {
        $tab_log = array();
        $tab_log['SQL_code'] = $reqsql;
        $tab_log['Message'] = $DBexc->getMessage();
        $tab_log['Trace'] = $DBexc->getTraceAsString();
        $tab_log['Code'] = $DBexc->getCode();
        $tab_log['File'] = $DBexc->getFile();
        $tab_log['Line'] = $DBexc->getLine();
        if (is_array($args) && count($args) > 0) {
            $tab_log['Arguments'] = var_export($args, true);
        } else {
            $tab_log['Arguments'] = '';
        }
        $dump = var_export($tab_log);
        error_log($dump);
    }

    /*
     * Méthode permettant de récupérer le dernier ID créé dans la BD
     * Le dernier ID est soit l'ID interne DB2, soit une séquence DB2 dont le code est transmis en paramètre
     */
    public function getLastInsertId($sequence = '')
    {
        $sequence = trim($sequence);
        if ($sequence == '') {
            $sql = "SELECT IDENTITY_VAL_LOCAL() AS LAST_INSERT_ID FROM SYSIBM{SEPARATOR}SYSDUMMY1";
        } else {
            $sql = "SELECT NEXT VALUE FOR {$sequence} AS LAST_INSERT_ID FROM SYSIBM{SEPARATOR}SYSDUMMY1";
        }
        $data = $this->selectOne($sql, array(), true);
        if (is_array($data) && isset($data[0])) {
            return $data[0];
        } else {
            return false;
        }
    }

    /**
     * Retourne un tableau contenant la liste des attributs PDO supportés par le driver DB2
     *
     * @param unknown_type $db
     */
    public function getInfoDatabase()
    {
        $result = array();

        if ($this->getResource() instanceof PDO) {
            $attributes = array(
                'DRIVER_NAME',
                'ERRMODE',
                'CASE',
                'CLIENT_VERSION',
                'DRIVER_NAME',
                'ORACLE_NULLS',
                'PERSISTENT'
            );
            $resource = $this->getResource();
            foreach ($attributes as $val) {
                $result["PDO::ATTR_$val"] = $resource->getAttribute(constant("PDO::ATTR_$val"));
            }
        }
        return $result;
    }

    protected function generate_dsn($options)
    {
        $array_dsn = array();

        if (isset($options['i5_naming']) && $options['i5_naming'] == true) {
            $array_dsn[] = 'NAM=1';
            $this->_sql_separator = '/';
        } else {
            $array_dsn[] = 'NAM=0';
        }
        if (isset($options['i5_libl']) && $options['i5_libl'] != '') {
            $array_dsn[] = 'DBQ=' . $options['i5_libl'];
        }
        if (isset($options['i5_lib']) && $options['i5_lib'] != '') {
            $array_dsn[] = 'DATABASE=' . $options['i5_lib'];
        }
        if (isset($options['i5_commit'])) {
            $array_dsn[] = 'CMT=' . $options['i5_commit'];
        }
        if (isset($options['i5_date_fmt'])) {
            $array_dsn[] = 'DFT=' . $options['i5_date_fmt'];
        }
        if (isset($options['i5_date_sep'])) {
            $array_dsn[] = 'DSP=' . $options['i5_date_sep'];
        }
        if (isset($options['i5_decimal_sep'])) {
            $array_dsn[] = 'DEC=' . $options['i5_decimal_sep'];
        }
        if (isset($options['i5_time_fmt'])) {
            $array_dsn[] = 'TFT=' . $options['i5_time_fmt'];
        }
        if (isset($options['i5_time_sep'])) {
            $array_dsn[] = 'TSP=' . $options['i5_time_sep'];
        }
        if (isset($options['i5_override_ccsid'])) {
            $options['CCSID'] = $options['i5_override_ccsid'];
            unset($options['i5_override_ccsid']);
        }
        if (isset($options['CCSID'])) {
            $option_ccsid = strtoupper($options['CCSID']);
            if ($option_ccsid == 'UTF-8' || $option_ccsid == 'UTF8') {
                $option_ccsid = '1208';
            }
            $array_dsn[] = 'CCSID=' . $option_ccsid;
        }
        $dsn = implode(';', $array_dsn);

        return $dsn;
    }
}
