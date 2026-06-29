<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Method not allowed"]);
    exit();
}

$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
$required = ['booking_id', 'amount', 'payment_method', 'method_detail'];
foreach ($required as $f) {
    if (empty($data[$f])) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Missing: $f"]);
        exit();
    }
}

$booking_id     = trim($data['booking_id']);
$car_name       = trim($data['car_name']       ?? '');
$cust_name      = trim($data['cust_name']      ?? '');
$cust_email     = trim($data['cust_email']     ?? '');
$cust_phone     = trim($data['cust_phone']     ?? '');
$amount         = floatval($data['amount']);
$payment_method = trim($data['payment_method']);
$method_detail  = trim($data['method_detail']);

if ($amount <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid amount"]);
    exit();
}

if (!in_array($payment_method, ['card', 'upi', 'netbanking'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid payment method"]);
    exit();
}

// Generate secure unique transaction ID
$txn_id = 'LUX-TXN-' . strtoupper(dechex(time())) . '-' . strtoupper(bin2hex(random_bytes(4)));

try {
    // Save payment record
    $stmt = $pdo->prepare("
        INSERT INTO payments
        (txn_id, booking_id, car_name, cust_name, cust_email, cust_phone,
         amount, payment_method, method_detail, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'success', NOW())
    ");
    $stmt->execute([$txn_id, $booking_id, $car_name, $cust_name, $cust_email,
                    $cust_phone, $amount, $payment_method, $method_detail]);

    // Update booking status to confirmed
    $pdo->prepare("UPDATE bookings SET status='confirmed' WHERE booking_ref=?")
        ->execute([$booking_id]);

    // Log to activity_logs
    $pdo->prepare("INSERT INTO activity_logs (user_email, action, description, ip_address) VALUES (?, 'payment', ?, ?)")
        ->execute([
            $cust_email ?: 'guest',
            "Payment ₹{$amount} via {$payment_method} — Booking {$booking_id} — TXN: {$txn_id}",
            $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
        ]);

    echo json_encode([
        "success"    => true,
        "message"    => "Payment successful",
        "txn_id"     => $txn_id,
        "booking_id" => $booking_id,
        "amount"     => $amount
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "DB error: " . $e->getMessage()]);
}
?>