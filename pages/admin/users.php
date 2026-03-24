<?php
/**
 * Admin Users — pages/admin/users.php
 */

require_once '../../includes/security.php';
start_secure_session();
require_admin();

$pageTitle        = 'Guest Directory — Admin';
$pageCSS = ['dashboard.css', 'admin.css'];
$currentAdminPage = 'users';
$basePath         = '../../';

$adminError = get_flash('admin_error');

try {
  $pdo = db();
  $stmt = $pdo->prepare("SELECT u.user_id, u.first_name, u.last_name, u.email, u.phone, u.is_active, u.created_at, COUNT(a.appointment_id) AS total_res FROM users u JOIN roles r ON r.role_id = u.role_id LEFT JOIN appointments a ON a.user_id = u.user_id WHERE r.role_name IN ('guest','customer') GROUP BY u.user_id ORDER BY u.created_at DESC");
  $stmt->execute();
  $users = $stmt->fetchAll() ?: [];
  $stmt->closeCursor();
} catch (PDOException $e) {
  $adminError = safe_error_message($e);
  $users = [];
}

include '../../includes/header.php';
?>
<body>
<div class="admin-layout" id="adminLayout">

  <?php include '../../includes/admin-sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-content">

      <?php if ($adminError): ?>
        <div class="auth-alert"><span><?= e($adminError) ?></span></div>
      <?php endif; ?>

      <header class="admin-header">
        <div class="admin-header-row">
          <div>
            <h1 class="admin-page-title">Guest Directory</h1>
          </div>
        </div>
      </header>

      <div class="admin-section" style="margin-top:var(--space-8); border:none; box-shadow:none; padding:0;">
        <table class="admin-table guest-table" id="usersTable">
          <thead>
            <tr>
              <th>Name</th>
              <th>Email</th>
              <th>Phone</th>
              <th>Reservations</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($users as $u): ?>
              <?php $status = $u['is_active'] ? 'Active' : 'Inactive'; ?>
              <tr>
                <td style="font-weight:500;color:var(--clr-fg);"><?= e($u['first_name'] . ' ' . $u['last_name']) ?></td>
                <td style="color:var(--clr-muted-fg);font-size:var(--text-sm)"><?= e($u['email']) ?></td>
                <td style="color:var(--clr-muted-fg);font-size:var(--text-sm)"><?= e($u['phone'] ?? '—') ?></td>
                <td style="font-size:var(--text-sm);color:var(--clr-muted-fg);"><?= e((string) $u['total_res']) ?></td>
                <td>
                  <span class="badge badge-<?= $status === 'Active' ? 'confirmed' : 'cancelled' ?>"><?= e($status) ?></span>
                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($users)): ?>
              <tr><td colspan="5" style="text-align:center;color:var(--clr-muted-fg);padding:var(--space-8);">No guests found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>

</div>

<script src="<?= $basePath ?>js/admin.js"></script>
</body>
</html>





