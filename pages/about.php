<?php

$pageTitle = 'About Us | Eudaimonia';
$currentPage = 'about';
$pageCSS = 'about';
include '../includes/header.php';

?>

<!-- About Hero Section -->
<section class="about-hero hero">
  <div class="hero-bg">
    <img src="/assets/images/about-hero.jpg" alt="Eudaimonia restaurant team" class="img-cover">
  </div>
  <div class="hero-overlay"></div>
  <div class="hero-content container">
    <h4 class="hero-label">Our Story</h4>
    <h1 class="hero-title">A Legacy of Culinary Excellence</h1>
    <p class="hero-subtitle">We believe that every meal should be a celebration of flavor, art, and human connection.</p>
  </div>
</section>

<!-- The Story Section -->
<section class="section-lg">
  <div class="container grid md:grid-cols-2 gap-12 items-center">
    <div class="story-content">
      <h4 class="section-label">Since 2010</h4>
      <h2>Passion on Every Plate</h2>
      <p class="text-muted mb-6 mt-4">
        Founded by Chef Julian Sterling, Eudaimonia began with a simple yet ambitious goal: 
        to create a dining experience that elevates classic techniques with modern innovation.
      </p>
      <p class="text-muted mb-6">
        Every dish is a testament to our commitment to sourcing the finest local ingredients and 
        transforming them into culinary masterpieces that delight the senses.
      </p>
      <a href="/pages/dining-zones/index.php" class="btn btn-outline">Explore Our Spaces</a>
    </div>
    <div class="story-image">
      <img src="/assets/images/chef-plating.jpg" alt="Chef plating a dish" class="img-cover rounded-xl shadow-lg">
    </div>
  </div>
</section>

<!-- Our Values -->
<section class="section bg-muted">
  <div class="container text-center">
    <h4 class="section-label">Core Principles</h4>
    <h2 class="mb-12">What Drives Us</h2>
    
    <div class="grid sm:grid-cols-3 gap-8 text-left">
      <div class="card p-6">
        <div class="icon-circle mb-4">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <h3>Quality First</h3>
        <p class="text-muted mt-2 text-sm">We never compromise on the quality of our ingredients, sourcing only the best from local producers.</p>
      </div>
      
      <div class="card p-6">
        <div class="icon-circle mb-4">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 9a2 2 0 0 1-2 2H6l-4 4V4c0-1.1.9-2 2-2h8a2 2 0 0 1 2 2v5Z"/><path d="M18 9h2a2 2 0 0 1 2 2v11l-4-4h-6a2 2 0 0 1-2-2v-1"/></svg>
        </div>
        <h3>Gastronomic Innovation</h3>
        <p class="text-muted mt-2 text-sm">While we respect tradition, we constantly push boundaries to surprise and delight our guests.</p>
      </div>
      
      <div class="card p-6">
        <div class="icon-circle mb-4">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        </div>
        <h3>Heartfelt Service</h3>
        <p class="text-muted mt-2 text-sm">Our team is dedicated to providing warm, intuitive service that anticipates your every need.</p>
      </div>
    </div>
  </div>
</section>

<!-- Team Section -->
<section class="section-lg">
  <div class="container text-center">
    <h4 class="section-label">Meet the Experts</h4>
    <h2 class="mb-12">Our Culinary Team</h2>
    
    <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-6 text-center">
      <!-- Team Member 1 -->
      <div class="team-card">
        <div class="team-image mb-4">
          <img src="/assets/images/famous-chefs/gordon_ramsay.png" alt="Chef Gordon Ramsay" class="img-cover rounded-xl aspect-square">
        </div>
        <h3 class="text-lg">Gordon Ramsay</h3>
        <p class="text-sm text-primary">Executive Chef</p>
      </div>
      <!-- Team Member 2 -->
      <div class="team-card">
        <div class="team-image mb-4">
          <img src="/assets/images/famous-chefs/massimo_bottura.png" alt="Massimo Bottura" class="img-cover rounded-xl aspect-square">
        </div>
        <h3 class="text-lg">Massimo Bottura</h3>
        <p class="text-sm text-primary">Culinary Director</p>
      </div>
      <!-- Team Member 3 -->
      <div class="team-card">
        <div class="team-image mb-4">
          <img src="/assets/images/famous-chefs/dominique_crenn.png" alt="Dominique Crenn" class="img-cover rounded-xl aspect-square">
        </div>
        <h3 class="text-lg">Dominique Crenn</h3>
        <p class="text-sm text-primary">Head Chef</p>
      </div>
      <!-- Team Member 4 -->
      <div class="team-card">
        <div class="team-image mb-4">
          <img src="/assets/images/famous-chefs/rene_redzepi.png" alt="René Redzepi" class="img-cover rounded-xl aspect-square">
        </div>
        <h3 class="text-lg">René Redzepi</h3>
        <p class="text-sm text-primary">R&D Chef</p>
      </div>
    </div>
  </div>
</section>

<?php include '../includes/footer.php'; ?>
