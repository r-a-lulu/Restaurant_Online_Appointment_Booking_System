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
    serviceId:   '',
    packageId:   '',
    // Step 2 – Zone & Spot
    zone:        '',       // 'patio' | 'dining-room' | 'bar'
    zoneLabel:   '',
    zoneId:      '',
    spot:        '',       // e.g. 'Garden Terrace'
    tableId:     '',
    // Step 3 – Date & Time
    date:        '',
    dateLabel:   '',
    time:        '',
    timeValue:   '',
    timeLabel:   '',
  };

  // ─── Seating spots per zone ───────────────────────────────────────────────
  // Names match the zone detail pages (patio.php, dining-room.php, bar.php)
  var SEATING_SPOTS = {
    'patio':       { title: 'in the Patio',       spots: ['Garden View', 'Fountain Side', 'Pergola', 'Corner Alcove', 'Olive Grove'] },
    'dining-room': { title: 'in the Dining Room', spots: ['Chef\'s View', 'Window Table', 'Banquette', 'Fireplace', 'Private Alcove', 'Chandelier'] },
    'bar':         { title: 'at the Bar',          spots: ['Bar Counter', 'Lounge Booths', 'High Tops', 'Corner Sofa'] },
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

      var serviceSel = $('#service-select');
      var packageSel = $('#package-select');
      var serviceVal = serviceSel ? serviceSel.value : '';
      var packageVal = packageSel ? packageSel.value : '';
      if (!serviceVal && !packageVal) { showStepError('Please select a service or a package.', serviceSel || packageSel); return false; }
      if (serviceVal && packageVal) { showStepError('Please choose either a service or a package, not both.', serviceSel || packageSel); return false; }
      state.serviceId = serviceVal;
      state.packageId = packageVal;
    }
    if (n === 2) {
      if (!state.zone || !state.zoneId) { showStepError('Please select a dining zone to continue.', null); return false; }
      if (!state.spot) { showStepError('Please select a seating preference to continue.', null); return false; }
      if (!state.tableId) { showStepError('Please select a table to continue.', null); return false; }
    }
    if (n === 3) {
      var dateEl = $('#book-date');
      if (!dateEl || !dateEl.value) { showStepError('Please choose a reservation date.', dateEl); return false; }
      if (!state.timeValue) { showStepError('Please select a time slot.', null); return false; }
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
    setVal('#sum-spot',   state.spot || null);
    setVal('#sum-date',   state.dateLabel || null);
    setVal('#sum-time',   state.timeLabel || null);
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
        state.zoneId    = card.dataset.zoneId;
        state.spot      = '';   // reset spot when zone changes
        state.tableId   = '';
        resetTableSelection(state.zoneId);
        revealSpotPills(state.zone);
        updateSummary();
        fetchAvailability();
      });
    });
  }

  function revealSpotPills(zone) {
    var container = $('#seating-reveal');
    var pillsEl   = $('#seating-spot-pills');
    var titleEl   = $('#seating-reveal-title');
    if (!container || !pillsEl) return;

    var data = SEATING_SPOTS[zone];
    if (!data) { container.classList.remove('visible'); return; }

    // Build pills
    pillsEl.innerHTML = '';
    if (titleEl) titleEl.textContent = 'Select your spot ' + data.title;
    data.spots.forEach(function (spot) {
      var pill = document.createElement('button');
      pill.type = 'button';
      pill.className = 'seating-spot-pill';
      pill.textContent = spot;
      pill.addEventListener('click', function () {
        pillsEl.querySelectorAll('.seating-spot-pill').forEach(function (p) { p.classList.remove('selected'); });
        pill.classList.add('selected');
        state.spot = spot;
        updateSummary();
      });
      pillsEl.appendChild(pill);
    });

    container.classList.add('visible');
  }

  // ─── Time slot selection ──────────────────────────────────────────────────
  function initTimeSlots() {
    $$('.time-slot').forEach(function (slot) {
      if (slot.classList.contains('unavailable')) return;
      slot.addEventListener('click', function () {
        $$('.time-slot').forEach(function (s) { s.classList.remove('selected'); });
        slot.classList.add('selected');
        state.timeLabel = slot.textContent.trim();
        state.timeValue = slot.dataset.time || '';
        updateSummary();
      });
    });
  }

  // ─── Date input ───────────────────────────────────────────────────────────
  function initDateInput() {
    var dateEl = $('#book-date');
    if (!dateEl) return;

    // Set min date to tomorrow (24h lead time)
    var minDate = new Date();
    minDate.setDate(minDate.getDate() + 1);
    var yyyy  = minDate.getFullYear();
    var mm    = String(minDate.getMonth() + 1).padStart(2, '0');
    var dd    = String(minDate.getDate()).padStart(2, '0');
    dateEl.min = yyyy + '-' + mm + '-' + dd;

    dateEl.addEventListener('change', function () {
      if (!dateEl.value) return;
      var selected = new Date(dateEl.value + 'T00:00:00');
      var isMonday = selected.getDay() === 1;
      var tooSoon = selected < minDate;
      if (isMonday) {
        showStepError('We are closed on Mondays. Please choose another date.', dateEl);
        dateEl.value = '';
        state.date = '';
        state.dateLabel = '';
        updateSummary();
        return;
      }
      if (tooSoon) {
        showStepError('Please book at least 24 hours in advance.', dateEl);
        dateEl.value = '';
        state.date = '';
        state.dateLabel = '';
        updateSummary();
        return;
      }

      clearStepError();
      state.date      = dateEl.value;
      state.dateLabel = formatDate(dateEl.value);
      updateSummary();
      fetchAvailability();
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
    ['guest-firstname', 'guest-lastname', 'guest-email', 'guest-phone', 'guest-count', 'guest-occasion', 'service-select', 'package-select'].forEach(function (id) {
      var el = $('#' + id);
      if (!el) return;
      el.addEventListener('input', function () {
        state.firstName = ($('#guest-firstname') || {}).value || '';
        state.lastName  = ($('#guest-lastname')  || {}).value || '';
        state.email     = ($('#guest-email')     || {}).value || '';
        state.phone     = ($('#guest-phone')     || {}).value || '';
        state.guests    = ($('#guest-count')     || {}).value || '';
        state.occasion  = ($('#guest-occasion')  || {}).value || '';
        state.serviceId = ($('#service-select')  || {}).value || '';
        state.packageId = ($('#package-select')  || {}).value || '';
        updateSummary();
      });
    });
  }

  function initTableSelect() {
    var tableSelect = $('#table-select');
    if (!tableSelect) return;
    tableSelect.addEventListener('change', function () {
      state.tableId = tableSelect.value || '';
      fetchAvailability();
    });
  }

  function resetTableSelection(zoneId) {
    var tableSelect = $('#table-select');
    if (!tableSelect) return;
    var hasZone = !!zoneId;
    Array.prototype.forEach.call(tableSelect.options, function (opt) {
      if (!opt.value) return;
      var optZone = opt.getAttribute('data-zone-id');
      var match = hasZone && optZone === String(zoneId);
      opt.disabled = !match;
      if (!match && opt.selected) opt.selected = false;
    });
    tableSelect.value = '';
  }

  function fetchAvailability() {
    if (!state.date || (!state.zoneId && !state.tableId)) return;

    var form = document.getElementById('booking-form');
    var csrfEl = form ? form.querySelector('input[name="csrf_token"]') : null;
    var csrf = csrfEl ? csrfEl.value : '';

    var actionTokenEl = form ? document.getElementById('check-availability-token') : null;
    var actionToken = actionTokenEl ? actionTokenEl.value : '';
    var body = new URLSearchParams();
    body.set('csrf_token', csrf);
    body.set('action_token', actionToken);
    body.set('appointment_date', state.date);
    if (state.tableId) {
      body.set('table_id', state.tableId);
    } else {
      body.set('zone_id', state.zoneId);
    }

    fetch('../actions.php?action=check_availability', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString(),
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (!data || !data.availability) return;
        updateTimeSlots(data.availability);
      })
      .catch(function () {});
  }

  function updateTimeSlots(availability) {
    $$('.time-slot').forEach(function (slot) {
      var time = slot.dataset.time;
      if (!time) return;
      var isAvailable = availability[time];
      slot.classList.remove('unavailable');
      slot.disabled = false;
      if (!isAvailable) {
        slot.classList.add('unavailable');
        slot.disabled = true;
        slot.classList.remove('selected');
        if (state.timeValue === time) {
          state.timeValue = '';
          state.timeLabel = '';
          updateSummary();
        }
      }
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
      '#rev-spot':     state.spot || '—',
      '#rev-date':     state.dateLabel,
      '#rev-time':     state.timeLabel,
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
    var wizard = $('#booking-form');
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
          // Final submission — send to server
          var form = document.getElementById('booking-form');
          if (!form) return;
          setHiddenValue('service-id', state.serviceId);
          setHiddenValue('event-package-id', state.packageId);
          setHiddenValue('zone-id', state.tableId ? '' : (state.zoneId || ''));
          setHiddenValue('table-id', state.tableId || '');
          setHiddenValue('appointment-date', state.date);
          setHiddenValue('start-time', state.timeValue);
          setHiddenValue('party-size', state.guests);
          setHiddenValue('special-requests', state.requests || '');
          setHiddenValue('zone-label', state.zoneLabel || '');
          setHiddenValue('date-label', state.dateLabel || '');
          setHiddenValue('time-label', state.timeLabel || '');
          form.submit();
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

  function setHiddenValue(id, val) {
    var el = document.getElementById(id);
    if (el) el.value = val;
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
      '#conf-total':   'total',
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
    if ($('#booking-form')) {
      goToStep(1);
      initZoneCards();
      initTimeSlots();
      initDateInput();
      initGuestFieldListeners();
      initTableSelect();
      resetTableSelection('');
      initNavButtons();
      updateSummary();
    }

    // Confirmation page
    if ($('#confirmation-page')) {
      initConfirmPage();
    }
  });

})();
