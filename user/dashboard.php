<?php
// user/dashboard.php
session_start();

// Auth check — redirect if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../signinUP.html?msg=login_required");
    exit();
}

// Logout handler
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ../signinUP.html");
    exit();
}

require_once '../api/db.php';

$uid   = $_SESSION['user_id'];
$uname = $_SESSION['user_name'];
$uemail= $_SESSION['user_email'];

// ── FETCH USER ────────────────────────────────────────────
$uStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$uStmt->execute([$uid]);
$user  = $uStmt->fetch(PDO::FETCH_ASSOC);

// ── FETCH ALL BOOKINGS ────────────────────────────────────
$bStmt = $pdo->prepare("
    SELECT b.*, p.txn_id, p.status as pay_status, p.amount as paid_amount
    FROM bookings b
    LEFT JOIN payments p ON b.booking_ref = p.booking_id
    WHERE b.user_id = ? OR b.user_email = ?
    ORDER BY b.created_at DESC
");
$bStmt->execute([$uid, $uemail]);
$allBookings = $bStmt->fetchAll(PDO::FETCH_ASSOC);

// ── FETCH WISHLIST ────────────────────────────────────────
$wStmt = $pdo->prepare("
    SELECT w.car_id, c.name, c.brand, c.image, c.price, c.type, c.rating, c.seats, c.fuel
    FROM wishlist w
    JOIN cars c ON w.car_id = c.id
    WHERE w.user_id = ?
    ORDER BY w.added_at DESC
");
$wStmt->execute([$uid]);
$wishlist = $wStmt->fetchAll(PDO::FETCH_ASSOC);

// ── COUNTS ────────────────────────────────────────────────
$active    = array_filter($allBookings, fn($b) => in_array($b['status'], ['pending','confirmed','active']));
$past      = array_filter($allBookings, fn($b) => in_array($b['status'], ['completed','cancelled']));
$confirmed = array_filter($allBookings, fn($b) => $b['status'] === 'confirmed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LuxRide — My Account</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    :root {
      --bg:#f8fafc; --bg2:#ffffff; --bg3:#f1f5f9;
      --border:#e2e8f0; --accent:#1e40af; --amber:#f59e0b;
      --text:#0f172a; --muted:#64748b; --green:#16a34a;
      --red:#dc2626; --r:14px;
    }
    [data-theme="dark"] {
      --bg:#0f172a; --bg2:#1e293b; --bg3:#0f172a;
      --border:#334155; --text:#f1f5f9; --muted:#94a3b8; --bg3:#1e293b;
    }
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:system-ui,sans-serif; background:var(--bg); color:var(--text); min-height:100vh; }

    /* NAV */
    .topnav {
      background:var(--bg2); border-bottom:1px solid var(--border);
      padding:.85rem 2rem; display:flex; align-items:center;
      justify-content:space-between; position:sticky; top:0; z-index:100;
    }
    .logo { font-size:1.3rem; font-weight:800; color:var(--text); text-decoration:none; }
    .logo span { color:var(--amber); }
    .nav-right { display:flex; align-items:center; gap:.75rem; flex-wrap:wrap; }
    .nbtn { padding:.4rem .9rem; border-radius:8px; font-size:.82rem; font-weight:600; cursor:pointer; border:none; text-decoration:none; transition:all .2s; }
    .nbtn-ghost { background:transparent; border:1px solid var(--border); color:var(--muted); }
    .nbtn-ghost:hover { border-color:var(--accent); color:var(--accent); }
    .nbtn-danger { background:rgba(220,38,38,.08); color:var(--red); border:1px solid rgba(220,38,38,.2); }
    .nbtn-danger:hover { background:rgba(220,38,38,.15); }
    .theme-btn { background:none; border:none; cursor:pointer; font-size:1.1rem; color:var(--muted); }

    /* LAYOUT */
    .layout { display:grid; grid-template-columns:240px 1fr; min-height:calc(100vh - 57px); }

    /* SIDEBAR */
    .sidebar { background:var(--bg2); border-right:1px solid var(--border); padding:1.25rem .75rem; }
    .user-pill {
      text-align:center; padding:1.25rem .5rem 1rem;
      border-bottom:1px solid var(--border); margin-bottom:.75rem;
    }
    .avatar {
      width:60px; height:60px; border-radius:50%; margin:0 auto .75rem;
      background:linear-gradient(135deg,var(--accent),var(--amber));
      display:flex; align-items:center; justify-content:center;
      font-size:1.4rem; font-weight:800; color:#fff;
    }
    .uname { font-weight:700; font-size:.95rem; }
    .uemail { font-size:.72rem; color:var(--muted); margin-top:.15rem; word-break:break-all; }
    .slink {
      display:flex; align-items:center; gap:.65rem;
      padding:.6rem .85rem; border-radius:10px;
      text-decoration:none; color:var(--muted);
      font-size:.86rem; font-weight:500;
      transition:all .2s; cursor:pointer; margin-bottom:.1rem;
    }
    .slink:hover, .slink.active { background:var(--bg3); color:var(--accent); }
    .slink i { width:16px; text-align:center; }
    .sbadge {
      margin-left:auto; background:var(--accent); color:#fff;
      font-size:.62rem; font-weight:800; padding:.1rem .4rem; border-radius:20px;
    }
    .sbadge.red { background:var(--red); }

    /* CONTENT */
    .content { padding:2rem; overflow-y:auto; }
    .sec { display:none; animation:fadeIn .25s ease; }
    .sec.active { display:block; }
    @keyframes fadeIn { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:none} }

    /* STAT CARDS */
    .stats { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.75rem; }
    .stat-card {
      background:var(--bg2); border:1px solid var(--border);
      border-radius:var(--r); padding:1.1rem 1.25rem; position:relative; overflow:hidden;
    }
    .stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; }
    .sc-blue::before   { background:var(--accent); }
    .sc-green::before  { background:var(--green); }
    .sc-amber::before  { background:var(--amber); }
    .sc-red::before    { background:var(--red); }
    .stat-lbl { font-size:.68rem; text-transform:uppercase; letter-spacing:.08em; color:var(--muted); font-weight:700; margin-bottom:.35rem; }
    .stat-num { font-size:1.9rem; font-weight:800; line-height:1; }
    .sc-blue  .stat-num { color:var(--accent); }
    .sc-green .stat-num { color:var(--green); }
    .sc-amber .stat-num { color:var(--amber); }
    .sc-red   .stat-num { color:var(--red); }

    /* SECTION HEADER */
    .sec-hdr { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; }
    .sec-title { font-size:1.05rem; font-weight:700; }

    /* BOOKING CARD */
    .bk-card {
      background:var(--bg2); border:1px solid var(--border);
      border-radius:var(--r); padding:1.1rem 1.25rem; margin-bottom:.85rem;
    }
    .bk-top { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:.5rem; margin-bottom:.6rem; }
    .bk-car-name { font-weight:700; font-size:.98rem; }
    .bk-right { display:flex; align-items:center; gap:.6rem; }
    .bk-amount { font-weight:800; color:var(--green); font-size:1rem; }
    .pill { display:inline-block; padding:.18rem .6rem; border-radius:20px; font-size:.68rem; font-weight:700; text-transform:uppercase; }
    .pill-pending   { background:rgba(245,158,11,.12); color:var(--amber); }
    .pill-confirmed { background:rgba(30,64,175,.12);  color:var(--accent); }
    .pill-active    { background:rgba(22,163,74,.12);  color:var(--green); }
    .pill-completed { background:rgba(100,116,139,.12);color:var(--muted); }
    .pill-cancelled { background:rgba(220,38,38,.12);  color:var(--red); }
    .bk-meta { font-size:.78rem; color:var(--muted); margin-bottom:.5rem; display:flex; flex-wrap:wrap; gap:.75rem; }
    .bk-meta i { color:var(--accent); margin-right:.2rem; }
    .bk-ref { font-size:.72rem; color:var(--muted); }
    .bk-ref b { color:var(--text); }
    .bk-actions { display:flex; gap:.5rem; margin-top:.65rem; flex-wrap:wrap; }
    .btn { padding:.35rem .8rem; border-radius:7px; font-size:.78rem; font-weight:600; cursor:pointer; border:none; transition:all .2s; font-family:inherit; }
    .btn-cancel { background:rgba(220,38,38,.1); color:var(--red); border:1px solid rgba(220,38,38,.2); }
    .btn-cancel:hover { background:rgba(220,38,38,.18); }

    /* EMPTY STATE */
    .empty { text-align:center; padding:3.5rem 2rem; color:var(--muted); }
    .empty-icon { font-size:3rem; opacity:.2; display:block; margin-bottom:1rem; }
    .empty h3 { font-size:1.05rem; color:var(--text); margin-bottom:.4rem; }
    .empty-btn { display:inline-block; margin-top:1rem; padding:.65rem 1.5rem; background:var(--accent); color:#fff; border-radius:8px; text-decoration:none; font-weight:700; font-size:.875rem; }

    /* WISHLIST */
    .wish-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(190px,1fr)); gap:1rem; }
    .wish-card { background:var(--bg2); border:1px solid var(--border); border-radius:var(--r); overflow:hidden; transition:transform .2s; }
    .wish-card:hover { transform:translateY(-2px); border-color:var(--accent); }
    .wish-card img { width:100%; height:115px; object-fit:cover; }
    .wish-body { padding:.75rem; }
    .wish-name { font-weight:700; font-size:.85rem; margin-bottom:.2rem; }
    .wish-price { color:var(--green); font-weight:700; font-size:.82rem; }
    .wish-type { font-size:.7rem; color:var(--muted); text-transform:uppercase; margin-bottom:.5rem; }
    .wish-del { background:none; border:none; color:var(--red); cursor:pointer; font-size:.75rem; font-weight:600; margin-top:.3rem; }
    .wish-del:hover { text-decoration:underline; }

    /* PROFILE FORM */
    .form-card { background:var(--bg2); border:1px solid var(--border); border-radius:var(--r); padding:1.5rem; }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.25rem; }
    .fg { display:flex; flex-direction:column; gap:.3rem; }
    .fg label { font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); }
    .fg input {
      padding:.65rem .85rem; border:1px solid var(--border); border-radius:8px;
      font-size:.9rem; background:var(--bg3); color:var(--text); outline:none; transition:border .2s;
    }
    .fg input:focus { border-color:var(--accent); }
    .fg input:disabled { opacity:.5; cursor:not-allowed; }
    .save-btn { padding:.7rem 1.75rem; background:var(--accent); color:#fff; border:none; border-radius:8px; font-size:.9rem; font-weight:700; cursor:pointer; }
    .save-btn:hover { background:#1d4ed8; }

    /* TOAST */
    .toast { position:fixed; bottom:1.5rem; right:1.5rem; z-index:999; padding:.75rem 1.4rem; border-radius:10px; font-size:.85rem; font-weight:600; transform:translateY(60px); opacity:0; transition:all .3s; }
    .toast.show { transform:translateY(0); opacity:1; }
    .toast.success { background:#dcfce7; color:#166534; border:1px solid #bbf7d0; }
    .toast.error   { background:#fee2e2; color:#991b1b; border:1px solid #fecaca; }
    .toast.info    { background:#dbeafe; color:#1e40af; border:1px solid #bfdbfe; }

    @media(max-width:768px) {
      .layout { grid-template-columns:1fr; }
      .sidebar { display:none; }
      .stats { grid-template-columns:1fr 1fr; }
      .form-grid { grid-template-columns:1fr; }
    }
  </style>
</head>
<body>

<!-- NAV -->
<nav class="topnav">
  <a href="../index.html" class="logo">✨ Lux<span>Ride</span></a>
  <div class="nav-right">
    <a href="../cars.html" class="nbtn nbtn-ghost"><i class="fa-solid fa-car"></i> Browse Cars</a>
    <button class="theme-btn" onclick="toggleTheme()" title="Toggle theme">
      <i class="fa-solid fa-moon" id="theme-icon"></i>
    </button>
    <a href="?logout=1" class="nbtn nbtn-danger"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
  </div>
</nav>

<div class="layout">

  <!-- SIDEBAR -->
  <aside class="sidebar">
    <div class="user-pill">
      <div class="avatar"><?= strtoupper(substr($uname, 0, 1)) ?></div>
      <div class="uname"><?= htmlspecialchars($uname) ?></div>
      <div class="uemail"><?= htmlspecialchars($uemail) ?></div>
    </div>

    <div class="slink active" onclick="show('overview',this)">
      <i class="fa-solid fa-gauge"></i> Overview
    </div>
    <div class="slink" onclick="show('bookings',this)">
      <i class="fa-solid fa-calendar-check"></i> My Bookings
      <span class="sbadge"><?= count($allBookings) ?></span>
    </div>
    <div class="slink" onclick="show('active',this)">
      <i class="fa-solid fa-car"></i> Active Rentals
      <span class="sbadge red"><?= count($active) ?></span>
    </div>
    <div class="slink" onclick="show('past',this)">
      <i class="fa-solid fa-clock-rotate-left"></i> Past Trips
    </div>
    <div class="slink" onclick="show('wishlist',this)">
      <i class="fa-solid fa-heart"></i> Wishlist
      <span class="sbadge"><?= count($wishlist) ?></span>
    </div>
    <div class="slink" onclick="show('profile',this)">
      <i class="fa-solid fa-user-pen"></i> Edit Profile
    </div>
    <div class="slink" onclick="show('security',this)">
      <i class="fa-solid fa-lock"></i> Security
    </div>
  </aside>

  <!-- CONTENT -->
  <main class="content">

    <!-- ═══ OVERVIEW ═══ -->
    <div class="sec active" id="sec-overview">
      <h2 style="margin-bottom:1.5rem;font-size:1.2rem">
        Welcome back, <?= htmlspecialchars(explode(' ', $uname)[0]) ?>! 👋
      </h2>

      <div class="stats">
        <div class="stat-card sc-blue">
          <div class="stat-lbl">Total Bookings</div>
          <div class="stat-num"><?= count($allBookings) ?></div>
        </div>
        <div class="stat-card sc-green">
          <div class="stat-lbl">Active / Confirmed</div>
          <div class="stat-num"><?= count($active) ?></div>
        </div>
        <div class="stat-card sc-amber">
          <div class="stat-lbl">Saved Cars</div>
          <div class="stat-num"><?= count($wishlist) ?></div>
        </div>
        <div class="stat-card sc-red">
          <div class="stat-lbl">Completed Trips</div>
          <div class="stat-num"><?= count($past) ?></div>
        </div>
      </div>

      <div class="sec-hdr">
        <div class="sec-title">Recent Bookings</div>
        <span class="slink" style="padding:.3rem .7rem;font-size:.82rem" onclick="show('bookings',null)">
          View All →
        </span>
      </div>

      <?php if (empty($allBookings)): ?>
        <div class="empty">
          <i class="fa-solid fa-calendar-xmark empty-icon"></i>
          <h3>No bookings yet</h3>
          <p>Your booking history will appear here after your first booking.</p>
          <a href="../cars.html" class="empty-btn">Browse Cars</a>
        </div>
      <?php else: ?>
        <?php foreach(array_slice($allBookings, 0, 3) as $b): ?>
          <?php renderCard($b); ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- ═══ ALL BOOKINGS ═══ -->
    <div class="sec" id="sec-bookings">
      <div class="sec-hdr"><div class="sec-title">All Bookings (<?= count($allBookings) ?>)</div></div>
      <?php if (empty($allBookings)): ?>
        <div class="empty">
          <i class="fa-solid fa-calendar-xmark empty-icon"></i>
          <h3>No bookings found</h3>
          <p>Book your first car and it will show here.</p>
          <a href="../cars.html" class="empty-btn">Browse Cars</a>
        </div>
      <?php else: foreach($allBookings as $b): renderCard($b); endforeach; endif; ?>
    </div>

    <!-- ═══ ACTIVE RENTALS ═══ -->
    <div class="sec" id="sec-active">
      <div class="sec-hdr"><div class="sec-title">Active Rentals (<?= count($active) ?>)</div></div>
      <?php if (empty($active)): ?>
        <div class="empty">
          <i class="fa-solid fa-car empty-icon"></i>
          <h3>No active rentals</h3>
          <p>Cars you've booked and confirmed will appear here.</p>
        </div>
      <?php else: foreach($active as $b): renderCard($b, true); endforeach; endif; ?>
    </div>

    <!-- ═══ PAST TRIPS ═══ -->
    <div class="sec" id="sec-past">
      <div class="sec-hdr"><div class="sec-title">Past Trips (<?= count($past) ?>)</div></div>
      <?php if (empty($past)): ?>
        <div class="empty">
          <i class="fa-solid fa-road empty-icon"></i>
          <h3>No past trips yet</h3>
          <p>Completed and cancelled bookings will appear here.</p>
        </div>
      <?php else: foreach($past as $b): renderCard($b); endforeach; endif; ?>
    </div>

    <!-- ═══ WISHLIST ═══ -->
    <div class="sec" id="sec-wishlist">
      <div class="sec-hdr"><div class="sec-title">My Wishlist (<?= count($wishlist) ?>)</div></div>
      <?php if (empty($wishlist)): ?>
        <div class="empty">
          <i class="fa-solid fa-heart empty-icon"></i>
          <h3>Wishlist is empty</h3>
          <p>Tap the ♡ heart on any car to save it here.</p>
          <a href="../cars.html" class="empty-btn">Browse Cars</a>
        </div>
      <?php else: ?>
        <div class="wish-grid">
          <?php foreach($wishlist as $w): ?>
          <div class="wish-card" id="wc-<?= $w['car_id'] ?>">
            <img src="../<?= htmlspecialchars($w['image']) ?>"
                 onerror="this.src='https://via.placeholder.com/190x115?text=Car'"
                 alt="<?= htmlspecialchars($w['name']) ?>">
            <div class="wish-body">
              <div class="wish-type"><?= htmlspecialchars($w['type']) ?></div>
              <div class="wish-name"><?= htmlspecialchars($w['brand'].' '.$w['name']) ?></div>
              <div class="wish-price">₹<?= number_format($w['price']) ?>/day</div>
              <div style="font-size:.72rem;color:var(--muted);margin-top:.2rem">
                ⭐ <?= $w['rating'] ?> &nbsp;·&nbsp; <?= $w['seats'] ?> seats
              </div>
              <button class="wish-del" onclick="removeWish(<?= $w['car_id'] ?>, this)">
                <i class="fa-solid fa-trash"></i> Remove
              </button>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- ═══ PROFILE ═══ -->
    <div class="sec" id="sec-profile">
      <div class="sec-hdr"><div class="sec-title">Edit Profile</div></div>
      <div class="form-card">
        <div class="form-grid">
          <div class="fg">
            <label>Full Name</label>
            <input type="text" id="p-name" value="<?= htmlspecialchars($user['full_name']) ?>">
          </div>
          <div class="fg">
            <label>Email Address</label>
            <input type="email" id="p-email" value="<?= htmlspecialchars($user['email']) ?>">
          </div>
          <div class="fg">
            <label>Phone Number</label>
            <input type="tel" id="p-phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
          </div>
          <div class="fg">
            <label>Member Since</label>
            <input type="text" value="<?= date('d M Y', strtotime($user['created_at'])) ?>" disabled>
          </div>
        </div>
        <button class="save-btn" onclick="saveProfile()">
          <i class="fa-solid fa-floppy-disk"></i> Save Changes
        </button>
      </div>
    </div>

    <!-- ═══ SECURITY ═══ -->
    <div class="sec" id="sec-security">
      <div class="sec-hdr"><div class="sec-title">Change Password</div></div>
      <div class="form-card" style="max-width:460px">
        <div class="fg" style="margin-bottom:.85rem">
          <label>Current Password</label>
          <input type="password" id="s-curr" placeholder="Enter current password">
        </div>
        <div class="fg" style="margin-bottom:.85rem">
          <label>New Password</label>
          <input type="password" id="s-new" placeholder="Minimum 8 characters">
        </div>
        <div class="fg" style="margin-bottom:1.25rem">
          <label>Confirm New Password</label>
          <input type="password" id="s-conf" placeholder="Repeat new password">
        </div>
        <button class="save-btn" onclick="changePass()">
          <i class="fa-solid fa-key"></i> Update Password
        </button>
      </div>
    </div>

  </main>
</div>

<div class="toast" id="toast"></div>

<?php
function renderCard($b, $canCancel = false) {
    $status = $b['status'];
    $cls    = 'pill-' . $status;
    $amt    = number_format(floatval($b['paid_amount'] ?? $b['total_amount']), 0, '.', ',');
    $date   = date('d M Y', strtotime($b['created_at']));
    $dur    = $b['duration'] . ' ' . ($b['rate_type'] === 'hourly' ? 'hr(s)' : 'day(s)');
    $canCancel = in_array($status, ['pending','confirmed']);
    echo "
    <div class='bk-card' id='bk-{$b['id']}'>
      <div class='bk-top'>
        <div>
          <div class='bk-car-name'>{$b['car_brand']} {$b['car_name']}</div>
          <div class='bk-meta' style='margin-top:.3rem'>
            <span><i class='fa-solid fa-location-dot'></i>{$b['pickup_location']}</span>
            <span><i class='fa-solid fa-calendar'></i>{$date}</span>
            <span><i class='fa-solid fa-clock'></i>{$dur}</span>
            <span><i class='fa-solid fa-car'></i>" . ($b['booking_type'] === 'chauffeur' ? 'Chauffeur' : 'Self Drive') . "</span>
          </div>
        </div>
        <div class='bk-right'>
          <span class='pill {$cls}'>{$status}</span>
          <span class='bk-amount'>₹{$amt}</span>
        </div>
      </div>
      <div class='bk-ref'>
        Booking: <b>{$b['booking_ref']}</b>" .
        ($b['txn_id'] ? " &nbsp;·&nbsp; TXN: <b>{$b['txn_id']}</b>" : '') .
      "</div>
      " . ($canCancel ? "
      <div class='bk-actions'>
        <button class='btn btn-cancel' onclick='cancelBooking({$b['id']}, \"{$b['booking_ref']}\", this)'>
          <i class='fa-solid fa-xmark'></i> Cancel Booking
        </button>
      </div>" : '') . "
    </div>";
}
?>

<script>
// ── SECTION SWITCH ────────────────────────────────────────
function show(name, el) {
  document.querySelectorAll('.sec').forEach(s => s.classList.remove('active'));
  document.querySelectorAll('.slink').forEach(l => l.classList.remove('active'));
  const sec = document.getElementById('sec-' + name);
  if (sec) sec.classList.add('active');
  if (el)  el.classList.add('active');
}

// ── THEME ─────────────────────────────────────────────────
function toggleTheme() {
  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  document.documentElement.setAttribute('data-theme', isDark ? 'light' : 'dark');
  document.getElementById('theme-icon').className = isDark ? 'fa-solid fa-moon' : 'fa-solid fa-sun';
  localStorage.setItem('luxride_theme', isDark ? 'light' : 'dark');
}
if (localStorage.getItem('luxride_theme') === 'dark') {
  document.documentElement.setAttribute('data-theme', 'dark');
  document.getElementById('theme-icon').className = 'fa-solid fa-sun';
}

// ── CANCEL BOOKING ────────────────────────────────────────
async function cancelBooking(id, ref, btn) {
  if (!confirm(`Cancel booking ${ref}?\n\nThis action cannot be undone.`)) return;
  btn.disabled = true;
  btn.textContent = 'Cancelling...';
  try {
    const res  = await fetch('../api/admin_bookings.php?action=update_status', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id, status: 'cancelled' })
    });
    const data = await res.json();
    showToast('Booking cancelled successfully', 'success');
    setTimeout(() => location.reload(), 1200);
  } catch(e) {
    showToast('Failed to cancel. Please try again.', 'error');
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-xmark"></i> Cancel Booking';
  }
}

// ── REMOVE WISHLIST ───────────────────────────────────────
async function removeWish(carId, btn) {
  try {
    const res  = await fetch('../api/wishlist.php?action=remove', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ car_id: carId })
    });
    const data = await res.json();
    document.getElementById('wc-' + carId)?.remove();
    showToast('Removed from wishlist', 'info');
  } catch(e) {
    showToast('Failed to remove', 'error');
  }
}

