<?php
$content = <<<'HTML'
<?php
/**
 * Dashboard Profile — pages/dashboard/profile.php
 */

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
    $dashError = safe_error_message($e);
}

include '../../includes/header.php';
?>
<body>
<div class="dashboard-layout" id="dashboardLayout">

  <?php include '../../includes/dashboard-sidebar.php'; ?>

  <main class="dashboard-main">
    <div class="dashboard-content" style="max-width: 800px;">

      <?php if ($dashError): ?>
        <div class="auth-alert"><span><?= e($dashError) ?></span></div>
      <?php elseif ($dashSuccess): ?>
        <div class="auth-alert" style="border-color: var(--clr-success, #2e7d32); color: var(--clr-success, #2e7d32);"><span><?= e($dashSuccess) ?></span></div>
      <?php endif; ?>

      <header class="dashboard-header">
        <div class="dashboard-header-row">
          <div>
            <h1 class="dashboard-page-title">My Profile</h1>
            <p class="dashboard-page-subtitle">Manage your personal information and password.</p>
          </div>
        </div>
      </header>

      <div class="dash-section">
        <h2 style="font-size: 1.25rem; font-weight: 500; margin-bottom: 1.5rem;">Personal Information</h2>
        <form action="<?= $basePath ?>actions.php?action=update_profile" method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action_token" value="<?= e(action_token('update_profile')) ?>">
          
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label" for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" value="<?= e($user['first_name'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" value="<?= e($user['last_name'] ?? '') ?>" required>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= e($user['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?= e($user['phone'] ?? '') ?>">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
      </div>

      <div class="dash-section" style="margin-top: 2rem;">
        <h2 style="font-size: 1.25rem; font-weight: 500; margin-bottom: 1.5rem;">Change Password</h2>
        <form action="<?= $basePath ?>actions.php?action=update_password" method="POST">
            <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
            <input type="hidden" name="action_token" value="<?= e(action_token('update_password')) ?>">
          
            <div class="form-group" style="margin-bottom: 1rem;">
                <label class="form-label" for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" class="form-control" required>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1.5rem;">
                <div class="form-group">
                    <label class="form-label" for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required minlength="8">
                </div>
                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="8">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Password</button>
        </form>
      </div>

    </div>
  </main>
</div>

<script src="<?= $basePath ?>js/dashboard.js"></script>
</body>
</html>
HTML;

if (file_put_contents(__DIR__ . '/pages/dashboard/profile.php', $content) !== false) {
    echo "SUCCESS: Wrote Profile.php";
} else {
    echo "FAILURE: Could not write Profile.php";
}
