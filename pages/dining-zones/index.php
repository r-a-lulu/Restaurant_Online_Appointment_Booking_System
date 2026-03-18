<?php
/**
 * Dining Zones Index — Eudaimonia Restaurant
 * Hero, 3 alternating zone rows, private events CTA
 */

$pageTitle   = 'Dining Zones';
$pageCSS     = ['about.css', 'dining-zones.css'];
$currentPage = 'dining-zones';
$navStyle    = 'solid';
$basePath    = '../../';

include '../../includes/header.php';
include '../../includes/nav.php';
?>

<!-- Page Hero -->
<section class="page-hero">
  <div class="container">
    <div class="page-hero-inner">
      <p class="section-label">Spaces</p>
      <h1>Dining Zones</h1>
      <p>Three distinct atmospheres, each designed to create the perfect setting for your dining experience.</p>
    </div>
  </div>
</section>

<!-- Zones Alternating Rows -->
<section class="section">
  <div class="container">
    <div class="zone-rows">

      <!-- The Patio (image left) -->
      <div class="zone-row">
        <div class="zone-row-img">
        <img src="<?= $basePath ?>assets/images/zones/zone-patio.jpg" alt="The Patio Dining Zone">
      </div>
        <div class="zone-row-text">
          <div>
            <p class="section-label">Al Fresco Elegance</p>
            <h2>The Patio</h2>
          </div>
          <p>Nestled within our lush garden courtyard, The Patio offers an enchanting outdoor dining experience. Surrounded by climbing vines and fragrant blooms, guests enjoy seasonal cuisine under the open sky or our retractable canopy.</p>
          <div class="zone-features">
            <div class="zone-feature">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              Capacity: 40 guests
            </div>
            <div class="zone-feature">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
              Weather-protected
            </div>
            <div class="zone-feature">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              Available April – October
            </div>
          </div>
          <div>
            <a href="patio.php" class="btn btn-outline">
              View Details
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
          </div>
        </div>
      </div>

      <!-- Main Dining Room (image right) -->
      <div class="zone-row zone-row--reverse">
        <div class="zone-row-img">
        <img src="<?= $basePath ?>assets/images/zones/zone-dining.jpg" alt="Main Dining Room">
      </div>
        <div class="zone-row-text">
          <div>
            <p class="section-label">Timeless Sophistication</p>
            <h2>Main Dining Room</h2>
          </div>
          <p>Our signature dining space embodies the essence of Eudaimonia. Crystal chandeliers cast a warm glow over intimate tables, while our open kitchen allows guests to witness culinary artistry in action. This is where memories are made.</p>
          <div class="zone-features">
            <div class="zone-feature">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              Capacity: 80 guests
            </div>
            <div class="zone-feature">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
              Dinner service nightly
            </div>
            <div class="zone-feature">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              Available year-round
            </div>
          </div>
          <div>
            <a href="dining-room.php" class="btn btn-outline">
              View Details
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
          </div>
        </div>
      </div>

      <!-- The Bar (image left) -->
      <div class="zone-row">
        <div class="zone-row-img">
        <img src="<?= $basePath ?>assets/images/zones/zone-bar.jpg" alt="The Bar Lounge">
      </div>
        <div class="zone-row-text">
          <div>
            <p class="section-label">Intimate Indulgence</p>
            <h2>The Bar</h2>
          </div>
          <p>A sophisticated retreat for those seeking an elevated cocktail experience. Our bar features a curated selection of rare spirits, craft cocktails, and an expertly chosen wine list, complemented by an inventive small plates menu.</p>
          <div class="zone-features">
            <div class="zone-feature">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              Capacity: 24 guests
            </div>
            <div class="zone-feature">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M8 22h8M7 10h10l-2 7H9z"/><path d="M12 10V3M5 3h14"/></svg>
              Full cocktail service
            </div>
            <div class="zone-feature">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              Opens at 4pm daily
            </div>
          </div>
          <div>
            <a href="bar.php" class="btn btn-outline">
              View Details
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>

<!-- Private Events CTA -->
<section class="private-events-cta">
  <div class="container">
    <div class="private-events-cta-inner">
      <p class="section-label">Private Events</p>
      <h2>Host Your Special Occasion</h2>
      <p>Each of our dining zones can be reserved for private events. From intimate dinners to corporate gatherings, our team will create a bespoke experience tailored to your needs.</p>
      <a href="#" onclick="alert('Booking page coming in Phase 4'); return false;" class="btn btn-primary btn-lg">
        Inquire About Events
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
      </a>
    </div>
  </div>
</section>

<?php include '../../includes/footer.php'; ?>

<script src="<?= $basePath ?>js/nav.js"></script>
</body>
</html>
