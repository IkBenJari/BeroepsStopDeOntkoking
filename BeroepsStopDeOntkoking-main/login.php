<?php
session_start();

// Database configuration (using JSON file storage)
$users_file = 'data/users.json';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($email) || empty($password)) {
        $response['message'] = 'Email en wachtwoord zijn verplicht!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = 'Ongeldig email adres!';
    } else {
        // Check if users file exists
        if (file_exists($users_file)) {
            $users = json_decode(file_get_contents($users_file), true);
            
            // Find user by email
            $found_user = null;
            foreach ($users as $user) {
                if ($user['email'] === $email) {
                    $found_user = $user;
                    break;
                }
            }
            
            if ($found_user && password_verify($password, $found_user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $found_user['id'];
                $_SESSION['user_name'] = $found_user['voornaam'];
                $_SESSION['user_email'] = $found_user['email'];
                
                $response['success'] = true;
                $response['message'] = 'Succesvol ingelogd!';
                $response['redirect'] = 'pages/homepage.html';
            } else {
                $response['message'] = 'Ongeldig email adres of wachtwoord!';
            }
        } else {
            $response['message'] = 'Geen gebruikersaccount gevonden!';
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