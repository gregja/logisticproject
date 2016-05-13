# logisticproject
Projet Test pour recherche d'ergonomie
Ce projet est un prototype visant à tester différents composants permettant : 
- le split d'écran redimensionnable
- le drag and drop de données entre deux tableaux HTML distincts

Plusieurs composants et techniques ont été retenus pour ce projet :
- l'utilisation de Twitter Bootstrap pour le rendu des pages
- l'utilisation du moteur de templating Handlebars.js pour le chargement dynamique des listes (via AJAX)
- l'utilisation du plugin jQuery Splitter pour réaliser simplement un "split" d'écran librement redimensionnable par l'utilisateur
- l'utilisation du projet Tablesort (projet de type VanillaJS) qui s'est révélé très efficace pour le chargement des listes avec un tri par colonnes
- l'implémentation en VanillaJS de l'API HTML5 "Drag and Drop" pour le transfert de données d'une liste vers une autre

