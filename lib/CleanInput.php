<?php

namespace Logistic ;

abstract class CleanInput {

    /**
     * méthode pour protéger les paramètres GET de différents types d'attaques
     * @param string $getchk : nom du poste de tableau à contrôler dans $_GET
     * @param string $san_type (facultatif) : valeurs possibles => "boolean", "bool", "integer", "int", "float", "string"
     * @param string $san_func (facultatif) : nom de fonction PHP pouvant être appelée pour des besoins spécifiques
     * @param array $sql_autorise (inutilisé)
     */
    public static function get($getchk, $san_type = "", $san_func = "", $sql_autorise = array()) {
        if (array_key_exists($getchk, $_GET)) {
            $tmp_get = $_GET[$getchk];
            $getclean = self::cleanVar($tmp_get, $san_type, $san_func);
        } else {
            $getclean = '';
        }
        return $getclean;
    }

    /**
     * méthode pour protéger les paramètres POST de différents types d'attaques
     * @param string $postchk : nom du poste de tableau à contrôler dans $_POST
     * @param string $san_type (facultatif) : valeurs possibles => "boolean", "bool", "integer", "int", "float", "string"
     * @param string $san_func (facultatif) : nom de fonction PHP pouvant être appelée pour des besoins spécifiques
     * @param array $sql_autorise (inutilisé)
     */
    public static function post($postchk, $san_type = "", $san_func = "", $sql_autorise = array()) {
        if (array_key_exists($postchk, $_POST)) {
            $tmp_post = $_POST[$postchk];
            $postclean = self::cleanVar($tmp_post, $san_type, $san_func);
        } else {
            $postclean = '';
        }
        return $postclean;
    }

    /**
     * Méthode appelée par les méthodes post et get
     * @param unknown $san_var : contenu de la variable à contrôler
     * @param string $san_type : valeurs possibles => "boolean", "bool", "integer", "int", "float", "string"
     * @param string $san_func (facultatif) : nom de fonction PHP pouvant être appelée pour des besoins spécifiques
     */
    public static function cleanVar($san_var, $san_type = "", $san_func = "") {

        $san_var = trim(strip_tags($san_var));
        $san_var = trim(self::remove_invisible_characters($san_var));
        $tmp_var = '';
        $lng_var = strlen($san_var);
        /*
         *  Les chaînes de plus de 50000 caractères sont rejetées systématiquement
         */
        if ($lng_var > 0 && $lng_var < 50000) {
            /*
             *  Convertit les caractères spéciaux en entités HTML, sauf les quotes et double-quotes
             */
            $tmp_var = htmlspecialchars($san_var, ENT_NOQUOTES);

            /*
             *  force un type particulier si précisé dans l'appel de la fonction
             */
            if ($san_type != '') {
                $arr_types = array("boolean", "bool", "integer", "int", "float", "string");
                if (in_array($san_type, $arr_types)) {
                    if (!settype($tmp_var, $san_type)) {
                        $tmp_var = '';
                    }
                } else {
                    // tentative d'utilisation d'un type inexistant, alors envoi d'un champ "vide" en réponse
                    $tmp_var = '';
                }
            }

            /*
             *  applique une fonction particulière si précisée dans l'appel de la fonction
             */
            if ($san_func != '' && is_callable($san_func)) {
                /*
                 * apply functions to the variables, you can use the standard PHP
                 * functions, but also use your own for added flexibility.
                 */
                $tmp_var = $san_func($tmp_var);
            }
        }
        return $tmp_var;
    }

    /**
     * méthode empruntée au framework Code Igniter
     * @param unknown $str
     * @param string $url_encoded
     */
    public static function remove_invisible_characters($str, $url_encoded = TRUE) {
        $non_displayables = array();

        // every control character except newline (dec 10)
        // carriage return (dec 13), and horizontal tab (dec 09)

        if ($url_encoded) {
            $non_displayables[] = '/%0[0-8bcef]/'; // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/'; // url encoded 16-31
        }

        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S'; // 00-08, 11, 12, 14-31, 127

        do {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        } while ($count);

        return $str;
    }

    /**
     * méthode empruntée à l'article suivant :
     * http://www.sitepoint.com/character-encodings-and-input/
     * très efficace pour éliminer certains caractères déclencheurs de plantages sur DB2
     * @param unknown $str
     * @return mixed
     * @param unknown $str
     */
    public static function remove_dangerous_characters($str) {
        return preg_replace('/[^\x09\x0A\x0D\x20-\x7F\xC0-\xFF]/', '', $str);
    }

    /**
     * Elimination de certains caractères parasites, dont le caractère SUB qui est renvoyé quelques fois par SQL,
     * notamment à la fin du source des procédures stockées et des fonctions, quand elles sont récupérées via db2_connect
     * (le problème ne se présentant pas avec PDO).
     * Le tableau des codes ASCII est disponible ici : www.asciitable.com
     * @param string $sql
     */
    public static function clean_code($sql) {
        $tab_chr = array();
        for ($control = 0; $control < 32; $control++) {
            if ($control != 9 && $control != 10) {
                // les sauts de ligne et les tabulations sont conservées
                $tab_chr[] = chr($control);
            }
        }
        // le caractère DEL est lui aussi à éliminer
        $tab_chr[] = chr(127);

        $sql = str_replace($tab_chr, '', $sql);

        return $sql;
    }

}
