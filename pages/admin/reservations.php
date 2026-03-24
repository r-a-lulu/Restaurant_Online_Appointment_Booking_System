<?php
/**
 * Admin Reservations Manager â€” pages/admin/reservations.php
 */

require_once '../../includes/security.php';
start_secure_session();
require_admin();

$pageTitle        = 'Reservations â€” Admin';
$pageCSS = ['dashboard.css', 'admin.css'];
$currentAdminPage = 'reservations';
$basePath         = '../../';

$adminError = get_flash('admin_error');
$adminSuccess = get_flash('admin_success');

try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT appointment_id, customer_name, customer_email, zone_name, table_number, appointment_date, start_time, party_size, status_name FROM vw_admin_appointments');
    $stmt->execute();
    $reservations = $stmt->fetchAll();
    $stmt->closeCursor();
} catch (PDOException $e) {
    $adminError = safe_error_message($e);
    $reservations = [];
}

$byStatus = ['pending'=>[],'confirmed'=>[],'cancelled'=>[],'completed'=>[]];
foreach ($reservations as $r) {
    $status = $r['status_name'];
    if (!isset($byStatus[$status])) {
        $byStatus[$status] = [];
    }
    $byStatus[$status][] = $r;
}

include '../../includes/header.php';
?>
<body>
<div class="admin-layout" id="adminLayout">

  <?php include '../../includes/admin-sidebar.php'; ?>

  <main class="admin-main">
    <div class="admin-content">

      <header class="admin-header">
        <div class="admin-header-row">
          <div>
            <h1 class="admin-page-title">Reservations</h1>
            <p class="admin-page-subtitle">Review, approve, and manage all guest reservations.</p>
          </div>
        </div>
      </header>

      <?php if ($adminError): ?>
        <div class="auth-alert"><span><?= e($adminError) ?></span></div>
      <?php elseif ($adminSuccess): ?>
        <div class="auth-alert" style="border-color: var(--clr-success, #2e7d32); color: var(--clr-success, #2e7d32);"><span><?= e($adminSuccess) ?></span></div>
      <?php endif; ?>

      <div class="admin-section">

        <div class="admin-filter-bar">
          <input type="text" class="admin-search-input" placeholder="Search guest, zone, or ID…" data-search-input="resTable" id="resSearch">
        </div>

        <div class="admin-tabs" data-admin-tabs>
          <?php foreach ($byStatus as $key=>$rows): ?>
          <button class="admin-tab <?= $key === 'pending' ? 'active' : '' ?>" data-tab="<?= e($key) ?>" aria-selected="<?= $key === 'pending' ? 'true' : 'false' ?>">
            <?= ucfirst($key) ?> <span class="admin-tab-count">(<?= count($rows) ?>)</span>
          </button>
          <?php endforeach; ?>
        </div>

        <?php foreach ($byStatus as $status => $rows): ?>
        <div class="admin-panel <?= $status === 'pending' ? 'active' : '' ?>" data-panel="<?= e($status) ?>">
          <div class="admin-table-wrap">
            <table class="admin-table resTable">
              <thead>
                <tr>
                  <th>#ID</th>
                  <th>Guest</th>
                  <th>Zone</th>
                  <th>Table</th>
                  <th>Date</th>
                  <th>Time</th>
                  <th>Guests</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($rows as $r):
                  $badgeClass = $status === 'confirmed' ? 'badge-confirmed' : ($status === 'pending' ? 'badge-pending' : ($status === 'completed' ? 'badge-completed' : 'badge-cancelled'));
                ?>
                <tr>
                  <td style="font-size:var(--text-xs);color:var(--clr-muted-fg)">#<?= e($r['appointment_id']) ?></td>
                  <td>
                    <span class="admin-guest-name"><?= e($r['customer_name']) ?></span>
                    <br><span class="admin-guest-email"><?= e($r['customer_email']) ?></span>
                  </td>
                  <td><?= e($r['zone_name'] ?? '—') ?></td>
                  <td><?= e($r['table_number'] ?? '—') ?></td>
                  <td><?= e(date('d M Y', strtotime($r['appointment_date']))) ?></td>
                  <td><?= e(date('g:i A', strtotime($r['start_time']))) ?></td>
                  <td><?= e((string) $r['party_size']) ?></td>
                  <td><span class="badge <?= $badgeClass ?>"><?= e(ucfirst($status)) ?></span></td>
                  <td class="admin-row-actions">
                    <?php if ($status === 'pending'): ?>
                      <form method="post" action="<?= $basePath ?>actions/admin_update_status.php" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="appointment_id" value="<?= e($r['appointment_id']) ?>">
                        <button class="btn btn-primary btn-sm" name="action" value="approve">Approve</button>
                      </form>
                      <form method="post" action="<?= $basePath ?>actions/admin_update_status.php" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="appointment_id" value="<?= e($r['appointment_id']) ?>">
                        <button class="btn btn-outline btn-sm" style="color:var(--clr-destructive);border-color:var(--clr-destructive);" name="action" value="reject">Reject</button>
                      </form>
                    <?php elseif ($status === 'confirmed'): ?>
                      <form method="post" action="<?= $basePath ?>actions/admin_update_status.php" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="appointment_id" value="<?= e($r['appointment_id']) ?>">
                        <button class="btn btn-outline btn-sm" name="action" value="complete">Complete</button>
                      </form>
                      <form method="post" action="<?= $basePath ?>actions/admin_update_status.php" style="display:inline;">
                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                        <input type="hidden" name="appointment_id" value="<?= e($r['appointment_id']) ?>">
                        <button class="btn btn-outline btn-sm" style="color:var(--clr-destructive);border-color:var(--clr-destructive);" name="action" value="cancel">Cancel</button>
                      </form>
                    <?php else: ?>
                      <span style="color:var(--clr-muted-fg);font-size:var(--text-xs);">No actions</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?>
                <tr><td colspan="9" style="text-align:center;color:var(--clr-muted-fg);padding:var(--space-8);">No reservations in this category.</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
        <?php endforeach; ?>

      </div>

    </div>
  </main>

</div>

<script src="<?= $basePath ?>js/admin.js"></script>
</body>
</html>


