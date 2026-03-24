<?php
/**
 * Dashboard My Profile — pages/dashboard/profile.php
 */

require_once '../../includes/security.php';
start_secure_session();
require_login();

$pageTitle       = 'My Profile';
$pageCSS         = ['dashboard.css'];
$currentDashPage = 'profile';
$basePath        = '../../';

$dashError = get_flash('dash_error');
$user = [];

try {
  $pdo = db();
  $stmt = $pdo->prepare('SELECT first_name, last_name, email, phone FROM users WHERE user_id = :uid LIMIT 1');
  $stmt->execute([':uid' => (int) $_SESSION['user_id']]);
  $user = $stmt->fetch() ?: [];
  $stmt->closeCursor();
} catch (PDOException $e) {
  $dashError = safe_error_message($e);
}

include '../../includes/header.php';
?>
<body>
<div class="dashboard-layout" id="dashboardLayout">

  <?php include '../../includes/dashboard-sidebar.php'; ?>

  <main class="dashboard-main">
    <div class="dashboard-content">

      <?php if ($dashError): ?>
        <div class="auth-alert"><span><?= e($dashError) ?></span></div>
      <?php endif; ?>

      <header class="dashboard-header">
        <div class="dashboard-header-row">
          <div>
            <h1 class="dashboard-page-title">My Profile</h1>
            <p class="dashboard-page-subtitle">Manage your personal information and preferences.</p>
          </div>
        </div>
      </header>

      <div class="profile-sections">

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
                  <input type="text" id="firstName" class="form-input" value="<?= e($user['first_name'] ?? '') ?>" readonly>
                </div>
                <div class="form-group">
                  <label for="lastName" class="form-label">Last Name</label>
                  <input type="text" id="lastName" class="form-input" value="<?= e($user['last_name'] ?? '') ?>" readonly>
                </div>
              </div>
              <div class="form-row" style="margin-bottom: var(--space-6);">
                <div class="form-group">
                  <label for="email" class="form-label">Email Address</label>
                  <input type="email" id="email" class="form-input" value="<?= e($user['email'] ?? '') ?>" readonly>
                </div>
                <div class="form-group">
                  <label for="phone" class="form-label">Phone Number</label>
                  <input type="tel" id="phone" class="form-input" value="<?= e($user['phone'] ?? '') ?>" readonly>
                </div>
              </div>
            </form>
          </div>
        </div>

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
          </div>
        </div>

      </div>

    </div>
  </main>

</div>

<script src="<?= $basePath ?>js/dashboard.js"></script>
</body>
</html>

