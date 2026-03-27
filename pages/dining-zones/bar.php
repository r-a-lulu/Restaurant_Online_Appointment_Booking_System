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

<?php
// Fetch actual seating options for The Bar from the database
$barTables = [];
try {
  $pdo = db();
  $stmt = $pdo->prepare("
    SELECT t.seating_preference, MAX(t.capacity) AS max_capacity, COUNT(*) AS table_count
    FROM `tables` t
    JOIN dining_zones dz ON dz.zone_id = t.zone_id
    WHERE dz.zone_name = 'The Bar'
    GROUP BY t.seating_preference
    ORDER BY max_capacity ASC, t.seating_preference ASC
  ");
  $stmt->execute();
  $barTables = $stmt->fetchAll();
  $stmt->closeCursor();
} catch (PDOException $e) {
  // Silently fail — seating section simply won't render
}
?>

<section class="section-lg">
  <div class="container">
    <div style="max-width: 48rem; margin-inline: auto; text-align: center;" class="mb-12">
      <p class="section-label">Seating Options</p>
      <h2>Find Your Spot</h2>
    </div>
    <?php if (!empty($barTables)): ?>
    <div class="zone-table-grid">
      <?php foreach ($barTables as $t): ?>
      <div class="zone-table-card"><div class="zone-table-card-body"><div class="zone-table-card-header"><h3><?= e($t['seating_preference']) ?></h3><span>Up to <?= e((string) $t['max_capacity']) ?> seats</span></div><p><?= e((string) $t['table_count']) ?> table<?= (int) $t['table_count'] > 1 ? 's' : '' ?> available</p></div></div>
      <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-muted" style="text-align:center;">No tables are currently configured for this zone.</p>
    <?php endif; ?>
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
