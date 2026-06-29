<?php
// api/admin_payments.php  — Add this file to your api/ folder
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit();
}

require_once 'db.php';

$action = $_GET['action'] ?? 'list';
$data   = json_decode(file_get_contents("php://input"), true);

// LIST ALL PAYMENTS
if ($action === 'list') {
    $stmt = $pdo->query("SELECT * FROM payments ORDER BY created_at DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

// STATS
elseif ($action === 'stats') {
    $total     = $pdo->query("SELECT COUNT(*) FROM payments WHERE status='success'")->fetchColumn();
    $revenue   = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='success'")->fetchColumn();
    $today     = $pdo->query("SELECT COUNT(*) FROM payments WHERE DATE(created_at)=CURDATE() AND status='success'")->fetchColumn();
    $todayRev  = $pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE DATE(created_at)=CURDATE() AND status='success'")->fetchColumn();
    $byMethod  = $pdo->query("SELECT payment_method, COUNT(*) as count, SUM(amount) as total FROM payments WHERE status='success' GROUP BY payment_method")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(compact('total','revenue','today','todayRev','byMethod'));
}

// DELETE
elseif ($action === 'delete') {
    $pdo->prepare("DELETE FROM payments WHERE id=?")->execute([$data['id']]);
    echo json_encode(["message" => "Deleted"]);
}

else {
    http_response_code(400);
    echo json_encode(["message" => "Invalid action"]);
}
?>