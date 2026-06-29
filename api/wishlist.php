<?php
// api/wishlist.php — Toggle & manage wishlist
session_start();
header("Content-Type: application/json");

if (!isset($_SESSION['logged_in'])) {
    http_response_code(401);
    echo json_encode(["message" => "Please login to use wishlist"]);
    exit();
}

require_once 'db.php';

$action = $_GET['action'] ?? 'toggle';
$data   = json_decode(file_get_contents("php://input"), true);
$uid    = $_SESSION['user_id'];
$car_id = intval($data['car_id'] ?? 0);

// ── CREATE TABLE IF NEEDED ───────────────────────────────
$pdo->exec("CREATE TABLE IF NOT EXISTS wishlist (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    car_id  INT NOT NULL,
    added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uk_user_car (user_id, car_id)
)");

if ($action === 'toggle') {
    $exists = $pdo->prepare("SELECT id FROM wishlist WHERE user_id=? AND car_id=?");
    $exists->execute([$uid, $car_id]);

    if ($exists->fetch()) {
        $pdo->prepare("DELETE FROM wishlist WHERE user_id=? AND car_id=?")->execute([$uid, $car_id]);
        echo json_encode(["message" => "Removed from wishlist", "wishlisted" => false]);
    } else {
        $pdo->prepare("INSERT INTO wishlist (user_id, car_id) VALUES (?,?)")->execute([$uid, $car_id]);
        echo json_encode(["message" => "Added to wishlist!", "wishlisted" => true]);
    }
}

elseif ($action === 'remove') {
    $pdo->prepare("DELETE FROM wishlist WHERE user_id=? AND car_id=?")->execute([$uid, $car_id]);
    echo json_encode(["message" => "Removed from wishlist"]);
}

elseif ($action === 'list') {
    $stmt = $pdo->prepare("SELECT w.car_id FROM wishlist w WHERE w.user_id=?");
    $stmt->execute([$uid]);
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo json_encode(["car_ids" => $ids]);
}

elseif ($action === 'check') {
    $stmt = $pdo->prepare("SELECT id FROM wishlist WHERE user_id=? AND car_id=?");
    $stmt->execute([$uid, $car_id]);
    echo json_encode(["wishlisted" => (bool)$stmt->fetch()]);
}
?>
