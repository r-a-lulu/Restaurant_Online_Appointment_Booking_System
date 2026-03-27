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

<?php
// Fetch actual seating options for Main Dining Room from the database
$diningTables = [];
try {
  $pdo = db();
  $stmt = $pdo->prepare("
    SELECT t.seating_preference, MAX(t.capacity) AS max_capacity, COUNT(*) AS table_count
    FROM `tables` t
    JOIN dining_zones dz ON dz.zone_id = t.zone_id
    WHERE dz.zone_name = 'Main Dining Room'
    GROUP BY t.seating_preference
    ORDER BY max_capacity ASC, t.seating_preference ASC
  ");
  $stmt->execute();
  $diningTables = $stmt->fetchAll();
  $stmt->closeCursor();
} catch (PDOException $e) {
  // Silently fail — seating section simply won't render
}
?>

<section class="section-lg" style="background-color: var(--clr-muted);">
  <div class="container">
    <div style="max-width: 48rem; margin-inline: auto; text-align: center;" class="mb-12">
      <p class="section-label">Seating Options</p>
      <h2>Choose Your Perfect Table</h2>
    </div>
    <?php if (!empty($diningTables)): ?>
    <div class="zone-table-grid zone-table-grid--3col">
      <?php foreach ($diningTables as $t): ?>
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
    <h2>Reserve the Main Dining Room</h2>
    <p>Experience the full grandeur of <?= e($siteName) ?> in our signature dining space.</p>
    <a href="../../pages/book.php" class="btn btn-outline-light btn-lg">Make a Reservation</a>
  </div>
</section>

<?php include '../../includes/footer.php'; ?>

<script src="<?= $basePath ?>js/nav.js"></script>
</body>
</html>
