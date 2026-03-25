(function () {
  'use strict';

  /* ------------------------------------------------------------------
     Floor Status Modal + Polling (10s)
  ------------------------------------------------------------------ */
  const floorMeta = document.getElementById('floor-csrf-container');
  const floorModal = document.getElementById('floorStatusModal');
  const floorForm = document.getElementById('floorStatusForm');
  const floorSelect = document.getElementById('floorStatusSelect');
  const floorTableId = document.getElementById('floorStatusTableId');
  const floorTableLabel = document.getElementById('floorStatusTableLabel');

  const FLOOR_STATUS = ['available', 'reserved', 'occupied'];

  /* ------------------------------------------------------------------
     Segmented Tabs (Admin Panels)
  ------------------------------------------------------------------ */
  document.querySelectorAll('[data-admin-tabs]').forEach(function (tabsRoot) {
    const buttons = tabsRoot.querySelectorAll('[data-tab]');
    const panels = document.querySelectorAll('.admin-panel[data-panel]');

    function activateTab(key) {
      buttons.forEach(function (btn) {
        const isActive = btn.getAttribute('data-tab') === key;
        btn.classList.toggle('active', isActive);
        btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
      });
      panels.forEach(function (panel) {
        const isActive = panel.getAttribute('data-panel') === key;
        panel.classList.toggle('active', isActive);
      });
    }

    buttons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        const key = btn.getAttribute('data-tab');
        if (key) activateTab(key);
      });
    });
  });

  function setTileStatus(tile, status) {
    FLOOR_STATUS.forEach(function (s) { tile.classList.remove('floor-tile-new--' + s); });
    tile.classList.add('floor-tile-new--' + status);
    tile.setAttribute('data-status', status);
    const lbl = tile.querySelector('.floor-tile-new-status');
    if (lbl) lbl.textContent = status.charAt(0).toUpperCase() + status.slice(1);
  }

  function updatePanelStats(panel) {
    if (!panel) return;
    let avail = 0, res = 0, occ = 0;
    panel.querySelectorAll('.floor-tile-new').forEach(function (t) {
      const st = t.getAttribute('data-status');
      if (st === 'available') avail++;
      if (st === 'reserved') res++;
      if (st === 'occupied') occ++;
    });
    const statVals = panel.querySelectorAll('.floor-stat-val');
    if (statVals.length >= 3) {
      statVals[0].textContent = avail;
      statVals[1].textContent = res;
      statVals[2].textContent = occ;
    }
  }

  if (floorMeta) {
    const csrf = floorMeta.dataset.csrf || '';
    const actionToken = floorMeta.dataset.actionToken || '';

    document.querySelectorAll('.floor-tile-new[data-status]').forEach(function (tile) {
      tile.addEventListener('click', function () {
        const tableId = tile.getAttribute('data-table-id');
        const currentStatus = tile.getAttribute('data-status') || 'available';
        const STATUS_CYCLE = ['available', 'occupied'];
        const nextStatus = STATUS_CYCLE[(STATUS_CYCLE.indexOf(currentStatus) + 1) % STATUS_CYCLE.length];

        // Update UI immediately and mark as recently changed
        setTileStatus(tile, nextStatus);
        tile.setAttribute('data-last-changed', Date.now().toString());
        updatePanelStats(tile.closest('.admin-panel'));

        const body = new URLSearchParams();
        body.set('csrf_token', csrf);
        body.set('action_token', actionToken);
        body.set('table_id', tableId);
        body.set('status', nextStatus);

        fetch('../actions.php?action=admin_floor_update', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: body.toString(),
        })
          .then(function (res) { return res.json(); })
          .then(function (data) {
            if (!data || !data.ok) {
              setTileStatus(tile, currentStatus);
              updatePanelStats(tile.closest('.admin-panel'));
              console.error('Failed to update status:', data);
            }
          })
          .catch(function (err) {
            setTileStatus(tile, currentStatus);
            updatePanelStats(tile.closest('.admin-panel'));
            console.error('Error updating status:', err);
          });
      });
    });
  }

  if (floorMeta && floorModal && floorForm) {
    const csrf = floorMeta.dataset.csrf || '';
    const actionToken = floorMeta.dataset.actionToken || '';
    const statusToken = floorMeta.dataset.statusToken || '';

    document.querySelectorAll('.floor-tile-new[data-status]').forEach(function (tile) {
      tile.addEventListener('click', function () {
        const tableId = tile.getAttribute('data-table-id');
        const labelEl = tile.querySelector('.floor-tile-new-number');
        if (floorTableId) floorTableId.value = tableId || '';
        if (floorTableLabel) floorTableLabel.textContent = labelEl ? labelEl.textContent.trim() : 'Table';
        if (floorSelect) floorSelect.value = tile.getAttribute('data-status') || 'available';
        floorModal.classList.add('open');
      });
    });

    floorForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const tableId = floorTableId ? floorTableId.value : '';
      const status = floorSelect ? floorSelect.value : '';
      if (!tableId || FLOOR_STATUS.indexOf(status) === -1) return;

      const body = new URLSearchParams();
      body.set('csrf_token', csrf);
      body.set('action_token', actionToken);
      body.set('table_id', tableId);
      body.set('status', status);

      fetch('../actions.php?action=admin_floor_update', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body.toString(),
      })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (data && data.ok) {
            const tile = document.querySelector('.floor-tile-new[data-table-id="' + tableId + '"]');
            if (tile) {
              setTileStatus(tile, status);
              updatePanelStats(tile.closest('.admin-panel'));
            }
            floorModal.classList.remove('open');
          }
        })
        .catch(function () { });
    });

    // Poll every 10s
    setInterval(function () {
      const body = new URLSearchParams();
      body.set('csrf_token', csrf);
      body.set('action_token', statusToken);
      fetch('../actions.php?action=admin_floor_status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body.toString(),
      })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (!data || !data.tables) return;
          const now = Date.now();
          const PROTECT_MS = 30000; // Don't override manual changes for 30 seconds

          data.tables.forEach(function (t) {
            const tile = document.querySelector('.floor-tile-new[data-table-id="' + t.table_id + '"]');
            if (!tile) return;

            // Skip if tile was recently clicked (manual override)
            const lastChanged = tile.getAttribute('data-last-changed');
            if (lastChanged && (now - parseInt(lastChanged)) < PROTECT_MS) {
              return; // Don't override recent manual changes
            }

            setTileStatus(tile, t.status);
          });
          document.querySelectorAll('.admin-panel').forEach(function (panel) {
            updatePanelStats(panel);
          });
        })
        .catch(function () { });
    }, 10000);
  }

  /* ------------------------------------------------------------------
     Pre-fill Reservation Detail Modal from table row
  ------------------------------------------------------------------ */
  document.querySelectorAll('[data-open-detail]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const modal = document.getElementById('reservationDetailModal');
      if (!modal) return;
      const row = btn.closest('tr');
      if (!row) { modal.classList.add('open'); return; }

      const cells = row.cells;
      const set = function (id, val) { const el = modal.querySelector('#detail' + id); if (el) el.textContent = val; };

      set('Id', cells[0] ? cells[0].textContent.trim() : '-');
      set('Guest', cells[1] ? cells[1].textContent.trim() : '-');
      set('Zone', cells[2] ? cells[2].textContent.trim() : '-');
      set('Seating', cells[3] ? cells[3].textContent.trim() : '-');
      set('Date', cells[4] ? cells[4].textContent.trim() : '-');
      set('Time', cells[5] ? cells[5].textContent.trim() : '-');
      set('Guests', cells[6] ? cells[6].textContent.trim() : '-');
      const badgeEl = row.querySelector('.badge');
      const statusEl = modal.querySelector('#detailStatus');
      if (statusEl && badgeEl) {
        statusEl.className = badgeEl.className;
        statusEl.textContent = badgeEl.textContent;
      }

      modal.classList.add('open');
    });
  });

  /* ------------------------------------------------------------------
     Guest Profile Modal Dynamic Population & Edit
  ------------------------------------------------------------------ */
  document.querySelectorAll('.view-profile-btn').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const row = btn.closest('tr');
      if (!row) return;

      const index = row.getAttribute('data-user-index');
      const name = row.querySelector('.guest-name').textContent.trim();
      const statusBadge = row.querySelector('.guest-status-badge');
      const statusText = statusBadge ? statusBadge.textContent.trim() : 'Regular';

      const initials = name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase();

      const modal = document.getElementById('userProfileModal');
      if (!modal) return;

      modal.setAttribute('data-active-user-index', index);

      const modalName = modal.querySelector('#modalGuestName');
      const modalInitials = modal.querySelector('#modalGuestInitials');
      const modalStatus = modal.querySelector('#modalGuestStatus');
      const selectStatus = modal.querySelector('#guestStatusSelect');

      if (modalName) modalName.textContent = name;
      if (modalInitials) modalInitials.textContent = initials;
      if (modalStatus) {
        modalStatus.textContent = statusText;
        modalStatus.className = 'badge badge-' + statusText.toLowerCase() + ' guest-status-badge';
      }
      if (selectStatus) {
        selectStatus.value = statusText;
      }

      const editBlock = document.getElementById('guestStatusEdit');
      const viewBlock = document.getElementById('guestStatusView');
      if (editBlock && viewBlock) {
        editBlock.style.display = 'none';
        viewBlock.style.display = 'flex';
      }
    });
  });

  const editStatusBtn = document.getElementById('editGuestStatusBtn');
  const saveStatusBtn = document.getElementById('saveGuestStatusBtn');
  const cancelStatusBtn = document.getElementById('cancelGuestStatusBtn');

  if (editStatusBtn && saveStatusBtn && cancelStatusBtn) {
    const viewMode = document.getElementById('guestStatusView');
    const editMode = document.getElementById('guestStatusEdit');
    const statusSelect = document.getElementById('guestStatusSelect');
    const modalStatus = document.getElementById('modalGuestStatus');
    const modal = document.getElementById('userProfileModal');

    editStatusBtn.addEventListener('click', function () {
      viewMode.style.display = 'none';
      editMode.style.display = 'flex';
    });

    cancelStatusBtn.addEventListener('click', function () {
      editMode.style.display = 'none';
      viewMode.style.display = 'flex';
      statusSelect.value = modalStatus.textContent.trim();
    });

    saveStatusBtn.addEventListener('click', function () {
      const newStatus = statusSelect.value;
      const lowerStatus = newStatus.toLowerCase();

      modalStatus.textContent = newStatus;
      modalStatus.className = 'badge badge-' + lowerStatus + ' guest-status-badge';

      const activeIndex = modal.getAttribute('data-active-user-index');
      if (activeIndex !== null) {
        const row = document.querySelector('tr[data-user-index="' + activeIndex + '"]');
        if (row) {
          const rowBadge = row.querySelector('.guest-status-badge');
          if (rowBadge) {
            rowBadge.textContent = newStatus;
            rowBadge.className = 'badge badge-' + lowerStatus + ' guest-status-badge';
          }
        }
      }

      editMode.style.display = 'none';
      viewMode.style.display = 'flex';
    });
  }

  /* ------------------------------------------------------------------
     Add New Table Flow (Floor Management)
  ------------------------------------------------------------------ */
  const addTableForm = document.getElementById('addTableForm');
  if (addTableForm) {
    addTableForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const zone = document.getElementById('newTableZone').value;
      const name = document.getElementById('newTableName').value;
      const seats = document.getElementById('newTableSeats').value;

      const panel = document.querySelector('[data-panel="' + zone + '"]');
      const grid = panel ? panel.querySelector('.floor-grid-new') : null;

      if (grid) {
        // Create the new tile element
        const tile = document.createElement('div');
        tile.className = 'floor-tile-new floor-tile-new--available';
        tile.setAttribute('data-status', 'available');
        tile.setAttribute('tabindex', '0');

        tile.innerHTML = `
          <div class="floor-tile-new-top">
            <span class="floor-tile-new-number">${name}</span>
            <svg class="floor-tile-new-edit" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
          </div>
          <div class="floor-tile-new-capacity">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            ${seats} seats
          </div>
          <div class="floor-tile-new-status">Available</div>
        `;

        // Attach the critical click handler so the new tile behaves like natively rendered ones
        tile.addEventListener('click', function () {
          const tableId = tile.getAttribute('data-table-id');
          const currentStatus = tile.getAttribute('data-status') || 'available';
          const STATUS_CYCLE = ['available', 'occupied'];
          const nextStatus = STATUS_CYCLE[(STATUS_CYCLE.indexOf(currentStatus) + 1) % STATUS_CYCLE.length];

          // Update UI immediately
          STATUS_CYCLE.forEach(function (s) { tile.classList.remove('floor-tile-new--' + s); });
          tile.classList.add('floor-tile-new--' + nextStatus);
          tile.setAttribute('data-status', nextStatus);

          const lbl = tile.querySelector('.floor-tile-new-status');
          if (lbl) {
            lbl.textContent = nextStatus.charAt(0).toUpperCase() + nextStatus.slice(1);
          }

          // Update stats
          if (panel) {
            let avail = 0, res = 0, occ = 0;
            panel.querySelectorAll('.floor-tile-new').forEach(function (t) {
              const st = t.getAttribute('data-status');
              if (st === 'available') avail++;
              if (st === 'reserved') res++;
              if (st === 'occupied') occ++;
            });
            const statVals = panel.querySelectorAll('.floor-stat-val');
            if (statVals.length >= 3) {
              statVals[0].textContent = avail;
              statVals[1].textContent = res;
              statVals[2].textContent = occ;
            }
          }

          // Save to database if we have the CSRF token
          const floorMeta = document.getElementById('floor-csrf-container');
          if (floorMeta && tableId) {
            const csrf = floorMeta.dataset.csrf || '';
            const actionToken = floorMeta.dataset.actionToken || '';

            const body = new URLSearchParams();
            body.set('csrf_token', csrf);
            body.set('action_token', actionToken);
            body.set('table_id', tableId);
            body.set('status', nextStatus);

            fetch('../actions.php?action=admin_floor_update', {
              method: 'POST',
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
              body: body.toString(),
            })
              .then(function (res) { return res.json(); })
              .catch(function () { });
          }
        });

        // Render it to the DOM
        grid.appendChild(tile);

        // Instantly increment the parent panel's Available counter since new tables are always Available
        let avail = 0;
        panel.querySelectorAll('.floor-tile-new').forEach(function (t) {
          if (t.getAttribute('data-status') === 'available') avail++;
        });
        const statVals = panel.querySelectorAll('.floor-stat-val');
        if (statVals.length > 0) {
          statVals[0].textContent = avail;
        }
      }

      // Clear up the form and shut the modal safely
      addTableForm.reset();
      const modal = document.getElementById('addTableModal');
      if (modal) modal.classList.remove('open');
    });
  }

  /* ------------------------------------------------------------------
     Reserve Table Modal - User lookup and auto-fill
  ------------------------------------------------------------------ */
  const reserveUserId = document.getElementById('reserveUserId');
  const guestInfoFields = document.getElementById('guestInfoFields');
  const reserveFirstName = document.getElementById('reserveFirstName');
  const reserveLastName = document.getElementById('reserveLastName');
  const reserveEmail = document.getElementById('reserveEmail');
  const reservePhone = document.getElementById('reservePhone');
  const reserveZone = document.getElementById('reserveZone');
  const reserveTable = document.getElementById('reserveTable');

  // Store tables data for zone-based filtering
  window.ALL_TABLES = window.ALL_TABLES || {};

  if (reserveUserId && guestInfoFields) {
    reserveUserId.addEventListener('change', function () {
      const selected = reserveUserId.options[reserveUserId.selectedIndex];
      const userId = selected.value;

      if (userId === 'guest') {
        // Walk-in guest - show empty fields for manual entry
        guestInfoFields.style.display = 'block';
        reserveFirstName.value = '';
        reserveLastName.value = '';
        reserveEmail.value = '';
        reservePhone.value = '';
        reserveFirstName.required = true;
        reserveLastName.required = true;
        reserveEmail.required = true;
      } else if (userId) {
        // Registered user - auto-fill from data attributes
        guestInfoFields.style.display = 'block';
        reserveFirstName.value = selected.dataset.firstName || '';
        reserveLastName.value = selected.dataset.lastName || '';
        reserveEmail.value = selected.dataset.email || '';
        reservePhone.value = selected.dataset.phone || '';
        reserveFirstName.required = true;
        reserveLastName.required = true;
        reserveEmail.required = true;
      } else {
        // No selection
        guestInfoFields.style.display = 'none';
      }
    });
  }

  if (reserveZone && reserveTable) {
    reserveZone.addEventListener('change', function () {
      const zoneKey = reserveZone.value;
      reserveTable.innerHTML = '<option value="">-- Select table --</option>';
      reserveTable.disabled = true;

      if (!zoneKey || !window.ALL_TABLES[zoneKey]) return;

      const tables = window.ALL_TABLES[zoneKey];
      tables.forEach(function (t) {
        const opt = document.createElement('option');
        opt.value = t.table_id;
        opt.textContent = t.label + ' (' + t.cap + ' seats)';
        reserveTable.appendChild(opt);
      });
      reserveTable.disabled = false;
    });
  }

})();


