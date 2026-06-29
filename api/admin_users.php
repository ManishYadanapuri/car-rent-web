<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Access-Control-Allow-Headers: Content-Type");

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit();
}

require_once 'db.php';

$action = $_GET['action'] ?? 'list';
$data   = json_decode(file_get_contents("php://input"), true);

// LIST USERS
if ($action === 'list') {
    $stmt = $pdo->query("SELECT id, full_name, email, phone, status, login_count, last_login, created_at FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($users);
}

// GET SINGLE USER
elseif ($action === 'get') {
    $id = intval($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT id, full_name, email, phone, status, login_count, last_login, created_at FROM users WHERE id=?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    echo json_encode($user ?: []);
}

// ADD USER
elseif ($action === 'add') {
    if (empty($data['full_name']) || empty($data['email']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(["message" => "Name, email, and password required"]);
        exit();
    }
    $check = $pdo->prepare("SELECT id FROM users WHERE email=?");
    $check->execute([$data['email']]);
    if ($check->fetch()) {
        http_response_code(409);
        echo json_encode(["message" => "Email already exists"]);
        exit();
    }
    $hash = password_hash($data['password'], PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password, status) VALUES (?,?,?,?,?)");
    $stmt->execute([$data['full_name'], $data['email'], $data['phone'] ?? '', $hash, $data['status'] ?? 'active']);
    echo json_encode(["message" => "User created successfully!"]);
}

// EDIT USER
elseif ($action === 'edit') {
    $id = intval($data['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(["message" => "Invalid user ID"]); exit(); }

    if (!empty($data['password'])) {
        $hash = password_hash($data['password'], PASSWORD_BCRYPT);
        $stmt = $pdo->prepare("UPDATE users SET full_name=?, email=?, phone=?, status=?, password=? WHERE id=?");
        $stmt->execute([$data['full_name'], $data['email'], $data['phone'] ?? '', $data['status'], $hash, $id]);
    } else {
        $stmt = $pdo->prepare("UPDATE users SET full_name=?, email=?, phone=?, status=? WHERE id=?");
        $stmt->execute([$data['full_name'], $data['email'], $data['phone'] ?? '', $data['status'], $id]);
    }
    echo json_encode(["message" => "User updated successfully!"]);
}

// DELETE USER
elseif ($action === 'delete') {
    $id = intval($data['id'] ?? 0);
    if (!$id) { http_response_code(400); echo json_encode(["message" => "Invalid ID"]); exit(); }
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=?");
    $stmt->execute([$id]);
    echo json_encode(["message" => "User deleted!"]);
}

// TOGGLE STATUS (suspend/activate)
elseif ($action === 'toggle_status') {
    $id = intval($data['id'] ?? 0);
    $stmt = $pdo->prepare("UPDATE users SET status = IF(status='active','suspended','active') WHERE id=?");
    $stmt->execute([$id]);
    $newStatus = $pdo->prepare("SELECT status FROM users WHERE id=?");
    $newStatus->execute([$id]);
    $s = $newStatus->fetchColumn();
    echo json_encode(["message" => "User " . ($s === 'active' ? 'activated' : 'suspended') . "!", "status" => $s]);
}

else {
    http_response_code(400);
    echo json_encode(["message" => "Invalid action"]);
}
?>