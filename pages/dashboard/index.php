<?php
/**
 * Dashboard Overview — pages/dashboard/index.php
 */

$pageTitle       = 'My Dashboard';
$pageCSS         = ['dashboard.css'];
$currentDashPage = 'overview';
$basePath        = '../../';

include '../../includes/header.php';
?>
<body>
<div class="dashboard-layout" id="dashboardLayout">

  <?php include '../../includes/dashboard-sidebar.php'; ?>

  <!-- Main Content -->
  <main class="dashboard-main">
    <div class="dashboard-content">

      <!-- Header -->
      <header class="dashboard-header">
        <div class="dashboard-header-row">
          <div>
            <h1 class="dashboard-page-title">Welcome back, Jane!</h1>
            <p class="dashboard-page-subtitle">Here's an overview of your dining activity.</p>
          </div>
          <a href="book.php" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            Book a Table
          </a>
        </div>
      </header>

      <!-- Stat Cards -->
      <div class="stat-cards">
        <div class="stat-card">
          <div class="icon-circle">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
          </div>
          <div class="stat-card-body">
            <p class="stat-card-label">Total Reservations</p>
            <p class="stat-card-value">12</p>
            <p class="stat-card-sub">All time</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="icon-circle">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </div>
          <div class="stat-card-body">
            <p class="stat-card-label">Upcoming</p>
            <p class="stat-card-value">2</p>
            <p class="stat-card-sub">Confirmed</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="icon-circle">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          </div>
          <div class="stat-card-body">
            <p class="stat-card-label">Completed</p>
            <p class="stat-card-value">9</p>
            <p class="stat-card-sub">Visits</p>
          </div>
        </div>
        <div class="stat-card">
          <div class="icon-circle">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
          </div>
          <div class="stat-card-body">
            <p class="stat-card-label">Favourite Zone</p>
            <p class="stat-card-value" style="font-size: var(--text-xl);">Patio</p>
            <p class="stat-card-sub">Most visited</p>
          </div>
        </div>
      </div>

      <!-- Upcoming Reservations -->
      <div class="dash-section">
        <div class="dash-section-header">
          <h2 class="dash-section-title">Upcoming Reservations</h2>
          <a href="reservations.php" class="btn btn-outline btn-sm">View All</a>
        </div>
        <div class="reservation-list">

          <div class="reservation-row">
            <div class="reservation-date-block">
              <span class="reservation-date-day">24</span>
              <span class="reservation-date-month">Mar</span>
            </div>
            <div class="reservation-info">
              <p class="reservation-zone">Patio — Outdoor Seating</p>
              <p class="reservation-meta">7:00 PM &nbsp;·&nbsp; 2 Guests &nbsp;·&nbsp; Reservation #EU-2024-089</p>
            </div>
            <span class="badge badge-confirmed">Confirmed</span>
            <div class="reservation-actions">
              <a href="reservations.php" class="btn btn-outline btn-sm">Details</a>
            </div>
          </div>

          <div class="reservation-row">
            <div class="reservation-date-block">
              <span class="reservation-date-day">01</span>
              <span class="reservation-date-month">Apr</span>
            </div>
            <div class="reservation-info">
              <p class="reservation-zone">Dining Room — Private Table</p>
              <p class="reservation-meta">8:30 PM &nbsp;·&nbsp; 4 Guests &nbsp;·&nbsp; Reservation #EU-2024-091</p>
            </div>
            <span class="badge badge-pending">Pending</span>
            <div class="reservation-actions">
              <a href="reservations.php" class="btn btn-outline btn-sm">Details</a>
            </div>
          </div>

        </div>
      </div>

      <!-- Quick Actions -->
      <div class="dash-section">
        <div class="dash-section-header">
          <h2 class="dash-section-title">Quick Actions</h2>
        </div>
        <div class="dash-section-body">
          <div class="quick-actions">
            <a href="book.php" class="quick-action-card">
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

    </div><!-- /.dashboard-content -->
  </main>

</div><!-- /.dashboard-layout -->

<script src="<?= $basePath ?>js/dashboard.js"></script>
</body>
</html>
