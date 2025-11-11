<?php
require_once __DIR__ . '/../backend/functions_auth.php';
$current_user = current_user();

// Redirect naar login als niet ingelogd
if (!$current_user) {
    header('Location: ../backend/login.php');
    exit;
}

// Haal gebruikers recepten op uit de database
require_once __DIR__ . '/../backend/db.php';
$stmt = $pdo->prepare('SELECT * FROM recipes WHERE created_by = :user_id ORDER BY created_at DESC');
$stmt->execute(['user_id' => $current_user['id']]);
$user_recipes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Recepten - Stop De Ontkoking</title>
    <link rel="stylesheet" href="../styles/recepten.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="user">
                <img src="../images/unknown-user.png" alt="User pictogram">
                <p><?php echo htmlspecialchars($current_user['username']); ?></p>
            </div>
            <a href="../index.php">Home</a>
            <div class="search-container">
                <input type="text" placeholder="Zoek recepten..." class="search-input">
                <button class="search-button">Zoek</button>
            </div>
            <a href="ontbijt.html">Recepten</a>
            <a href="toevoegen.php">Recept Toevoegen</a>
            <a href="mijn-recepten.php" class="active">Mijn Recepten</a>
            <a href="../backend/logout.php">Uitloggen</a>
        </nav>
    </header>

    <main>
        <div class="page-header">
            <h1>Mijn Recepten</h1>
            <p>Jouw toegevoegde recepten</p>
        </div>

        <div class="recipes-grid" id="myRecipesGrid">
            <?php if (empty($user_recipes)): ?>
                <div class="empty-state">
                    <h3>Nog geen recepten toegevoegd</h3>
                    <p>Begin met het toevoegen van je eerste recept!</p>
                    <a href="toevoegen.php">Recept Toevoegen</a>
                </div>
            <?php else: ?>
                <?php foreach ($user_recipes as $recipe): ?>
                    <?php
                    $ingredients = json_decode($recipe['ingredients'], true) ?: [];
                    $instructions = json_decode($recipe['instructions'], true) ?: [];
                    ?>
                    <div class="recipe-card">
                        <img src="../images/gezond-recept.jpg" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                        <div class="recipe-info">
                            <h3><?php echo htmlspecialchars($recipe['title']); ?></h3>
                            <p><?php echo htmlspecialchars($recipe['description']); ?></p>
                            <div class="recipe-meta">
                                <span>⏱️ <?php echo htmlspecialchars($recipe['prep_time']); ?> min</span>
                                <span>👥 <?php echo htmlspecialchars($recipe['servings']); ?> personen</span>
                                <span>📊 <?php echo htmlspecialchars($recipe['difficulty']); ?></span>
                                <span>🏷️ <?php echo htmlspecialchars($recipe['category']); ?></span>
                            </div>
                            <div class="recipe-actions">
                                <button class="view-recipe-btn" onclick="viewRecipe(<?php echo $recipe['id']; ?>)">Bekijk Recept</button>
                                <button class="delete-btn" onclick="deleteRecipe(<?php echo $recipe['id']; ?>)">Verwijderen</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <style>
        .empty-state {
            text-align: center;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            margin: 2rem auto;
            color: white;
            grid-column: 1 / -1;
        }

        .empty-state h3 {
            margin-bottom: 1rem;
            color: #fff9c4;
        }

        .empty-state a {
            display: inline-block;
            background: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
            color: white;
            text-decoration: none;
            padding: 1rem 2rem;
            border-radius: 25px;
            margin-top: 1rem;
            transition: all 0.3s ease;
            font-weight: 600;
        }

        .empty-state a:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(253, 203, 110, 0.6);
        }

        .recipe-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .delete-btn:hover {
            background: #c0392b;
            transform: translateY(-1px);
        }

        .view-recipe-btn {
            background: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .view-recipe-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(253, 203, 110, 0.4);
        }

        .recipe-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin: 0.5rem 0;
            font-size: 0.9rem;
            color: #666;
        }

        .recipe-meta span {
            background: rgba(253, 203, 110, 0.1);
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }
    </style>

    <script>
        function viewRecipe(recipeId) {
            window.location.href = 'recept-detail.php?id=' + recipeId;
        }

        function deleteRecipe(recipeId) {
            if (confirm('Weet je zeker dat je dit recept wilt verwijderen?')) {
                // AJAX call om recept te verwijderen
                fetch('../backend/recipes_delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ recipe_id: recipeId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Herlaad pagina om wijzigingen te tonen
                    } else {
                        alert('Fout bij verwijderen: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Er is een fout opgetreden');
                });
            }
        }
    </script>
</body>
</html>