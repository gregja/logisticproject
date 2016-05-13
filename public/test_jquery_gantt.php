<?php
require_once '../bootinit.php';
$titre = 'Test Gantt Query';

$page = new Logistic\HTMLPage();
$params = array();
$params['title'] = $titre;
echo $page->header($params);
echo $page->jquery();
echo $page->jquerygantt();
echo $page->menu();
?>
<style type="text/css">
	body {
		font-family: Helvetica, Arial, sans-serif;
		font-size: 13px;
		padding: 0 0 50px 0;
	}
	.contain {
		width: 800px;
		margin: 0 auto;
	}
	h1 {
		margin: 40px 0 20px 0;
	}
	h2 {
		font-size: 1.5em;
		padding-bottom: 3px;
		border-bottom: 1px solid #DDD;
		margin-top: 50px;
		margin-bottom: 25px;
	}
	table th:first-child {
		width: 130px;
	}
  /* Bootstrap 3.x re-reset */
  .fn-gantt *,
  .fn-gantt *:after,
  .fn-gantt *:before {
    -webkit-box-sizing: content-box;
       -moz-box-sizing: content-box;
            box-sizing: content-box;
  }
</style>
<div class="container_900">
	<br><br><br><br>
	<div class="container">
<p>
		Le projet <a href="https://taitems.github.io/jQuery.Gantt/" target="_blank">jQuery Gantt</a> est plus un planning qu'un véritable Gantt, mais
		il correspondait bien, fonctionnellement du moins, au besoin de ce prototype
		d'application.
</p><p>
		jQuery Gantt n'est pas particulièrement souple dans sa conception technique, aussi l'intégration
		dans ce composant du mode "drag and drop" s'est révélée délicate, mais néanmoins faisable.
		La structure de chaque ligne du planning étant peu appropriée pour un "drag and drop",
		j'ai opté pour un "drop" à destination du nom du chauffeur uniquement, comme
		on le verra dans la <a href="test_split-tablesort-tmpl4.php">version finale</a> du prototype.
</p><p>
		Dans l'optique de dépasser le stade du prototype, une solution de rechange
		à jQuery Gantt sera à rechercher du côté de produits commerciaux tels que
		<a href="http://www.bryntum.com/products/gantt/examples/" target="_blank">Ext Gantt de Bryntum</a>
		(qui est apparu peu de temps après le développement de ce prototype) ou éventuellement <a href="http://docs.dhtmlx.com/scheduler/" target="_blank">DHTMLX Scheduler</a> (le module Scheduler de DHTMLX nécessite de prendre une licence payante pour pouvoir le tester dans la configuration souhaitée pour ce prototype).
</p>
	</div>
<br><br>
	<div class="gantt"></div>
<br>
</div>
 <script>

 var month = 01;
 var day = 26;
 var year = 2016;
 var dt = new Date(year,day,month);
 console.log(dt.getTime());
 console.log(dt.getTime()+2);


	var $datasource = [{
		name: "Chauffeur 1",
		desc: "Livraisons",
		values: [{
			id: '0001',
			from: "/Date(1456441200000)/",
			to: "/Date(1456527600000)/",
			label: "Livraison 1",
		    desc : "<b>Task #1<br>",
			customClass: "ganttRed",
			label: "Task #1"
		},
		{
			id: '0002',
			from: "/Date(1456700400000)/",
			to: "/Date(1456873200000)/",
			label: "Livraison 2",
			desc : "<b>Task #2<br>",
			customClass: "ganttGreen"
		},
		{
			id: '0003',
			from: "/Date(1457046000000)/",
			to: "/Date(1457132400000)/",
			label: "Livraison 3",
			desc : "<b>Task #3<br>",
			customClass: "ganttBlue"
		}
		]
	},{
		name: " ",
		desc: "Divers",
		values: [{
			from: "/Date(1457046000000)/",
			to: "/Date(1457046000000)/",
			label: "Divers",
			customClass: "ganttRed"
		}]
	},{
		name: "Chauffeur 2",
		desc: "Livraisons",
		values: [{
			from: "/Date(1456441200000)/",
			to: "/Date(1456441200000)/",
			label: "Livraisons",
			customClass: "ganttGreen"
		}]
	},{
		name: " ",
		desc: "Divers",
		values: [{
			from: "/Date(1457046000000)/",
			to: "/Date(1457046000000)/",
			label: "Divers",
			customClass: "ganttBlue"
		}]
	},{
		name: "Chauffeur 3",
		desc: "Livraisons",
		values: [{
			from: "/Date(1456873200000)/",
			to: "/Date(1456873200000)/",
			label: "Livraisons",
			customClass: "ganttGreen"
		}]
	},{
		name: " ",
		desc: "Divers",
		values: [{
			from: "/Date(1456873200000)/",
			to: "/Date(1456873200000)/",
			label: "Showcasing",
			customClass: "ganttBlue"
		}]
	}];
 document.addEventListener('DOMContentLoaded', function() {

			"use strict";

			$(".gantt").gantt({
				source: $datasource,
				scale: "days",
				navigate: "scroll",
				maxScale: "hours",
				itemsPerPage: 10,
				onItemClick: function(data) {
					alert("Item clicked - show some details");
				},
				onAddClick: function(dt, rowId) {
					alert("Empty space clicked - add an item!");
				},
				onRender: function() {
					if (window.console && typeof console.log === "function") {
						// console.log("chart rendered");
					}
				}
			});

			$(".gantt").popover({
				selector: ".bar",
				title: "Je suis un 'popover'",
				content: "Contenu du 'popover'",
				trigger: "hover"
			});

		});
  </script>

<?php

echo $page->footer();
