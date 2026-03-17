<?php
/**
 * Main Dining Room — Zone Detail Page
 * Hero, Overview + Sidebar, Table Cards, CTA
 */

$pageTitle   = 'Main Dining Room';
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
    <img src="<?= $basePath ?>assets/images/zone-dining.jpg" alt="Main Dining Room at Eudaimonia">
  </div>
  <div class="zone-detail-hero-overlay"></div>
  <div class="zone-detail-hero-content container">
    <a href="index.php" class="zone-back-link">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
      All Dining Zones
    </a>
    <p class="section-label">Timeless Sophistication</p>
    <h1>Main Dining Room</h1>
  </div>
</section>

<!-- Overview + Details Sidebar -->
<section class="section-lg">
  <div class="container">
    <div class="zone-overview-grid">
      <div class="zone-overview-text">
        <h2>The Heart of Eudaimonia</h2>
        <div class="zone-overview-paragraphs">
          <p>Our Main Dining Room is where the Eudaimonia experience reaches its fullest expression. This magnificent space, anchored by soaring ceilings and illuminated by bespoke crystal chandeliers, sets the stage for extraordinary culinary journeys.</p>
          <p>The room's design balances grandeur with intimacy. Rich wood paneling, sumptuous velvet seating, and carefully curated artwork create an atmosphere that feels both celebratory and comfortable. Tables are thoughtfully spaced to ensure privacy while maintaining the energy of a world-class dining destination.</p>
          <p>Our open kitchen concept allows guests to witness Chef Marchetti and her team in action—a ballet of precision and creativity that adds another dimension to the dining experience.</p>
        </div>
      </div>

      <div class="zone-details-sidebar">
        <h3>Details</h3>
        <div class="zone-detail-rows">
          <div class="zone-detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <div>
              <p>Capacity</p>
              <p>80 guests maximum</p>
            </div>
          </div>
          <div class="zone-detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
            <div>
              <p>Hours</p>
              <p>Tue–Thu: 5–10 PM, Fri–Sat: 5–11 PM</p>
            </div>
          </div>
          <div class="zone-detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
            <div>
              <p>Service</p>
              <p>Dinner service nightly</p>
            </div>
          </div>
          <div class="zone-detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/><line x1="6" y1="17" x2="18" y2="17"/></svg>
            <div>
              <p>Kitchen</p>
              <p>Open kitchen concept</p>
            </div>
          </div>
          <div class="zone-detail-row">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3-1.912 5.813a2 2 0 0 1-1.275 1.275L3 12l5.813 1.912a2 2 0 0 1 1.275 1.275L12 21l1.912-5.813a2 2 0 0 1 1.275-1.275L21 12l-5.813-1.912a2 2 0 0 1-1.275-1.275L12 3Z"/></svg>
            <div>
              <p>Ambiance</p>
              <p>Crystal chandeliers, wood paneling</p>
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
          <h3>Chef's View</h3>
          <span>2 seats</span>
        </div>
        <p>Front-row seats to our open kitchen</p>
      </div>
      <div class="zone-table-card">
        <div class="zone-table-card-header">
          <h3>Window Table</h3>
          <span>4 seats</span>
        </div>
        <p>Elegant seating with street views</p>
      </div>
      <div class="zone-table-card">
        <div class="zone-table-card-header">
          <h3>Banquette</h3>
          <span>6 seats</span>
        </div>
        <p>Comfortable booth seating in our main area</p>
      </div>
      <div class="zone-table-card">
        <div class="zone-table-card-header">
          <h3>Fireplace</h3>
          <span>4 seats</span>
        </div>
        <p>Cozy setting near our marble fireplace</p>
      </div>
      <div class="zone-table-card">
        <div class="zone-table-card-header">
          <h3>Private Alcove</h3>
          <span>8 seats</span>
        </div>
        <p>Semi-private space for larger parties</p>
      </div>
      <div class="zone-table-card">
        <div class="zone-table-card-header">
          <h3>Chandelier</h3>
          <span>4 seats</span>
        </div>
        <p>Centered beneath our signature crystal chandelier</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="zone-cta">
  <div class="container">
    <h2>Reserve the Main Dining Room</h2>
    <p>Experience the full grandeur of Eudaimonia in our signature dining space.</p>
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
