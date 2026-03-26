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
  const selectedFloorDate = floorMeta ? (floorMeta.dataset.selectedDate || '') : '';
  const todayFloorDate = floorMeta ? (floorMeta.dataset.todayDate || '') : '';
  const isTodayFloorView = !!selectedFloorDate && selectedFloorDate === todayFloorDate;
  const isPastFloorView = !!floorMeta && floorMeta.dataset.isPastView === '1';
  const floorDetailModal = document.getElementById('floorDetailModal');
  const floorDetailLoading = document.getElementById('floorDetailLoading');
  const floorDetailContent = document.getElementById('floorDetailContent');
  const floorDetailError = document.getElementById('floorDetailError');
  const floorDetailListWrap = document.getElementById('floorDetailListWrap');
  const floorDetailList = document.getElementById('floorDetailList');
  const floorDetailTitle = document.getElementById('floorDetailTitle');
  const floorDetailSubtitle = document.getElementById('floorDetailSubtitle');
  const floorDetailTableLabel = document.getElementById('floorDetailTableLabel');
  const floorDetailZoneLabel = document.getElementById('floorDetailZoneLabel');
  const floorDetailStatusBadge = document.getElementById('floorDetailStatusBadge');
  const floorDetailGuestName = document.getElementById('floorDetailGuestName');
  const floorDetailGuestEmail = document.getElementById('floorDetailGuestEmail');
  const floorDetailPartySize = document.getElementById('floorDetailPartySize');
  const floorDetailDate = document.getElementById('floorDetailDate');
  const floorDetailTime = document.getElementById('floorDetailTime');
  const floorDetailService = document.getElementById('floorDetailService');
  const floorDetailReference = document.getElementById('floorDetailReference');
  const floorDetailCreated = document.getElementById('floorDetailCreated');
  const floorDetailNotes = document.getElementById('floorDetailNotes');

  const FLOOR_STATUS = ['available', 'reserved', 'occupied'];

  function openModalById(id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add('open');
  }

  function closeModal(modal) {
    if (modal) modal.classList.remove('open');
  }

  const ACTIONS_BASE = '../../actions.php?action=';
  function actionUrl(action) {
    return ACTIONS_BASE + encodeURIComponent(action);
  }

  document.querySelectorAll('[data-modal-open]').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const targetId = btn.getAttribute('data-modal-open');
      if (targetId === 'reserveTableModal' && btn.getAttribute('data-reset-reserve') === '1') {
        resetReserveModalFields();
      }
      if (targetId) openModalById(targetId);
      if (targetId === 'reserveTableModal' && btn.getAttribute('data-reset-reserve') === '1') {
        setTimeout(function () {
          if (reserveZone) {
            reserveZone.focus();
          } else if (reserveService) {
            reserveService.focus();
          }
        }, 0);
      }
    });
  });

  document.querySelectorAll('[data-modal-close]').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      const modal = btn.closest('.admin-modal');
      closeModal(modal);
    });
  });

  document.querySelectorAll('.admin-modal').forEach(function (modal) {
    modal.addEventListener('click', function (e) {
      if (e.target === modal) closeModal(modal);
    });
  });

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
    const reserveBtn = tile.querySelector('[data-reserve-table]');
    if (reserveBtn) reserveBtn.disabled = status !== 'available';
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

  function markTileReserved(tableId) {
    const tile = document.querySelector('.floor-tile-new[data-table-id="' + tableId + '"]');
    if (!tile) return;
    setTileStatus(tile, 'reserved');
    tile.setAttribute('data-last-changed', Date.now().toString());
    const btn = tile.querySelector('[data-reserve-table]');
    if (btn) btn.disabled = true;
    updatePanelStats(tile.closest('.admin-panel'));
  }

  function isReserveButtonClick(event) {
    return !!(event && event.target && event.target.closest && event.target.closest('[data-reserve-table]'));
  }

  function setFloorDetailText(element, value) {
    if (!element) return;
    element.textContent = value || '-';
  }

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function resetFloorDetailState() {
    if (floorDetailLoading) floorDetailLoading.style.display = 'block';
    if (floorDetailContent) floorDetailContent.style.display = 'none';
    if (floorDetailListWrap) floorDetailListWrap.style.display = 'none';
    if (floorDetailList) floorDetailList.innerHTML = '';
    if (floorDetailError) {
      floorDetailError.style.display = 'none';
      const errorText = floorDetailError.querySelector('span');
      if (errorText) errorText.textContent = '';
    }
  }

  function showFloorDetailError(message) {
    if (floorDetailLoading) floorDetailLoading.style.display = 'none';
    if (floorDetailContent) floorDetailContent.style.display = 'none';
    if (floorDetailError) {
      floorDetailError.style.display = 'flex';
      const errorText = floorDetailError.querySelector('span');
      if (errorText) errorText.textContent = message || 'Could not load the table details right now.';
    }
  }

  function showFloorDetailContent(payload) {
    const table = payload && payload.table ? payload.table : {};
    const detail = payload && payload.detail ? payload.detail : {};

    if (floorDetailTitle) {
      floorDetailTitle.textContent = (detail.type === 'occupied' ? 'Occupied Table Details' : 'Reserved Table Details');
    }
    if (floorDetailSubtitle) {
      floorDetailSubtitle.textContent = 'Reservation details for ' + (table.label || 'this table') + ' on ' + (detail.date_label || selectedFloorDate || 'the selected date') + '.';
    }

    setFloorDetailText(floorDetailTableLabel, table.label || 'Table');
    setFloorDetailText(floorDetailZoneLabel, (table.zone_name || '') + (table.capacity ? (' • ' + table.capacity + ' seats') : ''));
    setFloorDetailText(floorDetailGuestName, detail.guest_name || 'Guest');
    setFloorDetailText(floorDetailGuestEmail, detail.guest_email || 'No email provided');
    setFloorDetailText(floorDetailPartySize, detail.party_size ? String(detail.party_size) : (detail.manual_override ? 'Manual floor state' : '-'));
    setFloorDetailText(floorDetailDate, detail.date_label || '-');
    setFloorDetailText(floorDetailTime, detail.time_label || '-');
    setFloorDetailText(floorDetailService, detail.service_label || 'Standard reservation');
    setFloorDetailText(floorDetailReference, detail.appointment_id ? ('#' + detail.appointment_id) : 'Manual override');
    setFloorDetailText(floorDetailCreated, detail.created_label || '-');
    setFloorDetailText(floorDetailNotes, detail.special_requests || 'No special requests.');

    if (floorDetailStatusBadge) {
      floorDetailStatusBadge.className = 'badge ' + (detail.status_badge || '');
      floorDetailStatusBadge.textContent = detail.status_label || 'Reserved';
    }

    if (floorDetailLoading) floorDetailLoading.style.display = 'none';
    if (floorDetailError) floorDetailError.style.display = 'none';
    if (floorDetailContent) floorDetailContent.style.display = 'block';
  }

  function renderFloorDetailList(payload) {
    const details = payload && Array.isArray(payload.details) ? payload.details : [];
    const table = payload && payload.table ? payload.table : {};
    if (!floorDetailListWrap || !floorDetailList) return;

    if (details.length <= 1) {
      floorDetailListWrap.style.display = 'none';
      floorDetailList.innerHTML = '';
      return;
    }

    floorDetailList.innerHTML = '';
    details.forEach(function (entry, index) {
      const tableMeta = [
        table.label || 'Table',
        entry.appointment_id ? ('Ref #' + entry.appointment_id) : 'Manual override'
      ].join(' • ');
      const item = document.createElement('button');
      item.type = 'button';
      item.className = 'floor-detail-list-item' + (index === 0 ? ' active' : '');
      item.setAttribute('data-detail-index', String(index));
      item.innerHTML =
        '<div class="floor-detail-list-main">' +
          '<div>' +
            '<div class="floor-detail-list-name">' + escapeHtml(entry.guest_name || 'Guest') + '</div>' +
            '<div class="floor-detail-list-time">' + escapeHtml(entry.time_label || '-') + '</div>' +
            '<div class="floor-detail-list-meta">' + escapeHtml(tableMeta) + '</div>' +
          '</div>' +
          '<span class="badge ' + escapeHtml(entry.status_badge || '') + '">' + escapeHtml(entry.status_label || 'Reserved') + '</span>' +
        '</div>';

      item.addEventListener('click', function () {
        floorDetailList.querySelectorAll('.floor-detail-list-item').forEach(function (button) {
          button.classList.remove('active');
        });
        item.classList.add('active');
        showFloorDetailContent({
          table: payload.table || {},
          detail: entry,
        });
      });

      floorDetailList.appendChild(item);
    });
    floorDetailListWrap.style.display = 'block';
  }

  function openFloorDetailForTile(tile) {
    if (!floorMeta || !floorDetailModal || !tile) return;

    const tableId = tile.getAttribute('data-table-id') || '';
    const csrf = floorMeta.dataset.csrf || '';
    const detailsToken = floorMeta.dataset.detailsToken || '';

    if (!tableId || !csrf || !detailsToken) return;

    resetFloorDetailState();
    floorDetailModal.classList.add('open');

    const body = new URLSearchParams();
    body.set('csrf_token', csrf);
    body.set('action_token', detailsToken);
    body.set('table_id', tableId);
    body.set('floor_date', selectedFloorDate);

    fetch(actionUrl('admin_floor_details'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString(),
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (!data || !data.ok || !data.detail) {
          showFloorDetailError(data && data.error ? data.error : 'Could not load the table details right now.');
          return;
        }
        renderFloorDetailList(data);
        showFloorDetailContent(data);
      })
      .catch(function () {
        showFloorDetailError('Could not load the table details right now.');
      });
  }

  function pastFloorReserveMessage() {
    return 'Reservations cannot be created while viewing a past floor date.';
  }

  function resetReserveModalFields() {
    if (reservePartySize) reservePartySize.value = '2';
    if (reserveZone) reserveZone.value = '';
    if (reserveZoneDbId) reserveZoneDbId.value = '';
    if (reserveTableId) reserveTableId.value = '';
    if (reserveService) reserveService.value = '';
    if (reservePackage) reservePackage.value = '';
    if (reserveSeating) {
      clearReserveSeating('-- Select zone first --');
      reserveSeating.disabled = false;
    }
    if (reservePartySize) reservePartySize.removeAttribute('max');
    if (reserveDate) reserveDate.value = '';
    if (reserveTime) reserveTime.value = '';
    if (reserveGuestSearch) reserveGuestSearch.value = '';
    if (reserveGuestSuggestions) {
      reserveGuestSuggestions.style.display = 'none';
      reserveGuestSuggestions.innerHTML = '';
    }
    if (reserveUserId) reserveUserId.value = '';
    if (guestInfoFields) guestInfoFields.style.display = 'none';
    if (reserveFirstName) reserveFirstName.value = '';
    if (reserveLastName) reserveLastName.value = '';
    if (reserveEmail) reserveEmail.value = '';
    if (reservePhone) reservePhone.value = '';
    document.querySelectorAll('.admin-addon-item input[type="checkbox"]').forEach(function (cb) {
      cb.checked = false;
    });
    document.querySelectorAll('.admin-addon-item input[type="number"]').forEach(function (qty) {
      qty.value = 1;
      qty.style.display = 'none';
    });
    updateReserveZones();
  }

  if (floorMeta) {
    const csrf = floorMeta.dataset.csrf || '';
    const actionToken = floorMeta.dataset.actionToken || '';
    const statusToken = floorMeta.dataset.statusToken || '';

    document.querySelectorAll('.floor-tile-new[data-status]').forEach(function (tile) {
      tile.addEventListener('click', function (event) {
        const reserveBtn = event && event.target && event.target.closest ? event.target.closest('[data-reserve-table]') : null;
        if (reserveBtn) {
          event.preventDefault();
          event.stopImmediatePropagation();
          event.stopPropagation();
          openReserveModalFromButton(reserveBtn);
          return;
        }
        const tableId = tile.getAttribute('data-table-id');
        const currentStatus = tile.getAttribute('data-status') || 'available';
        if (currentStatus === 'reserved' || currentStatus === 'occupied') {
          openFloorDetailForTile(tile);
          return;
        }
        if (!isTodayFloorView) return;
        if (currentStatus !== 'available') return;
        if (!window.confirm || !window.confirm('Mark this table as occupied now?')) return;
        const nextStatus = 'occupied';

        // Update UI immediately and mark as recently changed
        setTileStatus(tile, nextStatus);
        tile.setAttribute('data-last-changed', Date.now().toString());
        updatePanelStats(tile.closest('.admin-panel'));

        const body = new URLSearchParams();
        body.set('csrf_token', csrf);
        body.set('action_token', actionToken);
        body.set('table_id', tableId);
        body.set('status', nextStatus);
        body.set('floor_date', selectedFloorDate);

        fetch(actionUrl('admin_floor_update'), {
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

      tile.addEventListener('keydown', function (event) {
        if (!event || (event.key !== 'Enter' && event.key !== ' ')) return;
        const currentStatus = tile.getAttribute('data-status') || 'available';
        if (currentStatus !== 'reserved' && currentStatus !== 'occupied') return;
        event.preventDefault();
        openFloorDetailForTile(tile);
      });
    });

    setInterval(function () {
      const body = new URLSearchParams();
      body.set('csrf_token', csrf);
      body.set('action_token', statusToken);
      body.set('floor_date', selectedFloorDate);
      fetch(actionUrl('admin_floor_status'), {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body.toString(),
      })
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (!data || !data.tables) return;
          const now = Date.now();
          const PROTECT_MS = 30000;

          data.tables.forEach(function (t) {
            const tile = document.querySelector('.floor-tile-new[data-table-id="' + t.table_id + '"]');
            if (!tile) return;

            const lastChanged = tile.getAttribute('data-last-changed');
            if (lastChanged && (now - parseInt(lastChanged, 10)) < PROTECT_MS) {
              return;
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

  if (floorMeta && floorModal && floorForm) {
    const csrf = floorMeta.dataset.csrf || '';
    const actionToken = floorMeta.dataset.actionToken || '';
    const statusToken = floorMeta.dataset.statusToken || '';

    document.querySelectorAll('.floor-tile-new[data-status]').forEach(function (tile) {
      tile.addEventListener('click', function (event) {
        const reserveBtn = event && event.target && event.target.closest ? event.target.closest('[data-reserve-table]') : null;
        if (reserveBtn) {
          event.preventDefault();
          event.stopImmediatePropagation();
          event.stopPropagation();
          openReserveModalFromButton(reserveBtn);
          return;
        }
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
      if (!isTodayFloorView) return;

      const body = new URLSearchParams();
      body.set('csrf_token', csrf);
      body.set('action_token', actionToken);
      body.set('table_id', tableId);
      body.set('status', status);
      body.set('floor_date', selectedFloorDate);

      fetch(actionUrl('admin_floor_update'), {
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
      body.set('floor_date', selectedFloorDate);
      fetch(actionUrl('admin_floor_status'), {
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
     Reserve Table Modal - User lookup and auto-fill
  ------------------------------------------------------------------ */
  const reserveUserId = document.getElementById('reserveUserId');
  const reserveGuestSearch = document.getElementById('reserveGuestSearch');
  const reserveGuestSuggestions = document.getElementById('reserveGuestSuggestions');
  const guestInfoFields = document.getElementById('guestInfoFields');
  const reserveFirstName = document.getElementById('reserveFirstName');
  const reserveLastName = document.getElementById('reserveLastName');
  const reserveEmail = document.getElementById('reserveEmail');
  const reservePhone = document.getElementById('reservePhone');
  const reservePartySize = document.getElementById('reservePartySize');
  const reserveZoneDbId = document.getElementById('reserveZoneDbId');
  const reserveTableId = document.getElementById('reserveTableId');
  const reserveZone = document.getElementById('reserveZone');
  const reserveService = document.getElementById('reserveService');
  const reservePackage = document.getElementById('reservePackage');
  const reserveSeating = document.getElementById('reserveSeating');
  const reserveDate = document.getElementById('reserveDate');
  const reserveTime = document.getElementById('reserveTime');

  // Store tables data for zone-based filtering
  window.ALL_TABLES = window.ALL_TABLES || {};
  window.ADMIN_GUESTS = window.ADMIN_GUESTS || [];

  function normalizeText(value) {
    return String(value || '').trim().toLowerCase();
  }

  function normalizeStatus(value) {
    return normalizeText(value);
  }

  function getTablePreference(table) {
    return normalizeText(table && (table.seating_preference || table.seatingPreference || ''));
  }

  function getDisplayPreference(table) {
    return String(table && (table.seating_preference || table.label || '')).trim();
  }

  function findTableById(zoneKey, tableId) {
    const zoneTables = getZoneKeyTables(zoneKey);
    return zoneTables.find(function (table) {
      return String(table.table_id) === String(tableId);
    }) || null;
  }

  function getPreferenceCapacity(zoneKey, seatingPref, tableId) {
    if (zoneKey && tableId) {
      const selected = findTableById(zoneKey, tableId);
      if (selected) {
        const selectedCap = parseInt(selected.cap, 10);
        return isNaN(selectedCap) || selectedCap < 1 ? 0 : selectedCap;
      }
    }

    const pref = normalizeText(seatingPref);
    if (!pref) return 0;
    const zoneTables = getZoneKeyTables(zoneKey).filter(function (table) {
      return normalizeText(table.seating_preference || table.label || '') === pref;
    });
    const caps = zoneTables
      .map(function (table) { return parseInt(table.cap, 10); })
      .filter(function (cap) { return !isNaN(cap) && cap > 0; });
    return caps.length ? Math.max.apply(Math, caps) : 0;
  }

  function capReservePartySize(zoneKey, seatingPref, tableId) {
    if (!reservePartySize) return;
    const maxCap = getPreferenceCapacity(zoneKey, seatingPref, tableId);
    if (!maxCap) {
      reservePartySize.removeAttribute('max');
      return;
    }
    reservePartySize.max = String(maxCap);
    const current = parseInt(reservePartySize.value, 10);
    if (!isNaN(current) && current > maxCap) {
      reservePartySize.value = String(maxCap);
    }
  }

  function getReservePartySize() {
    const raw = reservePartySize ? parseInt(reservePartySize.value, 10) : 0;
    return isNaN(raw) || raw < 1 ? 0 : raw;
  }

  function getZoneKeyTables(zoneKey) {
    return (window.ALL_TABLES && window.ALL_TABLES[zoneKey] && window.ALL_TABLES[zoneKey].tables) ? window.ALL_TABLES[zoneKey].tables : [];
  }

  function zoneHasTablesForParty(zoneKey, partySize) {
    const size = partySize || getReservePartySize() || 1;
    const zoneTables = getZoneKeyTables(zoneKey);
    return zoneTables.some(function (table) {
      return normalizeStatus(table.status) === 'available' && parseInt(table.cap, 10) >= size;
    });
  }

  function clearReserveSeating(message) {
    if (!reserveSeating) return;
    reserveSeating.innerHTML = '<option value="">' + (message || '-- Select seating preference --') + '</option>';
    reserveSeating.value = '';
  }

  function populateReserveSeatingOptions(zoneKey, preferredPref, preferredTableId) {
    if (!reserveSeating) return;

    clearReserveSeating('-- Select seating preference --');

    const size = getReservePartySize() || 1;
    const preferredNorm = normalizeText(preferredPref);
    const preferredTableNorm = normalizeText(preferredTableId);
    const zoneTables = getZoneKeyTables(zoneKey).filter(function (table) {
      const tableCap = parseInt(table.cap, 10);
      const matchesPreferred = preferredTableNorm && String(table.table_id) === String(preferredTableId);
      const matchesPref = preferredNorm && normalizeText(table.seating_preference || table.label || '') === preferredNorm;
      return tableCap >= size || matchesPreferred || matchesPref;
    });

    const seen = new Set();
    const prefs = [];
    zoneTables.forEach(function (table) {
      const pref = getDisplayPreference(table);
      const norm = normalizeText(pref);
      if (!pref || seen.has(norm)) return;
      seen.add(norm);
      prefs.push(pref);
    });

    if (!prefs.length) {
      reserveSeating.innerHTML = '<option value="">No seating preferences available</option>';
      reserveSeating.disabled = true;
      return;
    }

    reserveSeating.disabled = false;
    reserveSeating.innerHTML = '<option value="">-- Select seating preference --</option>';
    prefs.forEach(function (pref) {
      const opt = document.createElement('option');
      opt.value = pref;
      opt.textContent = pref;
      reserveSeating.appendChild(opt);
    });

    const target = normalizeText(preferredPref);
    if (target) {
      const match = Array.from(reserveSeating.options).find(function (opt) {
        return normalizeText(opt.value) === target;
      });
      if (match) {
        reserveSeating.value = match.value;
      }
    } else if (preferredPref) {
      reserveSeating.value = preferredPref;
    }

    capReservePartySize(zoneKey, reserveSeating.value || preferredPref || '', preferredTableId || '');
  }

  function updateReserveZones() {
    if (!reserveZone) return;
    const partySize = getReservePartySize();
    Array.from(reserveZone.options).forEach(function (opt) {
      if (!opt.value) return;
      const allowed = partySize > 0 && zoneHasTablesForParty(opt.value, partySize);
      opt.disabled = !allowed;
    });
    if (reserveZone.value) {
      const selectedOpt = reserveZone.options[reserveZone.selectedIndex];
      if (selectedOpt && selectedOpt.disabled) {
        reserveZone.value = '';
        clearReserveSeating('-- Select zone first --');
      }
    }
  }

  function updateReserveTimeSlots(availability, assignedTables) {
    if (!reserveTime) return;
    Array.from(reserveTime.options).forEach(function (opt) {
      if (!opt.value) return;
      const isAvailable = availability && (availability[opt.value] === true || availability[opt.value] === 1);
      opt.hidden = !isAvailable;
      opt.disabled = !isAvailable;
      opt.setAttribute('data-assigned-table', assignedTables && assignedTables[opt.value] ? assignedTables[opt.value] : '');
      if (!isAvailable && reserveTime.value === opt.value) {
        reserveTime.value = '';
      }
    });
  }

  function fetchReserveAvailability() {
    if (!reserveDate || !reserveTime || !reserveZone || !reserveZoneDbId) return;
    const dateVal = reserveDate.value || '';
    const zoneVal = reserveZone.value || '';
    const zoneDbId = reserveZoneDbId.value || '';
    const seatingVal = reserveSeating ? reserveSeating.value || '' : '';
    const partySize = getReservePartySize();
    if (!dateVal || !zoneVal || !zoneDbId || !partySize || !seatingVal) return;

    const csrfEl = document.querySelector('#floor-csrf-container');
    const csrf = csrfEl ? (csrfEl.dataset.csrf || '') : '';
    const actionToken = csrfEl ? (csrfEl.dataset.availabilityToken || '') : '';
    const body = new URLSearchParams();
    body.set('csrf_token', csrf);
    body.set('action_token', actionToken);
    body.set('appointment_date', dateVal);
    body.set('zone_id', String(zoneDbId));
    body.set('seating_preference', seatingVal);
    body.set('party_size', String(partySize));
    if (reserveTableId && reserveTableId.value) {
      body.set('table_id', String(reserveTableId.value));
    }

    fetch(actionUrl('check_availability'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: body.toString(),
    })
      .then(function (res) { return res.json(); })
      .then(function (data) {
        if (!data || !data.availability) return;
        updateReserveTimeSlots(data.availability, data.assigned_tables || {});
      })
      .catch(function () { });
  }

  function fillGuestFieldsFromOption(option) {
    if (!option) return;

    const userId = option.value;
    if (userId === 'guest') {
      guestInfoFields.style.display = 'block';
      reserveFirstName.value = '';
      reserveLastName.value = '';
      reserveEmail.value = '';
      reservePhone.value = '';
      reserveFirstName.required = true;
      reserveLastName.required = true;
      reserveEmail.required = true;
      return;
    }

    if (userId) {
      guestInfoFields.style.display = 'block';
      reserveFirstName.value = option.dataset.firstName || '';
      reserveLastName.value = option.dataset.lastName || '';
      reserveEmail.value = option.dataset.email || '';
      reservePhone.value = option.dataset.phone || '';
      reserveFirstName.required = true;
      reserveLastName.required = true;
      reserveEmail.required = true;
      return;
    }

    guestInfoFields.style.display = 'none';
  }

  function renderGuestSuggestions(query) {
    if (!reserveGuestSuggestions) return;
    const q = (query || '').trim().toLowerCase();
    const guests = window.ADMIN_GUESTS || [];

    if (!q) {
      reserveGuestSuggestions.style.display = 'none';
      reserveGuestSuggestions.innerHTML = '';
      return;
    }

    const matches = guests.filter(function (guest) {
      const first = String(guest.first_name || '').toLowerCase();
      const last = String(guest.last_name || '').toLowerCase();
      const email = String(guest.email || '').toLowerCase();
      const phone = String(guest.phone || '').toLowerCase();
      const full = (first + ' ' + last).trim();
      return full.includes(q) || email.includes(q) || phone.includes(q) || first.includes(q) || last.includes(q);
    }).slice(0, 8);

    if (!matches.length) {
      reserveGuestSuggestions.style.display = 'none';
      reserveGuestSuggestions.innerHTML = '';
      return;
    }

    reserveGuestSuggestions.innerHTML = matches.map(function (guest) {
      const name = [guest.first_name, guest.last_name].filter(Boolean).join(' ');
      const meta = [guest.email, guest.phone ? ('• ' + guest.phone) : ''].filter(Boolean).join(' ');
      return (
        '<button type="button" class="guest-suggest-item" ' +
        'data-user-id="' + (guest.user_id || '') + '" ' +
        'data-first-name="' + (guest.first_name || '') + '" ' +
        'data-last-name="' + (guest.last_name || '') + '" ' +
        'data-email="' + (guest.email || '') + '" ' +
        'data-phone="' + (guest.phone || '') + '">' +
        '<span class="guest-suggest-name">' + name + '</span>' +
        '<span class="guest-suggest-meta">' + meta + '</span>' +
        '</button>'
      );
    }).join('');
    reserveGuestSuggestions.style.display = 'block';

    reserveGuestSuggestions.querySelectorAll('.guest-suggest-item').forEach(function (item) {
      item.addEventListener('click', function () {
        if (reserveUserId) reserveUserId.value = item.getAttribute('data-user-id') || '';
        fillGuestFieldsFromOption({
          value: item.getAttribute('data-user-id') || '',
          dataset: {
            firstName: item.getAttribute('data-first-name') || '',
            lastName: item.getAttribute('data-last-name') || '',
            email: item.getAttribute('data-email') || '',
            phone: item.getAttribute('data-phone') || '',
          }
        });
        reserveGuestSearch.value = [item.getAttribute('data-first-name'), item.getAttribute('data-last-name')].filter(Boolean).join(' ');
        reserveGuestSuggestions.style.display = 'none';
      });
    });
  }

  if (reserveUserId && guestInfoFields) {
    reserveUserId.addEventListener('change', function () {
      const selected = reserveUserId.options[reserveUserId.selectedIndex];
      fillGuestFieldsFromOption(selected);
    });
  }

  if (reserveGuestSearch && reserveUserId) {
    reserveGuestSearch.addEventListener('input', function () {
      renderGuestSuggestions(reserveGuestSearch.value);
    });
    reserveGuestSearch.addEventListener('focus', function () {
      renderGuestSuggestions(reserveGuestSearch.value);
    });
  }

  if (reserveZone) {
    reserveZone.addEventListener('change', function () {
      const zoneKey = reserveZone.value;
      if (reserveZoneDbId) {
        const zoneMeta = window.ALL_TABLES && window.ALL_TABLES[zoneKey] ? window.ALL_TABLES[zoneKey] : null;
        reserveZoneDbId.value = zoneMeta && zoneMeta.zone_id ? String(zoneMeta.zone_id) : '';
      }
      if (reserveTableId) reserveTableId.value = '';
      populateReserveSeatingOptions(zoneKey, '', '');
      fetchReserveAvailability();
    });
  }

  if (reserveService && reservePackage) {
    reserveService.addEventListener('change', function () {
      if (reserveService.value) {
        reservePackage.value = '';
      }
    });

    reservePackage.addEventListener('change', function () {
      if (reservePackage.value) {
        reserveService.value = '';
      }
    });
  }

  if (reservePartySize) {
    reservePartySize.addEventListener('input', function () {
      updateReserveZones();
      const zoneKey = reserveZone ? reserveZone.value : '';
      const currentPref = reserveSeating ? reserveSeating.value : '';
      const currentTableId = reserveTableId ? reserveTableId.value : '';
      if (currentPref) {
        capReservePartySize(zoneKey, currentPref, currentTableId);
      } else {
        populateReserveSeatingOptions(zoneKey, currentPref, currentTableId);
      }
      fetchReserveAvailability();
    });
    updateReserveZones();
  }

  if (reserveSeating) {
    reserveSeating.addEventListener('change', function () {
      const zoneKey = reserveZone ? reserveZone.value : '';
      if (reserveTableId) reserveTableId.value = '';
      capReservePartySize(zoneKey, reserveSeating.value, '');
      fetchReserveAvailability();
    });
  }

  if (reserveDate) {
    reserveDate.addEventListener('change', function () {
      fetchReserveAvailability();
    });
  }

  function initAdminAddonControls() {
    document.querySelectorAll('.admin-addon-item').forEach(function (item) {
      const cb = item.querySelector('input[type="checkbox"]');
      const qty = item.querySelector('input[type="number"]');
      if (!cb || !qty) return;

      function syncQty() {
        qty.style.display = cb.checked ? 'block' : 'none';
        if (!cb.checked) {
          qty.value = 1;
        }
      }

      cb.addEventListener('change', syncQty);
      syncQty();
    });
  }
  initAdminAddonControls();

  function openReserveModalFromButton(btn) {
    if (!btn) return;
    if (isPastFloorView) {
      if (window.alert) window.alert(pastFloorReserveMessage());
      return;
    }

    const tableId = btn.getAttribute('data-table-id') || '';
    const zoneKey = btn.getAttribute('data-zone-key') || '';
    const seatingPref = btn.getAttribute('data-seating-preference') || '';
    const modal = document.getElementById('reserveTableModal');

    if (reserveZone && zoneKey) {
      reserveZone.value = zoneKey;
    }
    if (reserveZoneDbId) {
      const zoneMeta = window.ALL_TABLES && window.ALL_TABLES[zoneKey] ? window.ALL_TABLES[zoneKey] : null;
      reserveZoneDbId.value = zoneMeta && zoneMeta.zone_id ? String(zoneMeta.zone_id) : '';
    }
    if (reserveTableId) {
      reserveTableId.value = tableId;
    }
    if (reserveSeating) {
      reserveSeating.value = seatingPref;
    }
    updateReserveZones();
    populateReserveSeatingOptions(zoneKey, seatingPref, tableId);
    capReservePartySize(zoneKey, seatingPref, tableId);
    if (modal) {
      modal.classList.add('open');
    }
    fetchReserveAvailability();
  }

  window.adminOpenReserveTable = function (btn) {
    if (isPastFloorView) {
      if (window.alert) window.alert(pastFloorReserveMessage());
      return false;
    }
    openReserveModalFromButton(btn);
    return false;
  };

  document.addEventListener('click', function (e) {
    const reserveBtn = e.target.closest('[data-reserve-table]');
    if (!reserveBtn) return;
    e.preventDefault();
    e.stopImmediatePropagation();
    e.stopPropagation();
    openReserveModalFromButton(reserveBtn);
  });

  document.querySelectorAll('[data-reserve-table]').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      openReserveModalFromButton(btn);
    });
  });

  const reserveTableForm = document.getElementById('reserveTableForm');
  if (reserveTableForm) {
    reserveTableForm.addEventListener('submit', function (e) {
      e.preventDefault();

      const formData = new FormData(reserveTableForm);
      const modal = document.getElementById('reserveTableModal');
      const submitBtn = reserveTableForm.querySelector('button[type="submit"]');

      if (submitBtn) submitBtn.disabled = true;

      fetch(reserveTableForm.getAttribute('action') || actionUrl('admin_reserve_table'), {
        method: 'POST',
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json'
        },
        body: formData
      })
        .then(function (res) {
          return res.json().then(function (data) {
            return { ok: res.ok, data: data };
          }).catch(function () {
            return { ok: res.ok, data: null };
          });
        })
        .then(function (result) {
          if (result && result.data && result.data.ok) {
            const createdTableId = result && result.data ? String(result.data.table_id || '') : '';
            if (createdTableId) markTileReserved(createdTableId);
            if (modal) modal.classList.remove('open');
            resetReserveModalFields();
            reserveTableForm.reset();
            if (window.location && typeof window.location.reload === 'function') {
              setTimeout(function () {
                window.location.reload();
              }, 300);
            }
            return;
          }

          const message = result && result.data && result.data.error ? result.data.error : 'Could not create the reservation. Please try again.';
          if (window.alert) window.alert(message);
        })
        .catch(function () {
          if (window.alert) window.alert('Could not create the reservation. Please try again.');
        })
        .finally(function () {
          if (submitBtn) submitBtn.disabled = false;
        });
    });
  }

  document.addEventListener('click', function (e) {
    if (!reserveGuestSuggestions || !reserveGuestSearch) return;
    if (e.target === reserveGuestSearch || reserveGuestSuggestions.contains(e.target)) return;
    reserveGuestSuggestions.style.display = 'none';
  });

  /* ------------------------------------------------------------------
     Admin Reservations Table Search (Guest, Zone, ID)
  ------------------------------------------------------------------ */
  const resSearchInput = document.getElementById('resSearch');
  if (resSearchInput) {
    resSearchInput.addEventListener('input', function () {
      const query = resSearchInput.value.trim().toLowerCase();
      const tables = document.querySelectorAll('table.resTable');

      tables.forEach(function (table) {
        const rows = table.querySelectorAll('tbody tr');
        let visibleCount = 0;

        rows.forEach(function (row) {
          // Skip the "no reservations" row
          if (row.querySelector('td[colspan]')) {
            if (query) row.style.display = 'none';
            return;
          }

          const cells = row.cells;
          if (!cells || cells.length < 9) return;

          // Column indexes: 0=ID, 1=Guest(name+email), 2=Zone, 3=Seating, 4=Date, 5=Time, 6=Guests, 7=Status, 8=Actions
          const idText = cells[0] ? cells[0].textContent.toLowerCase() : '';
          const guestText = cells[1] ? cells[1].textContent.toLowerCase() : '';
          const zoneText = cells[2] ? cells[2].textContent.toLowerCase() : '';
          const seatingText = cells[3] ? cells[3].textContent.toLowerCase() : '';

          const match = !query ||
            idText.includes(query) ||
            guestText.includes(query) ||
            zoneText.includes(query) ||
            seatingText.includes(query);

          row.style.display = match ? '' : 'none';
          if (match) visibleCount++;
        });

        // Show "no results" message if all rows are hidden
        const tbody = table.querySelector('tbody');
        const existingNoResults = tbody.querySelector('.no-search-results');
        if (existingNoResults) existingNoResults.remove();

        if (query && visibleCount === 0) {
          const noResultsRow = document.createElement('tr');
          noResultsRow.className = 'no-search-results';
          noResultsRow.innerHTML = '<td colspan="9" style="text-align:center;color:var(--clr-muted-fg);padding:var(--space-8);">No reservations match your search.</td>';
          tbody.appendChild(noResultsRow);
        }
      });

      // Update tab counts based on visible rows
      document.querySelectorAll('.admin-tab').forEach(function (tab) {
        const statusKey = tab.getAttribute('data-tab');
        const panel = document.querySelector('.admin-panel[data-panel="' + statusKey + '"]');
        if (panel) {
          const visibleRows = panel.querySelectorAll('tbody tr:not([style*="display: none"]):not(.no-search-results)');
          const countSpan = tab.querySelector('.admin-tab-count');
          if (countSpan) {
            countSpan.textContent = '(' + visibleRows.length + ')';
          }
        }
      });
    });
  }

})();
