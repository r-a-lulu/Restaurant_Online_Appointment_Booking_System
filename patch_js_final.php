<?php
$f = 'js/admin.js';
$c = file_get_contents($f);

$missing_logic = <<<EOT

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
      
      const tableId = tile.getAttribute('data-table-id');
      const csrfContainer = document.getElementById('floor-csrf-container');
      if (tableId && csrfContainer) {
          const fd = new URLSearchParams();
          fd.append('table_id', tableId);
          fd.append('status', next);
          fd.append('csrf_token', csrfContainer.getAttribute('data-csrf') || '');
          fd.append('action_token', csrfContainer.getAttribute('data-action-token') || '');
          fetch('../../actions.php?action=admin_floor_update', {
              method: 'POST',
              body: fd,
              headers: { 'Content-Type': 'application/x-www-form-urlencoded' }
          }).catch(console.error);
      }

      const lbl = tile.querySelector('.floor-tile-new-status');
      if (lbl) {
         lbl.textContent = next === 'available' ? 'Available' : (next === 'reserved' ? 'Reserved' : 'Occupied');
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

EOT;

// Insert before the Add New Table Flow
$c = str_replace(
    "/* ------------------------------------------------------------------\n     Add New Table Flow",
    "/* ═══════════════════════════════════════════════════════\n     Floor Tile Status Toggle\n  ═══════════════════════════════════════════════════════ */\n".$missing_logic."\n  /* ------------------------------------------------------------------\n     Add New Table Flow",
    $c
);

// Also substitute ════... if it used those before Add New Table
$c = str_replace(
    "/* ═══════════════════════════════════════════════════════\n     Add New Table Flow",
    $missing_logic."\n  /* ═══════════════════════════════════════════════════════\n     Add New Table Flow",
    $c
);

file_put_contents($f, $c);
echo "Restored JS logic.";
