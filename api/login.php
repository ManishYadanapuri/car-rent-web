<?php
// api/login.php — Session-based secure login
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
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["message" => "Method not allowed"]);
    exit();
}

$data     = json_decode(file_get_contents("php://input"), true);
$email    = trim($data['email']    ?? '');
$password = $data['password']      ?? '';

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(["message" => "Email and password are required."]);
    exit();
}

// Fetch user
$stmt = $pdo->prepare("SELECT id, full_name, email, phone, password, status FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(["message" => "Invalid email or password."]);
    exit();
}

if ($user['status'] === 'suspended') {
    http_response_code(403);
    echo json_encode(["message" => "Your account has been suspended. Please contact support."]);
    exit();
}

// ── SET PHP SESSION ──────────────────────────────────────
session_regenerate_id(true); // Prevent session fixation
$_SESSION['user_id']    = $user['id'];
$_SESSION['user_name']  = $user['full_name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['logged_in']  = true;

// Update last_login and login_count
$pdo->prepare("UPDATE users SET last_login = NOW(), login_count = login_count + 1 WHERE id = ?")
    ->execute([$user['id']]);

// Log activity
$pdo->prepare("INSERT INTO activity_logs (user_id, user_email, action, description, ip_address) VALUES (?,?,'login','User logged in',?)")
    ->execute([$user['id'], $user['email'], $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']);

unset($user['password']);
http_response_code(200);
	echo json_encode([
	    "status"   => "success",
	    "message"  => "Login successful!",
	    "redirect" => "index.html",
	    "user"     => [
	        "id"    => $user['id'],
	        "name"  => $user['full_name'],
	        "email" => $user['email'],
	        "phone" => $user['phone']
	    ]
	]);
?>