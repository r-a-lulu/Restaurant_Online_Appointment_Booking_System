<?php
/**
 * The Patio — Zone Detail Page
 * Hero, Overview + Sidebar, Table Cards, CTA
 */

$pageTitle   = 'The Patio';
$pageCSS     = ['dining-zones.css'];
$currentPage = 'dining-zones';
$navStyle    = 'transparent';
$basePath    = '../../';

include '../../includes/header.php';
include '../../includes/nav.php';
?>

<!-- Hero -->
<section class="zone-detail-hero">
  <div class="zone-detail-hero-bg">
    <img src="<?= $basePath ?>assets/images/zone-patio.jpg" alt="The Patio at Eudaimonia">
  </div>
  <div class="zone-detail-hero-overlay"></div>
  <div class="zone-detail-hero-content container">
    <a href="index.php" class="zone-back-link">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      All Dining Zones
    </a>
    <p class="section-label">Al Fresco Elegance</p>
    <h1>The Patio</h1>
  </div>
</section>

<!-- Overview + Details Sidebar -->
<section class="section-lg">
  <div class="container">
    <div class="zone-overview-grid">
      <div class="zone-overview-text">
        <h2>Dining Under the Stars</h2>
        <div class="zone-overview-paragraphs">
          <p>The Patio at Eudaimonia is a sanctuary of natural beauty and culinary excellence. Our garden courtyard has been meticulously designed to transport guests to a Mediterranean oasis, where every detail—from the hand-selected flora to the ambient lighting—creates an atmosphere of enchantment.</p>
          <p>During warm evenings, enjoy your meal al fresco surrounded by the gentle fragrance of jasmine and lavender. Our retractable canopy ensures comfort in any weather, while subtle heating elements extend the season well into autumn.</p>
          <p>The Patio menu celebrates seasonal ingredients at their peak, with lighter preparations that complement the outdoor setting without sacrificing the sophistication that defines Eudaimonia.</p>
        </div>
      </div>

      <div class="zone-details-sidebar">
        <h3>Details</h3>
        <div class="zone-detail-rows">
          <div class="zone-detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <div>
              <p>Capacity</p>
              <p>40 guests maximum</p>
            </div>
          </div>
          <div class="zone-detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <div>
              <p>Hours</p>
              <p>5:00 PM – 10:00 PM</p>
            </div>
          </div>
          <div class="zone-detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>
            <div>
              <p>Season</p>
              <p>April through October</p>
            </div>
          </div>
          <div class="zone-detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M23 12a11.05 11.05 0 0 0-22 0zm-5 7a3 3 0 0 1-6 0v-7"/></svg>
            <div>
              <p>Weather</p>
              <p>Covered with retractable canopy</p>
            </div>
          </div>
          <div class="zone-detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
            <div>
              <p>Ambiance</p>
              <p>Garden setting with ambient lighting</p>
            </div>
          </div>
        </div>
        <a href="#" onclick="alert('Booking page coming in Phase 4'); return false;" class="btn btn-primary btn-block">
          Reserve a Table
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Table Options -->
<section class="section-lg" style="background-color: var(--clr-muted);">
  <div class="container">
    <div style="max-width: 48rem; margin-inline: auto; text-align: center;" class="mb-12">
      <p class="section-label">Seating Options</p>
      <h2>Choose Your Perfect Table</h2>
    </div>
    <div class="zone-table-grid zone-table-grid--3col">
      <div class="zone-table-card">
        <div class="zone-table-card-header">
          <h3>Garden View</h3>
          <span>2 seats</span>
        </div>
        <p>Intimate table beside the rose garden</p>
      </div>
      <div class="zone-table-card">
        <div class="zone-table-card-header">
          <h3>Fountain Side</h3>
          <span>4 seats</span>
        </div>
        <p>Prime location near the centerpiece fountain</p>
      </div>
      <div class="zone-table-card">
        <div class="zone-table-card-header">
          <h3>Pergola</h3>
          <span>6 seats</span>
        </div>
        <p>Covered seating under our wisteria-draped pergola</p>
      </div>
      <div class="zone-table-card">
        <div class="zone-table-card-header">
          <h3>Corner Alcove</h3>
          <span>4 seats</span>
        </div>
        <p>Secluded spot perfect for private conversations</p>
      </div>
      <div class="zone-table-card">
        <div class="zone-table-card-header">
          <h3>Olive Grove</h3>
          <span>8 seats</span>
        </div>
        <p>Our largest outdoor table, ideal for gatherings</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="zone-cta">
  <div class="container">
    <h2>Reserve The Patio</h2>
    <p>Book your table in our enchanting garden courtyard for an unforgettable outdoor dining experience.</p>
    <a href="#" onclick="alert('Booking page coming in Phase 4'); return false;" class="btn btn-outline-light btn-lg">
      Make a Reservation
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
  </div>
</section>

<?php include '../../includes/footer.php'; ?>

<script src="<?= $basePath ?>js/nav.js"></script>
</body>
</html>
