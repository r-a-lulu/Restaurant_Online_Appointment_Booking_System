<?php
/**
 * Dashboard Book a Table — pages/dashboard/book.php
 */

$pageTitle       = 'Book a Table';
$pageCSS         = ['dashboard.css'];
$currentDashPage = 'book';
$basePath        = '../../';

require_once '../../includes/security.php';
start_secure_session();
require_login();

$bookingError = get_flash('booking_error');
$bookingSuccess = get_flash('dash_success');

$zones = [];
$tables = [];
$serviceId = '';

$zoneMeta = [
  'The Patio' => [
    'image' => 'assets/images/zones/zone-patio.jpg',
    'capacity' => '2-8 guests',
    'slug' => 'patio',
  ],
  'Main Dining Room' => [
    'image' => 'assets/images/zones/zone-dining.jpg',
    'capacity' => '2-8 guests',
    'slug' => 'dining-room',
  ],
  'The Bar' => [
    'image' => 'assets/images/zones/zone-bar.jpg',
    'capacity' => '1-6 guests',
    'slug' => 'bar',
  ],
];

try {
  $pdo = db();

  // Get default service (e.g. Table Reservation)
  $stmt = $pdo->query("SELECT service_id FROM services ORDER BY price ASC LIMIT 1");
  $serviceId = $stmt->fetchColumn();

  $stmt = $pdo->query('SELECT zone_id, zone_name FROM dining_zones ORDER BY zone_name');
  $zones = $stmt->fetchAll();

  $stmt = $pdo->query('SELECT table_id, table_number, capacity, seating_preference, zone_id, zone_name FROM vw_available_tables');
  $tables = $stmt->fetchAll();
} catch (PDOException $e) {
  $bookingError = safe_error_message($e);
}

