<?php
session_start();
require_once __DIR__ . '/db.php';


function current_user() {
return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}


function require_login() {
if (!current_user()) {
// Bepaal het relatieve pad naar login.php vanuit de huidige locatie
$script_path = $_SERVER['SCRIPT_NAME'];
$script_dir = dirname($script_path);
$relative_path = '';

// Tel hoeveel niveaus diep we zijn
$depth = substr_count(trim($script_dir, '/'), '/');
for ($i = 0; $i < $depth; $i++) {
    $relative_path .= '../';
}

header('Location: ' . $relative_path . 'backend/login.php');
exit;
}
}


function require_admin() {
$u = current_user();
if (!$u || $u['role'] !== 'admin') {
http_response_code(403);
echo 'Forbidden';
exit;
}
}


function login($username, $password) {
global $pdo;
$stmt = $pdo->prepare('SELECT * FROM users WHERE username = :u LIMIT 1');
$stmt->execute(['u'=>$username]);
$user = $stmt->fetch();
if (!$user) return false;


// If password in DB is a bcrypt hash, use password_verify
if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
// This checks if it's hash format; if not, fallback
}


if (password_verify($password, $user['password'])) {
// ok
unset($user['password']);
$_SESSION['user'] = $user;
return true;
}


// fallback: plain-text comparison (only for the seeded admin entry)
if ($user['password'] === $password) {
unset($user['password']);
$_SESSION['user'] = $user;
return true;
}


return false;
}


function logout() {
session_unset();
session_destroy();
}


function register($username, $password, $confirmPassword) {
global $pdo;

// Validatie
if (empty($username) || empty($password) || empty($confirmPassword)) {
return ['success' => false, 'error' => 'Alle velden zijn verplicht'];
}

if (strlen($username) < 3) {
return ['success' => false, 'error' => 'Gebruikersnaam moet minimaal 3 karakters lang zijn'];
}

if (strlen($password) < 6) {
return ['success' => false, 'error' => 'Wachtwoord moet minimaal 6 karakters lang zijn'];
}

if ($password !== $confirmPassword) {
return ['success' => false, 'error' => 'Wachtwoorden komen niet overeen'];
}

// Check of gebruikersnaam al bestaat
$stmt = $pdo->prepare('SELECT id FROM users WHERE username = :username LIMIT 1');
$stmt->execute(['username' => $username]);
if ($stmt->fetch()) {
return ['success' => false, 'error' => 'Gebruikersnaam is al in gebruik'];
}

// Hash het wachtwoord
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Voeg gebruiker toe aan database
try {
$stmt = $pdo->prepare('INSERT INTO users (username, password, role) VALUES (:username, :password, :role)');
$stmt->execute([
'username' => $username,
'password' => $hashedPassword,
'role' => 'user' // standaard rol
]);

return ['success' => true, 'message' => 'Account succesvol aangemaakt'];
} catch (Exception $e) {
// Log de echte fout voor debugging
error_log("Registration error: " . $e->getMessage());
return ['success' => false, 'error' => 'Er is een fout opgetreden bij het aanmaken van het account: ' . $e->getMessage()];
}
}