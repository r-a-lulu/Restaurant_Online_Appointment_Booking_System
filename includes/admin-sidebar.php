<?php
/**
 * Admin Sidebar — includes/admin-sidebar.php
 *
 * Required variables (set before including):
 *   $currentAdminPage — string, one of: 'dashboard', 'reservations', 'floor', 'guests', 'reports'
 *   $basePath         — string, relative path back to root (e.g. '../../')
 */

$currentAdminPage = $currentAdminPage ?? 'dashboard';
$siteName = get_setting('restaurant_name', 'Eudaimonia');

$navLinks = [
    'dashboard'    => [
        'label' => 'Dashboard',
        'href'  => 'index.php',
        'icon'  => '<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/>',
    ],
    'reservations' => [
        'label' => 'Reservations',
        'href'  => 'reservations.php',
        'icon'  => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>',
    ],
    'floor'        => [
        'label' => 'Floor Management',
        'href'  => 'floor.php',
        'icon'  => '<rect x="3" y="3" width="8" height="8" rx="1"/><rect x="13" y="3" width="8" height="8" rx="1"/><rect x="3" y="13" width="8" height="8" rx="1"/><rect x="13" y="13" width="8" height="8" rx="1"/>',
    ],
    'master-data'  => [
        'label' => 'Master Data',
        'href'  => 'master-data.php',
        'icon'  => '<path d="M4 4h16v6H4z"/><path d="M4 14h16v6H4z"/><path d="M8 10v4"/><path d="M16 10v4"/>',
    ],
    'guests'       => [
        'label' => 'Guests',
        'href'  => 'users.php',
        'icon'  => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>',
    ],
    'reports'      => [
        'label' => 'Reports',
        'href'  => 'reports.php',
        'icon'  => '<line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/>',
    ],
];
?>

<!-- Mobile Top Navigation Bar -->
<div class="mobile-topbar" id="adminMobileTopbar">
  <button class="sidebar-mobile-toggle" id="adminSidebarToggle" aria-label="Open admin menu">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
      <line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/>
    </svg>
  </button>
  <div class="mobile-topbar-brand"><?= e($siteName) ?></div>
</div>

<!-- Sidebar overlay (mobile) -->
<div class="sidebar-overlay" id="adminSidebarOverlay"></div>

<!-- Admin Sidebar -->
<aside class="dashboard-sidebar admin-sidebar" id="adminSidebar">

  <!-- Brand -->
  <div class="sidebar-brand">
    <a href="<?= $basePath ?>index.php" class="sidebar-brand-link" style="display:flex; align-items:center; gap:0.5rem;">
      <span class="sidebar-brand-name"><?= e($siteName) ?></span>
      <span class="admin-badge" style="margin-top:0;">Admin</span>
    </a>
  </div>

  <hr class="sidebar-divider">

  <!-- Navigation -->
  <nav class="sidebar-nav" aria-label="Admin navigation">
    <ul class="sidebar-nav-list">
      <?php foreach ($navLinks as $key => $link): ?>
      <li>
        <a href="<?= $link['href'] ?>"
           class="sidebar-nav-link <?= ($currentAdminPage === $key) ? 'active' : '' ?>"
           <?= ($currentAdminPage === $key) ? 'aria-current="page"' : '' ?>>
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

  <!-- Admin User Info -->
  <div class="sidebar-user">
    <div class="sidebar-avatar sidebar-avatar--admin">
      <?= e(strtoupper(substr($_SESSION['first_name'] ?? 'S', 0, 1) . substr($_SESSION['last_name'] ?? 'A', 0, 1))) ?>
    </div>
    <div class="sidebar-user-info">
      <p class="sidebar-user-name"><?= e($_SESSION['first_name'] ?? 'System') ?> <?= e($_SESSION['last_name'] ?? 'Admin') ?></p>
      <p class="sidebar-user-since">Administrator</p>
    </div>
    <!-- Profile Sign out using form POST -->
    <form method="post" action="<?= $basePath ?>actions.php?action=logout" style="margin: 0; padding: 0;">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="action_token" value="<?= e(action_token('logout')) ?>">
      <button type="submit" class="sidebar-signout" aria-label="Sign Out" title="Sign Out" style="background:none; border:none; cursor:pointer;" data-confirm="Are you sure you want to sign out?">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
          <polyline points="16 17 21 12 16 7"/>
          <line x1="21" y1="12" x2="9" y2="12"/>
        </svg>
      </button>
    </form>
  </div>

  <hr class="sidebar-divider">

  <!-- Footer links -->
  <div class="sidebar-footer">
    <a href="settings.php" class="sidebar-footer-link <?= ($currentAdminPage === 'settings') ? 'active' : '' ?>">
      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="3"/>
        <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>
      </svg>
      Settings
    </a>
    <form method="post" action="<?= $basePath ?>actions.php?action=logout" class="sidebar-footer-link sidebar-logout" style="margin: 0;" data-confirm="Are you sure you want to sign out?">
      <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
      <input type="hidden" name="action_token" value="<?= e(action_token('logout')) ?>">
      <button type="submit" style="all:unset; cursor:pointer; display:flex; align-items:center; gap:var(--space-4); color:inherit; width:100%; font:inherit;">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
          <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
          <polyline points="16 17 21 12 16 7"/>
          <line x1="21" y1="12" x2="9" y2="12"/>
        </svg>
        Sign Out
      </button>
    </form>
  </div>

</aside>
