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
  $stmt = $pdo->prepare("SELECT appointment_id, customer_email, service_name, package_name, zone_name, table_label, appointment_date, start_time, end_time, party_size, status_name, special_requests, created_at FROM vw_appointments_detail WHERE user_id = :uid AND status_name IN ('pending', 'confirmed') ORDER BY appointment_date DESC, start_time DESC");
  $stmt->execute([':uid' => (int) $_SESSION['user_id']]);
  $allReservations = $stmt->fetchAll() ?: [];
  $stmt->closeCursor();
} catch (PDOException $e) {
  error_log('Dashboard reservations load failed for user ' . (int) ($_SESSION['user_id'] ?? 0) . ': ' . $e->getMessage());
  $dashError = 'We could not load your reservations right now. Please try again.';
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
        <div class="dash-section-header">
          <h2 class="dash-section-title">Active Reservations</h2>
          <span class="badge badge-secondary">Tap a reservation to view details</span>
        </div>
        <div class="reservation-list" style="margin-top: 1rem;">
          <?php foreach ($allReservations as $row): ?>
            <?php
              $sName = $row['status_name'];
              $badgeClass = $sName === 'confirmed' ? 'badge-confirmed' : 'badge-pending';
              $badgeLabel = $sName === 'confirmed' ? 'Upcoming' : 'Pending';
            ?>
          <div
            class="reservation-row reservation-row--clickable"
            tabindex="0"
            role="button"
            aria-label="View details for reservation #<?= e((string) $row['appointment_id']) ?>"
            data-reservation-details="1"
            data-appointment-id="<?= e((string) $row['appointment_id']) ?>"
            data-zone-name="<?= e($row['zone_name'] ?? '-') ?>"
            data-start-time="<?= e((string) $row['start_time']) ?>"
            data-end-time="<?= e((string) $row['end_time']) ?>"
            data-party-size="<?= e((string) $row['party_size']) ?>"
            data-status-label="<?= e($badgeLabel) ?>"
            data-status-name="<?= e((string) $row['status_name']) ?>"
            data-appointment-date="<?= e((string) $row['appointment_date']) ?>"
            data-table-label="<?= e($row['table_label'] ?? 'Unassigned') ?>"
            data-service-name="<?= e($row['service_name'] ?? '') ?>"
            data-package-name="<?= e($row['package_name'] ?? '') ?>"
            data-customer-email="<?= e($row['customer_email'] ?? '') ?>"
            data-special-requests="<?= e($row['special_requests'] ?? '') ?>"
            data-created-at="<?= e((string) $row['created_at']) ?>">
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

      <div class="reservation-detail-modal" id="reservationDetailModal" aria-hidden="true">
        <div class="reservation-detail-card" role="dialog" aria-modal="true" aria-labelledby="reservationDetailTitle">
          <div class="reservation-detail-header">
            <div>
              <p class="reservation-detail-eyebrow">Reservation Details</p>
              <h2 class="reservation-detail-title" id="reservationDetailTitle">Reservation #<span id="reservationDetailId">-</span></h2>
            </div>
            <button type="button" class="reservation-detail-close" id="reservationDetailClose" aria-label="Close reservation details">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
          </div>

          <div class="reservation-detail-grid">
            <div class="reservation-detail-item">
              <span class="reservation-detail-label">Status</span>
              <span class="reservation-detail-value" id="reservationDetailStatus">-</span>
            </div>
            <div class="reservation-detail-item">
              <span class="reservation-detail-label">Dining Zone</span>
              <span class="reservation-detail-value" id="reservationDetailZone">-</span>
            </div>
            <div class="reservation-detail-item">
              <span class="reservation-detail-label">Date</span>
              <span class="reservation-detail-value" id="reservationDetailDate">-</span>
            </div>
            <div class="reservation-detail-item">
              <span class="reservation-detail-label">Time</span>
              <span class="reservation-detail-value" id="reservationDetailTime">-</span>
            </div>
            <div class="reservation-detail-item">
              <span class="reservation-detail-label">Party Size</span>
              <span class="reservation-detail-value" id="reservationDetailParty">-</span>
            </div>
            <div class="reservation-detail-item">
              <span class="reservation-detail-label">Seating</span>
              <span class="reservation-detail-value" id="reservationDetailTable">-</span>
            </div>
            <div class="reservation-detail-item">
              <span class="reservation-detail-label">Service</span>
              <span class="reservation-detail-value" id="reservationDetailService">-</span>
            </div>
            <div class="reservation-detail-item">
              <span class="reservation-detail-label">Package</span>
              <span class="reservation-detail-value" id="reservationDetailPackage">-</span>
            </div>
            <div class="reservation-detail-item">
              <span class="reservation-detail-label">Contact Email</span>
              <span class="reservation-detail-value" id="reservationDetailEmail">-</span>
            </div>
            <div class="reservation-detail-item">
              <span class="reservation-detail-label">Created</span>
              <span class="reservation-detail-value" id="reservationDetailCreated">-</span>
            </div>
          </div>

          <div class="reservation-detail-notes">
            <span class="reservation-detail-label">Special Requests</span>
            <p class="reservation-detail-notes-value" id="reservationDetailNotes">No special requests.</p>
          </div>
        </div>
      </div>

    </div>
  </main>

</div>

<script src="<?= $basePath ?>js/dashboard.js"></script>
</body>
</html>
