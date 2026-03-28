/**
 * dashboard.js — Guest Dashboard Interactions
 * Handles: mobile sidebar, tabs, cancel modal, zone selection, time slots, star ratings, and booking form via DB data
 */

(function () {
  'use strict';

  /* ─── Mobile Sidebar ─── */
  const layout = document.querySelector('.dashboard-layout');
  const toggle = document.getElementById('sidebarToggle');
  const overlay = document.getElementById('sidebarOverlay');

  function openSidebar() {
    if (layout) {
      layout.classList.add('sidebar-open');
      localStorage.setItem('guestSidebarOpen', 'true');
    }
  }

  function closeSidebar() {
    if (layout) {
      layout.classList.remove('sidebar-open');
      localStorage.setItem('guestSidebarOpen', 'false');
    }
  }

  // Restore state across navigations
  if (layout && localStorage.getItem('guestSidebarOpen') === 'true') {
    layout.classList.add('sidebar-open');
  }

  toggle && toggle.addEventListener('click', openSidebar);
  overlay && overlay.addEventListener('click', closeSidebar);

  /* Close on Escape */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
      closeSidebar();
      if (reservationDetailModal && reservationDetailModal.classList.contains('open')) {
        closeReservationDetail();
      }
    }
  });

  /* ─── Tabs ─── */
  const tabTriggers = document.querySelectorAll('.tab-trigger[data-tab]');

  tabTriggers.forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      const tabId = trigger.getAttribute('data-tab');
      const parent = trigger.closest('.tabs-container') || document;

      parent.querySelectorAll('.tab-trigger').forEach(function (t) {
        t.classList.remove('active');
        t.setAttribute('aria-selected', 'false');
      });
      parent.querySelectorAll('.tab-content').forEach(function (c) {
        c.classList.remove('active');
      });

      trigger.classList.add('active');
      trigger.setAttribute('aria-selected', 'true');
      const content = parent.querySelector('[data-tab-content="' + tabId + '"]');
      content && content.classList.add('active');
    });
  });

  /* ─── Cancel Reservation Modal ─── */
  const cancelModal = document.getElementById('cancelModal');
  const cancelBtns = document.querySelectorAll('[data-action="cancel-reservation"]');
  const cancelConfirm = document.getElementById('cancelConfirm');
  const cancelBack = document.querySelectorAll('[data-action="close-cancel-modal"]');

  function openCancelModal() {
    cancelModal && cancelModal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeCancelModal() {
    cancelModal && cancelModal.classList.remove('active');
    document.body.style.overflow = '';
  }

  function formatDashboardTime(time24) {
    if (!time24) return '';
    const parts = String(time24).split(':');
    let h = parseInt(parts[0], 10);
    const m = parts[1] || '00';
    const ampm = h >= 12 ? 'PM' : 'AM';
    if (h > 12) h -= 12;
    if (h === 0) h = 12;
    return h + ':' + m + ' ' + ampm;
  }

  cancelBtns.forEach(function (btn) {
    btn.addEventListener('click', openCancelModal);
  });

  cancelBack.forEach(function (btn) {
    btn.addEventListener('click', closeCancelModal);
  });

  cancelConfirm && cancelConfirm.addEventListener('click', function () {
    closeCancelModal();
  });

  cancelModal && cancelModal.addEventListener('click', function (e) {
    if (e.target === cancelModal) closeCancelModal();
  });

  /* ─── Interactive Star Rating (History page) ─── */
  const reservationDetailModal = document.getElementById('reservationDetailModal');
  const reservationDetailClose = document.getElementById('reservationDetailClose');
  const reservationRows = document.querySelectorAll('[data-reservation-details="1"]');

  function setReservationDetailText(id, value) {
    const el = document.getElementById(id);
    if (!el) return;
    el.textContent = value && String(value).trim() !== '' ? value : '-';
  }

  function formatDetailDate(dateValue) {
    if (!dateValue) return '-';
    const parsed = new Date(dateValue + 'T00:00:00');
    if (Number.isNaN(parsed.getTime())) return dateValue;
    return parsed.toLocaleDateString('en-US', {
      weekday: 'long',
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    });
  }

  function formatDetailDateTime(dateTimeValue) {
    if (!dateTimeValue) return '-';
    const normalized = String(dateTimeValue).replace(' ', 'T');
    const parsed = new Date(normalized);
    if (Number.isNaN(parsed.getTime())) return dateTimeValue;
    return parsed.toLocaleString('en-US', {
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: 'numeric',
      minute: '2-digit'
    });
  }

  function openReservationDetail(row) {
    if (!reservationDetailModal || !row) return;

    const startTime = row.dataset.startTime ? formatDashboardTime(row.dataset.startTime) : '-';
    const endTime = row.dataset.endTime ? formatDashboardTime(row.dataset.endTime) : '-';
    const partySize = row.dataset.partySize || '';
    const notes = row.dataset.specialRequests || '';

    setReservationDetailText('reservationDetailId', row.dataset.appointmentId || '-');
    setReservationDetailText('reservationDetailStatus', row.dataset.statusLabel || row.dataset.statusName || '-');
    setReservationDetailText('reservationDetailZone', row.dataset.zoneName || '-');
    setReservationDetailText('reservationDetailDate', formatDetailDate(row.dataset.appointmentDate || ''));
    setReservationDetailText('reservationDetailTime', startTime !== '-' && endTime !== '-' ? startTime + ' - ' + endTime : startTime);
    setReservationDetailText('reservationDetailParty', partySize ? partySize + (partySize === '1' ? ' Guest' : ' Guests') : '-');
    setReservationDetailText('reservationDetailTable', row.dataset.tableLabel || '-');
    setReservationDetailText('reservationDetailService', row.dataset.serviceName || '-');
    setReservationDetailText('reservationDetailPackage', row.dataset.packageName || '-');
    setReservationDetailText('reservationDetailEmail', row.dataset.customerEmail || '-');
    setReservationDetailText('reservationDetailCreated', formatDetailDateTime(row.dataset.createdAt || ''));
    setReservationDetailText('reservationDetailNotes', notes || 'No special requests.');

    reservationDetailModal.classList.add('open');
    reservationDetailModal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
  }

  function closeReservationDetail() {
    if (!reservationDetailModal) return;
    reservationDetailModal.classList.remove('open');
    reservationDetailModal.setAttribute('aria-hidden', 'true');
    document.body.style.overflow = '';
  }

  reservationRows.forEach(function (row) {
    row.addEventListener('click', function (e) {
      if (e.target.closest('.reservation-actions')) return;
      openReservationDetail(row);
    });

    row.addEventListener('keydown', function (e) {
      if (e.target.closest('.reservation-actions')) return;
      if (e.key === 'Enter' || e.key === ' ') {
        e.preventDefault();
        openReservationDetail(row);
      }
    });
  });

  reservationDetailClose && reservationDetailClose.addEventListener('click', closeReservationDetail);
  reservationDetailModal && reservationDetailModal.addEventListener('click', function (e) {
    if (e.target === reservationDetailModal) closeReservationDetail();
  });

  const ratingGroups = document.querySelectorAll('.rating-interactive');
  ratingGroups.forEach(function (group) {
    const stars = group.querySelectorAll('.rating-star');
    stars.forEach(function (star, index) {
      star.addEventListener('mouseover', function () {
        stars.forEach(function (s, i) { s.classList.toggle('hovered', i <= index); });
      });
      star.addEventListener('mouseout', function () {
        stars.forEach(function (s) { s.classList.remove('hovered'); });
      });
      star.addEventListener('click', function () {
        stars.forEach(function (s, i) { s.classList.toggle('selected', i <= index); });
      });
    });
  });

  /* ─── BOOKING PAGE LOGIC ─── */
  const bookingForm = document.getElementById('dashboardBookingForm');
  if (bookingForm) {

    // Dynamic state
    const state = {
      zoneId: '',
      zoneLabel: '',
      seatingPref: '',
      tableId: '', // Set when a time slot is selected (check_availability returns this)
      date: '',
      timeVal: '',
      partySize: 2,
      assignedTables: {}
    };

    const zoneCards = document.querySelectorAll('.zone-card-select');
    const guestInput = document.getElementById('bookGuests');
    const datePicker = document.getElementById('bookDate');
    const notesInput = document.getElementById('bookNotes');

    // Summary Els
    const sumGuests = document.getElementById('summaryGuests');
    const sumZone = document.getElementById('summaryZone');
    const sumSpot = document.getElementById('summarySpot');
    const sumDate = document.getElementById('summaryDate');
    const sumTime = document.getElementById('summaryTime');
    const errorMsg = document.getElementById('formReviewError');

    function updateSummary() {
      if (sumGuests) sumGuests.textContent = state.partySize + (state.partySize === 1 ? ' Guest' : ' Guests');
      if (sumZone) sumZone.textContent = state.zoneLabel || '—';
      if (sumSpot) sumSpot.textContent = state.seatingPref || '—';

      if (sumDate && state.date) {
        const d = new Date(state.date + 'T00:00:00');
        sumDate.textContent = d.toLocaleDateString('en-US', { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' });
      } else if (sumDate) {
        sumDate.textContent = '—';
      }

      if (sumTime) sumTime.textContent = state.timeVal ? formatTime(state.timeVal) : '—';

      const submitBtn = document.getElementById('btnConfirmReservation');
      if (submitBtn) {
        if (!state.zoneId || !state.date || !state.timeVal || !state.tableId || !state.seatingPref) {
          submitBtn.style.opacity = '0.5';
          submitBtn.style.pointerEvents = 'none';
        } else {
          submitBtn.style.opacity = '1';
          submitBtn.style.pointerEvents = 'auto';
        }
      }
    }

    /* 1. Zone Selection */
    zoneCards.forEach(function (card) {
      card.addEventListener('click', function () {
        zoneCards.forEach(c => c.classList.remove('selected'));
        card.classList.add('selected');
        state.zoneId = card.getAttribute('data-zone-id');
        state.zoneLabel = card.getAttribute('data-zone');
        state.seatingPref = '';
        state.tableId = '';
        state.timeVal = '';
        renderPills();
        updateSummary();
        fetchAvailability();
      });
    });

    /* 2. Seating Preference Selection */
    function renderPills() {
      const reveal = document.getElementById('seatingReveal');
      const pillsEl = document.getElementById('seatingSpotPills');
      if (!reveal || !pillsEl) return;
      if (!state.zoneId) { reveal.classList.remove('visible'); return; }

      // Filter DB_TABLES
      const tables = (window.DB_TABLES || []).filter(t => String(t.zone_id) === String(state.zoneId) && parseInt(t.capacity, 10) >= state.partySize);

      // Get unique seating preferences
      const prefs = new Set();
      tables.forEach(t => {
        if (t.seating_preference) prefs.add(t.seating_preference);
      });

      pillsEl.innerHTML = '';

      if (prefs.size === 0) {
        pillsEl.innerHTML = '<p style="color:var(--clr-muted-fg); font-size:0.875rem;">No seating options big enough for your party in this zone.</p>';
      } else {
        prefs.forEach(pref => {
          const pill = document.createElement('button');
          pill.type = 'button';
          pill.className = 'seating-spot-pill';
          pill.textContent = pref;
          pill.addEventListener('click', function () {
            pillsEl.querySelectorAll('.seating-spot-pill').forEach(p => p.classList.remove('selected'));
            pill.classList.add('selected');
            state.seatingPref = pref;
            state.tableId = '';
            state.timeVal = ''; // Reset time on spot change
            updateSummary();
            fetchAvailability();
          });
          pillsEl.appendChild(pill);
        });
      }
      reveal.classList.add('visible');
    }

    /* 3. Guests & Date */
    if (guestInput) {
      guestInput.addEventListener('input', function () {
        state.partySize = parseInt(guestInput.value, 10) || 1;
        state.seatingPref = '';
        state.tableId = '';
        state.timeVal = '';
        renderPills();
        updateSummary();
        fetchAvailability();
      });
      state.partySize = parseInt(guestInput.value, 10) || 2;
    }

    if (datePicker) {
      function handleDateSelect() {
        if (!datePicker.value) return;
        state.date = datePicker.value;
        state.timeVal = '';
        state.tableId = '';
        updateSummary();
        fetchAvailability();
      }
      datePicker.addEventListener('change', handleDateSelect);
      datePicker.addEventListener('input', handleDateSelect);
    }

    /* 4. Fetch Availability Ajax */
    function fetchAvailability() {
      const timeContainer = document.getElementById('timeSlotContainer');
      if (!timeContainer) return;

      if (!state.date || !state.zoneId || !state.seatingPref) {
        timeContainer.innerHTML = '<p style="color:var(--clr-muted-fg); font-size:0.875rem;">Please select a zone, seating preference, and date to view available times.</p>';
        return;
      }

      const isMonday = new Date(state.date + 'T00:00:00').getDay() === 1;
      if (isMonday) {
        timeContainer.innerHTML = '<p style="color:var(--clr-destructive); font-size:0.875rem;">We are closed on Mondays. Please choose another date.</p>';
        return;
      }

      timeContainer.innerHTML = '<p style="color:var(--clr-muted-fg); font-size:0.875rem;">Loading available times...</p>';

      const csrf = document.querySelector('input[name="csrf_token"]').value;
      const actionToken = document.getElementById('dash-check-availability-token').value;

      const body = new URLSearchParams();
      body.set('csrf_token', csrf);
      body.set('action_token', actionToken);
      body.set('appointment_date', state.date);
      body.set('zone_id', state.zoneId);
      body.set('seating_preference', state.seatingPref);
      body.set('party_size', state.partySize);

      fetch('../../actions.php?action=check_availability', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body.toString()
      })
        .then(res => res.json())
        .then(data => {
          if (!data || !data.availability) {
            timeContainer.innerHTML = '<p style="color:var(--clr-destructive); font-size:0.875rem;">Error loading times.</p>';
            return;
          }
          state.assignedTables = data.assigned_tables || {};
          renderTimeSlots(data.availability, state.assignedTables);
        })
        .catch(err => {
          timeContainer.innerHTML = '<p style="color:var(--clr-destructive); font-size:0.875rem;">Error communicating with server.</p>';
        });
    }

    function renderTimeSlots(availabilityMap, assignedTables) {
      const timeContainer = document.getElementById('timeSlotContainer');
      timeContainer.innerHTML = '';

      const sortedTimes = Object.keys(availabilityMap).sort();
      if (sortedTimes.length === 0) {
        timeContainer.innerHTML = '<p style="color:var(--clr-muted-fg); font-size:0.875rem;">No times available.</p>';
        return;
      }

      let hasAvailableSlots = false;

      sortedTimes.forEach(function (time) {
        const t = document.createElement('div');
        t.className = 'time-slot';
        t.textContent = formatTime(time);
        t.dataset.assignedTable = assignedTables && assignedTables[time] ? assignedTables[time] : '';

        // availabilityMap[time] is boolean true/false from PHP
        const isAvailable = availabilityMap[time] === true || availabilityMap[time] === 1;

        if (isAvailable) {
          hasAvailableSlots = true;
        }

        if (!isAvailable) {
          t.classList.add('unavailable');
          t.setAttribute('disabled', 'true');
        } else {
          t.addEventListener('click', function () {
            timeContainer.querySelectorAll('.time-slot').forEach(function (s) {
              s.classList.remove('selected');
            });
            t.classList.add('selected');
            state.timeVal = time;
            state.tableId = t.dataset.assignedTable || '';
            updateSummary();
          });
        }
        timeContainer.appendChild(t);
      });

      // Show/hide "no available seats" message
      var noSeatsMsg = document.getElementById('dash-no-seats-message');
      if (!hasAvailableSlots && state.date) {
        if (!noSeatsMsg) {
          noSeatsMsg = document.createElement('div');
          noSeatsMsg.id = 'dash-no-seats-message';
          noSeatsMsg.className = 'auth-alert';
          noSeatsMsg.style.cssText = 'margin-top: var(--space-4); display: flex; align-items: center; gap: var(--space-2);';
          noSeatsMsg.innerHTML =
            '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>' +
            '<span>No available seats in the selected zone for this time. Please choose another time or zone.</span>';
          timeContainer.parentNode.appendChild(noSeatsMsg);
        } else {
          noSeatsMsg.style.display = 'flex';
        }
      } else if (noSeatsMsg) {
        noSeatsMsg.style.display = 'none';
      }
    }

    function formatTime(time24) {
      if (!time24) return '';
      const parts = time24.split(':');
      let h = parseInt(parts[0], 10);
      const m = parts[1];
      const ampm = h >= 12 ? 'PM' : 'AM';
      if (h > 12) h -= 12;
      if (h === 0) h = 12;
      return h + ':' + m + ' ' + ampm;
    }

    /* 5. Submit Handling */
    const submitBtn = document.getElementById('btnConfirmReservation');
    if (submitBtn) {
      submitBtn.addEventListener('click', function (e) {
        e.preventDefault();

        // Validation check
        if (!state.zoneId || !state.date || !state.timeVal || !state.tableId || !state.seatingPref) {
          if (errorMsg) {
            errorMsg.style.display = 'block';
            setTimeout(() => { errorMsg.style.display = 'none'; }, 4000);
          }
          return;
        }

        // Fill hidden inputs
        document.getElementById('zone-id').value = state.zoneId;
        document.getElementById('table-id').value = state.tableId;
        document.getElementById('seating-preference').value = state.seatingPref;
        document.getElementById('start-time').value = state.timeVal;
        document.getElementById('zone-label').value = state.zoneLabel;
        document.getElementById('date-label').value = state.date;
        document.getElementById('time-label').value = formatTime(state.timeVal);
        document.getElementById('hidden-party-size').value = state.partySize;
        document.getElementById('hidden-appointment-date').value = state.date;

        let reqNotes = notesInput ? notesInput.value.trim() : '';
        // Prepend seating preference to notes (optional but nice for DB records)
        if (state.seatingPref) {
          reqNotes = `Seating Preference: ${state.seatingPref} ` + (reqNotes ? `\nNotes: ${reqNotes}` : '');
        }

        document.getElementById('hidden-special-requests').value = reqNotes;

        bookingForm.submit();
      });
    }

    // Initialize defaults 
    errorMsg && (errorMsg.style.display = 'none');
    updateSummary();

  }

})();
