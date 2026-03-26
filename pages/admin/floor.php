<?php
/**
 * Admin Floor Management — pages/admin/floor.php
 */
require_once '../../includes/security.php';
start_secure_session();
require_admin();

$pageTitle        = 'Floor Management — Admin';
$pageCSS = ['dashboard.css', 'admin.css'];
$currentAdminPage = 'floor';
$basePath         = '../../';

$adminError = get_flash('admin_error');
$adminSuccess = get_flash('admin_success');
$zones = [];
$users = [];
$services = [];
$packages = [];
$addOns = [];
$selectedFloorDate = trim((string) ($_GET['floor_date'] ?? date('Y-m-d')));
$floorDateObj = DateTime::createFromFormat('Y-m-d', $selectedFloorDate);
$floorDateErrors = DateTime::getLastErrors();
if (!$floorDateObj || (is_array($floorDateErrors) && (($floorDateErrors['warning_count'] ?? 0) > 0 || ($floorDateErrors['error_count'] ?? 0) > 0))) {
  $selectedFloorDate = date('Y-m-d');
}
$todayDate = date('Y-m-d');
$isViewingToday = ($selectedFloorDate === $todayDate);
$isPastFloorDate = ($selectedFloorDate < $todayDate);
$reservationDurationLabel = reservation_duration_label();

try {
  $pdo = db();
  
  // Fetch the same guest/customer set shown in the admin guest directory
  $stmt = $pdo->prepare("SELECT u.user_id, u.first_name, u.last_name, u.email, u.phone, u.is_active, u.created_at, COUNT(a.appointment_id) AS total_res
    FROM users u
    JOIN roles r ON r.role_id = u.role_id
    LEFT JOIN appointments a ON a.user_id = u.user_id
    WHERE r.role_name IN ('guest','customer')
    GROUP BY u.user_id
    ORDER BY u.created_at DESC");
  $stmt->execute();
  $users = $stmt->fetchAll() ?: [];
  $stmt->closeCursor();
  
  $stmt = $pdo->prepare('SELECT zone_id, zone_name FROM dining_zones ORDER BY zone_name');
  $stmt->execute();
  $zoneRows = $stmt->fetchAll() ?: [];
  $stmt->closeCursor();

  $stmt = $pdo->prepare('SELECT service_id, service_name, price FROM vw_active_services');
  $stmt->execute();
  $services = $stmt->fetchAll() ?: [];
  $stmt->closeCursor();

  $stmt = $pdo->prepare('SELECT package_id, package_name, base_price, description FROM vw_active_event_packages');
  $stmt->execute();
  $packages = $stmt->fetchAll() ?: [];
  $stmt->closeCursor();

  $stmt = $pdo->prepare('SELECT add_on_id, category, name, description, price FROM vw_active_add_ons');
  $stmt->execute();
  $addOns = $stmt->fetchAll() ?: [];
  $stmt->closeCursor();

  $stmt = $pdo->prepare("SELECT t.table_id, t.capacity, t.seating_preference, t.current_status, t.manual_status_until, dz.zone_id, dz.zone_name,
    -- Check if there's a confirmed appointment happening right now on the selected date
    EXISTS(
      SELECT 1 FROM appointments a
      JOIN appointment_status s ON s.status_id = a.status_id
      WHERE a.table_id = t.table_id
      AND a.appointment_date = :floor_date
      AND :is_today = 1
      AND a.start_time <= CURTIME() 
      AND a.end_time > CURTIME()
      AND s.status_name = 'confirmed'
    ) AS is_active_now,
    -- Check if table has any reservation on the selected date
    EXISTS(
      SELECT 1 FROM appointments a2
      JOIN appointment_status s2 ON s2.status_id = a2.status_id
      WHERE a2.table_id = t.table_id
      AND a2.appointment_date = :floor_date_reserved
      AND s2.status_name IN ('pending','confirmed')
      AND (:is_today_reserved = 0 OR a2.start_time > CURTIME())
    ) AS is_reserved_for_date
    FROM `tables` t
    JOIN dining_zones dz ON dz.zone_id = t.zone_id
    GROUP BY t.table_id
    ORDER BY dz.zone_name, t.seating_preference, t.capacity");
  $stmt->execute([
    ':floor_date' => $selectedFloorDate,
    ':is_today' => $isViewingToday ? 1 : 0,
    ':floor_date_reserved' => $selectedFloorDate,
    ':is_today_reserved' => $isViewingToday ? 1 : 0,
  ]);
  $tables = $stmt->fetchAll() ?: [];
  $stmt->closeCursor();

  $tablesByZone = [];
  foreach ($tables as $t) {
    $manualOccupiedActive = $isViewingToday
      && ($t['current_status'] ?? '') === 'occupied'
      && (
        empty($t['manual_status_until'])
        || strtotime((string) $t['manual_status_until']) > time()
      );

    if ((int) $t['is_active_now'] === 1) {
      $status = 'occupied';
    } elseif ($manualOccupiedActive) {
      $status = 'occupied';
    } elseif ((int) $t['is_reserved_for_date'] === 1) {
      $status = 'reserved';
    } else {
      $status = 'available';
    }

    $displayLabel = trim((string) ($t['seating_preference'] ?? ''));
    if ($displayLabel === '') {
      $displayLabel = 'Unassigned';
    }

    $tablesByZone[$t['zone_id']][] = [
      'table_id' => $t['table_id'],
      'cap' => $t['capacity'],
      'seating_preference' => $t['seating_preference'] ?? '',
      'status' => $status,
      'label' => $displayLabel,
    ];
  }

  foreach ($zoneRows as $z) {
    $key = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $z['zone_name']));
    $zones[$key] = [
      'label' => $z['zone_name'],
      'zone_id' => $z['zone_id'],
      'tables' => $tablesByZone[$z['zone_id']] ?? [],
    ];
  }
} catch (PDOException $e) {
  $adminError = safe_error_message($e);
}

