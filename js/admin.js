(function () {
  'use strict';

  /* ═══════════════════════════════════════════════════════
     Custom Confirm Dialog — replaces window.confirm()
     Injected into the DOM on load, reused for every action.
  ═══════════════════════════════════════════════════════ */

  const CONFIRM_HTML = `
<div class="ac-backdrop" id="adminConfirmBackdrop" role="dialog" aria-modal="true" aria-labelledby="acTitle">
  <div class="ac-card">
    <h2 class="ac-title" id="acTitle">Confirm Action</h2>
    <p  class="ac-message" id="acMessage"></p>
    <div class="ac-actions">
      <button class="ac-btn ac-btn-cancel" id="acCancel">Cancel</button>
      <button class="ac-btn ac-btn-confirm" id="acConfirm">Confirm</button>
    </div>
  </div>
</div>`;

  // Inject once
  const wrapper = document.createElement('div');
  wrapper.innerHTML = CONFIRM_HTML;
  document.body.appendChild(wrapper.firstElementChild);

  const backdrop  = document.getElementById('adminConfirmBackdrop');
  const acTitle   = document.getElementById('acTitle');
  const acMessage = document.getElementById('acMessage');
  const acConfirm = document.getElementById('acConfirm');
  const acCancel  = document.getElementById('acCancel');

  let _resolve = null;

  function adminConfirm(opts) {
    /* opts: { title, message, confirmLabel, action } */
    acTitle.textContent   = opts.title   || 'Confirm Action';
    acMessage.textContent = opts.message || 'Are you sure?';
    acConfirm.textContent = opts.confirmLabel || 'Confirm';

    // Colour the button based on action type
    const isDanger = (opts.action === 'reject' || opts.action === 'cancel');
    acConfirm.className   = 'ac-btn '      + (isDanger ? 'ac-btn-danger'  : 'ac-btn-confirm');

    backdrop.classList.add('ac-open');
    acCancel.focus();

    return new Promise(function (resolve) { _resolve = resolve; });
  }

  function closeConfirm(result) {
    backdrop.classList.remove('ac-open');
    if (_resolve) { _resolve(result); _resolve = null; }
  }

  acConfirm.addEventListener('click', function () { closeConfirm(true);  });
  acCancel .addEventListener('click', function () { closeConfirm(false); });
  backdrop .addEventListener('click', function (e) { if (e.target === backdrop) closeConfirm(false); });

  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && backdrop.classList.contains('ac-open')) closeConfirm(false);
  });

  /* ═══════════════════════════════════════════════════════
     Mobile Sidebar Toggle
  ═══════════════════════════════════════════════════════ */
  const sidebar   = document.getElementById('adminSidebar');
  const toggleBtn = document.getElementById('adminSidebarToggle');
  const overlay   = document.getElementById('adminSidebarOverlay');

  function openSidebar()  { sidebar && sidebar.classList.add('open'); overlay && overlay.classList.add('visible'); }
  function closeSidebar() { sidebar && sidebar.classList.remove('open'); overlay && overlay.classList.remove('visible'); }

  toggleBtn && toggleBtn.addEventListener('click', openSidebar);
  overlay   && overlay.addEventListener('click', closeSidebar);

  /* ═══════════════════════════════════════════════════════
     Tab Switching
  ═══════════════════════════════════════════════════════ */
  document.querySelectorAll('[data-admin-tabs]').forEach(function (container) {
    const tabs   = container.querySelectorAll('[data-tab]');
    // Panels might be siblings, so search within parent
    const panels = container.parentElement.querySelectorAll('[data-panel]');

    tabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        const target = tab.getAttribute('data-tab');
        tabs.forEach(function (t) { t.classList.remove('active'); t.setAttribute('aria-selected', 'false'); });
        tab.classList.add('active');
        tab.setAttribute('aria-selected', 'true');
        panels.forEach(function (panel) {
          panel.classList.toggle('active', panel.getAttribute('data-panel') === target);
        });
      });
    });
  });

  /* ═══════════════════════════════════════════════════════
     Live Search Filter
  ═══════════════════════════════════════════════════════ */
  document.querySelectorAll('[data-search-input]').forEach(function (input) {
    const targetQuery = input.getAttribute('data-search-input');
    const tables = document.querySelectorAll('#' + targetQuery + ', .' + targetQuery);
    if (tables.length === 0) return;
    
    input.addEventListener('input', function () {
      const q = input.value.toLowerCase().trim();
      tables.forEach(table => {
        table.querySelectorAll('tbody tr').forEach(function (row) {
          row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
      });
    });
  });

  /* ═══════════════════════════════════════════════════════
     Modal Open / Close
  ═══════════════════════════════════════════════════════ */
  document.querySelectorAll('[data-modal-open]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const modal = document.getElementById(btn.getAttribute('data-modal-open'));
      modal && modal.classList.add('open');
    });
  });

  document.querySelectorAll('[data-modal-close]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const m = btn.closest('.admin-modal');
      m && m.classList.remove('open');
    });
  });

  document.querySelectorAll('.admin-modal').forEach(function (modal) {
    modal.addEventListener('click', function (e) {
      if (e.target === modal) modal.classList.remove('open');
    });
  });

  /* ═══════════════════════════════════════════════════════
     Approve / Reject — uses custom confirm dialog
  ═══════════════════════════════════════════════════════ */
  document.querySelectorAll('[data-confirm-action]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const action = btn.getAttribute('data-confirm-action');
      const row    = btn.closest('tr');
      const name   = row
        ? (row.querySelector('.admin-guest-name') || row.cells[1]).textContent.trim()
        : 'this reservation';

      const isApprove = action === 'approve';
      const isReject  = action === 'reject' || action === 'cancel';

      adminConfirm({
        title:        isApprove ? 'Approve Reservation'  : 'Cancel Reservation',
        message:      isApprove
          ? 'Confirm the reservation for ' + name + '? They will be notified by email.'
          : 'Reject the reservation for ' + name + '? This action cannot be undone.',
        confirmLabel: isApprove ? 'Yes, Approve' : 'Yes, Reject',
        action:       action,
      }).then(function (confirmed) {
        if (!confirmed) return;

        const id = row.cells[0].textContent.trim();
        const targetState = isApprove ? 'confirmed' : 'cancelled';

        document.querySelectorAll('.admin-table tbody tr').forEach(function (r) {
          if (r.cells[0] && r.cells[0].textContent.trim() === id) {
            
            // 1. Update Badge
            const badge = r.querySelector('.badge');
            if (badge) {
              badge.className = 'badge badge-' + targetState;
              badge.textContent = targetState.charAt(0).toUpperCase() + targetState.slice(1);
            }

            // 2. Remove old action buttons
            r.querySelectorAll('[data-confirm-action]').forEach(function (b) {
              b.remove(); // Remove buttons so they can't be clicked again
            });

            // 3. Move row to the correct panel if needed (exclude the "All" panel row)
            const panel = r.closest('[data-panel]');
            if (panel) {
              const panelType = panel.getAttribute('data-panel');
              if (panelType !== 'all' && panelType !== targetState) {
                  const destTbody = document.querySelector('[data-panel="' + targetState + '"] tbody');
                  if (destTbody) destTbody.appendChild(r);
              }
            }
          }
        });

        // 4. Update the tab counters
        ['pending', 'confirmed', 'cancelled'].forEach(function (key) {
          const tabCount = document.querySelector('[data-tab="' + key + '"] .admin-tab-count');
          const tbody = document.querySelector('[data-panel="' + key + '"] tbody');
          if (tabCount && tbody) {
             let validRows = 0;
             tbody.querySelectorAll('tr').forEach(function (tr) {
                 if (!tr.querySelector('td[colspan]')) validRows++;
             });
             tabCount.textContent = '(' + validRows + ')';
          }
        });
      });
    });
  });

  /* ═══════════════════════════════════════════════════════
     Floor Tile Status Toggle
  ═══════════════════════════════════════════════════════ */
  const STATUS_CYCLE = ['available', 'reserved', 'occupied'];
  document.querySelectorAll('.floor-tile-new[data-status]').forEach(function (tile) {
    tile.addEventListener('click', function () {
      const current = tile.getAttribute('data-status');
      const next    = STATUS_CYCLE[(STATUS_CYCLE.indexOf(current) + 1) % STATUS_CYCLE.length];
      
      STATUS_CYCLE.forEach(function (s) { tile.classList.remove('floor-tile-new--' + s); });
      tile.classList.add('floor-tile-new--' + next);
      tile.setAttribute('data-status', next);
      
      const lbl = tile.querySelector('.floor-tile-new-status');
      if (lbl) {
         lbl.textContent = next === 'available' ? 'Walk-in' : (next === 'reserved' ? '6:30 PM' : 'VIP');
      }

      // Update the panel's top summary stats live!
      const panel = tile.closest('.admin-panel');
      if (panel) {
        let avail = 0, res = 0, occ = 0;
        panel.querySelectorAll('.floor-tile-new').forEach(function(t) {
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
    });
  });

  /* ═══════════════════════════════════════════════════════
     Pre-fill Reservation Detail Modal from table row
  ═══════════════════════════════════════════════════════ */
  document.querySelectorAll('[data-open-detail]').forEach(function (btn) {
    btn.addEventListener('click', function () {
      const modal = document.getElementById('reservationDetailModal');
      if (!modal) return;
      const row = btn.closest('tr');
      if (!row) { modal.classList.add('open'); return; }

      const cells = row.cells;
      const set   = function (id, val) { const el = modal.querySelector('#detail' + id); if (el) el.textContent = val; };

      set('Id',      cells[0] ? cells[0].textContent.trim() : '—');
      set('Guest',   cells[1] ? cells[1].textContent.trim() : '—');
      set('Zone',    cells[2] ? cells[2].textContent.trim() : '—');
      set('Seating', cells[3] ? cells[3].textContent.trim() : '—');
      set('Date',    cells[4] ? cells[4].textContent.trim() : '—');
      set('Time',    cells[5] ? cells[5].textContent.trim() : '—');
      set('Guests',  cells[6] ? cells[6].textContent.trim() : '—');

      const badgeEl  = row.querySelector('.badge');
      const statusEl = modal.querySelector('#detailStatus');
      if (statusEl && badgeEl) {
        statusEl.className   = badgeEl.className;
        statusEl.textContent = badgeEl.textContent;
      }

      modal.classList.add('open');
    });
  });

  /* ═══════════════════════════════════════════════════════
     Guest Profile Modal Dynamic Population & Edit
  ═══════════════════════════════════════════════════════ */
  document.querySelectorAll('.view-profile-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
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
      if(editBlock && viewBlock) {
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
    
    editStatusBtn.addEventListener('click', function() {
      viewMode.style.display = 'none';
      editMode.style.display = 'flex';
    });
    
    cancelStatusBtn.addEventListener('click', function() {
      editMode.style.display = 'none';
      viewMode.style.display = 'flex';
      statusSelect.value = modalStatus.textContent.trim();
    });
    
    saveStatusBtn.addEventListener('click', function() {
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

})();
