<?php
require_once __DIR__ . '/../modele/class.carte.inc.php';

class BatailleController {
    private $couleurs;
    private $figures;
    private $tbCartes;
    private $jeuJoueur1;
    private $jeuJoueur2;
    private $scoreJoueur1;
    private $scoreJoueur2;
    private $historiqueTours;

    public function __construct() {
        // 3. Déclarez et initialisez un tableau avec les 4 couleurs
        $this->couleurs = ['Cœur♥️​', 'Carreau♦️', 'Pique♠️', 'Trèfle♣️'];
        
        // 4. Déclarez et initialisez un tableau avec les 13 figures
        $this->figures = ['As', 'Roi', 'Dame', 'Valet', '10', '9', '8', '7', '6', '5', '4', '3', '2'];
        
        // 5. Déclarez un tableau, nommé tbCartes, qui représentera le jeu de 52 cartes
        $this->tbCartes = [];
        
        // 6. Initialisez le jeu de cartes en utilisant les tableaux de figures et de couleurs
        $this->initialiserJeu();
        
        // Initialisation des scores
        $this->scoreJoueur1 = 0;
        $this->scoreJoueur2 = 0;
        $this->historiqueTours = [];
    }

    /**
     * Initialise le jeu de 52 cartes
     */
    private function initialiserJeu() {
        foreach ($this->couleurs as $couleur) {
            foreach ($this->figures as $figure) {
                $this->tbCartes[] = new Carte($couleur, $figure);
            }
        }
    }

    /**
     * Retourne le jeu de cartes
     * @return array Tableau d'objets Carte
     */
    public function getJeu() {
        return $this->tbCartes;
    }

    /**
     * Retourne les couleurs
     * @return array Tableau des couleurs
     */
    public function getCouleurs() {
        return $this->couleurs;
    }

    /**
     * Retourne les figures
     * @return array Tableau des figures
     */
    public function getFigures() {
        return $this->figures;
    }

    /**
     * Mélange le jeu de cartes
     * Principe : On parcourt le jeu. Pour chaque indice on tire une position au hasard
     * (un nombre entre 0 et indice dernière cellule) et on échange les deux cartes.
     */
    public function melangerJeu() {
        $nbCartes = count($this->tbCartes);
        
        // On parcourt le jeu
        for ($i = 0; $i < $nbCartes; $i++) {
            // Pour chaque indice on tire une position au hasard entre 0 et l'indice de la dernière cellule
            $indHasard = mt_rand(0, $nbCartes - 1);
            
            // On échange les deux cartes
            $temp = $this->tbCartes[$i];
            $this->tbCartes[$i] = $this->tbCartes[$indHasard];
            $this->tbCartes[$indHasard] = $temp;
        }
    }

    /**
     * Définit la couleur atout au hasard
     */
    public function definirCouleurAtoutAuHasard() {
        $nbCouleurs = count($this->couleurs);
        $indiceHasard = mt_rand(0, $nbCouleurs - 1);
        $couleurAtout = $this->couleurs[$indiceHasard];
        Carte::setCouleurAtout($couleurAtout);
        return $couleurAtout;
    }

    /**
     * Distribue le jeu à 2 joueurs (2 tableaux de 26 cartes)
     */
    public function distribuerJeu() {
        $this->jeuJoueur1 = [];
        $this->jeuJoueur2 = [];
        
        $nbCartes = count($this->tbCartes);
        
        for ($i = 0; $i < $nbCartes; $i++) {
            if ($i % 2 == 0) {
                // Cartes paires au joueur 1
                $this->jeuJoueur1[] = $this->tbCartes[$i];
            } else {
                // Cartes impaires au joueur 2
                $this->jeuJoueur2[] = $this->tbCartes[$i];
            }
        }
    }

    /**
     * Définit le jeu du joueur 1 (pour restauration de session)
     * @param array $jeu Tableau d'objets Carte
     */
    public function setJeuJoueur1($jeu) {
        $this->jeuJoueur1 = $jeu;
    }

    /**
     * Définit le jeu du joueur 2 (pour restauration de session)
     * @param array $jeu Tableau d'objets Carte
     */
    public function setJeuJoueur2($jeu) {
        $this->jeuJoueur2 = $jeu;
    }

    /**
     * Définit les scores (pour restauration de session)
     * @param int $score1 Score du joueur 1
     * @param int $score2 Score du joueur 2
     */
    public function setScores($score1, $score2) {
        $this->scoreJoueur1 = $score1;
        $this->scoreJoueur2 = $score2;
    }

