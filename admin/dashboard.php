<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) { header("Location: login.php"); exit(); }
if (isset($_GET['logout'])) { session_destroy(); header("Location: login.php"); exit(); }
$adminUser = htmlspecialchars($_SESSION['admin_username']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>LuxRide Admin Panel</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.0/chart.umd.min.js"></script>
<style>
  @import url('https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500;600&display=swap');

  :root {
    --bg:       #080b14;
    --bg2:      #0e1420;
    --bg3:      #131b2e;
    --border:   rgba(99,179,237,0.1);
    --accent:   #63b3ed;
    --accent2:  #76e4f7;
    --green:    #68d391;
    --red:      #fc8181;
    --yellow:   #f6e05e;
    --purple:   #b794f4;
    --text:     #e2e8f0;
    --muted:    #718096;
    --sidebar-w: 260px;
  }

  * { margin:0; padding:0; box-sizing:border-box; }
  body { font-family:'DM Sans',sans-serif; background:var(--bg); color:var(--text); min-height:100vh; display:flex; overflow-x:hidden; }

  /* ── SIDEBAR ── */
  .sidebar {
    width: var(--sidebar-w); background: var(--bg2);
    border-right: 1px solid var(--border);
    display: flex; flex-direction: column;
    position: fixed; top:0; left:0; height:100vh;
    z-index: 100; transition: transform 0.3s;
  }
  .sidebar-logo {
    padding: 1.5rem 1.5rem 1rem;
    font-family: 'Syne', sans-serif;
    font-size: 1.5rem; font-weight: 800;
    color: #fff; border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 0.6rem;
  }
  .sidebar-logo .dot { width:8px; height:8px; border-radius:50%; background:var(--accent); display:inline-block; animation: pulse 2s infinite; }
  @keyframes pulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(1.3)} }

  .sidebar-section { padding: 1.25rem 1rem 0.5rem; font-size:0.65rem; text-transform:uppercase; letter-spacing:0.12em; color:var(--muted); font-weight:700; }
  .nav-item {
    display: flex; align-items: center; gap: 0.8rem;
    padding: 0.7rem 1.25rem; margin: 0.15rem 0.5rem;
    border-radius: 10px; cursor: pointer;
    color: var(--muted); font-size: 0.9rem; font-weight: 500;
    transition: all 0.2s; position: relative;
  }
  .nav-item:hover { background: rgba(99,179,237,0.08); color: var(--text); }
  .nav-item.active { background: rgba(99,179,237,0.12); color: var(--accent); }
  .nav-item.active::before {
    content:''; position:absolute; left:0; top:20%; height:60%;
    width:3px; background:var(--accent); border-radius:0 3px 3px 0;
  }
  .nav-item .badge {
    margin-left: auto; background: var(--accent); color: var(--bg);
    font-size:0.65rem; font-weight:800; padding:0.1rem 0.45rem; border-radius:20px;
  }
  .nav-item .badge.red { background: var(--red); }
  .nav-item .badge.green { background: var(--green); }

  .sidebar-bottom {
    margin-top: auto; padding: 1rem;
    border-top: 1px solid var(--border);
  }
  .admin-profile {
    display:flex; align-items:center; gap:0.75rem;
    padding: 0.75rem; border-radius:10px;
    background: rgba(255,255,255,0.03);
    border: 1px solid var(--border);
  }
  .avatar {
    width:36px; height:36px; border-radius:50%;
    background: linear-gradient(135deg, var(--accent), var(--purple));
    display:flex; align-items:center; justify-content:center;
    font-weight:800; font-size:0.85rem; color:#fff; font-family:'Syne',sans-serif;
    flex-shrink:0;
  }
  .admin-name { font-size:0.85rem; font-weight:600; }
  .admin-role { font-size:0.7rem; color:var(--muted); }
  .logout-btn {
    margin-left:auto; color:var(--red); font-size:0.85rem;
    text-decoration:none; padding:0.3rem 0.5rem; border-radius:6px;
    transition: background 0.2s;
  }
  .logout-btn:hover { background:rgba(252,129,129,0.1); }

  /* ── MAIN ── */
  .main { margin-left: var(--sidebar-w); flex:1; min-height:100vh; display:flex; flex-direction:column; }

  .topbar {
    background: var(--bg2); border-bottom: 1px solid var(--border);
    padding: 1rem 2rem; display:flex; align-items:center; gap:1rem;
    position: sticky; top:0; z-index:50;
  }
  .page-title { font-family:'Syne',sans-serif; font-size:1.2rem; font-weight:700; }
  .page-title span { color: var(--accent); }
  .topbar-right { margin-left:auto; display:flex; align-items:center; gap:1rem; }
  .time-badge {
    font-size:0.8rem; color:var(--muted);
    background:rgba(255,255,255,0.04); border:1px solid var(--border);
    padding:0.4rem 0.85rem; border-radius:8px;
  }
  .refresh-btn {
    background:rgba(99,179,237,0.1); border:1px solid rgba(99,179,237,0.25);
    color:var(--accent); padding:0.4rem 0.9rem; border-radius:8px;
    cursor:pointer; font-size:0.82rem; font-weight:600; transition:all 0.2s;
  }
  .refresh-btn:hover { background:rgba(99,179,237,0.2); }

  .content { padding: 1.75rem 2rem; flex:1; }

  /* ── SECTION VISIBILITY ── */
  .section { display:none; }
  .section.active { display:block; animation: fadeIn 0.3s ease; }
  @keyframes fadeIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:none} }

  /* ── STAT CARDS ── */
  .stats-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(210px,1fr)); gap:1rem; margin-bottom:1.5rem; }
  .stat-card {
    background:var(--bg3); border:1px solid var(--border);
    border-radius:14px; padding:1.25rem 1.4rem;
    position:relative; overflow:hidden; transition:transform 0.2s, border-color 0.2s;
  }
  .stat-card:hover { transform:translateY(-2px); border-color: rgba(99,179,237,0.3); }
  .stat-card::before {
    content:''; position:absolute; top:0; left:0; right:0; height:2px;
  }
  .stat-card.blue::before  { background: linear-gradient(90deg,var(--accent),var(--accent2)); }
  .stat-card.green::before { background: linear-gradient(90deg,var(--green),#9ae6b4); }
  .stat-card.red::before   { background: linear-gradient(90deg,var(--red),#feb2b2); }
  .stat-card.purple::before{ background: linear-gradient(90deg,var(--purple),#d6bcfa); }
  .stat-card.yellow::before{ background: linear-gradient(90deg,var(--yellow),#fefcbf); }
  .stat-label { font-size:0.75rem; text-transform:uppercase; letter-spacing:0.08em; color:var(--muted); font-weight:700; margin-bottom:0.5rem; }
  .stat-num { font-family:'Syne',sans-serif; font-size:2rem; font-weight:800; line-height:1; }
  .stat-num.blue   { color:var(--accent); }
  .stat-num.green  { color:var(--green); }
  .stat-num.red    { color:var(--red); }
  .stat-num.purple { color:var(--purple); }
  .stat-num.yellow { color:var(--yellow); }
  .stat-sub { font-size:0.75rem; color:var(--muted); margin-top:0.4rem; }
  .stat-icon {
    position:absolute; right:1.25rem; top:50%; transform:translateY(-50%);
    font-size:2rem; opacity:0.08;
  }

  /* ── SECTION HEADER ── */
  .section-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem; flex-wrap:wrap; gap:0.75rem; }
  .section-title { font-family:'Syne',sans-serif; font-size:1.1rem; font-weight:700; display:flex; align-items:center; gap:0.5rem; }
  .section-title i { color:var(--accent); }

  /* ── TABLES ── */
  .card {
    background:var(--bg3); border:1px solid var(--border);
    border-radius:14px; overflow:hidden; margin-bottom:1.25rem;
  }
  .card-header {
    padding:1rem 1.25rem; border-bottom:1px solid var(--border);
    display:flex; align-items:center; gap:0.75rem; flex-wrap:wrap;
  }
  .card-title { font-weight:700; font-size:0.95rem; }
  .table-wrap { overflow-x:auto; }
  table { width:100%; border-collapse:collapse; }
  thead tr { background:rgba(99,179,237,0.05); }
  th { padding:0.75rem 1rem; font-size:0.7rem; text-transform:uppercase; letter-spacing:0.07em; color:var(--accent); text-align:left; white-space:nowrap; font-weight:700; }
  td { padding:0.7rem 1rem; font-size:0.85rem; border-bottom:1px solid rgba(255,255,255,0.04); vertical-align:middle; }
  tr:last-child td { border-bottom:none; }
  tr:hover td { background:rgba(255,255,255,0.02); }

  /* ── FORMS ── */
  .form-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(200px,1fr)); gap:1rem; }
  .form-group { display:flex; flex-direction:column; gap:0.35rem; }
  .form-group label { font-size:0.72rem; text-transform:uppercase; letter-spacing:0.07em; color:var(--muted); font-weight:700; }
  .form-group input, .form-group select, .form-group textarea {
    background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1);
    border-radius:8px; padding:0.6rem 0.85rem; color:var(--text);
    font-size:0.875rem; outline:none; transition:border 0.2s; font-family:inherit;
  }
  .form-group input:focus, .form-group select:focus, .form-group textarea:focus { border-color:var(--accent); }
  .form-group select option { background:#1a1a2e; }

  /* ── BUTTONS ── */
  .btn { padding:0.55rem 1.1rem; border-radius:8px; font-size:0.85rem; font-weight:600; cursor:pointer; border:none; transition:all 0.2s; font-family:inherit; display:inline-flex; align-items:center; gap:0.4rem; }
  .btn-primary { background:linear-gradient(135deg,#2b6cb0,var(--accent)); color:#fff; }
  .btn-primary:hover { opacity:0.88; transform:translateY(-1px); }
  .btn-success { background:rgba(104,211,145,0.15); border:1px solid rgba(104,211,145,0.3); color:var(--green); }
  .btn-success:hover { background:rgba(104,211,145,0.28); }
  .btn-danger  { background:rgba(252,129,129,0.15); border:1px solid rgba(252,129,129,0.3); color:var(--red); }
  .btn-danger:hover  { background:rgba(252,129,129,0.28); }
  .btn-warning { background:rgba(246,224,94,0.15); border:1px solid rgba(246,224,94,0.3); color:var(--yellow); }
  .btn-warning:hover { background:rgba(246,224,94,0.28); }
  .btn-secondary { background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.12); color:var(--muted); }
  .btn-secondary:hover { color:var(--text); background:rgba(255,255,255,0.1); }
  .btn-sm { padding:0.3rem 0.7rem; font-size:0.78rem; }

  /* ── BADGES ── */
  .pill { display:inline-block; padding:0.2rem 0.6rem; border-radius:20px; font-size:0.7rem; font-weight:700; text-transform:uppercase; }
  .pill-active    { background:rgba(104,211,145,0.15); color:var(--green); }
  .pill-suspended { background:rgba(252,129,129,0.15); color:var(--red); }
  .pill-pending   { background:rgba(246,224,94,0.15);  color:var(--yellow); }
  .pill-confirmed { background:rgba(99,179,237,0.15);  color:var(--accent); }
  .pill-completed { background:rgba(104,211,145,0.15); color:var(--green); }
  .pill-cancelled { background:rgba(252,129,129,0.15); color:var(--red); }
  .pill-premium   { background:rgba(246,224,94,0.15);  color:var(--yellow); }
  .pill-new       { background:rgba(104,211,145,0.15); color:var(--green); }
  .pill-sale      { background:rgba(252,129,129,0.15); color:var(--red); }
  .type-chip { background:rgba(183,148,244,0.1); color:var(--purple); padding:0.18rem 0.55rem; border-radius:6px; font-size:0.72rem; font-weight:700; }

  /* ── SEARCH ── */
  .search-input {
    background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1);
    border-radius:8px; padding:0.5rem 0.9rem; color:var(--text); font-size:0.875rem;
    outline:none; min-width:220px; transition:border 0.2s;
  }
  .search-input:focus { border-color:var(--accent); }
  .filter-select {
    background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1);
    border-radius:8px; padding:0.5rem 0.85rem; color:var(--text); font-size:0.82rem; outline:none;
  }
  .filter-select option { background:#1a1a2e; }

  /* ── CHARTS ── */
  .charts-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.25rem; margin-bottom:1.25rem; }
  .chart-card { background:var(--bg3); border:1px solid var(--border); border-radius:14px; padding:1.25rem; }
  .chart-title { font-weight:700; font-size:0.9rem; margin-bottom:1rem; color:var(--text); }
  canvas { max-height:220px; }

  /* ── MODAL ── */
  .modal-overlay { position:fixed; inset:0; background:rgba(0,0,0,0.75); display:none; align-items:center; justify-content:center; z-index:300; padding:1rem; }
  .modal-overlay.active { display:flex; }
  .modal {
    background:var(--bg2); border:1px solid rgba(99,179,237,0.2);
    border-radius:18px; padding:1.75rem; width:100%; max-width:680px;
    max-height:90vh; overflow-y:auto;
  }
  .modal-title { font-family:'Syne',sans-serif; font-size:1.1rem; font-weight:800; margin-bottom:1.5rem; display:flex; align-items:center; gap:0.5rem; }
  .modal-title i { color:var(--accent); }
  .modal-actions { margin-top:1.5rem; display:flex; gap:0.75rem; justify-content:flex-end; }

  /* ── CAR IMAGE ── */
  .car-thumb { width:64px; height:44px; object-fit:cover; border-radius:6px; border:1px solid var(--border); }
  .user-avatar { width:32px; height:32px; border-radius:50%; background:linear-gradient(135deg,var(--accent),var(--purple)); display:inline-flex; align-items:center; justify-content:center; font-size:0.75rem; font-weight:800; color:#fff; font-family:'Syne',sans-serif; }

  /* ── TOAST ── */
  .toast { position:fixed; bottom:1.5rem; right:1.5rem; z-index:999; background:var(--bg2); border:1px solid var(--accent); color:var(--accent); padding:0.8rem 1.4rem; border-radius:12px; font-size:0.875rem; font-weight:600; box-shadow:0 10px 30px rgba(0,0,0,0.4); transform:translateY(80px); opacity:0; transition:all 0.3s ease; max-width:340px; }
  .toast.show { transform:translateY(0); opacity:1; }
  .toast.error { border-color:var(--red); color:var(--red); }
  .toast.success { border-color:var(--green); color:var(--green); }

  /* ── LOG ENTRIES ── */
  .log-entry { display:flex; align-items:flex-start; gap:0.85rem; padding:0.75rem 0; border-bottom:1px solid rgba(255,255,255,0.04); }
  .log-dot { width:8px; height:8px; border-radius:50%; margin-top:5px; flex-shrink:0; }
  .log-text { font-size:0.825rem; line-height:1.4; }
  .log-time { font-size:0.72rem; color:var(--muted); margin-top:0.2rem; }

  /* ── EMPTY STATE ── */
  .empty { text-align:center; padding:3rem; color:var(--muted); }
  .empty i { font-size:2.5rem; opacity:0.3; margin-bottom:0.75rem; }

  /* ── IMG PREVIEW ── */
  .img-preview { width:72px; height:50px; object-fit:cover; border-radius:6px; border:1px solid var(--border); display:none; margin-top:0.5rem; }

  /* ── OVERVIEW GRID ── */
  .two-col { display:grid; grid-template-columns:1.6fr 1fr; gap:1.25rem; }

  /* ── SCROLLBAR ── */
  ::-webkit-scrollbar { width:5px; height:5px; }
  ::-webkit-scrollbar-track { background:transparent; }
  ::-webkit-scrollbar-thumb { background:rgba(99,179,237,0.2); border-radius:10px; }

  @media(max-width:900px) { .charts-grid,.two-col { grid-template-columns:1fr; } }
  @media(max-width:700px) { 
    .sidebar { transform:translateX(-100%); }
    .main { margin-left:0; }
  }
</style>
</head>
<body>

<!-- ══ SIDEBAR ══════════════════════════════════════════════ -->
<aside class="sidebar">
  <div class="sidebar-logo">
    <span class="dot"></span> LuxRide
  </div>

  <div class="sidebar-section">Main</div>
  <div class="nav-item active" onclick="showSection('overview',this)">
    <i class="fa-solid fa-gauge-high fa-fw"></i> Overview
  </div>

  <div class="sidebar-section">Management</div>
  <div class="nav-item" onclick="showSection('users',this)">
    <i class="fa-solid fa-users fa-fw"></i> Users
    <span class="badge" id="nav-users-count">—</span>
  </div>
  <div class="nav-item" onclick="showSection('cars',this)">
    <i class="fa-solid fa-car fa-fw"></i> Cars
    <span class="badge green" id="nav-cars-count">—</span>
  </div>
  <div class="nav-item" onclick="showSection('bookings',this)">
    <i class="fa-solid fa-calendar-check fa-fw"></i> Bookings
    <span class="badge red" id="nav-bookings-count">—</span>
  </div>

  <div class="sidebar-section">Insights</div>
  <div class="nav-item" onclick="showSection('payments',this)">
    <i class="fa-solid fa-indian-rupee-sign fa-fw"></i> Payments
    <span class="badge green" id="nav-pay-count">—</span>
  </div>
  <div class="nav-item" onclick="showSection('logs',this)">
    <i class="fa-solid fa-list-check fa-fw"></i> Activity Logs
  </div>

  <div class="sidebar-section">System</div>
  <div class="nav-item" onclick="showSection('settings',this)">
    <i class="fa-solid fa-gear fa-fw"></i> Settings
  </div>

  <div class="sidebar-bottom">
    <div class="admin-profile">
      <div class="avatar"><?= strtoupper(substr($adminUser,0,1)) ?></div>
      <div><div class="admin-name"><?= $adminUser ?></div><div class="admin-role">Super Admin</div></div>
      <a href="?logout=1" class="logout-btn" title="Logout"><i class="fa-solid fa-right-from-bracket"></i></a>
    </div>
  </div>
</aside>

<!-- ══ MAIN ══════════════════════════════════════════════════ -->
<div class="main">
  <div class="topbar">
    <div class="page-title" id="topbar-title"><span>Overview</span> Dashboard</div>
    <div class="topbar-right">
      <div class="time-badge" id="live-time"></div>
      <button class="refresh-btn" onclick="refreshAll()"><i class="fa-solid fa-rotate-right"></i> Refresh</button>
    </div>
  </div>

  <div class="content">

    <!-- ══ 1. OVERVIEW ══════════════════════════════ -->
    <div class="section active" id="sec-overview">
      <div class="stats-grid" id="overview-stats">
        <div class="stat-card blue"><div class="stat-label">Total Users</div><div class="stat-num blue" id="ov-users">—</div><div class="stat-sub" id="ov-users-sub">Loading...</div><i class="fa-solid fa-users stat-icon"></i></div>
        <div class="stat-card green"><div class="stat-label">Total Cars</div><div class="stat-num green" id="ov-cars">—</div><div class="stat-sub" id="ov-cars-sub">Loading...</div><i class="fa-solid fa-car stat-icon"></i></div>
        <div class="stat-card purple"><div class="stat-label">Total Bookings</div><div class="stat-num purple" id="ov-bookings">—</div><div class="stat-sub" id="ov-bookings-sub">Loading...</div><i class="fa-solid fa-calendar-check stat-icon"></i></div>
        <div class="stat-card yellow"><div class="stat-label">Revenue (Completed)</div><div class="stat-num yellow" id="ov-revenue">—</div><div class="stat-sub" id="ov-revenue-sub">Loading...</div><i class="fa-solid fa-indian-rupee-sign stat-icon"></i></div>
        <div class="stat-card green"><div class="stat-label">New Users Today</div><div class="stat-num green" id="ov-newusers">—</div><div class="stat-sub">Registered today</div><i class="fa-solid fa-user-plus stat-icon"></i></div>
        <div class="stat-card red"><div class="stat-label">Pending Bookings</div><div class="stat-num red" id="ov-pending">—</div><div class="stat-sub">Awaiting confirmation</div><i class="fa-solid fa-clock stat-icon"></i></div>
        <div class="stat-card blue"><div class="stat-label">Active Rentals</div><div class="stat-num blue" id="ov-active">—</div><div class="stat-sub">Currently on road</div><i class="fa-solid fa-road stat-icon"></i></div>
        <div class="stat-card purple"><div class="stat-label">Pending Revenue</div><div class="stat-num purple" id="ov-pending-rev">—</div><div class="stat-sub">From active bookings</div><i class="fa-solid fa-hourglass stat-icon"></i></div>
      </div>

      <div class="two-col">
        <div class="chart-card">
          <div class="chart-title"><i class="fa-solid fa-chart-column" style="color:var(--accent)"></i> Revenue — Last 6 Months</div>
          <canvas id="revenueChart"></canvas>
        </div>
        <div class="chart-card">
          <div class="chart-title"><i class="fa-solid fa-chart-pie" style="color:var(--purple)"></i> Fleet by Category</div>
          <canvas id="typeChart"></canvas>
        </div>
      </div>

      <div class="two-col">
        <div class="card">
          <div class="card-header"><div class="card-title"><i class="fa-solid fa-trophy" style="color:var(--yellow)"></i> Top Booked Cars</div></div>
          <div class="table-wrap">
            <table><thead><tr><th>#</th><th>Car</th><th>Bookings</th><th>Revenue</th></tr></thead>
            <tbody id="top-cars-tbody"><tr><td colspan="4" class="empty">Loading...</td></tr></tbody></table>
          </div>
        </div>
        <div class="card">
          <div class="card-header"><div class="card-title"><i class="fa-solid fa-clock-rotate-left" style="color:var(--green)"></i> Recent Logins</div></div>
          <div id="recent-logins-list" style="padding:0.5rem 1rem; max-height:250px; overflow-y:auto;">
            <div class="empty"><i class="fa-solid fa-spinner fa-spin"></i></div>
          </div>
        </div>
      </div>
    </div>

    <!-- ══ 2. USERS ══════════════════════════════════ -->
    <div class="section" id="sec-users">
      <div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
        <div class="stat-card blue"><div class="stat-label">Total Users</div><div class="stat-num blue" id="u-total">—</div><i class="fa-solid fa-users stat-icon"></i></div>
        <div class="stat-card green"><div class="stat-label">Active</div><div class="stat-num green" id="u-active">—</div><i class="fa-solid fa-user-check stat-icon"></i></div>
        <div class="stat-card red"><div class="stat-label">Suspended</div><div class="stat-num red" id="u-suspended">—</div><i class="fa-solid fa-user-slash stat-icon"></i></div>
        <div class="stat-card yellow"><div class="stat-label">New This Week</div><div class="stat-num yellow" id="u-week">—</div><i class="fa-solid fa-user-plus stat-icon"></i></div>
      </div>

      <!-- Add User Form -->
      <div class="card" style="margin-bottom:1.25rem">
        <div class="card-header"><div class="card-title"><i class="fa-solid fa-user-plus" style="color:var(--green)"></i> Add New User</div></div>
        <div style="padding:1.25rem">
          <div class="form-grid">
            <div class="form-group"><label>Full Name *</label><input type="text" id="ua-name" placeholder="John Doe"></div>
            <div class="form-group"><label>Email *</label><input type="email" id="ua-email" placeholder="john@example.com"></div>
            <div class="form-group"><label>Phone</label><input type="tel" id="ua-phone" placeholder="+91 9876543210"></div>
            <div class="form-group"><label>Password *</label><input type="password" id="ua-pass" placeholder="Min 8 characters"></div>
            <div class="form-group"><label>Status</label>
              <select id="ua-status"><option value="active">Active</option><option value="suspended">Suspended</option></select>
            </div>
          </div>
          <div style="margin-top:1rem;display:flex;gap:0.75rem">
            <button class="btn btn-primary" onclick="addUser()"><i class="fa-solid fa-plus"></i> Create User</button>
            <button class="btn btn-secondary" onclick="clearUserForm()"><i class="fa-solid fa-rotate-left"></i> Clear</button>
          </div>
        </div>
      </div>

      <!-- Users Table -->
      <div class="card">
        <div class="card-header">
          <div class="card-title"><i class="fa-solid fa-table-list" style="color:var(--accent)"></i> All Users</div>
          <input type="text" class="search-input" placeholder="🔍 Search name or email..." oninput="filterUsers(this.value)">
          <select class="filter-select" onchange="filterUsersByStatus(this.value)">
            <option value="">All Status</option><option value="active">Active</option><option value="suspended">Suspended</option>
          </select>
        </div>
        <div class="table-wrap">
          <table><thead><tr><th>ID</th><th>User</th><th>Email</th><th>Phone</th><th>Status</th><th>Logins</th><th>Last Login</th><th>Joined</th><th>Actions</th></tr></thead>
          <tbody id="users-tbody"><tr><td colspan="9" class="empty"><i class="fa-solid fa-spinner fa-spin"></i></td></tr></tbody></table>
        </div>
      </div>
    </div>

    <!-- ══ 3. CARS ════════════════════════════════════ -->
    <div class="section" id="sec-cars">
      <div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
        <div class="stat-card green"><div class="stat-label">Total Cars</div><div class="stat-num green" id="c-total">—</div><i class="fa-solid fa-car stat-icon"></i></div>
        <div class="stat-card blue"><div class="stat-label">Categories</div><div class="stat-num blue" id="c-types">—</div><i class="fa-solid fa-layer-group stat-icon"></i></div>
        <div class="stat-card yellow"><div class="stat-label">Avg Price/Day</div><div class="stat-num yellow" id="c-avg">—</div><i class="fa-solid fa-indian-rupee-sign stat-icon"></i></div>
        <div class="stat-card purple"><div class="stat-label">Most Expensive</div><div class="stat-num purple" style="font-size:0.95rem;line-height:1.3" id="c-top">—</div></div>
      </div>

      <!-- Add Car Form -->
      <div class="card" style="margin-bottom:1.25rem">
        <div class="card-header"><div class="card-title"><i class="fa-solid fa-circle-plus" style="color:var(--green)"></i> Add New Car</div></div>
        <div style="padding:1.25rem">
          <div class="form-grid">
            <div class="form-group"><label>Car ID *</label><input type="number" id="ca-id" placeholder="e.g. 60"></div>
            <div class="form-group"><label>Car Name *</label><input type="text" id="ca-name" placeholder="e.g. Civic Type R"></div>
            <div class="form-group"><label>Brand *</label><input type="text" id="ca-brand" placeholder="e.g. Honda"></div>
            <div class="form-group"><label>Category *</label>
              <select id="ca-type"><option value="">Select...</option><option value="sedan">Sedan</option><option value="suv">SUV</option><option value="luxury">Luxury</option><option value="electric">Electric</option><option value="sports">Sports</option><option value="supercar">Supercar</option><option value="pickup">Pickup</option><option value="vintage">Vintage</option><option value="family">Family</option><option value="offroad">Offroad</option></select>
            </div>
            <div class="form-group"><label>Price/Day (₹) *</label><input type="number" id="ca-price" placeholder="e.g. 150"></div>
            <div class="form-group"><label>Image Path *</label><input type="text" id="ca-image" placeholder="img/sedan/car.jpg" oninput="previewCarImg('ca-image','ca-preview')"><img id="ca-preview" class="img-preview" alt="preview"></div>
            <div class="form-group"><label>Badge</label>
              <select id="ca-badge"><option value="">None</option><option value="new">New</option><option value="premium">Premium</option><option value="sale">Sale</option><option value="ultra">Ultra</option><option value="classic">Classic</option><option value="rare">Rare</option><option value="electric">Electric</option><option value="hybrid">Hybrid</option></select>
            </div>
            <div class="form-group"><label>Rating *</label><input type="number" id="ca-rating" placeholder="4.7" step="0.1" min="1" max="5"></div>
            <div class="form-group"><label>Seats *</label>
              <select id="ca-seats"><option value="">—</option><option value="2">2</option><option value="4">4</option><option value="5">5</option><option value="7">7</option><option value="8">8</option></select>
            </div>
            <div class="form-group"><label>Transmission *</label>
              <select id="ca-trans"><option value="">—</option><option value="Auto">Automatic</option><option value="Manual">Manual</option><option value="PDK">PDK</option></select>
            </div>
            <div class="form-group"><label>Fuel *</label>
              <select id="ca-fuel"><option value="">—</option><option value="Petrol">Petrol</option><option value="Diesel">Diesel</option><option value="Electric">Electric</option><option value="Hybrid">Hybrid</option></select>
            </div>
          </div>
          <div style="margin-top:1rem;display:flex;gap:0.75rem">
            <button class="btn btn-primary" onclick="addCar()"><i class="fa-solid fa-plus"></i> Add Car to DB</button>
            <button class="btn btn-secondary" onclick="clearCarForm()"><i class="fa-solid fa-rotate-left"></i> Clear</button>
          </div>
        </div>
      </div>

      <!-- Cars Table -->
      <div class="card">
        <div class="card-header">
          <div class="card-title"><i class="fa-solid fa-table-list" style="color:var(--accent)"></i> Fleet</div>
          <input type="text" class="search-input" placeholder="🔍 Search cars..." oninput="filterCarsTable(this.value)">
          <select class="filter-select" onchange="filterCarsByType(this.value)">
            <option value="">All Types</option><option value="sedan">Sedan</option><option value="suv">SUV</option><option value="luxury">Luxury</option><option value="electric">Electric</option><option value="sports">Sports</option><option value="supercar">Supercar</option><option value="pickup">Pickup</option><option value="vintage">Vintage</option><option value="family">Family</option>
          </select>
        </div>
        <div class="table-wrap">
          <table><thead><tr><th>ID</th><th>Image</th><th>Name</th><th>Brand</th><th>Type</th><th>Price/Day</th><th>Badge</th><th>Rating</th><th>Seats</th><th>Trans.</th><th>Fuel</th><th>Actions</th></tr></thead>
          <tbody id="cars-tbody"><tr><td colspan="12" class="empty"><i class="fa-solid fa-spinner fa-spin"></i></td></tr></tbody></table>
        </div>
      </div>
    </div>

    <!-- ══ 4. BOOKINGS ════════════════════════════════ -->
    <div class="section" id="sec-bookings">
      <div class="stats-grid" style="grid-template-columns:repeat(5,1fr)">
        <div class="stat-card blue"><div class="stat-label">Total</div><div class="stat-num blue" id="b-total">—</div><i class="fa-solid fa-calendar stat-icon"></i></div>
        <div class="stat-card yellow"><div class="stat-label">Pending</div><div class="stat-num yellow" id="b-pending">—</div></div>
        <div class="stat-card blue"><div class="stat-label">Active</div><div class="stat-num blue" id="b-active">—</div></div>
        <div class="stat-card green"><div class="stat-label">Completed</div><div class="stat-num green" id="b-completed">—</div></div>
        <div class="stat-card red"><div class="stat-label">Cancelled</div><div class="stat-num red" id="b-cancelled">—</div></div>
      </div>

      <div class="card">
        <div class="card-header">
          <div class="card-title"><i class="fa-solid fa-list-check" style="color:var(--accent)"></i> All Bookings</div>
          <input type="text" class="search-input" placeholder="🔍 Search booking ref or customer..." oninput="filterBookings(this.value)">
          <select class="filter-select" onchange="filterBookingsByStatus(this.value)">
            <option value="">All Status</option><option value="pending">Pending</option><option value="confirmed">Confirmed</option><option value="active">Active</option><option value="completed">Completed</option><option value="cancelled">Cancelled</option>
          </select>
        </div>
        <div class="table-wrap">
          <table><thead><tr><th>Ref</th><th>Customer</th><th>Car</th><th>Pickup</th><th>Duration</th><th>Amount</th><th>Type</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
          <tbody id="bookings-tbody"><tr><td colspan="10" class="empty"><i class="fa-solid fa-spinner fa-spin"></i></td></tr></tbody></table>
        </div>
      </div>
    </div>


       <!-- PAYMENTS SECTION -->
    <div class="section" id="sec-payments">
      <div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
        <div class="stat-card green"><div class="stat-label">Total Transactions</div><div class="stat-num green" id="pay-total">—</div><i class="fa-solid fa-receipt stat-icon"></i></div>
        <div class="stat-card yellow"><div class="stat-label">Total Revenue</div><div class="stat-num yellow" id="pay-revenue">—</div><i class="fa-solid fa-indian-rupee-sign stat-icon"></i></div>
        <div class="stat-card blue"><div class="stat-label">Today Txns</div><div class="stat-num blue" id="pay-today">—</div><i class="fa-solid fa-calendar-day stat-icon"></i></div>
        <div class="stat-card purple"><div class="stat-label">Today Revenue</div><div class="stat-num purple" id="pay-today-rev">—</div><i class="fa-solid fa-coins stat-icon"></i></div>
      </div>
      <div class="card">
        <div class="card-header">
          <div class="card-title"><i class="fa-solid fa-table-list" style="color:var(--accent)"></i> All Payments</div>
          <input type="text" class="search-input" placeholder="🔍 Search by booking ID, customer..." oninput="filterPayments(this.value)">
          <select class="filter-select" onchange="filterPaymentsByMethod(this.value)">
            <option value="">All Methods</option>
            <option value="upi">UPI</option>
            <option value="card">Card</option>
            <option value="netbanking">Net Banking</option>
          </select>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr>
              <th>TXN ID</th><th>Booking ID</th><th>Customer</th><th>Car</th>
              <th>Amount</th><th>Method</th><th>Detail</th><th>Status</th><th>Date</th><th>Action</th>
            </tr></thead>
            <tbody id="payments-tbody"><tr><td colspan="10" class="empty"><i class="fa-solid fa-spinner fa-spin"></i></td></tr></tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- ══ 5. ANALYTICS ═══════════════════════════════ -->
    <div class="section" id="sec-analytics">
      <div class="charts-grid">
        <div class="chart-card">
          <div class="chart-title"><i class="fa-solid fa-chart-line" style="color:var(--accent)"></i> Revenue Trend (6 Months)</div>
          <canvas id="revenueChart2"></canvas>
        </div>
        <div class="chart-card">
          <div class="chart-title"><i class="fa-solid fa-chart-bar" style="color:var(--green)"></i> User Registrations (7 Days)</div>
          <canvas id="userGrowthChart"></canvas>
        </div>
        <div class="chart-card">
          <div class="chart-title"><i class="fa-solid fa-chart-pie" style="color:var(--purple)"></i> Fleet Distribution</div>
          <canvas id="typeChart2"></canvas>
        </div>
        <div class="chart-card">
          <div class="chart-title"><i class="fa-solid fa-chart-bar" style="color:var(--yellow)"></i> Booking Status Split</div>
          <canvas id="bookingStatusChart"></canvas>
        </div>
      </div>

      <div class="card">
        <div class="card-header"><div class="card-title"><i class="fa-solid fa-trophy" style="color:var(--yellow)"></i> Top Performing Cars</div></div>
        <div class="table-wrap">
          <table><thead><tr><th>Rank</th><th>Car</th><th>Brand</th><th>Bookings</th><th>Revenue</th></tr></thead>
          <tbody id="top-cars-tbody2"><tr><td colspan="5" class="empty">Loading...</td></tr></tbody></table>
        </div>
      </div>
    </div>

    <!-- ══ 6. LOGS ════════════════════════════════════ -->
    <div class="section" id="sec-logs">
      <div class="card">
        <div class="card-header">
          <div class="card-title"><i class="fa-solid fa-terminal" style="color:var(--green)"></i> Activity Logs</div>
          <input type="text" class="search-input" placeholder="🔍 Filter logs..." oninput="filterLogs(this.value)" id="log-search">
          <select class="filter-select" onchange="filterLogsByAction(this.value)">
            <option value="">All Actions</option><option value="login">Login</option><option value="logout">Logout</option><option value="register">Register</option>
          </select>
        </div>
        <div id="logs-container" style="padding:0.5rem 1.25rem; max-height:600px; overflow-y:auto;">
          <div class="empty"><i class="fa-solid fa-spinner fa-spin"></i> Loading logs...</div>
        </div>
      </div>
    </div>

    <!-- ══ 7. SETTINGS ════════════════════════════════ -->
    <div class="section" id="sec-settings">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem">

        <div class="card">
          <div class="card-header"><div class="card-title"><i class="fa-solid fa-key" style="color:var(--yellow)"></i> Change Admin Password</div></div>
          <div style="padding:1.25rem">
            <div class="form-grid" style="grid-template-columns:1fr">
              <div class="form-group"><label>Current Password</label><input type="password" id="s-curr" placeholder="Current password"></div>
              <div class="form-group"><label>New Password</label><input type="password" id="s-new" placeholder="New password (min 8 chars)"></div>
              <div class="form-group"><label>Confirm New Password</label><input type="password" id="s-conf" placeholder="Repeat new password"></div>
            </div>
            <button class="btn btn-warning" style="margin-top:1rem" onclick="changePassword()"><i class="fa-solid fa-floppy-disk"></i> Update Password</button>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><div class="card-title"><i class="fa-solid fa-circle-info" style="color:var(--accent)"></i> System Info</div></div>
          <div style="padding:1.25rem">
            <table style="width:100%">
              <tr><td style="color:var(--muted);font-size:0.82rem;padding:0.5rem 0;border-bottom:1px solid var(--border)">Project</td><td style="font-size:0.85rem;font-weight:600;padding:0.5rem 0;border-bottom:1px solid var(--border)">LuxRide Car Rental</td></tr>
              <tr><td style="color:var(--muted);font-size:0.82rem;padding:0.5rem 0;border-bottom:1px solid var(--border)">Database</td><td style="font-size:0.85rem;padding:0.5rem 0;border-bottom:1px solid var(--border)">luxride_db (MySQL)</td></tr>
              <tr><td style="color:var(--muted);font-size:0.82rem;padding:0.5rem 0;border-bottom:1px solid var(--border)">Server</td><td style="font-size:0.85rem;padding:0.5rem 0;border-bottom:1px solid var(--border)">Apache / XAMPP</td></tr>
              <tr><td style="color:var(--muted);font-size:0.82rem;padding:0.5rem 0;border-bottom:1px solid var(--border)">Admin User</td><td style="font-size:0.85rem;padding:0.5rem 0;border-bottom:1px solid var(--border)"><?= $adminUser ?></td></tr>
              <tr><td style="color:var(--muted);font-size:0.82rem;padding:0.5rem 0">Session</td><td id="session-time" style="font-size:0.85rem;padding:0.5rem 0"></td></tr>
            </table>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><div class="card-title"><i class="fa-solid fa-database" style="color:var(--purple)"></i> Database Quick Stats</div></div>
          <div style="padding:1.25rem" id="db-stats">
            <div class="empty"><i class="fa-solid fa-spinner fa-spin"></i></div>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><div class="card-title"><i class="fa-solid fa-shield-halved" style="color:var(--green)"></i> Security</div></div>
          <div style="padding:1.25rem">
            <div style="display:flex;flex-direction:column;gap:0.75rem">
              <div style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem;background:rgba(104,211,145,0.06);border:1px solid rgba(104,211,145,0.15);border-radius:10px">
                <span style="font-size:0.85rem"><i class="fa-solid fa-check" style="color:var(--green)"></i> Password Hashing</span>
                <span class="pill pill-active">bcrypt</span>
              </div>
              <div style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem;background:rgba(104,211,145,0.06);border:1px solid rgba(104,211,145,0.15);border-radius:10px">
                <span style="font-size:0.85rem"><i class="fa-solid fa-check" style="color:var(--green)"></i> Session Protection</span>
                <span class="pill pill-active">Active</span>
              </div>
              <div style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem;background:rgba(104,211,145,0.06);border:1px solid rgba(104,211,145,0.15);border-radius:10px">
                <span style="font-size:0.85rem"><i class="fa-solid fa-check" style="color:var(--green)"></i> Admin Auth Guard</span>
                <span class="pill pill-active">Enabled</span>
              </div>
              <div style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem;background:rgba(99,179,237,0.06);border:1px solid rgba(99,179,237,0.15);border-radius:10px">
                <span style="font-size:0.85rem"><i class="fa-solid fa-database" style="color:var(--accent)"></i> PDO Prepared Statements</span>
                <span class="pill pill-confirmed">Enabled</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div><!-- /content -->
</div><!-- /main -->

<!-- ══ MODALS ══════════════════════════════════════════════ -->

<!-- Edit User Modal -->
<div class="modal-overlay" id="modal-edit-user">
  <div class="modal">
    <div class="modal-title"><i class="fa-solid fa-user-pen"></i> Edit User</div>
    <input type="hidden" id="eu-id">
    <div class="form-grid">
      <div class="form-group"><label>Full Name *</label><input type="text" id="eu-name"></div>
      <div class="form-group"><label>Email *</label><input type="email" id="eu-email"></div>
      <div class="form-group"><label>Phone</label><input type="tel" id="eu-phone"></div>
      <div class="form-group"><label>New Password <small style="color:var(--muted)">(leave blank to keep)</small></label><input type="password" id="eu-pass" placeholder="Leave blank to keep current"></div>
      <div class="form-group"><label>Status</label>
        <select id="eu-status"><option value="active">Active</option><option value="suspended">Suspended</option></select>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn btn-secondary" onclick="closeModal('modal-edit-user')">Cancel</button>
      <button class="btn btn-primary" onclick="saveUser()"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
    </div>
  </div>
</div>

<!-- Edit Car Modal -->
<div class="modal-overlay" id="modal-edit-car">
  <div class="modal">
    <div class="modal-title"><i class="fa-solid fa-car-side"></i> Edit Car</div>
    <input type="hidden" id="ec-id">
    <div class="form-grid">
      <div class="form-group"><label>Car Name *</label><input type="text" id="ec-name"></div>
      <div class="form-group"><label>Brand *</label><input type="text" id="ec-brand"></div>
      <div class="form-group"><label>Category *</label>
        <select id="ec-type"><option value="sedan">Sedan</option><option value="suv">SUV</option><option value="luxury">Luxury</option><option value="electric">Electric</option><option value="sports">Sports</option><option value="supercar">Supercar</option><option value="pickup">Pickup</option><option value="vintage">Vintage</option><option value="family">Family</option><option value="offroad">Offroad</option></select>
      </div>
      <div class="form-group"><label>Price/Day (₹) *</label><input type="number" id="ec-price"></div>
      <div class="form-group"><label>Image Path *</label><input type="text" id="ec-image" oninput="previewCarImg('ec-image','ec-preview')"><img id="ec-preview" class="img-preview" style="display:block" alt="preview"></div>
      <div class="form-group"><label>Badge</label>
        <select id="ec-badge"><option value="">None</option><option value="new">New</option><option value="premium">Premium</option><option value="sale">Sale</option><option value="ultra">Ultra</option><option value="classic">Classic</option><option value="rare">Rare</option><option value="electric">Electric</option><option value="hybrid">Hybrid</option></select>
      </div>
      <div class="form-group"><label>Rating *</label><input type="number" id="ec-rating" step="0.1" min="1" max="5"></div>
      <div class="form-group"><label>Seats *</label>
        <select id="ec-seats"><option value="2">2</option><option value="4">4</option><option value="5">5</option><option value="7">7</option><option value="8">8</option></select>
      </div>
      <div class="form-group"><label>Transmission *</label>
        <select id="ec-trans"><option value="Auto">Automatic</option><option value="Manual">Manual</option><option value="PDK">PDK</option></select>
      </div>
      <div class="form-group"><label>Fuel *</label>
        <select id="ec-fuel"><option value="Petrol">Petrol</option><option value="Diesel">Diesel</option><option value="Electric">Electric</option><option value="Hybrid">Hybrid</option></select>
      </div>
    </div>
    <div class="modal-actions">
      <button class="btn btn-secondary" onclick="closeModal('modal-edit-car')">Cancel</button>
      <button class="btn btn-primary" onclick="saveCar()"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
    </div>
  </div>
</div>

<!-- Toast -->
<div class="toast" id="toast"></div>

<script>
// ══════════════════════════════════════════════════════════
// STATE
// ══════════════════════════════════════════════════════════
let allUsers=[], allCars=[], allBookings=[], allLogs=[], statsData={};
let revChart=null, typeChart=null, revChart2=null, typeChart2=null, growthChart=null, bStatusChart=null;
let allPayments = [];

  async function loadPayments() {
    try {
      const res = await fetch('../api/admin_payments.php?action=list');
      allPayments = await res.json();
      renderPaymentsTable(allPayments);
      // Load stats
      const sr = await fetch('../api/admin_payments.php?action=stats');
      const s  = await sr.json();
      document.getElementById('pay-total').textContent     = s.total;
      document.getElementById('pay-revenue').textContent   = '₹' + Number(s.revenue).toLocaleString('en-IN');
      document.getElementById('pay-today').textContent     = s.today;
      document.getElementById('pay-today-rev').textContent = '₹' + Number(s.todayRev).toLocaleString('en-IN');
      document.getElementById('nav-pay-count').textContent = s.total;
    } catch(e) { console.error(e); }
  }

  function renderPaymentsTable(payments) {
    const tb = document.getElementById('payments-tbody');
    if (!payments.length) { tb.innerHTML = '<tr><td colspan="10" class="empty">No payments found.</td></tr>'; return; }
    tb.innerHTML = payments.map(p => `<tr>
      <td style="font-size:.75rem;color:var(--accent)">${p.txn_id}</td>
      <td><b style="font-size:.8rem">${p.booking_id}</b></td>
      <td><div><b>${p.cust_name||'—'}</b><div style="font-size:.72rem;color:var(--muted)">${p.cust_email||''}</div></div></td>
      <td style="font-size:.82rem">${p.car_name||'—'}</td>
      <td><b style="color:var(--green)">₹${Number(p.amount).toLocaleString('en-IN')}</b></td>
      <td><span class="type-chip">${p.payment_method}</span></td>
      <td style="font-size:.78rem;color:var(--muted)">${p.method_detail||'—'}</td>
      <td><span class="pill pill-${p.status==='success'?'active':'cancelled'}">${p.status}</span></td>
      <td style="font-size:.75rem;color:var(--muted)">${new Date(p.created_at).toLocaleString('en-IN')}</td>
      <td><button class="btn btn-danger btn-sm" onclick="deletePayment(${p.id})"><i class="fa-solid fa-trash"></i></button></td>
    </tr>`).join('');
  }

  function filterPayments(q) {
    const m = document.querySelector('#sec-payments .filter-select')?.value || '';
    renderPaymentsTable(allPayments.filter(p =>
      (p.txn_id+p.booking_id+p.cust_name+p.cust_email).toLowerCase().includes(q.toLowerCase()) &&
      (m==='' || p.payment_method===m)
    ));
  }

  function filterPaymentsByMethod(m) {
    const q = document.querySelector('#sec-payments .search-input')?.value || '';
    renderPaymentsTable(allPayments.filter(p => (m===''||p.payment_method===m) && (p.txn_id+p.booking_id+p.cust_name).toLowerCase().includes(q.toLowerCase())));
  }

  async function deletePayment(id) {
    if (!confirm('Delete this payment record?')) return;
    const res = await fetch('../api/admin_payments.php?action=delete', {
      method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({id})
    });
    const d = await res.json();
    showToast(d.message, 'success');
    loadPayments();
  }
// ══════════════════════════════════════════════════════════
// NAVIGATION
// ══════════════════════════════════════════════════════════
function showSection(name, el) {
  document.querySelectorAll('.section').forEach(s=>s.classList.remove('active'));
  document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('active'));
  document.getElementById('sec-'+name).classList.add('active');
  if(el) el.classList.add('active');
  const titles = {overview:'Overview Dashboard',users:'User Management',cars:'Fleet Manager',bookings:'Booking Management',analytics:'Analytics',logs:'Activity Logs',settings:'Settings'};
  document.getElementById('topbar-title').innerHTML = `<span>${name.charAt(0).toUpperCase()+name.slice(1)}</span> ${titles[name]?.split(' ').slice(1).join(' ')||''}`;
  if(name==='analytics') renderAnalyticsCharts();
  if(name==='logs') loadLogs();
  if(name==='settings') loadDbStats();
}

// ══════════════════════════════════════════════════════════
// CLOCK
// ══════════════════════════════════════════════════════════
function updateClock(){
  const now=new Date();
  document.getElementById('live-time').textContent=now.toLocaleDateString('en-IN',{weekday:'short',day:'2-digit',month:'short'})+' · '+now.toLocaleTimeString('en-IN',{hour:'2-digit',minute:'2-digit'});
  const si=document.getElementById('session-time');
  if(si) si.textContent=now.toLocaleTimeString('en-IN');
}
setInterval(updateClock,1000); updateClock();

// ══════════════════════════════════════════════════════════
// LOAD OVERVIEW STATS
// ══════════════════════════════════════════════════════════
async function loadStats(){
  try {
    const res=await fetch('../api/admin_stats.php?action=overview');
    if(res.status===401){location.href='login.php';return;}
    statsData=await res.json();
    const {users,cars,bookings,revenue,charts,recentLogins}=statsData;

    // Overview cards
    document.getElementById('ov-users').textContent=users.totalUsers;
    document.getElementById('ov-users-sub').textContent=`${users.activeUsers} active · ${users.suspendedUsers} suspended`;
    document.getElementById('ov-cars').textContent=cars.totalCars;
    document.getElementById('ov-cars-sub').textContent=`${cars.carTypes} categories`;
    document.getElementById('ov-bookings').textContent=bookings.totalBookings;
    document.getElementById('ov-bookings-sub').textContent=`${bookings.todayBookings} today`;
    document.getElementById('ov-revenue').textContent='₹'+Number(revenue.total).toLocaleString('en-IN');
    document.getElementById('ov-revenue-sub').textContent='From completed bookings';
    document.getElementById('ov-newusers').textContent=users.newUsersToday;
    document.getElementById('ov-pending').textContent=bookings.pendingBookings;
    document.getElementById('ov-active').textContent=bookings.activeBookings;
    document.getElementById('ov-pending-rev').textContent='₹'+Number(revenue.pending).toLocaleString('en-IN');

    // Users section stats
    document.getElementById('u-total').textContent=users.totalUsers;
    document.getElementById('u-active').textContent=users.activeUsers;
    document.getElementById('u-suspended').textContent=users.suspendedUsers;
    document.getElementById('u-week').textContent=users.newUsersWeek;

    // Cars section stats
    document.getElementById('c-total').textContent=cars.totalCars;
    document.getElementById('c-types').textContent=cars.carTypes;
    document.getElementById('c-avg').textContent='₹'+cars.avgPrice;
    document.getElementById('c-top').textContent=cars.mostExpensive||'—';

    // Bookings section stats
    document.getElementById('b-total').textContent=bookings.totalBookings;
    document.getElementById('b-pending').textContent=bookings.pendingBookings;
    document.getElementById('b-active').textContent=bookings.activeBookings;
    document.getElementById('b-completed').textContent=bookings.completedBookings;
    document.getElementById('b-cancelled').textContent=bookings.cancelledBookings;

    // Sidebar badges
    document.getElementById('nav-users-count').textContent=users.totalUsers;
    document.getElementById('nav-cars-count').textContent=cars.totalCars;
    document.getElementById('nav-bookings-count').textContent=bookings.pendingBookings;

    // Top cars
    const tcBody=document.getElementById('top-cars-tbody');
    tcBody.innerHTML=charts.topCars.map((c,i)=>`<tr>
      <td><b style="color:var(--accent)">#${i+1}</b></td>
      <td><b>${c.car_name}</b></td><td>${c.car_brand}</td>
      <td><span style="color:var(--green);font-weight:700">${c.bookings}</span></td>
      <td><span style="color:var(--yellow)">₹${Number(c.revenue||0).toLocaleString('en-IN')}</span></td>
    </tr>`).join('') || '<tr><td colspan="4" class="empty">No data</td></tr>';

    // Recent logins
    const ll=document.getElementById('recent-logins-list');
    ll.innerHTML=recentLogins.map(l=>`<div class="log-entry">
      <div class="log-dot" style="background:var(--green)"></div>
      <div><div class="log-text"><b>${l.user_email}</b> — ${l.description||'Login'}</div>
      <div class="log-time">${l.ip_address} · ${new Date(l.created_at).toLocaleString('en-IN')}</div></div>
    </div>`).join('') || '<div class="empty">No recent logins</div>';

    // Overview charts
    renderOverviewCharts(charts);
  } catch(e){ console.error(e); }
}

function renderOverviewCharts(charts) {
  const cCfg = { responsive:true, maintainAspectRatio:true, plugins:{legend:{display:false}}, scales:{x:{grid:{color:'rgba(255,255,255,0.04)'},ticks:{color:'#718096',font:{size:11}}},y:{grid:{color:'rgba(255,255,255,0.04)'},ticks:{color:'#718096',font:{size:11}}}} };

  // Revenue chart
  if(revChart) revChart.destroy();
  revChart=new Chart(document.getElementById('revenueChart'),{type:'bar',data:{labels:charts.revenueByMonth.map(r=>r.month),datasets:[{label:'Revenue',data:charts.revenueByMonth.map(r=>r.revenue),backgroundColor:'rgba(99,179,237,0.3)',borderColor:'#63b3ed',borderWidth:2,borderRadius:6}]},options:{...cCfg,plugins:{legend:{display:false}}}});

  // Type chart
  if(typeChart) typeChart.destroy();
  const colors=['#63b3ed','#68d391','#b794f4','#f6e05e','#fc8181','#76e4f7','#fbb6ce','#9ae6b4','#d6bcfa','#fed7aa'];
  typeChart=new Chart(document.getElementById('typeChart'),{type:'doughnut',data:{labels:charts.carTypeData.map(t=>t.type),datasets:[{data:charts.carTypeData.map(t=>t.count),backgroundColor:colors.map(c=>c+'55'),borderColor:colors,borderWidth:2}]},options:{responsive:true,maintainAspectRatio:true,plugins:{legend:{position:'right',labels:{color:'#a0aec0',font:{size:11},boxWidth:12}}}}});
}

// ══════════════════════════════════════════════════════════
// USERS
// ══════════════════════════════════════════════════════════
async function loadUsers(){
  const res=await fetch('../api/admin_users.php?action=list');
  allUsers=await res.json();
  renderUsersTable(allUsers);
}
function renderUsersTable(users){
  const tb=document.getElementById('users-tbody');
  if(!users.length){tb.innerHTML='<tr><td colspan="9" class="empty">No users found.</td></tr>';return;}
  tb.innerHTML=users.map(u=>`<tr>
    <td><b style="color:var(--accent)">#${u.id}</b></td>
    <td><div style="display:flex;align-items:center;gap:0.6rem"><div class="user-avatar">${(u.full_name||'?').charAt(0).toUpperCase()}</div><b>${u.full_name}</b></div></td>
    <td>${u.email}</td><td>${u.phone||'—'}</td>
    <td><span class="pill pill-${u.status}">${u.status}</span></td>
    <td>${u.login_count||0}</td>
    <td style="color:var(--muted);font-size:0.78rem">${u.last_login?new Date(u.last_login).toLocaleDateString('en-IN'):'Never'}</td>
    <td style="color:var(--muted);font-size:0.78rem">${new Date(u.created_at).toLocaleDateString('en-IN')}</td>
    <td>
      <div style="display:flex;gap:0.4rem;flex-wrap:wrap">
        <button class="btn btn-secondary btn-sm" onclick="openEditUser(${u.id})"><i class="fa-solid fa-pen"></i></button>
        <button class="btn ${u.status==='active'?'btn-warning':'btn-success'} btn-sm" onclick="toggleUser(${u.id})">${u.status==='active'?'<i class="fa-solid fa-ban"></i>':'<i class="fa-solid fa-check"></i>'}</button>
        <button class="btn btn-danger btn-sm" onclick="deleteUser(${u.id},'${u.full_name}')"><i class="fa-solid fa-trash"></i></button>
      </div>
    </td>
  </tr>`).join('');
}
function filterUsers(q){
  const s=document.querySelector('#sec-users .filter-select')?.value||'';
  renderUsersTable(allUsers.filter(u=>(u.full_name+u.email).toLowerCase().includes(q.toLowerCase())&&(s===''||u.status===s)));
}
function filterUsersByStatus(s){
  const q=document.querySelector('#sec-users .search-input')?.value||'';
  renderUsersTable(allUsers.filter(u=>(s===''||u.status===s)&&(u.full_name+u.email).toLowerCase().includes(q.toLowerCase())));
}
async function addUser(){
  const body={full_name:document.getElementById('ua-name').value.trim(),email:document.getElementById('ua-email').value.trim(),phone:document.getElementById('ua-phone').value.trim(),password:document.getElementById('ua-pass').value,status:document.getElementById('ua-status').value};
  if(!body.full_name||!body.email||!body.password){showToast('Fill all required fields','error');return;}
  const res=await fetch('../api/admin_users.php?action=add',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});
  const d=await res.json();
  showToast(d.message,res.ok?'success':'error');
  if(res.ok){clearUserForm();loadUsers();loadStats();}
}
function clearUserForm(){['ua-name','ua-email','ua-phone','ua-pass'].forEach(id=>document.getElementById(id).value='');document.getElementById('ua-status').selectedIndex=0;}
function openEditUser(id){
  const u=allUsers.find(x=>x.id==id); if(!u)return;
  document.getElementById('eu-id').value=u.id;
  document.getElementById('eu-name').value=u.full_name;
  document.getElementById('eu-email').value=u.email;
  document.getElementById('eu-phone').value=u.phone||'';
  document.getElementById('eu-pass').value='';
  document.getElementById('eu-status').value=u.status;
  openModal('modal-edit-user');
}
async function saveUser(){
  const body={id:document.getElementById('eu-id').value,full_name:document.getElementById('eu-name').value.trim(),email:document.getElementById('eu-email').value.trim(),phone:document.getElementById('eu-phone').value.trim(),password:document.getElementById('eu-pass').value,status:document.getElementById('eu-status').value};
  const res=await fetch('../api/admin_users.php?action=edit',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});
  const d=await res.json();
  showToast(d.message,res.ok?'success':'error');
  if(res.ok){closeModal('modal-edit-user');loadUsers();}
}
async function toggleUser(id){
  const res=await fetch('../api/admin_users.php?action=toggle_status',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id})});
  const d=await res.json();
  showToast(d.message,'success');
  loadUsers(); loadStats();
}
async function deleteUser(id,name){
  if(!confirm(`Delete user "${name}"? This cannot be undone.`))return;
  const res=await fetch('../api/admin_users.php?action=delete',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id})});
  const d=await res.json();
  showToast(d.message,res.ok?'success':'error');
  if(res.ok){loadUsers();loadStats();}
}

