<?php

/**
 * Admin Reservations Manager — pages/admin/reservations.php
 */

require_once '../../includes/security.php';
start_secure_session();
require_admin();

$pageTitle        = 'Reservations — Admin';
$pageCSS = ['dashboard.css', 'admin.css'];
$currentAdminPage = 'reservations';
$basePath         = '../../';

$adminError = get_flash('admin_error');
$adminSuccess = get_flash('admin_success');

try {
  $pdo = db();
  $stmt = $pdo->prepare("SELECT v.appointment_id, v.customer_name, v.customer_email, v.zone_name, v.table_label, v.appointment_date, v.start_time, v.party_size, v.status_name, a.user_id, (SELECT COUNT(*) FROM appointments a2 WHERE a2.user_id = a.user_id AND a2.appointment_id <> v.appointment_id AND a2.status_id IN (SELECT status_id FROM appointment_status WHERE status_name IN ('pending','confirmed'))) AS active_count FROM vw_admin_appointments v JOIN appointments a ON a.appointment_id = v.appointment_id");
  $stmt->execute();
  $reservations = $stmt->fetchAll();
  $stmt->closeCursor();
} catch (PDOException $e) {
  $adminError = safe_error_message($e);
  $reservations = [];
}

$byStatus = ['pending' => [], 'confirmed' => [], 'cancelled' => [], 'completed' => []];
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
            <input type="text" class="admin-search-input" placeholder="Search guest, zone, or ID" data-search-input="resTable" id="resSearch">
          </div>

          <div class="admin-tabs" data-admin-tabs>
            <?php foreach ($byStatus as $key => $rows): ?>
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
                      <th>Seating Preference</th>
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
                      $activeCount = (int) ($r['active_count'] ?? 0);
                      $canApprove = ($status === 'pending') && ($activeCount < 5);
                    ?>
                      <tr>
                        <td style="font-size:var(--text-xs);color:var(--clr-muted-fg)">#<?= e($r['appointment_id']) ?></td>
                        <td>
                          <span class="admin-guest-name"><?= e($r['customer_name']) ?></span>
                          <br><span class="admin-guest-email"><?= e($r['customer_email']) ?></span>
                        </td>
                        <td><?= e($r['zone_name'] ?? '�') ?></td>
                        <td><?= e($r['table_label'] ?? '—') ?></td>
                        <td><?= e(date('d M Y', strtotime($r['appointment_date']))) ?></td>
                        <td><?= e(date('g:i A', strtotime($r['start_time']))) ?></td>
                        <td><?= e((string) $r['party_size']) ?></td>
                        <td><span class="badge <?= $badgeClass ?>"><?= e(ucfirst($status)) ?></span></td>
                        <td class="admin-row-actions">
                          <div class="admin-row-actions-inner">
                            <?php if ($status === 'pending'): ?>
                              <form method="post" action="<?= $basePath ?>actions.php?action=admin_update_status" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="action_token" value="<?= e(action_token('admin_update_status')) ?>">
                                <input type="hidden" name="appointment_id" value="<?= e($r['appointment_id']) ?>">
                                <button class="btn btn-primary btn-sm" name="action" value="approve" <?php echo $canApprove ? '' : 'disabled title="User has reached the 5 active bookings limit."'; ?>>Approve</button>
                              </form>
                              <form method="post" action="<?= $basePath ?>actions.php?action=admin_update_status" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="action_token" value="<?= e(action_token('admin_update_status')) ?>">
                                <input type="hidden" name="appointment_id" value="<?= e($r['appointment_id']) ?>">
                                <button class="btn btn-outline btn-sm" style="color:var(--clr-destructive);border-color:var(--clr-destructive);" name="action" value="reject">Reject</button>
                              </form>
                              <?php if ($status === 'pending' && !$canApprove): ?>
                                <span style="display:inline-block;margin-top:6px;font-size:var(--text-xs);color:var(--clr-muted-fg);">Limit reached</span>
                              <?php endif; ?>
                            <?php elseif ($status === 'confirmed'): ?>
                              <form method="post" action="<?= $basePath ?>actions.php?action=admin_update_status" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="action_token" value="<?= e(action_token('admin_update_status')) ?>">
                                <input type="hidden" name="appointment_id" value="<?= e($r['appointment_id']) ?>">
                                <button class="btn btn-outline btn-sm" name="action" value="complete">Complete</button>
                              </form>
                              <form method="post" action="<?= $basePath ?>actions.php?action=admin_update_status" style="display:inline;">
                                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                <input type="hidden" name="action_token" value="<?= e(action_token('admin_update_status')) ?>">
                                <input type="hidden" name="appointment_id" value="<?= e($r['appointment_id']) ?>">
                                <button class="btn btn-outline btn-sm" style="color:var(--clr-destructive);border-color:var(--clr-destructive);" name="action" value="cancel">Cancel</button>
                              </form>
                            <?php else: ?>
                              <span style="color:var(--clr-muted-fg);font-size:var(--text-xs);">No actions</span>
                            <?php endif; ?>
                          </div>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                    <?php if (empty($rows)): ?>
                      <tr>
                        <td colspan="9" style="text-align:center;color:var(--clr-muted-fg);padding:var(--space-8);">No reservations in this category.</td>
                      </tr>
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