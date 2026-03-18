/**
 * book.js — Booking Wizard Logic
 * Handles: step navigation, zone selection, date/time, summary panel, validation.
 */

(function () {
  'use strict';

  // ─── State ────────────────────────────────────────────────────────────────
  var state = {
    currentStep: 1,
    totalSteps: 4,
    // Step 1 – Guest Info
    firstName:   '',
    lastName:    '',
    email:       '',
    phone:       '',
    guests:      '',
    occasion:    '',
    requests:    '',
    // Step 2 – Zone
    zone:        '',       // 'patio' | 'dining-room' | 'bar'
    zoneLabel:   '',
    // Step 3 – Date & Time
    date:        '',
    dateLabel:   '',
    time:        '',
  };

  // ─── DOM helpers ──────────────────────────────────────────────────────────
  function $(sel) { return document.querySelector(sel); }
  function $$(sel) { return document.querySelectorAll(sel); }

  // ─── Step rendering ───────────────────────────────────────────────────────
  function goToStep(n) {
    if (n < 1 || n > state.totalSteps) return;
    state.currentStep = n;

    // Show / hide panels
    $$('.wizard-step-panel').forEach(function (p) { p.classList.remove('active'); });
    var panel = $('#step-' + n);
    if (panel) panel.classList.add('active');

    // Update progress indicator circles
    $$('.wizard-step').forEach(function (el) {
      var s = parseInt(el.dataset.step, 10);
      el.classList.remove('active', 'completed');
      if (s === n)     el.classList.add('active');
      if (s < n)       el.classList.add('completed');
    });

    // Update nav buttons
    renderNavButtons();

    // Scroll wizard body to top
    var body = $('.wizard-body');
    if (body) body.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }

  function renderNavButtons() {
    // Update ALL instances (each step panel has its own set)
    $$('#btn-prev, [data-action="prev"]').forEach(function (btn) {
      btn.disabled = state.currentStep === 1;
    });
    $$('#btn-next, [data-action="next"]').forEach(function (btn) {
      // Preserve the arrow icon inside the button
      var label = state.currentStep === state.totalSteps ? 'Confirm Reservation' : 'Continue';
      var svg = btn.querySelector('svg');
      btn.textContent = label;
      if (svg) btn.appendChild(svg);
    });
    $$('#step-count, [data-role="step-count"]').forEach(function (el) {
      el.textContent = 'Step ' + state.currentStep + ' of ' + state.totalSteps;
    });
  }

  // ─── Validation per step ─────────────────────────────────────────────────
  function validateStep(n) {
    if (n === 1) {
      var fn = $('#guest-firstname');
      var ln = $('#guest-lastname');
      var em = $('#guest-email');
      var gs = $('#guest-count');
      if (!fn || !fn.value.trim()) { showStepError('Please enter your first name.', fn); return false; }
      if (!ln || !ln.value.trim()) { showStepError('Please enter your last name.', ln); return false; }
      var emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!em || !emailRe.test(em.value.trim())) { showStepError('Please enter a valid email address.', em); return false; }
      if (!gs || !gs.value) { showStepError('Please select the number of guests.', gs); return false; }
      // Persist into state
      state.firstName = fn.value.trim();
      state.lastName  = ln.value.trim();
      state.email     = em.value.trim();
      state.phone     = ($('#guest-phone') || {}).value || '';
      state.guests    = gs.value;
      state.occasion  = ($('#guest-occasion') || {}).value || '';
      state.requests  = ($('#guest-requests') || {}).value || '';
    }
    if (n === 2) {
      if (!state.zone) { showStepError('Please select a dining zone to continue.', null); return false; }
    }
    if (n === 3) {
      var dateEl = $('#book-date');
      if (!dateEl || !dateEl.value) { showStepError('Please choose a reservation date.', dateEl); return false; }
      if (!state.time) { showStepError('Please select a time slot.', null); return false; }
      state.date = dateEl.value;
      state.dateLabel = formatDate(dateEl.value);
    }
    clearStepError();
    return true;
  }

  function showStepError(msg, focusEl) {
    clearStepError();
    var panel = $('.wizard-step-panel.active');
    if (!panel) return;
    var err = document.createElement('div');
    err.className = 'auth-alert wizard-error';
    err.id = 'wizard-error';
    err.innerHTML =
      '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>' +
      '<span>' + msg + '</span>';
    var nav = panel.querySelector('.wizard-nav');
    if (nav) panel.insertBefore(err, nav);
    else panel.appendChild(err);
    if (focusEl) focusEl.focus();
  }

  function clearStepError() {
    var err = $('#wizard-error');
    if (err) err.remove();
  }

  // ─── Summary sidebar update ───────────────────────────────────────────────
  function updateSummary() {
    setVal('#sum-name',   state.firstName && state.lastName ? state.firstName + ' ' + state.lastName : null);
    setVal('#sum-email',  state.email || null);
    setVal('#sum-guests', state.guests ? state.guests + (state.guests === '1' ? ' guest' : ' guests') : null);
    setVal('#sum-zone',   state.zoneLabel || null);
    setVal('#sum-date',   state.dateLabel || null);
    setVal('#sum-time',   state.time || null);
    setVal('#sum-occasion', state.occasion || null);
  }

  function setVal(sel, val) {
    var el = $(sel);
    if (!el) return;
    if (!val) {
      el.textContent = '—';
      el.classList.add('empty');
    } else {
      el.textContent = val;
      el.classList.remove('empty');
    }
  }

  // ─── Zone selection ───────────────────────────────────────────────────────
  function initZoneCards() {
    $$('.zone-select-card').forEach(function (card) {
      card.addEventListener('click', function () {
        $$('.zone-select-card').forEach(function (c) { c.classList.remove('selected'); });
        card.classList.add('selected');
        state.zone      = card.dataset.zone;
        state.zoneLabel = card.dataset.label;
        updateSummary();
      });
    });
  }

  // ─── Time slot selection ──────────────────────────────────────────────────
  function initTimeSlots() {
    $$('.time-slot').forEach(function (slot) {
      if (slot.classList.contains('unavailable')) return;
      slot.addEventListener('click', function () {
        $$('.time-slot').forEach(function (s) { s.classList.remove('selected'); });
        slot.classList.add('selected');
        state.time = slot.textContent.trim();
        updateSummary();
      });
    });
  }

  // ─── Date input ───────────────────────────────────────────────────────────
  function initDateInput() {
    var dateEl = $('#book-date');
    if (!dateEl) return;

    // Set min date to today
    var today = new Date();
    var yyyy  = today.getFullYear();
    var mm    = String(today.getMonth() + 1).padStart(2, '0');
    var dd    = String(today.getDate()).padStart(2, '0');
    dateEl.min = yyyy + '-' + mm + '-' + dd;

    dateEl.addEventListener('change', function () {
      state.date      = dateEl.value;
      state.dateLabel = formatDate(dateEl.value);
      updateSummary();
    });
  }

  function formatDate(iso) {
    if (!iso) return '';
    var parts = iso.split('-');
    var d = new Date(parseInt(parts[0]), parseInt(parts[1]) - 1, parseInt(parts[2]));
    return d.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
  }

  // ─── Live summary from Step 1 fields ─────────────────────────────────────
  function initGuestFieldListeners() {
    ['guest-firstname', 'guest-lastname', 'guest-email', 'guest-phone', 'guest-count', 'guest-occasion'].forEach(function (id) {
      var el = $('#' + id);
      if (!el) return;
      el.addEventListener('input', function () {
        state.firstName = ($('#guest-firstname') || {}).value || '';
        state.lastName  = ($('#guest-lastname')  || {}).value || '';
        state.email     = ($('#guest-email')     || {}).value || '';
        state.phone     = ($('#guest-phone')     || {}).value || '';
        state.guests    = ($('#guest-count')     || {}).value || '';
        state.occasion  = ($('#guest-occasion')  || {}).value || '';
        updateSummary();
      });
    });
  }

  // ─── Review step: populate details ────────────────────────────────────────
  function populateReview() {
    var fields = {
      '#rev-name':     state.firstName + ' ' + state.lastName,
      '#rev-email':    state.email,
      '#rev-phone':    state.phone || '—',
      '#rev-guests':   state.guests + (state.guests === '1' ? ' guest' : ' guests'),
      '#rev-zone':     state.zoneLabel,
      '#rev-date':     state.dateLabel,
      '#rev-time':     state.time,
      '#rev-occasion': state.occasion || '—',
      '#rev-requests': state.requests || '—',
    };
    for (var sel in fields) {
      var el = $(sel);
      if (el) el.textContent = fields[sel];
    }
  }

  // ─── Navigation buttons (event delegation — handles all step panels) ─────
  function initNavButtons() {
    var wizard = $('#booking-wizard');
    if (!wizard) return;

    wizard.addEventListener('click', function (e) {
      var prevBtn = e.target.closest('#btn-prev');
      var nextBtn = e.target.closest('#btn-next');

      if (prevBtn) {
        clearStepError();
        goToStep(state.currentStep - 1);
        updateSummary();
        return;
      }

      if (nextBtn) {
        if (!validateStep(state.currentStep)) return;

        if (state.currentStep === state.totalSteps) {
          // Final submission — navigate to confirmation page
          var params = new URLSearchParams({
            name:    state.firstName + ' ' + state.lastName,
            email:   state.email,
            guests:  state.guests,
            zone:    state.zoneLabel,
            date:    state.dateLabel,
            time:    state.time,
            occasion: state.occasion,
            ref:     generateRef(),
          });
          window.location.href = 'book-confirmation.php?' + params.toString();
        } else {
          // Save special requests from step 3 before moving to review
          var reqEl = $('#guest-requests');
          if (reqEl) state.requests = reqEl.value || '';

          if (state.currentStep === state.totalSteps - 1) {
            populateReview();
          }
          goToStep(state.currentStep + 1);
          updateSummary();
        }
        return;
      }
    });
  }

  // ─── Confirmation reference ────────────────────────────────────────────────
  function generateRef() {
    var chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    var ref   = 'EUD-';
    for (var i = 0; i < 8; i++) ref += chars[Math.floor(Math.random() * chars.length)];
    return ref;
  }

  // ─── Confirmation page: read URL params ──────────────────────────────────
  function initConfirmPage() {
    var params = new URLSearchParams(window.location.search);
    var map = {
      '#conf-name':    'name',
      '#conf-guests':  'guests',
      '#conf-zone':    'zone',
      '#conf-date':    'date',
      '#conf-time':    'time',
      '#conf-email':   'email',
      '#conf-occasion':'occasion',
      '#conf-ref':     'ref',
    };
    for (var sel in map) {
      var el = $(sel);
      var val = params.get(map[sel]);
      if (el && val) el.textContent = val;
    }
  }

  // ─── Init ─────────────────────────────────────────────────────────────────
  document.addEventListener('DOMContentLoaded', function () {
    // Booking wizard page
    if ($('#booking-wizard')) {
      goToStep(1);
      initZoneCards();
      initTimeSlots();
      initDateInput();
      initGuestFieldListeners();
      initNavButtons();
      updateSummary();
    }

    // Confirmation page
    if ($('#confirmation-page')) {
      initConfirmPage();
    }
  });

})();
