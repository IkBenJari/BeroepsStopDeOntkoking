<?php
$config = require __DIR__ . '/config.php';
try {
    // SQLite database (geen server nodig)
    $dbPath = __DIR__ . '/../database/site_db.sqlite';
    
    // Maak database directory aan als die niet bestaat
    $dbDir = dirname($dbPath);
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0755, true);
    }
    
    $dsn = "sqlite:" . $dbPath;
    $pdo = new PDO($dsn, null, null, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    // Maak tabellen aan als ze niet bestaan
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        username TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        role TEXT DEFAULT 'user',
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");
    
    // Voeg role kolom toe als deze nog niet bestaat (voor bestaande databases)
    try {
        $pdo->exec("ALTER TABLE users ADD COLUMN role TEXT DEFAULT 'user'");
    } catch (Exception $e) {
        // Kolom bestaat al, dit is oké
    }
    
    // Maak de recipes tabel met alle benodigde kolommen
    $pdo->exec("CREATE TABLE IF NOT EXISTS recipes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        title TEXT NOT NULL,
        description TEXT,
        ingredients TEXT,
        instructions TEXT,
        category TEXT,
        prep_time INTEGER DEFAULT 0,
        servings INTEGER DEFAULT 1,
        difficulty TEXT,
        image_url TEXT,
        created_by INTEGER,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(id)
    )");
    
    // Voeg ontbrekende kolommen toe aan bestaande recipes tabel
    $columns_to_add = [
        'prep_time INTEGER DEFAULT 0',
        'servings INTEGER DEFAULT 1',
        'difficulty TEXT',
        'created_by INTEGER'
    ];
    
    foreach ($columns_to_add as $column) {
        try {
            $column_name = explode(' ', $column)[0];
            $pdo->exec("ALTER TABLE recipes ADD COLUMN $column");
        } catch (Exception $e) {
            // Kolom bestaat al, dit is oké
        }
    }
    
    // Voeg standaard admin gebruiker toe als die nog niet bestaat
    $stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username');
    $stmt->execute(['username' => 'admin']);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (:username, :password, :role)');
        $stmt->execute([
            'username' => 'admin',
            'password' => password_hash('admin123', PASSWORD_DEFAULT),
            'role' => 'admin'
        ]);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo "Database connection error: " . htmlspecialchars($e->getMessage());
    exit;
}