<?php
require_once '../bootinit.php' ;
$titre = 'Test Splitter' ;

$page = new Logistic\HTMLPage() ;
$params = array();
$params['title'] = $titre ;
echo $page->header($params) ;
echo $page->menu() ;
?>
<!-- Begin page content -->
<div class="container">
    <br><br><br>


<div id="main" class="container theme-showcase ui-corner" role="main">
	<div id="splitter1" class="splitscreen">
		<div id="split-container">
        <div id="a" class="split-top ui-corner" >
        <br>
    		<table id="liste_ot" class="table table-condensed display" cellspacing="0" width="100%">
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
            <tbody id="datalist_ot"></tbody>
            </table>
		</div><!-- #a -->
		</div><!-- #split-container -->
        <div id="b" class="split-bottom ui-corner" >
        <br>
    		<table id="liste_voy" class="table table-condensed display" cellspacing="0" width="100%">
    			<thead>
    				<tr>
    					<th>Id Voyage</th>
    					<th>Libellé</th>
    					<th>Id Chauffeur</th>
    					<th>Nom Chauffeur</th>
    					<th>Nb Liv.</th>
    					<th>Date départ</th>
    				</tr>
    			</thead>
            <tbody id="datalist_voy">
                <tr data-id="">
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                </tr>
            </tbody>

        </div>
        </div>  <!-- end of #splitter1 -->
        </div> <!-- end of #main -->
        <div id="debug"></div> <!-- end of #debug -->
	</div>
</div> <!-- end of #container -->

