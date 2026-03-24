<?php
/**
 * Admin Users — pages/admin/users.php
 */

$pageTitle        = 'Guest Directory — Admin';
$pageCSS = ['dashboard.css', 'admin.css'];
$currentAdminPage = 'users';
$basePath         = '../../';

include '../../includes/header.php';

$users = [
  ['initials'=>'SJ','name'=>'Sarah Johnson', 'email'=>'sarah@example.com', 'phone'=>'(555) 111-2222', 'res'=>12, 'status'=>'VIP'],
  ['initials'=>'MB','name'=>'Michael Brown', 'email'=>'michael@example.com', 'phone'=>'(555) 333-4444', 'res'=>8, 'status'=>'Regular'],
  ['initials'=>'ED','name'=>'Emily Davis',   'email'=>'emily@example.com', 'phone'=>'(555) 555-6666', 'res'=>5, 'status'=>'Regular'],
  ['initials'=>'JW','name'=>'James Wilson',  'email'=>'james@example.com', 'phone'=>'(555) 777-8888', 'res'=>25, 'status'=>'VIP'],
  ['initials'=>'LA','name'=>'Lisa Anderson', 'email'=>'lisa@example.com',  'phone'=>'(555) 999-0000', 'res'=>3, 'status'=>'New'],
  ['initials'=>'RT','name'=>'Robert Taylor', 'email'=>'robert@example.com','phone'=>'(555) 222-3333', 'res'=>18, 'status'=>'VIP'],
];
?>
<body>
<div class="admin-layout" id="adminLayout">

  <?php include '../../includes/admin-sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-content">

      <header class="admin-header">
        <div class="admin-header-row">
          <div>
            <h1 class="admin-page-title">Guest Directory</h1>
          </div>
        </div>
      </header>

      <div class="admin-section" style="margin-top:var(--space-8); border:none; box-shadow:none; padding:0;">
        <!-- Table -->
        <table class="admin-table guest-table" id="usersTable">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Reservations</th>
              <th>Status</th>
              <th style="text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $i => $u): ?>
            <tr data-user-index="<?= $i ?>">
              <td class="guest-name" style="font-weight:500;color:var(--clr-fg);"><?= $u['name'] ?></td>
              <td style="color:var(--clr-muted-fg);font-size:var(--text-sm)"><?= $u['email'] ?></td>
              <td style="color:var(--clr-muted-fg);font-size:var(--text-sm)"><?= $u['phone'] ?></td>
              <td style="font-size:var(--text-sm);color:var(--clr-muted-fg);"><?= $u['res'] ?></td>
              <td class="status-cell">
                <span class="badge badge-<?= strtolower($u['status']) ?> guest-status-badge"><?= $u['status'] ?></span>
              </td>
              <td style="text-align:right;">
                <button class="btn btn-icon view-profile-btn" data-modal-open="userProfileModal" aria-label="View Profile">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color:var(--clr-fg);opacity:0.8"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>

</div>

<!-- User Profile Modal -->
<div class="admin-modal" id="userProfileModal">
  <div class="admin-modal-card guest-profile-card">
    <div class="admin-modal-header" style="border-bottom:none; margin-bottom:0; padding-bottom:var(--space-2);">
      <h2 class="admin-modal-title" style="font-size:var(--text-xl);">Guest Profile</h2>
      <button class="admin-modal-close" data-modal-close aria-label="Close">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>

    <!-- Header info -->
    <div class="guest-profile-header">
      <div class="guest-avatar-large" id="modalGuestInitials">MB</div>
      <div class="guest-info">
        <h3 id="modalGuestName">Michael Brown</h3>
        
        <!-- View Mode -->
        <div class="guest-status-wrapper" id="guestStatusView" style="display: flex; gap: var(--space-2); align-items: center;">
          <span class="badge badge-regular" id="modalGuestStatus">Regular</span>
          <button class="btn btn-icon btn-ghost" id="editGuestStatusBtn" style="width: 24px; height: 24px; padding: 0; color: var(--clr-muted-fg);" aria-label="Edit Status">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
          </button>
        </div>
        
        <!-- Edit Mode -->
        <div class="guest-status-edit" id="guestStatusEdit" style="display: none; gap: var(--space-2); align-items: center; margin-top: 4px;">
          <select class="form-select status-select" id="guestStatusSelect" style="padding: 2px 8px; font-size: var(--text-xs); height: 28px; width: auto; background-color: var(--bg-card); border-color: rgba(0,0,0,0.1);">
            <option value="VIP">VIP</option>
            <option value="Regular">Regular</option>
            <option value="New">New</option>
          </select>
          <button class="btn btn-primary" id="saveGuestStatusBtn" style="padding: 0 8px; height: 28px; font-size: var(--text-xs);">Save</button>
          <button class="btn btn-ghost" id="cancelGuestStatusBtn" style="padding: 0 8px; height: 28px; font-size: var(--text-xs);">Cancel</button>
        </div>

      </div>
    </div>

    <!-- Contact details -->
    <div class="guest-contact-list">
      <div class="contact-item">
        <svg class="contact-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
        michael@example.com
      </div>
      <div class="contact-item">
        <svg class="contact-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
        (555) 333-4444
      </div>
      <div class="contact-item">
        <svg class="contact-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        Member since Mar 22, 2025
      </div>
    </div>

    <!-- Stats grid -->
    <div class="guest-stats-split">
      <div class="guest-stat">
        <div class="guest-stat-val">8</div>
        <div class="guest-stat-lbl">Total Visits</div>
      </div>
      <div class="guest-stat" style="border-left: 1px solid rgba(0,0,0,0.06);">
        <div class="guest-stat-val" style="font-size: var(--text-base); font-weight:600; font-family:var(--font-sans); margin-bottom:6px;">Mar 1, 2026</div>
        <div class="guest-stat-lbl">Last Visit</div>
      </div>
    </div>

    <!-- Preferences Box -->
    <div class="guest-prefs-box">
      <div class="guest-prefs-title">Preferences & Notes</div>
      <div class="guest-prefs-desc">Bar seating preferred</div>
    </div>

    <div class="guest-modal-actions">
      <button class="btn btn-outline" style="flex:1; justify-content:center; display:flex; align-items:center; gap:var(--space-2);">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2" ry="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
        Email
      </button>
      <button class="btn btn-primary" style="flex:1; justify-content:center;">View History</button>
    </div>

  </div>
</div>

<script src="<?= $basePath ?>js/admin.js"></script>
</body>
</html>
