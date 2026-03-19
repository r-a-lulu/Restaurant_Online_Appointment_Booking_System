<?php
/**
 * Dashboard Book a Table — pages/dashboard/book.php
 */

$pageTitle       = 'Book a Table';
$pageCSS         = ['dashboard.css'];
$currentDashPage = 'book';
$basePath        = '../../';

include '../../includes/header.php';
?>
<body>
<div class="dashboard-layout" id="dashboardLayout">

  <?php include '../../includes/dashboard-sidebar.php'; ?>

  <main class="dashboard-main">
    <div class="dashboard-content" style="max-width: 72rem;">

      <header class="dashboard-header">
        <div class="dashboard-header-row" style="display: flex; justify-content: space-between; align-items: flex-start; gap: var(--space-4);">
          <div>
            <h1 class="dashboard-page-title">Book a Table</h1>
            <p class="dashboard-page-subtitle">Reserve your table at Eudaimonia.</p>
          </div>
          <button type="button" id="magicFillBook" class="btn btn-outline" style="font-size: var(--text-sm); padding: var(--space-2) var(--space-4);">
            ✨ Magic Fill
          </button>
        </div>
      </header>

      <!-- Zone Selection -->
      <div class="dash-section" style="margin-bottom: var(--space-6);">
        <div class="dash-section-header">
          <h2 class="dash-section-title">Select a Dining Zone</h2>
        </div>
        <div class="dash-section-body">
          <div class="zone-cards-grid">

            <div class="zone-card-select" data-zone="Patio" tabindex="0" role="button" aria-pressed="false">
              <img src="<?= $basePath ?>assets/images/zones/patio-hero.jpg" alt="Patio Outdoor Seating" class="zone-card-img">
              <div class="zone-card-body">
                <p class="zone-card-name">Patio</p>
                <p class="zone-card-cap">Outdoor seating &nbsp;·&nbsp; Up to 6 guests</p>
              </div>
            </div>

            <div class="zone-card-select" data-zone="Bar" tabindex="0" role="button" aria-pressed="false">
              <img src="<?= $basePath ?>assets/images/zones/bar-hero.jpg" alt="Bar &amp; Cocktail Lounge" class="zone-card-img">
              <div class="zone-card-body">
                <p class="zone-card-name">Bar</p>
                <p class="zone-card-cap">Cocktail lounge &nbsp;·&nbsp; Up to 4 guests</p>
              </div>
            </div>

            <div class="zone-card-select" data-zone="Dining Room" tabindex="0" role="button" aria-pressed="false">
              <img src="<?= $basePath ?>assets/images/zones/dining-room-hero.jpg" alt="Dining Room" class="zone-card-img">
              <div class="zone-card-body">
                <p class="zone-card-name">Dining Room</p>
                <p class="zone-card-cap">Fine dining &nbsp;·&nbsp; Up to 8 guests</p>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Seating Spot Sub-selection -->
      <div class="seating-reveal" id="seatingReveal" style="margin-bottom: var(--space-2);">
        <div class="seating-reveal-inner">
          <p class="seating-reveal-label">Seating Preference</p>
          <p class="seating-reveal-title" id="seatingRevealTitle">Choose your preferred spot</p>
          <div class="seating-spot-pills" id="seatingSpotPills"></div>
        </div>
      </div>

      <!-- Form + Summary -->
      <div class="book-form-grid">

        <!-- Form Fields -->
        <div class="dash-section">
          <div class="dash-section-header">
            <h2 class="dash-section-title">Reservation Details</h2>
          </div>
          <div class="dash-section-body">

            <div class="form-row" style="margin-bottom: var(--space-4);">
              <div class="form-group">
                <label for="bookDate" class="form-label">Date</label>
                <input type="date" id="bookDate" class="form-input"
                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
              </div>
              <div class="form-group">
                <label for="bookGuests" class="form-label">Number of Guests</label>
                <input type="number" id="bookGuests" class="form-input" min="1" max="8" value="2" placeholder="2">
              </div>
            </div>

            <div class="form-group" style="margin-bottom: var(--space-6);">
              <label class="form-label">Time Slot</label>
              <div class="time-slot-grid">
                <div class="time-slot unavailable">11:00 AM</div>
                <div class="time-slot">11:30 AM</div>
                <div class="time-slot">12:00 PM</div>
                <div class="time-slot">12:30 PM</div>
                <div class="time-slot unavailable">1:00 PM</div>
                <div class="time-slot">1:30 PM</div>
                <div class="time-slot">5:30 PM</div>
                <div class="time-slot">6:00 PM</div>
                <div class="time-slot unavailable">6:30 PM</div>
                <div class="time-slot">7:00 PM</div>
                <div class="time-slot">7:30 PM</div>
                <div class="time-slot">8:00 PM</div>
                <div class="time-slot unavailable">8:30 PM</div>
                <div class="time-slot">9:00 PM</div>
                <div class="time-slot">9:30 PM</div>
                <div class="time-slot">10:00 PM</div>
              </div>
              <p style="font-size: var(--text-xs); color: var(--clr-muted-fg); margin-top: var(--space-2);">
                Greyed-out slots are fully booked.
              </p>
            </div>

            <div class="form-group">
              <label for="bookNotes" class="form-label">Special Requests <span style="color: var(--clr-muted-fg); font-weight: 400;">(optional)</span></label>
              <textarea id="bookNotes" class="form-textarea" placeholder="Dietary restrictions, occasion, seating preferences…" rows="3"></textarea>
            </div>

          </div>
        </div>

        <!-- Summary Sidebar -->
        <div class="book-summary">
          <h3 class="book-summary-title">Reservation Summary</h3>
          <div class="book-summary-row">
            <span class="book-summary-key">Zone</span>
            <span class="book-summary-val" id="summaryZone">—</span>
          </div>
          <div class="book-summary-row">
            <span class="book-summary-key">Seating</span>
            <span class="book-summary-val" id="summarySpot">—</span>
          </div>
          <div class="book-summary-row">
            <span class="book-summary-key">Date</span>
            <span class="book-summary-val" id="summaryDate">—</span>
          </div>
          <div class="book-summary-row">
            <span class="book-summary-key">Time</span>
            <span class="book-summary-val" id="summaryTime">—</span>
          </div>
          <div class="book-summary-row">
            <span class="book-summary-key">Guests</span>
            <span class="book-summary-val" id="summaryGuests">2 Guests</span>
          </div>

          <a href="<?= $basePath ?>pages/book-confirmation.php" class="btn btn-primary btn-block" style="margin-top: var(--space-6);">
            Confirm Reservation
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
          </a>
          <p style="font-size: var(--text-xs); color: var(--clr-muted-fg); text-align: center; margin-top: var(--space-3);">
            You can cancel or modify your reservation up to 24 hours in advance.
          </p>
        </div>

      </div><!-- /.book-form-grid -->

    </div><!-- /.dashboard-content -->
  </main>

</div><!-- /.dashboard-layout -->

<script src="<?= $basePath ?>js/dashboard.js"></script>
</body>
</html>
