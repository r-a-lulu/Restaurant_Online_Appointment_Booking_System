<?php
/**
 * Dashboard Sidebar — Guest Dashboard partial.
 *
 * Required variables (set before including):
 *   $currentDashPage — string, one of: 'overview', 'book', 'reservations', 'history', 'profile'
 *   $basePath        — string, relative path back to root (e.g. '../../')
 */

$currentDashPage = $currentDashPage ?? 'overview';

$navLinks = [
    'overview'     => ['label' => 'Overview',          'href' => 'index.php',        'icon' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>'],
    'book'         => ['label' => 'Book Reservation',  'href' => 'book.php',         'icon' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="12" y1="11" x2="12" y2="17"/><line x1="9" y1="14" x2="15" y2="14"/>'],
    'reservations' => ['label' => 'My Reservations',   'href' => 'reservations.php', 'icon' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="M9 16l2 2 4-4"/>'],
    'history'      => ['label' => 'History',           'href' => 'history.php',      'icon' => '<path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/>'],
    'profile'      => ['label' => 'Profile',           'href' => 'profile.php',      'icon' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'],
];
?>

<!-- Mobile sidebar toggle button -->
<button class="sidebar-mobile-toggle" id="sidebarToggle" aria-label="Open navigation menu">
  <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
    <line x1="3" y1="12" x2="21" y2="12"/>
    <line x1="3" y1="6" x2="21" y2="6"/>
    <line x1="3" y1="18" x2="21" y2="18"/>
  </svg>
</button>

<!-- Sidebar overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Sidebar -->
<aside class="dashboard-sidebar" id="dashboardSidebar">

  <!-- Brand -->
  <div class="sidebar-brand">
    <a href="<?= $basePath ?>index.php" class="sidebar-brand-link">
      <span class="sidebar-brand-name">Eudaimonia</span>
    </a>
  </div>

  <hr class="sidebar-divider">

  <!-- Navigation -->
  <nav class="sidebar-nav" aria-label="Dashboard navigation">
    <ul class="sidebar-nav-list">
      <?php foreach ($navLinks as $key => $link): ?>
      <li>
        <a href="<?= $link['href'] ?>"
           class="sidebar-nav-link <?= ($currentDashPage === $key) ? 'active' : '' ?>"
           <?= ($currentDashPage === $key) ? 'aria-current="page"' : '' ?>>
          <svg class="sidebar-nav-icon" width="18" height="18" viewBox="0 0 24 24" fill="none"
               stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <?= $link['icon'] ?>
          </svg>
          <span class="nav-lbl-text"><?= $link['label'] ?></span>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
  </nav>

  <!-- Spacer -->
  <div class="sidebar-spacer"></div>

  <hr class="sidebar-divider">

  <!-- User -->
  <div class="sidebar-user">
    <div class="sidebar-avatar">JD</div>
    <div class="sidebar-user-info">
      <p class="sidebar-user-name">John Doe</p>
      <p class="sidebar-user-email">john@example.com</p>
    </div>
  </div>

  <!-- Footer links -->
  <div class="sidebar-footer">
    <a href="<?= $basePath ?>pages/login.php" class="sidebar-footer-link sidebar-logout">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
        <polyline points="16 17 21 12 16 7"/>
        <line x1="21" y1="12" x2="9" y2="12"/>
      </svg>
      <span>Sign Out</span>
    </a>
  </div>
</aside>
