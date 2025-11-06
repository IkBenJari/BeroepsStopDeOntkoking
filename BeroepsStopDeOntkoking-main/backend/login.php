<?php
require_once __DIR__ . '/functions_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';
    if (login($u, $p)) {
        header('Location: admin/index.php');
        exit;
    } else {
        $error = 'Login mislukt';
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen - Stop De Ontkoking</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="user">
                <img src="../images/unknown-user.png" alt="User pictogram">
                <p>Gast</p>
            </div>
            <a href="../index.html">Home</a>
            <div class="search-container">
                <input type="text" placeholder="Zoek recepten..." class="search-input" disabled>
                <button class="search-button" disabled>Zoek</button>
            </div>
            <a href="../pages/ontbijt.html">Recepten</a>
            <a href="login.php" class="admin-login-btn active">Inloggen / Beheer</a>
        </nav>
    </header>

    <main>
        <div class="login-container">
            <div class="login-form">
                <h1>Beheerder Inloggen</h1>
                <p>Log in om recepten en gebruikers te beheren</p>
                
                <?php if (!empty($error)): ?>
                    <div class="error-message">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <form method="post">
                    <div class="form-group">
                        <label for="username">Gebruikersnaam</label>
                        <input type="text" id="username" name="username" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Wachtwoord</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="login-btn">Inloggen</button>
                </form>
                
                <div class="login-info">
                    <p>Toegang alleen voor beheerders van Stop De Ontkoking</p>
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
                <h3>Links</h3>
                <a href="../index.html">Home</a>
                <a href="../pages/ontbijt.html">Recepten</a>
                <a href="login.php">Beheer</a>
            </div>
            <div class="footer-section">
                <h3>Categorieën</h3>
                <a href="../pages/ontbijt.html">Ontbijt</a>
                <a href="../pages/drankjes.html">Drankjes</a>
                <a href="../pages/snacks.html">Snacks</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 GLR Food Freaks - Team OJN. Alle rechten voorbehouden.</p>
        </div>
    </footer>
</body>
</html>