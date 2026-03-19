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
    'overview'     => ['label' => 'Overview',        'href' => 'index.php',        'icon' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>'],
    'book'         => ['label' => 'Book a Table',    'href' => 'book.php',         'icon' => '<rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>'],
    'reservations' => ['label' => 'My Reservations', 'href' => 'reservations.php', 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>'],
    'history'      => ['label' => 'Dining History',  'href' => 'history.php',      'icon' => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'],
    'profile'      => ['label' => 'My Profile',      'href' => 'profile.php',      'icon' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'],
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

  <!-- User -->
  <div class="sidebar-user">
    <div class="sidebar-avatar">JD</div>
    <div class="sidebar-user-info">
      <p class="sidebar-user-name">Jane Doe</p>
      <p class="sidebar-user-since">Member since Jan 2024</p>
    </div>
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
               stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
            <?= $link['icon'] ?>
          </svg>
          <span><?= $link['label'] ?></span>
        </a>
      </li>
      <?php endforeach; ?>
    </ul>
  </nav>

  <!-- Spacer -->
  <div class="sidebar-spacer"></div>

  <hr class="sidebar-divider">

  <!-- Footer links -->
  <div class="sidebar-footer">
    <a href="<?= $basePath ?>index.php" class="sidebar-footer-link">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
      </svg>
      Back to Site
    </a>
    <a href="<?= $basePath ?>pages/login.php" class="sidebar-footer-link sidebar-logout">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
        <polyline points="16 17 21 12 16 7"/>
        <line x1="21" y1="12" x2="9" y2="12"/>
      </svg>
      Log Out
    </a>
  </div>

</aside>
