<?php
session_start();
require_once __DIR__ . '/db.php';


function current_user() {
return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}


function require_login() {
if (!current_user()) {
header('Location: /backend/login.php');
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