// ══════════════════════════════════════════════════════════
// CARS
// ══════════════════════════════════════════════════════════
async function loadCars(){
  const res=await fetch('../api/get_cars.php');
  allCars=await res.json();
  renderCarsTable(allCars);
}
function renderCarsTable(cars){
  const tb=document.getElementById('cars-tbody');
  if(!cars.length){tb.innerHTML='<tr><td colspan="12" class="empty">No cars found.</td></tr>';return;}
  tb.innerHTML=cars.map(c=>`<tr>
    <td><b style="color:var(--accent)">#${c.id}</b></td>
    <td><img src="../${c.image}" class="car-thumb" onerror="this.src='https://via.placeholder.com/64x44?text=No+Img'"></td>
    <td><b>${c.name}</b></td><td>${c.brand}</td>
    <td><span class="type-chip">${c.type}</span></td>
    <td><b style="color:var(--green)">₹${Number(c.price).toLocaleString('en-IN')}</b></td>
    <td>${c.badge?`<span class="pill pill-${c.badge||'active'}">${c.badge}</span>`:'<span style="color:var(--muted)">—</span>'}</td>
    <td>⭐ ${c.rating}</td><td>${c.seats}</td><td>${c.transmission}</td><td>${c.fuel}</td>
    <td>
      <div style="display:flex;gap:0.4rem">
        <button class="btn btn-secondary btn-sm" onclick="openEditCar(${c.id})"><i class="fa-solid fa-pen"></i></button>
        <button class="btn btn-danger btn-sm" onclick="deleteCar(${c.id},'${c.name}')"><i class="fa-solid fa-trash"></i></button>
      </div>
    </td>
  </tr>`).join('');
}
function filterCarsTable(q){
  const t=document.querySelector('#sec-cars .filter-select')?.value||'';
  renderCarsTable(allCars.filter(c=>(c.name+c.brand).toLowerCase().includes(q.toLowerCase())&&(t===''||c.type===t)));
}
function filterCarsByType(t){
  const q=document.querySelector('#sec-cars .search-input')?.value||'';
  renderCarsTable(allCars.filter(c=>(t===''||c.type===t)&&(c.name+c.brand).toLowerCase().includes(q.toLowerCase())));
}
async function addCar(){
  const car={id:parseInt(document.getElementById('ca-id').value),name:document.getElementById('ca-name').value.trim(),brand:document.getElementById('ca-brand').value.trim(),type:document.getElementById('ca-type').value,price:parseInt(document.getElementById('ca-price').value),image:document.getElementById('ca-image').value.trim(),badge:document.getElementById('ca-badge').value,rating:parseFloat(document.getElementById('ca-rating').value),seats:parseInt(document.getElementById('ca-seats').value),transmission:document.getElementById('ca-trans').value,fuel:document.getElementById('ca-fuel').value};
  if(!car.id||!car.name||!car.brand||!car.type||!car.price||!car.image||!car.rating||!car.seats||!car.transmission||!car.fuel){showToast('Fill all required fields','error');return;}
  const res=await fetch('../api/cars_admin.php?action=add',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(car)});
  const d=await res.json();
  showToast(d.message,res.ok?'success':'error');
  if(res.ok){clearCarForm();loadCars();loadStats();}
}
function clearCarForm(){['ca-id','ca-name','ca-brand','ca-price','ca-image','ca-rating'].forEach(id=>document.getElementById(id).value='');['ca-type','ca-badge','ca-seats','ca-trans','ca-fuel'].forEach(id=>document.getElementById(id).selectedIndex=0);document.getElementById('ca-preview').style.display='none';}
function openEditCar(id){
  const c=allCars.find(x=>x.id==id);if(!c)return;
  document.getElementById('ec-id').value=c.id;
  document.getElementById('ec-name').value=c.name;
  document.getElementById('ec-brand').value=c.brand;
  document.getElementById('ec-type').value=c.type;
  document.getElementById('ec-price').value=c.price;
  document.getElementById('ec-image').value=c.image;
  document.getElementById('ec-badge').value=c.badge||'';
  document.getElementById('ec-rating').value=c.rating;
  document.getElementById('ec-seats').value=c.seats;
  document.getElementById('ec-trans').value=c.transmission;
  document.getElementById('ec-fuel').value=c.fuel;
  const p=document.getElementById('ec-preview');p.src='../'+c.image;p.style.display='block';
  openModal('modal-edit-car');
}
async function saveCar(){
  const car={id:document.getElementById('ec-id').value,name:document.getElementById('ec-name').value.trim(),brand:document.getElementById('ec-brand').value.trim(),type:document.getElementById('ec-type').value,price:parseInt(document.getElementById('ec-price').value),image:document.getElementById('ec-image').value.trim(),badge:document.getElementById('ec-badge').value,rating:parseFloat(document.getElementById('ec-rating').value),seats:parseInt(document.getElementById('ec-seats').value),transmission:document.getElementById('ec-trans').value,fuel:document.getElementById('ec-fuel').value};
  const res=await fetch('../api/cars_admin.php?action=edit',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(car)});
  const d=await res.json();
  showToast(d.message,res.ok?'success':'error');
  if(res.ok){closeModal('modal-edit-car');loadCars();}
}
async function deleteCar(id,name){
  if(!confirm(`Delete "${name}"? This cannot be undone.`))return;
  const res=await fetch('../api/cars_admin.php?action=delete',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id})});
  const d=await res.json();
  showToast(d.message,res.ok?'success':'error');
  if(res.ok){loadCars();loadStats();}
}
function previewCarImg(inputId,previewId){
  const v=document.getElementById(inputId).value;
  const img=document.getElementById(previewId);
  img.src='../'+v; img.style.display=v?'block':'none';
}

