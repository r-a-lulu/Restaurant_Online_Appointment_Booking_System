<?php
/**
 * Admin Dashboard Overview — pages/admin/index.php
 */

$pageTitle        = 'Admin Dashboard';
$pageCSS          = ['dashboard.css', 'admin.css'];
$currentAdminPage = 'dashboard';
$basePath         = '../../';

include '../../includes/header.php';
?>
<body>
<div class="admin-layout" id="adminLayout">

  <?php include '../../includes/admin-sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-content">

      <!-- Page Header -->
      <header class="admin-header">
        <div class="admin-header-row">
          <div>
            <h1 class="admin-page-title">Dashboard</h1>
            <p class="admin-page-subtitle">Welcome back, Marcus! Here's what's happening today.</p>
          </div>
        </div>
      </header>

      <!-- Stat Cards -->
      <div class="admin-stat-cards">

        <div class="admin-stat-card">
          <div class="admin-stat-card-body">
            <span class="admin-stat-label">Today's Reservations</span>
            <div class="admin-stat-value">24</div>
            <div class="admin-stat-trend admin-stat-trend--up">+3 from yesterday</div>
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
            <div class="admin-stat-value">5</div>
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
            <div class="admin-stat-value">86</div>
            <div class="admin-stat-trend admin-stat-trend--up">+12% vs avg</div>
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
            <div class="admin-stat-value">78%</div>
            <div class="admin-stat-trend admin-stat-trend--neutral">Peak hours: 7–9 PM</div>
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
            <h2 class="admin-section-title">Recent Reservations</h2>
            <a href="reservations.php" class="admin-view-all-link">
              View All
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
              </svg>
            </a>
          </div>
          <div class="admin-res-list">

            <div class="admin-res-row" data-status="pending">
              <div class="admin-res-info">
                <div class="admin-res-name-row">
                  <span class="admin-res-name">Sarah Johnson</span>
                  <span class="badge badge-pending">pending</span>
                </div>
                <p class="admin-res-meta">Main Dining Room &bull; 4 guests &bull; 7:30 PM</p>
              </div>
              <div class="admin-res-actions">
                <button class="admin-res-btn admin-res-btn--approve" data-confirm-action="approve" aria-label="Approve">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                  </svg>
                </button>
                <button class="admin-res-btn admin-res-btn--reject" data-confirm-action="reject" aria-label="Reject">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                  </svg>
                </button>
              </div>
            </div>

            <div class="admin-res-row" data-status="confirmed">
              <div class="admin-res-info">
                <div class="admin-res-name-row">
                  <span class="admin-res-name">Michael Brown</span>
                  <span class="badge badge-confirmed">confirmed</span>
                </div>
                <p class="admin-res-meta">The Bar &bull; 2 guests &bull; 6:00 PM</p>
              </div>
            </div>

            <div class="admin-res-row" data-status="pending">
              <div class="admin-res-info">
                <div class="admin-res-name-row">
                  <span class="admin-res-name">Emily Davis</span>
                  <span class="badge badge-pending">pending</span>
                </div>
                <p class="admin-res-meta">The Patio &bull; 6 guests &bull; 8:00 PM</p>
              </div>
              <div class="admin-res-actions">
                <button class="admin-res-btn admin-res-btn--approve" data-confirm-action="approve" aria-label="Approve">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
                  </svg>
                </button>
                <button class="admin-res-btn admin-res-btn--reject" data-confirm-action="reject" aria-label="Reject">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                  </svg>
                </button>
              </div>
            </div>

            <div class="admin-res-row" data-status="confirmed">
              <div class="admin-res-info">
                <div class="admin-res-name-row">
                  <span class="admin-res-name">James Wilson</span>
                  <span class="badge badge-confirmed">confirmed</span>
                </div>
                <p class="admin-res-meta">Main Dining Room &bull; 2 guests &bull; 7:00 PM</p>
              </div>
            </div>

          </div>
        </div>

        <!-- Zone Status -->
        <div class="admin-section">
          <div class="admin-section-header">
            <h2 class="admin-section-title">Zone Status</h2>
          </div>
          <div class="admin-section-body">

            <div class="zone-status-list">

              <div class="zone-status-item">
                <div class="zone-status-top">
                  <span class="zone-status-name">Main Dining Room</span>
                  <span class="zone-status-count">14/20 tables</span>
                </div>
                <div class="occupancy-track">
                  <div class="occupancy-fill" style="width:70%"></div>
                </div>
                <p class="zone-status-avail">6 tables available</p>
              </div>

              <div class="zone-status-item">
                <div class="zone-status-top">
                  <span class="zone-status-name">The Patio</span>
                  <span class="zone-status-count">6/10 tables</span>
                </div>
                <div class="occupancy-track">
                  <div class="occupancy-fill" style="width:60%"></div>
                </div>
                <p class="zone-status-avail">4 tables available</p>
              </div>

              <div class="zone-status-item">
                <div class="zone-status-top">
                  <span class="zone-status-name">The Bar</span>
                  <span class="zone-status-count">5/6 tables</span>
                </div>
                <div class="occupancy-track">
                  <div class="occupancy-fill" style="width:83%"></div>
                </div>
                <p class="zone-status-avail">1 table available</p>
              </div>

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

<!-- Reservation Detail Modal -->
<div class="admin-modal" id="reservationDetailModal">
  <div class="admin-modal-card">
    <div class="admin-modal-header">
      <h2 class="admin-modal-title">Reservation Detail</h2>
      <button class="admin-modal-close" data-modal-close aria-label="Close">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <dl class="admin-modal-rows">
      <div class="admin-modal-row"><dt>Guest Name</dt><dd id="detailGuest">—</dd></div>
      <div class="admin-modal-row"><dt>Dining Zone</dt><dd id="detailZone">—</dd></div>
      <div class="admin-modal-row"><dt>Date</dt><dd id="detailDate">—</dd></div>
      <div class="admin-modal-row"><dt>Time</dt><dd id="detailTime">—</dd></div>
      <div class="admin-modal-row"><dt>Guests</dt><dd id="detailGuests">—</dd></div>
      <div class="admin-modal-row"><dt>Status</dt><dd><span class="badge badge-confirmed" id="detailStatus">—</span></dd></div>
    </dl>
    <div class="admin-modal-footer">
      <button class="btn btn-outline" data-modal-close>Close</button>
    </div>
  </div>
</div>

<script src="<?= $basePath ?>js/admin.js"></script>
</body>
</html>
