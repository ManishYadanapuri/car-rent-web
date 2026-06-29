<?php
// api/change_password.php
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['logged_in'])) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit();
}

require_once 'db.php';

$data    = json_decode(file_get_contents("php://input"), true);
$id      = $_SESSION['user_id'];
$current = $data['current'] ?? '';
$newpass = $data['newpass']  ?? '';

if (strlen($newpass) < 8) {
    http_response_code(400);
    echo json_encode(["message" => "New password must be at least 8 characters."]);
    exit();
}

$stmt = $pdo->prepare("SELECT password FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($current, $user['password'])) {
    http_response_code(401);
    echo json_encode(["message" => "Current password is incorrect."]);
    exit();
}

$hash = password_hash($newpass, PASSWORD_BCRYPT);
$pdo->prepare("UPDATE users SET password=? WHERE id=?")->execute([$hash, $id]);

echo json_encode(["message" => "Password updated successfully!"]);
?>
