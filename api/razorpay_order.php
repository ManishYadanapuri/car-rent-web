<?php
// api/razorpay_order.php — Creates Razorpay order
session_start();
header("Content-Type: application/json");
require_once 'db.php';

// ── RAZORPAY CONFIG ─────────────────────────────────────
// STEP: Get these from https://dashboard.razorpay.com/
// Sign up → Settings → API Keys → Generate Test Key
define('RAZORPAY_KEY_ID',     'rzp_test_XXXXXXXXXXXXXXX'); // ← paste your Key ID here
define('RAZORPAY_KEY_SECRET', 'XXXXXXXXXXXXXXXXXXXXXXXX'); // ← paste your Key Secret here

$data       = json_decode(file_get_contents("php://input"), true);
$amount     = intval(floatval($data['amount'] ?? 0) * 100); // Razorpay needs PAISE
$booking_id = trim($data['booking_id'] ?? '');
$currency   = 'INR';

if ($amount <= 0 || !$booking_id) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Invalid amount or booking ID"]);
    exit();
}

// ── CREATE RAZORPAY ORDER ────────────────────────────────
$orderData = [
    'receipt'  => $booking_id,
    'amount'   => $amount,
    'currency' => $currency,
    'notes'    => ['booking_id' => $booking_id, 'source' => 'LuxRide']
];

$ch = curl_init('https://api.razorpay.com/v1/orders');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode($orderData),
    CURLOPT_USERPWD        => RAZORPAY_KEY_ID . ':' . RAZORPAY_KEY_SECRET,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json']
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$order = json_decode($response, true);

if ($httpCode !== 200 || !isset($order['id'])) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Failed to create Razorpay order", "error" => $order]);
    exit();
}

echo json_encode([
    "success"    => true,
    "order_id"   => $order['id'],
    "key_id"     => RAZORPAY_KEY_ID,
    "amount"     => $amount,
    "currency"   => $currency,
    "booking_id" => $booking_id
]);
?>