include '../../includes/header.php';
?>
<body>
<div class="admin-layout" id="adminLayout">

  <?php include '../../includes/admin-sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-content">
      
      <?php if ($adminError): ?>
        <div class="auth-alert"><span><?= e($adminError) ?></span></div>
      <?php endif; ?>
      <?php if ($adminSuccess): ?>
        <div class="auth-alert auth-success"><span><?= e($adminSuccess) ?></span></div>
      <?php endif; ?>

      <header class="admin-header">
        <div class="admin-header-row" style="display:flex; justify-content:space-between; align-items:center;">
          <div>
            <h1 class="admin-page-title">Floor Management</h1>
            <p class="admin-page-subtitle">Manage tables and dining zones for <?= e(date('F j, Y', strtotime($selectedFloorDate))) ?>.</p>
          </div>
          <div style="display:flex; gap:var(--space-3); align-items:flex-end; flex-wrap:wrap;">
            <form method="get" action="floor.php" style="display:flex; flex-direction:column; gap:var(--space-2); min-width: 12rem;">
              <label for="floorDate" class="form-label" style="margin-bottom:0;">Floor Date</label>
              <input type="date" id="floorDate" name="floor_date" class="form-input" value="<?= e($selectedFloorDate) ?>" onchange="this.form.submit()">
            </form>
            <button class="btn btn-outline" data-modal-open="reserveTableModal" data-reset-reserve="1" style="display:flex; align-items:center; gap:var(--space-2);" <?= $isPastFloorDate ? 'disabled title="Reservations cannot be created from a past floor date."' : '' ?>>
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
              Reserve
            </button>
          </div>
        </div>
      </header>

      <div class="admin-floor-wrap" style="margin-top:var(--space-6); overflow:visible;">
        <div id="floor-csrf-container" data-csrf="<?= e(csrf_token()) ?>" data-action-token="<?= e(action_token('admin_floor_update')) ?>" data-status-token="<?= e(action_token('admin_floor_status')) ?>" data-details-token="<?= e(action_token('admin_floor_details')) ?>" data-availability-token="<?= e(action_token('check_availability')) ?>" data-selected-date="<?= e($selectedFloorDate) ?>" data-today-date="<?= e($todayDate) ?>" data-is-past-view="<?= $isPastFloorDate ? '1' : '0' ?>" style="display:none"></div>

        <!-- Segmented Tabs -->
        <div class="segmented-tabs" data-admin-tabs>
          <?php $first = true; foreach ($zones as $key => $zone): ?>
          <button class="segment-btn <?= $first ? 'active' : '' ?>" data-tab="<?= e($key) ?>" aria-selected="<?= $first ? 'true' : 'false' ?>">
            <?= e($zone['label']) ?>
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
        <div class="admin-panel <?= $first ? 'active' : '' ?>" data-panel="<?= e($key) ?>" data-zone-id="<?= e((string)$zone['zone_id']) ?>" data-zone-key="<?= e($key) ?>">
          
          <!-- Summary Cards Row -->
          <div class="floor-stats-grid">
            <div class="floor-stat-card">
              <div class="floor-stat-val text-green"><?= e((string)$avail) ?></div>
              <div class="floor-stat-lbl">Available</div>
            </div>
            <div class="floor-stat-card">
              <div class="floor-stat-val text-yellow"><?= e((string)$reserved) ?></div>
              <div class="floor-stat-lbl">Reserved</div>
            </div>
            <div class="floor-stat-card">
              <div class="floor-stat-val text-red"><?= e((string)$occupied) ?></div>
              <div class="floor-stat-lbl">Occupied</div>
            </div>
          </div>

          <!-- Tables Grid Card -->
          <div class="admin-card" style="margin-top: var(--space-6);">
            <div class="admin-card-header">
              <h2 class="admin-card-title"><?= e($zone['label']) ?> Tables</h2>
            </div>
            <div class="admin-card-body">
              <div class="floor-grid-new">
                <?php foreach ($zone['tables'] as $t): ?>
                <div class="floor-tile-new floor-tile-new--<?= e($t['status']) ?>" data-status="<?= e($t['status']) ?>" data-table-id="<?= e((string)$t['table_id']) ?>" data-zone-key="<?= e($key) ?>" data-zone-label="<?= e($zone['label']) ?>" data-seating-preference="<?= e($t['seating_preference'] ?? '') ?>" tabindex="0">
                  <div class="floor-tile-new-top">
                    <span class="floor-tile-new-number"><?= e($t['label']) ?></span>
                    <svg class="floor-tile-new-edit" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                  </div>
                  <div class="floor-tile-new-capacity">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px;"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <?= e((string)$t['cap']) ?> seats
                  </div>
                  <div class="floor-tile-new-status"><?= e(ucfirst($t['status'])) ?></div>
                  <div class="floor-tile-new-actions">
                    <button type="button" class="btn btn-sm btn-outline floor-tile-reserve-btn" data-reserve-table data-table-id="<?= e((string)$t['table_id']) ?>" data-zone-key="<?= e($key) ?>" data-seating-preference="<?= e($t['seating_preference'] ?? '') ?>" onclick="event.stopPropagation(); return window.adminOpenReserveTable(this);" <?= ($t['status'] !== 'available' || $isPastFloorDate) ? 'disabled' : '' ?> <?= $isPastFloorDate ? 'title="Reservations cannot be created from a past floor date."' : '' ?>>
                      Reserve
                    </button>
                  </div>
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