// ══════════════════════════════════════════════════════════
// BOOKINGS
// ══════════════════════════════════════════════════════════
async function loadBookings(){
  const res=await fetch('../api/admin_bookings.php?action=list');
  allBookings=await res.json();
  renderBookingsTable(allBookings);
}
function renderBookingsTable(bks){
  const tb=document.getElementById('bookings-tbody');
  if(!bks.length){tb.innerHTML='<tr><td colspan="10" class="empty">No bookings found.</td></tr>';return;}
  tb.innerHTML=bks.map(b=>`<tr>
    <td><b style="color:var(--accent);font-size:0.8rem">${b.booking_ref}</b></td>
    <td><div><b>${b.user_name||'—'}</b><div style="font-size:0.75rem;color:var(--muted)">${b.user_email||''}</div></div></td>
    <td><div><b>${b.car_name||'—'}</b><div style="font-size:0.75rem;color:var(--muted)">${b.car_brand||''}</div></div></td>
    <td style="font-size:0.8rem;color:var(--muted)">${b.pickup_location||'—'}</td>
    <td><b>${b.duration}</b> ${b.rate_type==='hourly'?'hr':'day'}(s)</td>
    <td><b style="color:var(--green)">₹${Number(b.total_amount||0).toLocaleString('en-IN')}</b></td>
    <td><span class="pill pill-${b.booking_type==='self'?'confirmed':'active'}">${b.booking_type}</span></td>
    <td><span class="pill pill-${b.status}">${b.status}</span></td>
    <td style="font-size:0.78rem;color:var(--muted)">${new Date(b.created_at).toLocaleDateString('en-IN')}</td>
    <td>
      <div style="display:flex;gap:0.4rem;flex-wrap:wrap">
        <select class="filter-select" style="font-size:0.75rem;padding:0.25rem 0.5rem" onchange="updateBookingStatus(${b.id},this.value)">
          <option value="">Status...</option>
          <option value="pending" ${b.status==='pending'?'selected':''}>Pending</option>
          <option value="confirmed" ${b.status==='confirmed'?'selected':''}>Confirmed</option>
          <option value="active" ${b.status==='active'?'selected':''}>Active</option>
          <option value="completed" ${b.status==='completed'?'selected':''}>Completed</option>
          <option value="cancelled" ${b.status==='cancelled'?'selected':''}>Cancelled</option>
        </select>
        <button class="btn btn-danger btn-sm" onclick="deleteBooking(${b.id})"><i class="fa-solid fa-trash"></i></button>
      </div>
    </td>
  </tr>`).join('');
}
function filterBookings(q){
  const s=document.querySelector('#sec-bookings .filter-select')?.value||'';
  renderBookingsTable(allBookings.filter(b=>(b.booking_ref+b.user_name+b.user_email).toLowerCase().includes(q.toLowerCase())&&(s===''||b.status===s)));
}
function filterBookingsByStatus(s){
  const q=document.querySelector('#sec-bookings .search-input')?.value||'';
  renderBookingsTable(allBookings.filter(b=>(s===''||b.status===s)&&(b.booking_ref+b.user_name).toLowerCase().includes(q.toLowerCase())));
}
async function updateBookingStatus(id,status){
  if(!status)return;
  const res=await fetch('../api/admin_bookings.php?action=update_status',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id,status})});
  const d=await res.json();
  showToast(d.message,'success');
  loadBookings(); loadStats();
}
async function deleteBooking(id){
  if(!confirm('Delete this booking?'))return;
  const res=await fetch('../api/admin_bookings.php?action=delete',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id})});
  const d=await res.json();
  showToast(d.message,res.ok?'success':'error');
  if(res.ok){loadBookings();loadStats();}
}

