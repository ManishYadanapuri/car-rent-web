// ═══════════════════════════════════════════════════════════
// LuxRide Premium Features — features.js
// Add <script src="features.js"></script> before </body> in cars.html
// ═══════════════════════════════════════════════════════════

// ── DARK / LIGHT MODE ────────────────────────────────────
(function initTheme() {
  const saved = localStorage.getItem('luxride_theme') || 'light';
  document.documentElement.setAttribute('data-theme', saved);
})();

function toggleTheme() {
  const cur  = document.documentElement.getAttribute('data-theme');
  const next = cur === 'dark' ? 'light' : 'dark';
  document.documentElement.setAttribute('data-theme', next);
  localStorage.setItem('luxride_theme', next);
  const icon = document.getElementById('theme-toggle-icon');
  if (icon) icon.className = next === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
}

// ── WISHLIST ─────────────────────────────────────────────
let wishlistIds = new Set();

async function loadWishlist() {
  try {
    const res  = await fetch('api/wishlist.php?action=list');
    if (!res.ok) return; // not logged in
    const data = await res.json();
    wishlistIds = new Set(data.car_ids.map(String));
    updateAllHearts();
  } catch (e) {}
}

async function toggleWishlist(carId, btn) {
  try {
    const res  = await fetch('api/wishlist.php?action=toggle', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ car_id: carId })
    });
    if (res.status === 401) {
      showFeatureToast('Please login to save cars to your wishlist', 'info');
      window.location.href = 'signinUP.html';
      return;
    }
    const data = await res.json();
    if (data.wishlisted) {
      wishlistIds.add(String(carId));
      btn.classList.add('wishlisted');
      btn.title = 'Remove from wishlist';
      showFeatureToast('Added to wishlist ❤️', 'success');
    } else {
      wishlistIds.delete(String(carId));
      btn.classList.remove('wishlisted');
      btn.title = 'Save to wishlist';
      showFeatureToast('Removed from wishlist', 'info');
    }
  } catch (e) {
    showFeatureToast('Please login to use wishlist', 'error');
  }
}

function updateAllHearts() {
  document.querySelectorAll('.wishlist-btn').forEach(btn => {
    const carId = String(btn.dataset.carId);
    if (wishlistIds.has(carId)) {
      btn.classList.add('wishlisted');
      btn.title = 'Remove from wishlist';
    } else {
      btn.classList.remove('wishlisted');
      btn.title = 'Save to wishlist';
    }
  });
}

// ── COMPARE CARS ─────────────────────────────────────────
let compareList = []; // max 3

function toggleCompare(car) {
  const idx = compareList.findIndex(c => c.id === car.id);
  if (idx >= 0) {
    compareList.splice(idx, 1);
  } else {
    if (compareList.length >= 3) {
      showFeatureToast('You can compare up to 3 cars at a time', 'info');
      return;
    }
    compareList.push(car);
  }
  updateCompareBar();
}

function updateCompareBar() {
  let bar = document.getElementById('compare-bar');
  if (!bar) {
    bar = document.createElement('div');
    bar.id = 'compare-bar';
    bar.className = 'compare-bar';
    document.body.appendChild(bar);
  }
  if (compareList.length === 0) {
    bar.style.transform = 'translateY(100%)';
    return;
  }
  bar.style.transform = 'translateY(0)';
  bar.innerHTML = `
    <div class="compare-bar-inner">
      <div class="compare-bar-cars">
        ${compareList.map(c => `
          <div class="compare-chip">
            <img src="${c.image}" onerror="this.src='https://via.placeholder.com/50x35'">
            <span>${c.brand} ${c.name}</span>
            <button onclick="toggleCompare({id:${c.id}})" class="compare-remove">&times;</button>
          </div>`).join('')}
        ${compareList.length < 3 ? `<div class="compare-empty-slot">+ Add car</div>` : ''}
      </div>
      <div class="compare-bar-actions">
        <span style="font-size:.85rem;color:#64748b">${compareList.length}/3 cars selected</span>
        <button class="compare-btn-go" onclick="openCompareModal()">Compare Now</button>
        <button class="compare-btn-clear" onclick="clearCompare()">Clear</button>
      </div>
    </div>`;
}

function clearCompare() {
  compareList = [];
  updateCompareBar();
  document.querySelectorAll('.compare-check').forEach(b => b.classList.remove('comparing'));
}

