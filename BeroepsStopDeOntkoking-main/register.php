<?php
session_start();

// Database configuration (for now using JSON file storage for simplicity)
$users_file = 'data/users.json';

// Create data directory if it doesn't exist
if (!file_exists('data')) {
    mkdir('data', 0755, true);
}

// Initialize users file if it doesn't exist
if (!file_exists($users_file)) {
    file_put_contents($users_file, json_encode([]));
}

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $voornaam = trim($_POST['voornaam'] ?? '');
    $achternaam = trim($_POST['achternaam'] ?? '');
    $telefoon = trim($_POST['telefoon'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($voornaam) || empty($achternaam) || empty($email) || empty($password)) {
        $response['message'] = 'Alle velden zijn verplicht!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Ongeldig email adres!';
    } elseif (strlen($password) < 5) {
        $response['message'] = 'Wachtwoord moet minimaal 5 karakters lang zijn!';
    } else {
        // Load existing users
        $users = json_decode(file_get_contents($users_file), true);
        
        // Check if email already exists
        $email_exists = false;
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                $email_exists = true;
                break;
            }
        }
        
        if ($email_exists) {
            $response['message'] = 'Een account met dit email adres bestaat al!';
        } else {
            // Create new user
            $new_user = [
                'id' => uniqid(),
                'voornaam' => $voornaam,
                'achternaam' => $achternaam,
                'telefoon' => $telefoon,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $users[] = $new_user;
            
            if (file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT))) {
                $response['success'] = true;
                $response['message'] = 'Account succesvol aangemaakt!';
                
                // Set session
                $_SESSION['user_id'] = $new_user['id'];
                $_SESSION['user_name'] = $voornaam;
                $_SESSION['user_email'] = $email;
            } else {
                $response['message'] = 'Fout bij het opslaan van gebruikersgegevens!';
            }
        }
    }
    
    // Return JSON response for AJAX requests
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}
?>