// ══════════════════════════════════════════════════════════
// ANALYTICS CHARTS
// ══════════════════════════════════════════════════════════
function renderAnalyticsCharts(){
  if(!statsData.charts) return;
  const {carTypeData,revenueByMonth,topCars,userGrowth}=statsData.charts;
  const {bookings}=statsData;
  const gridCfg={grid:{color:'rgba(255,255,255,0.04)'},ticks:{color:'#718096',font:{size:11}}};
  const opts={responsive:true,maintainAspectRatio:true,plugins:{legend:{display:false}},scales:{x:gridCfg,y:gridCfg}};

  if(revChart2) revChart2.destroy();
  revChart2=new Chart(document.getElementById('revenueChart2'),{type:'line',data:{labels:revenueByMonth.map(r=>r.month),datasets:[{label:'Revenue',data:revenueByMonth.map(r=>r.revenue),borderColor:'#63b3ed',backgroundColor:'rgba(99,179,237,0.1)',fill:true,tension:0.4,pointBackgroundColor:'#63b3ed',pointRadius:5}]},options:opts});

  if(growthChart) growthChart.destroy();
  growthChart=new Chart(document.getElementById('userGrowthChart'),{type:'bar',data:{labels:userGrowth.map(r=>r.day),datasets:[{data:userGrowth.map(r=>r.count),backgroundColor:'rgba(104,211,145,0.4)',borderColor:'#68d391',borderWidth:2,borderRadius:6}]},options:opts});

  if(typeChart2) typeChart2.destroy();
  const colors=['#63b3ed','#68d391','#b794f4','#f6e05e','#fc8181','#76e4f7','#fbb6ce','#9ae6b4','#d6bcfa','#fed7aa'];
  typeChart2=new Chart(document.getElementById('typeChart2'),{type:'pie',data:{labels:carTypeData.map(t=>t.type),datasets:[{data:carTypeData.map(t=>t.count),backgroundColor:colors.map(c=>c+'55'),borderColor:colors,borderWidth:2}]},options:{responsive:true,maintainAspectRatio:true,plugins:{legend:{position:'right',labels:{color:'#a0aec0',font:{size:11},boxWidth:12}}}}});

  if(bStatusChart) bStatusChart.destroy();
  bStatusChart=new Chart(document.getElementById('bookingStatusChart'),{type:'bar',data:{
    labels:['Pending','Active','Completed','Cancelled'],
    datasets:[{data:[bookings.pendingBookings,bookings.activeBookings,bookings.completedBookings,bookings.cancelledBookings],backgroundColor:['rgba(246,224,94,0.3)','rgba(99,179,237,0.3)','rgba(104,211,145,0.3)','rgba(252,129,129,0.3)'],borderColor:['#f6e05e','#63b3ed','#68d391','#fc8181'],borderWidth:2,borderRadius:6}]},
    options:opts});

  // Top cars table 2
  const tc2=document.getElementById('top-cars-tbody2');
  tc2.innerHTML=topCars.map((c,i)=>`<tr><td><b style="color:var(--accent)">#${i+1}</b></td><td><b>${c.car_name}</b></td><td>${c.car_brand}</td><td style="color:var(--green);font-weight:700">${c.bookings}</td><td style="color:var(--yellow)">₹${Number(c.revenue||0).toLocaleString('en-IN')}</td></tr>`).join('')||'<tr><td colspan="5" class="empty">No data</td></tr>';
}