    /**
     * Définit l'historique (pour restauration de session)
     * @param array $historique Historique des tours
     */
    public function setHistorique($historique) {
        $this->historiqueTours = $historique;
    }

    /**
     * Retourne le jeu du joueur 1
     * @return array Tableau d'objets Carte
     */
    public function getJeuJoueur1() {
        return $this->jeuJoueur1;
    }

    /**
     * Retourne le jeu du joueur 2
     * @return array Tableau d'objets Carte
     */
    public function getJeuJoueur2() {
        return $this->jeuJoueur2;
    }

    /**
     * Joue un tour en comparant 2 cartes
     * @param Carte $carteJoueur1 Carte du joueur 1
     * @param Carte $carteJoueur2 Carte du joueur 2
     * @return array Tableau avec le gagnant (1, 2, ou 0 pour égalité) et les points gagnés
     */
    public function jouerTour($carteJoueur1, $carteJoueur2) {
        $valeur1 = $carteJoueur1->getValeur();
        $valeur2 = $carteJoueur2->getValeur();
        $atout1 = $carteJoueur1->isAtout();
        $atout2 = $carteJoueur2->isAtout();
        $sommeValeurs = $valeur1 + $valeur2;
        
        // Si l'un seulement des 2 joueurs a une carte de la couleur atout, c'est lui qui remporte la somme
        if ($atout1 && !$atout2) {
            $this->scoreJoueur1 += $sommeValeurs;
            return ['gagnant' => 1, 'points' => $sommeValeurs, 'raison' => 'atout'];
        } elseif ($atout2 && !$atout1) {
            $this->scoreJoueur2 += $sommeValeurs;
            return ['gagnant' => 2, 'points' => $sommeValeurs, 'raison' => 'atout'];
        }
        
        // Sinon, c'est le joueur dont la carte a le plus de valeur qui emporte la somme
        if ($valeur1 > $valeur2) {
            $this->scoreJoueur1 += $sommeValeurs;
            return ['gagnant' => 1, 'points' => $sommeValeurs, 'raison' => 'valeur'];
        } elseif ($valeur2 > $valeur1) {
            $this->scoreJoueur2 += $sommeValeurs;
            return ['gagnant' => 2, 'points' => $sommeValeurs, 'raison' => 'valeur'];
        }
        
        // En cas d'égalité des valeurs, aucun des 2 joueurs ne récupère les points
        return ['gagnant' => 0, 'points' => 0, 'raison' => 'égalité'];
    }

    /**
     * Joue toute la partie (26 tours)
     * @return array Historique des tours et scores finaux
     */
    public function jouerPartie() {
        $this->scoreJoueur1 = 0;
        $this->scoreJoueur2 = 0;
        $this->historiqueTours = [];
        
        $nbCartes = count($this->jeuJoueur1);
        
        for ($i = 0; $i < $nbCartes; $i++) {
            $carte1 = $this->jeuJoueur1[$i];
            $carte2 = $this->jeuJoueur2[$i];
            
            $resultat = $this->jouerTour($carte1, $carte2);
            
            $this->historiqueTours[] = [
                'tour' => $i + 1,
                'carteJoueur1' => $carte1,
                'carteJoueur2' => $carte2,
                'resultat' => $resultat
            ];
        }
        
        return [
            'scoreJoueur1' => $this->scoreJoueur1,
            'scoreJoueur2' => $this->scoreJoueur2,
            'historique' => $this->historiqueTours
        ];
    }

    /**
     * Retourne le score du joueur 1
     * @return int Score du joueur 1
     */
    public function getScoreJoueur1() {
        return $this->scoreJoueur1;
    }

    /**
     * Retourne le score du joueur 2
     * @return int Score du joueur 2
     */
    public function getScoreJoueur2() {
        return $this->scoreJoueur2;
    }

    /**
     * Retourne le gagnant de la partie
     * @return int 1 pour joueur 1, 2 pour joueur 2, 0 pour égalité
     */
    public function getGagnant() {
        if ($this->scoreJoueur1 > $this->scoreJoueur2) {
            return 1;
        } elseif ($this->scoreJoueur2 > $this->scoreJoueur1) {
            return 2;
        }
        return 0; // Égalité
    }

