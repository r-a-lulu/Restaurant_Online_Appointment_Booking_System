<?php
/**
 * Dashboard Dining History - pages/dashboard/history.php
 */

require_once '../../includes/security.php';
start_secure_session();
require_login();

$pageTitle       = 'Dining History';
$pageCSS         = ['dashboard.css'];
$currentDashPage = 'history';
$basePath        = '../../';
$siteName        = get_setting('restaurant_name', 'Eudaimonia');

$dashError = get_flash('dash_error');
$history = [];

try {
  $pdo = db();
  $stmt = $pdo->prepare("SELECT appointment_id, zone_name, appointment_date, start_time, party_size, status_name FROM vw_appointments_detail WHERE user_id = :uid AND status_name IN ('completed', 'cancelled', 'no_show') ORDER BY appointment_date DESC, start_time DESC");
  $stmt->execute([':uid' => (int) $_SESSION['user_id']]);
  $history = $stmt->fetchAll() ?: [];
  $stmt->closeCursor();
} catch (PDOException $e) {
  error_log('Dashboard history load failed for user ' . (int) ($_SESSION['user_id'] ?? 0) . ': ' . $e->getMessage());
  $dashError = 'We could not load your reservation history right now. Please try again.';
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
      <?php endif; ?>

      <header class="dashboard-header">
        <div class="dashboard-header-row">
          <div>
            <h1 class="dashboard-page-title">Dining History</h1>
            <p class="dashboard-page-subtitle">A record of your completed, cancelled, and missed reservations at <?= e($siteName) ?>.</p>
          </div>
        </div>
      </header>

      <div class="dash-section">
        <div class="dash-section-header">
          <h2 class="dash-section-title">Reservation History</h2>
          <span class="badge badge-secondary"><?= e((string) count($history)) ?> records</span>
        </div>

        <?php if (empty($history)): ?>
        <div class="empty-state">
          <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <h3 class="empty-state-title">No dining history yet</h3>
          <p class="empty-state-text">Your completed, cancelled, and no-show reservations will appear here automatically.</p>
        </div>
        <?php else: ?>

        <div class="reservation-list">
          <?php foreach ($history as $visit): ?>
          <?php
            $statusName = $visit['status_name'];
            $badgeClass = $statusName === 'completed' ? 'badge-confirmed' : 'badge-cancelled';
            if ($statusName === 'completed') {
              $badgeLabel = 'Completed';
            } elseif ($statusName === 'no_show') {
              $badgeLabel = 'No Show';
            } else {
              $badgeLabel = 'Cancelled';
            }
          ?>
          <div class="history-row">
            <div class="reservation-date-block">
              <span class="reservation-date-day"><?= e(date('d', strtotime($visit['appointment_date']))) ?></span>
              <span class="reservation-date-month"><?= e(date('M', strtotime($visit['appointment_date']))) ?></span>
            </div>
            <div class="history-body">
              <p class="history-zone"><?= e($visit['zone_name'] ?? '-') ?></p>
              <p class="history-meta"><?= e(date('g:i A', strtotime($visit['start_time']))) ?> | <?= e((string) $visit['party_size']) ?> Guests | #<?= e((string) $visit['appointment_id']) ?></p>
            </div>
            <span class="badge <?= e($badgeClass) ?>"><?= e($badgeLabel) ?></span>
          </div>
          <?php endforeach; ?>
        </div>

        <?php endif; ?>
      </div>

    </div>
  </main>

</div>

<script src="<?= $basePath ?>js/dashboard.js"></script>
</body>
</html>
