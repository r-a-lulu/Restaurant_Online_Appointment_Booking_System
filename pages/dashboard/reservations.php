<?php
/**
 * Dashboard Reservations — pages/dashboard/reservations.php
 */

$pageTitle       = 'My Reservations';
$pageCSS         = ['dashboard.css'];
$currentDashPage = 'reservations';
$basePath        = '../../';

include '../../includes/header.php';
?>
<body>
<div class="dashboard-layout" id="dashboardLayout">

  <?php include '../../includes/dashboard-sidebar.php'; ?>

  <main class="dashboard-main">
    <div class="dashboard-content">

      <header class="dashboard-header">
        <div class="dashboard-header-row">
          <div>
            <h1 class="dashboard-page-title">My Reservations</h1>
            <p class="dashboard-page-subtitle">View and manage all your table reservations.</p>
          </div>
          <a href="book.php" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Reservation
          </a>
        </div>
      </header>

      <!-- Tabs -->
      <div class="dash-section">
        <div class="dash-section-header">
          <div class="tabs-container">
            <div class="tabs-list" role="tablist">
              <button class="tab-trigger active" data-tab="upcoming" role="tab" aria-selected="true">Upcoming</button>
              <button class="tab-trigger" data-tab="pending" role="tab" aria-selected="false">Pending</button>
              <button class="tab-trigger" data-tab="cancelled" role="tab" aria-selected="false">Cancelled</button>
            </div>
          </div>
        </div>

        <!-- Upcoming Tab -->
        <div class="tab-content active" data-tab-content="upcoming" role="tabpanel">
          <div class="reservation-list">

            <div class="reservation-row">
              <div class="reservation-date-block">
                <span class="reservation-date-day">24</span>
                <span class="reservation-date-month">Mar</span>
              </div>
              <div class="reservation-info">
                <p class="reservation-zone">Patio — Outdoor Seating</p>
                <p class="reservation-meta">7:00 PM &nbsp;·&nbsp; 2 Guests &nbsp;·&nbsp; #EU-2024-089</p>
              </div>
              <span class="badge badge-confirmed">Confirmed</span>
              <div class="reservation-actions">
                <button class="btn btn-outline btn-sm" data-action="cancel-reservation">Cancel</button>
              </div>
            </div>

            <div class="reservation-row">
              <div class="reservation-date-block">
                <span class="reservation-date-day">01</span>
                <span class="reservation-date-month">Apr</span>
              </div>
              <div class="reservation-info">
                <p class="reservation-zone">Dining Room — Private Table</p>
                <p class="reservation-meta">8:30 PM &nbsp;·&nbsp; 4 Guests &nbsp;·&nbsp; #EU-2024-091</p>
              </div>
              <span class="badge badge-confirmed">Confirmed</span>
              <div class="reservation-actions">
                <button class="btn btn-outline btn-sm" data-action="cancel-reservation">Cancel</button>
              </div>
            </div>

          </div>
        </div>

        <!-- Pending Tab -->
        <div class="tab-content" data-tab-content="pending" role="tabpanel">
          <div class="reservation-list">

            <div class="reservation-row">
              <div class="reservation-date-block">
                <span class="reservation-date-day">10</span>
                <span class="reservation-date-month">Apr</span>
              </div>
              <div class="reservation-info">
                <p class="reservation-zone">Bar — Cocktail Lounge</p>
                <p class="reservation-meta">6:00 PM &nbsp;·&nbsp; 3 Guests &nbsp;·&nbsp; #EU-2024-095</p>
              </div>
              <span class="badge badge-pending">Pending</span>
              <div class="reservation-actions">
                <button class="btn btn-ghost btn-sm" data-action="cancel-reservation">Cancel</button>
              </div>
            </div>

          </div>
        </div>

        <!-- Cancelled Tab -->
        <div class="tab-content" data-tab-content="cancelled" role="tabpanel">
          <div class="reservation-list">

            <div class="reservation-row">
              <div class="reservation-date-block">
                <span class="reservation-date-day">05</span>
                <span class="reservation-date-month">Feb</span>
              </div>
              <div class="reservation-info">
                <p class="reservation-zone">Dining Room — Chef's Table</p>
                <p class="reservation-meta">7:30 PM &nbsp;·&nbsp; 2 Guests &nbsp;·&nbsp; #EU-2024-072</p>
              </div>
              <span class="badge badge-cancelled">Cancelled</span>
              <div class="reservation-actions"></div>
            </div>

          </div>
        </div>

      </div><!-- /.dash-section (tabs) -->

    </div><!-- /.dashboard-content -->
  </main>

</div><!-- /.dashboard-layout -->

<!-- Cancel Modal -->
<div class="modal-overlay" id="cancelModal" role="dialog" aria-modal="true" aria-labelledby="cancelModalTitle">
  <div class="modal">
    <div class="modal-header">
      <h3 class="modal-title" id="cancelModalTitle">Cancel Reservation</h3>
      <p class="modal-description">Are you sure you want to cancel this reservation? This action cannot be undone.</p>
    </div>
    <div class="modal-footer">
      <button class="btn btn-outline" data-action="close-cancel-modal">Go Back</button>
      <button class="btn btn-destructive" id="cancelConfirm">Yes, Cancel</button>
    </div>
  </div>
</div>

<script src="<?= $basePath ?>js/dashboard.js"></script>
</body>
</html>