function openCompareModal() {
  if (compareList.length < 2) {
    showFeatureToast('Select at least 2 cars to compare', 'info');
    return;
  }
  const fields = ['brand','type','price','seats','transmission','fuel','rating'];
  const labels = { brand:'Brand', type:'Category', price:'Price/Day (₹)', seats:'Seats', transmission:'Transmission', fuel:'Fuel', rating:'Rating' };

  const cols = compareList.map(c => `
    <div class="cmp-col">
      <img src="${c.image}" onerror="this.src='https://via.placeholder.com/160x110'">
      <div class="cmp-car-name">${c.brand} ${c.name}</div>
      ${fields.map(f => `<div class="cmp-cell">${f==='price'?'₹'+Number(c[f]).toLocaleString('en-IN'):c[f]||'—'}</div>`).join('')}
      <button class="compare-btn-go" style="margin-top:1rem;width:100%" onclick="openBookingModal(${c.id})">Book Now</button>
    </div>`).join('');

  const rows = fields.map(f => `<div class="cmp-label">${labels[f]}</div>`).join('');

  const modal = document.createElement('div');
  modal.className = 'compare-modal-overlay';
  modal.innerHTML = `
    <div class="compare-modal">
      <button class="compare-modal-close" onclick="this.closest('.compare-modal-overlay').remove()">&times;</button>
      <h2 style="margin-bottom:1.5rem;font-size:1.2rem;font-weight:700">Car Comparison</h2>
      <div class="compare-grid">
        <div class="cmp-labels-col">
          <div class="cmp-img-placeholder"></div>
          <div class="cmp-car-name">&nbsp;</div>
          ${rows}
        </div>
        ${cols}
      </div>
    </div>`;
  document.body.appendChild(modal);
  modal.addEventListener('click', e => { if (e.target === modal) modal.remove(); });
}

// ── RECENTLY VIEWED ───────────────────────────────────────
const MAX_RECENT = 6;

function trackRecentlyViewed(car) {
  let recent = JSON.parse(localStorage.getItem('luxride_recent') || '[]');
  recent = recent.filter(c => c.id !== car.id);
  recent.unshift(car);
  if (recent.length > MAX_RECENT) recent = recent.slice(0, MAX_RECENT);
  localStorage.setItem('luxride_recent', JSON.stringify(recent));
}

function renderRecentlyViewed() {
  const recent = JSON.parse(localStorage.getItem('luxride_recent') || '[]');
  const section = document.getElementById('recently-viewed-section');
  if (!section || recent.length === 0) return;
  section.style.display = 'block';
  const grid = document.getElementById('recently-viewed-grid');
  if (!grid) return;
  grid.innerHTML = recent.map(c => `
    <div class="recent-card" onclick="openBookingModal(${c.id})">
      <img src="${c.image}" onerror="this.src='https://via.placeholder.com/120x80'">
      <div class="recent-info">
        <div class="recent-name">${c.brand} ${c.name}</div>
        <div class="recent-price">₹${Number(c.price).toLocaleString('en-IN')}/day</div>
      </div>
    </div>`).join('');
}

// ── TOAST ─────────────────────────────────────────────────
function showFeatureToast(msg, type = 'success') {
  let toast = document.getElementById('feature-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'feature-toast';
    document.body.appendChild(toast);
  }
  toast.textContent = msg;
  toast.className = `feature-toast feature-toast-${type} show`;
  clearTimeout(toast._timer);
  toast._timer = setTimeout(() => toast.classList.remove('show'), 3200);
}

// ── URGENCY TAGS ─────────────────────────────────────────
// Called from car card render — adds urgency/badge info
function getUrgencyTag(car) {
  if (car.rating >= 5.0 && car.price >= 500) return { text: 'Only 1 left!', cls: 'urgency-hot' };
  if (car.price <= 100)                       return { text: 'Best Deal!',   cls: 'urgency-deal' };
  if (car.badge === 'new')                    return { text: 'Just Listed',  cls: 'urgency-new' };
  if (car.rating >= 4.9)                      return { text: 'Top Rated',    cls: 'urgency-top' };
  return null;
}

// ── INIT ──────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  loadWishlist();
  renderRecentlyViewed();

  // Add theme toggle button to nav if not present
  const nav = document.querySelector('.top-bar-right') || document.querySelector('.main-nav');
  if (nav && !document.getElementById('theme-toggle-btn')) {
    const btn = document.createElement('button');
    btn.id = 'theme-toggle-btn';
    btn.onclick = toggleTheme;
    btn.innerHTML = `<i class="fa-solid fa-moon" id="theme-toggle-icon"></i>`;
    btn.style.cssText = 'background:none;border:none;cursor:pointer;font-size:1rem;color:inherit;padding:.3rem .5rem;';
    nav.appendChild(btn);
    // apply icon state
    const savedTheme = localStorage.getItem('luxride_theme') || 'light';
    if (savedTheme === 'dark') document.getElementById('theme-toggle-icon').className = 'fa-solid fa-sun';
  }
});
