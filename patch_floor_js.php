<?php
$f = 'js/admin.js';
$c = file_get_contents($f);

// 1. Fix the text labels from Walk-in / VIP to Available / Occupied
$c = str_replace(
    "lbl.textContent = next === 'available' ? 'Walk-in' : (next === 'reserved' ? '6:30 PM' : 'VIP');",
    "lbl.textContent = next === 'available' ? 'Available' : (next === 'reserved' ? 'Reserved' : 'Occupied');",
    $c
);

// 2. Insert the fetch logic into the two locations where data-status is set
$fetch_code = <<<EOT
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
EOT;

$c = preg_replace("/tile\.setAttribute\('data-status',\s*next\);/ius", $fetch_code, $c);

file_put_contents($f, $c);
echo "admin.js padded and labeled.";
