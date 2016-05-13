<?php
require_once '../bootinit.php' ;
$titre = 'Test Datatables' ;

$page = new Logistic\HTMLPage() ;
$params = array();
$params['title'] = $titre ;
echo $page->header($params) ;
echo $page->menu() ;
?>
        <!-- Begin page content -->
        <div class="container">
            <br><br>
            <div class="page-header">
                <h2><?php echo $titre; ?></h2>
            </div>
            <div>
              <p>Le plugin jQuery <a href="https://datatables.net/" target="_blank">Datatables</a> avait été envisagé dans un premier
                temps pour la réalisation de ce prototype. Mais Datatables a pour particularité
                de se "faire" une représentation interne d'un tableau HTML préalablement
                constitué, et il me manquait une fonction permettant de regénérer
                cette représentation interne en fonction des lignes de tableau
                supprimées au sein du DOM (la suppression s'effectuant au moment
                du "drag and drop"). En réalité, la fonctionnalité existe peut être
                au sein de Datatables, mais des lacunes dans la documentation du plugin
                m'ont amené à chercher une solution de remplacement.
            </div>
            <div>
				<table id="example" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th>N° Cmde</th>
							<th>N° Position</th>
							<th>N° Destin.</th>
							<th>N° Expédit.</th>
							<th>Date Liv.</th>
							<th>Heure Liv.</th>
              <th>Date Enl.</th>
							<th>Heure Enl.</th>
						</tr>
					</thead>
					<tbody>
<?php
require 'data/tmp_dataset_ot_json.php' ;
$tmpdata = json_decode($jsondata) ;

foreach ($tmpdata as $data) {
    echo '<tr>';
    echo '<td>'.$data->nocd.'</td>';
    echo '<td>'.$data->nopo.'</td>';
    echo '<td>'.$data->nocl.'</td>';
    echo '<td>'.$data->noex.'</td>';
    echo '<td>'.$data->dalvth.'</td>';
    echo '<td>'.$data->hhlvth.'</td>';
    echo '<td>'.$data->daenth.'</td>';
    echo '<td>'.$data->hhenth.'</td>';
    echo '</tr>'. PHP_EOL ;
  }
?>
					</tbody>
				</table>
			</div>
        </div>

        <footer class="footer">
            <div class="container">
                <p class="text-muted"></p>
            </div>
        </footer>
<?php
echo $page->jquery();
echo $page->datatables();
?>
<script>
$(document).ready(function() {
	$('#example').DataTable({
		  paginate: false,
		  scrollY: 300
		}
	);

	/**
	  Code "scrapper" utilisé temporairement pour récupérer les données du tableau HTML au format
	  JSON, de manière à pouvoir les réutiliser avec un moteur de templating

	var tmpobj = document.querySelector('#example').querySelector('tbody').querySelectorAll('tr');
	var tmpobj_length = tmpobj.length ;
    var i, j ;
    var datares = [];
    var tmptd = [];
    var datalig = {};
    for (i = 0 ; i < tmpobj_length ; i+=1) {
        tmptd = tmpobj[i].children ;
        datalig = {};
        for (j = 0 ; j < tmptd.length ; j+=1) {
            if (tmptd[j].nodeType == 1) {
                switch (j) {
                	case 0:{
                    	datalig.name = tmptd[j].firstChild.data ;
                    }
                	case 1:{
                    	datalig.position = tmptd[j].firstChild.data ;
                	}
                	case 2:{
                    	datalig.office = tmptd[j].firstChild.data ;
                	}
                	case 3:{
                    	datalig.age = tmptd[j].firstChild.data ;
                	}
                	case 4:{
                    	datalig.startdate = tmptd[j].firstChild.data ;
                	}
                	case 5:{
                    	datalig.salary = tmptd[j].firstChild.data ;
                	}
                }
            }
        }
        datares.push(datalig);

    }
	console.log(JSON.stringify(datares));
    */

});
</script>
<?php

echo $page->footer();
