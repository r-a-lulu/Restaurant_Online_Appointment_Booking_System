<?php
/**
 * Home Page — Eudaimonia Restaurant
 * Hero section + philosophy + dining zones + features + CTA
 */

$pageTitle   = 'Welcome';
$pageCSS     = ['home.css'];
$currentPage = 'home';
$navStyle    = 'transparent';
$basePath    = './';

include 'includes/header.php';
include 'includes/nav.php';
?>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-bg">
    <img src="assets/images/hero/hero-restaurant.jpg" alt="Interior of Eudaimonia restaurant" class="img-cover">
  </div>
  <div class="hero-overlay"></div>
  <div class="hero-content container">
    <h4 class="hero-label">Welcome to</h4>
    <h1 class="hero-title">Eudaimonia</h1>
    <p class="hero-subtitle">An exquisite dining experience where every detail is crafted to create moments of pure joy and connection.</p>
    <div class="hero-actions">
      <a href="#" onclick="alert('Booking page coming in Phase 4'); return false;" class="btn btn-hero">Reserve a Table</a>
      <a href="pages/about.php" class="btn btn-outline-light btn-lg">Our Story</a>
    </div>
  </div>
</section>

<!-- Philosophy Section -->
<section class="section-lg">
  <div class="container philosophy-inner">
    <h4 class="section-label">Our Philosophy</h4>
    <h2>The Art of Dining</h2>
    <p class="text-muted">
      At Eudaimonia, we believe that dining is more than just a meal — it's an experience 
      that nourishes the soul. Every dish is crafted with passion, every moment designed 
      to create lasting memories.
    </p>
  </div>
</section>

<!-- Dining Zones Section -->
<section class="section zones-section">
  <div class="container">
    <div class="zones-header mb-12">
      <h4 class="section-label">Explore Our Spaces</h4>
      <h2>Dining Zones</h2>
      <p class="text-muted">
        Three distinctive environments, each offering a unique atmosphere for your perfect dining experience.
      </p>
    </div>

    <div class="zones-grid">
      <!-- The Patio -->
      <a href="pages/dining-zones/patio.php" class="zone-card">
        <div class="zone-card-img">
          <img src="assets/images/zones/zone-patio.jpg" alt="The Patio dining zone" class="img-cover">
        </div>
        <div class="zone-card-overlay"></div>
        <div class="zone-card-content">
          <h3>The Patio</h3>
          <p>Al fresco dining surrounded by lush greenery and soft lighting.</p>
        </div>
      </a>

      <!-- Main Dining Room -->
      <a href="pages/dining-zones/dining-room.php" class="zone-card">
        <div class="zone-card-img">
          <img src="assets/images/zones/zone-dining.jpg" alt="Main Dining Room" class="img-cover">
        </div>
        <div class="zone-card-overlay"></div>
        <div class="zone-card-content">
          <h3>Main Dining Room</h3>
          <p>Elegant sophistication with an open kitchen concept.</p>
        </div>
      </a>

      <!-- The Bar -->
      <a href="pages/dining-zones/bar.php" class="zone-card">
        <div class="zone-card-img">
          <img src="assets/images/zones/zone-bar.jpg" alt="The Bar lounge area" class="img-cover">
        </div>
        <div class="zone-card-overlay"></div>
        <div class="zone-card-content">
          <h3>The Bar</h3>
          <p>Intimate setting with artisanal cocktails and live jazz.</p>
        </div>
      </a>
    </div>
  </div>
</section>

<!-- Features Section -->
<section class="section-lg">
  <div class="container">
    <div class="features-header mb-12">
      <h4 class="section-label">Why Eudaimonia</h4>
      <h2>An Unparalleled Experience</h2>
    </div>

    <div class="features-grid">
      <div class="feature-card">
        <div class="icon-circle icon-circle-lg mb-4">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M6 13.87A4 4 0 0 1 7.41 6a5.11 5.11 0 0 1 1.05-1.54 5 5 0 0 1 7.08 0A5.11 5.11 0 0 1 16.59 6 4 4 0 0 1 18 13.87V21H6Z"/><line x1="6" y1="17" x2="18" y2="17"/></svg>
        </div>
        <h3>Award-Winning Cuisine</h3>
        <p class="text-muted text-sm">Our chef creates seasonal menus using locally sourced, premium ingredients.</p>
      </div>

      <div class="feature-card">
        <div class="icon-circle icon-circle-lg mb-4">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/></svg>
        </div>
        <h3>Curated Ambiance</h3>
        <p class="text-muted text-sm">Three distinct dining zones, each designed to complement your mood and occasion.</p>
      </div>

      <div class="feature-card">
        <div class="icon-circle icon-circle-lg mb-4">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <h3>Impeccable Service</h3>
        <p class="text-muted text-sm">Our attentive staff ensures every visit exceeds your expectations.</p>
      </div>

      <div class="feature-card">
        <div class="icon-circle icon-circle-lg mb-4">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <h3>Easy Reservations</h3>
        <p class="text-muted text-sm">Book your table in moments with our seamless reservation system.</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
  <div class="container cta-inner">
    <h4 class="section-label">Begin Your Journey</h4>
    <h2>Ready for an Unforgettable Evening?</h2>
    <p>
      Reserve your table today and discover why Eudaimonia is the city's most sought-after dining destination.
    </p>
    <div class="cta-actions">
      <a href="#" onclick="alert('Booking page coming in Phase 4'); return false;" class="btn btn-hero">Reserve a Table</a>
      <a href="pages/dining-zones/index.php" class="btn btn-outline-light btn-lg">Explore Dining Zones</a>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<script src="js/nav.js"></script>
</body>
</html>
