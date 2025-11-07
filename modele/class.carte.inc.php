<?php
class Carte {
    private $couleur;
    private $figure;
    private static $couleurAtout = null;

    public function __construct($couleur, $figure) {
        $this->couleur = $couleur;
        $this->figure = $figure;
    }

    /**
     * Définit la couleur atout
     * @param string $couleur La couleur à définir comme atout
     */
    public static function setCouleurAtout($couleur) {
        self::$couleurAtout = $couleur;
    }

    /**
     * Retourne la couleur atout actuelle
     * @return string|null La couleur atout
     */
    public static function getCouleurAtout() {
        return self::$couleurAtout;
    }

    public function getCouleur() {
        return $this->couleur;
    }

    public function getFigure() {
        return $this->figure;
    }

    public function getNom() {
        return $this->figure . ' de ' . $this->couleur;
    }

    public function getValeur() {
        $valeurs = [
            '2' => 2,
            '3' => 3,
            '4' => 4,
            '5' => 5,
            '6' => 6,
            '7' => 7,
            '8' => 8,
            '9' => 9,
            '10' => 10,
            'Valet' => 11,
            'Dame' => 12,
            'Roi' => 13,
            'As' => 14
        ];
        return $valeurs[$this->figure];
    }

    public function isAtout(){
        if (self::$couleurAtout === null) {
            return false;
        }
        return $this->couleur === self::$couleurAtout;
    }
}
?>