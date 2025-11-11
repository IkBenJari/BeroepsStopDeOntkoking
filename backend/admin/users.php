<?php
require_once __DIR__ . '/../functions_auth.php';
require_once __DIR__ . '/../db.php';
require_admin();

// list users
$stmt = $pdo->query('SELECT id,username,created_at FROM users ORDER BY created_at DESC');
$users = $stmt->fetchAll();
$user = current_user();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gebruikers Beheren - Stop De Ontkoking</title>
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
                <a href="index.php" class="back-btn">← Terug naar Dashboard</a>
                <h1>Gebruikers Beheren</h1>
                <p>Bekijk en beheer alle geregistreerde gebruikers van de website</p>
            </div>

            <div class="admin-content">
                <?php if (!empty($users)): ?>
                    <div class="table-container">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Gebruikersnaam</th>
                                    <th>Aangemaakt</th>
                                    <th>Acties</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($users as $u): ?>
                                <tr>
                                    <td><?= $u['id'] ?></td>
                                    <td><?= htmlspecialchars($u['username']) ?></td>
                                    <td><?= date('d-m-Y H:i', strtotime($u['created_at'])) ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="edit_user.php?id=<?= $u['id'] ?>" class="btn-edit">Bewerk</a>
                                            <a href="delete_user.php?id=<?= $u['id'] ?>" class="btn-delete" onclick="return confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?')">Verwijder</a>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">👥</div>
                        <h3>Geen gebruikers gevonden</h3>
                        <p>Er zijn nog geen gebruikers geregistreerd op de website.</p>
                    </div>
                <?php endif; ?>
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
                <a href="index.php">Dashboard</a>
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