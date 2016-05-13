<?php
require_once '../bootinit.php' ;
$titre = 'Test jqGrid' ;

$page = new Logistic\HTMLPage() ;
$params = array();
$params['title'] = $titre ;
echo $page->header($params) ;
echo $page->menu() ;
echo $page->jquery();
echo $page->jqgrid();
?>
<!-- Begin page content -->
<div class="container">
    <br><br>
    <div class="page-header">
        <h2><?php echo $titre; ?></h2>
    </div>

    <div>
      <p>Le plugin jQuery <a href="http://www.trirand.com/" target="_blank">jqGrid</a>
        a été envisagé dans le cadre du projet, car il offre des fonctionnalités
        dignes d'intérêt, tels qu'un mode "CRUD" qui semble facile à mettre en oeuvre.
        De plus, un nouveau rendu de type Bootstrap est apparu récemment, qui
        rend ce projet plus apte à s'intégrer dans ce prototype d'application.
        Si jQGrid n'a pas été retenu pour la version finale du prototype, ce choix pourrait
        être rediscuté aujourd'hui, au vu des dernières évolutions du projet.
    </div>


<table id="jqGrid"></table>
<div id="jqGridPager"></div>

<div>

<script type="text/javascript">
jQuery(document).ready(function () {
    jQuery("#jqGrid").jqGrid({
        url: 'data/ajax_dataset_ot_json.php?<?php echo MODE_EXE_BD ; ?>',
        mtype: "GET",
        datatype: "json",
        editurl: 'clientArray',
        colModel: [
                   { label: 'N° cmde', name: 'nocd', width: 75,
                        editable: true,
						editrules : { required: true} },
                   { label: 'N° pos.', name: 'nopo', width: 75, editable: true },
                   { label: 'N° client', name: 'nocl', width: 75, editable: true },
                   { label: 'n° expéd.', name: 'noex', width: 75, editable: true },
                   { label:'Date Liv.', name: 'dalvth', width: 75, editable: true },
                   { label:'Heure Liv.', name: 'hhlvth', width: 75, editable: true },
                   { label:'Date Enl.', name: 'daenth', width: 75, editable: true },
                   { label:'Heure Enl.', name: 'hhenth', width: 75, editable: true }
                  ],
		viewrecords: true,
        width: 780,
        height: 250,
        rowNum: 20,
        pager: "#jqGridPager"
    });



$('#jqGrid').navGrid('#jqGridPager',
		// the buttons to appear on the toolbar of the grid
		{ edit: true, add: true, del: true, search: false, refresh: false, view: false, position: "left", cloneToTop: false },
		// options for the Edit Dialog
		{
			editCaption: "The Edit Dialog",
			recreateForm: true,
			checkOnUpdate : true,
			checkOnSubmit : true,
			closeAfterEdit: true,
			errorTextFormat: function (data) {
				return 'Error: ' + data.responseText
			}
		},
		// options for the Add Dialog
		{
			closeAfterAdd: true,
			recreateForm: true,
			errorTextFormat: function (data) {
				return 'Error: ' + data.responseText
			}
		},
		// options for the Delete Dailog
		{
			errorTextFormat: function (data) {
				return 'Error: ' + data.responseText
		}
	});

});

</script>

<?php

echo $page->footer();
