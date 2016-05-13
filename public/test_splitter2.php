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
            <br><br><br><br>


        <div id="main" class="container theme-showcase ui-corner" role="main">
           <div id="splitter1" class="splitscreen">
              <div id="split-container">
                <div id="a" class="split-top ui-corner" >
                  <p>Il s'agit toujours ici de jQuery <a href="https://plugins.jquery.com/splitter/" target="_blank">Splitter</a>, mais dans un mode d'utilisation proche de celui souhaité pour le projet final.</p>
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec a diam lectus. Sed sit amet ipsum mauris. Maecenas congue ligula ac quam viverra nec consectetur ante hendrerit. Donec et mollis dolor. Praesent et diam eget libero egestas mattis sit amet vitae augue. Nam tincidunt congue enim, ut porta lorem lacinia consectetur. Donec ut libero sed arcu vehicula ultricies a non tortor. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean ut gravida lorem. Ut turpis felis, pulvinar a semper sed, adipiscing id dolor. Pellentesque auctor nisi id magna consequat sagittis. Curabitur dapibus enim sit amet elit pharetra tincidunt feugiat nisl imperdiet. Ut convallis libero in urna ultrices accumsan. Donec sed odio eros. Donec viverra mi quis quam pulvinar at malesuada arcu rhoncus. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. In rutrum accumsan ultricies. Mauris vitae nisi at sem facilisis semper ac in est.
Vivamus fermentum semper porta. Nunc diam velit, adipiscing ut tristique vitae, sagittis vel odio. Maecenas convallis ullamcorper ultricies. Curabitur ornare, ligula semper consectetur sagittis, nisi diam iaculis velit, id fringilla sem nunc vel mi. Nam dictum, odio nec pretium volutpat, arcu ante placerat erat, non tristique elit urna et turpis. Quisque mi metus, ornare sit amet fermentum et, tincidunt et orci. Fusce eget orci a orci congue vestibulum. Ut dolor diam, elementum et vestibulum eu, porttitor vel elit. Curabitur venenatis pulvinar tellus gravida ornare. Sed et erat faucibus nunc euismod ultricies ut id justo. Nullam cursus suscipit nisi, et ultrices justo sodales nec. Fusce venenatis facilisis lectus ac semper. Aliquam at massa ipsum. Quisque bibendum purus convallis nulla ultrices ultricies. Nullam aliquam, mi eu aliquam tincidunt, purus velit laoreet tortor, viverra pretium nisi quam vitae mi. Fusce vel volutpat elit. Nam sagittis nisi dui.
                </div>
                </div><!-- #a -->
                <div id="b" class="split-bottom ui-corner" >Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla sed dolor nisl, in suscipit justo. Donec a enim et est porttitor semper at vitae augue. Proin at nulla at dui mattis mattis. Nam a volutpat ante. Aliquam consequat dui eu sem convallis ullamcorper. Nulla suscipit, massa vitae suscipit ornare, tellus est consequat nunc, quis blandit elit odio eu arcu. Nam a urna nec nisl varius sodales. Mauris iaculis tincidunt orci id commodo. Aliquam non magna quis tortor malesuada aliquam eget ut lacus. Nam ut vestibulum est. Praesent volutpat tellus in eros dapibus elementum. Nam laoreet risus non nulla mollis ac luctus felis dapibus. Pellentesque mattis elementum augue non sollicitudin. Nullam lobortis fermentum elit ac mollis. Nam ac varius risus. Cras faucibus euismod nulla, ac auctor diam rutrum sit amet. Nulla vel odio erat, ac mattis enim.</div>
                <div id="bar">xxxxxLorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla sed dolor nisl, in suscipit justo. Donec a enim et est porttitor semper at vitae augue. Proin at nulla at dui mattis mattis. Nam a volutpat ante. Aliquam consequat dui eu sem convallis ullamcorper. Nulla suscipit, massa vitae suscipit ornare, tellus est consequat nunc, quis blandit elit odio eu arcu. Nam a urna nec nisl varius sodales. Mauris iaculis tincidunt orci id commodo. Aliquam non magna quis tortor malesuada aliquam eget ut lacus. Nam ut vestibulum est. Praesent volutpat tellus in eros dapibus elementum. Nam laoreet risus non nulla mollis ac luctus felis dapibus. Pellentesque mattis elementum augue non sollicitudin. Nullam lobortis fermentum elit ac mollis. Nam ac varius risus. Cras faucibus euismod nulla, ac auctor diam rutrum sit amet. Nulla vel odio erat, ac mattis enim.</div>
            </div>  <!-- end of #foo -->

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
<script>
jQuery(document).ready(function() {
	var objsize = $('#main') ;
	var scrwidth = objsize.width() * 0.9 ;
	var scrheight = 600 ; ;
    $('#splitter1').width(scrwidth).height(scrheight).split({orientation: 'horizontal', limit: 50, position: '50%'});
    //$('#bar').width(100).height(400).split({orientation: 'vertical', limit: 10}); */

});
</script>
<?php

echo $page->footer();
