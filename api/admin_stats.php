<?php
session_start();
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(401);
    echo json_encode(["message" => "Unauthorized"]);
    exit();
}

require_once 'db.php';

$action = $_GET['action'] ?? 'overview';

if ($action === 'overview') {
    // Users stats
    $totalUsers     = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $activeUsers    = $pdo->query("SELECT COUNT(*) FROM users WHERE status='active'")->fetchColumn();
    $suspendedUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE status='suspended'")->fetchColumn();
    $newUsersToday  = $pdo->query("SELECT COUNT(*) FROM users WHERE DATE(created_at)=CURDATE()")->fetchColumn();
    $newUsersWeek   = $pdo->query("SELECT COUNT(*) FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)")->fetchColumn();

    // Cars stats
    $totalCars   = $pdo->query("SELECT COUNT(*) FROM cars")->fetchColumn();
    $carTypes    = $pdo->query("SELECT COUNT(DISTINCT type) FROM cars")->fetchColumn();
    $avgPrice    = $pdo->query("SELECT ROUND(AVG(price),0) FROM cars")->fetchColumn();
    $mostExpensive = $pdo->query("SELECT CONCAT(brand,' ',name) FROM cars ORDER BY price DESC LIMIT 1")->fetchColumn();

    // Bookings stats
    $totalBookings    = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
    $pendingBookings  = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='pending'")->fetchColumn();
    $activeBookings   = $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='active'")->fetchColumn();
    $completedBookings= $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='completed'")->fetchColumn();
    $cancelledBookings= $pdo->query("SELECT COUNT(*) FROM bookings WHERE status='cancelled'")->fetchColumn();
    $totalRevenue     = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM bookings WHERE status='completed'")->fetchColumn();
    $pendingRevenue   = $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM bookings WHERE status IN ('pending','confirmed','active')")->fetchColumn();
    $todayBookings    = $pdo->query("SELECT COUNT(*) FROM bookings WHERE DATE(created_at)=CURDATE()")->fetchColumn();

    // Recent logins
    $recentLogins = $pdo->query("SELECT user_email, description, ip_address, created_at FROM activity_logs WHERE action='login' ORDER BY created_at DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);

    // Car type distribution
    $carTypeData = $pdo->query("SELECT type, COUNT(*) as count FROM cars GROUP BY type ORDER BY count DESC")->fetchAll(PDO::FETCH_ASSOC);

    // Revenue by month (last 6 months)
    $revenueByMonth = $pdo->query("
        SELECT DATE_FORMAT(created_at,'%b %Y') as month, 
               COALESCE(SUM(total_amount),0) as revenue,
               COUNT(*) as bookings
        FROM bookings 
        WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH) AND status='completed'
        GROUP BY DATE_FORMAT(created_at,'%Y-%m')
        ORDER BY created_at ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Top booked cars
    $topCars = $pdo->query("
        SELECT car_name, car_brand, COUNT(*) as bookings, SUM(total_amount) as revenue
        FROM bookings GROUP BY car_id, car_name, car_brand ORDER BY bookings DESC LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);

    // Users registered per day (last 7 days)
    $userGrowth = $pdo->query("
        SELECT DATE_FORMAT(created_at,'%a') as day, COUNT(*) as count
        FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(created_at) ORDER BY created_at ASC
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'users'    => compact('totalUsers','activeUsers','suspendedUsers','newUsersToday','newUsersWeek'),
        'cars'     => compact('totalCars','carTypes','avgPrice','mostExpensive'),
        'bookings' => compact('totalBookings','pendingBookings','activeBookings','completedBookings','cancelledBookings','todayBookings'),
        'revenue'  => ['total' => $totalRevenue, 'pending' => $pendingRevenue],
        'charts'   => compact('carTypeData','revenueByMonth','topCars','userGrowth'),
        'recentLogins' => $recentLogins
    ]);
}
?>