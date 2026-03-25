<?php
/**
 * Booking Confirmation - Restaurant
 * Success state with reservation details loaded from DB.
 */

$pageTitle   = 'Reservation Confirmed';
$pageCSS     = ['book.css'];
$currentPage = 'book';
$navStyle    = 'solid';
$basePath    = '../';

require_once '../includes/security.php';
start_secure_session();

if (!isset($_SESSION['user_id'])) {
  header('Location: ' . $basePath . 'pages/login.php');
  exit;
}

$siteName = get_setting('restaurant_name', 'Eudaimonia');
$hideLogout = (isset($_GET['source']) && $_GET['source'] === 'dashboard');
$fromDashboard = (isset($_GET['source']) && $_GET['source'] === 'dashboard');
$contactPhone = get_setting('restaurant_phone', '(555) 123-4567');
$contactEmail = get_setting('restaurant_email', 'hello@eudaimonia.com');
$confirmationNoteTemplate = get_setting(
  'reservation_confirmation_note',
  'A confirmation email will be sent to your address within 1 hour. If you have any questions, please call us at {phone} or email {email}.'
);
$confirmationNote = strtr($confirmationNoteTemplate, [
  '{phone}' => $contactPhone,
  '{email}' => $contactEmail,
]);

$appointment = [];
$confError = '';

$appointmentId = isset($_GET['appointment_id']) ? (int) $_GET['appointment_id'] : 0;
if ($appointmentId > 0) {
  try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT appointment_id, appointment_date, start_time, party_size, status_name, zone_name, customer_name, customer_email, special_requests FROM vw_appointments_detail WHERE appointment_id = :id AND user_id = :uid LIMIT 1');
    $stmt->execute([':id' => $appointmentId, ':uid' => (int) $_SESSION['user_id']]);
    $appointment = $stmt->fetch() ?: [];
    $stmt->closeCursor();
  } catch (PDOException $e) {
    $confError = safe_error_message($e);
  }
}

include '../includes/header.php';
include '../includes/nav.php';
?>

<div class="confirm-page">
  <div class="container" id="confirmation-page">

    <div class="confirm-card">
      <div class="confirm-card-top">
        <div class="confirm-success-icon">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 6 9 17l-5-5"/>
          </svg>
        </div>

        <h1>Request Received!</h1>
        <p>Thank you for choosing <?= e($siteName) ?>. Your reservation request has been submitted.</p>

        <span class="confirm-ref" id="conf-ref">EUD-<?= e((string) ($appointment['appointment_id'] ?? 'XXXXXXXX')) ?></span>
      </div>

      <div class="confirm-card-body">
        <?php if ($confError): ?>
          <div class="auth-alert"><span><?= e($confError) ?></span></div>
        <?php endif; ?>

        <div class="confirm-details">
          <div class="confirm-detail-row">
            <div class="confirm-detail-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div class="confirm-detail-text">
              <span class="confirm-detail-label">Guest</span>
              <span class="confirm-detail-value" id="conf-name"><?= e($appointment['customer_name'] ?? '') ?></span>
            </div>
          </div>

          <div class="confirm-detail-row">
            <div class="confirm-detail-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="confirm-detail-text">
              <span class="confirm-detail-label">Party Size</span>
              <span class="confirm-detail-value" id="conf-guests"><?= e((string) ($appointment['party_size'] ?? '')) ?></span>
            </div>
          </div>

          <div class="confirm-detail-row">
            <div class="confirm-detail-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <div class="confirm-detail-text">
              <span class="confirm-detail-label">Dining Zone</span>
              <span class="confirm-detail-value" id="conf-zone"><?= e($appointment['zone_name'] ?? '') ?></span>
            </div>
          </div>

          <div class="confirm-detail-row">
            <div class="confirm-detail-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            </div>
            <div class="confirm-detail-text">
              <span class="confirm-detail-label">Date</span>
              <span class="confirm-detail-value" id="conf-date"><?= $appointment ? e(date('F j, Y', strtotime($appointment['appointment_date']))) : '' ?></span>
            </div>
          </div>

          <div class="confirm-detail-row">
            <div class="confirm-detail-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="confirm-detail-text">
              <span class="confirm-detail-label">Time</span>
              <span class="confirm-detail-value" id="conf-time"><?= $appointment ? e(date('g:i A', strtotime($appointment['start_time']))) : '' ?></span>
            </div>
          </div>
        </div>

        <div class="confirm-actions" style="margin-top: var(--space-6);">
          <?php if ($fromDashboard): ?>
            <a href="<?= $basePath ?>pages/dashboard/index.php" class="btn btn-primary">Return to Dashboard</a>
            <a href="<?= $basePath ?>pages/dashboard/reservations.php" class="btn btn-outline">View My Reservations</a>
          <?php else: ?>
            <a href="<?= $basePath ?>pages/dashboard/reservations.php" class="btn btn-primary">View My Reservations</a>
            <a href="<?= $basePath ?>pages/dashboard/index.php" class="btn btn-outline">Return to Dashboard</a>
          <?php endif; ?>
        </div>

        <p class="confirm-note" style="margin-top: var(--space-5);">
          <?= nl2br(e($confirmationNote)) ?>
        </p>
      </div>
    </div>
  </div>
</div>

<?php include '../includes/footer.php'; ?>
<script src="<?= $basePath ?>js/nav.js"></script>
</body>
</html>
