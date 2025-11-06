<?php
require_once __DIR__ . '/../functions_auth.php';
require_admin();
$user = current_user();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Stop De Ontkoking</title>
    <link rel="stylesheet" href="../../styles/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="user">
                <img src="../../images/unknown-user.png" alt="User pictogram">
                <p><?php echo htmlspecialchars($user['username']); ?></p>
            </div>
            <a href="../../index.html">Home</a>
            <div class="search-container">
                <input type="text" placeholder="Zoek recepten..." class="search-input" disabled>
                <button class="search-button" disabled>Zoek</button>
            </div>
            <a href="../../pages/ontbijt.html">Recepten</a>
            <a href="../logout.php" class="admin-login-btn">Uitloggen</a>
        </nav>
    </header>

    <main>
        <div class="admin-container">
            <div class="admin-header">
                <h1>Admin Dashboard</h1>
                <p>Welkom, <?php echo htmlspecialchars($user['username']); ?>! Beheer hier de website content.</p>
            </div>

            <div class="admin-grid">
                <div class="admin-card">
                    <div class="admin-card-icon">👥</div>
                    <h3>Gebruikers Beheren</h3>
                    <p>Bekijk en beheer alle geregistreerde gebruikers</p>
                    <a href="users.php" class="admin-btn">Ga naar Gebruikers</a>
                </div>

                <div class="admin-card">
                    <div class="admin-card-icon">🍽️</div>
                    <h3>Recepten Beheren</h3>
                    <p>Voeg recepten toe, bewerk of verwijder bestaande recepten</p>
                    <a href="recipes.php" class="admin-btn">Ga naar Recepten</a>
                </div>

                <div class="admin-card">
                    <div class="admin-card-icon">💡</div>
                    <h3>Tips Beheren</h3>
                    <p>Beheer kook tips en advies voor gebruikers</p>
                    <a href="tips.php" class="admin-btn">Ga naar Tips</a>
                </div>

                <div class="admin-card">
                    <div class="admin-card-icon">⭐</div>
                    <h3>Ervaringen Beheren</h3>
                    <p>Bekijk en modereer gebruikerservaringen</p>
                    <a href="experiences.php" class="admin-btn">Ga naar Ervaringen</a>
                </div>
            </div>

            <div class="admin-stats">
                <h2>Website Statistieken</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Totaal Gebruikers</h3>
                        <div class="stat-number">-</div>
                    </div>
                    <div class="stat-card">
                        <h3>Totaal Recepten</h3>
                        <div class="stat-number">-</div>
                    </div>
                    <div class="stat-card">
                        <h3>Nieuwe Vandaag</h3>
                        <div class="stat-number">-</div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Stop De Ontkoking</h3>
                <p>Inspiring Generation Z to cook again!</p>
            </div>
            <div class="footer-section">
                <h3>Admin Links</h3>
                <a href="users.php">Gebruikers</a>
                <a href="recipes.php">Recepten</a>
                <a href="../logout.php">Uitloggen</a>
            </div>
            <div class="footer-section">
                <h3>Website</h3>
                <a href="../../index.html">Hoofdpagina</a>
                <a href="../../pages/ontbijt.html">Recepten</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 GLR Food Freaks - Team OJN. Alle rechten voorbehouden.</p>
        </div>
    </footer>
</body>
</html>