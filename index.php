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
        echo "</head><body><div class='game-layout'><div class='main-area'><div class='container'>";
        echo "<h1>ERREUR</h1>";
        echo "<p class='error-text'>CARTE INVALIDE</p>";
        echo "<p><a href='?nouvelle_partie=1'><button>NOUVELLE PARTIE</button></a></p>";
        echo "</div></div></div></body></html>";
        exit;
    }
    
    try {
        $resultat = $controller->jouerTourInteractif($indiceCarte, $tourActuel);
    } catch (Exception $e) {
        echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Erreur</title>";
        echo "<link rel='stylesheet' href='vue/style.css'>";
        echo "</head><body><div class='game-layout'><div class='main-area'><div class='container'>";
        echo "<h1>ERREUR</h1>";
        echo "<p class='error-text'>" . htmlspecialchars($e->getMessage()) . "</p>";
        echo "<p><a href='?nouvelle_partie=1'><button>NOUVELLE PARTIE</button></a></p>";
        echo "</div></div></div></body></html>";
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
    <title>BATAILLE INTERACTIVE</title>
    <link rel='stylesheet' href='vue/style.css'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
</head>
<body>
    <div class='game-layout'>
        <!-- Panneau latéral gauche -->
        <div class='sidebar'>
            <!-- En-tête Big Blind -->
            <div class='big-blind-header'>
                <div class='big-blind-title'>Bataille</div>
                <div class='big-blind-badge'>
                    <div class='badge-icon'>⚔️</div>
                </div>
                <div class='big-blind-info'>
                    <div class='blind-label'>Couleur atout</div>
                    <div class='blind-value'><?php echo strtoupper($couleurAtout); ?></div>
                </div>
            </div>
            
            <!-- Scores des joueurs -->
            <div class='player-scores'>
                <div class='score-left'>
                    <div class='score-big'><?php echo $_SESSION['score_joueur1']; ?></div>
                </div>
                <div class='vs-symbol'>✕</div>
                <div class='score-right'>
                    <div class='score-big'><?php echo $_SESSION['score_joueur2']; ?></div>
                </div>
            </div>
            
            <!-- Stats -->
            <div class='stats-box'>
                <div class='stat-item'>
                    <div class='stat-label'>Mains</div>
                    <div class='stat-value'><?php echo count($_SESSION['jeu_joueur1']); ?></div>
                </div>
                <div class='stat-item'>
                    <div class='stat-label'>Tours</div>
                    <div class='stat-value'><?php echo $_SESSION['tour_actuel']; ?></div>
                </div>
            </div>
            
            <!-- Total en or -->
            <div class='gold-total'>
                <div class='gold-symbol'>$</div>
                <div class='gold-amount'><?php echo $_SESSION['score_joueur1'] + $_SESSION['score_joueur2']; ?></div>
            </div>
            
            <!-- Ante et Round -->
            <div class='ante-round-box'>
                <div class='ante-item'>
                    <div class='ante-label'>Ante</div>
                    <div class='ante-value'><?php echo count($_SESSION['jeu_joueur1']); ?>/<?php echo count($_SESSION['jeu_joueur1']) + count($_SESSION['jeu_joueur2']); ?></div>
                </div>
                <div class='ante-item'>
                    <div class='ante-label'>Round</div>
                    <div class='ante-value'><?php echo $_SESSION['tour_actuel']; ?></div>
                </div>
            </div>
            
            <!-- Boutons -->
            <div class='sidebar-buttons'>
                <a href='?nouvelle_partie=1'><button class='pixel-btn pixel-btn-red'>Run Info</button></a>
                <button class='pixel-btn pixel-btn-orange'>Options</button>
            </div>
        </div>
        
        <!-- Zone de jeu principale -->
        <div class='main-area'>
            
            <div class='container'>
        
        <?php
        // Si on vient de jouer un tour, afficher le résultat
        if ($resultat !== null) {
            $carte1 = $resultat['carteJoueur1'];
            $carte2 = $resultat['carteJoueur2'];
            $res = $resultat['resultat'];
            
            echo "<div class='resultat-tour'>";
            
            // Afficher le score gagné en grand
            if ($res['points'] > 0) {
                echo "<div class='points-popup'>+" . $res['points'] . "</div>";
            }
            
            echo "<h2>RESULTAT TOUR " . ($_SESSION['tour_actuel'] - 1) . "</h2>";
            
            echo "<div class='played-cards'>";
            
            echo "<figure class='played-card'>";
            echo "<figcaption class='played-card-title'>JOUEUR</figcaption>";
            $imageCarte1 = ObtenirImageCarte($carte1);
                echo "<img src='$imageCarte1' alt='" . $carte1->getNom() . "' class='played-card-image'>";
            if ($carte1->isAtout()) {
                echo "<span class='played-card-badge'>*</span>";
            }
            echo "</figure>";
            
            echo "<figure class='played-card'>";
            echo "<figcaption class='played-card-title'>ORDI</figcaption>";
            $imageCarte2 = ObtenirImageCarte($carte2);
            echo "<img src='$imageCarte2' alt='" . $carte2->getNom() . "' class='played-card-image'>";
            if ($carte2->isAtout()) {
                echo "<span class='played-card-badge'>*</span>";
            }
            echo "</figure>";
            
            echo "</div>";
            
            if ($res['gagnant'] == 1) {
                echo "<div class='resultat-message gagnant'>VICTOIRE! +" . $res['points'] . " POINTS</div>";
            } elseif ($res['gagnant'] == 2) {
                echo "<div class='resultat-message perdant'>DEFAITE! +" . $res['points'] . " POINTS</div>";
            } else {
                echo "<div class='resultat-message egalite'>EGALITE! 0 POINTS</div>";
            }
            echo "</div>";
        }
        
        // Vérifier si la partie est terminée
        if (count($_SESSION['jeu_joueur1']) == 0 || count($_SESSION['jeu_joueur2']) == 0) {
            $controller = new BatailleController();
            $controller->setScores($_SESSION['score_joueur1'], $_SESSION['score_joueur2']);
            $gagnant = $controller->getGagnant();
            
            echo "<div class='partie-terminee'>";
            echo "<h1>PARTIE TERMINEE!</h1>";
            if ($gagnant == 1) {
                echo "<h2 class='winner-text'>VICTOIRE!</h2>";
            } elseif ($gagnant == 2) {
                echo "<h2 class='loser-text'>DEFAITE!</h2>";
            } else {
                echo "<h2 class='draw-text'>EGALITE!</h2>";
            }
            echo "<p><a href='?nouvelle_partie=1'><button>NOUVELLE PARTIE</button></a></p>";
            echo "</div>";
            session_destroy();
        } else {
            
            echo "<form method='POST' action='' class='card-select-form'>";
            foreach ($_SESSION['jeu_joueur1'] as $index => $carteTab) {
                $carte = new Carte($carteTab['couleur'], $carteTab['figure']);
                $imageCarte = ObtenirImageCarte($carte);
                $isAtout = $carte->isAtout() ? ' carte-atout' : '';
                echo "<label class='card-choice'>";
                echo "<input type='radio' name='carte_choisie' value='$index' required class='card-choice-input'>";
                echo "<img src='$imageCarte' alt='" . $carte->getNom() . "' class='card-choice-image$isAtout'>";
                if ($carte->isAtout()) {
                    echo "<span class='card-choice-badge'>*</span>";
                }
                echo "</label>";
            }
            echo "<button type='submit' class='btn-play'>JOUER</button>";
            echo "</form>";
        }
        ?>
            </div>
        </div>
    </div>
</body>
</html>

