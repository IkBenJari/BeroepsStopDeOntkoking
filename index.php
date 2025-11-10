<?php
require_once __DIR__ . '/backend/functions_auth.php';
$current_user = current_user();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stop De Ontkoking - Inspiratie voor Generatie Z</title>
    <link rel="stylesheet" href="styles/homepage.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="user">
                <img src="images/unknown-user.png" alt="User pictogram">
                <?php if ($current_user): ?>
                    <p><?php echo htmlspecialchars($current_user['username']); ?></p>
                <?php else: ?>
                    <p>Gast</p>
                <?php endif; ?>
            </div>
            <a href="index.php">Home</a>
            <div class="search-container">
                <input type="text" placeholder="Zoek recepten..." class="search-input" id="searchInput">
                <button class="search-button" onclick="searchAllRecipes()">Zoek</button>
            </div>
            <a href="pages/ontbijt.html">Recepten</a>
            <?php if ($current_user): ?>
                <a href="pages/toevoegen.html" class="add-recipe-btn">Recept Toevoegen</a>
                <a href="backend/logout.php" class="logout-btn">Uitloggen</a>
            <?php else: ?>
                <a href="backend/login.php" class="admin-login-btn">Inloggen / Beheer</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <div class="hero-section">
            <div class="hero-content">
                <h1>Stop De Ontkoking!</h1>
                <p>Ontdek heerlijke recepten en begin weer met koken. Perfect voor generatie Z en iedereen die van lekker eten houdt!</p>
                <button class="cta-button" onclick="scrollToCategories()">Begin met Koken 🍳</button>
            </div>
            <div class="hero-image">
                <img src="images/gezond-recept.jpg" alt="Gezond eten">
            </div>
        </div>

        <div class="stats-section">
            <div class="stat-item">
                <h3 id="totalRecipes">50+</h3>
                <p>Recepten</p>
            </div>
            <div class="stat-item">
                <h3>1000+</h3>
                <p>Tevreden Koks</p>
            </div>
            <div class="stat-item">
                <h3>4.8⭐</h3>
                <p>Gemiddelde Rating</p>
            </div>
        </div>

        <div class="categories-section" id="categoriesSection">
            <h2>Kies je Categorie</h2>
            <p>Van ontbijt tot diner, van snacks tot drankjes - wij hebben het allemaal!</p>
            
            <div class="categories-grid">
                <a href="pages/ontbijt.html" class="category-card">
                    <img src="images/Pancake.jpg" alt="Ontbijt">
                    <div class="category-overlay">
                        <h3>Ontbijt</h3>
                        <p>Start je dag goed</p>
                    </div>
                </a>

                <a href="pages/drankjes.html" class="category-card">
                    <img src="images/citroen-munt-drankje.webp" alt="Drankjes">
                    <div class="category-overlay">
                        <h3>Drankjes</h3>
                        <p>Verfrissend & gezond</p>
                    </div>
                </a>

                <a href="pages/snacks.html" class="category-card">
                    <img src="images/groente-achtergrond.jpg" alt="Snacks">
                    <div class="category-overlay">
                        <h3>Snacks</h3>
                        <p>Gezond tussendoortje</p>
                    </div>
                </a>

                <a href="pages/ontbijt.html" class="category-card">
                    <img src="images/groente-achtergrond.jpg" alt="Lunch">
                    <div class="category-overlay">
                        <h3>Lunch</h3>
                        <p>Voedzame middagmaaltijd</p>
                    </div>
                </a>

                <a href="pages/ontbijt.html" class="category-card">
                    <img src="images/gezond-recept.jpg" alt="Diner">
                    <div class="category-overlay">
                        <h3>Diner</h3>
                        <p>Heerlijke avondmaaltijd</p>
                    </div>
                </a>

                <a href="pages/ontbijt.html" class="category-card">
                    <img src="images/Pancake.jpg" alt="Dessert">
                    <div class="category-overlay">
                        <h3>Dessert</h3>
                        <p>Zoete afsluiting</p>
                    </div>
                </a>
            </div>
        </div>

        <div class="featured-section">
            <h2>Populaire Recepten</h2>
            <div class="featured-recipes" id="featuredRecipes">
                <!-- Populaire recepten worden hier geladen -->
            </div>
        </div>

        <div class="tips-section">
            <h2>Kook Tips voor Beginners</h2>
            <div class="tips-grid">
                <div class="tip-card">
                    <div class="tip-icon">📝</div>
                    <h3>Plan Vooruit</h3>
                    <p>Maak een weekmenu en boodschappenlijstje. Dit bespaart tijd en geld!</p>
                </div>
                <div class="tip-card">
                    <div class="tip-icon">🔪</div>
                    <h3>Prep je Ingrediënten</h3>
                    <p>Snijd alle groenten van tevoren. Dit maakt koken veel makkelijker!</p>
                </div>
                <div class="tip-card">
                    <div class="tip-icon">🍽️</div>
                    <h3>Begin Simpel</h3>
                    <p>Start met eenvoudige recepten en bouw je vaardigheden langzaam op.</p>
                </div>
                <div class="tip-card">
                    <div class="tip-icon">👥</div>
                    <h3>Kook Samen</h3>
                    <p>Koken met vrienden of familie is leuker en je leert van elkaar!</p>
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
                <a href="index.php">Home</a>
                <a href="pages/ontbijt.html">Recepten</a>
                <a href="backend/login.php">Beheer</a>
            </div>
            <div class="footer-section">
                <h3>Categorieën</h3>
                <a href="pages/ontbijt.html">Ontbijt</a>
                <a href="pages/drankjes.html">Drankjes</a>
                <a href="pages/snacks.html">Snacks</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2025 GLR Food Freaks - Team OJN. Alle rechten voorbehouden.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadFeaturedRecipes();
            updateRecipeCount();
        });

        function loadFeaturedRecipes() {
            const featuredContainer = document.getElementById('featuredRecipes');
            const featuredRecipes = [
                { id: 'pancakes', title: 'Fluffy Pancakes', image: 'images/Pancake.jpg', rating: '4.5/5' },
                { id: 'acai-bowl', title: 'Acai Bowl', image: 'images/gezond-recept.jpg', rating: '4.8/5' },
                { id: 'citroen-munt', title: 'Citroen Munt Water', image: 'images/citroen-munt-drankje.webp', rating: '4.7/5' }
            ];

            featuredContainer.innerHTML = '';
            featuredRecipes.forEach(recipe => {
                const recipeCard = document.createElement('div');
                recipeCard.className = 'featured-recipe-card';
                recipeCard.innerHTML = `
                    <img src="${recipe.image}" alt="${recipe.title}">
                    <div class="recipe-info">
                        <h3>${recipe.title}</h3>
                        <p>Rating: ${recipe.rating}</p>
                        <button onclick="viewRecipe('${recipe.id}')">Bekijk Recept</button>
                    </div>
                `;
                featuredContainer.appendChild(recipeCard);
            });
        }

        function updateRecipeCount() {
            // Voor publieke pagina tonen we een vast aantal
            document.getElementById('totalRecipes').textContent = '50+';
        }

        function scrollToCategories() {
            document.getElementById('categoriesSection').scrollIntoView({ 
                behavior: 'smooth' 
            });
        }

        function searchAllRecipes() {
            const searchTerm = document.getElementById('searchInput').value.trim();
            if (searchTerm) {
                localStorage.setItem('searchTerm', searchTerm);
                window.location.href = 'pages/ontbijt.html';
            }
        }

        function viewRecipe(recipeId) {
            localStorage.setItem('selectedRecipe', recipeId);
            window.location.href = 'pages/recept-detail.html';
        }
    </script>
</body>
</html>