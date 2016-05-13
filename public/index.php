<?php
require_once '../bootinit.php' ;
$titre = 'Logistic Project' ;

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
Cette application est la version allégée d'un prototype que j'ai réalisé
en prévision du développement d'une application "métier" de plus grande
envergure.</p>
<p>
  Le client pour lequel ce prototype a été réalisé avait quelques attentes très
  fortes qui étaient les suivantes :
</p>
<ul>
  <li>la possibilité de splitter l'écran en plusieurs parties distinctes et librement redimensionnables par l'utilisateur</li>
  <li>la possibilité de transférer des données d'une liste à une autre via une fonctionnalité de type "drag and drop"</li>
  <li>l'utilisation de PHP côté serveur, en lien avec un back-office géré par une base de données DB2</li>
</ul>
<p>
Un panel de solutions techniques assez large était à ma disposition côté client,
j'ai donc effectué un travail de défrichage pour vérifier la pertinence et la compatibilité
de composants open-source d'origine diverse. A l'usage, certains composants
se sont révélés ne pas bien fonctionner, soit avec la dernière version de jQuery,
soit avec Twitter Bootstrap. Comme mon objectif n'est pas de dénigrer quelque
projet que ce soit, je me suis contenté dans cette version du projet de conserver
une "short list" des composants les plus pertinents et les plus à même de répondre
aux besoins.
</p>
<p>
L'objectif en filigrane dans ce projet, employer les "best practices" du moment,
tant côté serveur que côté poste client, en évitant de s'enfermer dans une solution
technique trop figée.
</p>
<p>
Parmi les solutions techniques mises en oeuvre au sein de ce projet, on trouve :
<ul>
  <li>côté serveur (PHP)
    <ul>
      <li>l'utilisation des namespaces et de Composer pour l'assemblage du projet</li>
      <li>l'implémentation d'une couche d'accès base de données simplifiée directement inspirée
        d'un projet plus avancé que j'avais développé précédemment (MacaronDB).</li>
    </ul>
  </li>
  <li>côté navigateur (Javascript et HTML5)
    <ul>
      <li>l'utilisation de Twitter Bootstrap pour le rendu des pages</li>
      <li>l'utilisation du moteur de templating Handlebars.js pour le chargement dynamique des
      listes (via AJAX)</li>
      <li>l'utilisation du plugin jQuery Splitter pour réaliser simplement un "split" d'écran
      librement redimensionnable par l'utilisateur</li>
      <li>l'utilisation du projet Tablesort (projet de type VanillaJS) qui s'est révélé très efficace
        pour le chargement des listes avec un tri par colonnes</li>
      <li>l'implémentation en VanillaJS de l'API HTML5 "Drag and Drop" pour le transfert
      de données d'une liste vers une autre</li>
    </ul>
  </li>
</ul>
</p>
<p>
  Dans cette version de démonstration, la couche d'accès base de données a
  été court-circuitée au profit de données stockées sur le serveur au format
  JSON. L'activation de la couche DB2 se fait dans le fichier bootinit.php.
</p>
<p>
  Je vous invite à prendre connaissance des commentaires que j'ai placés sur
  chacun des projets testés au sein de ce projet.
  Les commentaires que j'ai indiqués n'engagent que moi, si certains des projets
  n'ont pas été retenus pour le prototype final, ce sont néanmoins des projets
  intéressants qui méritaient leur place ici. J'ai en revanche éliminé certains
  projets que je me garderai de citer ici. Entre les vieux Gantt obsolètes et
  généralement plus maintenus, les Datagrid présentant des incompatibilités
  manifestes avec Bootstrap, ou encore certains "splitters" d'écran incapables
  de fonctionner avec la dernière version de jQuery, c'est à un véritable
  travail de défrichage que j'ai dû me livrer. Mais je dois quand même ajouter
  que c'était un projet passionnant, que j'ai eu beaucoup de plaisir à réaliser.
</p>
            </div>
        </div>

        <footer class="footer">
            <div class="container">
                <p class="text-muted"></p>
            </div>
        </footer>
<?php
echo $page->jquery();
/*
?>
        <script src="js/lib/polyfill.js"></script>
        <script src="js/lib/opart.js"></script>
<?php
*/
echo $page->footer();
