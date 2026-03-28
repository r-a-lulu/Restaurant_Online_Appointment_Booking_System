<?php
/**
 * Guest Settings â€” pages/dashboard/settings.php
 */

$pageTitle       = 'Settings';
$pageCSS         = ['dashboard.css'];
$currentDashPage = 'settings';
$basePath        = '../../';

require_once '../../includes/security.php';
start_secure_session();
require_login();

include '../../includes/header.php';
?>
<body>
<div class="dashboard-layout" id="dashboardLayout">

  <?php include '../../includes/dashboard-sidebar.php'; ?>

  <main class="dashboard-main">
    <div class="dashboard-content">

      <header class="dashboard-header">
        <div class="dashboard-header-row">
          <div>
            <h1 class="dashboard-page-title">Settings</h1>
            <p class="dashboard-page-subtitle">Manage your account preferences and security options.</p>
          </div>
        </div>
      </header>

      <div class="profile-sections">

        <!-- Notification Preferences -->
        <div class="profile-section">
          <div class="profile-section-header">
            <h2 class="profile-section-title">Notifications</h2>
            <p class="profile-section-desc">Choose how you'd like to hear from us.</p>
          </div>
          <div class="profile-section-body" style="padding-top: 0;">
            <div class="notification-row">
              <div class="notification-info">
                <p class="notification-label">Email Confirmations</p>
                <p class="notification-desc">Receive email confirmations and reminders for your reservations.</p>
              </div>
              <label class="switch" aria-label="Email confirmations">
                <input type="checkbox" id="notifEmail" checked>
                <span class="switch-slider"></span>
              </label>
            </div>
            <div class="notification-row">
              <div class="notification-info">
                <p class="notification-label">SMS Reminders</p>
                <p class="notification-desc">Get a text message 24 hours before your reservation.</p>
              </div>
              <label class="switch" aria-label="SMS reminders">
                <input type="checkbox" id="notifSMS" checked>
                <span class="switch-slider"></span>
              </label>
            </div>
            <div class="notification-row">
              <div class="notification-info">
                <p class="notification-label">Promotional Offers</p>
                <p class="notification-desc">Hear about special events, menus, and exclusive offers.</p>
              </div>
              <label class="switch" aria-label="Promotional offers">
                <input type="checkbox" id="notifPromo">
                <span class="switch-slider"></span>
              </label>
            </div>
            <div class="profile-form-actions profile-form-actions--end" style="margin-top: var(--space-5);">
              <button type="button" class="btn btn-primary" onclick="showSettingsToast('Notification preferences saved.')">Save Preferences</button>
            </div>
          </div>
        </div>

        <!-- Security -->
        <div class="profile-section">
          <div class="profile-section-header">
            <h2 class="profile-section-title">Security</h2>
            <p class="profile-section-desc">Control access and two-factor authentication for your account.</p>
          </div>
          <div class="profile-section-body" style="padding-top: 0;">
            <div class="notification-row">
              <div class="notification-info">
                <p class="notification-label">Two-Factor Authentication</p>
                <p class="notification-desc">Add an extra layer of security when signing in.</p>
              </div>
              <label class="switch" aria-label="Two-factor authentication">
                <input type="checkbox" id="twoFactor">
                <span class="switch-slider"></span>
              </label>
            </div>
            <div class="notification-row">
              <div class="notification-info">
                <p class="notification-label">Remember This Device</p>
                <p class="notification-desc">Stay signed in on trusted devices for 30 days.</p>
              </div>
              <label class="switch" aria-label="Remember device">
                <input type="checkbox" id="rememberDevice" checked>
                <span class="switch-slider"></span>
              </label>
            </div>
            <div class="profile-form-actions profile-form-actions--end" style="margin-top: var(--space-5);">
              <button type="button" class="btn btn-primary" onclick="showSettingsToast('Security settings saved.')">Save Settings</button>
            </div>
          </div>
        </div>

        <!-- Privacy -->
        <div class="profile-section">
          <div class="profile-section-header">
            <h2 class="profile-section-title">Privacy</h2>
            <p class="profile-section-desc">Control how your data is used.</p>
          </div>
          <div class="profile-section-body" style="padding-top: 0;">
            <div class="notification-row">
              <div class="notification-info">
                <p class="notification-label">Save Dining History</p>
                <p class="notification-desc">Allow us to keep a record of your past reservations.</p>
              </div>
              <label class="switch" aria-label="Save dining history">
                <input type="checkbox" id="saveDiningHistory" checked>
                <span class="switch-slider"></span>
              </label>
            </div>
            <div class="notification-row">
              <div class="notification-info">
                <p class="notification-label">Share Preferences Anonymously</p>
                <p class="notification-desc">Help us improve by sharing anonymised usage data.</p>
              </div>
              <label class="switch" aria-label="Share preferences anonymously">
                <input type="checkbox" id="shareAnon">
                <span class="switch-slider"></span>
              </label>
            </div>
            <div class="profile-form-actions profile-form-actions--end" style="margin-top: var(--space-5);">
              <button type="button" class="btn btn-primary" onclick="showSettingsToast('Privacy settings saved.')">Save Settings</button>
            </div>
          </div>
        </div>

        <!-- Danger Zone -->
        <div class="profile-section" style="border-color: #f87171;">
          <div class="profile-section-header">
            <h2 class="profile-section-title" style="color: #ef4444;">Danger Zone</h2>
            <p class="profile-section-desc">Irreversible actions. Please proceed with caution.</p>
          </div>
          <div class="profile-section-body">
            <div class="notification-row" style="border-bottom: none; padding-block: 0; align-items: center; flex-wrap: wrap;">
              <div>
                <p class="notification-label">Delete Account</p>
                <p class="notification-desc">Permanently remove your account and all associated data.</p>
              </div>
              <button type="button" class="btn" id="deleteAccountBtn"
                style="background:transparent; color:#ef4444; border:1px solid #ef4444; white-space:nowrap;"
                onclick="document.getElementById('deleteAccountModal').style.display='flex'">
                Delete Account
              </button>
            </div>
          </div>
        </div>

      </div><!-- /.profile-sections -->

    </div><!-- /.dashboard-content -->
  </main>

