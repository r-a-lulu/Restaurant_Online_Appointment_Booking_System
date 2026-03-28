<?php
require_once '../../includes/security.php';
start_secure_session();
require_login();

$pageTitle       = 'My Profile';
$pageCSS         = ['dashboard.css'];
$currentDashPage = 'profile';
$basePath        = '../../';

$dashError = get_flash('dash_error');
$dashSuccess = get_flash('dash_success');

// Fetch latest user details
try {
    $pdo = db();
    $stmt = $pdo->prepare("SELECT first_name, last_name, email, phone FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch() ?: [];
} catch (PDOException $e) {
    error_log('Dashboard profile load failed for user ' . (int) ($_SESSION['user_id'] ?? 0) . ': ' . $e->getMessage());
    $dashError = 'We could not load your profile right now. Please try again.';
}

$firstName = $user['first_name'] ?? '';
$lastName = $user['last_name'] ?? '';
$initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));

include '../../includes/header.php';
?>
<body>
<div class="dashboard-layout" id="dashboardLayout">

  <?php include '../../includes/dashboard-sidebar.php'; ?>

  <main class="dashboard-main">
    <div class="dashboard-content">

      <?php if ($dashError): ?>
        <div class="auth-alert" style="margin-bottom: var(--space-4);">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
          <span><?= e($dashError) ?></span>
        </div>
      <?php elseif ($dashSuccess): ?>
        <div class="auth-alert auth-success" style="margin-bottom: var(--space-4);">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
          <span><?= e($dashSuccess) ?></span>
        </div>
      <?php endif; ?>

      <header class="dashboard-header" style="border-bottom:none; margin-bottom:var(--space-6); padding-bottom:0;">
        <div class="dashboard-header-row">
          <div>
            <h1 class="dashboard-page-title">My Profile</h1>
            <p class="dashboard-page-subtitle" style="font-size:1rem; color:#5c4e36;">Manage your personal information and password</p>
          </div>
        </div>
      </header>
      
      <!-- Profile Header -->
      <div class="dash-section profile-hero" style="margin-bottom: var(--space-6); border-color:#e5cd9e;">
          <div class="profile-hero-avatar">
              <?= e($initials) ?>
          </div>
          <div class="profile-hero-details">
              <h2><?= e($firstName . ' ' . $lastName) ?></h2>
              <p>
                  <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                  <?= e($user['email'] ?? '') ?>
              </p>
          </div>
      </div>

      <!-- Personal Information Box -->
      <div class="dash-section" style="margin-bottom: var(--space-6); border-color:#e5cd9e;">
        <div class="dash-section-header" style="border-bottom:none; padding-bottom:var(--space-4);">
            <h2 class="dash-section-title" style="display:flex; align-items:center; gap:var(--space-3); color:#542125; font-size:1.25rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                Personal Information
            </h2>
        </div>
        
        <div class="dash-section-body">
            <form action="<?= $basePath ?>actions.php?action=update_profile" method="POST">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action_token" value="<?= e(action_token('update_profile')) ?>">
              
                <div class="profile-form-grid">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="first_name" style="font-family:var(--font-sans); font-size:0.9rem; font-weight:500; color:#5c4e36;">First Name</label>
                        <div style="margin-top:var(--space-2);">
                            <input type="text" id="first_name" name="first_name" class="form-input custom-soft-select" style="width: 100%; border: 1px solid #d1d5db;" value="<?= e($user['first_name'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="last_name" style="font-family:var(--font-sans); font-size:0.9rem; font-weight:500; color:#5c4e36;">Last Name</label>
                        <div style="margin-top:var(--space-2);">
                            <input type="text" id="last_name" name="last_name" class="form-input custom-soft-select" style="width: 100%; border: 1px solid #d1d5db;" value="<?= e($user['last_name'] ?? '') ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="profile-form-grid">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="email" style="font-family:var(--font-sans); font-size:0.9rem; font-weight:500; color:#5c4e36;">Email Address</label>
                        <div style="margin-top:var(--space-2);">
                            <input type="email" id="email" name="email" class="form-input custom-soft-select" style="width: 100%; border: 1px solid #d1d5db;" value="<?= e($user['email'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="phone" style="font-family:var(--font-sans); font-size:0.9rem; font-weight:500; color:#5c4e36;">Phone Number</label>
                        <div style="margin-top:var(--space-2);">
                            <input type="text" id="phone" name="phone" class="form-input custom-soft-select" style="width: 100%; border: 1px solid #d1d5db;" value="<?= e($user['phone'] ?? '') ?>">
                        </div>
                    </div>
                </div>
                <div class="profile-form-actions">
                    <button type="submit" class="btn-confirm-res" style="padding: 0.75rem 2rem; border-radius: 8px;">Save Changes &rarr;</button>
                </div>
            </form>
        </div>
      </div>

      <!-- Change Password Box -->
      <div class="dash-section" style="margin-bottom: var(--space-6); border-color:#e5cd9e;">
        <div class="dash-section-header" style="border-bottom:none; padding-bottom:var(--space-4);">
            <h2 class="dash-section-title" style="display:flex; align-items:center; gap:var(--space-3); color:#542125; font-size:1.25rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                Security & Password
            </h2>
        </div>
        
        <div class="dash-section-body">
            <form action="<?= $basePath ?>actions.php?action=update_password" method="POST">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <input type="hidden" name="action_token" value="<?= e(action_token('update_password')) ?>">
              
                <div class="profile-form-grid profile-form-grid--single">
                    <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="current_password" style="font-family:var(--font-sans); font-size:0.9rem; font-weight:500; color:#5c4e36;">Current Password</label>
                    <div style="margin-top:var(--space-2);">
                        <input type="password" id="current_password" name="current_password" class="form-input custom-soft-select" style="width: 100%; border: 1px solid #d1d5db;" required>
                    </div>
                </div>
                </div>
                
                <div class="profile-form-grid">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="new_password" style="font-family:var(--font-sans); font-size:0.9rem; font-weight:500; color:#5c4e36;">New Password</label>
                        <div style="margin-top:var(--space-2);">
                            <input type="password" id="new_password" name="new_password" class="form-input custom-soft-select" style="width: 100%; border: 1px solid #d1d5db;" required minlength="8">
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label" for="confirm_password" style="font-family:var(--font-sans); font-size:0.9rem; font-weight:500; color:#5c4e36;">Confirm New Password</label>
                        <div style="margin-top:var(--space-2);">
                            <input type="password" id="confirm_password" name="confirm_password" class="form-input custom-soft-select" style="width: 100%; border: 1px solid #d1d5db;" required minlength="8">
                        </div>
                    </div>
                </div>
                <div class="profile-form-actions">
                    <button type="submit" class="btn-confirm-res" style="padding: 0.75rem 2rem; border-radius: 8px;">Update Password &rarr;</button>
                </div>
            </form>
        </div>
      </div>

    </div>
  </main>
</div>

<script src="<?= $basePath ?>js/dashboard.js"></script>
</body>
</html>
