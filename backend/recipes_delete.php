<?php
require_once __DIR__ . '/functions_auth.php';
require_login();

header('Content-Type: application/json');

$user = current_user();
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['recipe_id'])) {
    echo json_encode(['success' => false, 'error' => 'Recipe ID is required']);
    exit;
}

$recipe_id = (int)$input['recipe_id'];

try {
    require_once __DIR__ . '/db.php';
    
    // Check of het recept van de huidige gebruiker is
    $stmt = $pdo->prepare('SELECT created_by FROM recipes WHERE id = :id');
    $stmt->execute(['id' => $recipe_id]);
    $recipe = $stmt->fetch();
    
    if (!$recipe) {
        echo json_encode(['success' => false, 'error' => 'Recept niet gevonden']);
        exit;
    }
    
    if ($recipe['created_by'] != $user['id'] && $user['role'] !== 'admin') {
        echo json_encode(['success' => false, 'error' => 'Je kunt alleen je eigen recepten verwijderen']);
        exit;
    }
    
    // Verwijder het recept
    $stmt = $pdo->prepare('DELETE FROM recipes WHERE id = :id');
    $stmt->execute(['id' => $recipe_id]);
    
    echo json_encode(['success' => true, 'message' => 'Recept succesvol verwijderd']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database fout: ' . $e->getMessage()]);
}