<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LuxRide — Secure Payment</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
  <style>
    :root {
      --accent: #1e40af;
      --amber:  #f59e0b;
      --green:  #16a34a;
      --red:    #dc2626;
      --bg:     #f8fafc;
      --card:   #ffffff;
      --border: #e2e8f0;
      --text:   #0f172a;
      --muted:  #64748b;
    }
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: system-ui, -apple-system, sans-serif;
      background: var(--bg); color: var(--text);
      min-height: 100vh; display: flex; flex-direction: column;
    }

    /* ── HEADER ── */
    .header {
      background: #0f172a; padding: 1rem 2rem;
      display: flex; align-items: center; justify-content: space-between;
      flex-shrink: 0;
    }
    .logo { font-size: 1.3rem; font-weight: 800; color: #fff; text-decoration: none; }
    .logo span { color: var(--amber); }
    .secure-badge { font-size: .8rem; color: #22c55e; display: flex; align-items: center; gap: .4rem; }

    /* ── MAIN ── */
    .main {
      flex: 1; display: flex; align-items: flex-start;
      justify-content: center; padding: 2.5rem 1rem;
      gap: 2rem; flex-wrap: wrap;
    }

    /* ── CARDS ── */
    .summary-card, .payment-card {
      background: var(--card); border: 1px solid var(--border);
      border-radius: 16px; padding: 1.75rem; width: 100%;
    }
    .summary-card { max-width: 380px; }
    .payment-card { max-width: 420px; }

    .card-title {
      font-size: 1rem; font-weight: 700; margin-bottom: 1.25rem;
      display: flex; align-items: center; gap: .5rem;
    }
    .card-title i { color: var(--accent); }

    /* ── CAR PREVIEW ── */
    .car-preview {
      display: flex; gap: 1rem; align-items: center;
      padding: 1rem; background: #f8fafc;
      border: 1px solid var(--border); border-radius: 10px;
      margin-bottom: 1.25rem;
    }
    .car-preview img { width: 80px; height: 54px; object-fit: cover; border-radius: 8px; }
    .car-nm { font-weight: 700; font-size: .9rem; }
    .car-meta { font-size: .75rem; color: var(--muted); margin-top: .2rem; }

    /* ── PRICE ROWS ── */
    .price-row { display: flex; justify-content: space-between; padding: .5rem 0; font-size: .875rem; border-bottom: 1px solid #f1f5f9; }
    .price-row:last-child { border: none; }
    .price-row.total { font-weight: 800; font-size: 1.05rem; padding-top: .75rem; border-top: 2px solid var(--border); }
    .price-row.total span:last-child { color: var(--accent); }

    .booking-badge {
      background: #f0fdf4; border: 1px solid #bbf7d0;
      color: #166534; padding: .5rem .85rem; border-radius: 8px;
      font-size: .8rem; font-weight: 600; text-align: center; margin-top: .85rem;
    }
    .mini-badges { display: flex; gap: .5rem; flex-wrap: wrap; margin-top: .75rem; }
    .mb { font-size: .7rem; font-weight: 700; padding: .2rem .55rem; border-radius: 4px; }
    .mb-green { background: #dcfce7; color: #166534; }
    .mb-blue  { background: #dbeafe; color: #1e40af; }

    /* ── METHOD TABS ── */
    .method-tabs { display: flex; gap: .5rem; margin-bottom: 1.25rem; }
    .method-tab {
      flex: 1; padding: .6rem .4rem; border-radius: 10px; cursor: pointer;
      border: 1.5px solid var(--border); background: #f8fafc;
      text-align: center; font-size: .75rem; font-weight: 600; color: var(--muted);
      transition: all .2s; display: flex; flex-direction: column; align-items: center; gap: .25rem;
    }
    .method-tab i { font-size: 1.1rem; }
    .method-tab.active { border-color: var(--accent); background: #eff6ff; color: var(--accent); }

    /* ── RAZORPAY SECTION ── */
    .rzp-box { text-align: center; padding: 1.5rem 0; }
    .rzp-icon { font-size: 2.5rem; margin-bottom: .75rem; }
    .rzp-title { font-weight: 700; font-size: 1rem; margin-bottom: .35rem; }
    .rzp-sub { font-size: .82rem; color: var(--muted); }

    /* ── UPI SECTION ── */
    .qr-box { text-align: center; padding: .5rem 0; }
    .qr-wrap {
      background: #fff; border: 1px solid var(--border);
      border-radius: 12px; padding: 10px; display: inline-block; margin-bottom: .85rem;
    }
    .qr-wrap img { width: 160px; height: 160px; display: block; }
    .upi-id-txt { font-size: .8rem; color: var(--muted); margin-bottom: 1rem; }
    .upi-id-txt b { color: var(--accent); }
    .app-btns { display: flex; gap: .5rem; justify-content: center; flex-wrap: wrap; margin-bottom: 1rem; }
    .app-btn {
      display: flex; align-items: center; gap: .4rem;
      padding: .45rem .9rem; border-radius: 8px;
      border: 1.5px solid var(--border); background: #f8fafc;
      font-size: .8rem; font-weight: 600; cursor: pointer;
      transition: all .2s; color: var(--text);
    }
    .app-btn:hover { border-color: var(--accent); background: #eff6ff; color: var(--accent); }
    .app-btn img { width: 20px; height: 20px; object-fit: contain; }
    .divider { display: flex; align-items: center; gap: .75rem; color: var(--muted); font-size: .75rem; margin-bottom: 1rem; }
    .divider::before, .divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }

    /* ── CARD FORM ── */
    .card-visual {
      background: linear-gradient(135deg, #1e3a5f, #0f2244);
      border-radius: 12px; padding: 1.2rem 1.4rem;
      margin-bottom: 1.25rem; min-height: 120px;
      position: relative; overflow: hidden;
    }
    .card-visual::before {
      content: ''; position: absolute; top: -30px; right: -30px;
      width: 130px; height: 130px; border-radius: 50%;
      background: rgba(59,130,246,.12);
    }
    .card-chip { width: 32px; height: 24px; background: linear-gradient(135deg, #d4a93e, #f0d070); border-radius: 4px; margin-bottom: .75rem; }
    .card-num-vis { font-size: .95rem; letter-spacing: .15em; color: #fff; margin-bottom: .65rem; font-family: monospace; }
    .card-bot { display: flex; justify-content: space-between; }
    .card-fld-lbl { font-size: .55rem; text-transform: uppercase; letter-spacing: .1em; color: rgba(255,255,255,.45); }
    .card-fld-val { font-size: .78rem; color: #fff; font-weight: 600; margin-top: .1rem; }
    .card-brand-ico { font-size: 1.3rem; color: rgba(255,255,255,.7); }

    /* ── FORM FIELDS ── */
    .fg { display: flex; flex-direction: column; gap: .3rem; margin-bottom: .9rem; }
    .fg label { font-size: .7rem; text-transform: uppercase; letter-spacing: .07em; color: var(--muted); font-weight: 700; }
    .fg input, .fg select {
      padding: .65rem .85rem; border: 1.5px solid var(--border);
      border-radius: 8px; font-size: .9rem; outline: none;
      transition: border .2s; color: var(--text); background: #fff;
    }
    .fg input:focus { border-color: var(--accent); }
    .frow { display: grid; grid-template-columns: 1fr 1fr; gap: .85rem; }
    .field-err { font-size: .7rem; color: var(--red); display: none; margin-top: .15rem; }

    /* ── PAY BUTTON ── */
    .pay-btn {
      width: 100%; padding: .875rem; border: none; border-radius: 10px;
      background: linear-gradient(135deg, #1d4ed8, #3b82f6);
      color: #fff; font-size: 1rem; font-weight: 700; cursor: pointer;
      transition: all .2s; display: flex; align-items: center;
      justify-content: center; gap: .5rem;
      box-shadow: 0 4px 16px rgba(59,130,246,.3);
      margin-top: .25rem;
    }
    .pay-btn:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(59,130,246,.4); }
    .pay-btn:disabled { opacity: .65; cursor: not-allowed; transform: none; }
    .spin {
      width: 18px; height: 18px; border: 2px solid rgba(255,255,255,.3);
      border-top-color: #fff; border-radius: 50%;
      animation: spin .7s linear infinite; display: none;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    .ssl-note { text-align: center; font-size: .72rem; color: var(--muted); margin-top: .85rem; display: flex; align-items: center; justify-content: center; gap: .35rem; }

    /* ── SUCCESS SCREEN ── */
    .success-wrap {
      display: none; text-align: center; background: var(--card);
      border: 1px solid var(--border); border-radius: 16px;
      padding: 3rem 2rem; max-width: 440px; width: 100%;
    }
    .success-wrap.show { display: block; animation: fadeIn .4s ease; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: none; } }
    .success-icon {
      width: 68px; height: 68px; border-radius: 50%;
      background: #dcfce7; border: 2px solid #86efac;
      display: flex; align-items: center; justify-content: center;
      margin: 0 auto 1.25rem; font-size: 1.8rem; color: var(--green);
    }
    .success-title { font-size: 1.4rem; font-weight: 800; margin-bottom: .4rem; }
    .success-sub { color: var(--muted); margin-bottom: 1.5rem; }
    .success-details {
      background: #f8fafc; border: 1px solid var(--border);
      border-radius: 10px; padding: 1rem 1.25rem;
      text-align: left; margin-bottom: 1.25rem;
    }
    .srow { display: flex; justify-content: space-between; padding: .4rem 0; font-size: .85rem; border-bottom: 1px solid #f1f5f9; }
    .srow:last-child { border: none; }
    .srow span:last-child { font-weight: 700; color: var(--accent); }
    .success-btns { display: flex; gap: .75rem; justify-content: center; flex-wrap: wrap; }
    .btn-home { padding: .7rem 1.5rem; background: var(--accent); color: #fff; border-radius: 10px; text-decoration: none; font-weight: 700; font-size: .9rem; }
    .btn-dash  { padding: .7rem 1.5rem; background: #f1f5f9; color: var(--text); border-radius: 10px; text-decoration: none; font-weight: 700; font-size: .9rem; }

    @media (max-width: 640px) {
      .main { padding: 1.25rem .75rem; }
      .frow { grid-template-columns: 1fr; }
      .summary-card, .payment-card { max-width: 100%; }
    }
  </style>
</head>
<body>

<div class="header">
  <a href="index.html" class="logo">✨ Lux<span>Ride</span></a>
  <div class="secure-badge"><i class="fa-solid fa-lock"></i> 256-bit SSL Secured</div>
</div>

<!-- PAYMENT FORM -->
<div class="main" id="main-wrap">

  <!-- ORDER SUMMARY -->
  <div class="summary-card">
    <div class="card-title"><i class="fa-solid fa-receipt"></i> Order Summary</div>
    <div class="car-preview">
      <img id="s-img" src="" onerror="this.src='https://via.placeholder.com/80x54?text=Car'" alt="Car">
      <div>
        <div class="car-nm" id="s-car-name">Loading...</div>
        <div class="car-meta" id="s-car-meta"></div>
      </div>
    </div>
    <div id="s-price-rows"></div>
    <div class="booking-badge">Booking ID: <b id="s-bid">—</b></div>
    <div class="mini-badges">
      <span class="mb mb-green"><i class="fa-solid fa-rotate-left"></i> Free Cancellation</span>
      <span class="mb mb-blue"><i class="fa-solid fa-bolt"></i> Instant Confirmation</span>
    </div>
  </div>

  <!-- PAYMENT CARD -->
  <div class="payment-card">
    <div class="card-title"><i class="fa-solid fa-credit-card"></i> Payment Details</div>

    <!-- TABS -->
    <div class="method-tabs">
      <div class="method-tab active" onclick="setMethod('razorpay',this)">
        <i class="fa-solid fa-bolt"></i> Razorpay
      </div>
      <div class="method-tab" onclick="setMethod('upi',this)">
        <i class="fa-solid fa-mobile-screen"></i> UPI
      </div>
      <div class="method-tab" onclick="setMethod('card',this)">
        <i class="fa-solid fa-credit-card"></i> Card
      </div>
    </div>

    <!-- RAZORPAY -->
    <div id="form-razorpay">
      <div class="rzp-box">
        <div class="rzp-icon">⚡</div>
        <div class="rzp-title">Pay with Razorpay</div>
        <div class="rzp-sub">UPI, Cards, Net Banking, Wallets — all in one secure popup.</div>
      </div>
    </div>

    <!-- UPI -->
    <div id="form-upi" style="display:none">
      <div class="qr-box">
        <div class="qr-wrap"><img id="upi-qr" src="" alt="UPI QR Code"></div>
        <div class="upi-id-txt">Scan &amp; pay to <b>yadanapurimanish@okhdfcbank</b></div>
        <div class="app-btns">
          <span class="app-btn" onclick="openApp('gpay')">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/f/f2/Google_Pay_Logo.svg/64px-Google_Pay_Logo.svg.png" alt="GPay"> GPay
          </span>
          <span class="app-btn" onclick="openApp('phonepe')">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5f/PhonePe_Logo.svg/64px-PhonePe_Logo.svg.png" alt="PhonePe"> PhonePe
          </span>
          <span class="app-btn" onclick="openApp('paytm')">
            <img src="https://upload.wikimedia.org/wikipedia/commons/4/42/Paytm_logo.png" alt="Paytm"> Paytm
          </span>
        </div>
        <div class="divider">or enter UPI ID manually</div>
        <div class="fg">
          <label>Your UPI ID</label>
          <input type="text" id="upi-input" placeholder="yourname@okaxis">
          <span class="field-err" id="err-upi">Enter a valid UPI ID (e.g. name@okaxis)</span>
        </div>
      </div>
    </div>

    <!-- CARD -->
    <div id="form-card" style="display:none">
      <div class="card-visual">
        <div class="card-chip"></div>
        <div class="card-num-vis" id="vis-num">•••• •••• •••• ••••</div>
        <div class="card-bot">
          <div><div class="card-fld-lbl">Cardholder</div><div class="card-fld-val" id="vis-name">YOUR NAME</div></div>
          <div><div class="card-fld-lbl">Expires</div><div class="card-fld-val" id="vis-exp">MM/YY</div></div>
          <div class="card-brand-ico" id="vis-brand"><i class="fa-brands fa-cc-visa"></i></div>
        </div>
      </div>
      <div class="fg">
        <label>Card Number</label>
        <input type="text" id="c-num" placeholder="1234 5678 9012 3456" maxlength="19" oninput="fmtCard(this)">
        <span class="field-err" id="err-num">Enter a valid 16-digit card number</span>
      </div>
      <div class="fg">
        <label>Cardholder Name</label>
        <input type="text" id="c-name" placeholder="Name as on card" oninput="updVis()">
        <span class="field-err" id="err-cname">Enter cardholder name</span>
      </div>
      <div class="frow">
        <div class="fg">
          <label>Expiry</label>
          <input type="text" id="c-exp" placeholder="MM/YY" maxlength="5" oninput="fmtExp(this)">
          <span class="field-err" id="err-exp">Invalid expiry date</span>
        </div>
        <div class="fg">
          <label>CVV</label>
          <input type="password" id="c-cvv" placeholder="•••" maxlength="4">
          <span class="field-err" id="err-cvv">Enter valid CVV</span>
        </div>
      </div>
    </div>

    <!-- PAY BUTTON -->
    <button class="pay-btn" id="pay-btn" onclick="pay()">
      <div class="spin" id="spin"></div>
      <i class="fa-solid fa-lock" id="pay-icon"></i>
      <span>Pay ₹<span id="pay-amt">0</span></span>
    </button>
    <div class="ssl-note">
      <i class="fa-solid fa-shield-halved"></i> Your payment is encrypted &amp; secure
    </div>
  </div>

</div><!-- /main-wrap -->

<!-- SUCCESS SCREEN -->
<div class="main" id="success-wrap" style="display:none">
  <div class="success-wrap show">
    <div class="success-icon"><i class="fa-solid fa-check"></i></div>
    <div class="success-title">Booking Confirmed! 🎉</div>
    <div class="success-sub">Your payment was successful. Enjoy your LuxRide!</div>
    <div class="success-details">
      <div class="srow"><span>Booking ID</span><span id="ss-bid">—</span></div>
      <div class="srow"><span>Car</span><span id="ss-car">—</span></div>
      <div class="srow"><span>Amount Paid</span><span id="ss-amt">—</span></div>
      <div class="srow"><span>Payment</span><span id="ss-method">—</span></div>
      <div class="srow"><span>Transaction ID</span><span id="ss-txn">—</span></div>
      <div class="srow"><span>Status</span><span style="color:#16a34a">✅ Confirmed</span></div>
    </div>
    <div class="success-btns">
      <a href="index.html" class="btn-home"><i class="fa-solid fa-house"></i> Home</a>
      <a href="user/dashboard.php" class="btn-dash"><i class="fa-solid fa-gauge"></i> My Bookings</a>
    </div>
  </div>
</div>

<script>
// ── READ URL PARAMS ───────────────────────────────────────
const P         = new URLSearchParams(window.location.search);
const bookingId = P.get('booking_id') || ('LUX-' + Date.now());
const carName   = P.get('car_name')   || 'Car';
const carBrand  = P.get('car_brand')  || '';
const carImage  = P.get('car_image')  || '';
const carType   = P.get('car_type')   || '';
const totalAmt  = parseFloat(P.get('total') || 0);
const base      = parseFloat(P.get('base')  || 0);
const gst       = parseFloat(P.get('gst')   || 0);
const sgst      = parseFloat(P.get('sgst')  || 0);
const duration  = P.get('duration')   || '1';
const rateType  = P.get('rate_type')  || 'daily';
const custName  = P.get('cust_name')  || '';
const custEmail = P.get('cust_email') || '';
const custPhone = P.get('cust_phone') || '';
const MERCHANT  = 'yadanapurimanish@okhdfcbank';

// ── POPULATE SUMMARY ─────────────────────────────────────
document.getElementById('s-bid').textContent       = bookingId;
document.getElementById('s-img').src               = carImage;
document.getElementById('s-car-name').textContent  = carBrand + ' ' + carName;
document.getElementById('s-car-meta').textContent  = carType + ' | ' + duration + ' ' + (rateType === 'hourly' ? 'hr(s)' : 'day(s)');
document.getElementById('pay-amt').textContent     = Math.round(totalAmt).toLocaleString('en-IN');
document.getElementById('s-price-rows').innerHTML  = `
  <div class="price-row"><span style="color:#64748b">Base Rent</span><span>₹${Math.round(base).toLocaleString('en-IN')}</span></div>
  <div class="price-row"><span style="color:#64748b">GST (9%)</span><span>₹${Math.round(gst).toLocaleString('en-IN')}</span></div>
  <div class="price-row"><span style="color:#64748b">SGST (9%)</span><span>₹${Math.round(sgst).toLocaleString('en-IN')}</span></div>
  <div class="price-row total"><span>Total</span><span>₹${Math.round(totalAmt).toLocaleString('en-IN')}</span></div>`;

// ── UPI QR ────────────────────────────────────────────────
const upiNote  = encodeURIComponent('LuxRide ' + bookingId);
const upiURI   = `upi://pay?pa=${MERCHANT}&pn=LuxRide&am=${Math.round(totalAmt).toFixed(2)}&cu=INR&tn=${upiNote}`;
document.getElementById('upi-qr').src = `https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=${encodeURIComponent(upiURI)}&margin=4`;

// ── UPI APP LINKS ─────────────────────────────────────────
const appURIs = {
  gpay:    `tez://upi/pay?pa=${MERCHANT}&pn=LuxRide&am=${Math.round(totalAmt).toFixed(2)}&cu=INR&tn=${upiNote}`,
  phonepe: `phonepe://pay?pa=${MERCHANT}&pn=LuxRide&am=${Math.round(totalAmt).toFixed(2)}&cu=INR&tn=${upiNote}`,
  paytm:   `paytmmp://pay?pa=${MERCHANT}&pn=LuxRide&am=${Math.round(totalAmt).toFixed(2)}&cu=INR&tn=${upiNote}`
};
function openApp(app) {
  if (/Android|iPhone|iPad/i.test(navigator.userAgent)) {
    window.location.href = appURIs[app];
    setTimeout(() => {
      if (confirm('Did you complete the payment?\nTap OK to confirm your booking.')) {
        sendPayment('upi', 'UPI via ' + app + ' → ' + MERCHANT);
      }
    }, 2500);
  } else {
    alert('On desktop, scan the QR code with your ' + app + ' app.\nUPI ID: ' + MERCHANT);
  }
}

// ── METHOD TABS ───────────────────────────────────────────
let activeMethod = 'razorpay';
function setMethod(m, el) {
  activeMethod = m;
  document.querySelectorAll('.method-tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  document.getElementById('form-razorpay').style.display = m === 'razorpay' ? 'block' : 'none';
  document.getElementById('form-upi').style.display      = m === 'upi'      ? 'block' : 'none';
  document.getElementById('form-card').style.display     = m === 'card'     ? 'block' : 'none';
}

// ── CARD VISUAL ───────────────────────────────────────────
function fmtCard(input) {
  let v = input.value.replace(/\D/g, '').substring(0, 16);
  input.value = v.replace(/(.{4})/g, '$1 ').trim();
  document.getElementById('vis-num').textContent = v.padEnd(16, '•').replace(/(.{4})/g, '$1 ').trim();
  const ico = /^4/.test(v) ? 'fa-cc-visa' : /^5[1-5]/.test(v) ? 'fa-cc-mastercard' : /^6/.test(v) ? 'fa-indian-rupee-sign' : 'fa-credit-card';
  document.getElementById('vis-brand').innerHTML = `<i class="fa-brands ${ico}"></i>`;
}
function fmtExp(input) {
  let v = input.value.replace(/\D/g, '').substring(0, 4);
  if (v.length >= 2) v = v.slice(0, 2) + '/' + v.slice(2);
  input.value = v;
  document.getElementById('vis-exp').textContent = v || 'MM/YY';
}
function updVis() {
  document.getElementById('vis-name').textContent = (document.getElementById('c-name').value || 'YOUR NAME').toUpperCase().substring(0, 22);
}

// ── VALIDATION ────────────────────────────────────────────
function showErr(id, show) {
  const el = document.getElementById(id);
  el.style.display = show ? 'block' : 'none';
  if (el.previousElementSibling?.tagName === 'INPUT') {
    el.previousElementSibling.style.borderColor = show ? '#dc2626' : '';
  }
}
function validate() {
  let ok = true;
  if (activeMethod === 'card') {
    const num = document.getElementById('c-num').value.replace(/\s/g, '');
    const nm  = document.getElementById('c-name').value.trim();
    const exp = document.getElementById('c-exp').value;
    const cvv = document.getElementById('c-cvv').value;
    if (num.length !== 16)            { showErr('err-num',   true); ok = false; } else showErr('err-num', false);
    if (!nm)                          { showErr('err-cname', true); ok = false; } else showErr('err-cname', false);
    if (!/^\d{2}\/\d{2}$/.test(exp)) { showErr('err-exp',   true); ok = false; } else showErr('err-exp', false);
    if (cvv.length < 3)               { showErr('err-cvv',   true); ok = false; } else showErr('err-cvv', false);
  } else if (activeMethod === 'upi') {
    const u = document.getElementById('upi-input').value.trim();
    if (u && !/^[\w.\-]+@[\w]+$/.test(u)) { showErr('err-upi', true); ok = false; } else showErr('err-upi', false);
  }
  return ok;
}

// ── PAY ───────────────────────────────────────────────────
async function pay() {
  if (!validate()) return;
  setBtnLoading(true);
  if (activeMethod === 'razorpay') {
    await payWithRazorpay();
  } else if (activeMethod === 'upi') {
    const uid = document.getElementById('upi-input').value.trim();
    await sendPayment('upi', uid ? 'UPI: ' + uid : 'UPI QR/App → ' + MERCHANT);
  } else {
    const num = document.getElementById('c-num').value.replace(/\s/g, '');
    await sendPayment('card', 'Card ending ' + num.slice(-4));
  }
}

// ── RAZORPAY ──────────────────────────────────────────────
async function payWithRazorpay() {
  try {
    const res   = await fetch('api/razorpay_order.php', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ booking_id: bookingId, amount: totalAmt })
    });
    const order = await res.json();
    if (!order.success) {
      alert(order.message || 'Could not create order. Please try UPI or Card.');
      setBtnLoading(false); return;
    }
    const options = {
      key:         order.key_id,
      amount:      order.amount,
      currency:    'INR',
      name:        'LuxRide',
      description: carBrand + ' ' + carName,
      order_id:    order.order_id,
      prefill:     { name: custName, email: custEmail, contact: custPhone },
      theme:       { color: '#1e40af' },
      handler: async function(response) {
        const vRes  = await fetch('api/razorpay_verify.php', {
          method: 'POST', headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            razorpay_order_id:   response.razorpay_order_id,
            razorpay_payment_id: response.razorpay_payment_id,
            razorpay_signature:  response.razorpay_signature,
            booking_id:   bookingId, amount: totalAmt,
            car_name:     carBrand + ' ' + carName,
            cust_name:    custName, cust_email: custEmail, cust_phone: custPhone,
            payment_method: 'razorpay'
          })
        });
        const vData = await vRes.json();
        if (vData.success) {
          showSuccess(vData.txn_id, 'Razorpay (' + response.razorpay_payment_id.slice(-6) + ')');
        } else {
          alert('Verification failed: ' + vData.message);
          setBtnLoading(false);
        }
      },
      modal: { ondismiss: () => setBtnLoading(false) }
    };
    new Razorpay(options).open();
  } catch (e) {
    alert('Razorpay error. Try UPI or Card.');
    setBtnLoading(false);
  }
}

// ── FALLBACK PAYMENT ──────────────────────────────────────
async function sendPayment(method, detail) {
  try {
    const res  = await fetch('api/process_payment.php', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({
        booking_id:     bookingId,
        car_name:       carBrand + ' ' + carName,
        cust_name:      custName,
        cust_email:     custEmail,
        cust_phone:     custPhone,
        amount:         totalAmt,
        payment_method: method,
        method_detail:  detail
      })
    });
    const data = await res.json();
    if (res.ok && data.success) {
      showSuccess(data.txn_id, detail);
    } else {
      alert(data.message || 'Payment failed. Please try again.');
      setBtnLoading(false);
    }
  } catch (e) {
    alert('Network error. Please try again.');
    setBtnLoading(false);
  }
}

// ── SHOW SUCCESS ──────────────────────────────────────────
function showSuccess(txnId, method) {
  document.getElementById('main-wrap').style.display    = 'none';
  document.getElementById('success-wrap').style.display = 'flex';
  document.getElementById('ss-bid').textContent    = bookingId;
  document.getElementById('ss-car').textContent    = carBrand + ' ' + carName;
  document.getElementById('ss-amt').textContent    = '₹' + Math.round(totalAmt).toLocaleString('en-IN');
  document.getElementById('ss-method').textContent = method;
  document.getElementById('ss-txn').textContent    = txnId;

  // Send SMS
  if (custPhone) {
    const sms =
      `LuxRide Booking Confirmed!\n` +
      `Booking ID: ${bookingId}\n` +
      `Car: ${carBrand} ${carName}\n` +
      `Amount: Rs.${Math.round(totalAmt).toLocaleString('en-IN')}\n` +
      `TXN: ${txnId}\n` +
      `Thank you for choosing LuxRide!`;
    fetch('api/send_sms.php', {
      method: 'POST', headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ phone: custPhone, message: sms })
    }).then(r => r.json()).then(d => console.log('SMS:', d.message)).catch(() => {});
  }
}

function setBtnLoading(on) {
  document.getElementById('pay-btn').disabled             = on;
  document.getElementById('spin').style.display           = on ? 'block' : 'none';
  document.getElementById('pay-icon').style.display       = on ? 'none'  : 'inline';
}
</script>
</body>
</html>