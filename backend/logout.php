<?php
require_once __DIR__ . '/functions_auth.php';
logout();
header('Location: /backend/login.php');
exit;