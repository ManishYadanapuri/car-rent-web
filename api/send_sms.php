<?php
// api/send_sms.php — Send SMS after payment
header("Content-Type: application/json");

// ── PASTE YOUR FAST2SMS KEY HERE ──────────────────────────
define('FAST2SMS_KEY', '6RozTprvx7wGQdHnIBDf2MqjA9KStNYXb1JWskc3gmCuaheZUENiIuY5OEJsCfLtxTbWhyw28ZpQ1c4V');

$data    = json_decode(file_get_contents("php://input"), true);
$phone   = preg_replace('/\D/', '', $data['phone']    ?? ''); // digits only
$message = $data['message'] ?? '';

if (!$phone || !$message) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Phone and message required"]);
    exit();
}

// Fast2SMS API call
$ch = curl_init('https://www.fast2sms.com/dev/bulkV2');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => json_encode([
        "route"   => "q",        // Quick route (transactional)
        "message" => $message,
        "numbers" => $phone,
        "flash"   => 0
    ]),
    CURLOPT_HTTPHEADER => [
        'authorization: ' . FAST2SMS_KEY,
        'Content-Type: application/json'
    ]
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

$result = json_decode($response, true);

if ($result['return'] === true) {
    echo json_encode(["success" => true, "message" => "SMS sent successfully!"]);
} else {
    echo json_encode(["success" => false, "message" => "SMS failed", "error" => $result]);
}
?>