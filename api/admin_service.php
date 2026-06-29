<?php
// api/admin_service.php — Service module
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

// ── LIST: all cars with their status ─────────────────────
if ($action === 'list') {
    $stmt = $pdo->query("
        SELECT id, name, brand, type, image, status,
               rental_count, rating,
               service_notes, last_service_date, next_service_date
        FROM cars ORDER BY
          FIELD(status,'servicing','available','rented'), name ASC
    ");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}

// ── SEND CAR TO SERVICE ───────────────────────────────────
elseif ($action === 'send_to_service') {
    $id    = intval($data['id']    ?? 0);
    $notes = trim($data['notes']   ?? '');
    $next  = trim($data['next_service_date'] ?? '');

    if (!$id) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid car ID"]);
        exit();
    }

    $pdo->prepare("
        UPDATE cars
        SET status = 'servicing',
            service_notes = ?,
            next_service_date = ?
        WHERE id = ?
    ")->execute([$notes ?: null, $next ?: null, $id]);

    echo json_encode(["message" => "Car sent to service successfully."]);
}

// ── MARK SERVICE COMPLETE ─────────────────────────────────
elseif ($action === 'complete_service') {
    $id   = intval($data['id'] ?? 0);
    $next = trim($data['next_service_date'] ?? '');

    if (!$id) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid car ID"]);
        exit();
    }

    $pdo->prepare("
        UPDATE cars
        SET status = 'available',
            last_service_date = CURDATE(),
            next_service_date = ?,
            service_notes = NULL
        WHERE id = ?
    ")->execute([$next ?: null, $id]);

    echo json_encode(["message" => "Service marked complete. Car is now available."]);
}

// ── FLAG: mark car as needs-service (keeps available, just flags) ─
elseif ($action === 'flag') {
    $id    = intval($data['id']  ?? 0);
    $notes = trim($data['notes'] ?? 'Flagged for upcoming service');
    $next  = trim($data['next_service_date'] ?? '');

    $pdo->prepare("
        UPDATE cars SET service_notes = ?, next_service_date = ?
        WHERE id = ?
    ")->execute([$notes, $next ?: null, $id]);

    echo json_encode(["message" => "Car flagged for service."]);
}

// ── STATS: summary for the service widget ────────────────
elseif ($action === 'stats') {
    $inService  = $pdo->query("SELECT COUNT(*) FROM cars WHERE status='servicing'")->fetchColumn();
    $available  = $pdo->query("SELECT COUNT(*) FROM cars WHERE status='available'")->fetchColumn();
    $overdue    = $pdo->query("
        SELECT COUNT(*) FROM cars
        WHERE next_service_date IS NOT NULL
          AND next_service_date < CURDATE()
          AND status != 'servicing'
    ")->fetchColumn();
    $dueSoon    = $pdo->query("
        SELECT COUNT(*) FROM cars
        WHERE next_service_date IS NOT NULL
          AND next_service_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
          AND status != 'servicing'
    ")->fetchColumn();

    $servicingCars = $pdo->query("
        SELECT id, CONCAT(brand,' ',name) AS car_name, service_notes, next_service_date
        FROM cars WHERE status='servicing'
    ")->fetchAll(PDO::FETCH_ASSOC);

    $overdueCars = $pdo->query("
        SELECT id, CONCAT(brand,' ',name) AS car_name, next_service_date
        FROM cars
        WHERE next_service_date < CURDATE() AND status != 'servicing'
        ORDER BY next_service_date ASC LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'in_service'    => (int)$inService,
        'available'     => (int)$available,
        'overdue'       => (int)$overdue,
        'due_soon'      => (int)$dueSoon,
        'servicing_cars'=> $servicingCars,
        'overdue_cars'  => $overdueCars,
    ]);
}

else {
    http_response_code(400);
    echo json_encode(["message" => "Invalid action"]);
}
?>