<?php
/**
 * Dashboard Dining History — pages/dashboard/history.php
 */

require_once '../../includes/security.php';
start_secure_session();
require_login();

$pageTitle       = 'Dining History';
$pageCSS         = ['dashboard.css'];
$currentDashPage = 'history';
$basePath        = '../../';

$dashError = get_flash('dash_error');
$history = [];

try {
  $pdo = db();
  $stmt = $pdo->prepare("SELECT appointment_id, zone_name, appointment_date, start_time, party_size FROM vw_appointments_detail WHERE user_id = :uid AND status_id = (SELECT status_id FROM appointment_status WHERE status_name = 'completed' LIMIT 1) ORDER BY appointment_date DESC, start_time DESC");
  $stmt->execute([':uid' => (int) $_SESSION['user_id']]);
  $history = $stmt->fetchAll() ?: [];
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
      <?php endif; ?>

      <header class="dashboard-header">
        <div class="dashboard-header-row">
          <div>
            <h1 class="dashboard-page-title">Dining History</h1>
            <p class="dashboard-page-subtitle">A record of all your past visits at Eudaimonia.</p>
          </div>
        </div>
      </header>

      <div class="dash-section">
        <div class="dash-section-header">
          <h2 class="dash-section-title">Past Visits</h2>
          <span class="badge badge-secondary"><?= e((string) count($history)) ?> visits</span>
        </div>

        <?php if (empty($history)): ?>
        <div class="empty-state">
          <svg class="empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <h3 class="empty-state-title">No dining history yet</h3>
          <p class="empty-state-text">Your completed reservations will appear here after your first visit.</p>
        </div>
        <?php else: ?>

        <div class="reservation-list">
          <?php foreach ($history as $visit): ?>
          <div class="history-row">
            <div class="reservation-date-block">
              <span class="reservation-date-day"><?= e(date('d', strtotime($visit['appointment_date']))) ?></span>
              <span class="reservation-date-month"><?= e(date('M', strtotime($visit['appointment_date']))) ?></span>
            </div>
            <div class="history-body">
              <p class="history-zone"><?= e($visit['zone_name'] ?? '—') ?></p>
              <p class="history-meta"><?= e(date('g:i A', strtotime($visit['start_time']))) ?> · <?= e((string) $visit['party_size']) ?> Guests · #<?= e((string) $visit['appointment_id']) ?></p>
            </div>
            <span class="badge badge-confirmed">Completed</span>
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

