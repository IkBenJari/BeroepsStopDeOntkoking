<?php
require_once __DIR__ . '/../backend/functions_auth.php';
$current_user = current_user();

// Haal recept ID op
$recipe_id = $_GET['id'] ?? null;
if (!$recipe_id) {
    header('Location: ontbijt.php');
    exit;
}

// Haal recept op uit database
require_once __DIR__ . '/../backend/db.php';
$stmt = $pdo->prepare('SELECT * FROM recipes WHERE id = :id');
$stmt->execute(['id' => $recipe_id]);
$recipe = $stmt->fetch();

if (!$recipe) {
    // Fallback naar standaard recepten
    $default_recipes = [
        'pancakes' => [
            'id' => 'pancakes',
            'title' => 'Fluffy Pancakes',
            'description' => 'Heerlijke fluffy pancakes perfect voor een weekend ontbijt',
            'ingredients' => json_encode(['200g bloem', '250ml melk', '2 eieren', '2 el suiker', '1 tl bakpoeder', 'Snufje zout', 'Boter voor bakken']),
            'instructions' => json_encode(['Meng bloem, bakpoeder, suiker en zout in een kom', 'Klop melk en eieren samen in een andere kom', 'Voeg het melk-ei mengsel toe aan de droge ingrediënten', 'Roer tot een glad beslag (niet te lang roeren)', 'Verhit boter in een pan op middelhoog vuur', 'Schep beslag in de pan en bak tot bubbels verschijnen', 'Keer om en bak nog 1-2 minuten goudbruin']),
            'prep_time' => 15,
            'servings' => 4,
            'difficulty' => 'Makkelijk',
            'category' => 'ontbijt',
            'created_at' => date('Y-m-d H:i:s')
        ],
        'acai-bowl' => [
            'id' => 'acai-bowl',
            'title' => 'Acai Bowl',
            'description' => 'Een gezonde en kleurrijke start van de dag vol vitaminen',
            'ingredients' => json_encode(['100g bevroren acai pulp', '1 banaan', '100ml kokosmelk', '1 el honing', 'Granola', 'Verse bessen', 'Kokosraspels']),
            'instructions' => json_encode(['Laat acai pulp 5 minuten ontdooien', 'Mix acai, halve banaan, kokosmelk en honing in blender', 'Blend tot gladde consistentie', 'Giet in een kom', 'Versier met granola, bessen en kokosraspels', 'Serveer direct']),
            'prep_time' => 10,
            'servings' => 2,
            'difficulty' => 'Zeer makkelijk',
            'category' => 'ontbijt',
            'created_at' => date('Y-m-d H:i:s')
        ]
    ];
    
    if (isset($default_recipes[$recipe_id])) {
        $recipe = $default_recipes[$recipe_id];
    } else {
        header('Location: ontbijt.php');
        exit;
    }
}

