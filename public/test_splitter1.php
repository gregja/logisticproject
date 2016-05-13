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
            <br><br>
            <div class="page-header">
                <h2><?php echo $titre; ?></h2>
            </div>
            <div>
              <p>
              Le plugin jQuery <a href="https://plugins.jquery.com/splitter/" target="_blank">Splitter</a> a été utilisé au sein de ce Projet
              pour créer des écrans splittable et librement redimensionnables
              par l'utilisateur. Simple à mettre en oeuvre, et peu gourmand
              en ressources, ce plugin s'est révélé idéal dans le cadre
              de ce prototype.
            </p>
            <p>

            </p>
            </div>

            <div>
        <div id="splitter1">
            <div id="foo">
                <div id="a">
                    <div id="x"><div style="padding: 0.5em; text-align:justify">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla sed dolor nisl, in suscipit justo. Donec a enim et est porttitor semper at vitae augue. Proin at nulla at dui mattis mattis. Nam a volutpat ante. Aliquam consequat dui eu sem convallis ullamcorper.</div></div>
                    <div id="y">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla sed dolor nisl, in suscipit justo. Donec a enim et est porttitor semper at vitae augue. Proin at nulla at dui mattis mattis. Nam a volutpat ante. Aliquam consequat dui eu sem convallis ullamcorper.</div>

                </div><!-- #a -->
                <div id="b">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla sed dolor nisl, in suscipit justo. Donec a enim et est porttitor semper at vitae augue. Proin at nulla at dui mattis mattis. Nam a volutpat ante. Aliquam consequat dui eu sem convallis ullamcorper. Nulla suscipit, massa vitae suscipit ornare, tellus est consequat nunc, quis blandit elit odio eu arcu. Nam a urna nec nisl varius sodales. Mauris iaculis tincidunt orci id commodo. Aliquam non magna quis tortor malesuada aliquam eget ut lacus. Nam ut vestibulum est. Praesent volutpat tellus in eros dapibus elementum. Nam laoreet risus non nulla mollis ac luctus felis dapibus. Pellentesque mattis elementum augue non sollicitudin. Nullam lobortis fermentum elit ac mollis. Nam ac varius risus. Cras faucibus euismod nulla, ac auctor diam rutrum sit amet. Nulla vel odio erat, ac mattis enim.</div>
            </div> <!-- end of #foo -->
            <div id="bar">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla sed dolor nisl, in suscipit justo. Donec a enim et est porttitor semper at vitae augue. Proin at nulla at dui mattis mattis. Nam a volutpat ante. Aliquam consequat dui eu sem convallis ullamcorper. Nulla suscipit, massa vitae suscipit ornare, tellus est consequat nunc, quis blandit elit odio eu arcu. Nam a urna nec nisl varius sodales. Mauris iaculis tincidunt orci id commodo. Aliquam non magna quis tortor malesuada aliquam eget ut lacus. Nam ut vestibulum est. Praesent volutpat tellus in eros dapibus elementum. Nam laoreet risus non nulla mollis ac luctus felis dapibus. Pellentesque mattis elementum augue non sollicitudin. Nullam lobortis fermentum elit ac mollis. Nam ac varius risus. Cras faucibus euismod nulla, ac auctor diam rutrum sit amet. Nulla vel odio erat, ac mattis enim.</div>
        </div> <!-- end of #splitter1 -->
        <div id="debug"></div>
			</div>
        </div>

        <footer class="footer">
            <div class="container">
                <p class="text-muted"></p>
            </div>
        </footer>
<?php
echo $page->jquery();
echo $page->splitter();
?>
<style>
    #spliter2 .a {
        background-color: #2d2d2d;
    }
    #spliter2 .b {
        background-color: #2d002d;
    }
    #foo {
        background-color: #E92727;
    }
    #x {
        background-color: #EFBD73;
    }
    #y {
        background-color: #EF3e32;
    }
    #b {
        background-color: #73A4EF;
    }
    #bar {
        background-color: #BEE927;
    }
</style>
<script>
jQuery(document).ready(function() {
    jQuery(function ($) {
        $('#splitter1').width(700).height(400).split({orientation: 'vertical', limit: 100, position: '70%'});
        $('#foo').split({orientation: 'horizontal', limit: 10});
        $('#a').split({orientation: 'vertical', limit: 10});
     /*   $('#bar').width(100).height(400).split({orientation: 'vertical', limit: 10}); */
    });
});
</script>
<?php

echo $page->footer();
