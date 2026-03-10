<?php

$pageTitle = 'Main Dining Room | Eudaimonia';
$currentPage = 'dining';
$pageCSS = 'dining-zones';
include '../../includes/header.php';

?>

<!-- Dining Room Hero -->
<section class="zone-detail-hero hero">
  <div class="hero-bg">
    <img src="/assets/images/zone-dining.jpg" alt="Main Dining Room" class="img-cover">
  </div>
  <div class="hero-overlay"></div>
  <div class="hero-content container">
    <h4 class="hero-label">Dining Zones</h4>
    <h1 class="hero-title">Main Dining Room</h1>
    <p class="hero-subtitle">The heart of culinary creation and sophisticated elegance.</p>
  </div>
</section>

<!-- Detail Content -->
<section class="section-lg">
  <div class="container grid md:grid-cols-3 gap-12">
    <!-- Overview -->
    <div class="md:col-span-2">
      <h2>The Pulse of Eudaimonia</h2>
      <p class="text-muted mt-4 mb-6 leading-relaxed">
        The Main Dining Room is where the magic happens. With a direct view of our open kitchen, guests become part of the culinary journey. The decor features deep burgundy accents, rich woodwork, and an awe-inspiring floor-to-ceiling wine wall that serves as both a cellar and an art piece.
      </p>
      <h3>Ambiance & Seating</h3>
      <p class="text-muted mt-4 mb-6 leading-relaxed">
        Expect a vibrant, energetic atmosphere balanced by the luxurious comfort of our seating. Large, spacious tables ensure privacy while allowing you to absorb the ambient energy of the room.
      </p>
      
      <!-- Table Listings (Mock) -->
      <h3 class="mb-4">Available Tables</h3>
      <div class="grid sm:grid-cols-2 gap-4">
        <div class="card p-4 flex justify-between items-center">
          <div>
            <h4 class="font-serif text-lg">Table M1</h4>
            <p class="text-sm text-muted">2-4 Guests • Window</p>
          </div>
          <a href="javascript:void(0)" class="btn btn-primary btn-sm">Book</a>
        </div>
        <div class="card p-4 flex justify-between items-center">
          <div>
            <h4 class="font-serif text-lg">Table M2</h4>
            <p class="text-sm text-muted">4-6 Guests • Center</p>
          </div>
          <a href="javascript:void(0)" class="btn btn-primary btn-sm">Book</a>
        </div>
        <div class="card p-4 flex justify-between items-center">
          <div>
            <h4 class="font-serif text-lg">Chef's Table</h4>
            <p class="text-sm text-muted">6-8 Guests • Kitchen View</p>
          </div>
          <a href="javascript:void(0)" class="btn btn-primary btn-sm">Book</a>
        </div>
        <div class="card p-4 flex justify-between items-center">
          <div>
            <h4 class="font-serif text-lg">Table M4</h4>
            <p class="text-sm text-muted">2 Guests • Intimate</p>
          </div>
          <a href="/pages/book.php?zone=dining-room&table=m4" class="btn btn-outline btn-sm" disabled>Unavailable</a>
        </div>
      </div>
    </div>
    
    <!-- Details Sidebar -->
    <div class="sidebar">
      <div class="muted-box">
        <h3 class="mb-4">Zone Details</h3>
        <ul class="zone-details-list">
          <li><strong>Capacity:</strong> 80 Guests</li>
          <li><strong>Atmosphere:</strong> Vibrant, Elegant</li>
          <li><strong>Best For:</strong> Special Occasions, Groups</li>
          <li><strong>Dress Code:</strong> Formal / Smart Casual</li>
        </ul>
        <div class="mt-6">
          <a href="javascript:void(0)" class="btn btn-primary btn-block">Reserve Dining Room</a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include '../../includes/footer.php'; ?>
