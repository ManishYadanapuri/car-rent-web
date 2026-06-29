<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, DELETE, PUT");
header("Access-Control-Allow-Headers: Content-Type");

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit();
}

require_once 'db.php';

$action = $_GET['action'] ?? '';
$data   = json_decode(file_get_contents("php://input"), true);

// ── ADD CAR ──────────────────────────────────────────────
if ($action === 'add') {
    $required = ['id','name','brand','type','price','image','rating','seats','transmission','fuel'];
    foreach ($required as $field) {
        if (empty($data[$field]) && $data[$field] !== 0) {
            http_response_code(400);
            echo json_encode(["message" => "Missing field: $field"]);
            exit();
        }
    }

    // Check duplicate ID
    $check = $pdo->prepare("SELECT id FROM cars WHERE id = ?");
    $check->execute([$data['id']]);
    if ($check->fetch()) {
        http_response_code(409);
        echo json_encode(["message" => "Car ID already exists. Use a different ID."]);
        exit();
    }

    $stmt = $pdo->prepare("INSERT INTO cars 
        (id, name, brand, type, price, image, badge, rating, seats, transmission, fuel) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $data['id'], $data['name'], $data['brand'], $data['type'],
        $data['price'], $data['image'], $data['badge'] ?? '',
        $data['rating'], $data['seats'], $data['transmission'], $data['fuel']
    ]);

    echo json_encode(["message" => "Car added successfully!"]);
}

// ── EDIT CAR ─────────────────────────────────────────────
elseif ($action === 'edit') {
    $stmt = $pdo->prepare("UPDATE cars SET 
        name=?, brand=?, type=?, price=?, image=?, badge=?, rating=?, seats=?, transmission=?, fuel=?
        WHERE id=?");
    $stmt->execute([
        $data['name'], $data['brand'], $data['type'], $data['price'],
        $data['image'], $data['badge'] ?? '', $data['rating'],
        $data['seats'], $data['transmission'], $data['fuel'], $data['id']
    ]);

    echo json_encode(["message" => "Car updated successfully!"]);
}

// ── DELETE CAR ────────────────────────────────────────────
elseif ($action === 'delete') {
    $id = intval($data['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid car ID"]);
        exit();
    }
    $stmt = $pdo->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(["message" => "Car deleted successfully!"]);
}

// ── UPDATE CAR STATUS ─────────────────────────────────────
elseif ($action === 'update_status') {
    $id     = intval($data['id']     ?? 0);
    $status = trim($data['status']   ?? '');
    $allowed = ['available', 'rented', 'servicing'];

    if (!$id || !in_array($status, $allowed)) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid ID or status"]);
        exit();
    }
    $pdo->prepare("UPDATE cars SET status = ? WHERE id = ?")
        ->execute([$status, $id]);

    // If marking available after service, record last service date
    if ($status === 'available') {
        $pdo->prepare("UPDATE cars SET last_service_date = CURDATE() WHERE id = ? AND status = 'servicing'")
            ->execute([$id]);
    }
    echo json_encode(["message" => "Car status updated to $status"]);
}

else {
    http_response_code(400);
    echo json_encode(["message" => "Invalid action"]);
}
?>