<?php

/**
 * Description of HTMLPage
 *
 * @author
 */

namespace Logistic ;

class HTMLPage {
    private $profondeur = '../' ;

    public function header ($datas=array()) {
        $title = 'Titre non défini' ;
        if (array_key_exists('title', $datas)) {
            $title = $datas['title'];
        }

$bloc_HTML = <<<BLOC
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{$title}</title>
        <!-- HTML5 Boilerplate -->
        <link href="{$this->profondeur}public/css/normalize.css" rel="stylesheet">
        <!-- Bootstrap -->
        <link href="{$this->profondeur}vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- CSS principal -->
        <link href="{$this->profondeur}public/css/main.css" rel="stylesheet">
    </head>
    <body>
BLOC;
        return $bloc_HTML ;
    }

    public function footer ($datas=array()) {

        $bloc_html = <<<BLOC
    </body>
</html>
BLOC;
        return $bloc_html ;
    }

    public function setProfondeur ($profondeur=1) {
        $profondeur = (int)$profondeur ;
        if ($profondeur<1 || $profondeur>10) {
            error_log('profondeur erronée sur setProfondeur (forcée à 1') ;
            $profondeur = 1 ;
        }
        $this->profondeur = str_repeat('../', $profondeur) ;
        $this->profondeur_server = str_repeat('../', $profondeur-1) ;
    }

    public function jquery ($datas=array()) {
        $bloc_html = <<<BLOC
        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!-- Placed at the end of the document so the pages load faster -->
        <script src="{$this->profondeur}vendor/components/jquery/jquery.min.js"></script>
        <script src="{$this->profondeur}vendor/twbs/bootstrap/dist/js/bootstrap.min.js"></script>
        <script src="{$this->profondeur}public/js/handlebars-v4.0.5.js"></script>
BLOC;
        return $bloc_html;
    }

    public function datatables ($datas=array()) {
        $bloc_html = <<<BLOC
<!--        <link href="{$this->profondeur}vendor/datatables/datatables/media/css/dataTables.foundation.min.css" rel="stylesheet"> -->
        <link href="{$this->profondeur}vendor/datatables/datatables/media/css/jquery.dataTables.min.css" rel="stylesheet">
        <script src="{$this->profondeur}vendor/datatables/datatables/media/js/jquery.dataTables.min.js"></script>
BLOC;
        return $bloc_html;
    }

    public function jqgrid ($datas=array()) {
        $bloc_html = <<<BLOC
        <link href="{$this->profondeur}public/js/jquery-ui-1.11.4.custom/jquery-ui.min.css" rel="stylesheet">
        <link href="{$this->profondeur}public/js/jqGrid-master/css/ui.jqgrid.css" rel="stylesheet">
        <script src="{$this->profondeur}public/js/jqGrid-master/jqGrid.min.js"></script>
        <script src="{$this->profondeur}public/js/jqGrid-master/js/i18n/grid.locale-fr.js"></script>
BLOC;
        return $bloc_html;
    }

    public function kendoui ($data=array()) {
        $bloc_html = <<<BLOC
        <link rel="stylesheet" href="{$this->profondeur}public/js/kendoui/styles/kendo.common.min.css">
        <link rel="stylesheet" href="{$this->profondeur}public/js/kendoui/styles/kendo.rtl.min.css">
        <link rel="stylesheet" href="{$this->profondeur}public/js/kendoui/styles/kendo.default.min.css">
        <link rel="stylesheet" href="{$this->profondeur}public/js/kendoui/styles/kendo.mobile.all.min.css">

<!--        <script src="{$this->profondeur}public/js/kendoui/jquery.min.js"></script> -->
        <script src="{$this->profondeur}public/js/kendoui/kendo.all.min.js"></script>
        <script src="{$this->profondeur}public/js/kendoui/jszip.min.js"></script>
        <script src="{$this->profondeur}public/js/kendoui/kendo.grid.min.js"></script>
BLOC;
        return $bloc_html;
    }

    public function jquerygantt ($datas=array()) {
        $bloc_html = <<<BLOC
        <link href="{$this->profondeur}public/js/jQuery_Gantt/jquery.gantt.css" rel="stylesheet">
        <script src="{$this->profondeur}public/js/jQuery_Gantt/jquery.fn.gantt.js"></script>
BLOC;
        return $bloc_html;
    }

    public function tablesort ($datas=array()) {
        $bloc_html = <<<BLOC
<!--        <link href="{$this->profondeur}public/js/tablesort_gh-pages.css" rel="stylesheet"> -->
        <script src="{$this->profondeur}public/js/tablesort_gh-pages.min.js"></script>
BLOC;
        return $bloc_html;
    }

    public function d3js ($datas=array()) {
        $bloc_html = <<<BLOC
        <script src="{$this->profondeur}public/js/d3.v3.min.js"></script>
BLOC;
        return $bloc_html;
    }

    public function d3gantt ($datas=array()) {
        $bloc_html = <<<BLOC
        <script src="{$this->profondeur}public/js/gantt-chart-d3v2.js"></script>
BLOC;
        return $bloc_html;
    }

    public function splitter() {
        $bloc_html = <<<BLOC
<script src="js/jquery.splitter-0.14.0.js"></script>
<link href="css/jquery.splitter.css" rel="stylesheet"/>
BLOC;
        return $bloc_html;
    }

    public function menu ($datas=array()) {
        $bloc_html = <<<BLOC
        <!-- Fixed navbar -->
        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container">
                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">LOGISTIC</a>
                </div>
                <div id="navbar" class="collapse navbar-collapse">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="/logisticproject">Accueil</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Options <span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li role="separator" class="divider"></li>
                                <li class="dropdown-header">DataGrid</li>
                                <li><a href="test_grid_datatables.php">Datagrid avec Datatables</a></li>
                                <li><a href="test_grid_jqgrid.php">Datagrid avec jqGrid</a></li>
                                <li><a href="test_grid_tablesort.php">Datagrid avec Tablesort</a></li>
                                <li role="separator" class="divider"></li>
                                <li class="dropdown-header">Splitter</li>
                                <li><a href="test_splitter1.php">Test Splitter 1</a></li>
                                <li><a href="test_splitter2.php">Test Splitter 2</a></li>
                                <li role="separator" class="divider"></li>
                                <li class="dropdown-header">Planning</li>
                                <li><a href="test_jquery_gantt.php">Test jQuery Gantt</a></li>
                                <li><a href="test_d3gantt.php">Test D3.js Gantt Chart</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Le P.O.C.<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li class="dropdown-header">Test intégration composants</li>
                                 <li><a href="test_split-datatab-tmpl.php">Splitter+Template+Datatables</a></li>
                                <li><a href="test_split-tablesort-tmpl.php">Splitter+Template+Tablesort</a></li>
                                <li><a href="test_split-tablesort-tmpl2.php">Splitter+Template+Tablesort+Drag&Drop</a></li>
                                <li class="dropdown-header">Version finale du POC</li>
                                <li><a href="test_split-tablesort-tmpl4.php">Planning de livraison</a></li>
                            </ul>
                        </li>
                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>
BLOC;
        return $bloc_html ;
    }
}
