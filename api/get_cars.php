<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

require_once 'db.php';

$stmt = $pdo->query("SELECT * FROM cars ORDER BY id ASC");
$cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($cars as &$car) {
    $car['id']           = (int)$car['id'];
    $car['price']        = (int)$car['price'];
    $car['rating']       = (float)$car['rating'];
    $car['seats']        = (int)$car['seats'];
    $car['rental_count'] = (int)($car['rental_count'] ?? 0);
    $car['status']       = $car['status'] ?? 'available';
}

echo json_encode($cars);
?>