<?php
echo $page->jquery();
echo $page->tablesort();
echo $page->splitter();
?>
<script id="ot_tmpl" type="template/handlebars">
<tbody id="datalist_ot">
  {{#each messages}}
<tr class="sort" data-id="{{id}}" draggable="true">
<td>{{nocd}}</td>
<td>{{nopo}}</td>
<td>{{nocl}}</td>
<td>{{noex}}</td>
<td>{{dalvth}}</td>
<td>{{hhlvth}}</td>
<td>{{daenth}}</td>
<td>{{hhenth}}</td>
</tr>
  {{/each}}
</tbody>
</script>

<script id="voyage_tmpl" type="template/handlebars">
<tbody id="datalist_voy">
  {{#each messages}}
<tr class="sort" data-id="{{id}}" draggable="true">
<td>{{id}}</td>
<td>{{libelle}}</td>
<td>{{idchf}}</td>
<td>{{nomchf}}</td>
<td>{{nbliv}}</td>
<td>{{startdate}}</td>
</tr>
  {{/each}}
</tbody>
</script>

<script>
/*
var jqxhr_ot = jQuery.getJSON("test_datatables_json.php", function(data) {
    console.log( "success" );
  })
  .done(function() {
    console.log( "second success" );
  })
  .fail(function( jqxhr_ot, textStatus, error ) {
    var err = textStatus + ", " + error;
    console.log( "Request Failed: " + err )})
  .always(function() {
    console.log( "complete" );
  });
*/

document.addEventListener('DOMContentLoaded', function() {
	"use strict";

	var objsize = jQuery('#main') ;
	var scrwidth = objsize.width() * 0.9 ;
	var scrheight = Math.floor(window.innerHeight * 0.95) ;  //  700 ; ;
    jQuery('#splitter1').width(scrwidth).height(scrheight).split({orientation: 'horizontal', limit: 50, position: '50%'});

    var jqxhr_ot = jQuery.getJSON("tmpdata/tmp_dataset_ot_json.php", function(datas) {
        var tmplsrc1 = document.getElementById('ot_tmpl').innerHTML;
        var template1 = Handlebars.compile(tmplsrc1);

        var datalist_ot = document.getElementById("datalist_ot");
        if (datalist_ot) {
            var tmp = {};
            tmp.messages = datas ;
            var result = template1(tmp)
        	datalist_ot.innerHTML = result;
            addDragEvents('datalist_ot', 'datalist_voy') ;
            var datagrid_ot = new Tablesort(document.getElementById('liste_ot'));

        } else {
            console.log('Erreur : élément "liste_ot" absent du DOM');
        }
    })
    .fail(function( jqxhr_ot, textStatus, error ) {
        var err = textStatus + ", " + error;
        console.log( "Request Failed: " + err )
    });

    var jqxhr_voy = jQuery.getJSON("tmpdata/tmp_dataset_voyage_json.php", function(datas) {
        var tmplsrc2 = document.getElementById('voyage_tmpl').innerHTML;
        var template2 = Handlebars.compile(tmplsrc2);

        var datalist_voy = document.getElementById("datalist_voy");
        if (datalist_voy) {
            var tmp = {};
            tmp.messages = datas ;
            var result = template2(tmp)
        	datalist_voy.innerHTML = result;
            addDropEvents('datalist_ot', 'datalist_voy') ;

            var datagrid_voy = new Tablesort(document.getElementById('liste_voy'));

        } else {
            console.log('Erreur : élément "datalist_voy" absent du DOM');
        }
    })
    .fail(function( jqxhr_voy, textStatus, error ) {
        var err = textStatus + ", " + error;
        console.log( "Request Failed: " + err )
    });

    function addDragEvents (origine, destination) {
        var draghead = document.getElementById(origine);
        if (!draghead) {
            console.log('fonction addDragEvents : origine inexistante')
        }
        var draglist = draghead.querySelectorAll("tr");
        var draglist_length = draglist.length ;
        for (var i = 0; i < draglist_length; i += 1) {
            draglist[i].addEventListener("dragstart", function (evt) {
                evt.target.style.border = "3px dotted #000"; // black dotted-line border
                evt.dataTransfer.effectAllowed = "move";
                evt.dataTransfer.setData("Text", this.dataset['id']);
            }, false);
            draglist[i].addEventListener("dragend", function (evt) {
                evt.target.style.border = null; // remove the border
            }, false);
        }
    }

    function addDropEvents (origine, destination) {
        var draghead = document.getElementById(origine);
        if (!draghead) {
            console.log('fonction addDropEvents : origine inexistante') ;
            return false ;
        }
    	var catcher = document.getElementById(destination);
        if (!catcher) {
            console.log('fonction addDropEvents : destination inexistante')
        }

        var droplist = catcher.querySelectorAll("tr");
        var droplist_length = droplist.length;
        for (var i = 0; i < droplist_length; i += 1) {

        	// catch the dropped element
        	droplist[i].addEventListener("dragenter", function (evt) {
        		evt.target.parentNode.style.border = "3px solid red"; // give the catcher a red border
        	}, false);
        	droplist[i].addEventListener("dragleave", function (evt) {
        		evt.target.parentNode.style.border = null; // remove the border from the catcher
        	}, false);
        	droplist[i].addEventListener("dragover", function (evt) {
        		if (evt.preventDefault)
        			evt.preventDefault();
        		return false;
        	}, false);
        	droplist[i].addEventListener("drop", function (evt) {
        		evt.target.parentNode.style.border = null;
        		if (evt.preventDefault) {
        			evt.preventDefault();
        		}
        		if (evt.stopPropagation) {
        			evt.stopPropagation();
        		}
        		this.style.border = ""; // remove the border from the catcher
        		var id = evt.dataTransfer.getData("Text"); // get the id
        		var elem = draghead.querySelector("[data-id='" + id + "']");
            $( elem ).fadeOut( 800, function() {
              elem.parentNode.removeChild(elem);
            });
        		var targetnodes = evt.target.parentNode ;
        		var nbliv = targetnodes.children[4];
        		var nblivnum = Number(nbliv.innerHTML)+1;
        		targetnodes.children[4].innerHTML = nblivnum ;
        		var evtinfo = {};
        		evtinfo.orig = id;
                evtinfo.dest = targetnodes.children[0].innerHTML ;
        		console.log (JSON.stringify(evtinfo));
        		//TODO : plugger ici une requête AJAX POST vers le serveur
        		return false;
        	}, false);
        }

    }
});
</script>
<style>
tr {
	line-height: 10px;
}
</style>
<?php

echo $page->footer();