// ══════════════════════════════════════════════════════════
// LOGS
// ══════════════════════════════════════════════════════════
async function loadLogs(){
  try {
    const res=await fetch('../api/admin_stats.php?action=overview');
    const d=await res.json();
    allLogs=d.recentLogins||[];
    renderLogs(allLogs);
  } catch(e){}
}
function renderLogs(logs){
  const c=document.getElementById('logs-container');
  if(!logs.length){c.innerHTML='<div class="empty"><i class="fa-solid fa-inbox"></i><br>No logs found.</div>';return;}
  const dotColors={login:'var(--green)',logout:'var(--red)',register:'var(--accent)'};
  c.innerHTML=logs.map(l=>`<div class="log-entry">
    <div class="log-dot" style="background:${dotColors[l.action]||'var(--muted)'}"></div>
    <div>
      <div class="log-text"><b>${l.user_email}</b> — ${l.description||l.action}</div>
      <div class="log-time"><i class="fa-solid fa-location-dot"></i> ${l.ip_address||'—'} &nbsp;·&nbsp; ${new Date(l.created_at).toLocaleString('en-IN')}</div>
    </div>
  </div>`).join('');
}
function filterLogs(q){renderLogs(allLogs.filter(l=>(l.user_email+l.description).toLowerCase().includes(q.toLowerCase())));}
function filterLogsByAction(a){renderLogs(allLogs.filter(l=>a===''||l.action===a));}

