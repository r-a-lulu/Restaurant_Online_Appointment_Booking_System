<?php
$floor_file = 'pages/admin/floor.php';
$js_file = 'js/admin.js';

// Patch floor.php
$content = file_get_contents($floor_file);

// 1. Add current_status to query
$content = str_replace(
    'SELECT t.table_id, t.table_number, t.capacity, dz.zone_id, dz.zone_name,',
    'SELECT t.table_id, t.table_number, t.capacity, t.current_status, dz.zone_id, dz.zone_name,',
    $content
);

// 2. Override status logic
$search_logic = <<<EOT
    if ((int) \$t['status_rank'] === 2) {
      \$status = 'occupied';
    } elseif ((int) \$t['status_rank'] === 1) {
      \$status = 'reserved';
    }
EOT;

$replace_logic = <<<EOT
    if ((int) \$t['status_rank'] === 2) {
      \$status = 'occupied';
    } elseif ((int) \$t['status_rank'] === 1) {
      \$status = 'reserved';
    }
    if (!empty(\$t['current_status']) && \$t['current_status'] !== 'available') {
        \$status = \$t['current_status'];
    }
EOT;
$content = str_replace($search_logic, $replace_logic, $content);

// 3. Add table_id to $tablesByZone array
$search_array = <<<EOT
      'status' => \$status,
      'label' => \$t['table_number'],
EOT;

$replace_array = <<<EOT
      'status' => \$status,
      'label' => \$t['table_number'],
      'table_id' => \$t['table_id'],
EOT;
$content = str_replace($search_array, $replace_array, $content);

// 4. Add data-table-id and CSRF token block to HTML
$content = str_replace(
    'data-status="<?= e($t[\'status\']) ?>" tabindex="0">',
    'data-status="<?= e($t[\'status\']) ?>" data-table-id="<?= e($t[\'table_id\']) ?>" tabindex="0">',
    $content
);

// Add CSRF token wrapper for JS
$content = str_replace(
    '</header>',
    '</header><div id="floor-csrf-container" data-csrf="'.htmlspecialchars('<?= e(csrf_token()) ?>').'" data-action-token="'.htmlspecialchars('<?= e(action_token(\'admin_floor_update\')) ?>').'" style="display:none"></div>',
    $content
);

file_put_contents($floor_file, $content);
echo "floor.php patched.\n";

// Patch admin.js
$js_content = file_get_contents($js_file);
$js_search = <<<EOT
      tile.setAttribute('data-status', next);
      
      const lbl = tile.querySelector('.floor-tile-new-status');
EOT;

$js_replace = <<<EOT
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
          });
      }

      const lbl = tile.querySelector('.floor-tile-new-status');
EOT;

$js_content = str_replace($js_search, $js_replace, $js_content);
file_put_contents($js_file, $js_content);
echo "admin.js patched.\n";
