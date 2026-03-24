<?php
/**
 * Admin Floor Management — pages/admin/floor.php
 */

$pageTitle        = 'Floor Management — Admin';
$pageCSS = ['dashboard.css', 'admin.css'];
$currentAdminPage = 'floor';
$basePath         = '../../';

include '../../includes/header.php';

$zones = [
  'patio' => [
    'label'  => 'The Patio',
    'tables' => [
      ['no'=>'P1','cap'=>2,'status'=>'available','label'=>'Garden View'],
      ['no'=>'P2','cap'=>4,'status'=>'reserved', 'label'=>'Fountain Side'],
      ['no'=>'P3','cap'=>6,'status'=>'occupied',  'label'=>'Pergola'],
      ['no'=>'P4','cap'=>4,'status'=>'available','label'=>'Corner Alcove'],
      ['no'=>'P5','cap'=>8,'status'=>'reserved', 'label'=>'Olive Grove'],
      ['no'=>'P6','cap'=>2,'status'=>'available','label'=>'Garden View 2'],
      ['no'=>'P7','cap'=>4,'status'=>'occupied',  'label'=>'Fountain 2'],
      ['no'=>'P8','cap'=>6,'status'=>'available','label'=>'Pergola 2'],
    ],
  ],
  'dining-room' => [
    'label'  => 'Main Dining Room',
    'tables' => [
      ['no'=>'D1','cap'=>2,'status'=>'occupied',  'label'=>"Chef's View"],
      ['no'=>'D2','cap'=>4,'status'=>'reserved', 'label'=>'Window Table'],
      ['no'=>'D3','cap'=>6,'status'=>'available','label'=>'Banquette'],
      ['no'=>'D4','cap'=>4,'status'=>'occupied',  'label'=>'Fireplace'],
      ['no'=>'D5','cap'=>8,'status'=>'available','label'=>'Private Alcove'],
      ['no'=>'D6','cap'=>4,'status'=>'reserved', 'label'=>'Chandelier'],
      ['no'=>'D7','cap'=>2,'status'=>'available','label'=>'Corner Table'],
      ['no'=>'D8','cap'=>4,'status'=>'available','label'=>'Centre Table'],
      ['no'=>'D9','cap'=>6,'status'=>'occupied',  'label'=>'Arch Booth'],
      ['no'=>'D10','cap'=>4,'status'=>'reserved','label'=>'South Window'],
    ],
  ],
  'bar' => [
    'label'  => 'The Bar',
    'tables' => [
      ['no'=>'B1','cap'=>8,'status'=>'occupied',  'label'=>'Bar Counter'],
      ['no'=>'B2','cap'=>4,'status'=>'reserved', 'label'=>'Lounge Booths'],
      ['no'=>'B3','cap'=>4,'status'=>'available','label'=>'High Tops'],
      ['no'=>'B4','cap'=>6,'status'=>'available','label'=>'Corner Sofa'],
      ['no'=>'B5','cap'=>2,'status'=>'occupied',  'label'=>'Bar Nook'],
      ['no'=>'B6','cap'=>4,'status'=>'reserved', 'label'=>'Side Booth'],
    ],
  ],
];
?>
<body>
<div class="admin-layout" id="adminLayout">

  <?php include '../../includes/admin-sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-content">

      <header class="admin-header">
        <div class="admin-header-row" style="display:flex; justify-content:space-between; align-items:center;">
          <div>
            <h1 class="admin-page-title">Floor Management</h1>
            <p class="admin-page-subtitle">Manage tables and dining zones</p>
          </div>
          <button class="btn btn-primary" data-modal-open="addTableModal" style="display:flex; align-items:center; gap:var(--space-2);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Table
          </button>
        </div>
      </header>

      <div class="admin-floor-wrap" style="margin-top:var(--space-6); overflow:visible;">

        <!-- Segmented Tabs -->
        <div class="segmented-tabs" data-admin-tabs>
          <?php $first = true; foreach ($zones as $key => $zone): ?>
          <button class="segment-btn <?= $first ? 'active' : '' ?>" data-tab="<?= $key ?>" aria-selected="<?= $first ? 'true' : 'false' ?>">
            <?= $zone['label'] ?>
          </button>
          <?php $first = false; endforeach; ?>
        </div>

        <!-- Floor Panels -->
        <?php $first = true; foreach ($zones as $key => $zone): 
          $avail = 0; $reserved = 0; $occupied = 0;
          foreach ($zone['tables'] as $t) {
             if ($t['status'] === 'available') $avail++;
             elseif ($t['status'] === 'reserved') $reserved++;
             elseif ($t['status'] === 'occupied') $occupied++;
          }
        ?>
        <div class="admin-panel <?= $first ? 'active' : '' ?>" data-panel="<?= $key ?>">
          
          <!-- Summary Cards Row -->
          <div class="floor-stats-grid">
            <div class="floor-stat-card">
              <div class="floor-stat-val text-green"><?= $avail ?></div>
              <div class="floor-stat-lbl">Available</div>
            </div>
            <div class="floor-stat-card">
              <div class="floor-stat-val text-yellow"><?= $reserved ?></div>
              <div class="floor-stat-lbl">Reserved</div>
            </div>
            <div class="floor-stat-card">
              <div class="floor-stat-val text-red"><?= $occupied ?></div>
              <div class="floor-stat-lbl">Occupied</div>
            </div>
          </div>

          <!-- Tables Grid Card -->
          <div class="admin-card" style="margin-top: var(--space-6);">
            <div class="admin-card-header">
              <h2 class="admin-card-title"><?= $zone['label'] ?> Tables</h2>
            </div>
            <div class="admin-card-body">
              <div class="floor-grid-new">
                <?php foreach ($zone['tables'] as $t): ?>
                <div class="floor-tile-new floor-tile-new--<?= $t['status'] ?>" data-status="<?= $t['status'] ?>" tabindex="0">
                  <div class="floor-tile-new-top">
                    <span class="floor-tile-new-number"><?= $t['label'] ?></span>
                    <svg class="floor-tile-new-edit" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                  </div>
                  <div class="floor-tile-new-capacity">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <?= $t['cap'] ?> seats
                  </div>
                  <div class="floor-tile-new-status"><?= $t['status'] === 'available' ? 'Walk-in' : ($t['status'] === 'reserved' ? '6:30 PM' : 'VIP') ?></div>
                </div>
                <?php endforeach; ?>
              </div>

              <!-- Legend -->
              <div class="floor-legend-new">
                <div class="legend-item"><span class="legend-box available"></span> Available</div>
                <div class="legend-item"><span class="legend-box reserved"></span> Reserved</div>
                <div class="legend-item"><span class="legend-box occupied"></span> Occupied</div>
              </div>
            </div>
          </div> <!-- /.admin-card -->

        </div>
        <?php $first = false; endforeach; ?>
      </div>

    </div>
  </main>

