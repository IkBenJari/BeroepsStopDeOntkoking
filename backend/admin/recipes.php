<?php
require_once __DIR__ . '/../functions_auth.php';
require_once __DIR__ . '/../db.php';
require_admin();

$stmt = $pdo->query('SELECT r.id, r.title, r.created_at, u.username FROM recipes r JOIN users u ON r.created_by = u.id ORDER BY r.created_at DESC');
$rows = $stmt->fetchAll();
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recepten</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .nav-links { margin-bottom: 20px; }
        .nav-links a { color: #2c5530; text-decoration: none; margin-right: 15px; }
        .nav-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="nav-links">
        <a href="index.php">← Terug naar Admin</a>
        <a href="../../index.html">Publieke Site</a>
        <a href="../logout.php">Uitloggen</a>
    </div>
    
    <h1>Recepten beheren</h1>
    <p><a href="../recipes_create.php">Nieuw recept toevoegen</a></p>
    
    <table>
        <tr>
            <th>ID</th>
            <th>Titel</th>
            <th>Auteur</th>
            <th>Categorie</th>
            <th>Datum</th>
            <th>Acties</th>
        </tr>
        <?php foreach($rows as $r): ?>
        <tr>
            <td><?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['title']) ?></td>
            <td><?= htmlspecialchars($r['username']) ?></td>
            <td><?= htmlspecialchars($r['category'] ?? 'Onbekend') ?></td>
            <td><?= $r['created_at'] ?></td>
            <td>
                <a href="edit_recipe.php?id=<?= $r['id'] ?>">Bewerk</a> |
                <a href="delete_recipe.php?id=<?= $r['id'] ?>" onclick="return confirm('Weet je zeker dat je dit recept wilt verwijderen?')">Verwijder</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    
    <?php if (empty($rows)): ?>
        <p><em>Nog geen recepten toegevoegd.</em></p>
    <?php endif; ?>
</body>
</html>