<!-- Floor Details Modal -->
<div class="admin-modal" id="floorDetailModal">
  <div class="admin-modal-card" style="max-width:34rem;">
    <div class="admin-modal-header" style="align-items:flex-start;">
      <div>
        <h2 class="admin-modal-title" id="floorDetailTitle" style="margin-bottom:var(--space-1);">Table Details</h2>
        <p class="admin-modal-subtitle" id="floorDetailSubtitle" style="color:var(--clr-muted-fg);font-size:var(--text-sm);">Reservation details for the selected table</p>
      </div>
      <button class="admin-modal-close" data-modal-close aria-label="Close">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <div class="floor-detail-loading" id="floorDetailLoading">Loading table details...</div>
    <div class="auth-alert" id="floorDetailError" style="display:none; margin-bottom:var(--space-4);"><span></span></div>

    <div id="floorDetailContent" style="display:none;">
      <div class="floor-detail-list-wrap" id="floorDetailListWrap" style="display:none;">
        <div class="floor-detail-list-title">Reservations for this table</div>
        <div class="floor-detail-list" id="floorDetailList"></div>
      </div>

      <div class="floor-detail-highlight">
        <div>
          <div class="floor-detail-kicker">Current Selection</div>
          <div class="floor-detail-table" id="floorDetailTableLabel">Table</div>
          <div class="floor-detail-zone" id="floorDetailZoneLabel">Zone</div>
        </div>
        <span class="badge" id="floorDetailStatusBadge">Reserved</span>
      </div>

      <dl class="admin-modal-rows floor-detail-rows">
        <div class="admin-modal-row">
          <dt>Guest</dt>
          <dd id="floorDetailGuestName">-</dd>
        </div>
        <div class="admin-modal-row">
          <dt>Email</dt>
          <dd id="floorDetailGuestEmail">-</dd>
        </div>
        <div class="admin-modal-row">
          <dt>Guests</dt>
          <dd id="floorDetailPartySize">-</dd>
        </div>
        <div class="admin-modal-row">
          <dt>Date</dt>
          <dd id="floorDetailDate">-</dd>
        </div>
        <div class="admin-modal-row">
          <dt>Time</dt>
          <dd id="floorDetailTime">-</dd>
        </div>
        <div class="admin-modal-row">
          <dt>Booking</dt>
          <dd id="floorDetailService">-</dd>
        </div>
        <div class="admin-modal-row">
          <dt>Reference</dt>
          <dd id="floorDetailReference">-</dd>
        </div>
        <div class="admin-modal-row">
          <dt>Created</dt>
          <dd id="floorDetailCreated">-</dd>
        </div>
        <div class="admin-modal-row">
          <dt>Notes</dt>
          <dd id="floorDetailNotes">No special requests.</dd>
        </div>
      </dl>
    </div>

    <div class="admin-modal-footer">
      <button type="button" class="btn btn-outline" data-modal-close>Close</button>
    </div>
  </div>
