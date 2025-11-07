<?php
// Structure MVC pour le jeu de bataille
require_once 'controleur/BatailleController.php';
require_once 'vue/fonctions.inc.php';

// Création du contrôleur qui initialise le jeu
$controller = new BatailleController();

// Récupération du jeu de cartes
$tbCartes = $controller->getJeu();

// 8. Appelez cette procédure pour afficher le jeu de cartes après initialisation

// 10. Mélangez le jeu de cartes
$controller->melangerJeu();

// Récupération du jeu mélangé
$tbCartes = $controller->getJeu();


// 13. Définissez la couleur atout au hasard
$couleurAtout = $controller->definirCouleurAtoutAuHasard();;

// 14. Distribuez le jeu à 2 joueurs (2 tableaux de 26 cartes)
$controller->distribuerJeu();

// Récupération des jeux des joueurs
$jeuJoueur1 = $controller->getJeuJoueur1();
$jeuJoueur2 = $controller->getJeuJoueur2();

// 15. Affichez les 2 jeux de cartes

// 17. Jouez en comptant les points
$resultats = $controller->jouerPartie();
$historique = $resultats['historique'];

// 18. Affichez le gagnant
AfficherResultats($controller, $historique);
?>

