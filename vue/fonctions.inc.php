<?php
require_once 'controleur/BatailleController.php';
/**
 * Procédure pour afficher le jeu de cartes complet
 * Chaque carte sera affichée au moyen de sa méthode getNom
 * 
 * @param array $tbCartes Tableau contenant les objets Carte
 */
function AfficherJeu($tbCartes) {
    echo "<h2>Jeu de 52 cartes</h2>";
    echo "<div style='display: flex; flex-wrap: wrap; gap: 10px;'>";
    
    foreach ($tbCartes as $carte) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; border-radius: 5px;'>";
        echo $carte->getNom();
        echo "</div>";
    }
    
    echo "</div>";
    echo "<p><strong>Total : " . count($tbCartes) . " cartes</strong></p>";
}

/**
 * Affiche les jeux des 2 joueurs
 * 
 * @param array $jeuJoueur1 Tableau contenant les cartes du joueur 1
 * @param array $jeuJoueur2 Tableau contenant les cartes du joueur 2
 */
function AfficherJeuxJoueurs($jeuJoueur1, $jeuJoueur2) {
    echo "<div style='display: flex; gap: 20px;'>";
    
    // Jeu du joueur 1
    echo "<div style='flex: 1;'>";
    echo "<h3>Joueur 1 (" . count($jeuJoueur1) . " cartes)</h3>";
    echo "<div style='display: flex; flex-wrap: wrap; gap: 10px;'>";
    foreach ($jeuJoueur1 as $carte) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; border-radius: 5px;'>";
        echo $carte->getNom();
        echo "</div>";
    }
    echo "</div>";
    echo "</div>";
    
    // Jeu du joueur 2
    echo "<div style='flex: 1;'>";
    echo "<h3>Joueur 2 (" . count($jeuJoueur2) . " cartes)</h3>";
    echo "<div style='display: flex; flex-wrap: wrap; gap: 10px;'>";
    foreach ($jeuJoueur2 as $carte) {
        echo "<div style='border: 1px solid #ccc; padding: 10px; border-radius: 5px;'>";
        echo $carte->getNom();
        echo "</div>";
    }
    echo "</div>";
    echo "</div>";
    
    echo "</div>";
}

/**
 * Affiche les résultats de la partie et le gagnant
 * 
 * @param BatailleController $controller Le contrôleur contenant les scores
 * @param array $historique Historique des tours
 */
function AfficherResultats($controller, $historique = null) {
    $score1 = $controller->getScoreJoueur1();
    $score2 = $controller->getScoreJoueur2();
    $gagnant = $controller->getGagnant();
    
    echo "<hr>";
    echo "<h1>Résultats de la partie</h1>";
    
    echo "<div style='display: flex; gap: 20px; margin-bottom: 20px;'>";
    echo "<div style='flex: 1; padding: 20px; border: 2px solid #333; border-radius: 10px;'>";
    echo "<h2>Joueur 1</h2>";
    echo "<p style='font-size: 2em; font-weight: bold;'>" . $score1 . " points</p>";
    echo "</div>";
    
    echo "<div style='flex: 1; padding: 20px; border: 2px solid #333; border-radius: 10px;'>";
    echo "<h2>Joueur 2</h2>";
    echo "<p style='font-size: 2em; font-weight: bold;'>" . $score2 . " points</p>";
    echo "</div>";
    echo "</div>";
    
    echo "<div style='text-align: center; padding: 30px; background-color: #f0f0f0; border-radius: 10px; margin-top: 20px;'>";
    if ($gagnant == 1) {
        echo "<h1 style='color: green; font-size: 3em;'>🏆 Joueur 1 gagne ! 🏆</h1>";
    } elseif ($gagnant == 2) {
        echo "<h1 style='color: green; font-size: 3em;'>🏆 Joueur 2 gagne ! 🏆</h1>";
    } else {
        echo "<h1 style='color: orange; font-size: 3em;'>🤝 Égalité ! 🤝</h1>";
    }
    echo "</div>";
    
    // Afficher l'historique des tours si fourni
    if ($historique !== null && count($historique) > 0) {
        echo "<hr>";
        echo "<h2>Détail des tours</h2>";
        echo "<div style='max-height: 400px; overflow-y: auto;'>";
        echo "<table style='width: 100%; border-collapse: collapse;'>";
        echo "<thead>";
        echo "<tr style='background-color: #333; color: white;'>";
        echo "<th style='padding: 10px; border: 1px solid #ccc;'>Tour</th>";
        echo "<th style='padding: 10px; border: 1px solid #ccc;'>Carte Joueur 1</th>";
        echo "<th style='padding: 10px; border: 1px solid #ccc;'>Carte Joueur 2</th>";
        echo "<th style='padding: 10px; border: 1px solid #ccc;'>Gagnant</th>";
        echo "<th style='padding: 10px; border: 1px solid #ccc;'>Points</th>";
        echo "<th style='padding: 10px; border: 1px solid #ccc;'>Raison</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        
        foreach ($historique as $tour) {
            $carte1 = $tour['carteJoueur1'];
            $carte2 = $tour['carteJoueur2'];
            $resultat = $tour['resultat'];
            
            $gagnantText = '';
            if ($resultat['gagnant'] == 1) {
                $gagnantText = '<span style="color: blue; font-weight: bold;">Joueur 1</span>';
            } elseif ($resultat['gagnant'] == 2) {
                $gagnantText = '<span style="color: red; font-weight: bold;">Joueur 2</span>';
            } else {
                $gagnantText = '<span style="color: orange;">Égalité</span>';
            }
            
            echo "<tr>";
            echo "<td style='padding: 10px; border: 1px solid #ccc; text-align: center;'>" . $tour['tour'] . "</td>";
            echo "<td style='padding: 10px; border: 1px solid #ccc;'>" . $carte1->getNom() . " (" . $carte1->getValeur() . ")" . ($carte1->isAtout() ? ' <span style="color: red;">[Atout]</span>' : '') . "</td>";
            echo "<td style='padding: 10px; border: 1px solid #ccc;'>" . $carte2->getNom() . " (" . $carte2->getValeur() . ")" . ($carte2->isAtout() ? ' <span style="color: red;">[Atout]</span>' : '') . "</td>";
            echo "<td style='padding: 10px; border: 1px solid #ccc; text-align: center;'>" . $gagnantText . "</td>";
            echo "<td style='padding: 10px; border: 1px solid #ccc; text-align: center;'>" . $resultat['points'] . "</td>";
            echo "<td style='padding: 10px; border: 1px solid #ccc;'>" . ucfirst($resultat['raison']) . "</td>";
            echo "</tr>";
        }
        
        echo "</tbody>";
        echo "</table>";
        echo "</div>";
    }
}

