<?php
/**
 * The Bar - Zone Detail Page
 */

$pageTitle   = 'The Bar';
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
    <img src="<?= $basePath ?>assets/images/zones/zone-bar.jpg" alt="The Bar at <?= e($siteName) ?>">
  </div>
  <div class="zone-detail-hero-overlay"></div>
  <div class="zone-detail-hero-content container">
    <a href="index.php" class="zone-back-link">All Dining Zones</a>
    <p class="section-label">Intimate Indulgence</p>
    <h1>The Bar</h1>
  </div>
</section>

<section class="section-lg">
  <div class="container">
    <div class="zone-overview-grid">
      <div class="zone-overview-text">
        <h2>The Art of the Cocktail</h2>
        <div class="zone-overview-paragraphs">
          <p>The Bar at <?= e($siteName) ?> is a sanctuary for those who appreciate the finer things. Our intimate space, anchored by a stunning 30-foot mahogany bar, pays homage to the golden age of cocktail culture while embracing contemporary innovation.</p>
          <p>Behind the bar, our expert mixologists craft each drink with precision and artistry. Our cocktail program features house originals alongside expertly executed classics, all utilizing premium spirits and house-made ingredients.</p>
          <p>Complement your drinks with our curated menu of small plates, perfect for sharing or enjoying solo. From charcuterie boards to our signature truffle fries, each dish is designed to enhance your bar experience.</p>
        </div>
      </div>

      <div class="zone-details-sidebar">
        <h3>Details</h3>
        <div class="zone-detail-rows">
          <div class="zone-detail-row"><div><p>Capacity</p><p>24 guests maximum</p></div></div>
          <div class="zone-detail-row"><div><p>Hours</p><p>Opens 4 PM daily</p></div></div>
          <div class="zone-detail-row"><div><p>Selection</p><p>200+ spirits, craft cocktails</p></div></div>
          <div class="zone-detail-row"><div><p>Food</p><p>Small plates &amp; bar bites</p></div></div>
          <div class="zone-detail-row"><div><p>Atmosphere</p><p>Live jazz on weekends</p></div></div>
        </div>
        <a href="../../pages/book.php" class="btn btn-primary btn-block">Reserve a Seat</a>
      </div>
    </div>
  </div>
</section>

<section class="section-lg" style="background-color: var(--clr-muted);">
  <div class="container">
    <div style="max-width: 48rem; margin-inline: auto; text-align: center;" class="mb-12">
      <p class="section-label">Signature Cocktails</p>
      <h2>House Creations</h2>
    </div>
    <div class="zone-cocktail-grid">
      <div class="zone-cocktail-card"><div><h3>The Flourish</h3><p>Aged rum, honey, citrus, sage</p></div><span class="zone-cocktail-price">$18</span></div>
      <div class="zone-cocktail-card"><div><h3>Golden Hour</h3><p>Single malt, amaro, orange bitters</p></div><span class="zone-cocktail-price">$22</span></div>
      <div class="zone-cocktail-card"><div><h3>Garden State</h3><p>Gin, elderflower, cucumber, basil</p></div><span class="zone-cocktail-price">$16</span></div>
      <div class="zone-cocktail-card"><div><h3>Midnight in Paris</h3><p>Cognac, champagne, violet liqueur</p></div><span class="zone-cocktail-price">$24</span></div>
    </div>
  </div>
</section>

<section class="section-lg">
  <div class="container">
    <div style="max-width: 48rem; margin-inline: auto; text-align: center;" class="mb-12">
      <p class="section-label">Seating Options</p>
      <h2>Find Your Spot</h2>
    </div>
    <div class="zone-table-grid">
      <div class="zone-table-card"><div class="zone-table-card-body"><div class="zone-table-card-header"><h3>Bar Counter</h3><span>8 seats</span></div><p>Premium seats at our mahogany bar</p></div></div>
      <div class="zone-table-card"><div class="zone-table-card-body"><div class="zone-table-card-header"><h3>Lounge Booths</h3><span>4 seats</span></div><p>Intimate leather booth seating</p></div></div>
      <div class="zone-table-card"><div class="zone-table-card-body"><div class="zone-table-card-header"><h3>High Tops</h3><span>4 seats</span></div><p>Casual elevated tables</p></div></div>
      <div class="zone-table-card"><div class="zone-table-card-body"><div class="zone-table-card-header"><h3>Corner Sofa</h3><span>6 seats</span></div><p>Relaxed seating for larger groups</p></div></div>
    </div>
  </div>
</section>

<section class="zone-cta">
  <div class="container">
    <h2>Reserve at The Bar</h2>
    <p>Secure your spot for an evening of exceptional cocktails and conversation.</p>
    <a href="../../pages/book.php" class="btn btn-outline-light btn-lg">Make a Reservation</a>
  </div>
</section>

<?php include '../../includes/footer.php'; ?>

<script src="<?= $basePath ?>js/nav.js"></script>
</body>
</html>