// ── SAVE PROFILE ──────────────────────────────────────────
async function saveProfile() {
  const body = {
    id:        <?= $uid ?>,
    full_name: document.getElementById('p-name').value.trim(),
    email:     document.getElementById('p-email').value.trim(),
    phone:     document.getElementById('p-phone').value.trim(),
    status:    'active'
  };
  if (!body.full_name || !body.email) {
    showToast('Name and email are required', 'error'); return;
  }
  try {
    const res  = await fetch('../api/admin_users.php?action=edit', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body)
    });
    const data = await res.json();
    showToast(res.ok ? 'Profile updated successfully!' : data.message, res.ok ? 'success' : 'error');
  } catch(e) { showToast('Failed to save', 'error'); }
}

// ── CHANGE PASSWORD ───────────────────────────────────────
async function changePass() {
  const curr = document.getElementById('s-curr').value;
  const nw   = document.getElementById('s-new').value;
  const cf   = document.getElementById('s-conf').value;
  if (!curr || !nw || !cf) { showToast('Fill all fields', 'error'); return; }
  if (nw !== cf)            { showToast('Passwords do not match', 'error'); return; }
  if (nw.length < 8)        { showToast('Min 8 characters required', 'error'); return; }
  try {
    const res  = await fetch('../api/change_password.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ id: <?= $uid ?>, current: curr, newpass: nw })
    });
    const data = await res.json();
    showToast(data.message, res.ok ? 'success' : 'error');
    if (res.ok) {
      document.getElementById('s-curr').value = '';
      document.getElementById('s-new').value  = '';
      document.getElementById('s-conf').value = '';
    }
  } catch(e) { showToast('Failed to update', 'error'); }
}

// ── TOAST ─────────────────────────────────────────────────
function showToast(msg, type = 'success') {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = `toast show ${type}`;
  clearTimeout(t._t);
  t._t = setTimeout(() => t.className = 'toast', 3500);
}
</script>
</body>
</html>