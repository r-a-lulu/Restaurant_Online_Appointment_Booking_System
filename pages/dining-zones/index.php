<?php
/**
 * Dining Zones Index - Restaurant
 * Hero, 3 alternating zone rows, private events CTA
 */

$pageTitle   = 'Dining Zones';
$pageCSS     = ['about.css', 'dining-zones.css'];
$currentPage = 'dining-zones';
$navStyle    = 'solid';
$basePath    = '../../';

require_once '../../includes/security.php';
start_secure_session();
$siteName    = get_setting('restaurant_name', 'Eudaimonia');

include '../../includes/header.php';
include '../../includes/nav.php';
?>

<section class="page-hero">
  <div class="container">
    <div class="page-hero-inner">
      <p class="section-label">Spaces</p>
      <h1>Dining Zones</h1>
      <p>Three distinct atmospheres at <?= e($siteName) ?>, each designed to create the perfect setting for your dining experience.</p>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="zone-rows">
      <div class="zone-row">
        <div class="zone-row-img"><img src="<?= $basePath ?>assets/images/zones/zone-patio.jpg" alt="The Patio Dining Zone"></div>
        <div class="zone-row-text">
          <div><p class="section-label">Al Fresco Elegance</p><h2>The Patio</h2></div>
          <p>Nestled within our lush garden courtyard, The Patio offers an enchanting outdoor dining experience. Surrounded by climbing vines and fragrant blooms, guests enjoy seasonal cuisine under the open sky or our retractable canopy.</p>
          <div class="zone-features">
            <div class="zone-feature">Capacity: 40 guests</div>
            <div class="zone-feature">Weather-protected</div>
            <div class="zone-feature">Available April - October</div>
          </div>
          <div><a href="patio.php" class="btn btn-outline">View Details</a></div>
        </div>
      </div>

      <div class="zone-row zone-row--reverse">
        <div class="zone-row-img"><img src="<?= $basePath ?>assets/images/zones/zone-dining.jpg" alt="Main Dining Room"></div>
        <div class="zone-row-text">
          <div><p class="section-label">Timeless Sophistication</p><h2>Main Dining Room</h2></div>
          <p>Our signature dining space embodies the essence of <?= e($siteName) ?>. Crystal chandeliers cast a warm glow over intimate tables, while our open kitchen allows guests to witness culinary artistry in action.</p>
          <div class="zone-features">
            <div class="zone-feature">Capacity: 80 guests</div>
            <div class="zone-feature">Dinner service nightly</div>
            <div class="zone-feature">Available year-round</div>
          </div>
          <div><a href="dining-room.php" class="btn btn-outline">View Details</a></div>
        </div>
      </div>

      <div class="zone-row">
        <div class="zone-row-img"><img src="<?= $basePath ?>assets/images/zones/zone-bar.jpg" alt="The Bar Lounge"></div>
        <div class="zone-row-text">
          <div><p class="section-label">Intimate Indulgence</p><h2>The Bar</h2></div>
          <p>A sophisticated retreat for those seeking an elevated cocktail experience. Our bar features a curated selection of rare spirits, craft cocktails, and an expertly chosen wine list, complemented by an inventive small plates menu.</p>
          <div class="zone-features">
            <div class="zone-feature">Capacity: 24 guests</div>
            <div class="zone-feature">Full cocktail service</div>
            <div class="zone-feature">Opens at 4pm daily</div>
          </div>
          <div><a href="bar.php" class="btn btn-outline">View Details</a></div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="private-events-cta">
  <div class="container">
    <div class="private-events-cta-inner">
      <p class="section-label">Private Events</p>
      <h2>Host Your Special Occasion</h2>
      <p>Each of our dining zones can be reserved for private events at <?= e($siteName) ?>. From intimate dinners to corporate gatherings, our team will create a bespoke experience tailored to your needs.</p>
      <a href="../../pages/book.php" class="btn btn-primary btn-lg">Inquire About Events</a>
    </div>
  </div>
</section>

<?php include '../../includes/footer.php'; ?>

<script src="<?= $basePath ?>js/nav.js"></script>
</body>
</html>
