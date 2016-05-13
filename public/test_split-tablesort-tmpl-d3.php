<?php
require_once '../bootinit.php' ;
$titre = 'Test Splitter' ;

$page = new Logistic\HTMLPage() ;
$params = array();
$params['title'] = $titre ;
echo $page->header($params) ;
echo $page->menu() ;
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
<?php
$ot_columns = Logistic\AppDatas::OTByDateColumns();
// template OT pour entête colonnes
$ot_tmpl1 = '' ;
// template OT pour détail colonnes "vides"
$ot_tmpl2 = '' ;
// template OT pour détail colonnes (destiné au template Handlebars)
$ot_tmpl3 = '' ;

foreach ($ot_columns as $ot_key=>$ot_value) {
    if ($ot_value['data_type'] == 'INTEGER' || $ot_value['data_type'] == 'NUMERIC') {
        $class = 'class="align_right"' ;
    } else {
        $class = '' ;
    }
    $ot_tmpl1 .= '<th '.$class.'>'.trim($ot_value['text']).'</th>'.PHP_EOL ;
    $ot_tmpl2 .= '<td '.$class.'>&nbsp;</td>'.PHP_EOL ;
    if (strstr($ot_key, '.') == false) {
        $tmp_key = $ot_key ;
    } else {
        $tmp_key = explode('.', $ot_key) ;
        $tmp_key = $tmp_key[count($tmp_key)-1] ;
    }
    $ot_tmpl3 .= '<td>{{'.$tmp_key.'}}</td>'.PHP_EOL ;
}

$chf_columns = Logistic\AppDatas::ChauffeursByDateColumns();
// template Chauffeurs pour entête colonnes
$chf_tmpl1 = '' ;
// template Chauffeurs pour détail colonnes "vides"
$chf_tmpl2 = '' ;
// template Chauffeurs pour détail colonnes (destiné au template Handlebars)
$chf_tmpl3 = '' ;

foreach ($chf_columns as $chf_key=>$chf_value) {
    if ($chf_value['data_type'] == 'INTEGER' || $chf_value['data_type'] == 'NUMERIC') {
        $class = 'class="align_right"' ;
    } else {
        $class = '' ;
    }
    $chf_tmpl1 .= '<th '.$class.'>'.trim($chf_value['text']).'</th>'.PHP_EOL ;
    $chf_tmpl2 .= '<td '.$class.'>&nbsp;</td>'.PHP_EOL ;
    if (strstr($chf_key, '.') == false) {
        $tmp_key = $chf_key ;
    } else {
        $tmp_key = explode('.', $chf_key) ;
        $tmp_key = $tmp_key[count($tmp_key)-1] ;
    }
    $chf_tmpl3 .= '<td>{{'.$tmp_key.'}}</td>'.PHP_EOL ;
}

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
    			<thead id="header">
    				<tr>
<?php echo $ot_tmpl1 ;?>
    				</tr>
    			</thead>
            <tbody id="datalist_ot">
                <tr data-id="">
<?php echo $ot_tmpl2 ;?>
                </tr>
            </tbody>
            </table>
		</div><!-- #a -->
		</div><!-- #split-container -->
        <div id="b" class="split-bottom ui-corner" >

	<div style="position: relative" class="gantt" id="datalist_chf" ></div>

        </div>
        </div>  <!-- end of #splitter1 -->
        </div> <!-- end of #main -->
        <div id="debug"></div> <!-- end of #debug -->
	</div>
