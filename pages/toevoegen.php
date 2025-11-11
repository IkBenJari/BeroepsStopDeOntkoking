<?php
require_once __DIR__ . '/../backend/functions_auth.php';
$current_user = current_user();

// Redirect naar login als niet ingelogd
if (!$current_user) {
    header('Location: ../backend/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recept Toevoegen - Stop De Ontkoking</title>
    <link rel="stylesheet" href="../styles/recepten.css">
    <style>
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 2rem;
            margin: 2rem auto;
            max-width: 800px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 2px solid #ddd;
            border-radius: 10px;
            font-family: inherit;
            transition: border-color 0.3s ease;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #fdcb6e;
            box-shadow: 0 0 10px rgba(253, 203, 110, 0.3);
        }
        
        .dynamic-list {
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 1rem;
            min-height: 100px;
            background: rgba(255, 255, 255, 0.5);
        }
        
        .list-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.8);
            border-radius: 5px;
        }
        
        .list-item input {
            flex: 1;
            border: none;
            background: transparent;
            padding: 0.25rem;
        }
        
        .remove-btn {
            background: #e74c3c;
            color: white;
            border: none;
            padding: 0.25rem 0.5rem;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .remove-btn:hover {
            background: #c0392b;
        }
        
        .add-btn {
            background: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
            color: white;
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .add-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(253, 203, 110, 0.4);
        }
        
        .submit-btn {
            background: linear-gradient(135deg, #fdcb6e 0%, #e17055 100%);
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            margin-top: 2rem;
            transition: all 0.3s ease;
        }
        
        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(253, 203, 110, 0.6);
        }
    </style>
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
            <a href="toevoegen.php" class="active">Recept Toevoegen</a>
            <a href="../backend/logout.php">Uitloggen</a>
        </nav>
    </header>

    <main>
        <div class="page-header">
            <h1>Nieuw Recept Toevoegen</h1>
            <p>Deel je favoriete recept met anderen!</p>
        </div>

        <div class="form-container">
            <form action="../backend/recipes_create.php" method="POST">
                <div class="form-group">
                    <label for="recipeTitle">Recept Titel *</label>
                    <input type="text" id="recipeTitle" name="title" required placeholder="Bijv. Heerlijke Pannenkoeken">
                </div>

                <div class="form-group">
                    <label for="recipeDescription">Korte Beschrijving *</label>
                    <textarea id="recipeDescription" name="description" rows="3" required placeholder="Korte beschrijving van je recept..."></textarea>
                </div>

                <div class="form-group">
                    <label for="recipeCategory">Categorie *</label>
                    <select id="recipeCategory" name="category" required>
                        <option value="">Kies een categorie</option>
                        <option value="ontbijt">Ontbijt</option>
                        <option value="lunch">Lunch</option>
                        <option value="diner">Diner</option>
                        <option value="snacks">Snacks</option>
                        <option value="drankjes">Drankjes</option>
                        <option value="dessert">Dessert</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="prepTime">Bereidingstijd (minuten) *</label>
                    <input type="number" id="prepTime" name="prep_time" required min="1" placeholder="15">
                </div>

                <div class="form-group">
                    <label for="servings">Aantal personen *</label>
                    <input type="number" id="servings" name="servings" required min="1" placeholder="4">
                </div>

                <div class="form-group">
                    <label for="difficulty">Moeilijkheidsgraad *</label>
                    <select id="difficulty" name="difficulty" required>
                        <option value="">Kies moeilijkheidsgraad</option>
                        <option value="Zeer makkelijk">Zeer makkelijk</option>
                        <option value="Makkelijk">Makkelijk</option>
                        <option value="Gemiddeld">Gemiddeld</option>
                        <option value="Moeilijk">Moeilijk</option>
                        <option value="Zeer moeilijk">Zeer moeilijk</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Ingrediënten *</label>
                    <div class="dynamic-list" id="ingredientsList">
                        <div class="list-item">
                            <input type="text" name="ingredients[]" placeholder="Bijv. 200g bloem" required>
                            <button type="button" class="remove-btn" onclick="removeItem(this)">×</button>
                        </div>
                    </div>
                    <button type="button" class="add-btn" onclick="addIngredient()">+ Ingrediënt toevoegen</button>
                </div>

                <div class="form-group">
                    <label>Bereidingswijze *</label>
                    <div class="dynamic-list" id="instructionsList">
                        <div class="list-item">
                            <input type="text" name="instructions[]" placeholder="Stap 1: Meng alle droge ingrediënten" required>
                            <button type="button" class="remove-btn" onclick="removeItem(this)">×</button>
                        </div>
                    </div>
                    <button type="button" class="add-btn" onclick="addInstruction()">+ Stap toevoegen</button>
                </div>

                <button type="submit" class="submit-btn">Recept Opslaan</button>
            </form>
        </div>
    </main>

    <script>
        // Voeg ingrediënt toe
        function addIngredient() {
            const container = document.getElementById('ingredientsList');
            const newItem = document.createElement('div');
            newItem.className = 'list-item';
            newItem.innerHTML = `
                <input type="text" name="ingredients[]" placeholder="Bijv. 1 ei" required>
                <button type="button" class="remove-btn" onclick="removeItem(this)">×</button>
            `;
            container.appendChild(newItem);
        }

        // Voeg instructie toe
        function addInstruction() {
            const container = document.getElementById('instructionsList');
            const newItem = document.createElement('div');
            newItem.className = 'list-item';
            newItem.innerHTML = `
                <input type="text" name="instructions[]" placeholder="Volgende stap..." required>
                <button type="button" class="remove-btn" onclick="removeItem(this)">×</button>
            `;
            container.appendChild(newItem);
        }

        // Verwijder item
        function removeItem(button) {
            const container = button.parentElement.parentElement;
            if (container.children.length > 1) {
                button.parentElement.remove();
            }
        }
    </script>
</body>
</html>