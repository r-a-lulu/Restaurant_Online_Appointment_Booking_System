<?php
/**
 * Main Dining Room - Zone Detail Page
 */

$pageTitle   = 'Main Dining Room';
$pageCSS     = ['dining-zones.css'];
$currentPage = 'dining-zones';
$navStyle    = 'transparent';
$basePath    = '../../';

require_once '../../includes/security.php';
start_secure_session();
$siteName    = get_setting('restaurant_name', 'Eudaimonia');

include '../../includes/header.php';
include '../../includes/nav.php';
?>

<section class="zone-detail-hero">
  <div class="zone-detail-hero-bg">
    <img src="<?= $basePath ?>assets/images/zones/zone-dining.jpg" alt="Main Dining Room at <?= e($siteName) ?>">
  </div>
  <div class="zone-detail-hero-overlay"></div>
  <div class="zone-detail-hero-content container">
    <a href="index.php" class="zone-back-link">All Dining Zones</a>
    <p class="section-label">Timeless Sophistication</p>
    <h1>Main Dining Room</h1>
  </div>
</section>

<section class="section-lg">
  <div class="container">
    <div class="zone-overview-grid">
      <div class="zone-overview-text">
        <h2>The Heart of <?= e($siteName) ?></h2>
        <div class="zone-overview-paragraphs">
          <p>Our Main Dining Room is where the <?= e($siteName) ?> experience reaches its fullest expression. This magnificent space, anchored by soaring ceilings and illuminated by bespoke crystal chandeliers, sets the stage for extraordinary culinary journeys.</p>
          <p>The room's design balances grandeur with intimacy. Rich wood paneling, sumptuous velvet seating, and carefully curated artwork create an atmosphere that feels both celebratory and comfortable.</p>
          <p>Our open kitchen concept allows guests to witness Chef Marchetti and her team in action, a ballet of precision and creativity that adds another dimension to the dining experience.</p>
        </div>
      </div>

      <div class="zone-details-sidebar">
        <h3>Details</h3>
        <div class="zone-detail-rows">
          <div class="zone-detail-row"><div><p>Capacity</p><p>80 guests maximum</p></div></div>
          <div class="zone-detail-row"><div><p>Hours</p><p>Tue-Thu: 5-10 PM, Fri-Sat: 5-11 PM</p></div></div>
          <div class="zone-detail-row"><div><p>Service</p><p>Dinner service nightly</p></div></div>
          <div class="zone-detail-row"><div><p>Kitchen</p><p>Open kitchen concept</p></div></div>
          <div class="zone-detail-row"><div><p>Ambiance</p><p>Crystal chandeliers, wood paneling</p></div></div>
        </div>
        <a href="../../pages/book.php" class="btn btn-primary btn-block">Reserve a Table</a>
      </div>
    </div>
  </div>
</section>

<section class="section-lg" style="background-color: var(--clr-muted);">
  <div class="container">
    <div style="max-width: 48rem; margin-inline: auto; text-align: center;" class="mb-12">
      <p class="section-label">Seating Options</p>
      <h2>Choose Your Perfect Table</h2>
    </div>
    <div class="zone-table-grid zone-table-grid--3col">
      <div class="zone-table-card"><div class="zone-table-card-body"><div class="zone-table-card-header"><h3>Chef's View</h3><span>2 seats</span></div><p>Front-row seats to our open kitchen</p></div></div>
      <div class="zone-table-card"><div class="zone-table-card-body"><div class="zone-table-card-header"><h3>Window Table</h3><span>4 seats</span></div><p>Elegant seating with street views</p></div></div>
      <div class="zone-table-card"><div class="zone-table-card-body"><div class="zone-table-card-header"><h3>Banquette</h3><span>6 seats</span></div><p>Comfortable booth seating in our main area</p></div></div>
      <div class="zone-table-card"><div class="zone-table-card-body"><div class="zone-table-card-header"><h3>Fireplace</h3><span>4 seats</span></div><p>Cozy setting near our marble fireplace</p></div></div>
      <div class="zone-table-card"><div class="zone-table-card-body"><div class="zone-table-card-header"><h3>Private Alcove</h3><span>8 seats</span></div><p>Semi-private space for larger parties</p></div></div>
      <div class="zone-table-card"><div class="zone-table-card-body"><div class="zone-table-card-header"><h3>Chandelier</h3><span>4 seats</span></div><p>Centered beneath our signature crystal chandelier</p></div></div>
    </div>
  </div>
</section>

<section class="zone-cta">
  <div class="container">
    <h2>Reserve the Main Dining Room</h2>
    <p>Experience the full grandeur of <?= e($siteName) ?> in our signature dining space.</p>
    <a href="../../pages/book.php" class="btn btn-outline-light btn-lg">Make a Reservation</a>
  </div>
</section>

<?php include '../../includes/footer.php'; ?>

<script src="<?= $basePath ?>js/nav.js"></script>
</body>
</html>