</div>

<!-- Reserve Table Modal -->
<div class="admin-modal" id="reserveTableModal">
  <div class="admin-modal-card" style="max-width:32rem;">
    <div class="admin-modal-header" style="align-items:flex-start;">
      <div>
        <h2 class="admin-modal-title" style="margin-bottom:var(--space-1);">Reserve Table</h2>
        <p class="admin-modal-subtitle" style="color:var(--clr-muted-fg);font-size:var(--text-sm);">Create a reservation for a guest</p>
        <p class="text-muted text-sm" style="margin-top:var(--space-2);">Reservation duration: <?= e($reservationDurationLabel) ?>.</p>
      </div>
      <button class="admin-modal-close" data-modal-close aria-label="Close">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <form class="admin-form" id="reserveTableForm" method="post" action="../../actions.php?action=admin_reserve_table">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="action_token" value="<?= e(action_token('admin_reserve_table')) ?>">
      
      <!-- Guest Selection -->
      <div class="form-group" style="margin-bottom:var(--space-4);">
        <label class="form-label">Guest Lookup</label>
        <input type="text" class="form-input" id="reserveGuestSearch" placeholder="Search by name or email">
        <div id="reserveGuestSuggestions" class="guest-suggest-list" style="display:none;"></div>
      </div>
      <div class="form-group" style="margin-bottom:var(--space-4);">
        <label class="form-label">Select Guest <span style="color:var(--clr-danger);">*</span></label>
        <select class="form-select" id="reserveUserId" name="user_id" required style="width:100%;">
          <option value="">-- Select a guest --</option>
          <?php foreach ($users as $user): ?>
            <option value="<?= e($user['user_id']) ?>" 
                    data-first-name="<?= e($user['first_name']) ?>"
                    data-last-name="<?= e($user['last_name']) ?>"
                    data-email="<?= e($user['email']) ?>"
                    data-phone="<?= e($user['phone'] ?? '') ?>">
              <?= e($user['last_name'] . ', ' . $user['first_name']) ?> (<?= e($user['email']) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <!-- Guest Info (auto-filled from the selected user) -->
      <div id="guestInfoFields" style="display:none; margin-bottom:var(--space-4);">
        <div class="form-row" style="margin-bottom:var(--space-3);">
          <div class="form-group" style="flex:1;">
            <label class="form-label">First Name <span style="color:var(--clr-danger);">*</span></label>
            <input type="text" class="form-input" id="reserveFirstName" name="first_name" required>
          </div>
          <div class="form-group" style="flex:1;">
            <label class="form-label">Last Name <span style="color:var(--clr-danger);">*</span></label>
            <input type="text" class="form-input" id="reserveLastName" name="last_name" required>
          </div>
        </div>
        <div class="form-row" style="margin-bottom:var(--space-3);">
          <div class="form-group" style="flex:1;">
            <label class="form-label">Email <span style="color:var(--clr-danger);">*</span></label>
            <input type="email" class="form-input" id="reserveEmail" name="email" required>
          </div>
          <div class="form-group" style="flex:1;">
            <label class="form-label">Phone</label>
            <input type="tel" class="form-input" id="reservePhone" name="phone">
          </div>
        </div>
      </div>
      
      <!-- Party Size -->
      <div class="form-group" style="margin-bottom:var(--space-4);">
        <label class="form-label">Party Size <span style="color:var(--clr-danger);">*</span></label>
        <input type="number" class="form-input" id="reservePartySize" name="party_size" min="1" max="20" value="2" required style="width:6rem;">
        <p class="text-muted text-sm" style="margin-top:var(--space-2);">Party size helps narrow which zones can accommodate the reservation.</p>
      </div>
      <input type="hidden" id="reserveZoneDbId" name="zone_id">
      <input type="hidden" id="reserveTableId" name="table_id">
      <div class="form-group" style="margin-bottom:var(--space-4);">
        <label class="form-label">Zone <span style="color:var(--clr-danger);">*</span></label>
        <select class="form-select" id="reserveZone" name="zone_key" required>
          <option value="">-- Select zone --</option>
          <?php foreach ($zones as $key => $zone): ?>
            <option value="<?= e($key) ?>" data-zone-id="<?= e((string) $zone['zone_id']) ?>"><?= e($zone['label']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group" style="margin-bottom:var(--space-4);">
        <label class="form-label">Seating Preference <span style="color:var(--clr-danger);">*</span></label>
        <select class="form-select" id="reserveSeating" name="seating_preference" required>
          <option value="">-- Select zone first --</option>
        </select>
        <p class="text-muted text-sm" style="margin-top:var(--space-2);">This will auto-filter to match the party size. For example, Fireplace can cap at 6.</p>
      </div>

      <div class="form-group" style="margin-bottom:var(--space-4);">
        <label class="form-label">Service <span style="color:var(--clr-danger);">*</span></label>
        <select class="form-select" id="reserveService" name="service_id">
          <option value="">Select a service</option>
          <?php foreach ($services as $service): ?>
            <option value="<?= e($service['service_id']) ?>" data-price="<?= e(number_format((float) $service['price'], 2)) ?>">
              <?= e($service['service_name']) ?> (<?= e(number_format((float) $service['price'], 2)) ?>)
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group" style="margin-bottom:var(--space-4);">
        <label class="form-label">Event Package <span style="font-weight:400; color:var(--clr-muted-fg)">(optional)</span></label>
        <select class="form-select" id="reservePackage" name="event_package_id">
          <option value="">No package</option>
          <?php foreach ($packages as $package): ?>
            <option value="<?= e($package['package_id']) ?>" data-price="<?= e(number_format((float) $package['base_price'], 2)) ?>">
              <?= e($package['package_name']) ?> (<?= e(number_format((float) $package['base_price'], 2)) ?>)
            </option>
          <?php endforeach; ?>
        </select>
        <p class="text-muted text-sm" style="margin-top:var(--space-2);">Choose either a service or an event package, not both.</p>
      </div>

      <div class="form-group" style="margin-bottom:var(--space-4);">
        <label class="form-label">Add-ons <span style="font-weight:400; color:var(--clr-muted-fg)">(optional)</span></label>
        <div class="admin-addon-list">
          <?php foreach ($addOns as $addOn): ?>
            <label class="admin-addon-item">
              <input type="checkbox" name="add_on_ids[]" value="<?= e($addOn['add_on_id']) ?>" data-name="<?= e($addOn['name']) ?>">
              <span class="admin-addon-main">
                <span class="admin-addon-text">
                  <span class="admin-addon-name"><?= e($addOn['name']) ?></span>
                  <span class="admin-addon-desc"><?= e($addOn['description'] ?: $addOn['category']) ?></span>
                </span>
                <span class="admin-addon-price">+<?= e(number_format((float) $addOn['price'], 2)) ?></span>
              </span>
              <input type="number" class="form-input admin-addon-qty" name="add_on_qty[<?= e($addOn['add_on_id']) ?>]" min="1" max="20" value="1">
            </label>
          <?php endforeach; ?>
        </div>
      </div>
      
      <!-- Date & Time -->
      <div class="form-row" style="margin-bottom:var(--space-4);">
        <div class="form-group" style="flex:1;">
          <label class="form-label">Date <span style="color:var(--clr-danger);">*</span></label>
          <input type="date" class="form-input" id="reserveDate" name="date" required min="<?= date('Y-m-d') ?>">
        </div>
        <div class="form-group" style="flex:1;">
          <label class="form-label">Time <span style="color:var(--clr-danger);">*</span></label>
          <select class="form-select" id="reserveTime" name="time" required>
            <option value="">-- Select time --</option>
            <?php 
            $times = ['11:00','11:30','12:00','12:30','13:00','13:30','14:00','14:30','15:00','15:30','16:00','16:30','17:00','17:30','18:00','18:30','19:00','19:30','20:00','20:30','21:00','21:30','22:00'];
            foreach ($times as $t): 
              $display = date('g:i A', strtotime($t));
            ?>
              <option value="<?= e($t) ?>"><?= e($display) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      
      <div class="admin-modal-footer" style="padding-top:0; border-top:none; display:flex; gap:var(--space-3); justify-content:flex-end;">
        <button type="button" class="btn btn-outline" data-modal-close>Cancel</button>
        <button type="submit" class="btn btn-primary">Create Reservation</button>
      </div>
    </form>
  </div>
</div>

<script>
// Pass table data to JavaScript for reservation modal
window.ALL_TABLES = <?= json_encode($zones) ?>;
window.ADMIN_GUESTS = <?= json_encode($users) ?>;
</script>
<script src="<?= $basePath ?>js/admin.js"></script>
</body>
</html>