    /**
     * Retourne l'historique des tours
     * @return array Historique des tours
     */
    public function getHistoriqueTours() {
        return $this->historiqueTours;
    }

    /**
     * Joue un tour interactif avec une carte choisie par le joueur
     * @param int $indiceCarteJoueur1 Indice de la carte choisie par le joueur 1
     * @param int $tourActuel Numéro du tour actuel
     * @return array Résultat du tour
     */
    public function jouerTourInteractif($indiceCarteJoueur1, $tourActuel) {
        // Vérifier que l'indice est valide
        if (!isset($this->jeuJoueur1[$indiceCarteJoueur1])) {
            throw new Exception("Indice de carte invalide : $indiceCarteJoueur1. Nombre de cartes disponibles : " . count($this->jeuJoueur1));
        }
        
        // Récupérer la carte choisie par le joueur 1
        $carteJoueur1 = $this->jeuJoueur1[$indiceCarteJoueur1];
        
        // Vérifier que la carte n'est pas null
        if ($carteJoueur1 === null) {
            throw new Exception("La carte à l'indice $indiceCarteJoueur1 est null");
        }
        
        // Le joueur 2 (ordinateur) joue automatiquement une carte au hasard
        $nbCartesJoueur2 = count($this->jeuJoueur2);
        if ($nbCartesJoueur2 == 0) {
            throw new Exception("Le joueur 2 n'a plus de cartes");
        }
        $indiceCarteJoueur2 = mt_rand(0, $nbCartesJoueur2 - 1);
        $carteJoueur2 = $this->jeuJoueur2[$indiceCarteJoueur2];
        
        // Vérifier que la carte du joueur 2 n'est pas null
        if ($carteJoueur2 === null) {
            throw new Exception("La carte du joueur 2 à l'indice $indiceCarteJoueur2 est null");
        }
        
        // Jouer le tour
        $resultat = $this->jouerTour($carteJoueur1, $carteJoueur2);
        
        // Retirer les cartes jouées des jeux et réindexer les tableaux
        array_splice($this->jeuJoueur1, $indiceCarteJoueur1, 1);
        array_splice($this->jeuJoueur2, $indiceCarteJoueur2, 1);
        
        // Réindexer les tableaux pour éviter les problèmes d'indices
        $this->jeuJoueur1 = array_values($this->jeuJoueur1);
        $this->jeuJoueur2 = array_values($this->jeuJoueur2);
        
        // Ajouter au historique
        $this->historiqueTours[] = [
            'tour' => $tourActuel,
            'carteJoueur1' => $carteJoueur1,
            'carteJoueur2' => $carteJoueur2,
            'resultat' => $resultat
        ];
        
        return [
            'carteJoueur1' => $carteJoueur1,
            'carteJoueur2' => $carteJoueur2,
            'resultat' => $resultat,
            'jeuJoueur1' => $this->jeuJoueur1,
            'jeuJoueur2' => $this->jeuJoueur2
        ];
    }

    /**
     * Vérifie si la partie est terminée
     * @return bool True si la partie est terminée
     */
    public function estPartieTerminee() {
        return count($this->jeuJoueur1) == 0 || count($this->jeuJoueur2) == 0;
    }

    /**
     * Convertit les cartes en tableaux pour la session
     * @param array $cartes Tableau d'objets Carte
     * @return array Tableau de tableaux représentant les cartes
     */
    public static function cartesVersTableaux($cartes) {
        $resultat = [];
        foreach ($cartes as $carte) {
            $resultat[] = [
                'couleur' => $carte->getCouleur(),
                'figure' => $carte->getFigure()
            ];
        }
        return $resultat;
    }

    /**
     * Reconstruit les cartes à partir de tableaux
     * @param array $tableaux Tableau de tableaux représentant les cartes
     * @return array Tableau d'objets Carte
     */
    public static function tableauxVersCartes($tableaux) {
        $resultat = [];
        foreach ($tableaux as $tab) {
            $resultat[] = new Carte($tab['couleur'], $tab['figure']);
        }
        return $resultat;
    }

    /**
     * Recommencer la partie
     */
    public function recommencerPartie() {
        $this->scoreJoueur1 = 0;
        $this->scoreJoueur2 = 0;
        $this->historiqueTours = [];
        $this->initialiserJeu();
        $this->melangerJeu();
        $this->definirCouleurAtoutAuHasard();
        $this->distribuerJeu();
    }
}
?>

