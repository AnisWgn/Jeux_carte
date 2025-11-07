<?php
// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once 'controleur/BatailleController.php';
require_once 'vue/fonctions.inc.php';

// Initialisation de la partie si nécessaire
if (!isset($_SESSION['partie_initialisee']) || isset($_GET['nouvelle_partie'])) {
    // Réinitialiser la session si demandé
    if (isset($_GET['nouvelle_partie'])) {
        session_destroy();
        session_start();
    }
    
    // Nouvelle partie
    $_SESSION['partie_initialisee'] = true;
    
    $controller = new BatailleController();
    $controller->melangerJeu();
    $couleurAtout = $controller->definirCouleurAtoutAuHasard();
    $controller->distribuerJeu();
    
    // Stocker les données en session
    $_SESSION['couleur_atout'] = $couleurAtout;
    $_SESSION['jeu_joueur1'] = BatailleController::cartesVersTableaux($controller->getJeuJoueur1());
    $_SESSION['jeu_joueur2'] = BatailleController::cartesVersTableaux($controller->getJeuJoueur2());
    $_SESSION['score_joueur1'] = 0;
    $_SESSION['score_joueur2'] = 0;
    $_SESSION['tour_actuel'] = 1;
    $_SESSION['historique'] = [];
    
    // Rediriger pour éviter la réinitialisation au rechargement
    if (isset($_GET['nouvelle_partie'])) {
        header('Location: bataille_interactive.php');
        exit;
    }
}

// Récupérer les données de la session
$couleurAtout = $_SESSION['couleur_atout'];
Carte::setCouleurAtout($couleurAtout);

// Variable pour stocker le résultat du tour
$resultat = null;

// Traiter l'action de jouer si un formulaire a été soumis
if (isset($_POST['carte_choisie'])) {
    // Réindexer la session pour correspondre aux indices du formulaire
    $_SESSION['jeu_joueur1'] = array_values($_SESSION['jeu_joueur1']);
    $_SESSION['jeu_joueur2'] = array_values($_SESSION['jeu_joueur2']);
    
    // Restaurer le contrôleur depuis la session
    $jeuJoueur1 = BatailleController::tableauxVersCartes($_SESSION['jeu_joueur1']);
    $jeuJoueur2 = BatailleController::tableauxVersCartes($_SESSION['jeu_joueur2']);
    
    $controller = new BatailleController();
    $controller->melangerJeu();
    $controller->distribuerJeu();
    $controller->setJeuJoueur1($jeuJoueur1);
    $controller->setJeuJoueur2($jeuJoueur2);
    $controller->setScores($_SESSION['score_joueur1'], $_SESSION['score_joueur2']);
    $controller->setHistorique($_SESSION['historique']);
    
    $indiceCarte = intval($_POST['carte_choisie']);
    $tourActuel = $_SESSION['tour_actuel'];
    
    // Vérifier que l'indice est valide
    if (!isset($_SESSION['jeu_joueur1'][$indiceCarte])) {
        echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Erreur</title>";
        echo "<link rel='stylesheet' href='vue/style.css'>";
        echo "</head><body><div class='container'>";
        echo "<h1>⚠️ Erreur</h1>";
        echo "<p>L'indice de la carte sélectionnée n'est plus valide.</p>";
        echo "<p><a href='?nouvelle_partie=1'><button>🔄 Nouvelle partie</button></a></p>";
        echo "</div></body></html>";
        exit;
    }
    
    try {
        $resultat = $controller->jouerTourInteractif($indiceCarte, $tourActuel);
    } catch (Exception $e) {
        echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Erreur</title>";
        echo "<link rel='stylesheet' href='vue/style.css'>";
        echo "</head><body><div class='container'>";
        echo "<h1>⚠️ Erreur</h1>";
        echo "<p style='color: red;'>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><a href='?nouvelle_partie=1'><button>🔄 Nouvelle partie</button></a></p>";
        echo "</div></body></html>";
        exit;
    }
    
    // Convertir l'historique pour la session
    $historiqueSession = [];
    foreach ($controller->getHistoriqueTours() as $tour) {
        $carte1 = is_array($tour['carteJoueur1']) ? $tour['carteJoueur1'] : ['couleur' => $tour['carteJoueur1']->getCouleur(), 'figure' => $tour['carteJoueur1']->getFigure()];
        $carte2 = is_array($tour['carteJoueur2']) ? $tour['carteJoueur2'] : ['couleur' => $tour['carteJoueur2']->getCouleur(), 'figure' => $tour['carteJoueur2']->getFigure()];
        $historiqueSession[] = [
            'tour' => $tour['tour'],
            'carteJoueur1' => $carte1,
            'carteJoueur2' => $carte2,
            'resultat' => $tour['resultat']
        ];
    }
    
    // Mettre à jour la session
    $_SESSION['jeu_joueur1'] = array_values(BatailleController::cartesVersTableaux($controller->getJeuJoueur1()));
    $_SESSION['jeu_joueur2'] = array_values(BatailleController::cartesVersTableaux($controller->getJeuJoueur2()));
    $_SESSION['score_joueur1'] = $controller->getScoreJoueur1();
    $_SESSION['score_joueur2'] = $controller->getScoreJoueur2();
    $_SESSION['tour_actuel']++;
    $_SESSION['historique'] = $historiqueSession;
}

// Affichage de l'interface
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>🎮 Bataille Interactive</title>
    <link rel='stylesheet' href='vue/style.css'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
