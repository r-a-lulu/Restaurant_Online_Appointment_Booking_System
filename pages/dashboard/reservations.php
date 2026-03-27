<?php
/**
 * Dashboard Reservations - pages/dashboard/reservations.php
 */

require_once '../../includes/security.php';
start_secure_session();
require_login();

$pageTitle = 'My Reservations';
$pageCSS = ['dashboard.css'];
$currentDashPage = 'reservations';
$basePath = '../../';

$dashError = get_flash('dash_error');
$dashSuccess = get_flash('dash_success');

try {
  $pdo = db();
  $stmt = $pdo->prepare("SELECT appointment_id, zone_name, appointment_date, start_time, party_size, status_name FROM vw_appointments_detail WHERE user_id = :uid AND status_name IN ('pending', 'confirmed') ORDER BY appointment_date DESC, start_time DESC");
  $stmt->execute([':uid' => (int) $_SESSION['user_id']]);
  $allReservations = $stmt->fetchAll() ?: [];
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
            <h1 class="dashboard-page-title">My Reservations</h1>
            <p class="dashboard-page-subtitle">View and manage your active reservations.</p>
          </div>
          <a href="../book.php" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Reservation
          </a>
        </div>
      </header>

      <div class="dash-section">
        <div class="reservation-list" style="margin-top: 1rem;">
          <?php foreach ($allReservations as $row): ?>
            <?php
              $sName = $row['status_name'];
              $badgeClass = $sName === 'confirmed' ? 'badge-confirmed' : 'badge-pending';
              $badgeLabel = $sName === 'confirmed' ? 'Upcoming' : 'Pending';
            ?>
          <div class="reservation-row">
            <div class="reservation-date-block">
              <span class="reservation-date-day"><?= e(date('d', strtotime($row['appointment_date']))) ?></span>
              <span class="reservation-date-month"><?= e(date('M', strtotime($row['appointment_date']))) ?></span>
            </div>
            <div class="reservation-info">
              <p class="reservation-zone"><?= e($row['zone_name'] ?? '-') ?></p>
              <p class="reservation-meta"><?= e(date('g:i A', strtotime($row['start_time']))) ?> | <?= e((string) $row['party_size']) ?> Guests | #<?= e((string) $row['appointment_id']) ?></p>
            </div>
            <span class="badge <?= $badgeClass ?>"><?= e($badgeLabel) ?></span>
            <div class="reservation-actions">
              <form method="post" action="<?= $basePath ?>actions.php?action=user_cancel_booking">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action_token" value="<?= e(action_token('user_cancel_booking')) ?>">
                <input type="hidden" name="appointment_id" value="<?= e($row['appointment_id']) ?>">
                <button class="btn btn-outline btn-sm" type="submit" data-confirm="Are you sure you want to cancel this reservation?">Cancel</button>
              </form>
            </div>
          </div>
          <?php endforeach; ?>
          <?php if (empty($allReservations)): ?>
            <div class="reservation-row">
              <div class="reservation-info">
                <p class="reservation-zone">You have no active reservations right now.</p>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>

    </div>
  </main>

</div>

<script src="<?= $basePath ?>js/dashboard.js"></script>
</body>
</html>
