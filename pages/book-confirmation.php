<?php
/**
 * Booking Confirmation — Eudaimonia Restaurant
 * Success state with reservation details passed via URL params.
 * Allows guests to view their reservation in the dashboard.
 */

$pageTitle   = 'Reservation Confirmed';
$pageCSS     = ['book.css'];
$currentPage = 'book';
$navStyle    = 'solid';
$basePath    = '../';

require_once '../includes/security.php';
start_secure_session();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
  redirect('/pages/login.php');
}

include '../includes/header.php';
include '../includes/nav.php';
?>

<div class="confirm-page">
  <div class="container" id="confirmation-page">

    <div class="confirm-card">

      <!-- ===== TOP: Success Banner ===== -->
      <div class="confirm-card-top">
        <div class="confirm-success-icon">
          <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M20 6 9 17l-5-5"/>
          </svg>
        </div>

        <h1>Request Received!</h1>
        <p>Thank you for choosing Eudaimonia. Your reservation request has been submitted.</p>

        <span class="confirm-ref" id="conf-ref">EUD-XXXXXXXX</span>
      </div>

      <!-- ===== BODY: Reservation Details ===== -->
      <div class="confirm-card-body">

        <div class="confirm-details">

          <div class="confirm-detail-row">
            <div class="confirm-detail-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
            <div class="confirm-detail-text">
              <span class="confirm-detail-label">Guest</span>
              <span class="confirm-detail-value" id="conf-name">—</span>
            </div>
          </div>

          <div class="confirm-detail-row">
            <div class="confirm-detail-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
            <div class="confirm-detail-text">
              <span class="confirm-detail-label">Party Size</span>
              <span class="confirm-detail-value" id="conf-guests">—</span>
            </div>
          </div>

          <div class="confirm-detail-row">
            <div class="confirm-detail-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
            </div>
            <div class="confirm-detail-text">
              <span class="confirm-detail-label">Dining Zone</span>
              <span class="confirm-detail-value" id="conf-zone">—</span>
            </div>
          </div>

          <div class="confirm-detail-row">
            <div class="confirm-detail-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
            </div>
            <div class="confirm-detail-text">
              <span class="confirm-detail-label">Date</span>
              <span class="confirm-detail-value" id="conf-date">—</span>
            </div>
          </div>

          <div class="confirm-detail-row">
            <div class="confirm-detail-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            </div>
            <div class="confirm-detail-text">
              <span class="confirm-detail-label">Time</span>
              <span class="confirm-detail-value" id="conf-time">—</span>
            </div>
          </div>

          <div class="confirm-detail-row">
            <div class="confirm-detail-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
            </div>
            <div class="confirm-detail-text">
              <span class="confirm-detail-label">Confirmation Email</span>
              <span class="confirm-detail-value" id="conf-email">—</span>
            </div>
          </div>

          <div class="confirm-detail-row">
            <div class="confirm-detail-icon">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="M6 8h12M6 12h12M6 16h6"/></svg>
            </div>
            <div class="confirm-detail-text">
              <span class="confirm-detail-label">Total</span>
              <span class="confirm-detail-value" id="conf-total">—</span>
            </div>
          </div>

        </div><!-- /confirm-details -->

        <!-- Actions -->
        <div class="confirm-actions">
          <a href="<?= $basePath ?>pages/dashboard/reservations.php" class="btn btn-primary btn-lg btn-block" id="conf-dashboard-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            View My Reservations
          </a>
          <a href="<?= $basePath ?>index.php" class="btn btn-outline btn-lg btn-block" id="conf-home-btn">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
            Return to Home
          </a>
        </div>

        <p class="confirm-note">
          A confirmation email will be sent to your address within 1 hour. If you have any questions, please call us at <strong>(555) 123-4567</strong> or email <strong>hello@eudaimonia.com</strong>.
        </p>

      </div><!-- /confirm-card-body -->
    </div><!-- /confirm-card -->

  </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="<?= $basePath ?>js/nav.js"></script>
<script src="<?= $basePath ?>js/book.js"></script>
</body>
</html>
