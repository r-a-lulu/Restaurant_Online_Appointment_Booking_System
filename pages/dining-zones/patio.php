<?php

$pageTitle = 'The Patio | Eudaimonia';
$currentPage = 'dining';
$pageCSS = 'dining-zones';
include '../../includes/header.php';

?>

<!-- Patio Hero -->
<section class="zone-detail-hero hero">
  <div class="hero-bg">
    <img src="/assets/images/zone-patio.jpg" alt="The Patio" class="img-cover">
  </div>
  <div class="hero-overlay"></div>
  <div class="hero-content container">
    <h4 class="hero-label">Dining Zones</h4>
    <h1 class="hero-title">The Patio</h1>
    <p class="hero-subtitle">Al fresco dining in a lush garden oasis.</p>
  </div>
</section>

<!-- Detail Content -->
<section class="section-lg">
  <div class="container grid md:grid-cols-3 gap-12">
    <!-- Overview -->
    <div class="md:col-span-2">
      <h2>An Oasis in the City</h2>
      <p class="text-muted mt-4 mb-6 leading-relaxed">
        The Patio offers a serene escape from the urban rush. Surrounded by carefully curated flora, this space seamlessly blends the comfort of fine dining with the refreshing ambiance of the outdoors. It is fully covered and climate-controlled, ensuring a perfect experience regardless of the season.
      </p>
      <h3>Ambiance & Seating</h3>
      <p class="text-muted mt-4 mb-6 leading-relaxed">
        Seating is arranged to provide privacy while maintaining an open, airy feel. The lighting is soft and warm, designed to mimic the glow of twilight. 
      </p>
      
      <!-- Table Listings (Mock) -->
      <h3 class="mb-4">Available Tables</h3>
      <div class="grid sm:grid-cols-2 gap-4">
        <div class="card p-4 flex justify-between items-center">
          <div>
            <h4 class="font-serif text-lg">Table P1</h4>
            <p class="text-sm text-muted">2-4 Guests • Corner</p>
          </div>
          <a href="javascript:void(0)" class="btn btn-primary btn-sm">Book</a>
        </div>
        <div class="card p-4 flex justify-between items-center">
          <div>
            <h4 class="font-serif text-lg">Table P2</h4>
            <p class="text-sm text-muted">2 Guests • Intimate</p>
          </div>
          <a href="javascript:void(0)" class="btn btn-primary btn-sm">Book</a>
        </div>
        <div class="card p-4 flex justify-between items-center">
          <div>
            <h4 class="font-serif text-lg">Table P3</h4>
            <p class="text-sm text-muted">4-6 Guests • Center</p>
          </div>
          <a href="/pages/book.php?zone=patio&table=p3" class="btn btn-outline btn-sm" disabled>Unavailable</a>
        </div>
      </div>
    </div>
    
    <!-- Details Sidebar -->
    <div class="sidebar">
      <div class="muted-box">
        <h3 class="mb-4">Zone Details</h3>
        <ul class="zone-details-list">
          <li><strong>Capacity:</strong> 40 Guests</li>
          <li><strong>Atmosphere:</strong> Relaxed, Natural</li>
          <li><strong>Best For:</strong> Brunch, Romantic Dinners</li>
          <li><strong>Dress Code:</strong> Smart Casual</li>
        </ul>
        <div class="mt-6">
          <a href="javascript:void(0)" class="btn btn-primary btn-block">Reserve in The Patio</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include '../../includes/footer.php'; ?>
