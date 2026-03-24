/**
 * dev-tools.js — Development-only helper utilities
 * Adds a floating "Magic Fill" button that auto-populates form fields with test data.
 * Include this script on any page during development; remove before production.
 */

(function () {
  'use strict';

  // ─── Test Data ──────────────────────────────────────────────────────────
  var MOCK = {
    firstName:    'Isabella',
    lastName:     'Marchetti',
    email:        'isabella.marchetti@example.com',
    phone:        '+1 (555) 234-5678',
    password:     'Eudaimonia2024!',
    guests:       '4',
    occasion:     'Anniversary',
    requests:     'Window table preferred. One guest has a gluten allergy.',
    zone:         'dining-room',
    zoneLabel:    'The Dining Room',
    time:         '7:00 PM',
  };

  // ─── Fill Strategies per page ───────────────────────────────────────────

  function fillInput(id, value) {
    var el = document.getElementById(id);
    if (!el) return;
    // Set value and fire events so JS listeners pick it up
    var nativeSet = Object.getOwnPropertyDescriptor(
      window.HTMLInputElement.prototype, 'value'
    );
    if (nativeSet && nativeSet.set) {
      nativeSet.set.call(el, value);
    } else {
      el.value = value;
    }
    el.dispatchEvent(new Event('input', { bubbles: true }));
    el.dispatchEvent(new Event('change', { bubbles: true }));
  }

  function fillSelect(id, value) {
    var el = document.getElementById(id);
    if (!el) return;
    el.value = value;
    el.dispatchEvent(new Event('change', { bubbles: true }));
  }

  function fillCheckbox(id, checked) {
    var el = document.getElementById(id);
    if (!el) return;
    el.checked = checked;
    el.dispatchEvent(new Event('change', { bubbles: true }));
  }

  // Login page
  function fillLogin() {
    fillInput('login-email', MOCK.email);
    fillInput('login-password', MOCK.password);
  }

  // Register page
  function fillRegister() {
    fillInput('reg-firstname', MOCK.firstName);
    fillInput('reg-lastname', MOCK.lastName);
    fillInput('reg-email', MOCK.email);
    fillInput('reg-phone', MOCK.phone);
    fillInput('reg-password', MOCK.password);
    fillInput('reg-confirm', MOCK.password);
    fillCheckbox('reg-terms', true);
  }

  // Booking wizard
  function fillBooking() {
    // Step 1 – Guest Info
    fillInput('guest-firstname', MOCK.firstName);
    fillInput('guest-lastname', MOCK.lastName);
    fillInput('guest-email', MOCK.email);
    fillInput('guest-phone', MOCK.phone);
    fillSelect('guest-count', MOCK.guests);
    fillSelect('guest-occasion', MOCK.occasion);

    // Step 2 – Zone selection (simulate click)
    var zoneCard = document.querySelector('.zone-select-card[data-zone="' + MOCK.zone + '"]');
    if (zoneCard) zoneCard.click();

    // Step 3 – Date (tomorrow)
    var tomorrow = new Date();
    tomorrow.setDate(tomorrow.getDate() + 1);
    // Skip to a non-Monday (restaurant closed Mondays)
    if (tomorrow.getDay() === 1) tomorrow.setDate(tomorrow.getDate() + 1);
    var yyyy = tomorrow.getFullYear();
    var mm = String(tomorrow.getMonth() + 1).padStart(2, '0');
    var dd = String(tomorrow.getDate()).padStart(2, '0');
    fillInput('book-date', yyyy + '-' + mm + '-' + dd);

    // Time slot (simulate click)
    var slots = document.querySelectorAll('.time-slot:not(.unavailable)');
    // Pick the 5th available slot (~7:00 PM) or last available
    var targetSlot = null;
    slots.forEach(function (s) {
      if (s.textContent.trim() === MOCK.time) targetSlot = s;
    });
    if (!targetSlot && slots.length > 0) targetSlot = slots[Math.min(4, slots.length - 1)];
    if (targetSlot) targetSlot.click();

    // Special requests
    var reqEl = document.getElementById('guest-requests');
    if (reqEl) {
      reqEl.value = MOCK.requests;
      reqEl.dispatchEvent(new Event('input', { bubbles: true }));
    }
  }

  // ─── Detect page & run ──────────────────────────────────────────────────

  function magicFill() {
    var filled = false;

    if (document.getElementById('login-form')) {
      fillLogin();
      filled = true;
    }

    if (document.getElementById('register-form')) {
      fillRegister();
      filled = true;
    }

    if (document.getElementById('booking-wizard')) {
      fillBooking();
      filled = true;
    }

    if (!filled) {
      console.log('[Dev Tools] No known form found on this page.');
    }
  }

  // ─── Floating button UI ─────────────────────────────────────────────────

  function createButton() {
    var btn = document.createElement('button');
    btn.id = 'dev-magic-fill';
    btn.innerHTML =
      '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
        '<path d="m15 4-1 1 4 4 1-1a2.83 2.83 0 1 0-4-4Z"/>' +
        '<path d="m13 6-8.5 8.5a2.12 2.12 0 0 0 3 3L16 9"/>' +
        '<path d="m2 22 5.5-1.5L21.17 6.83a2.82 2.82 0 0 0-4-4L3.5 16.5Z"/>' +
      '</svg>' +
      ' Magic Fill';

    // Styles
    btn.style.cssText = [
      'position: fixed',
      'bottom: 1.25rem',
      'right: 1.25rem',
      'z-index: 9999',
      'display: inline-flex',
      'align-items: center',
      'gap: 0.375rem',
      'padding: 0.5rem 1rem',
      'font-size: 0.8125rem',
      'font-weight: 600',
      'font-family: system-ui, -apple-system, sans-serif',
      'color: #fff',
      'background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%)',
      'border: none',
      'border-radius: 9999px',
      'cursor: pointer',
      'box-shadow: 0 4px 14px rgba(124, 58, 237, 0.4)',
      'transition: all 0.2s ease',
      'user-select: none',
    ].join('; ');

    btn.addEventListener('mouseenter', function () {
      btn.style.transform = 'translateY(-2px) scale(1.04)';
      btn.style.boxShadow = '0 6px 20px rgba(124, 58, 237, 0.5)';
    });
    btn.addEventListener('mouseleave', function () {
      btn.style.transform = '';
      btn.style.boxShadow = '0 4px 14px rgba(124, 58, 237, 0.4)';
    });

    btn.addEventListener('click', function () {
      magicFill();
      // Flash feedback
      var orig = btn.innerHTML;
      btn.innerHTML =
        '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">' +
          '<path d="M20 6 9 17l-5-5"/>' +
        '</svg>' +
        ' Filled!';
      btn.style.background = 'linear-gradient(135deg, #059669 0%, #10b981 100%)';
      btn.style.boxShadow = '0 4px 14px rgba(5, 150, 105, 0.4)';
      setTimeout(function () {
        btn.innerHTML = orig;
        btn.style.background = 'linear-gradient(135deg, #7c3aed 0%, #a855f7 100%)';
        btn.style.boxShadow = '0 4px 14px rgba(124, 58, 237, 0.4)';
      }, 1200);
    });

    document.body.appendChild(btn);
  }

  // ─── Init ───────────────────────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', function () {
    // Only show if there's a form on the page
    if (
      document.getElementById('login-form') ||
      document.getElementById('register-form') ||
      document.getElementById('booking-wizard')
    ) {
      createButton();
    }
  });

})();
