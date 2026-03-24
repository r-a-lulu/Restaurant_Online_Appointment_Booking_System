<?php
/**
 * Admin Dashboard Overview ? pages/admin/index.php
 */

require_once '../../includes/security.php';
start_secure_session();
require_admin();

$pageTitle        = 'Admin Dashboard';
$pageCSS          = ['dashboard.css', 'admin.css'];
$currentAdminPage = 'dashboard';
$basePath         = '../../';

$adminError = get_flash('admin_error');
$adminSuccess = get_flash('admin_success');

$stats = [
  'today_reservations' => 0,
  'pending' => 0,
  'total_guests' => 0,
  'occupancy' => 0,
];
$recent = [];
$zoneStatus = [];

try {
  $pdo = db();

  $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments WHERE appointment_date = CURDATE() AND status_id IN (SELECT status_id FROM appointment_status WHERE status_name IN ('pending','confirmed','completed'))");
  $stmt->execute();
  $stats['today_reservations'] = (int) $stmt->fetchColumn();
  $stmt->closeCursor();

  $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments a JOIN appointment_status s ON s.status_id = a.status_id WHERE a.appointment_date >= CURDATE() AND a.status_id = (SELECT status_id FROM appointment_status WHERE status_name = 'pending' LIMIT 1)");
  $stmt->execute();
  $stats['pending'] = (int) $stmt->fetchColumn();
  $stmt->closeCursor();

  $stmt = $pdo->prepare("SELECT COALESCE(SUM(party_size), 0) FROM appointments WHERE appointment_date = CURDATE() AND status_id IN (SELECT status_id FROM appointment_status WHERE status_name IN ('pending','confirmed','completed'))");
  $stmt->execute();
  $stats['total_guests'] = (int) $stmt->fetchColumn();
  $stmt->closeCursor();

  $stmt = $pdo->prepare('SELECT COUNT(*) FROM `tables`');
  $stmt->execute();
  $totalTables = (int) $stmt->fetchColumn();
  $stmt->closeCursor();

  $stmt = $pdo->prepare("SELECT COUNT(DISTINCT a.table_id) + SUM(CASE WHEN a.table_id IS NULL THEN 1 ELSE 0 END) FROM appointments a JOIN appointment_status s ON s.status_id = a.status_id WHERE a.appointment_date = CURDATE() AND a.status_id IN (SELECT status_id FROM appointment_status WHERE status_name IN ('pending','confirmed'))");
  $stmt->execute();
  $bookedTables = (int) $stmt->fetchColumn();
  $stmt->closeCursor();

  if ($totalTables > 0) {
    $bookedTables = min($bookedTables, $totalTables);
    $stats['occupancy'] = (int) round(($bookedTables / $totalTables) * 100);
  } else {
    $stats['occupancy'] = 0;
  }

  $stmt = $pdo->prepare("SELECT v.appointment_id, v.customer_name, v.zone_name, v.party_size, v.start_time, v.status_name, a.user_id, (SELECT COUNT(*) FROM appointments a2 WHERE a2.user_id = a.user_id AND a2.appointment_id <> v.appointment_id AND a2.status_id IN (SELECT status_id FROM appointment_status WHERE status_name IN ('pending','confirmed'))) AS active_count FROM vw_upcoming_appointments v JOIN appointments a ON a.appointment_id = v.appointment_id ORDER BY v.appointment_date ASC, v.start_time ASC LIMIT 5");
  $stmt->execute();
  $recent = $stmt->fetchAll() ?: [];
  $stmt->closeCursor();

  $stmt = $pdo->prepare('SELECT dz.zone_id, dz.zone_name, COUNT(t.table_id) AS total_tables FROM dining_zones dz LEFT JOIN `tables` t ON t.zone_id = dz.zone_id GROUP BY dz.zone_id ORDER BY dz.zone_name');
  $stmt->execute();
  $zones = $stmt->fetchAll() ?: [];
  $stmt->closeCursor();

  $stmt = $pdo->prepare("SELECT dz.zone_id, COUNT(DISTINCT a.table_id) + SUM(CASE WHEN a.table_id IS NULL THEN 1 ELSE 0 END) AS booked_tables FROM appointments a JOIN appointment_status s ON s.status_id = a.status_id LEFT JOIN `tables` t ON t.table_id = a.table_id JOIN dining_zones dz ON dz.zone_id = COALESCE(a.zone_id, t.zone_id) WHERE a.appointment_date = CURDATE() AND a.status_id IN (SELECT status_id FROM appointment_status WHERE status_name IN ('pending','confirmed')) GROUP BY dz.zone_id");
  $stmt->execute();
  $bookedRows = $stmt->fetchAll() ?: [];
  $stmt->closeCursor();

  $bookedMap = [];
  foreach ($bookedRows as $row) {
    $bookedMap[$row['zone_id']] = (int) $row['booked_tables'];
  }

  foreach ($zones as $z) {
    $booked = $bookedMap[$z['zone_id']] ?? 0;
    $total = (int) $z['total_tables'];
    $zoneStatus[] = [
      'name' => $z['zone_name'],
      'booked' => $booked,
      'total' => $total,
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
      <?php elseif ($adminSuccess): ?>
        <div class="auth-alert" style="border-color: var(--clr-success, #2e7d32); color: var(--clr-success, #2e7d32);"><span><?= e($adminSuccess) ?></span></div>
      <?php endif; ?>

      <!-- Page Header -->
      <header class="admin-header">
        <div class="admin-header-row">
          <div>
            <h1 class="admin-page-title">Dashboard</h1>
            <p class="admin-page-subtitle">Welcome back, <?= e($_SESSION['first_name'] ?? 'Admin') ?>.</p>
          </div>
        </div>
      </header>

      <!-- Stat Cards -->
      <div class="admin-stat-cards">

        <div class="admin-stat-card">
          <div class="admin-stat-card-body">
            <span class="admin-stat-label">Today's Reservations</span>
            <div class="admin-stat-value"><?= e((string) $stats['today_reservations']) ?></div>
            <div class="admin-stat-trend admin-stat-trend--neutral">Live count</div>
          </div>
          <div class="admin-stat-icon-wrap">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
          </div>
        </div>

        <div class="admin-stat-card">
          <div class="admin-stat-card-body">
            <span class="admin-stat-label">Pending Approval</span>
            <div class="admin-stat-value"><?= e((string) $stats['pending']) ?></div>
            <div class="admin-stat-trend admin-stat-trend--neutral">Requires attention</div>
          </div>
          <div class="admin-stat-icon-wrap">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
          </div>
        </div>

        <div class="admin-stat-card">
          <div class="admin-stat-card-body">
            <span class="admin-stat-label">Total Guests Today</span>
            <div class="admin-stat-value"><?= e((string) $stats['total_guests']) ?></div>
            <div class="admin-stat-trend admin-stat-trend--neutral">Live count</div>
          </div>
          <div class="admin-stat-icon-wrap">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
          </div>
        </div>

        <div class="admin-stat-card">
          <div class="admin-stat-card-body">
            <span class="admin-stat-label">Occupancy Rate</span>
            <div class="admin-stat-value"><?= e((string) $stats['occupancy']) ?>%</div>
            <div class="admin-stat-trend admin-stat-trend--neutral">Today</div>
          </div>
          <div class="admin-stat-icon-wrap">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
              <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
            </svg>
          </div>
        </div>

      </div>

      <!-- Overview Grid -->
      <div class="admin-overview-grid">

        <!-- Recent Reservations -->
        <div class="admin-section">
          <div class="admin-section-header">
            <h2 class="admin-section-title">Upcoming Reservations</h2>
            <a href="reservations.php" class="admin-view-all-link">
              View All
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
              </svg>
            </a>
          </div>
          <div class="admin-res-list">

            <?php foreach ($recent as $row): ?>
              <?php $canApprove = ($row['status_name'] === 'pending') && ((int) ($row['active_count'] ?? 0) < 5); ?>
              <?php $badge = $row['status_name'] === 'pending' ? 'badge-pending' : ($row['status_name'] === 'confirmed' ? 'badge-confirmed' : 'badge-cancelled'); ?>
              <div class="admin-res-row" data-status="<?= e($row['status_name']) ?>">
                <div class="admin-res-info">
                  <div class="admin-res-name-row">
                    <span class="admin-res-name"><?= e($row['customer_name']) ?></span>
                    <span class="badge <?= $badge ?>"><?= e($row['status_name']) ?></span>
                  </div>
                  <p class="admin-res-meta"><?= e($row['zone_name'] ?? '?') ?> ? <?= e((string) $row['party_size']) ?> guests ? <?= e(date('g:i A', strtotime($row['start_time']))) ?></p>
                </div>
                <?php if ($row['status_name'] === 'pending'): ?>
                  <div class="admin-res-actions">
                    <form method="post" action="<?= $basePath ?>actions.php?action=admin_update_status">
                      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action_token" value="<?= e(action_token('admin_update_status')) ?>">
                      <input type="hidden" name="appointment_id" value="<?= e($row['appointment_id']) ?>">
                      <button class="admin-res-btn admin-res-btn--approve" name="action" value="approve" aria-label="Approve" <?php echo $canApprove ? '' : 'disabled title="User has reached the 5 active bookings limit."'; ?>>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                      </button>
                    </form>
                    <form method="post" action="<?= $basePath ?>actions.php?action=admin_update_status">
                      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                    <input type="hidden" name="action_token" value="<?= e(action_token('admin_update_status')) ?>">
                      <input type="hidden" name="appointment_id" value="<?= e($row['appointment_id']) ?>">
                      <button class="admin-res-btn admin-res-btn--reject" name="action" value="reject" aria-label="Reject">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                          <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                      </button>
                    </form>
                  </div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>

            <?php if (empty($recent)): ?>
              <div class="admin-res-row"><div class="admin-res-info"><span class="admin-res-name">No upcoming reservations.</span></div></div>
            <?php endif; ?>

          </div>
        </div>

        <!-- Zone Status -->
        <div class="admin-section">
          <div class="admin-section-header">
            <h2 class="admin-section-title">Zone Status</h2>
          </div>
          <div class="admin-section-body">

            <div class="zone-status-list">
              <?php foreach ($zoneStatus as $zone): ?>
                <?php
                  $total = (int) $zone['total'];
                  $booked = (int) $zone['booked'];
                  $available = max(0, $total - $booked);
                  $pct = $total > 0 ? (int) round(($booked / $total) * 100) : 0;
                ?>
                <div class="zone-status-item">
                  <div class="zone-status-top">
                    <span class="zone-status-name"><?= e($zone['name']) ?></span>
                    <span class="zone-status-count"><?= e((string) $booked) ?>/<?= e((string) $total) ?> tables</span>
                  </div>
                  <div class="occupancy-track">
                    <div class="occupancy-fill" style="width:<?= e((string) $pct) ?>%"></div>
                  </div>
                  <p class="zone-status-avail"><?= e((string) $available) ?> tables available</p>
                </div>
              <?php endforeach; ?>
            </div>

            <a href="floor.php" class="admin-manage-floor-btn">
              Manage Floor Plan
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
              </svg>
            </a>

          </div>
        </div>

      </div><!-- /.admin-overview-grid -->

    </div><!-- /.admin-content -->
  </main>

</div><!-- /.admin-layout -->

<script src="<?= $basePath ?>js/admin.js"></script>
</body>
</html>




