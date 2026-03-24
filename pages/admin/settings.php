<?php
/**
 * Admin Settings — pages/admin/settings.php
 */

$pageTitle        = 'Settings';
$pageCSS          = ['dashboard.css', 'admin.css'];
$currentAdminPage = 'settings';
$basePath         = '../../';

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
      </header>

      <div class="profile-sections">

        <!-- General Information -->
        <div class="profile-section">
          <div class="profile-section-header">
            <h2 class="profile-section-title">General Information</h2>
            <p class="profile-section-desc">Basic details displayed to guests throughout the system.</p>
          </div>
          <div class="profile-section-body">
            <form>
              <div class="form-row" style="margin-bottom: var(--space-4);">
                <div class="form-group">
                  <label for="restaurantName" class="form-label">Restaurant Name</label>
                  <input type="text" id="restaurantName" class="form-input" value="Eudaimonia">
                </div>
                <div class="form-group">
                  <label for="restaurantEmail" class="form-label">Contact Email</label>
                  <input type="email" id="restaurantEmail" class="form-input" value="hello@eudaimonia.com">
                </div>
              </div>
              <div class="form-row" style="margin-bottom: var(--space-4);">
                <div class="form-group">
                  <label for="restaurantPhone" class="form-label">Phone Number</label>
                  <input type="tel" id="restaurantPhone" class="form-input" value="+1 (555) 000-1234">
                </div>
                <div class="form-group">
                  <label for="restaurantAddress" class="form-label">Address</label>
                  <input type="text" id="restaurantAddress" class="form-input" value="12 Harmony Lane, New York, NY">
                </div>
              </div>
              <div class="form-group" style="margin-bottom: var(--space-6);">
                <label for="restaurantDesc" class="form-label">Short Description</label>
                <textarea id="restaurantDesc" class="form-textarea" rows="2">A contemporary dining experience rooted in timeless hospitality.</textarea>
              </div>
              <div style="display:flex; justify-content:flex-end;">
                <button type="button" class="btn btn-primary" onclick="showAdminToast('General information saved.')">Save Changes</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Operating Hours -->
        <div class="profile-section">
          <div class="profile-section-header">
            <h2 class="profile-section-title">Operating Hours</h2>
            <p class="profile-section-desc">Set the daily opening and closing times shown during booking.</p>
          </div>
          <div class="profile-section-body">
            <form>
              <?php
              $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
              $defaultOpen  = ['Monday'=>'11:00','Tuesday'=>'11:00','Wednesday'=>'11:00','Thursday'=>'11:00','Friday'=>'11:00','Saturday'=>'10:00','Sunday'=>'10:00'];
              $defaultClose = ['Monday'=>'22:00','Tuesday'=>'22:00','Wednesday'=>'22:00','Thursday'=>'23:00','Friday'=>'23:00','Saturday'=>'23:00','Sunday'=>'21:00'];
              foreach ($days as $day): ?>
              <div style="display:flex; align-items:center; gap:var(--space-4); margin-bottom:var(--space-3); flex-wrap:wrap;">
                <span style="width:100px; font-size:var(--text-sm); color:var(--text-secondary);"><?= $day ?></span>
                <div class="form-group" style="margin:0; flex:1; min-width:120px;">
                  <label for="open<?= $day ?>" class="form-label" style="font-size:0.7rem;">Open</label>
                  <input type="time" id="open<?= $day ?>" class="form-input" value="<?= $defaultOpen[$day] ?>">
                </div>
                <div class="form-group" style="margin:0; flex:1; min-width:120px;">
                  <label for="close<?= $day ?>" class="form-label" style="font-size:0.7rem;">Close</label>
                  <input type="time" id="close<?= $day ?>" class="form-input" value="<?= $defaultClose[$day] ?>">
                </div>
                <label class="switch" aria-label="Enable <?= $day ?>" style="margin-top:1.2rem;">
                  <input type="checkbox" checked>
                  <span class="switch-slider"></span>
                </label>
              </div>
              <?php endforeach; ?>
              <div style="display:flex; justify-content:flex-end; margin-top:var(--space-5);">
                <button type="button" class="btn btn-primary" onclick="showAdminToast('Operating hours saved.')">Save Hours</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Booking Rules -->
        <div class="profile-section">
          <div class="profile-section-header">
            <h2 class="profile-section-title">Booking Rules</h2>
            <p class="profile-section-desc">Control reservation limits and advance booking windows.</p>
          </div>
          <div class="profile-section-body">
            <form>
              <div class="form-row" style="margin-bottom: var(--space-4);">
                <div class="form-group">
                  <label for="maxPartySize" class="form-label">Max Party Size</label>
                  <input type="number" id="maxPartySize" class="form-input" value="12" min="1">
                </div>
                <div class="form-group">
                  <label for="slotDuration" class="form-label">Slot Duration (minutes)</label>
                  <input type="number" id="slotDuration" class="form-input" value="90" min="30" step="15">
                </div>
              </div>
              <div class="form-row" style="margin-bottom: var(--space-4);">
                <div class="form-group">
                  <label for="minAdvance" class="form-label">Min. Advance Booking (hours)</label>
                  <input type="number" id="minAdvance" class="form-input" value="2" min="0">
                </div>
                <div class="form-group">
                  <label for="maxAdvance" class="form-label">Max. Advance Booking (days)</label>
                  <input type="number" id="maxAdvance" class="form-input" value="60" min="1">
                </div>
              </div>
              <div class="form-group" style="margin-bottom: var(--space-6);">
                <label for="cancellationPolicy" class="form-label">Cancellation Policy (hours before)</label>
                <input type="number" id="cancellationPolicy" class="form-input" value="24" min="0">
              </div>
              <div style="display:flex; justify-content:flex-end;">
                <button type="button" class="btn btn-primary" onclick="showAdminToast('Booking rules saved.')">Save Rules</button>
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
            <div class="notification-row">
              <div class="notification-info">
                <p class="notification-label">New Reservation Alert</p>
                <p class="notification-desc">Receive an email when a new reservation is submitted.</p>
              </div>
              <label class="switch" aria-label="New reservation alert">
                <input type="checkbox" checked>
                <span class="switch-slider"></span>
              </label>
            </div>
            <div class="notification-row">
              <div class="notification-info">
                <p class="notification-label">Cancellation Alert</p>
                <p class="notification-desc">Get notified when a guest cancels a reservation.</p>
              </div>
              <label class="switch" aria-label="Cancellation alert">
                <input type="checkbox" checked>
                <span class="switch-slider"></span>
              </label>
            </div>
            <div class="notification-row">
              <div class="notification-info">
                <p class="notification-label">Daily Summary Report</p>
                <p class="notification-desc">Receive a daily digest of reservations and occupancy.</p>
              </div>
              <label class="switch" aria-label="Daily summary report">
                <input type="checkbox">
                <span class="switch-slider"></span>
              </label>
            </div>
            <div style="display:flex; justify-content:flex-end; margin-top:var(--space-5);">
              <button type="button" class="btn btn-primary" onclick="showAdminToast('Notification settings saved.')">Save Notifications</button>
            </div>
          </div>
        </div>

        <!-- Maintenance Mode -->
        <div class="profile-section" style="border-color: #f59e0b;">
          <div class="profile-section-header">
            <h2 class="profile-section-title" style="color: #d97706;">Maintenance Mode</h2>
            <p class="profile-section-desc">When enabled, guests will see a maintenance message instead of the booking form.</p>
          </div>
          <div class="profile-section-body" style="padding-top:0;">
            <div class="notification-row">
              <div class="notification-info">
                <p class="notification-label">Enable Maintenance Mode</p>
                <p class="notification-desc">Temporarily disable online reservations for guests.</p>
              </div>
              <label class="switch" aria-label="Maintenance mode">
                <input type="checkbox" id="maintenanceMode">
                <span class="switch-slider"></span>
              </label>
            </div>
            <div class="form-group" style="margin-top:var(--space-4); margin-bottom:var(--space-5);">
              <label for="maintenanceMsg" class="form-label">Maintenance Message</label>
              <textarea id="maintenanceMsg" class="form-textarea" rows="2" placeholder="We're temporarily offline for maintenance. Please check back shortly.">We're temporarily offline for maintenance. Please check back shortly.</textarea>
            </div>
            <div style="display:flex; justify-content:flex-end;">
              <button type="button" class="btn btn-primary" onclick="showAdminToast('Maintenance settings saved.')">Save</button>
            </div>
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