// ══════════════════════════════════════════════════════════
// SETTINGS
// ══════════════════════════════════════════════════════════
async function changePassword(){
  const curr=document.getElementById('s-curr').value;
  const n=document.getElementById('s-new').value;
  const c=document.getElementById('s-conf').value;
  if(!curr||!n||!c){showToast('Fill all fields','error');return;}
  if(n!==c){showToast('Passwords do not match','error');return;}
  if(n.length<8){showToast('Min 8 characters','error');return;}
  showToast('Password change — connect to api/change_password.php to complete','success');
  ['s-curr','s-new','s-conf'].forEach(id=>document.getElementById(id).value='');
}
async function loadDbStats(){
  if(!statsData.users) return;
  document.getElementById('db-stats').innerHTML=`
    <table style="width:100%">
      <tr><td style="color:var(--muted);font-size:0.82rem;padding:0.5rem 0;border-bottom:1px solid var(--border)">Users Table</td><td style="font-size:0.85rem;font-weight:600;padding:0.5rem 0;border-bottom:1px solid var(--border)">${statsData.users.totalUsers} rows</td></tr>
      <tr><td style="color:var(--muted);font-size:0.82rem;padding:0.5rem 0;border-bottom:1px solid var(--border)">Cars Table</td><td style="font-size:0.85rem;font-weight:600;padding:0.5rem 0;border-bottom:1px solid var(--border)">${statsData.cars.totalCars} rows</td></tr>
      <tr><td style="color:var(--muted);font-size:0.82rem;padding:0.5rem 0;border-bottom:1px solid var(--border)">Bookings Table</td><td style="font-size:0.85rem;font-weight:600;padding:0.5rem 0;border-bottom:1px solid var(--border)">${statsData.bookings.totalBookings} rows</td></tr>
      <tr><td style="color:var(--muted);font-size:0.82rem;padding:0.5rem 0">Activity Logs</td><td style="font-size:0.85rem;font-weight:600;padding:0.5rem 0">${statsData.recentLogins?.length||0}+ entries</td></tr>
    </table>`;
}

// ══════════════════════════════════════════════════════════
// MODAL HELPERS
// ══════════════════════════════════════════════════════════
function openModal(id){document.getElementById(id).classList.add('active');}
function closeModal(id){document.getElementById(id).classList.remove('active');}
document.querySelectorAll('.modal-overlay').forEach(m=>{m.addEventListener('click',e=>{if(e.target===m)m.classList.remove('active');});});

// ══════════════════════════════════════════════════════════
// TOAST
// ══════════════════════════════════════════════════════════
function showToast(msg,type=''){
  const t=document.getElementById('toast');
  t.textContent=msg; t.className='toast show'+(type?' '+type:'');
  setTimeout(()=>t.className='toast',3500);
}

// ══════════════════════════════════════════════════════════
// REFRESH ALL
// ══════════════════════════════════════════════════════════
function refreshAll(){
  loadStats(); loadUsers(); loadCars(); loadBookings();
  showToast('Data refreshed!','success');
}

// ══════════════════════════════════════════════════════════
// INIT
// ══════════════════════════════════════════════════════════
loadStats();
loadUsers();
loadCars();
loadBookings();
loadPayments();
</script>
</body>
</html>