</div>

<!-- Add Table Modal -->
<div class="admin-modal" id="addTableModal">
  <div class="admin-modal-card" style="max-width:28rem;">
    <div class="admin-modal-header" style="align-items:flex-start;">
      <div>
        <h2 class="admin-modal-title" style="margin-bottom:var(--space-1);">Add New Table</h2>
        <p class="admin-modal-subtitle" style="color:var(--clr-muted-fg);font-size:var(--text-sm);">Add a new table to the selected zone</p>
      </div>
      <button class="admin-modal-close" data-modal-close aria-label="Close">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <form class="admin-form" id="addTableForm">
      <div class="form-group" style="margin-bottom:var(--space-4);">
        <label class="form-label">Zone</label>
        <div style="position:relative; width:max-content; min-width:8rem;">
          <select class="form-select" id="newTableZone" style="background-color:var(--clr-bg);" required>
            <option value="bar" selected>The Bar</option>
            <option value="patio">The Patio</option>
            <option value="dining-room">Main Dining Room</option>
          </select>
        </div>
      </div>
      <div class="form-group" style="margin-bottom:var(--space-4);">
        <label class="form-label">Table Name</label>
        <input type="text" class="form-input" id="newTableName" placeholder="e.g., Table 11" required>
      </div>
      <div class="form-group" style="margin-bottom:var(--space-6);">
        <label class="form-label">Seats</label>
        <input type="number" class="form-input" id="newTableSeats" min="1" max="50" placeholder="e.g., 4" required style="width: 8rem;">
      </div>
      <div class="admin-modal-footer" style="padding-top:0; border-top:none; display:flex; gap:var(--space-3); justify-content:flex-end;">
        <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
        <button type="submit" class="btn btn-primary">Add Table</button>
      </div>
    </form>
  </div>
</div>

<script src="<?= $basePath ?>js/admin.js"></script>
</body>
</html>
