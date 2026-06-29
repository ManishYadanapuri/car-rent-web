/**
 * LuxRide — Booking Form Validation
 * Drop-in file: add <script src="booking-validation.js"></script>
 * just before the closing </body> tag in cars.html
 *
 * What this covers:
 *  - Full Name      → letters & spaces only, 3–50 chars
 *  - Phone          → 10-digit Indian mobile (starts with 6–9)
 *  - Email          → standard RFC-5322 regex
 *  - Pickup Location→ min 3 chars
 *  - Pickup Date    → must be at least 1 hour in the future
 *  - Return Date    → must be after pickup date
 *  - DL Number      → Indian format: XX00 0000 0000000 (flexible)
 *  - Aadhaar upload → image only, max 5 MB
 *  - License upload → image only, max 5 MB
 *  - T&C checkbox   → must be checked
 */

(function () {
  "use strict";

  /* ─────────────────────────────────────────────
     1. STYLE INJECTION
     Injects CSS for error states, success states,
     and inline error message spans.
  ───────────────────────────────────────────── */
  const style = document.createElement("style");
  style.textContent = `
    /* Field states */
    .lux-field-wrap { position: relative; }

    .lux-input-invalid {
      border-color: #dc2626 !important;
      background: #fff5f5 !important;
      box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.10) !important;
    }
    .lux-input-valid {
      border-color: #16a34a !important;
      background: #f0fdf4 !important;
      box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.10) !important;
    }

    /* Inline error message */
    .lux-err {
      display: none;
      font-size: 0.72rem;
      color: #dc2626;
      margin-top: 4px;
      font-weight: 600;
      align-items: center;
      gap: 4px;
      animation: lux-err-in 0.18s ease;
    }
    .lux-err.show { display: flex; }
    @keyframes lux-err-in {
      from { opacity: 0; transform: translateY(-4px); }
      to   { opacity: 1; transform: translateY(0);    }
    }

    /* Inline success tick */
    .lux-tick {
      position: absolute;
      right: 10px;
      top: 50%;
      transform: translateY(-50%);
      color: #16a34a;
      font-size: 0.85rem;
      display: none;
      pointer-events: none;
    }
    .lux-tick.show { display: block; }

    /* File upload states */
    .lux-file-invalid .file-upload-box {
      border-color: #dc2626 !important;
      background: #fff5f5 !important;
    }
    .lux-file-valid .file-upload-box {
      border-color: #16a34a !important;
      background: #f0fdf4 !important;
    }
    .lux-file-err {
      font-size: 0.72rem;
      color: #dc2626;
      font-weight: 600;
      margin-top: 4px;
      display: none;
    }
    .lux-file-err.show { display: block; }

    /* T&C error */
    .lux-tnc-err {
      font-size: 0.72rem;
      color: #dc2626;
      font-weight: 600;
      margin-left: 4px;
      display: none;
    }
    .lux-tnc-err.show { display: inline; }

    /* Submit error banner (if multiple fields fail) */
    .lux-banner {
      display: none;
      background: #fef2f2;
      border: 1px solid #fecaca;
      border-radius: 8px;
      padding: 0.65rem 1rem;
      font-size: 0.82rem;
      color: #dc2626;
      font-weight: 600;
      margin-bottom: 0.75rem;
      align-items: center;
      gap: 8px;
    }
    .lux-banner.show { display: flex; }
  `;
  document.head.appendChild(style);

  /* ─────────────────────────────────────────────
     2. VALIDATORS  (pure functions → true/false)
  ───────────────────────────────────────────── */
  const RULES = {
    name(v) {
      if (!v) return "Full name is required.";
      if (v.length < 3) return "Name must be at least 3 characters.";
      if (v.length > 50) return "Name cannot exceed 50 characters.";
      if (!/^[a-zA-Z\s'.'-]+$/.test(v)) return "Name can only contain letters and spaces.";
      return "";
    },

    phone(v) {
      if (!v) return "Phone number is required.";
      const digits = v.replace(/\D/g, "");
      if (digits.length !== 10) return "Enter a valid 10-digit mobile number.";
      if (!/^[6-9]/.test(digits)) return "Indian mobile numbers start with 6, 7, 8, or 9.";
      return "";
    },

    email(v) {
      if (!v) return "Email address is required.";
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v)) return "Enter a valid email (e.g. name@example.com).";
      return "";
    },

    pickup(v) {
      if (!v) return "Pickup location is required.";
      if (v.length < 3) return "Enter a valid city or area name (min 3 chars).";
      return "";
    },

    pickupDatetime(v) {
      if (!v) return "Pickup date & time is required.";
      const selected = new Date(v);
      const now = new Date();
      const oneHourFromNow = new Date(now.getTime() + 60 * 60 * 1000);
      if (selected < oneHourFromNow) return "Pickup must be at least 1 hour from now.";
      return "";
    },

    returnDatetime(v, pickupVal) {
      if (!v) return "Return date & time is required.";
      if (!pickupVal) return "Please set a pickup date first.";
      const ret = new Date(v);
      const pickup = new Date(pickupVal);
      if (ret <= pickup) return "Return date must be after the pickup date & time.";
      return "";
    },

    dl(v) {
      if (!v) return "Driver's License number is required.";
      // Indian DL: 2-letter state code + 2-digit RTO + 4-digit year + 7-digit number
      // Accepted formats: MH0219991234567 | MH-02-1999-1234567 | MH02 1999 1234567
      const cleaned = v.replace(/[-\s]/g, "").toUpperCase();
      if (!/^[A-Z]{2}\d{13}$/.test(cleaned)) {
        return "Enter valid DL number (e.g. MH0219991234567 or MH-02-1999-1234567).";
      }
      return "";
    },

    file(file, label) {
      if (!file) return `${label} is required.`;
      const allowed = ["image/jpeg", "image/png", "image/webp", "application/pdf"];
      if (!allowed.includes(file.type)) return `Only JPG, PNG, WEBP, or PDF files allowed.`;
      if (file.size > 5 * 1024 * 1024) return `File must be under 5 MB.`;
      return "";
    },
  };

  /* ─────────────────────────────────────────────
     3. DOM HELPERS
  ───────────────────────────────────────────── */
  function getEl(id) { return document.getElementById(id); }

  /**
   * Injects an <span class="lux-err"> after an input if one doesn't exist.
   * Returns the span element.
   */
  function ensureErrSpan(inputEl, id) {
    let span = document.getElementById(id);
    if (!span) {
      span = document.createElement("span");
      span.className = "lux-err";
      span.id = id;
      span.innerHTML = '<i class="fa-solid fa-circle-exclamation"></i> <span class="lux-msg"></span>';
      inputEl.parentNode.insertBefore(span, inputEl.nextSibling);
    }
    return span;
  }

  /**
   * Shows or hides an error on a text/select/date input.
   * @param {HTMLElement} input
   * @param {string}      errMsg  — empty string = valid
   * @param {string}      spanId  — unique id for the error span
   */
  function setFieldState(input, errMsg, spanId) {
    const span = ensureErrSpan(input, spanId);
    if (errMsg) {
      input.classList.add("lux-input-invalid");
      input.classList.remove("lux-input-valid");
      span.querySelector(".lux-msg").textContent = errMsg;
      span.classList.add("show");
    } else {
      input.classList.remove("lux-input-invalid");
      input.classList.add("lux-input-valid");
      span.classList.remove("show");
    }
  }

  /** Returns true when the field is valid */
  function validateField(input, errMsg, spanId) {
    setFieldState(input, errMsg, spanId);
    return errMsg === "";
  }

  /* ─────────────────────────────────────────────
     4. PER-FIELD VALIDATION WRAPPERS
  ───────────────────────────────────────────── */
  function validateName() {
    const el = getEl("bf-name");
    if (!el) return true;
    return validateField(el, RULES.name(el.value.trim()), "lux-err-name");
  }

  function validatePhone() {
    const el = getEl("bf-phone");
    if (!el) return true;
    return validateField(el, RULES.phone(el.value.trim()), "lux-err-phone");
  }

  function validateEmail() {
    const el = getEl("bf-email");
    if (!el) return true;
    return validateField(el, RULES.email(el.value.trim()), "lux-err-email");
  }

  function validatePickup() {
    const el = getEl("bf-pickup");
    if (!el) return true;
    return validateField(el, RULES.pickup(el.value.trim()), "lux-err-pickup");
  }

  function validatePickupDatetime() {
    const el = getEl("bf-pickup-datetime");
    if (!el) return true;
    return validateField(el, RULES.pickupDatetime(el.value), "lux-err-pdt");
  }

  function validateReturnDatetime() {
    const retEl = getEl("bf-return-datetime");
    const pickupEl = getEl("bf-pickup-datetime");
    if (!retEl) return true;
    return validateField(
      retEl,
      RULES.returnDatetime(retEl.value, pickupEl ? pickupEl.value : ""),
      "lux-err-rdt"
    );
  }

  function validateDL() {
    const el = getEl("bf-dl");
    if (!el) return true;
    return validateField(el, RULES.dl(el.value.trim()), "lux-err-dl");
  }

  function validateAadhar() {
    const input = getEl("aadhar-card");
    const wrap = input ? input.closest(".file-upload-box") : null;
    // Error span lives after the parent file-upload-group
    let errSpan = getEl("lux-err-aadhar");
    if (!errSpan && wrap) {
      errSpan = document.createElement("div");
      errSpan.id = "lux-err-aadhar";
      errSpan.className = "lux-file-err";
      wrap.parentNode.parentNode.insertBefore(errSpan, wrap.parentNode.nextSibling);
    }
    const file = input && input.files[0];
    const msg = RULES.file(file, "Aadhaar Card");
    if (errSpan) {
      errSpan.textContent = msg;
      errSpan.classList.toggle("show", !!msg);
    }
    if (wrap) wrap.classList.toggle("lux-file-invalid", !!msg);
    if (wrap) wrap.classList.toggle("lux-file-valid", !msg);
    return !msg;
  }

  function validateLicense() {
    const input = getEl("driving-license");
    const wrap = input ? input.closest(".file-upload-box") : null;
    let errSpan = getEl("lux-err-license");
    if (!errSpan && wrap) {
      errSpan = document.createElement("div");
      errSpan.id = "lux-err-license";
      errSpan.className = "lux-file-err";
      wrap.parentNode.parentNode.appendChild(errSpan);
    }
    const file = input && input.files[0];
    const msg = RULES.file(file, "Driving License");
    if (errSpan) {
      errSpan.textContent = msg;
      errSpan.classList.toggle("show", !!msg);
    }
    if (wrap) wrap.classList.toggle("lux-file-invalid", !!msg);
    if (wrap) wrap.classList.toggle("lux-file-valid", !msg);
    return !msg;
  }

  function validateTnC() {
    const cb = getEl("bf-tnc");
    if (!cb) return true;
    let errSpan = getEl("lux-err-tnc");
    if (!errSpan) {
      errSpan = document.createElement("span");
      errSpan.id = "lux-err-tnc";
      errSpan.className = "lux-tnc-err";
      errSpan.textContent = "You must accept the Terms & Conditions.";
      cb.parentNode.appendChild(errSpan);
    }
    errSpan.classList.toggle("show", !cb.checked);
    return cb.checked;
  }

  /* ─────────────────────────────────────────────
     5. FULL FORM VALIDATION  (runs on submit)
  ───────────────────────────────────────────── */
  function validateAll() {
    const results = [
      validateName(),
      validatePhone(),
      validateEmail(),
      validatePickup(),
      validatePickupDatetime(),
      validateReturnDatetime(),
      validateDL(),
      validateAadhar(),
      validateLicense(),
      validateTnC(),
    ];

    const allValid = results.every(Boolean);

    // Show/hide the top banner
    let banner = getEl("lux-banner");
    if (!banner) {
      banner = document.createElement("div");
      banner.id = "lux-banner";
      banner.className = "lux-banner";
      banner.innerHTML =
        '<i class="fa-solid fa-triangle-exclamation"></i> Please fix the errors below before confirming your booking.';
      const form = getEl("booking-form");
      if (form) form.parentNode.insertBefore(banner, form);
    }
    banner.classList.toggle("show", !allValid);

    if (!allValid) {
      // Scroll to the first invalid field
      const firstInvalid = document.querySelector(
        ".lux-input-invalid, .lux-file-invalid"
      );
      if (firstInvalid) {
        firstInvalid.scrollIntoView({ behavior: "smooth", block: "center" });
        firstInvalid.focus();
      }
    }

    return allValid;
  }

  /* ─────────────────────────────────────────────
     6. REAL-TIME LISTENERS  (blur + input)
  ───────────────────────────────────────────── */
  function attachListeners() {
    // Text / email / tel / datetime fields
    const fieldMap = [
      { id: "bf-name",            fn: validateName },
      { id: "bf-phone",           fn: validatePhone },
      { id: "bf-email",           fn: validateEmail },
      { id: "bf-pickup",          fn: validatePickup },
      { id: "bf-pickup-datetime", fn: () => { validatePickupDatetime(); validateReturnDatetime(); } },
      { id: "bf-return-datetime", fn: validateReturnDatetime },
      { id: "bf-dl",              fn: validateDL },
    ];

    fieldMap.forEach(({ id, fn }) => {
      const el = getEl(id);
      if (!el) return;
      el.addEventListener("blur",  fn);   // validate on leave
      el.addEventListener("input", fn);   // clear error while typing
    });

    // File inputs
    const aadhar = getEl("aadhar-card");
    if (aadhar) aadhar.addEventListener("change", validateAadhar);

    const license = getEl("driving-license");
    if (license) license.addEventListener("change", validateLicense);

    // T&C checkbox
    const tnc = getEl("bf-tnc");
    if (tnc) tnc.addEventListener("change", validateTnC);
  }

  /* ─────────────────────────────────────────────
     7. INTERCEPT completeBooking()
     Wraps the existing function to run validation
     before proceeding. No original logic is lost.
  ───────────────────────────────────────────── */
  function interceptCompleteBooking() {
    // Wait until the original function exists (it's defined inline in cars.html)
    if (typeof window.completeBooking !== "function") {
      setTimeout(interceptCompleteBooking, 200);
      return;
    }

    const original = window.completeBooking.bind(window);

    window.completeBooking = function (e) {
      if (e && typeof e.preventDefault === "function") e.preventDefault();

      if (!validateAll()) return; // ← stop here if invalid

      // All good — call the original booking logic
      original(e);
    };
  }

  /* ─────────────────────────────────────────────
     8. INIT
  ───────────────────────────────────────────── */
  function init() {
    attachListeners();
    interceptCompleteBooking();

    // Also hook into the form's submit event (belt-and-suspenders)
    const form = getEl("booking-form");
    if (form) {
      form.addEventListener("submit", function (e) {
        if (!validateAll()) e.preventDefault();
      });
    }
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();