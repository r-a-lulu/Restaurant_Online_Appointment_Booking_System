<?php

$pageTitle = 'The Bar | Eudaimonia';
$currentPage = 'dining';
$pageCSS = 'dining-zones';
include '../../includes/header.php';

?>

<!-- Bar Hero -->
<section class="zone-detail-hero hero">
  <div class="hero-bg">
    <img src="/assets/images/zone-bar.jpg" alt="The Bar" class="img-cover">
  </div>
  <div class="hero-overlay"></div>
  <div class="hero-content container">
    <h4 class="hero-label">Dining Zones</h4>
    <h1 class="hero-title">The Bar</h1>
    <p class="hero-subtitle">Intimate, sensuous, and captivated by craft.</p>
  </div>
</section>

<!-- Detail Content -->
<section class="section-lg">
  <div class="container grid md:grid-cols-3 gap-12">
    <!-- Overview -->
    <div class="md:col-span-2">
      <h2>Mixology & Melody</h2>
      <p class="text-muted mt-4 mb-6 leading-relaxed">
        The Bar is an intimately scaled, dimly lit lounge focused on the art of mixology. With a magnificent backdrop of rare spirits and custom liqueurs, our bartenders craft both classic staples and vanguard signatures.
      </p>
      <h3>Ambiance & Seating</h3>
      <p class="text-muted mt-4 mb-6 leading-relaxed">
        Featuring velvet banquettes, low mahogany tables, and a sweeping brass bar. On weekends, the space comes alive with subtle live jazz, providing the perfect soundtrack for engaging conversations.
      </p>
      
      <!-- Table Listings (Mock) -->
      <h3 class="mb-4">Available Tables & Seats</h3>
      <div class="grid sm:grid-cols-2 gap-4">
        <div class="card p-4 flex justify-between items-center">
          <div>
            <h4 class="font-serif text-lg">Bar Seats</h4>
            <p class="text-sm text-muted">1-2 Guests • Front Row</p>
          </div>
          <a href="javascript:void(0)" class="btn btn-primary btn-sm">Book</a>
        </div>
        <div class="card p-4 flex justify-between items-center">
          <div>
            <h4 class="font-serif text-lg">Booth B1</h4>
            <p class="text-sm text-muted">2-4 Guests • Velvet Banquette</p>
          </div>
          <a href="javascript:void(0)" class="btn btn-primary btn-sm">Book</a>
        </div>
        <div class="card p-4 flex justify-between items-center">
          <div>
            <h4 class="font-serif text-lg">Booth B2</h4>
            <p class="text-sm text-muted">4-6 Guests • Corner</p>
          </div>
          <a href="/pages/book.php?zone=bar&table=b2" class="btn btn-outline btn-sm" disabled>Unavailable</a>
        </div>
      </div>
    </div>
    
    <!-- Details Sidebar -->
    <div class="sidebar">
      <div class="muted-box">
        <h3 class="mb-4">Zone Details</h3>
        <ul class="zone-details-list">
          <li><strong>Capacity:</strong> 25 Guests</li>
          <li><strong>Atmosphere:</strong> Intimate, Sultry</li>
          <li><strong>Best For:</strong> Cocktails, Small Bites</li>
          <li><strong>Dress Code:</strong> Smart Casual</li>
          <li><strong>Feature:</strong> Live Jazz (Thu-Sat)</li>
        </ul>
        <div class="mt-6">
          <a href="javascript:void(0)" class="btn btn-primary btn-block">Reserve The Bar</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include '../../includes/footer.php'; ?>
