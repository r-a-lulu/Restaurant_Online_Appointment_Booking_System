<?php
/**
 * Dashboard My Profile — pages/dashboard/profile.php
 */

$pageTitle       = 'My Profile';
$pageCSS         = ['dashboard.css'];
$currentDashPage = 'profile';
$basePath        = '../../';

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
            <h1 class="dashboard-page-title">My Profile</h1>
            <p class="dashboard-page-subtitle">Manage your personal information and preferences.</p>
          </div>
        </div>
      </header>

      <div class="profile-sections">

        <!-- Personal Information -->
        <div class="profile-section">
          <div class="profile-section-header">
            <h2 class="profile-section-title">Personal Information</h2>
            <p class="profile-section-desc">Update your name, email address, and phone number.</p>
          </div>
          <div class="profile-section-body">
            <form>
              <div class="form-row" style="margin-bottom: var(--space-4);">
                <div class="form-group">
                  <label for="firstName" class="form-label">First Name</label>
                  <input type="text" id="firstName" class="form-input" value="Jane">
                </div>
                <div class="form-group">
                  <label for="lastName" class="form-label">Last Name</label>
                  <input type="text" id="lastName" class="form-input" value="Doe">
                </div>
              </div>
              <div class="form-row" style="margin-bottom: var(--space-6);">
                <div class="form-group">
                  <label for="email" class="form-label">Email Address</label>
                  <input type="email" id="email" class="form-input" value="jane.doe@email.com">
                </div>
                <div class="form-group">
                  <label for="phone" class="form-label">Phone Number</label>
                  <input type="tel" id="phone" class="form-input" value="+1 (555) 012-3456">
                </div>
              </div>
              <div style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Dining Preferences -->
        <div class="profile-section">
          <div class="profile-section-header">
            <h2 class="profile-section-title">Dining Preferences</h2>
            <p class="profile-section-desc">Help us personalise your experience.</p>
          </div>
          <div class="profile-section-body">
            <form>
              <div class="form-group" style="margin-bottom: var(--space-4);">
                <label for="favZone" class="form-label">Favourite Dining Zone</label>
                <select id="favZone" class="form-select">
                  <option value="">No preference</option>
                  <option value="patio" selected>Patio</option>
                  <option value="bar">Bar</option>
                  <option value="dining-room">Dining Room</option>
                </select>
              </div>
              <div class="form-group" style="margin-bottom: var(--space-4);">
                <label class="form-label">Dietary Restrictions</label>
                <div style="display: flex; flex-direction: column; gap: var(--space-2); margin-top: var(--space-2);">
                  <?php
                  $restrictions = ['Vegetarian', 'Vegan', 'Gluten-free', 'Nut allergy', 'Dairy-free', 'Halal'];
                  foreach ($restrictions as $r):
                  ?>
                  <label style="display: flex; align-items: center; gap: var(--space-2); font-size: var(--text-sm); cursor: pointer;">
                    <input type="checkbox" class="form-checkbox"><?= $r ?>
                  </label>
                  <?php endforeach; ?>
                </div>
              </div>
              <div class="form-group" style="margin-bottom: var(--space-6);">
                <label for="specialNotes" class="form-label">Special Notes</label>
                <textarea id="specialNotes" class="form-textarea" placeholder="Anything our team should know about your dining needs…" rows="2"></textarea>
              </div>
              <div style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">Save Preferences</button>
              </div>
            </form>
          </div>
        </div>

        <!-- Notifications -->
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
                <input type="checkbox" checked>
                <span class="switch-slider"></span>
              </label>
            </div>
            <div class="notification-row">
              <div class="notification-info">
                <p class="notification-label">SMS Reminders</p>
                <p class="notification-desc">Get a text message 24 hours before your reservation.</p>
              </div>
              <label class="switch" aria-label="SMS reminders">
                <input type="checkbox" checked>
                <span class="switch-slider"></span>
              </label>
            </div>
            <div class="notification-row">
              <div class="notification-info">
                <p class="notification-label">Promotional Offers</p>
                <p class="notification-desc">Hear about special events, menus, and exclusive offers.</p>
              </div>
              <label class="switch" aria-label="Promotional offers">
                <input type="checkbox">
                <span class="switch-slider"></span>
              </label>
            </div>
          </div>
        </div>

        <!-- Security -->
        <div class="profile-section">
          <div class="profile-section-header">
            <h2 class="profile-section-title">Security</h2>
            <p class="profile-section-desc">Update your password to keep your account secure.</p>
          </div>
          <div class="profile-section-body">
            <form>
              <div class="form-group" style="margin-bottom: var(--space-4);">
                <label for="currentPassword" class="form-label">Current Password</label>
                <div class="input-password-wrap">
                  <input type="password" id="currentPassword" class="form-input" placeholder="Enter current password">
                  <span class="input-password-toggle" onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'password' ? 'text' : 'password'">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  </span>
                </div>
              </div>
              <div class="form-row" style="margin-bottom: var(--space-6);">
                <div class="form-group">
                  <label for="newPassword" class="form-label">New Password</label>
                  <div class="input-password-wrap">
                    <input type="password" id="newPassword" class="form-input" placeholder="New password">
                    <span class="input-password-toggle" onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'password' ? 'text' : 'password'">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </span>
                  </div>
                </div>
                <div class="form-group">
                  <label for="confirmPassword" class="form-label">Confirm Password</label>
                  <div class="input-password-wrap">
                    <input type="password" id="confirmPassword" class="form-input" placeholder="Confirm new password">
                    <span class="input-password-toggle" onclick="this.previousElementSibling.type = this.previousElementSibling.type === 'password' ? 'text' : 'password'">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    </span>
                  </div>
                </div>
              </div>
              <div style="display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary">Update Password</button>
              </div>
            </form>
          </div>
        </div>

      </div><!-- /.profile-sections -->

    </div><!-- /.dashboard-content -->
  </main>

</div><!-- /.dashboard-layout -->

<script src="<?= $basePath ?>js/dashboard.js"></script>
</body>
</html>