</div><!-- /.dashboard-layout -->

<!-- Delete Account Confirmation Modal -->
<div id="deleteAccountModal" class="dashboard-modal" style="display:none;">
  <div class="dashboard-modal-card" style="max-width:420px;">
    <div class="dashboard-modal-header">
      <h2 class="dashboard-modal-title">Delete Account</h2>
      <button class="dashboard-modal-close" aria-label="Close"
        onclick="document.getElementById('deleteAccountModal').style.display='none'">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
      </button>
    </div>
    <div style="padding: var(--space-5) var(--space-6);">
      <p style="color: var(--text-muted); font-size: var(--text-sm); margin-bottom: var(--space-4);">
        This will permanently delete your account, all reservations, and data. This action <strong>cannot be undone</strong>.
      </p>
      <div class="form-group">
        <label for="deleteConfirmInput" class="form-label">Type <strong>DELETE</strong> to confirm</label>
        <input type="text" id="deleteConfirmInput" class="form-input" placeholder="DELETE">
      </div>
    </div>
    <div class="dashboard-modal-footer">
      <button class="btn btn-outline" onclick="document.getElementById('deleteAccountModal').style.display='none'">Cancel</button>
      <button class="btn" id="confirmDeleteBtn"
        style="background:#ef4444; color:#fff; border:none;"
        onclick="
          if(document.getElementById('deleteConfirmInput').value === 'DELETE'){
            showSettingsToast('Account deletion requested.');
            document.getElementById('deleteAccountModal').style.display='none';
          } else {
            document.getElementById('deleteConfirmInput').style.borderColor='#ef4444';
          }
        ">
        Delete My Account
      </button>
    </div>
  </div>
</div>

<!-- Toast Notification -->
<div id="settingsToast" style="
  position:fixed; bottom:calc(1.5rem + env(safe-area-inset-bottom)); left:50%; z-index:9999;
  width: max-content; max-width: calc(100vw - 3rem);
  background: var(--card-bg, #1e293b); color: var(--text-primary, #f8fafc);
  padding: var(--space-3) var(--space-5); border-radius: 0.5rem;
  box-shadow: 0 4px 20px rgba(0,0,0,0.3); font-size: var(--text-sm);
  opacity:0; transform:translate(-50%, 0.5rem);
  transition: opacity 0.25s ease, transform 0.25s ease; pointer-events:none;
  text-align: center;">
</div>

<script>
function showSettingsToast(msg) {
  const t = document.getElementById('settingsToast');
  t.textContent = msg;
  t.style.opacity = '1';
  t.style.transform = 'translate(-50%, 0)';
  setTimeout(() => {
    t.style.opacity = '0';
    t.style.transform = 'translate(-50%, 0.5rem)';
  }, 2800);
}
</script>

<script src="<?= $basePath ?>js/dashboard.js"></script>
</body>
</html>

