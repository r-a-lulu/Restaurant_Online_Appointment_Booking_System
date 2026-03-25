<?php
/**
 * Admin Settings — pages/admin/settings.php
 */

$pageTitle        = 'Settings';
$pageCSS          = ['dashboard.css', 'admin.css'];
$currentAdminPage = 'settings';
$basePath         = '../../';

require_once '../../includes/security.php';
start_secure_session();
require_admin();

// Load settings from database
$settings = [];
try {
  $pdo = db();
  $stmt = $pdo->query("SELECT setting_key, setting_value FROM system_settings");
  while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
  }
} catch (PDOException $e) {
  // Table may not exist yet, use defaults
}

$saveSuccess = get_flash('settings_success');
$saveError = get_flash('settings_error');

include '../../includes/header.php';
?>
<body>
<div class="admin-layout" id="adminLayout">

  <?php include '../../includes/admin-sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-content">

      <!-- Page Header -->
      <header class="admin-header">
        <div class="admin-header-row">
          <div>
            <h1 class="admin-page-title">Settings</h1>
            <p class="admin-page-subtitle">Configure restaurant details, booking rules, and system preferences.</p>
          </div>
        </div>
        <?php if ($saveSuccess): ?>
          <div class="auth-alert auth-success" style="margin-top: var(--space-4);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span><?= e($saveSuccess) ?></span>
          </div>
        <?php endif; ?>
        <?php if ($saveError): ?>
          <div class="auth-alert" style="margin-top: var(--space-4);">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span><?= e($saveError) ?></span>
          </div>
        <?php endif; ?>
      </header>

      <div class="profile-sections">

        <!-- General Information -->
        <div class="profile-section">
          <div class="profile-section-header">
            <h2 class="profile-section-title">General Information</h2>
            <p class="profile-section-desc">Basic details displayed to guests throughout the system.</p>
          </div>
          <div class="profile-section-body">
            <form method="post" action="../../actions.php?action=save_settings" id="generalSettingsForm">
              <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="action_token" value="<?= e(action_token('save_settings')) ?>">
              <input type="hidden" name="section" value="general">
              <div class="form-row" style="margin-bottom: var(--space-4);">
                <div class="form-group">
                  <label for="restaurantName" class="form-label">Restaurant Name</label>
                  <input type="text" id="restaurantName" name="restaurant_name" class="form-input" value="<?= e($settings['restaurant_name'] ?? 'Eudaimonia') ?>">
                </div>
                <div class="form-group">
                  <label for="restaurantEmail" class="form-label">Contact Email</label>
                  <input type="email" id="restaurantEmail" name="restaurant_email" class="form-input" value="<?= e($settings['restaurant_email'] ?? 'hello@eudaimonia.com') ?>">
                </div>
              </div>
              <div class="form-row" style="margin-bottom: var(--space-4);">
                <div class="form-group">
                  <label for="restaurantPhone" class="form-label">Phone Number</label>
                  <input type="tel" id="restaurantPhone" name="restaurant_phone" class="form-input" value="<?= e($settings['restaurant_phone'] ?? '+1 (555) 000-1234') ?>">
                </div>
                <div class="form-group">
                  <label for="restaurantAddress" class="form-label">Address</label>
                  <input type="text" id="restaurantAddress" name="restaurant_address" class="form-input" value="<?= e($settings['restaurant_address'] ?? '12 Harmony Lane, New York, NY') ?>">
                </div>
              </div>
              <div class="form-group" style="margin-bottom: var(--space-6);">
                <label for="restaurantDesc" class="form-label">Short Description</label>
                <textarea id="restaurantDesc" name="restaurant_description" class="form-textarea" rows="2"><?= e($settings['restaurant_description'] ?? 'A contemporary dining experience rooted in timeless hospitality.') ?></textarea>
              </div>
              <div style="display:flex; justify-content:flex-end;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Notifications -->
        <div class="profile-section">
          <div class="profile-section-header">
            <h2 class="profile-section-title">Admin Notifications</h2>
            <p class="profile-section-desc">Choose what system events you receive alerts for.</p>
          </div>
          <div class="profile-section-body" style="padding-top:0;">
            <form method="post" action="../../actions.php?action=save_settings" id="notificationsForm">
              <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="action_token" value="<?= e(action_token('save_settings')) ?>">
              <input type="hidden" name="section" value="notifications">
              <div class="notification-row">
                <div class="notification-info">
                  <p class="notification-label">New Reservation Alert</p>
                  <p class="notification-desc">Receive an email when a new reservation is submitted.</p>
                </div>
                <label class="switch" aria-label="New reservation alert">
                  <input type="checkbox" name="notify_new_reservation" value="1" <?= ($settings['notify_new_reservation'] ?? '1') === '1' ? 'checked' : '' ?>>
                  <span class="switch-slider"></span>
                </label>
              </div>
              <div class="notification-row">
                <div class="notification-info">
                  <p class="notification-label">Cancellation Alert</p>
                  <p class="notification-desc">Get notified when a guest cancels a reservation.</p>
                </div>
                <label class="switch" aria-label="Cancellation alert">
                  <input type="checkbox" name="notify_cancellation" value="1" <?= ($settings['notify_cancellation'] ?? '1') === '1' ? 'checked' : '' ?>>
                  <span class="switch-slider"></span>
                </label>
              </div>
              <div class="notification-row">
                <div class="notification-info">
                  <p class="notification-label">Daily Summary Report</p>
                  <p class="notification-desc">Receive a daily digest of reservations and occupancy.</p>
                </div>
                <label class="switch" aria-label="Daily summary report">
                  <input type="checkbox" name="notify_daily_summary" value="1" <?= ($settings['notify_daily_summary'] ?? '0') === '1' ? 'checked' : '' ?>>
                  <span class="switch-slider"></span>
                </label>
              </div>
              <div style="display:flex; justify-content:flex-end; margin-top:var(--space-5);">
                <button type="submit" class="btn btn-primary">Save Notifications</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Maintenance Mode -->
        <div class="profile-section" style="border-color: #f59e0b;">
          <div class="profile-section-header">
            <h2 class="profile-section-title" style="color: #d97706;">Maintenance Mode</h2>
            <p class="profile-section-desc">When enabled, guests will see a maintenance message instead of the booking form.</p>
          </div>
          <div class="profile-section-body" style="padding-top:0;">
            <form method="post" action="../../actions.php?action=save_settings" id="maintenanceForm">
              <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="action_token" value="<?= e(action_token('save_settings')) ?>">
              <input type="hidden" name="section" value="maintenance">
              <div class="notification-row">
                <div class="notification-info">
                  <p class="notification-label">Enable Maintenance Mode</p>
                  <p class="notification-desc">Temporarily disable online reservations for guests.</p>
                </div>
                <label class="switch" aria-label="Maintenance mode">
                  <input type="checkbox" name="maintenance_mode" value="1" id="maintenanceMode" <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' ?>>
                  <span class="switch-slider"></span>
                </label>
              </div>
              <div class="form-group" style="margin-top:var(--space-4); margin-bottom:var(--space-5);">
                <label for="maintenanceMsg" class="form-label">Maintenance Message</label>
                <textarea id="maintenanceMsg" name="maintenance_message" class="form-textarea" rows="2" placeholder="We're temporarily offline for maintenance. Please check back shortly."><?= e($settings['maintenance_message'] ?? "We're temporarily offline for maintenance. Please check back shortly.") ?></textarea>
              </div>
              <div style="display:flex; justify-content:flex-end;">
                <button type="submit" class="btn btn-primary">Save</button>
              </div>
            </form>
          </div>
        </div>

      </div><!-- /.profile-sections -->

    </div><!-- /.admin-content -->
  </main>

</div><!-- /.admin-layout -->

<!-- Toast Notification -->
<div id="adminSettingsToast" style="
  position:fixed; bottom:1.5rem; right:1.5rem; z-index:9999;
  background: var(--card-bg, #1e293b); color: var(--text-primary, #f8fafc);
  padding: var(--space-3) var(--space-5); border-radius: 0.5rem;
  box-shadow: 0 4px 20px rgba(0,0,0,0.3); font-size: var(--text-sm);
  opacity:0; transform:translateY(0.5rem);
  transition: opacity 0.25s ease, transform 0.25s ease; pointer-events:none;">
</div>

<script>
function showAdminToast(msg) {
  const t = document.getElementById('adminSettingsToast');
  t.textContent = msg;
  t.style.opacity = '1';
  t.style.transform = 'translateY(0)';
  setTimeout(() => {
    t.style.opacity = '0';
    t.style.transform = 'translateY(0.5rem)';
  }, 2800);
}
</script>

<script src="<?= $basePath ?>js/admin.js"></script>
</body>
</html>
