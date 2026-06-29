<?php
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once '../api/db.php';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username']  = $admin['username'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>LuxRide Admin Login</title>
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      min-height: 100vh;
      display: flex; align-items: center; justify-content: center;
      font-family: 'Segoe UI', sans-serif;
      position: relative;
      overflow: hidden;
    }

    /* ── BACKGROUND IMAGE ── */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image: url('../bgpic/bg9.webp'); /* 🔁 Change this path to any car image you like */
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      transform: scale(1.05); /* slight zoom so edges don't show on blur */
      filter: blur(3px) brightness(1.1);
      z-index: 0;
    }

    /* ── DARK GRADIENT OVERLAY on top of image ── */
    body::after {
      content: '';
      position: fixed;
      inset: 0;
      background: linear-gradient(135deg,
        rgba(10, 5, 30, 0.75) 0%,
        rgba(30, 15, 60, 0.65) 50%,
        rgba(10, 20, 40, 0.8) 100%
      );
      z-index: 1;
    }

    /* ── CARD ── */
    .card {
      position: relative;
      z-index: 2;
      background: rgba(255, 255, 255, 0.07);
      backdrop-filter: blur(24px);
      -webkit-backdrop-filter: blur(24px);
      border: 1px solid rgba(255, 255, 255, 0.13);
      border-radius: 24px;
      padding: 3rem 2.5rem;
      width: 100%; max-width: 420px;
      box-shadow: 0 30px 70px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(255,255,255,0.05);
    }

    .logo {
      text-align: center; font-size: 2rem;
      font-weight: 800; color: #fff; margin-bottom: 0.4rem;
      letter-spacing: -0.5px;
    }
    .logo span { color: #a78bfa; }

    .subtitle {
      text-align: center; color: rgba(255,255,255,0.45);
      font-size: 0.85rem; margin-bottom: 2rem;
    }

    label {
      display: block; color: rgba(255,255,255,0.7);
      font-size: 0.825rem; margin-bottom: 0.4rem; font-weight: 500;
    }

    input {
      width: 100%; padding: 0.75rem 1rem;
      background: rgba(255, 255, 255, 0.08);
      border: 1px solid rgba(255, 255, 255, 0.15);
      border-radius: 12px; color: #fff; font-size: 1rem;
      margin-bottom: 1.25rem; outline: none; transition: border 0.2s, background 0.2s;
    }
    input:focus {
      border-color: #a78bfa;
      background: rgba(255, 255, 255, 0.12);
    }
    input::placeholder { color: rgba(255,255,255,0.3); }

    button {
      width: 100%; padding: 0.875rem;
      background: linear-gradient(135deg, #7c3aed, #a78bfa);
      color: #fff; border: none; border-radius: 12px;
      font-size: 1rem; font-weight: 700; cursor: pointer;
      transition: opacity 0.2s, transform 0.15s;
      letter-spacing: 0.02em;
    }
    button:hover { opacity: 0.9; transform: translateY(-1px); }
    button:active { transform: translateY(0); }

    .error {
      background: rgba(239, 68, 68, 0.15);
      border: 1px solid rgba(239, 68, 68, 0.35);
      color: #fca5a5; padding: 0.75rem 1rem; border-radius: 10px;
      margin-bottom: 1.25rem; font-size: 0.85rem; text-align: center;
    }

    /* little brand watermark at bottom */
    .watermark {
      position: fixed; bottom: 1.25rem; left: 50%; transform: translateX(-50%);
      color: rgba(255,255,255,0.2); font-size: 0.75rem; z-index: 2;
      white-space: nowrap;
    }
  </style>
</head>
<body>

<div class="card">
  <div class="logo">✨ Lux<span>Ride</span></div>
  <p class="subtitle">Admin Panel — Sign in to manage cars</p>

  <?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST">
    <label>Username</label>
    <input type="text" name="username" placeholder="Enter username" required autofocus>
    <label>Password</label>
    <input type="password" name="password" placeholder="Enter password" required>
    <button type="submit">Sign In →</button>
  </form>
</div>

<div class="watermark">© 2026 LuxRide · Admin Portal</div>

</body>
</html>