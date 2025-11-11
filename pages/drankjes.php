<?php
require_once __DIR__ . '/../backend/functions_auth.php';
$current_user = current_user();

// Haal recepten uit database
require_once __DIR__ . '/../backend/db.php';
$stmt = $pdo->prepare('SELECT * FROM recipes WHERE category = :category ORDER BY created_at DESC');
$stmt->execute(['category' => 'drankjes']);
$database_recipes = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drankjes Recepten - Stop De Ontkoking</title>
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
            <h1>Drankjes Recepten</h1>
            <p>Verfrissende en gezonde drankjes voor elke gelegenheid!</p>
        </div>

        <div class="filter-section">
            <button class="filter-btn active" onclick="filterRecipes('all')">Alle</button>
            <button class="filter-btn" onclick="filterRecipes('gezond')">Gezond</button>
            <button class="filter-btn" onclick="filterRecipes('koud')">Koud</button>
            <button class="filter-btn" onclick="filterRecipes('warm')">Warm</button>
        </div>

        <div class="recipes-grid" id="recipesGrid">
            <!-- Standaard recepten -->
            <div class="recipe-card" data-category="gezond koud">
                <img src="../images/citroen-munt-drankje.webp" alt="Citroen Munt Water">
                <div class="recipe-info">
                    <h3>Citroen Munt Water</h3>
                    <p>Verfrissend en detoxerend drankje perfect voor de zomer</p>
                    <div class="recipe-meta">
                        <span>⏱️ 5 min</span>
                        <span>👥 4 glazen</span>
                        <span>⭐ 4.7/5</span>
                    </div>
                    <button class="view-recipe-btn" onclick="viewRecipe('citroen-munt')">Bekijk Recept</button>
                </div>
            </div>

            <div class="recipe-card" data-category="gezond koud">
                <img src="../images/gezond-recept.jpg" alt="Groene Smoothie">
                <div class="recipe-info">
                    <h3>Groene Smoothie</h3>
                    <p>Energiegevende smoothie vol vitaminen en mineralen</p>
                    <div class="recipe-meta">
                        <span>⏱️ 8 min</span>
                        <span>👥 2 glazen</span>
                        <span>⭐ 4.4/5</span>
                    </div>
                    <button class="view-recipe-btn" onclick="viewRecipe('groene-smoothie')">Bekijk Recept</button>
                </div>
            </div>

            <div class="recipe-card" data-category="warm">
                <img src="../images/gezond-recept.jpg" alt="Kruidenthee">
                <div class="recipe-info">
                    <h3>Zelfgemaakte Kruidenthee</h3>
                    <p>Rustgevende thee met verse kruiden</p>
                    <div class="recipe-meta">
                        <span>⏱️ 10 min</span>
                        <span>👥 2 koppen</span>
                        <span>⭐ 4.6/5</span>
                    </div>
                    <button class="view-recipe-btn" onclick="viewRecipe('kruidenthee')">Bekijk Recept</button>
                </div>
            </div>

            <!-- Gebruikers recepten uit database -->
            <?php foreach ($database_recipes as $recipe): ?>
                <div class="recipe-card" data-category="user">
                    <img src="../images/citroen-munt-drankje.webp" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
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