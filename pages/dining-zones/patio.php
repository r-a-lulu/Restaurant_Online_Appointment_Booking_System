<?php
/**
 * The Patio - Zone Detail Page
 */

$pageTitle   = 'The Patio';
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
    <img src="<?= $basePath ?>assets/images/zones/zone-patio.jpg" alt="The Patio at <?= e($siteName) ?>">
  </div>
  <div class="zone-detail-hero-overlay"></div>
  <div class="zone-detail-hero-content container">
    <a href="index.php" class="zone-back-link">All Dining Zones</a>
    <p class="section-label">Al Fresco Elegance</p>
    <h1>The Patio</h1>
  </div>
</section>

<section class="section-lg">
  <div class="container">
    <div class="zone-overview-grid">
      <div class="zone-overview-text">
        <h2>Dining Under the Stars</h2>
        <div class="zone-overview-paragraphs">
          <p>The Patio at <?= e($siteName) ?> is a sanctuary of natural beauty and culinary excellence. Our garden courtyard has been meticulously designed to transport guests to a Mediterranean oasis, where every detail creates an atmosphere of enchantment.</p>
          <p>During warm evenings, enjoy your meal al fresco surrounded by the gentle fragrance of jasmine and lavender. Our retractable canopy ensures comfort in any weather, while subtle heating elements extend the season well into autumn.</p>
          <p>The Patio menu celebrates seasonal ingredients at their peak, with lighter preparations that complement the outdoor setting without sacrificing the sophistication that defines <?= e($siteName) ?>.</p>
        </div>
      </div>

      <div class="zone-details-sidebar">
        <h3>Details</h3>
        <div class="zone-detail-rows">
          <div class="zone-detail-row"><div><p>Capacity</p><p>40 guests maximum</p></div></div>
          <div class="zone-detail-row"><div><p>Hours</p><p>5:00 PM - 10:00 PM</p></div></div>
          <div class="zone-detail-row"><div><p>Season</p><p>April through October</p></div></div>
          <div class="zone-detail-row"><div><p>Weather</p><p>Covered with retractable canopy</p></div></div>
          <div class="zone-detail-row"><div><p>Ambiance</p><p>Garden setting with ambient lighting</p></div></div>
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
      <div class="zone-table-card"><div class="zone-table-card-body"><div class="zone-table-card-header"><h3>Garden View</h3><span>2 seats</span></div><p>Intimate table beside the rose garden</p></div></div>
      <div class="zone-table-card"><div class="zone-table-card-body"><div class="zone-table-card-header"><h3>Fountain Side</h3><span>4 seats</span></div><p>Prime location near the centerpiece fountain</p></div></div>
      <div class="zone-table-card"><div class="zone-table-card-body"><div class="zone-table-card-header"><h3>Pergola</h3><span>6 seats</span></div><p>Covered seating under our wisteria-draped pergola</p></div></div>
      <div class="zone-table-card"><div class="zone-table-card-body"><div class="zone-table-card-header"><h3>Corner Alcove</h3><span>4 seats</span></div><p>Secluded spot perfect for private conversations</p></div></div>
      <div class="zone-table-card"><div class="zone-table-card-body"><div class="zone-table-card-header"><h3>Olive Grove</h3><span>8 seats</span></div><p>Our largest outdoor table, ideal for gatherings</p></div></div>
    </div>
  </div>
</section>

<section class="zone-cta">
  <div class="container">
    <h2>Reserve The Patio</h2>
    <p>Book your table in our enchanting garden courtyard for an unforgettable outdoor dining experience.</p>
    <a href="../../pages/book.php" class="btn btn-outline-light btn-lg">Make a Reservation</a>
  </div>
</section>

<?php include '../../includes/footer.php'; ?>

<script src="<?= $basePath ?>js/nav.js"></script>
</body>
</html>