/**
 * Obtient le chemin de l'image d'une carte
 * 
 * @param Carte $carte L'objet Carte
 * @return string Le chemin vers l'image de la carte
 */
function ObtenirImageCarte($carte) {
    // Mapper les couleurs françaises vers les noms anglais des fichiers
    $couleurMap = [
        'Cœur' => 'hearts',
        'Cœur♥️​' => 'hearts',
        'Carreau' => 'diamonds',
        'Carreau♦️' => 'diamonds',
        'Pique' => 'spades',
        'Pique♠️' => 'spades',
        'Trèfle' => 'clubs',
        'Trèfle♣️' => 'clubs'
    ];
    
    // Mapper les figures françaises vers les codes des fichiers
    $figureMap = [
        'As' => 'A',
        'Roi' => 'K',
        'Dame' => 'Q',
        'Valet' => 'J',
        '10' => '10',
        '9' => '09',
        '8' => '08',
        '7' => '07',
        '6' => '06',
        '5' => '05',
        '4' => '04',
        '3' => '03',
        '2' => '02'
    ];
    
    $couleurCarte = $carte->getCouleur();
    $figureCarte = $carte->getFigure();
    
    // Vérifier que la couleur existe dans le mapping
    if (!isset($couleurMap[$couleurCarte])) {
        // Tenter de trouver une correspondance partielle (sans emoji)
        foreach ($couleurMap as $cle => $valeur) {
            if (strpos($cle, 'Cœur') !== false && strpos($couleurCarte, 'Cœur') !== false) {
                $couleur = 'hearts';
                break;
            } elseif (strpos($cle, 'Carreau') !== false && strpos($couleurCarte, 'Carreau') !== false) {
                $couleur = 'diamonds';
                break;
            } elseif (strpos($cle, 'Pique') !== false && strpos($couleurCarte, 'Pique') !== false) {
                $couleur = 'spades';
                break;
            } elseif (strpos($cle, 'Trèfle') !== false && strpos($couleurCarte, 'Trèfle') !== false) {
                $couleur = 'clubs';
                break;
            }
        }
        if (!isset($couleur)) {
            throw new Exception("Couleur non reconnue : " . $couleurCarte);
        }
    } else {
        $couleur = $couleurMap[$couleurCarte];
    }
    
    // Vérifier que la figure existe dans le mapping
    if (!isset($figureMap[$figureCarte])) {
        throw new Exception("Figure non reconnue : " . $figureCarte);
    }
    
    $figure = $figureMap[$figureCarte];
    
    return "web/img/Card/card_{$couleur}_{$figure}.png";
}

/**
 * Obtient le chemin de l'image du dos de carte
 * 
 * @return string Le chemin vers l'image du dos de carte
 */
function ObtenirImageDosCarte() {
    return "web/img/Card_Back/card_back.png";
}
?>

