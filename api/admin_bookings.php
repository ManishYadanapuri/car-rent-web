<?php
// api/admin_bookings.php — Fixed to show all bookings
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

// ── LIST ALL BOOKINGS ────────────────────────────────────
if ($action === 'list') {
    $stmt = $pdo->query("
        SELECT
            b.*,
            p.txn_id,
            p.status   AS pay_status,
            p.amount   AS paid_amount,
            p.payment_method
        FROM bookings b
        LEFT JOIN payments p ON b.booking_ref = p.booking_id
        ORDER BY b.created_at DESC
    ");
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Cast types
    foreach ($bookings as &$bk) {
        $bk['id']           = (int)$bk['id'];
        $bk['duration']     = (int)$bk['duration'];
        $bk['total_amount'] = (float)$bk['total_amount'];
    }
    echo json_encode($bookings);
}

// ── ADD BOOKING ──────────────────────────────────────────
elseif ($action === 'add') {
    $ref = 'LUX-' . strtoupper(substr(uniqid(), -6));
    $stmt = $pdo->prepare("
        INSERT INTO bookings
        (booking_ref, user_id, car_id, car_name, car_brand,
         user_name, user_email,
         pickup_location, pickup_datetime, return_datetime,
         rate_type, duration, total_amount, booking_type, status)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
    ");
    $stmt->execute([
        $ref,
        $data['user_id']        ?? null,
        $data['car_id']         ?? null,
        $data['car_name']       ?? '',
        $data['car_brand']      ?? '',
        $data['user_name']      ?? '',
        $data['user_email']     ?? '',
        $data['pickup_location']?? '',
        $data['pickup_datetime']?? null,
        $data['return_datetime']?? null,
        $data['rate_type']      ?? 'daily',
        $data['duration']       ?? 1,
        $data['total_amount']   ?? 0,
        $data['booking_type']   ?? 'self',
        $data['status']         ?? 'pending'
    ]);
    echo json_encode(["message" => "Booking created!", "ref" => $ref]);
}

// ── UPDATE STATUS ────────────────────────────────────────
elseif ($action === 'update_status') {
    $id     = intval($data['id']     ?? 0);
    $status = trim($data['status']   ?? '');
    $allowed = ['pending','confirmed','active','completed','cancelled'];

    if (!$id || !in_array($status, $allowed)) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid ID or status"]);
        exit();
    }

    $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?")
        ->execute([$status, $id]);

    // If cancelled, also log it
    if ($status === 'cancelled') {
        $bk = $pdo->prepare("SELECT user_email, booking_ref FROM bookings WHERE id = ?");
        $bk->execute([$id]);
        $row = $bk->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $pdo->prepare("INSERT INTO activity_logs (user_email, action, description, ip_address) VALUES (?, 'cancel', ?, ?)")
                ->execute([$row['user_email'], "Booking {$row['booking_ref']} cancelled", $_SERVER['REMOTE_ADDR'] ?? '']);
        }
    }

    echo json_encode(["message" => "Status updated to " . $status]);
}

// ── DELETE BOOKING ───────────────────────────────────────
elseif ($action === 'delete') {
    $id = intval($data['id'] ?? 0);
    if (!$id) {
        http_response_code(400);
        echo json_encode(["message" => "Invalid booking ID"]);
        exit();
    }
    $pdo->prepare("DELETE FROM bookings WHERE id = ?")->execute([$id]);
    echo json_encode(["message" => "Booking deleted!"]);
}

else {
    http_response_code(400);
    echo json_encode(["message" => "Invalid action"]);
}
?>