include '../../includes/header.php';
?>
<body>
<div class="dashboard-layout" id="dashboardLayout">

  <?php include '../../includes/dashboard-sidebar.php'; ?>

  <main class="dashboard-main">
    <form class="dashboard-content" id="dashboardBookingForm" method="post" action="<?= $basePath ?>actions.php?action=process_booking" style="max-width: 72rem;" novalidate>
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="action_token" value="<?= e(action_token('process_booking')) ?>">
      <input type="hidden" id="dash-check-availability-token" value="<?= e(action_token('check_availability')) ?>">
      <input type="hidden" name="service_id" value="<?= e($serviceId) ?>">
      <input type="hidden" name="zone_id" id="zone-id">
      <input type="hidden" name="table_id" id="table-id">
      <input type="hidden" name="start_time" id="start-time">
      <input type="hidden" name="special_requests" id="hidden-special-requests">
      <input type="hidden" name="zone_label" id="zone-label">
      <input type="hidden" name="date_label" id="date-label">
      <input type="hidden" name="time_label" id="time-label">
      <input type="hidden" name="source" value="dashboard">
      <input type="hidden" name="party_size" id="hidden-party-size" value="2">
      <input type="hidden" name="appointment_date" id="hidden-appointment-date" value="">

      <header class="dashboard-header" style="border-bottom:none; margin-bottom:var(--space-6); padding-bottom:0;">
        <div class="dashboard-header-row">
          <div>
            <h1 class="dashboard-page-title" style="font-family:var(--font-serif); font-size:2.5rem; color:#1a100d;">Book a Reservation</h1>
            <p class="dashboard-page-subtitle" style="font-size:1rem; color:#5c4e36;">Select your preferences to reserve a table</p>
          </div>
        </div>
        
        <?php if ($bookingError): ?>
          <div class="auth-alert" style="margin-top: var(--space-4);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span><?= e($bookingError) ?></span>
          </div>
        <?php endif; ?>

        <?php if ($bookingSuccess): ?>
          <div class="auth-alert auth-success" style="margin-top: var(--space-4);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span><?= e($bookingSuccess) ?></span>
          </div>
        <?php endif; ?>
      </header>

      <div class="book-form-grid" style="display: grid; grid-template-columns: 1fr 22rem; gap: var(--space-8); align-items: start;">
        
        <!-- Left Column: Steps -->
        <div class="book-steps" style="display: flex; flex-direction: column; gap: var(--space-6);">

          <!-- Step 1: Dining Zone -->
          <div class="dash-section" style="margin-bottom:0; border-color:#e5cd9e;">
            <div class="dash-section-header" style="border-bottom:none; padding-bottom:0;">
              <h2 class="dash-section-title" style="display:flex; align-items:center; gap:var(--space-3); color:#542125; font-size:1.25rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                Dining Zone
              </h2>
            </div>
            <div class="dash-section-body">
              <div class="zone-cards-grid">
                
                <?php foreach ($zones as $zone): ?>
                  <?php
                    $zoneName = $zone['zone_name'];
                    $meta = $zoneMeta[$zoneName] ?? $zoneMeta['Main Dining Room'];
                    $slug = $meta['slug'] ?? strtolower(preg_replace('/[^a-z0-9]+/i', '-', $zoneName));
                  ?>
                  <div class="zone-card-select" data-zone="<?= e($zoneName) ?>" data-zone-id="<?= e($zone['zone_id']) ?>" tabindex="0" role="button" aria-pressed="false">
                    <img src="<?= $basePath . e($meta['image']) ?>" alt="<?= e($zoneName) ?>" class="zone-card-img">
                    <div class="zone-card-overlay">
                      <p class="zone-card-name"><?= e($zoneName) ?></p>
                      <p class="zone-card-cap"><?= e($meta['capacity']) ?></p>
                    </div>
                    <div class="zc-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                  </div>
                <?php endforeach; ?>

              </div>
            </div>
          </div>

          <!-- Seating Spot Sub-selection -->
          <div class="seating-reveal" id="seatingReveal" style="margin-bottom: 0;">
            <div class="seating-reveal-inner" style="border-color:#e5cd9e;">
              <p class="seating-reveal-label" style="color:#542125;">Seating Preference</p>
              <p class="seating-reveal-title" id="seatingRevealTitle">Choose your preferred spot</p>
              <div class="seating-spot-pills" id="seatingSpotPills"></div>
            </div>
          </div>

          <!-- Step 2: Date & Time (& Party Size) -->
          <div class="dash-section" style="margin-bottom:0; border-color:#e5cd9e;">
            <div class="dash-section-header" style="border-bottom:none; padding-bottom:0;">
              <h2 class="dash-section-title" style="display:flex; align-items:center; gap:var(--space-3); color:#542125; font-size:1.25rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                Date &amp; Time
              </h2>
            </div>
            <div class="dash-section-body" style="display:flex; flex-direction:column; gap:var(--space-8);">
              
              <!-- Party Size -->
              <div class="form-group" style="margin-bottom:0;">
                <label for="bookGuests" class="form-label" style="font-family:var(--font-sans); font-size:0.9rem; font-weight:500; color:#5c4e36;">Party Size</label>
                <div style="width:140px; margin-top:var(--space-2);">
                  <input type="number" id="bookGuests" class="form-input custom-soft-select" min="1" max="8" value="2" placeholder="2">
                </div>
              </div>

              <!-- Date Picker Layout -->
              <div class="form-group" style="margin-bottom:0; max-width:180px;">
                <label for="bookDate" class="form-label" style="font-family:var(--font-sans); font-size:0.9rem; font-weight:500; color:#5c4e36;">Select Date</label>
                <div style="margin-top:var(--space-2);">
                  <input type="date" id="bookDate" class="form-input custom-soft-select" min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                </div>
              </div>

              <!-- Time Slot Grid -->
              <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-family:var(--font-sans); font-size:0.9rem; font-weight:500; color:#5c4e36;">Select Time</label>
                <div class="time-slot-grid" id="timeSlotContainer" style="margin-top:var(--space-2);">
                  <!-- Populated dynamically via JS -->
                  <p style="color:var(--clr-muted-fg); font-size:0.875rem;">Please select a zone and date to view available times.</p>
                </div>
                <p style="font-size: var(--text-xs); color: var(--clr-muted-fg); margin-top: var(--space-2);">
                  Times will update based on your selected table & party size.
                </p>
              </div>

            </div>
          </div>

          <!-- Step 3: Special Requests -->
          <div class="dash-section" style="margin-bottom:0; border-color:#e5cd9e;">
            <div class="dash-section-header" style="border-bottom:none; padding-bottom:0;">
              <h2 class="dash-section-title" style="display:flex; align-items:center; gap:var(--space-3); color:#542125; font-size:1.25rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><line x1="10" y1="9" x2="8" y2="9"></line></svg>
                Special Requests
              </h2>
            </div>
            <div class="dash-section-body">
              <textarea id="bookNotes" class="form-textarea custom-soft-area" placeholder="Allergies, special occasions, exact seating preferences if available..." rows="3"></textarea>
            </div>
          </div>

        </div>

        <!-- Right Side: Summary Sidebar -->
        <div class="book-summary-card">
          <h3 class="summary-card-title">Reservation Summary</h3>
          
          <div class="summary-item">
            <svg class="summary-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            <div class="summary-item-text">
              <span class="si-lbl">Party Size</span>
              <strong class="si-val" id="summaryGuests">2 Guests</strong>
            </div>
          </div>

          <div class="summary-item">
            <svg class="summary-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            <div class="summary-item-text">
              <span class="si-lbl">Dining Zone</span>
              <strong class="si-val" id="summaryZone">—</strong>
            </div>
          </div>

          <div class="summary-item">
            <svg class="summary-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
            <div class="summary-item-text">
              <span class="si-lbl">Seating</span>
              <strong class="si-val" id="summarySpot">—</strong>
            </div>
          </div>

          <div class="summary-item">
            <svg class="summary-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            <div class="summary-item-text">
              <span class="si-lbl">Date</span>
              <strong class="si-val" id="summaryDate">—</strong>
            </div>
          </div>

          <div class="summary-item">
            <svg class="summary-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            <div class="summary-item-text">
              <span class="si-lbl">Time</span>
              <strong class="si-val" id="summaryTime">—</strong>
            </div>
          </div>

          <button type="button" id="btnConfirmReservation" class="btn-confirm-res">
            Confirm Reservation &rarr;
          </button>
          <div id="formReviewError" style="display:none; color:var(--clr-destructive); font-size: 0.875rem; text-align:center; padding-top: var(--space-4);">Please fill out all required details.</div>
          <p style="font-size: 0.75rem; color: #7b6d5f; text-align: center; margin-top: var(--space-3); font-weight: 400;">
            You can cancel or modify your reservation up to 24 hours in advance.
          </p>
        </div>

      </div><!-- /.book-form-grid -->

    </form><!-- /.dashboard-content -->
  </main>

</div><!-- /.dashboard-layout -->

<script>
  window.DB_TABLES = <?= json_encode($tables) ?>;
</script>
<script src="<?= $basePath ?>js/dashboard.js"></script>
</body>
</html>