</head>
<body>
    <div class='container'>
        <div style='display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;'>
            <h1 style='margin: 0;'>🎮 Jeu de Bataille Interactive</h1>
            <a href='?nouvelle_partie=1'><button style='background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); margin: 0;'>🔄 Recommencer</button></a>
        </div>
        
        <div class='atout-info'>Couleur atout : <?php echo $couleurAtout; ?></div>
        
        <div class='scores'>
            <div class='score-box player1'>
                <h2>👤 Vous</h2>
                <div class='score'><?php echo $_SESSION['score_joueur1']; ?></div>
                <p>points</p>
            </div>
            <div class='score-box player2'>
                <h2>🤖 Ordinateur</h2>
                <div class='score'><?php echo $_SESSION['score_joueur2']; ?></div>
                <p>points</p>
            </div>
        </div>
        
        <?php
        // Si on vient de jouer un tour, afficher le résultat
        if ($resultat !== null) {
            $carte1 = $resultat['carteJoueur1'];
            $carte2 = $resultat['carteJoueur2'];
            $res = $resultat['resultat'];
            
            echo "<div class='resultat-tour'>";
            echo "<h2>⚔️ Résultat du Tour " . ($_SESSION['tour_actuel'] - 1) . "</h2>";
            
            echo "<div class='cartes-jouees'>";
            echo "<div class='carte-jouee'>";
            echo "<h3>👤 Votre carte</h3>";
            echo "<p><strong>" . $carte1->getNom() . "</strong></p>";
            echo "<p>Valeur: <strong>" . $carte1->getValeur() . "</strong></p>";
            if ($carte1->isAtout()) {
                echo "<p><span class='carte-badge'>ATOUT</span></p>";
            }
            echo "</div>";
            
            echo "<div class='carte-jouee'>";
            echo "<h3>🤖 Carte ordinateur</h3>";
            echo "<p><strong>" . $carte2->getNom() . "</strong></p>";
            echo "<p>Valeur: <strong>" . $carte2->getValeur() . "</strong></p>";
            if ($carte2->isAtout()) {
                echo "<p><span class='carte-badge'>ATOUT</span></p>";
            }
            echo "</div>";
            echo "</div>";
            
            if ($res['gagnant'] == 1) {
                echo "<div class='resultat-message gagnant'>🏆 Vous gagnez ce tour ! (+" . $res['points'] . " points) 🏆</div>";
            } elseif ($res['gagnant'] == 2) {
                echo "<div class='resultat-message perdant'>😔 L'ordinateur gagne ce tour ! (+" . $res['points'] . " points)</div>";
            } else {
                echo "<div class='resultat-message egalite'>🤝 Égalité ! Aucun point gagné.</div>";
            }
            echo "</div>";
        }
        
        // Vérifier si la partie est terminée
        if (count($_SESSION['jeu_joueur1']) == 0 || count($_SESSION['jeu_joueur2']) == 0) {
            $controller = new BatailleController();
            $controller->setScores($_SESSION['score_joueur1'], $_SESSION['score_joueur2']);
            $gagnant = $controller->getGagnant();
            
            echo "<div class='partie-terminee'>";
            echo "<h1>🎉 Partie terminée ! 🎉</h1>";
            if ($gagnant == 1) {
                echo "<h2 style='color: #4facfe; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);'>🏆 Félicitations ! Vous avez gagné ! 🏆</h2>";
            } elseif ($gagnant == 2) {
                echo "<h2 style='color: #fa709a; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);'>😔 L'ordinateur a gagné cette fois...</h2>";
            } else {
                echo "<h2 style='color: #fddb92; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);'>🤝 Match nul ! 🤝</h2>";
            }
            echo "<p><a href='?nouvelle_partie=1'><button>🔄 Nouvelle partie</button></a></p>";
            echo "</div>";
            session_destroy();
        } else {
            // Afficher le formulaire de choix de carte
            echo "<div class='tour-header'>";
            echo "<h2>🎯 Tour " . $_SESSION['tour_actuel'] . "</h2>";
            echo "<p class='cartes-restantes'>Cartes restantes : " . count($_SESSION['jeu_joueur1']) . " 🃏</p>";
            echo "</div>";
            
            echo "<form method='POST' action=''>";
            echo "<div class='cartes-container'>";
            foreach ($_SESSION['jeu_joueur1'] as $index => $carteTab) {
                $carte = new Carte($carteTab['couleur'], $carteTab['figure']);
                $isAtout = $carte->isAtout() ? ' carte-atout' : '';
                echo "<label class='carte-label'>";
                echo "<input type='radio' name='carte_choisie' value='$index' required>";
                echo "<div class='carte$isAtout'>";
                echo "<div class='carte-nom'>" . $carte->getFigure() . "</div>";
                echo "<div class='carte-valeur'>" . $carte->getCouleur() . "</div>";
                echo "<div style='font-size: 0.9em; color: #666;'>Valeur: " . $carte->getValeur() . "</div>";
                if ($carte->isAtout()) {
                    echo "<div class='carte-badge'>ATOUT</div>";
                }
                echo "</div>";
                echo "</label>";
            }
            echo "</div>";
            echo "<button type='submit' style='display: block; margin: 30px auto;'>🎲 Jouer cette carte</button>";
            echo "</form>";
        }
        ?>
    </div>
</body>
</html>

