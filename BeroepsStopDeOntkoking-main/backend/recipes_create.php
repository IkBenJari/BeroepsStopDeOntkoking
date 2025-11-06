<?php
require_once __DIR__ . '/functions_auth.php';
require_login(); // Alleen ingelogde gebruikers kunnen recepten aanmaken

$config = require __DIR__ . '/config.php';
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $ingredients = trim($_POST['ingredients'] ?? '');
    $instructions = trim($_POST['instructions'] ?? '');
    $category = $_POST['category'] ?? '';
    $prep_time = (int)($_POST['prep_time'] ?? 0);
    $cook_time = (int)($_POST['cook_time'] ?? 0);
    
    $errors = [];
    
    if (empty($title)) $errors[] = 'Titel is verplicht';
    if (empty($description)) $errors[] = 'Beschrijving is verplicht';
    if (empty($ingredients)) $errors[] = 'Ingrediënten zijn verplicht';
    if (empty($instructions)) $errors[] = 'Bereidingswijze is verplicht';
    if (empty($category)) $errors[] = 'Categorie is verplicht';
    
    // Handle image upload
    $image_path = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $file_type = $_FILES['image']['type'];
        
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = 'Alleen JPEG, PNG en WebP afbeeldingen zijn toegestaan';
        } else {
            $extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('recipe_') . '.' . $extension;
            $upload_path = $config['upload_dir'] . '/' . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                $image_path = 'backend/uploads/' . $filename;
            } else {
                $errors[] = 'Fout bij uploaden van afbeelding';
            }
        }
    }
    
    if (empty($errors)) {
        try {
            require_once __DIR__ . '/db.php';
            
            $stmt = $pdo->prepare('
                INSERT INTO recipes (title, description, ingredients, instructions, category, prep_time, cook_time, image_path, created_by, created_at) 
                VALUES (:title, :description, :ingredients, :instructions, :category, :prep_time, :cook_time, :image_path, :created_by, NOW())
            ');
            
            $stmt->execute([
                'title' => $title,
                'description' => $description,
                'ingredients' => $ingredients,
                'instructions' => $instructions,
                'category' => $category,
                'prep_time' => $prep_time,
                'cook_time' => $cook_time,
                'image_path' => $image_path,
                'created_by' => $user['id']
            ]);
            
            $success = 'Recept succesvol toegevoegd!';
            // Reset form
            $title = $description = $ingredients = $instructions = $category = '';
            $prep_time = $cook_time = 0;
            
        } catch (Exception $e) {
            $errors[] = 'Database fout: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recept Toevoegen - Stop De Ontkoking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c5530;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        .time-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        button {
            background: #2c5530;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background: #1e3a21;
        }
        .nav-links {
            text-align: center;
            margin-bottom: 20px;
        }
        .nav-links a {
            color: #2c5530;
            text-decoration: none;
            margin: 0 15px;
        }
        .nav-links a:hover {
            text-decoration: underline;
        }
        .error {
            background: #ffe6e6;
            color: #d00;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .success {
            background: #e6f7e6;
            color: #0a5d0a;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav-links">
            <a href="../index.html">← Terug naar Home</a>
            <a href="admin/index.php">Admin Paneel</a>
            <a href="logout.php">Uitloggen</a>
        </div>
        
        <h1>Nieuw Recept Toevoegen</h1>
        <p>Welkom, <strong><?php echo htmlspecialchars($user['username']); ?></strong>!</p>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <ul style="margin: 0; padding-left: 20px;">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="title">Recept Titel:</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($title ?? ''); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Korte Beschrijving:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($description ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="category">Categorie:</label>
                <select id="category" name="category" required>
                    <option value="">Kies een categorie</option>
                    <option value="ontbijt" <?php echo ($category ?? '') === 'ontbijt' ? 'selected' : ''; ?>>Ontbijt</option>
                    <option value="lunch" <?php echo ($category ?? '') === 'lunch' ? 'selected' : ''; ?>>Lunch</option>
                    <option value="diner" <?php echo ($category ?? '') === 'diner' ? 'selected' : ''; ?>>Diner</option>
                    <option value="snacks" <?php echo ($category ?? '') === 'snacks' ? 'selected' : ''; ?>>Snacks</option>
                    <option value="drankjes" <?php echo ($category ?? '') === 'drankjes' ? 'selected' : ''; ?>>Drankjes</option>
                    <option value="dessert" <?php echo ($category ?? '') === 'dessert' ? 'selected' : ''; ?>>Dessert</option>
                </select>
            </div>
            
            <div class="time-inputs">
                <div class="form-group">
                    <label for="prep_time">Voorbereidingstijd (minuten):</label>
                    <input type="number" id="prep_time" name="prep_time" min="0" value="<?php echo htmlspecialchars($prep_time ?? 0); ?>">
                </div>
                
                <div class="form-group">
                    <label for="cook_time">Bereidingstijd (minuten):</label>
                    <input type="number" id="cook_time" name="cook_time" min="0" value="<?php echo htmlspecialchars($cook_time ?? 0); ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label for="ingredients">Ingrediënten (één per regel):</label>
                <textarea id="ingredients" name="ingredients" placeholder="Bijvoorbeeld:&#10;2 eieren&#10;250ml melk&#10;200g bloem" required><?php echo htmlspecialchars($ingredients ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="instructions">Bereidingswijze:</label>
                <textarea id="instructions" name="instructions" placeholder="Beschrijf stap voor stap hoe het recept gemaakt wordt..." required><?php echo htmlspecialchars($instructions ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Afbeelding (optioneel):</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp">
            </div>
            
            <button type="submit">Recept Toevoegen</button>
        </form>
    </div>
</body>
</html>