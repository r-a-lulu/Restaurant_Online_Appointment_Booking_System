<?php
/**
 * Dashboard Overview - pages/dashboard/index.php
 */

require_once '../../includes/security.php';
start_secure_session();
require_login();

$pageTitle = 'My Dashboard';
$pageCSS = ['dashboard.css'];
$currentDashPage = 'overview';
$basePath = '../../';

$dashError = get_flash('dash_error');
$dashSuccess = get_flash('dash_success');

$stats = [
  'total' => 0,
  'upcoming' => 0,
  'completed' => 0,
  'favorite_zone' => '-',
];
$upcoming = [];

try {
  $pdo = db();

  $stmt = $pdo->prepare('SELECT COUNT(*) FROM appointments WHERE user_id = :uid');
  $stmt->execute([':uid' => (int) $_SESSION['user_id']]);
  $stats['total'] = (int) $stmt->fetchColumn();
  $stmt->closeCursor();

  $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments a WHERE a.user_id = :uid AND a.status_id = (SELECT status_id FROM appointment_status WHERE status_name = 'confirmed' LIMIT 1) AND a.appointment_date >= CURDATE()");
  $stmt->execute([':uid' => (int) $_SESSION['user_id']]);
  $stats['upcoming'] = (int) $stmt->fetchColumn();
  $stmt->closeCursor();

  $stmt = $pdo->prepare("SELECT COUNT(*) FROM appointments a WHERE a.user_id = :uid AND a.status_id = (SELECT status_id FROM appointment_status WHERE status_name = 'completed' LIMIT 1)");
  $stmt->execute([':uid' => (int) $_SESSION['user_id']]);
  $stats['completed'] = (int) $stmt->fetchColumn();
  $stmt->closeCursor();

  $stmt = $pdo->prepare("SELECT dz.zone_name, COUNT(*) AS cnt FROM appointments a LEFT JOIN `tables` t ON t.table_id = a.table_id JOIN dining_zones dz ON dz.zone_id = COALESCE(a.zone_id, t.zone_id) WHERE a.user_id = :uid GROUP BY dz.zone_id ORDER BY cnt DESC LIMIT 1");
  $stmt->execute([':uid' => (int) $_SESSION['user_id']]);
  $stats['favorite_zone'] = $stmt->fetchColumn() ?: '-';
  $stmt->closeCursor();

  $stmt = $pdo->prepare("SELECT appointment_id, zone_name, appointment_date, start_time, party_size, status_name FROM vw_appointments_detail WHERE user_id = :uid AND status_id IN (SELECT status_id FROM appointment_status WHERE status_name IN ('pending','confirmed')) AND appointment_date >= CURDATE() ORDER BY appointment_date ASC, start_time ASC LIMIT 5");
  $stmt->execute([':uid' => (int) $_SESSION['user_id']]);
  $upcoming = $stmt->fetchAll() ?: [];
  $stmt->closeCursor();
} catch (PDOException $e) {
  $dashError = safe_error_message($e);
}

include '../../includes/header.php';
?>
<body>
<div class="dashboard-layout" id="dashboardLayout">

  <?php include '../../includes/dashboard-sidebar.php'; ?>

  <main class="dashboard-main">
    <div class="dashboard-content">

      <?php if ($dashError): ?>
        <div class="auth-alert"><span><?= e($dashError) ?></span></div>
      <?php elseif ($dashSuccess): ?>
        <div class="auth-alert auth-success"><span><?= e($dashSuccess) ?></span></div>
      <?php endif; ?>

      <header class="dashboard-header">
        <div class="dashboard-header-row">
          <div>
            <h1 class="dashboard-page-title">Welcome back, <?= e($_SESSION['first_name'] ?? 'Guest') ?>!</h1>
            <p class="dashboard-page-subtitle">Here's an overview of your dining activity.</p>
          </div>
          <a href="../book.php" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Book a Table
          </a>
        </div>
      </header>

      <div class="stat-cards">
        <div class="stat-card">
          <div class="icon-circle">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          </div>
          <div class="stat-card-body">
            <p class="stat-card-label">Total Reservations</p>
            <p class="stat-card-value"><?= e((string) $stats['total']) ?></p>
            <p class="stat-card-sub">All time</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="icon-circle">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </div>
          <div class="stat-card-body">
            <p class="stat-card-label">Upcoming</p>
            <p class="stat-card-value"><?= e((string) $stats['upcoming']) ?></p>
            <p class="stat-card-sub">Confirmed</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="icon-circle">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          </div>
          <div class="stat-card-body">
            <p class="stat-card-label">Completed</p>
            <p class="stat-card-value"><?= e((string) $stats['completed']) ?></p>
            <p class="stat-card-sub">Visits</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="icon-circle">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <div class="stat-card-body">
            <p class="stat-card-label">Favorite Zone</p>
            <p class="stat-card-value" style="font-size: var(--text-xl);"><?= e($stats['favorite_zone']) ?></p>
            <p class="stat-card-sub">Most visited</p>
          </div>
        </div>
      </div>

      <div class="dash-section">
        <div class="dash-section-header">
          <h2 class="dash-section-title">Upcoming Reservations</h2>
          <a href="reservations.php" class="btn btn-outline btn-sm">View All</a>
        </div>
        <div class="reservation-list">
          <?php foreach ($upcoming as $row): ?>
          <div class="reservation-row">
            <div class="reservation-date-block">
              <span class="reservation-date-day"><?= e(date('d', strtotime($row['appointment_date']))) ?></span>
              <span class="reservation-date-month"><?= e(date('M', strtotime($row['appointment_date']))) ?></span>
            </div>
            <div class="reservation-info">
              <p class="reservation-zone"><?= e($row['zone_name'] ?? '-') ?></p>
              <p class="reservation-meta"><?= e(date('g:i A', strtotime($row['start_time']))) ?> | <?= e((string) $row['party_size']) ?> Guests | #<?= e((string) $row['appointment_id']) ?></p>
            </div>
            <span class="badge badge-<?= $row['status_name'] === 'confirmed' ? 'confirmed' : 'pending' ?>"><?= e($row['status_name'] === 'confirmed' ? 'Upcoming' : 'Pending') ?></span>
            <div class="reservation-actions">
              <a href="reservations.php" class="btn btn-outline btn-sm">Details</a>
            </div>
          </div>
          <?php endforeach; ?>
          <?php if (empty($upcoming)): ?>
          <div class="reservation-row"><div class="reservation-info"><p class="reservation-zone">No upcoming reservations.</p></div></div>
          <?php endif; ?>
        </div>
      </div>

      <div class="dash-section">
        <div class="dash-section-header">
          <h2 class="dash-section-title">Quick Actions</h2>
        </div>
        <div class="dash-section-body">
          <div class="quick-actions">
            <a href="../book.php" class="quick-action-card">
              <div class="icon-circle icon-circle-lg">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><line x1="8" y1="14" x2="8" y2="14"/><line x1="12" y1="14" x2="12" y2="14"/><line x1="16" y1="14" x2="16" y2="14"/></svg>
              </div>
              <span class="quick-action-label">Book a Table</span>
            </a>
            <a href="reservations.php" class="quick-action-card">
              <div class="icon-circle icon-circle-lg">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
              </div>
              <span class="quick-action-label">My Reservations</span>
            </a>
            <a href="profile.php" class="quick-action-card">
              <div class="icon-circle icon-circle-lg">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              </div>
              <span class="quick-action-label">My Profile</span>
            </a>
          </div>
        </div>
      </div>

    </div>
  </main>

</div>

<script src="<?= $basePath ?>js/dashboard.js"></script>
</body>
</html>
