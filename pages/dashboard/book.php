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

      <header class="dashboard-header" style="border-bottom:none; margin-bottom:var(--space-6); padding-bottom:0;">
        <div class="dashboard-header-row">
          <div>
            <h1 class="dashboard-page-title" style="font-family:var(--font-serif); font-size:2.5rem; color:#1a100d;">Book a Reservation</h1>
            <p class="dashboard-page-subtitle" style="font-size:1rem; color:#5c4e36;">Select your preferences to reserve a table</p>
          </div>
        </div>
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
                
                <div class="zone-card-select custom-selected" data-zone="The Patio" tabindex="0" role="button" aria-pressed="true">
                  <img src="<?= $basePath ?>assets/images/zones/patio-hero.jpg" alt="Patio Outdoor Seating" class="zone-card-img">
                  <div class="zone-card-overlay">
                    <p class="zone-card-name">The Patio</p>
                    <p class="zone-card-cap">2-8 guests</p>
                  </div>
                  <div class="zc-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                </div>

                <div class="zone-card-select" data-zone="Main Dining Room" tabindex="0" role="button" aria-pressed="false">
                  <img src="<?= $basePath ?>assets/images/zones/dining-room-hero.jpg" alt="Dining Room" class="zone-card-img">
                  <div class="zone-card-overlay">
                    <p class="zone-card-name">Main Dining Room</p>
                    <p class="zone-card-cap">2-8 guests</p>
                  </div>
                  <div class="zc-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                </div>

                <div class="zone-card-select" data-zone="The Bar" tabindex="0" role="button" aria-pressed="false">
                  <img src="<?= $basePath ?>assets/images/zones/bar-hero.jpg" alt="Bar &amp; Cocktail Lounge" class="zone-card-img">
                  <div class="zone-card-overlay">
                    <p class="zone-card-name">The Bar</p>
                    <p class="zone-card-cap">1-6 guests</p>
                  </div>
                  <div class="zc-check"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg></div>
                </div>

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
              
              <!-- Party Size (Moved here) -->
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
                  <input type="date" id="bookDate" class="form-input custom-soft-select"
                         min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
                </div>
              </div>

              <!-- Time Slot Grid -->
              <div class="form-group" style="margin-bottom:0;">
                <label class="form-label" style="font-family:var(--font-sans); font-size:0.9rem; font-weight:500; color:#5c4e36;">Select Time</label>
                <div class="time-slot-grid" style="margin-top:var(--space-2);">
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
              <textarea id="bookNotes" class="form-textarea custom-soft-area" placeholder="Allergies, special occasions, seating preferences..." rows="3"></textarea>
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
              <strong class="si-val">2 Guests</strong>
            </div>
          </div>

          <div class="summary-item">
            <svg class="summary-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            <div class="summary-item-text">
              <span class="si-lbl">Dining Zone</span>
              <strong class="si-val">The Patio</strong>
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
              <strong class="si-val">Saturday, March 28, 2026</strong>
            </div>
          </div>

          <div class="summary-item">
            <svg class="summary-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            <div class="summary-item-text">
              <span class="si-lbl">Time</span>
              <strong class="si-val">5:30 PM</strong>
            </div>
          </div>

          <a href="<?= $basePath ?>pages/book-confirmation.php" class="btn-confirm-res">
            Confirm Reservation &rarr;
          </a>
          <p style="font-size: 0.75rem; color: #7b6d5f; text-align: center; margin-top: var(--space-3); font-weight: 400;">
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
