<?php
/**
 * Navigation bar partial.
 * 
 * Expects:
 *   $currentPage — string key: 'home', 'about', 'dining', 'book', 'login'
 *   $navStyle    — 'transparent' (hero pages) or 'solid' (inner pages)
 *   $basePath    — relative path prefix set by header.php
 */

$currentPage = $currentPage ?? '';
$navStyle    = $navStyle ?? 'transparent';
$navClass    = ($navStyle === 'solid') ? 'site-nav solid' : 'site-nav';
?>

<nav class="<?= $navClass ?>" id="site-nav">
  <div class="nav-inner">
    <!-- Logo -->
    <a href="<?= $basePath ?>index.php" class="nav-logo">Eudaimonia</a>

    <!-- Desktop links -->
    <div class="nav-links">
      <a href="<?= $basePath ?>index.php" class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>">Home</a>
      <a href="#" onclick="alert('This page will be built in Phase 2'); return false;" class="nav-link <?= $currentPage === 'about' ? 'active' : '' ?>">About</a>
      <a href="#" onclick="alert('This page will be built in Phase 2'); return false;" class="nav-link <?= $currentPage === 'dining' ? 'active' : '' ?>">Dining</a>
    </div>

    <!-- Desktop actions -->
    <div class="nav-actions">
      <a href="#" onclick="alert('This page will be built in Phase 3'); return false;" class="btn btn-ghost btn-sm">Sign In</a>
      <a href="#" onclick="alert('This page will be built in Phase 4'); return false;" class="btn btn-primary btn-sm">Reserve a Table</a>
    </div>

    <!-- Mobile toggle -->
    <button class="nav-mobile-toggle" id="nav-mobile-toggle" aria-label="Open menu">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
        <line x1="3" y1="6" x2="21" y2="6"></line>
        <line x1="3" y1="12" x2="21" y2="12"></line>
        <line x1="3" y1="18" x2="21" y2="18"></line>
      </svg>
    </button>
  </div>
</nav>

<!-- Mobile menu overlay -->
<div class="mobile-menu-overlay" id="mobile-menu-overlay"></div>

<!-- Mobile slide-in menu -->
<div class="mobile-menu" id="mobile-menu">
  <button class="mobile-menu-close" id="mobile-menu-close" aria-label="Close menu">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
      <line x1="18" y1="6" x2="6" y2="18"></line>
      <line x1="6" y1="6" x2="18" y2="18"></line>
    </svg>
  </button>

  <div class="mobile-menu-links">
    <a href="<?= $basePath ?>index.php" class="mobile-menu-link <?= $currentPage === 'home' ? 'active' : '' ?>">Home</a>
    <a href="#" onclick="alert('This page will be built in Phase 2'); return false;" class="mobile-menu-link <?= $currentPage === 'about' ? 'active' : '' ?>">About</a>
    <a href="#" onclick="alert('This page will be built in Phase 2'); return false;" class="mobile-menu-link <?= $currentPage === 'dining' ? 'active' : '' ?>">Dining Zones</a>
    <a href="#" onclick="alert('This page will be built in Phase 4'); return false;" class="mobile-menu-link <?= $currentPage === 'book' ? 'active' : '' ?>">Reservations</a>
  </div>

  <div class="mobile-menu-actions">
    <a href="#" onclick="alert('This page will be built in Phase 3'); return false;" class="btn btn-outline btn-block">Sign In</a>
    <a href="#" onclick="alert('This page will be built in Phase 4'); return false;" class="btn btn-primary btn-block">Reserve a Table</a>
  </div>
</div>
