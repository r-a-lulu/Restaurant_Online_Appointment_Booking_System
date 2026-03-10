<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Eudaimonia — An exquisite dining experience. Reserve your table today.">
  <title><?php echo isset($pageTitle) ? $pageTitle : 'Welcome | Eudaimonia'; ?></title>

  <!-- Favicon -->
  <link rel="icon" href="/assets/images/icon.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="/assets/images/apple-icon.png">

  <!-- Core CSS (order matters) -->
  <link rel="stylesheet" href="/css/variables.css">
  <link rel="stylesheet" href="/css/reset.css">
  <link rel="stylesheet" href="/css/typography.css">
  <link rel="stylesheet" href="/css/base.css">
  <link rel="stylesheet" href="/css/components.css">
  <link rel="stylesheet" href="/css/nav.css">
  <link rel="stylesheet" href="/css/footer.css">

  <!-- Page-specific CSS -->
  <?php if (isset($pageCSS)): ?>
    <link rel="stylesheet" href="/css/<?php echo $pageCSS; ?>.css">
  <?php
endif; ?>
</head>
<body>

<!-- Navigation -->
<nav class="site-nav" id="site-nav">
  <div class="nav-inner">
    <!-- Logo -->
    <a href="/index.php" class="nav-logo">Eudaimonia</a>

    <!-- Desktop links -->
    <div class="nav-links">
      <a href="/index.php" class="nav-link <?php echo($currentPage === 'home') ? 'active' : ''; ?>">Home</a>
      <a href="/pages/about.php" class="nav-link <?php echo($currentPage === 'about') ? 'active' : ''; ?>">About</a>
      <a href="/pages/dining-zones/index.php" class="nav-link <?php echo($currentPage === 'dining') ? 'active' : ''; ?>">Dining</a>
    </div>

    <!-- Desktop actions -->
    <div class="nav-actions">
      <a href="javascript:void(0)" class="btn btn-ghost btn-sm">Sign In</a>
      <a href="javascript:void(0)" class="btn btn-primary btn-sm">Reserve a Table</a>
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
    <a href="/index.php" class="mobile-menu-link <?php echo($currentPage === 'home') ? 'active' : ''; ?>">Home</a>
    <a href="/pages/about.php" class="mobile-menu-link <?php echo($currentPage === 'about') ? 'active' : ''; ?>">About</a>
    <a href="/pages/dining-zones/index.php" class="mobile-menu-link <?php echo($currentPage === 'dining') ? 'active' : ''; ?>">Dining Zones</a>
    <a href="javascript:void(0)" class="mobile-menu-link">Reservations</a>
  </div>

  <div class="mobile-menu-actions">
    <a href="javascript:void(0)" class="btn btn-outline btn-block">Sign In</a>
    <a href="javascript:void(0)" class="btn btn-primary btn-block">Reserve a Table</a>
  </div>
</div>
