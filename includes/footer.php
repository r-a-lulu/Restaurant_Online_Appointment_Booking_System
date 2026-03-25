<?php
/**
 * Footer partial.
 */

$siteName = get_setting('restaurant_name', 'Eudaimonia');
$siteDescription = get_setting('restaurant_description', 'An exquisite dining experience where every detail is crafted to create moments of pure joy and connection.');
$siteEmail = get_setting('restaurant_email', 'hello@eudaimonia.com');
$sitePhone = get_setting('restaurant_phone', '(555) 123-4567');
$siteAddress = get_setting('restaurant_address', '123 Culinary Avenue, New York, NY 10001');
?>

<footer class="site-footer">
  <div class="footer-inner">
    <div class="footer-brand">
      <h3><?= e($siteName) ?></h3>
      <p><?= e($siteDescription) ?></p>
    </div>

    <div class="footer-column">
      <h4>Quick Links</h4>
      <div class="footer-links">
        <a href="<?= $basePath ?>pages/about.php" class="footer-link <?= $currentPage === 'about' ? 'active' : '' ?>">About Us</a>
        <a href="<?= $basePath ?>pages/dining-zones/index.php" class="footer-link <?= $currentPage === 'dining-zones' ? 'active' : '' ?>">Dining Zones</a>
        <a href="<?= $basePath ?>pages/book.php" class="footer-link <?= $currentPage === 'book' ? 'active' : '' ?>">Reservations</a>
        <a href="<?= $basePath ?>pages/login.php" class="footer-link <?= $currentPage === 'login' ? 'active' : '' ?>">Login</a>
      </div>
    </div>

    <div class="footer-column">
      <h4>Contact</h4>
      <div class="footer-links">
        <div class="footer-contact-item">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
          <span><?= nl2br(e(str_replace(', ', "\n", $siteAddress))) ?></span>
        </div>
        <div class="footer-contact-item">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
          <span><?= e($sitePhone) ?></span>
        </div>
        <div class="footer-contact-item">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
          <span><?= e($siteEmail) ?></span>
        </div>
      </div>
    </div>

    <div class="footer-column">
      <h4>Hours</h4>
      <div class="footer-links">
        <div class="footer-contact-item">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
          <span>Mon - Thu: 5:00 PM - 10:00 PM<br>Fri - Sat: 5:00 PM - 11:00 PM<br>Sun: 4:00 PM - 9:00 PM</span>
        </div>
      </div>
    </div>
  </div>

  <div class="footer-bottom">
    <p>&copy; <?= date('Y') ?> <?= e($siteName) ?>. All rights reserved.</p>
    <div class="footer-bottom-links">
      <a href="#">Privacy Policy</a>
      <a href="#">Terms of Service</a>
    </div>
  </div>
</footer>
