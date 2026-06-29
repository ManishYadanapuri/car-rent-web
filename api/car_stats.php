<?php
require_once 'db.php';
header("Content-Type: application/json");
header("Cache-Control: no-store");

try {
    $total     = $pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn();
    $available = $pdo->query("SELECT COUNT(*) FROM cars WHERE status='available'")->fetchColumn();
    $rented    = $pdo->query("SELECT COUNT(*) FROM cars WHERE status='rented'")->fetchColumn();
    $servicing = $pdo->query("SELECT COUNT(*) FROM cars WHERE status='servicing'")->fetchColumn();
    $rating    = $pdo->query("SELECT ROUND(AVG(rating),1) FROM cars")->fetchColumn();

    $best = $pdo->query("
        SELECT CONCAT(brand,' ',name) FROM cars ORDER BY rental_count DESC LIMIT 1
    ")->fetchColumn();

    // Names of cars currently in service (for tooltip on front page)
    $servicingCars = $pdo->query("
        SELECT CONCAT(brand,' ',name) AS name FROM cars WHERE status='servicing' LIMIT 5
    ")->fetchAll(PDO::FETCH_COLUMN);

    // Revenue from completed bookings
    $revenue = $pdo->query("
        SELECT COALESCE(SUM(total_amount),0) FROM bookings WHERE status='completed'
    ")->fetchColumn();

    // Fleet utilisation %
    $utilisation = $total > 0 ? round(($rented / $total) * 100) : 0;

    echo json_encode([
        'total'          => (int)$total,
        'available'      => (int)$available,
        'rented'         => (int)$rented,
        'servicing'      => (int)$servicing,
        'servicing_cars' => $servicingCars,
        'avg_rating'     => $rating ?: '4.9',
        'best_car'       => $best  ?: 'Ferrari F8',
        'revenue'        => (float)$revenue,
        'utilisation'    => $utilisation,
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>