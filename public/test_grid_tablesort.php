<?php
require_once '../bootinit.php' ;
$titre = 'Test Tablesort' ;

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
              <p>C'est le projet <a href="https://github.com/tristen/tablesort" target="_blank">Tablesort</a>
                qui a finalement été retenu pour la réalisation de ce prototype.
                Ecrit en VanillaJS, il est peu gourmand en ressources, performant
                et assez souple en termes de paramétrage. Il s'est révélé facile
                à intégrer, n'a posé aucun problème pour la mise au point du mode
                "drag and drop", et comme il ne gère pas lui même la pagination,
                c'est lui qui s'est révélé le mieux adapté à la problématique du
                 "split" d'écran.
              </p>
            </div>
            <div>
				<table id="example" class="sort table table-condensed display">
					<thead>
						<tr>
              <th class="sort-header">N° Cmde</th>
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
echo $page->tablesort();
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
	"use strict";
  var datagrid = new Tablesort(document.getElementById('example'));
  console.log(datagrid);
});
</script>
<?php

echo $page->footer();
