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
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    guests: '',
    occasion: '',
    requests: '',
    serviceId: '',
    packageId: '',
    // Step 2 – Zone & Spot
    zone: '',       // 'patio' | 'dining-room' | 'bar'
    zoneLabel: '',
    zoneId: '',
    spot: '',       // e.g. 'Garden Terrace'
    tableId: '',
    // Step 3 – Date & Time
    date: '',
    dateLabel: '',
    time: '',
    timeValue: '',
    timeLabel: '',
    assignedTables: {},
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
      if (s === n) el.classList.add('active');
      if (s < n) el.classList.add('completed');
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
      // Keep Step 1 back buttons active so they can return to the dashboard.
      btn.disabled = false;
    });
    $$('#btn-next, [data-action="next"]').forEach(function (btn) {
      var label = 'Continue';
      if (state.currentStep === 3) label = 'Review Reservation';
      if (state.currentStep === 4) label = 'Confirm Reservation';

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
      if (!gs || !gs.value) { showStepError('Please choose the number of guests.', gs); return false; }
      var guestCount = parseInt(gs.value, 10);
      var guestMax = parseInt(gs.max, 10) || getGuestCountMax(state.zoneId || '');
      if (!guestCount || guestCount < 1) { showStepError('Please choose at least 1 guest.', gs); return false; }
      if (guestCount > guestMax) { showStepError('Please choose ' + guestMax + ' guests or fewer.', gs); return false; }
      // Persist into state
      state.firstName = fn.value.trim();
      state.lastName = ln.value.trim();
      state.email = em.value.trim();
      state.phone = ($('#guest-phone') || {}).value || '';
      state.guests = gs.value;
      state.occasion = ($('#guest-occasion') || {}).value || '';
      state.requests = ($('#guest-requests') || {}).value || '';

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
      var hasPills = $('#seating-spot-pills') && $('#seating-spot-pills').children.length > 0;
      if (hasPills && !state.spot) { showStepError('Please select a seating preference to continue.', null); return false; }
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
    setVal('#sum-name', state.firstName && state.lastName ? state.firstName + ' ' + state.lastName : null);
    setVal('#sum-email', state.email || null);
    setVal('#sum-guests', state.guests ? state.guests + (state.guests === '1' ? ' guest' : ' guests') : null);
    setVal('#sum-zone', state.zoneLabel || null);
    setVal('#sum-spot', state.spot || null);
    setVal('#sum-date', state.dateLabel || null);
    setVal('#sum-time', state.timeLabel || null);
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

  function getGuestCountMax(zoneId) {
    var input = $('#guest-count');
    var fallback = 8;

    if (input) {
      var attrMax = parseInt(input.getAttribute('max') || input.dataset.maxCapacity || '8', 10);
      if (!isNaN(attrMax) && attrMax > 0) {
        fallback = attrMax;
      }
    }

    if (!zoneId || !window.ALL_TABLES || !window.ALL_TABLES.length) {
      return fallback;
    }

    var zoneMax = 0;
    window.ALL_TABLES.forEach(function (t) {
      if (String(t.zone_id) === String(zoneId)) {
        zoneMax = Math.max(zoneMax, parseInt(t.capacity, 10) || 0);
      }
    });

    return zoneMax > 0 ? zoneMax : fallback;
  }

  function updateGuestCountCap(zoneId) {
    var input = $('#guest-count');
    if (!input) return 0;

    var maxGuests = getGuestCountMax(zoneId || state.zoneId || '');
    input.max = String(maxGuests);

    var current = parseInt(input.value, 10);
    if (isNaN(current) || current < 1) current = 1;
    if (current > maxGuests) current = maxGuests;
    input.value = String(current);
    state.guests = String(current);

    var hint = $('#guest-count-hint');
    if (hint) {
      hint.textContent = zoneId || state.zoneId
        ? 'Up to ' + maxGuests + ' guests for this dining zone.'
        : 'Up to ' + maxGuests + ' guests based on the table capacities in our database.';
    }

    return maxGuests;
  }

  // ─── Zone selection ───────────────────────────────────────────────────────
  function initZoneCards() {
    $$('.zone-select-card').forEach(function (card) {
      card.addEventListener('click', function () {
        $$('.zone-select-card').forEach(function (c) { c.classList.remove('selected'); });
        card.classList.add('selected');
        state.zone = card.dataset.zone;
        state.zoneLabel = card.dataset.label;
        state.zoneId = card.dataset.zoneId;
        state.spot = '';   // reset spot when zone changes
        state.tableId = '';
        updateGuestCountCap(state.zoneId);
        revealSpotPills(state.zone);
        updateSummary();
        fetchAvailability();
      });
    });
  }

  function revealSpotPills(zone) {
    var container = $('#seating-reveal');
    var pillsEl = $('#seating-spot-pills');
    var titleEl = $('#seating-reveal-title');
    var capacityNotice = $('#seating-capacity-notice');
    if (!container || !pillsEl) return;

    if (!window.ALL_TABLES) { container.classList.remove('visible'); return; }

    updateGuestCountCap(state.zoneId);
    var partySize = parseInt(state.guests || 1, 10);

    // Find unique seating preferences for the selected zone and adequate capacity
    var availablePrefs = [];
    window.ALL_TABLES.forEach(function (t) {
      if (t.zone_id == state.zoneId && parseInt(t.capacity, 10) >= partySize) {
        if (t.seating_preference && availablePrefs.indexOf(t.seating_preference) === -1) {
          availablePrefs.push(t.seating_preference);
        }
      }
    });

    if (availablePrefs.length === 0) {
      container.classList.remove('visible');
      if (capacityNotice) capacityNotice.textContent = 'No seating preferences found for this party size.';
      return;
    }

    // Build pills
    pillsEl.innerHTML = '';
    if (titleEl) titleEl.textContent = 'Choose your preferred spot';
    if (capacityNotice) capacityNotice.textContent = 'Options are strictly filtered by your party size in this zone.';

    availablePrefs.forEach(function (spot) {
      var pill = document.createElement('button');
      pill.type = 'button';
      pill.className = 'seating-spot-pill';
      pill.textContent = spot;
      pill.addEventListener('click', function () {
        pillsEl.querySelectorAll('.seating-spot-pill').forEach(function (p) { p.classList.remove('selected'); });
        pill.classList.add('selected');
        state.spot = spot;
        updateSummary();
        fetchAvailability();
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
        state.tableId = slot.dataset.assignedTable || '';
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
    var yyyy = minDate.getFullYear();
    var mm = String(minDate.getMonth() + 1).padStart(2, '0');
    var dd = String(minDate.getDate()).padStart(2, '0');
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
      state.date = dateEl.value;
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
        var oldGuests = state.guests;
        state.firstName = ($('#guest-firstname') || {}).value || '';
        state.lastName = ($('#guest-lastname') || {}).value || '';
        state.email = ($('#guest-email') || {}).value || '';
        state.phone = ($('#guest-phone') || {}).value || '';
        state.guests = ($('#guest-count') || {}).value || '';
        state.occasion = ($('#guest-occasion') || {}).value || '';
        state.serviceId = ($('#service-select') || {}).value || '';
        state.packageId = ($('#package-select') || {}).value || '';

        if (id === 'guest-count') {
          updateGuestCountCap(state.zoneId);
        }

        if (id === 'guest-count' && oldGuests !== state.guests && state.zone) {
          revealSpotPills(state.zone);
          fetchAvailability();
        }

        updateSummary();
      });
    });
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
    if (state.spot) {
      body.set('seating_preference', state.spot);
    }
    body.set('zone_id', state.zoneId);
    body.set('party_size', state.guests);

    fetch('../actions.php?action=check_availability', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString(),
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (!data || !data.availability) return;
        state.assignedTables = data.assigned_tables || {};
        updateTimeSlots(data.availability, state.assignedTables);
      })
      .catch(function () { });
  }

  function updateTimeSlots(availability, assignedTables) {
    $$('.time-slot').forEach(function (slot) {
      var time = slot.dataset.time;
      if (!time) return;
      slot.dataset.assignedTable = assignedTables && assignedTables[time] ? assignedTables[time] : '';
      // availability[time] is boolean true/false from PHP
      var isAvailable = availability[time] === true || availability[time] === 1;
      slot.classList.remove('unavailable');
      slot.disabled = false;
      if (!isAvailable) {
        slot.classList.add('unavailable');
        slot.disabled = true;
        slot.classList.remove('selected');
        if (state.timeValue === time) {
          state.timeValue = '';
          state.timeLabel = '';
          state.tableId = '';
          updateSummary();
        }
      }
    });

    // Show/hide "no available seats" message before Review Reservation button
    var hasAvailableSlots = false;
    for (var time in availability) {
      if (availability[time] === true || availability[time] === 1) {
        hasAvailableSlots = true;
        break;
      }
    }

    var noSeatsMsg = $('#no-seats-message');
    if (!hasAvailableSlots && state.date) {
      if (!noSeatsMsg) {
        noSeatsMsg = document.createElement('div');
        noSeatsMsg.id = 'no-seats-message';
        noSeatsMsg.className = 'auth-alert';
        noSeatsMsg.style.cssText = 'margin: var(--space-4) 0; display: flex; align-items: center; gap: var(--space-2);';
        noSeatsMsg.innerHTML =
          '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>' +
          '<span>No available seats in the selected zone for this time. Please choose another time or zone.</span>';

        // Insert before the wizard-nav in step-3
        var step3 = $('#step-3');
        var wizardNav = step3 ? step3.querySelector('.wizard-nav') : null;
        if (wizardNav && step3) {
          step3.insertBefore(noSeatsMsg, wizardNav);
        }
      } else {
        noSeatsMsg.style.display = 'flex';
      }
    } else if (noSeatsMsg) {
      noSeatsMsg.style.display = 'none';
    }
  }

  // ─── Review step: populate details ────────────────────────────────────────
  function populateReview() {
    var fields = {
      '#rev-name': state.firstName + ' ' + state.lastName,
      '#rev-email': state.email,
      '#rev-phone': state.phone || '—',
      '#rev-guests': state.guests + (state.guests === '1' ? ' guest' : ' guests'),
      '#rev-zone': state.zoneLabel,
      '#rev-spot': state.spot || '—',
      '#rev-date': state.dateLabel,
      '#rev-time': state.timeLabel,
      '#rev-occasion': state.occasion || '—',
      '#rev-requests': state.requests || '—',
    };
    for (var sel in fields) {
      var el = $(sel);
      if (el) el.textContent = fields[sel];
    }

    var addonNames = [];
    $$('input[name="add_on_ids[]"]:checked').forEach(function (cb) {
      var qtyEl = document.querySelector('input[name="add_on_qty[' + cb.value + ']"]');
      var qty = qtyEl ? parseInt(qtyEl.value || 1, 10) : 1;
      addonNames.push(cb.dataset.name + (qty > 1 ? ' (x' + qty + ')' : ''));
    });

    var revAddon = $('#rev-addon');
    if (revAddon) {
      if (addonNames.length > 0) {
        revAddon.textContent = addonNames.join(', ');
      } else {
        revAddon.textContent = '—';
      }
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
        // On step 1, go back to dashboard
        if (state.currentStep === 1) {
          window.location.href = '/pages/dashboard/index.php';
          return;
        }
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
          setHiddenValue('zone-id', state.zoneId || '');
          setHiddenValue('table-id', state.tableId || '');
          setHiddenValue('seating-preference', state.spot || '');
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
    var ref = 'EUD-';
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
      '#conf-name': 'name',
      '#conf-guests': 'guests',
      '#conf-zone': 'zone',
      '#conf-date': 'date',
      '#conf-time': 'time',
      '#conf-email': 'email',
      '#conf-occasion': 'occasion',
      '#conf-ref': 'ref',
      '#conf-total': 'total',
    };
    for (var sel in map) {
      var el = $(sel);
      var val = params.get(map[sel]);
      if (el && val) el.textContent = val;
    }
  }

  function initAddonCheckboxes() {
    $$('input[name="add_on_ids[]"]').forEach(function (cb) {
      cb.addEventListener('change', function () {
        var qtyControls = cb.closest('.checkbox-item').querySelector('input[type="number"]');
        if (qtyControls) {
          if (cb.checked) {
            qtyControls.style.display = 'inline-block';
            if (!qtyControls.value) qtyControls.value = 1;
          } else {
            qtyControls.style.display = 'none';
          }
        }
      });
      // Initial state
      var qtyControls = cb.closest('.checkbox-item').querySelector('input[type="number"]');
      if (qtyControls) qtyControls.style.display = cb.checked ? 'inline-block' : 'none';
      if (!cb.checked && qtyControls) qtyControls.style.display = 'none';
    });
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
      initAddonCheckboxes();
      initNavButtons();
      updateGuestCountCap();
      updateSummary();
    }

    // Confirmation page
    if ($('#confirmation-page')) {
      initConfirmPage();
    }
  });

})();
