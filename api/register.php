<?php
// api/register.php — Secure registration
// Secure session cookie settings for modern browsers (Chrome/Brave)
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false, // Set to true if using HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'db.php';

$data     = json_decode(file_get_contents("php://input"), true);
$name     = trim($data['name']     ?? '');
$email    = trim($data['email']    ?? '');
$phone    = trim($data['phone']    ?? '');
$password = $data['password']      ?? '';

if (!$name || !$email || !$password) {
    http_response_code(400);
    echo json_encode(["message" => "Name, email and password are required."]);
    exit();
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["message" => "Invalid email format."]);
    exit();
}
if (strlen($password) < 8) {
    http_response_code(400);
    echo json_encode(["message" => "Password must be at least 8 characters."]);
    exit();
}

// Duplicate check
$chk = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$chk->execute([$email]);
if ($chk->fetch()) {
    http_response_code(409);
    echo json_encode(["message" => "An account with this email already exists."]);
    exit();
}

$hash = password_hash($password, PASSWORD_BCRYPT);
$stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password) VALUES (?,?,?,?)");
$stmt->execute([$name, $email, $phone, $hash]);
$userId = $pdo->lastInsertId();

// ── AUTO LOGIN AFTER REGISTER ──────────────────────────
session_regenerate_id(true);
$_SESSION['user_id']    = $userId;
$_SESSION['user_name']  = $name;
$_SESSION['user_email'] = $email;
$_SESSION['logged_in']  = true;

// Log
$pdo->prepare("INSERT INTO activity_logs (user_id,user_email,action,description,ip_address) VALUES (?,?,'register','New user registered',?)")
    ->execute([$userId, $email, $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);

http_response_code(201);
echo json_encode([
    "message" => "Account created successfully!",
    "user"    => ["id" => $userId, "name" => $name, "email" => $email]
]);
?>