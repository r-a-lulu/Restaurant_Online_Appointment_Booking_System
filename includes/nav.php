<?php
/**
 * Navigation bar partial.
 * 
 * Expects:
 *   $currentPage — string key: 'home', 'about', 'dining', 'book', 'login'
 *   $navStyle    — 'transparent' (hero pages) or 'solid' (inner pages)
 *   $basePath    — relative path prefix set by header.php
 */

require_once __DIR__ . '/security.php';
start_secure_session();

$currentPage = $currentPage ?? '';
$navStyle    = $navStyle ?? 'transparent';
$navClass    = ($navStyle === 'solid') ? 'site-nav solid' : 'site-nav';

$isLoggedIn = !empty($_SESSION['user_id']);
$roleName = $_SESSION['role_name'] ?? 'guest';
$firstName = $_SESSION['first_name'] ?? '';
?>

<nav class="<?= $navClass ?>" id="site-nav">
  <div class="nav-inner">
    <!-- Logo -->
    <a href="<?= $basePath ?>index.php" class="nav-logo">Eudaimonia</a>

    <!-- Desktop links -->
    <div class="nav-links">
      <a href="<?= $basePath ?>index.php" class="nav-link <?= $currentPage === 'home' ? 'active' : '' ?>">Home</a>
      <a href="<?= $basePath ?>pages/about.php" class="nav-link <?= $currentPage === 'about' ? 'active' : '' ?>">About</a>
      <a href="<?= $basePath ?>pages/dining-zones/index.php" class="nav-link <?= $currentPage === 'dining-zones' ? 'active' : '' ?>">Dining Zones</a>
      <?php if ($isLoggedIn): ?>
        <a href="<?= $basePath ?>pages/dashboard/index.php" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">Dashboard</a>
      <?php endif; ?>
      <?php if ($roleName === 'admin'): ?>
        <a href="<?= $basePath ?>pages/admin/index.php" class="nav-link">Admin</a>
      <?php endif; ?>
    </div>

    <!-- Desktop actions -->
    <div class="nav-actions">
      <?php if ($isLoggedIn): ?>
        <span class="nav-welcome">Hi, <?= e($firstName) ?></span>
        <?php if (empty($hideLogout)): ?>
          <form method="post" action="<?= $basePath ?>actions.php?action=logout" style="display:inline;">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action_token" value="<?= e(action_token('logout')) ?>">
            <button type="submit" class="btn btn-outline btn-sm">Logout</button>
          </form>
        <?php endif; ?>
      <?php else: ?>
        <a href="<?= $basePath ?>pages/login.php" class="btn btn-ghost btn-sm <?= $currentPage === 'login' ? 'active' : '' ?>">Sign In</a>
        <a href="<?= $basePath ?>pages/book.php" class="btn btn-primary btn-sm">Reserve a Table</a>
      <?php endif; ?>
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
    <a href="<?= $basePath ?>pages/about.php" class="mobile-menu-link <?= $currentPage === 'about' ? 'active' : '' ?>">About</a>
    <a href="<?= $basePath ?>pages/dining-zones/index.php" class="mobile-menu-link <?= $currentPage === 'dining-zones' ? 'active' : '' ?>">Dining Zones</a>
    <a href="<?= $basePath ?>pages/book.php" class="mobile-menu-link <?= $currentPage === 'book' ? 'active' : '' ?>">Reservations</a>
    <?php if ($isLoggedIn): ?>
      <a href="<?= $basePath ?>pages/dashboard/index.php" class="mobile-menu-link">Dashboard</a>
    <?php endif; ?>
    <?php if ($roleName === 'admin'): ?>
      <a href="<?= $basePath ?>pages/admin/index.php" class="mobile-menu-link">Admin</a>
    <?php endif; ?>
  </div>

  <div class="mobile-menu-actions">
    <?php if ($isLoggedIn): ?>
      <?php if (empty($hideLogout)): ?>
        <form method="post" action="<?= $basePath ?>actions.php?action=logout">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
          <input type="hidden" name="action_token" value="<?= e(action_token('logout')) ?>">
          <button type="submit" class="btn btn-outline btn-block">Logout</button>
        </form>
      <?php endif; ?>
    <?php else: ?>
      <a href="<?= $basePath ?>pages/login.php" class="btn btn-outline btn-block">Sign In</a>
      <a href="<?= $basePath ?>pages/book.php" class="btn btn-primary btn-block">Reserve a Table</a>
    <?php endif; ?>
  </div>
</div>
