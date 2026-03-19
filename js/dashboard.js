/**
 * dashboard.js — Guest Dashboard Interactions
 * Handles: mobile sidebar, tabs, cancel modal, zone selection, time slots, star ratings
 */

(function () {
  'use strict';

  /* ─── Mobile Sidebar ─── */
  const layout   = document.querySelector('.dashboard-layout');
  const toggle   = document.getElementById('sidebarToggle');
  const overlay  = document.getElementById('sidebarOverlay');

  function openSidebar() {
    layout && layout.classList.add('sidebar-open');
  }

  function closeSidebar() {
    layout && layout.classList.remove('sidebar-open');
  }

  toggle  && toggle.addEventListener('click', openSidebar);
  overlay && overlay.addEventListener('click', closeSidebar);

  /* Close on Escape */
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeSidebar();
  });

  /* ─── Tabs ─── */
  const tabTriggers = document.querySelectorAll('.tab-trigger[data-tab]');

  tabTriggers.forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      const tabId = trigger.getAttribute('data-tab');
      const parent = trigger.closest('.tabs-container') || document;

      /* Deactivate all triggers and contents within same container */
      parent.querySelectorAll('.tab-trigger').forEach(function (t) {
        t.classList.remove('active');
        t.setAttribute('aria-selected', 'false');
      });
      parent.querySelectorAll('.tab-content').forEach(function (c) {
        c.classList.remove('active');
      });

      /* Activate selected */
      trigger.classList.add('active');
      trigger.setAttribute('aria-selected', 'true');
      const content = parent.querySelector('[data-tab-content="' + tabId + '"]');
      content && content.classList.add('active');
    });
  });

  /* ─── Cancel Reservation Modal ─── */
  const cancelModal   = document.getElementById('cancelModal');
  const cancelBtns    = document.querySelectorAll('[data-action="cancel-reservation"]');
  const cancelConfirm = document.getElementById('cancelConfirm');
  const cancelBack    = document.querySelectorAll('[data-action="close-cancel-modal"]');

  function openCancelModal() {
    cancelModal && cancelModal.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeCancelModal() {
    cancelModal && cancelModal.classList.remove('active');
    document.body.style.overflow = '';
  }

  cancelBtns.forEach(function (btn) {
    btn.addEventListener('click', openCancelModal);
  });

  cancelBack.forEach(function (btn) {
    btn.addEventListener('click', closeCancelModal);
  });

  cancelConfirm && cancelConfirm.addEventListener('click', function () {
    /* Mock: just close modal, would trigger PHP/AJAX in real app */
    closeCancelModal();
  });

  cancelModal && cancelModal.addEventListener('click', function (e) {
    if (e.target === cancelModal) closeCancelModal();
  });

  /* ─── Seating Spots Data ─── */
  var SEATING_SPOTS = {
    'Patio':       { title: 'in the Patio',        spots: ['Garden Terrace', 'Pergola Nook', 'Fountain View', 'Open Lawn'] },
    'Bar':         { title: 'at the Bar',           spots: ['Bar Counter Seats', 'High-top Table', 'Lounge Sofas', 'Cocktail Booth'] },
    'Dining Room': { title: 'in the Dining Room',   spots: ['Window Table', 'Centre Room', 'Chef\'s Table', 'Intimate Alcove', 'Banquet Booth'] },
  };

  /* ─── Zone Card Selection (Book page) ─── */
  var zoneCards = document.querySelectorAll('.zone-card-select');

  zoneCards.forEach(function (card) {
    card.addEventListener('click', function () {
      zoneCards.forEach(function (c) { c.classList.remove('selected'); });
      card.classList.add('selected');

      var zoneName = card.getAttribute('data-zone');
      var zoneEl   = document.getElementById('summaryZone');
      zoneEl && (zoneEl.textContent = zoneName || '—');

      // Reset spot summary
      var spotEl = document.getElementById('summarySpot');
      spotEl && (spotEl.textContent = '—');

      // Reveal seating spot pills
      revealSpotPills(zoneName);
    });
  });

  function revealSpotPills(zone) {
    var reveal   = document.getElementById('seatingReveal');
    var pillsEl  = document.getElementById('seatingSpotPills');
    var titleEl  = document.getElementById('seatingRevealTitle');
    if (!reveal || !pillsEl) return;

    var data = SEATING_SPOTS[zone];
    if (!data) { reveal.classList.remove('visible'); return; }

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
        var spotEl = document.getElementById('summarySpot');
        spotEl && (spotEl.textContent = spot);
      });
      pillsEl.appendChild(pill);
    });

    reveal.classList.add('visible');
}

  /* ─── Time Slot Selection (Book page) ─── */
  const timeSlots = document.querySelectorAll('.time-slot');

  timeSlots.forEach(function (slot) {
    slot.addEventListener('click', function () {
      if (slot.classList.contains('unavailable')) return;
      timeSlots.forEach(function (s) { s.classList.remove('selected'); });
      slot.classList.add('selected');

      /* Update summary sidebar */
      const timeEl = document.getElementById('summaryTime');
      timeEl && (timeEl.textContent = slot.textContent.trim());
    });
  });

  /* ─── Date Picker → Summary (Book page) ─── */
  const datePicker = document.getElementById('bookDate');
  if (datePicker) {
    datePicker.addEventListener('change', function () {
      const dateEl = document.getElementById('summaryDate');
      if (!dateEl) return;
      const d = new Date(datePicker.value + 'T00:00:00');
      dateEl.textContent = d.toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
    });
  }

  /* ─── Guest Count → Summary (Book page) ─── */
  const guestInput = document.getElementById('bookGuests');
  if (guestInput) {
    guestInput.addEventListener('input', function () {
      const guestsEl = document.getElementById('summaryGuests');
      guestsEl && (guestsEl.textContent = guestInput.value + (guestInput.value === '1' ? ' Guest' : ' Guests'));
    });
  }

  /* ─── Interactive Star Rating (History page) ─── */
  const ratingGroups = document.querySelectorAll('.rating-interactive');

  ratingGroups.forEach(function (group) {
    const stars = group.querySelectorAll('.rating-star');

    stars.forEach(function (star, index) {
      star.addEventListener('mouseover', function () {
        stars.forEach(function (s, i) {
          s.classList.toggle('hovered', i <= index);
        });
      });

      star.addEventListener('mouseout', function () {
        stars.forEach(function (s) { s.classList.remove('hovered'); });
      });

      star.addEventListener('click', function () {
        stars.forEach(function (s, i) {
          s.classList.toggle('selected', i <= index);
        });
      });
    });
  });

})();
