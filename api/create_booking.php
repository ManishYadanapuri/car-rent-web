<?php
// api/create_booking.php — Save booking to database
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

// Get user_id from session if logged in
$user_id = $_SESSION['user_id'] ?? null;

$booking_ref     = trim($data['booking_ref']     ?? '');
$car_id          = intval($data['car_id']         ?? 0);
$car_name        = trim($data['car_name']         ?? '');
$car_brand       = trim($data['car_brand']        ?? '');
$pickup_location = trim($data['pickup_location']  ?? '');
$rate_type       = trim($data['rate_type']        ?? 'daily');
$duration        = intval($data['duration']        ?? 1);
$total_amount    = floatval($data['total_amount']  ?? 0);
$booking_type    = trim($data['booking_type']      ?? 'self');
$cust_name       = trim($data['cust_name']         ?? '');
$cust_email      = trim($data['cust_email']        ?? '');
$cust_phone      = trim($data['cust_phone']        ?? '');

if (!$booking_ref || !$car_name || !$cust_name || !$cust_email) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit();
}

// Check if booking_ref already exists
$check = $pdo->prepare("SELECT id FROM bookings WHERE booking_ref = ?");
$check->execute([$booking_ref]);
if ($check->fetch()) {
    // Already saved — just return success
    echo json_encode(["success" => true, "message" => "Booking already exists"]);
    exit();
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO bookings
        (booking_ref, user_id, car_id, car_name, car_brand,
         user_name, user_email,
         pickup_location, rate_type, duration,
         total_amount, booking_type, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
    ");
    $stmt->execute([
        $booking_ref, $user_id, $car_id, $car_name, $car_brand,
        $cust_name, $cust_email,
        $pickup_location, $rate_type, $duration,
        $total_amount, $booking_type
    ]);

    echo json_encode([
        "success"     => true,
        "message"     => "Booking saved successfully",
        "booking_ref" => $booking_ref
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "DB error: " . $e->getMessage()]);
}
?>