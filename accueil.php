<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <title>BATAILLE - Accueil</title>
    <link rel='stylesheet' href='vue/style-accueil.css'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
</head>
<body>
    <div class='accueil-container'>
        <!-- En-tête -->
        <header class='header'>
            <div class='title-container'>
                <h1 class='main-title'>🎴 BATAILLE</h1>
                <p class='subtitle'>Jeu de cartes classique</p>
            </div>
        </header>

        <!-- Contenu principal -->
        <main class='main-content'>
            <!-- Section de présentation -->
            <section class='intro-section'>
                <div class='intro-card'>
                    <h2>Bienvenue !</h2>
                    <p class='intro-text'>
                        Affrontez l'ordinateur dans une partie de bataille épique !<br>
                        Choisissez vos cartes avec stratégie et remportez la victoire.
                    </p>
                </div>
            </section>

            <!-- Section des règles -->
            <section class='rules-section'>
                <div class='rules-card'>
                    <h3>📖 Règles du jeu</h3>
                    <ul class='rules-list'>
                        <li>Chaque joueur reçoit la moitié du jeu de cartes</li>
                        <li>À chaque tour, choisissez une carte à jouer</li>
                        <li>La carte la plus forte remporte le tour</li>
                        <li>Les cartes de la couleur atout sont plus fortes</li>
                        <li>Le joueur avec le plus de points gagne !</li>
                    </ul>
                </div>
            </section>

            <!-- Section d'action -->
            <section class='action-section'>
                <div class='action-card'>
                    <h3>🚀 Prêt à jouer ?</h3>
                    <p class='action-text'>Lancez une nouvelle partie et testez vos compétences !</p>
                    <a href='index.php' class='btn-start'>
                        <span class='btn-text'>NOUVELLE PARTIE</span>
                        <span class='btn-icon'>▶</span>
                    </a>
                </div>
            </section>

            <!-- Section statistiques (optionnelle) -->
            <section class='stats-section'>
                <div class='stats-grid'>
                    <div class='stat-item'>
                        <div class='stat-icon'>🎯</div>
                        <div class='stat-value'>52</div>
                        <div class='stat-label'>Cartes</div>
                    </div>
                    <div class='stat-item'>
                        <div class='stat-icon'>⚔️</div>
                        <div class='stat-value'>26</div>
                        <div class='stat-label'>Tours</div>
                    </div>
                    <div class='stat-item'>
                        <div class='stat-icon'>🏆</div>
                        <div class='stat-value'>∞</div>
                        <div class='stat-label'>Parties</div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class='footer'>
            <p class='footer-text'>© 2024 Bataille - Jeu de cartes</p>
        </footer>
    </div>
</body>
</html>