$ingredients = json_decode($recipe['ingredients'], true) ?: [];
$instructions = json_decode($recipe['instructions'], true) ?: [];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($recipe['title']); ?> - Stop De Ontkoking</title>
    <link rel="stylesheet" href="../styles/detail.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="user">
                <img src="../images/unknown-user.png" alt="User pictogram">
                <p><?php echo $current_user ? htmlspecialchars($current_user['username']) : 'Gast'; ?></p>
            </div>
            <a href="../index.php">Home</a>
            <a href="javascript:history.back()">← Terug</a>
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
        <div class="recipe-container">
            <div class="recipe-header">
                <img src="../images/gezond-recept.jpg" alt="<?php echo htmlspecialchars($recipe['title']); ?>">
                <div class="recipe-info">
                    <h1><?php echo htmlspecialchars($recipe['title']); ?></h1>
                    <p class="recipe-description"><?php echo htmlspecialchars($recipe['description']); ?></p>
                    <div class="recipe-meta">
                        <span>⏱️ <?php echo htmlspecialchars($recipe['prep_time']); ?> min</span>
                        <span>👥 <?php echo htmlspecialchars($recipe['servings']); ?> personen</span>
                        <span>📊 <?php echo htmlspecialchars($recipe['difficulty']); ?></span>
                        <span>🏷️ <?php echo htmlspecialchars($recipe['category']); ?></span>
                    </div>
                    <div class="recipe-actions">
                        <?php if ($current_user): ?>
                            <button class="favorite-btn" onclick="toggleFavorite('<?php echo $recipe['id']; ?>')">❤️ Favoriet</button>
                        <?php endif; ?>
                        <button class="print-btn" onclick="window.print()">🖨️ Print</button>
                    </div>
                </div>
            </div>

            <div class="recipe-content">
                <div class="ingredients-section">
                    <h2>Ingrediënten</h2>
                    <div class="servings-adjuster">
                        <button onclick="adjustServings(-1)">-</button>
                        <span id="currentServings"><?php echo $recipe['servings']; ?></span>
                        <button onclick="adjustServings(1)">+</button>
                        <span>personen</span>
                    </div>
                    <ul id="ingredientsList">
                        <?php foreach ($ingredients as $ingredient): ?>
                            <li class="ingredient-item" data-original="<?php echo htmlspecialchars($ingredient); ?>">
                                <?php echo htmlspecialchars($ingredient); ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <div class="instructions-section">
                    <h2>Bereidingswijze</h2>
                    <ol>
                        <?php foreach ($instructions as $index => $instruction): ?>
                            <li>
                                <div class="step-number"><?php echo $index + 1; ?></div>
                                <div class="step-content"><?php echo htmlspecialchars($instruction); ?></div>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                </div>

                <?php if ($current_user): ?>
                <div class="reviews-section">
                    <h2>Wat vind je van dit recept?</h2>
                    <div class="add-review">
                        <div class="rating-input">
                            <span onclick="setRating(1)" class="star" data-rating="1">⭐</span>
                            <span onclick="setRating(2)" class="star" data-rating="2">⭐</span>
                            <span onclick="setRating(3)" class="star" data-rating="3">⭐</span>
                            <span onclick="setRating(4)" class="star" data-rating="4">⭐</span>
                            <span onclick="setRating(5)" class="star" data-rating="5">⭐</span>
                        </div>
                        <textarea id="reviewText" placeholder="Deel je ervaring met dit recept..."></textarea>
                        <button onclick="submitReview(<?php echo $recipe['id']; ?>)">Review Toevoegen</button>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <style>
        .recipe-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .recipe-header {
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
            margin-bottom: 40px;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .recipe-header img {
            width: 100%;
            height: 250px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        .recipe-info h1 {
            color: #2c5530;
            margin-bottom: 10px;
        }
        
        .recipe-description {
            color: #666;
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
        
        .recipe-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        
        .recipe-meta span {
            background: rgba(253, 203, 110, 0.2);
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 500;
        }
        
        .recipe-actions {
            display: flex;
            gap: 10px;
        }
        
        .recipe-actions button {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .favorite-btn {
            background: linear-gradient(135deg, #ff6b6b, #ee5a24);
            color: white;
        }
        
        .print-btn {
            background: linear-gradient(135deg, #54a0ff, #2e86de);
            color: white;
        }
        
        .recipe-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }
        
        .ingredients-section,
        .instructions-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        
        .servings-adjuster {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .servings-adjuster button {
            background: #fdcb6e;
            border: none;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            cursor: pointer;
            font-weight: bold;
        }
        
        .ingredient-item {
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .instructions-section ol {
            counter-reset: step-counter;
        }
        
        .instructions-section li {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 20px;
            counter-increment: step-counter;
        }
        
        .step-number {
            background: #fdcb6e;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }
        
        .reviews-section {
            grid-column: 1 / -1;
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin-top: 30px;
        }
        
        .rating-input {
            margin-bottom: 15px;
        }
        
        .star {
            cursor: pointer;
            font-size: 1.5rem;
            transition: all 0.2s ease;
        }
        
        .star:hover,
        .star.active {
            transform: scale(1.2);
        }
        
        @media (max-width: 768px) {
            .recipe-header {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .recipe-content {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        const originalServings = <?php echo $recipe['servings']; ?>;
        let currentRating = 0;

        function adjustServings(change) {
            const servingsSpan = document.getElementById('currentServings');
            let newServings = parseInt(servingsSpan.textContent) + change;
            
            if (newServings < 1) newServings = 1;
            if (newServings > 20) newServings = 20;
            
            servingsSpan.textContent = newServings;
            
            // Update ingredient quantities
            const ingredients = document.querySelectorAll('.ingredient-item');
            const multiplier = newServings / originalServings;
            
            ingredients.forEach(ingredient => {
                const original = ingredient.dataset.original;
                const updated = original.replace(/(\d+(?:\.\d+)?)/g, (match) => {
                    return Math.round(parseFloat(match) * multiplier * 10) / 10;
                });
                ingredient.textContent = updated;
            });
        }

        function setRating(rating) {
            currentRating = rating;
            const stars = document.querySelectorAll('.star');
            
            stars.forEach((star, index) => {
                if (index < rating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
        }

        function toggleFavorite(recipeId) {
            // Hier zou je een AJAX call kunnen maken naar de backend
            alert('Favoriet functionaliteit komt binnenkort!');
        }

        function submitReview(recipeId) {
            if (currentRating === 0) {
                alert('Geef eerst een rating!');
                return;
            }
            
            const reviewText = document.getElementById('reviewText').value;
            if (!reviewText.trim()) {
                alert('Schrijf een review!');
                return;
            }
            
            // Hier zou je de review naar de backend sturen
            alert('Bedankt voor je review! (Functionaliteit komt binnenkort)');
            document.getElementById('reviewText').value = '';
            setRating(0);
        }
    </script>
</body>
</html>