<?php
require_once __DIR__ . '/../backend/functions_auth.php';
$current_user = current_user();

// Haal recepten uit database
require_once __DIR__ . '/../backend/db.php';
$stmt = $pdo->prepare('SELECT * FROM recipes WHERE category = :category ORDER BY created_at DESC');
$stmt->execute(['category' => 'ontbijt']);
$database_recipes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ontbijt Recepten - Stop De Ontkoking</title>
    <link rel="stylesheet" href="../styles/recepten.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="user">
                <img src="../images/unknown-user.png" alt="User pictogram">
                <p><?php echo $current_user ? htmlspecialchars($current_user['username']) : 'Gast'; ?></p>
            </div>
            <a href="../index.php">Home</a>
            <div class="search-container">
                <input type="text" placeholder="Zoek recepten..." class="search-input" id="searchInput">
                <button class="search-button" onclick="searchRecipes()">Zoek</button>
            </div>
            <?php if ($current_user): ?>
                <a href="toevoegen.php">Recept Toevoegen</a>
                <a href="mijn-recepten.php">Mijn Recepten</a>
                <a href="../backend/logout.php">Uitloggen</a>
            <?php else: ?>
                <a href="../backend/login.php">Inloggen</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <div class="page-header">
            <h1>Ontbijt Recepten</h1>
            <p>Start je dag goed met deze heerlijke ontbijtrecepten!</p>
        </div>

        <div class="filter-section">
            <button class="filter-btn active" onclick="filterRecipes('all')">Alle</button>
            <button class="filter-btn" onclick="filterRecipes('gezond')">Gezond</button>
            <button class="filter-btn" onclick="filterRecipes('snel')">Snel & Makkelijk</button>
            <button class="filter-btn" onclick="filterRecipes('zoet')">Zoet</button>
        </div>

        <div class="recipes-grid" id="recipesGrid">
            <!-- Standaard recepten -->
            <div class="recipe-card" data-category="snel zoet">
                <img src="../images/Pancake.jpg" alt="Pancakes">
                <div class="recipe-info">
                    <h3>Fluffy Pancakes</h3>
                    <p>Heerlijke fluffy pancakes perfect voor een weekend ontbijt</p>
                    <div class="recipe-meta">
                        <span>⏱️ 15 min</span>
                        <span>👥 2 personen</span>
                        <span>⭐ 4.5/5</span>
                    </div>
                    <button class="view-recipe-btn" onclick="viewRecipe('pancakes')">Bekijk Recept</button>
                </div>
            </div>

            <div class="recipe-card" data-category="gezond">
                <img src="../images/gezond-recept.jpg" alt="Gezonde Bowl">
                <div class="recipe-info">
                    <h3>Acai Bowl</h3>
                    <p>Een gezonde en kleurrijke start van de dag vol vitaminen</p>
                    <div class="recipe-meta">
                        <span>⏱️ 10 min</span>
                        <span>👥 1 persoon</span>
                        <span>⭐ 4.8/5</span>
                    </div>
                    <button class="view-recipe-btn" onclick="viewRecipe('acai-bowl')">Bekijk Recept</button>
                </div>
            </div>

            <div class="recipe-card" data-category="snel">
                <img src="../images/gezonde-wrap-rolletjes.jpg" alt="Ontbijt Wrap">
                <div class="recipe-info">
                    <h3>Ontbijt Wrap</h3>
                    <p>Snelle en voedzame wrap perfect voor onderweg</p>
                    <div class="recipe-meta">
                        <span>⏱️ 5 min</span>
                        <span>👥 1 persoon</span>
                        <span>⭐ 4.2/5</span>
                    </div>
                    <button class="view-recipe-btn" onclick="viewRecipe('ontbijt-wrap')">Bekijk Recept</button>
                </div>
            </div>

            <!-- Gebruikers recepten uit database -->
            <?php foreach ($database_recipes as $recipe): ?>
                <?php
                $ingredients = json_decode($recipe['ingredients'], true) ?: [];
                $instructions = json_decode($recipe['instructions'], true) ?: [];
                ?>
                <div class="recipe-card" data-category="user">
                    <img src="../images/gezond-recept.jpg" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                    <div class="recipe-info">
                        <h3><?php echo htmlspecialchars($recipe['title']); ?></h3>
                        <p><?php echo htmlspecialchars($recipe['description']); ?></p>
                        <div class="recipe-meta">
                            <span>⏱️ <?php echo htmlspecialchars($recipe['prep_time']); ?> min</span>
                            <span>👥 <?php echo htmlspecialchars($recipe['servings']); ?> personen</span>
                            <span>📊 <?php echo htmlspecialchars($recipe['difficulty']); ?></span>
                        </div>
                        <button class="view-recipe-btn" onclick="viewRecipe(<?php echo $recipe['id']; ?>)">Bekijk Recept</button>
                        <small style="color: #666; font-style: italic; margin-top: 0.5rem; display: block;">
                            Door gebruiker • <?php echo date('j M Y', strtotime($recipe['created_at'])); ?>
                        </small>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </main>

    <script>
        // Check voor zoekterm
        document.addEventListener('DOMContentLoaded', function() {
            const searchTerm = localStorage.getItem('searchTerm');
            if (searchTerm) {
                document.getElementById('searchInput').value = searchTerm;
                searchRecipes();
                localStorage.removeItem('searchTerm');
            }
        });

        function filterRecipes(category) {
            const cards = document.querySelectorAll('.recipe-card');
            const buttons = document.querySelectorAll('.filter-btn');
            
            // Update active button
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            cards.forEach(card => {
                if (category === 'all') {
                    card.style.display = 'block';
                } else {
                    const cardCategories = card.getAttribute('data-category');
                    if (cardCategories && cardCategories.includes(category)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
        }

        function searchRecipes() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const cards = document.querySelectorAll('.recipe-card');
            
            cards.forEach(card => {
                const title = card.querySelector('h3').textContent.toLowerCase();
                const description = card.querySelector('p').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function viewRecipe(recipeId) {
            window.location.href = 'recept-detail.php?id=' + recipeId;
        }
    </script>
</body>
</html>