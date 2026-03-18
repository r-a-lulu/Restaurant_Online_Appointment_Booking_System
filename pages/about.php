<?php
/**
 * About Page — Eudaimonia Restaurant
 * Hero, Story, Values, Team, CTA
 */

$pageTitle   = 'About';
$pageCSS     = ['about.css'];
$currentPage = 'about';
$navStyle    = 'solid';
$basePath    = '../';

include '../includes/header.php';
include '../includes/nav.php';
?>

<!-- Page Hero -->
<section class="page-hero">
  <div class="container">
    <div class="page-hero-inner">
      <p class="section-label">Our Story</p>
      <h1>About Eudaimonia</h1>
      <p>A culinary destination where ancient wisdom meets modern gastronomy, creating experiences that nourish the soul.</p>
    </div>
  </div>
</section>

<!-- Story Section -->
<section class="section-lg" style="background-color: var(--clr-muted);">
  <div class="container">
    <div class="story-grid">
      <div class="story-img">
        <img src="<?= $basePath ?>assets/images/team/about-chef.jpg" alt="Our Executive Chef at Eudaimonia">
      </div>
      <div class="story-text">
        <p class="section-label">The Beginning</p>
        <h2>A Vision of Excellence</h2>
        <div class="story-paragraphs">
          <p>Founded in 2018, Eudaimonia was born from a simple yet profound belief: that dining should be more than sustenance—it should be a pathway to human flourishing.</p>
          <p>Our name comes from the ancient Greek concept of eudaimonia, which Aristotle described as the highest human good—a life of virtue, purpose, and wellbeing. We channel this philosophy into every aspect of our restaurant.</p>
          <p>Under the guidance of Executive Chef Isabella Marchetti, our kitchen transforms the finest seasonal ingredients into edible works of art. Each dish tells a story, each flavor note carefully composed to create harmony on the palate.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Values Section -->
<section class="section-lg">
  <div class="container">
    <div style="max-width: 48rem; margin-inline: auto; text-align: center;" class="mb-16">
      <p class="section-label">Our Values</p>
      <h2>Principles That Guide Us</h2>
    </div>
    <div class="values-grid">
      <div class="value-card">
        <div class="value-card-icon">
          <span>I</span>
        </div>
        <h3>Integrity</h3>
        <p>We source only the finest ingredients from ethical suppliers, maintaining transparency in every aspect of our operation.</p>
      </div>
      <div class="value-card">
        <div class="value-card-icon">
          <span>E</span>
        </div>
        <h3>Excellence</h3>
        <p>Every detail matters. From mise en place to final presentation, we pursue perfection in all that we do.</p>
      </div>
      <div class="value-card">
        <div class="value-card-icon">
          <span>C</span>
        </div>
        <h3>Connection</h3>
        <p>We create spaces where meaningful connections flourish—between guests, staff, and the culinary arts.</p>
      </div>
    </div>
  </div>
</section>

<!-- Team Section -->
<section class="section-lg" style="background-color: var(--clr-muted);">
  <div class="container">
    <div style="max-width: 48rem; margin-inline: auto; text-align: center;" class="mb-16">
      <p class="section-label">Leadership</p>
      <h2>Meet Our Team</h2>
    </div>
    <div class="team-grid">
      <div class="team-card">
        <div class="team-member-header">
          <img src="<?= $basePath ?>assets/images/team/team_jann.png" alt="Jann Francis Juson" class="team-photo">
          <div class="team-info">
            <h3>Jann Francis Juson</h3>
            <p class="team-role">Executive Chef</p>
          </div>
        </div>
        <p>Trained in the kitchens of Milan and Lyon, Chef Juson brings 20 years of culinary excellence to Eudaimonia. His philosophy centers on honoring ingredients while pushing creative boundaries.</p>
      </div>
      <div class="team-card">
        <div class="team-member-header">
          <img src="<?= $basePath ?>assets/images/team/team_marcus.png" alt="Marcus Chen" class="team-photo">
          <div class="team-info">
            <h3>Marcus Chen</h3>
            <p class="team-role">General Manager</p>
          </div>
        </div>
        <p>With a background in luxury hospitality spanning three continents, Marcus ensures every guest experience exceeds expectations. His attention to detail is legendary.</p>
      </div>
      <div class="team-card">
        <div class="team-member-header">
          <img src="<?= $basePath ?>assets/images/team/team_elena.png" alt="Elena Vasquez" class="team-photo">
          <div class="team-info">
            <h3>Elena Vasquez</h3>
            <p class="team-role">Head Sommelier</p>
          </div>
        </div>
        <p>A certified Master Sommelier, Elena curates our wine program with passion and precision. Her pairings elevate each dish to new heights.</p>
      </div>
      <div class="team-card">
        <div class="team-member-header">
          <img src="<?= $basePath ?>assets/images/team/team_david.png" alt="David Laurent" class="team-photo">
          <div class="team-info">
            <h3>David Laurent</h3>
            <p class="team-role">Pastry Chef</p>
          </div>
        </div>
        <p>David's desserts are legendary—architectural masterpieces that balance artistry with indulgence. His creations provide the perfect finale to every meal.</p>
      </div>
    </div>
  </div>
</section>

<!-- CTA Section -->
<section class="about-cta">
  <div class="container" style="text-align: center;">
    <h2>Experience Eudaimonia</h2>
    <p>We invite you to join us for an unforgettable dining experience.</p>
    <a href="<?= $basePath ?>pages/book.php" class="btn btn-outline-light btn-lg">
      Reserve Your Table
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
    </a>
  </div>
</section>

<?php include '../includes/footer.php'; ?>

<script src="<?= $basePath ?>js/nav.js"></script>
</body>
</html>