</div> <!-- end of #container -->
<div id="list_modals" ></div>
<?php
echo $page->jquery();
echo $page->tablesort();
echo $page->splitter();
echo $page->jquerygantt();
?>
<script id="ot_tmpl" type="template/handlebars">
<tbody id="datalist_ot">
  {{#each messages}}
<tr class="sort" data-id="{{id}}" data-id="{{id}}" data-datdep="{{daenth}}" data-heudep="{{hhenth}}" data-datfin="{{dalvth}}" data-heufin="{{hhlvth}}" draggable="true">
<?php echo $ot_tmpl3 ;?>
</tr>
  {{/each}}
</tbody>
</script>

<script id="planning_ot_tmpl" type="template/handlebars">
  {{#each messages}}
<!-- Widget brands form create -->
<div class="modal fade" id="detail_ot1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Détail Ordre de Transport</h4>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group">
            <label for="recipient-name" class="control-label">Identifiant OT:</label>
            <input type="text" class="form-control" id="recipient-name"
                disabled="disabled" value="{{id}}" />
          </div>
          <div class="form-group">
            <label for="recipient-name" class="control-label">Date Enlèvement:</label>
            <input type="text" class="form-control" id="recipient-name"
                disabled="disabled" value="{{datdep}}" />
          </div>
          <div class="form-group">
            <label for="recipient-name" class="control-label">Heure Enlèvement:</label>
            <input type="text" class="form-control" id="recipient-name"
                disabled="disabled" value="{{heudep}}" />
          </div>
          <div class="form-group">
            <label for="recipient-name" class="control-label">Date Livraison:</label>
            <input type="text" class="form-control" id="recipient-name"
                disabled="disabled" value="{{datfin}}" />
          </div>
          <div class="form-group">
            <label for="recipient-name" class="control-label">Heure Livraison:</label>
            <input type="text" class="form-control" id="recipient-name"
                disabled="disabled" value="{{heufin}}" />
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Inutilisé</button>
      </div>
    </div>
  </div>
</div>
  {{/each}}
</script>

<script>

var $datachauff = [];

document.addEventListener('DOMContentLoaded', function() {
	"use strict";

	function calcDateGantt(tmpdate, tmpheure) {
		var tmpdate2 = tmpdate.split('-');
		var tmpheure1 = Number(tmpheure);
		if (tmpheure1>0 && tmpheure1<24) {
			tmpheure1 = tmpheure1*100;
		}
		var tmpheure2 = Math.floor(tmpheure1/100);
		var tmpdate3 = new Date(tmpdate2[0], tmpdate2[1]-1, tmpdate2[2], tmpheure2, 0, 0, 0, 0);
		return tmpdate3.getTime(); ;
	}

	function drawGantt (sourcedata) {
    	jQuery(".gantt").gantt({
    		source: sourcedata,
    		scale: "days",
    		navigate: "scroll",
    		maxScale: "hours",
    		itemsPerPage: 10,
    		onItemClick: function(data) {
    			console.log("Gantt : Item clicked - show some details");
    			console.log(data) ;
                var tmp = {};
                tmp.messages = { data } ;

                var list_modals = document.getElementById('list_modals');
                list_modals.innerHTML = tmpl_planning_ot1_compiled(tmp);
    			$('#detail_ot1').modal('toggle');

    		},
    		onAddClick: function(dt, rowId) {
    			console.log("Gantt : Empty space clicked - add an item!");
    			console.log(dt);
    			console.log(rowId);
    		},
    		onRender: function() {
    			console.log("Gantt : chart rendered");
    	    	addDropEvents('datalist_ot', 'datalist_chf') ;
    		}
    	});
	}

    var tmpl_planning_ot1 = document.getElementById('planning_ot_tmpl').innerHTML;
    var tmpl_planning_ot1_compiled = Handlebars.compile(tmpl_planning_ot1);

	var objsize = jQuery('#main') ;
	var scrwidth = objsize.width() * 0.9 ;
	var scrheight = Math.floor(window.innerHeight * 0.95) ;  //  700 ; ;
    jQuery('#splitter1').width(scrwidth).height(scrheight).split({orientation: 'horizontal', limit: 50, position: '50%'});

    var jqxhr_ot = jQuery.getJSON("data/ajax_dataset_ot_json.php?<?php echo MODE_EXE_BD ; ?>", function(datas) {
        var tmplsrc1 = document.getElementById('ot_tmpl').innerHTML;
        var template1 = Handlebars.compile(tmplsrc1);

        var datalist_ot = document.getElementById("datalist_ot");
        if (datalist_ot) {
            var tmp = {};
            tmp.messages = datas ;

        	datalist_ot.innerHTML = template1(tmp);
            addDragEvents('datalist_ot', 'datalist_chf') ;
            var datagrid_ot = new Tablesort(document.getElementById('liste_ot'));

        } else {
            console.log('Erreur : élément "liste_ot" absent du DOM');
        }
    })
    .fail(function( jqxhr_ot, textStatus, error ) {
        var err = textStatus + ", " + error;
        console.log( "Request Failed: " + err );
    });

    var jqxhr_chf = jQuery.getJSON("data/ajax_dataset_chf_voy_json.php?format=jquerygantt&test", function(datas) {
        var i, j ;
        var values_length, values_tmpdata ;
        var datas_length = datas.length;
        var tmpdata ;
        var tmpvalues ;
        var datdep, datfin, tmpdatdep, tmpdatfin, datdepnum, datfinnum ;
        for (i=0 ; i < datas_length ; i += 1 ) {
            tmpvalues = [];
            values_tmpdata = datas[i].values ;
            values_length = values_tmpdata.length ;

            for (j=0 ; j<values_length ; j+=1) {

            	datdepnum = calcDateGantt(values_tmpdata[j].datdep, values_tmpdata[j].heudep);
            	datfinnum = calcDateGantt(values_tmpdata[j].datfin, values_tmpdata[j].heufin);

            	tmpvalues.push({ id: values_tmpdata[j].id,
            		 from : "/Date("+datdepnum+")/",
            		 to : "/Date("+datfinnum+")/",
            		 desc : "<b>OT "+values_tmpdata[j].id+"<br>",
            		 customClass : "ganttRed",
            		 label : "OT "+values_tmpdata[j].id ,
            		 dataObj: values_tmpdata[j]
            		});
        	}

            tmpdata = {
        	        id: datas[i].id,
        			name: datas[i].name,
        			desc: datas[i].desc,
        			values: tmpvalues
    		} ;
            $datachauff.push(tmpdata);
    		sessionStorage.setItem('logischf-'+[datas[i].id]+'-ent', JSON.stringify(tmpdata));
    		sessionStorage.setItem('logischf-'+[datas[i].id]+'-det', JSON.stringify(tmpvalues));
        }

        drawGantt($datachauff) ;

    })
    .fail(function( jqxhr_chf, textStatus, error ) {
        var err = textStatus + ", " + error;
        console.log( "Request Failed: " + err );
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
                evt.dataTransfer.setData("Text", JSON.stringify(this.dataset));
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

        var droplist = catcher.querySelectorAll('div>[data-id]');
        var droplist_length = droplist.length;
        var color = '';
        for (var i = 0; i < droplist_length; i += 1) {

        	droplist[i].addEventListener("dragenter", function (evt) {
        		evt.target.style.color = 'red' ;
        	}, false);
        	droplist[i].addEventListener("dragleave", function (evt) {
        		evt.target.style.color = null ;
        	}, false);
        	droplist[i].addEventListener("dragover", function (evt) {
        		if (evt.preventDefault) {
        			evt.preventDefault();
        		}
        		return false;
        	}, false);
        	droplist[i].addEventListener("drop", function (evt) {
        		evt.target.style.color = null ;
        		if (evt.preventDefault) {
        			evt.preventDefault();
        		}
        		if (evt.stopPropagation) {
        			evt.stopPropagation();
        		}

        		var dataset = JSON.parse(evt.dataTransfer.getData("Text")); // get the dataset
        		var id = dataset['id'];

        		dataset['datdepnum'] = calcDateGantt(dataset['datdep'], dataset['heudep']);
        		dataset['datfinnum'] = calcDateGantt(dataset['datfin'], dataset['heufin']);

        		var elem = draghead.querySelector("[data-id='" + id + "']");
        		elem.parentNode.removeChild(elem); // remove the element

        		var targetnodes = evt.target.parentNode ;
        		var idchf = targetnodes.dataset.id;
        		var tmpkey = 'logischf-'+idchf+'-det';
        		var tmpsto ;
        		if (sessionStorage.getItem(tmpkey)) {
        			tmpsto = JSON.parse(sessionStorage.getItem(tmpkey)) ;
        		} else {
            		tmpsto = [];
        		}

                tmpsto.push({ id: dataset.id,
             	     from : "/Date("+dataset.datdepnum+")/",
          		 to : "/Date("+dataset.datfinnum+")/",
          		 desc : "<b>OT "+dataset.id+"<br>",
          		 customClass : "ganttRed",
          		 label : "OT "+dataset.id ,
          		 dataObj: dataset
          		});

        		sessionStorage.setItem(tmpkey, JSON.stringify(tmpsto));

        		var evtinfo = {};
        		evtinfo.orig = id;
                evtinfo.dest = idchf ;
        		console.log (JSON.stringify(evtinfo));

        		var $datachauff2 = [], i;
        		var datachauff_length = $datachauff.length;
        		var tmpdata1, tmpdata2, tmpdata3, tmpdata4, tmpdata2_length, tmpdata2_i, tmpkey ;

        		for (i=0 ; i < datachauff_length ; i += 1 ) {
                    //tmpdata1 = JSON.parse(sessionStorage.getItem('logischf-'+[$datachauff[i].id]+'-ent'));
                    tmpkey = 'logischf-'+[$datachauff[i].id]+'-det';
                    if (sessionStorage.getItem(tmpkey)) {
                    	tmpdata2 = JSON.parse(sessionStorage.getItem(tmpkey)) ;
                    } else {
                    	tmpdata2 = [];
                    }

                    tmpdata4 = {
                	        id: $datachauff[i].id,
                			name: $datachauff[i].name,
                			desc: $datachauff[i].desc,
                			values: tmpdata2
            		} ;
                    $datachauff2.push(tmpdata4);
                }
        		//TODO : plugger ici une requête AJAX POST vers le serveur
                drawGantt($datachauff2) ;

        		return false;
        	}, false);
        };

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
