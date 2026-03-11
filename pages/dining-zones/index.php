<?php

$pageTitle = 'Dining Zones | Eudaimonia';
$currentPage = 'dining';
$pageCSS = 'dining-zones';
include '../../includes/header.php';

?>

<!-- Dining Zones Hero Section -->
<section class="dining-hero hero">
  <div class="hero-bg">
    <img src="/assets/images/dining-hero.jpg" alt="Dining zones overview" class="img-cover">
  </div>
  <div class="hero-overlay"></div>
  <div class="hero-content container">
    <h4 class="hero-label">Our Spaces</h4>
    <h1 class="hero-title">Distinctive Environments</h1>
    <p class="hero-subtitle">Discover the perfect setting for your dining experience, from intimate gatherings to grand celebrations.</p>
  </div>
</section>

<!-- The Patio (Alternating Row 1) -->
<section class="section-lg">
  <div class="container grid md:grid-cols-2 gap-12 items-center">
    <div class="zone-image order-2 md:order-1">
      <img src="/assets/images/zone-patio.jpg" alt="The Patio" class="img-cover rounded-xl shadow-lg">
    </div>
    <div class="zone-content order-1 md:order-2">
      <h2>The Patio</h2>
      <p class="text-muted mb-6 mt-4">
        Immerse yourself in nature without compromising on elegance. The Patio offers an al fresco dining experience surrounded by lush greenery, subtly lit to create a magical atmosphere as the sun sets.
      </p>
      <ul class="zone-features mb-8">
        <li><span class="icon-circle icon-sm mr-2">🌿</span> Garden setting</li>
        <li><span class="icon-circle icon-sm mr-2">🔅</span> Ambient lighting</li>
        <li><span class="icon-circle icon-sm mr-2">👥</span> Seats up to 40</li>
      </ul>
      <a href="/pages/dining-zones/patio.php" class="btn btn-outline">View Details</a>
    </div>
  </div>
</section>

<!-- Main Dining Room (Alternating Row 2) -->
<section class="section-lg bg-muted">
  <div class="container grid md:grid-cols-2 gap-12 items-center">
    <div class="zone-content text-left md:text-right">
      <h2>Main Dining Room</h2>
      <p class="text-muted mb-6 mt-4">
        The heart of Eudaimonia. High ceilings, plush seating, and an open kitchen concept let you feel the pulse of culinary creation while enjoying an atmosphere of refined sophistication.
      </p>
      <ul class="zone-features mb-8 md:justify-end">
        <li><span class="icon-circle icon-sm mr-2">🔥</span> Open kitchen</li>
        <li><span class="icon-circle icon-sm mr-2">🍷</span> Extensive wine wall</li>
        <li><span class="icon-circle icon-sm mr-2">👥</span> Seats up to 80</li>
      </ul>
      <a href="/pages/dining-zones/dining-room.php" class="btn btn-outline">View Details</a>
    </div>
    <div class="zone-image">
      <img src="/assets/images/zone-dining.jpg" alt="Main Dining Room" class="img-cover rounded-xl shadow-lg">
    </div>
  </div>
</section>

<!-- The Bar (Alternating Row 3) -->
<section class="section-lg">
  <div class="container grid md:grid-cols-2 gap-12 items-center">
    <div class="zone-image order-2 md:order-1">
      <img src="/assets/images/zone-bar.jpg" alt="The Bar" class="img-cover rounded-xl shadow-lg">
    </div>
    <div class="zone-content order-1 md:order-2">
      <h2>The Bar</h2>
      <p class="text-muted mb-6 mt-4">
        Sensuous and intimate, The Bar is the perfect prelude or finale to your evening. Enjoy artisanal cocktails, a curated spirits collection, and live jazz in a sultry, captivating setting.
      </p>
      <ul class="zone-features mb-8">
        <li><span class="icon-circle icon-sm mr-2">🍸</span> Signature cocktails</li>
        <li><span class="icon-circle icon-sm mr-2">🎷</span> Live jazz (Thu-Sat)</li>
        <li><span class="icon-circle icon-sm mr-2">👥</span> Seats up to 25</li>
      </ul>
      <a href="/pages/dining-zones/bar.php" class="btn btn-outline">View Details</a>
    </div>
  </div>
</section>

<!-- Private Events CTA -->
<section class="section-lg" style="background-color: var(--clr-sidebar);">
  <div class="container text-center">
    <h2 style="color: var(--clr-primary-fg);">Host a Private Event</h2>
    <p style="color: rgba(255,255,255,0.7); max-width: 32rem; margin-inline: auto; margin-top: var(--space-4);">
      Looking for an exclusive space for your special occasion? We offer full buyouts and tailored private dining experiences.
    </p>
    <div class="mt-8">
      <a href="#" class="btn btn-primary btn-lg">Inquire Now</a>
    </div>
  </div>
</section>

<?php include '../../includes/footer.php'; ?>
