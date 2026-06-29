<?php
// api/razorpay_verify.php — Verifies Razorpay payment signature
session_start();
header("Content-Type: application/json");
require_once 'db.php';

define('RAZORPAY_KEY_SECRET', 'XXXXXXXXXXXXXXXXXXXXXXXX'); // ← same secret as above

$data = json_decode(file_get_contents("php://input"), true);

$razorpay_order_id   = $data['razorpay_order_id']   ?? '';
$razorpay_payment_id = $data['razorpay_payment_id'] ?? '';
$razorpay_signature  = $data['razorpay_signature']  ?? '';
$booking_id          = $data['booking_id']           ?? '';
$amount              = floatval($data['amount']      ?? 0);
$payment_method      = $data['payment_method']       ?? 'razorpay';
$car_name            = $data['car_name']             ?? '';
$cust_name           = $data['cust_name']            ?? '';
$cust_email          = $data['cust_email']           ?? '';
$cust_phone          = $data['cust_phone']           ?? '';

// ── VERIFY SIGNATURE ─────────────────────────────────────
$generated_sig = hash_hmac(
    'sha256',
    $razorpay_order_id . '|' . $razorpay_payment_id,
    RAZORPAY_KEY_SECRET
);

if (!hash_equals($generated_sig, $razorpay_signature)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Payment verification failed — invalid signature."]);
    exit();
}

// ── SIGNATURE VALID → SAVE TO DB ────────────────────────
$txn_id = 'LUX-RZP-' . strtoupper(substr($razorpay_payment_id, -8));

try {
    // Save payment
    $pdo->prepare("
        INSERT INTO payments (txn_id,booking_id,car_name,cust_name,cust_email,cust_phone,amount,payment_method,method_detail,status)
        VALUES (?,?,?,?,?,?,?,?,?,?)
    ")->execute([
        $txn_id, $booking_id, $car_name, $cust_name, $cust_email, $cust_phone,
        $amount, 'razorpay', 'Razorpay: '.$razorpay_payment_id, 'success'
    ]);

    // Update booking status
    $pdo->prepare("UPDATE bookings SET status='confirmed' WHERE booking_ref=?")
        ->execute([$booking_id]);

    // Log activity
    $pdo->prepare("INSERT INTO activity_logs (user_email,action,description,ip_address) VALUES (?,'payment',?,?)")
        ->execute([$cust_email, "Razorpay payment ₹{$amount} for {$booking_id}", $_SERVER['REMOTE_ADDR'] ?? '']);

    echo json_encode([
        "success"    => true,
        "txn_id"     => $txn_id,
        "payment_id" => $razorpay_payment_id,
        "message"    => "Payment verified and booking confirmed!"
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "DB error: " . $e->getMessage()]);
}
?>
