<?php
require_once '../../includes/security.php';
start_secure_session();
require_admin();

$pageTitle        = 'Master Data — Admin';
$pageCSS = ['dashboard.css', 'admin.css'];
$currentAdminPage = 'master-data';
$basePath         = '../../';

$adminError = get_flash('admin_error');
$adminSuccess = get_flash('admin_success');

try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT service_id, service_name, price FROM services ORDER BY service_name');
    $stmt->execute();
    $services = $stmt->fetchAll();
    $stmt->closeCursor();

    $stmt = $pdo->prepare('SELECT add_on_id, category, name, description, price FROM add_ons ORDER BY category, name');
    $stmt->execute();
    $addOns = $stmt->fetchAll();
    $stmt->closeCursor();

    $stmt = $pdo->prepare('SELECT zone_id, zone_name FROM dining_zones ORDER BY zone_name');
    $stmt->execute();
    $zones = $stmt->fetchAll();
    $stmt->closeCursor();

    $stmt = $pdo->prepare('SELECT table_id, zone_id, table_number, capacity, seating_preference FROM `tables` ORDER BY table_number');
    $stmt->execute();
    $tables = $stmt->fetchAll();
    $stmt->closeCursor();
} catch (PDOException $e) {
    $adminError = safe_error_message($e);
    $services = $addOns = $zones = $tables = [];
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
            <h1 class="admin-page-title">Master Data</h1>
            <p class="admin-page-subtitle">Manage services, add-ons, dining zones, and tables.</p>
          </div>
        </div>
      </header>

      <?php if ($adminError): ?>
        <div class="auth-alert"><span><?= e($adminError) ?></span></div>
      <?php elseif ($adminSuccess): ?>
        <div class="auth-alert" style="border-color: var(--clr-success, #2e7d32); color: var(--clr-success, #2e7d32);"><span><?= e($adminSuccess) ?></span></div>
      <?php endif; ?>

      <div class="admin-section">
        <h2 class="admin-section-title">Services</h2>
        <form method="post" action="<?= $basePath ?>actions.php?action=admin_master_data" class="admin-form" style="margin-bottom:var(--space-4);">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action_token" value="<?= e(action_token('admin_master_data')) ?>">
          <input type="hidden" name="type" value="service">
          <input type="hidden" name="action" value="create">
          <div class="admin-form-row">
            <input type="text" name="service_name" class="form-input" placeholder="Service name" required>
            <input type="number" name="price" class="form-input" placeholder="Price" step="0.01" min="0" required>
            <button type="submit" class="btn btn-primary">Add Service</button>
          </div>
        </form>
        <table class="admin-table">
          <thead><tr><th>Name</th><th>Price</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($services as $service): ?>
              <tr>
                <form method="post" action="<?= $basePath ?>actions.php?action=admin_master_data">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action_token" value="<?= e(action_token('admin_master_data')) ?>">
                  <input type="hidden" name="type" value="service">
                  <input type="hidden" name="service_id" value="<?= e($service['service_id']) ?>">
                  <td><input type="text" name="service_name" class="form-input" value="<?= e($service['service_name']) ?>"></td>
                  <td><input type="number" name="price" class="form-input" value="<?= e((string) $service['price']) ?>" step="0.01" min="0"></td>
                  <td>
                    <button type="submit" name="action" value="update" class="btn btn-outline btn-sm">Update</button>
                    <button type="submit" name="action" value="delete" class="btn btn-outline btn-sm" style="color:var(--clr-destructive);border-color:var(--clr-destructive);">Delete</button>
                  </td>
                </form>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="admin-section">
        <h2 class="admin-section-title">Add-ons</h2>
        <form method="post" action="<?= $basePath ?>actions.php?action=admin_master_data" class="admin-form" style="margin-bottom:var(--space-4);">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action_token" value="<?= e(action_token('admin_master_data')) ?>">
          <input type="hidden" name="type" value="add_on">
          <input type="hidden" name="action" value="create">
          <div class="admin-form-row">
            <input type="text" name="category" class="form-input" placeholder="Category" required>
            <input type="text" name="name" class="form-input" placeholder="Name" required>
            <input type="text" name="description" class="form-input" placeholder="Description" required>
            <input type="number" name="price" class="form-input" placeholder="Price" step="0.01" min="0" required>
            <button type="submit" class="btn btn-primary">Add Add-on</button>
          </div>
        </form>
        <table class="admin-table">
          <thead><tr><th>Category</th><th>Name</th><th>Description</th><th>Price</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($addOns as $addOn): ?>
              <tr>
                <form method="post" action="<?= $basePath ?>actions.php?action=admin_master_data">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action_token" value="<?= e(action_token('admin_master_data')) ?>">
                  <input type="hidden" name="type" value="add_on">
                  <input type="hidden" name="add_on_id" value="<?= e($addOn['add_on_id']) ?>">
                  <td><input type="text" name="category" class="form-input" value="<?= e($addOn['category']) ?>"></td>
                  <td><input type="text" name="name" class="form-input" value="<?= e($addOn['name']) ?>"></td>
                  <td><input type="text" name="description" class="form-input" value="<?= e($addOn['description']) ?>"></td>
                  <td><input type="number" name="price" class="form-input" value="<?= e((string) $addOn['price']) ?>" step="0.01" min="0"></td>
                  <td>
                    <button type="submit" name="action" value="update" class="btn btn-outline btn-sm">Update</button>
                    <button type="submit" name="action" value="delete" class="btn btn-outline btn-sm" style="color:var(--clr-destructive);border-color:var(--clr-destructive);">Delete</button>
                  </td>
                </form>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="admin-section">
        <h2 class="admin-section-title">Dining Zones</h2>
        <form method="post" action="<?= $basePath ?>actions.php?action=admin_master_data" class="admin-form" style="margin-bottom:var(--space-4);">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action_token" value="<?= e(action_token('admin_master_data')) ?>">
          <input type="hidden" name="type" value="zone">
          <input type="hidden" name="action" value="create">
          <div class="admin-form-row">
            <input type="text" name="zone_name" class="form-input" placeholder="Zone name" required>
            <button type="submit" class="btn btn-primary">Add Zone</button>
          </div>
        </form>
        <table class="admin-table">
          <thead><tr><th>Name</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($zones as $zone): ?>
              <tr>
                <form method="post" action="<?= $basePath ?>actions.php?action=admin_master_data">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action_token" value="<?= e(action_token('admin_master_data')) ?>">
                  <input type="hidden" name="type" value="zone">
                  <input type="hidden" name="zone_id" value="<?= e($zone['zone_id']) ?>">
                  <td><input type="text" name="zone_name" class="form-input" value="<?= e($zone['zone_name']) ?>"></td>
                  <td>
                    <button type="submit" name="action" value="update" class="btn btn-outline btn-sm">Update</button>
                    <button type="submit" name="action" value="delete" class="btn btn-outline btn-sm" style="color:var(--clr-destructive);border-color:var(--clr-destructive);">Delete</button>
                  </td>
                </form>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <div class="admin-section">
        <h2 class="admin-section-title">Tables</h2>
        <form method="post" action="<?= $basePath ?>actions.php?action=admin_master_data" class="admin-form" style="margin-bottom:var(--space-4);">
          <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action_token" value="<?= e(action_token('admin_master_data')) ?>">
          <input type="hidden" name="type" value="table">
          <input type="hidden" name="action" value="create">
          <div class="admin-form-row">
            <select name="zone_id" class="form-select" required>
              <option value="">Select zone</option>
              <?php foreach ($zones as $zone): ?>
                <option value="<?= e($zone['zone_id']) ?>"><?= e($zone['zone_name']) ?></option>
              <?php endforeach; ?>
            </select>
            <input type="text" name="table_number" class="form-input" placeholder="Table number" required>
            <input type="number" name="capacity" class="form-input" placeholder="Capacity" min="1" required>
            <input type="text" name="seating_preference" class="form-input" placeholder="Seating Pref (e.g. Window)">
            <button type="submit" class="btn btn-primary">Add Table</button>
          </div>
        </form>
        <table class="admin-table">
          <thead><tr><th>Zone</th><th>Table</th><th>Capacity</th><th>Seating Pref</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($tables as $table): ?>
              <tr>
                <form method="post" action="<?= $basePath ?>actions.php?action=admin_master_data">
                  <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
        <input type="hidden" name="action_token" value="<?= e(action_token('admin_master_data')) ?>">
                  <input type="hidden" name="type" value="table">
                  <input type="hidden" name="table_id" value="<?= e($table['table_id']) ?>">
                  <td>
                    <select name="zone_id" class="form-select">
                      <?php foreach ($zones as $zone): ?>
                        <option value="<?= e($zone['zone_id']) ?>" <?= ((int) $zone['zone_id'] === (int) $table['zone_id']) ? 'selected' : '' ?>><?= e($zone['zone_name']) ?></option>
                      <?php endforeach; ?>
                    </select>
                  </td>
                  <td><input type="text" name="table_number" class="form-input" value="<?= e($table['table_number']) ?>"></td>
                  <td><input type="number" name="capacity" class="form-input" value="<?= e((string) $table['capacity']) ?>" min="1"></td>
                  <td><input type="text" name="seating_preference" class="form-input" value="<?= e($table['seating_preference'] ?? '') ?>" placeholder="None"></td>
                  <td>
                    <button type="submit" name="action" value="update" class="btn btn-outline btn-sm">Update</button>
                    <button type="submit" name="action" value="delete" class="btn btn-outline btn-sm" style="color:var(--clr-destructive);border-color:var(--clr-destructive);">Delete</button>
                  </td>
                </form>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

    </div>
  </main>
</div>

<script src="<?= $basePath ?>js/admin.js"></script>
</body>
</html>








