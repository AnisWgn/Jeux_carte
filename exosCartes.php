<?php
require_once 'modele/class.carte.inc.php';

// a. Créer un objet uneCarte en instanciant la classe Carte pour représenter le roi de trèfle
$uneCarte = new Carte('Trèfle', 'Roi');

// b. Afficher la couleur de la carte
echo "Couleur : " . $uneCarte->getCouleur() . "<br>";

// c. Afficher le nom de la carte
echo "Nom : " . $uneCarte->getNom() . "<br>";

// d. Afficher si cette carte est une carte atout (Cœur est la couleur atout)
echo "Est atout : " . ($uneCarte->isAtout() ? 'Oui' : 'Non') . "<br>";
?>