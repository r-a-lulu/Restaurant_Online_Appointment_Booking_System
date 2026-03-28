<?php
require_once '../../includes/security.php';
start_secure_session();
require_admin();

$pageTitle        = 'Master Data — Admin';
$pageCSS = ['dashboard.css', 'admin.css'];
$currentAdminPage = 'master-data';
$basePath         = '../../';

$adminError   = get_flash('admin_error');
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

    $stmt = $pdo->prepare('SELECT table_id, zone_id, capacity, seating_preference FROM `tables` ORDER BY seating_preference');
    $stmt->execute();
    $tables = $stmt->fetchAll();
    $stmt->closeCursor();
} catch (PDOException $e) {
    $adminError = safe_error_message($e);
    $services = $addOns = $zones = $tables = [];
}

// Determine which tab to restore after a form submission
$activeTab = 'services';
if ($adminError || $adminSuccess) {
    // peek at the flash origin so we can restore the right tab
    $tabHint = $_SESSION['master_data_tab'] ?? 'services';
    $activeTab = $tabHint;
}
// Store the current tab for next request (form sets a hidden field)
$postedTab = $_POST['active_tab'] ?? null;
if ($postedTab) {
    $_SESSION['master_data_tab'] = $postedTab;
    $activeTab = $postedTab;
} else {
    $_SESSION['master_data_tab'] = $activeTab;
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
        <div class="auth-alert auth-success"><span><?= e($adminSuccess) ?></span></div>
      <?php endif; ?>

      <!-- Tab Nav -->
      <div class="md-tabs" id="mdTabs" role="tablist">
        <button class="md-tab-btn <?= $activeTab === 'services'  ? 'md-tab-btn--active' : '' ?>" role="tab" aria-controls="mdpanel-services"  aria-selected="<?= $activeTab === 'services'  ? 'true' : 'false' ?>" data-tab="services"  id="mdtab-services">Services</button>
        <button class="md-tab-btn <?= $activeTab === 'addons'    ? 'md-tab-btn--active' : '' ?>" role="tab" aria-controls="mdpanel-addons"    aria-selected="<?= $activeTab === 'addons'    ? 'true' : 'false' ?>" data-tab="addons"    id="mdtab-addons">Add-ons</button>
        <button class="md-tab-btn <?= $activeTab === 'zones'     ? 'md-tab-btn--active' : '' ?>" role="tab" aria-controls="mdpanel-zones"     aria-selected="<?= $activeTab === 'zones'     ? 'true' : 'false' ?>" data-tab="zones"     id="mdtab-zones">Dining Zones</button>
        <button class="md-tab-btn <?= $activeTab === 'tables'    ? 'md-tab-btn--active' : '' ?>" role="tab" aria-controls="mdpanel-tables"    aria-selected="<?= $activeTab === 'tables'    ? 'true' : 'false' ?>" data-tab="tables"    id="mdtab-tables">Tables</button>
      </div>

      <!-- ═══════════ SERVICES ═══════════ -->
      <div class="md-tab-panel <?= $activeTab === 'services' ? 'md-tab-panel--active' : '' ?>" id="mdpanel-services" role="tabpanel" aria-labelledby="mdtab-services">
        <div class="admin-section">
          <div class="admin-section-header">
            <h2 class="admin-section-title">Services</h2>
          </div>
          <div class="admin-section-body">
            <form method="post" action="<?= $basePath ?>actions.php?action=admin_master_data" class="admin-form">
              <input type="hidden" name="csrf_token"   value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="action_token" value="<?= e(action_token('admin_master_data')) ?>">
              <input type="hidden" name="type"         value="service">
              <input type="hidden" name="action"       value="create">
              <input type="hidden" name="active_tab"   value="services">
              <div class="admin-form-row">
                <input type="text"   name="service_name" class="form-input" placeholder="Service Name (e.g. Dinner)" required>
                <input type="number" name="price"        class="form-input" placeholder="Price" step="0.01" min="0" required>
                <button type="submit" class="btn btn-primary">Add Service</button>
              </div>
            </form>
          </div>
          <div class="admin-table-wrap">
            <table class="admin-table">
              <thead><tr><th>Name</th><th>Price</th><th>Actions</th></tr></thead>
              <tbody>
                <?php foreach ($services as $service): ?>
                  <tr>
                    <form method="post" action="<?= $basePath ?>actions.php?action=admin_master_data">
                      <input type="hidden" name="csrf_token"   value="<?= e(csrf_token()) ?>">
                      <input type="hidden" name="action_token" value="<?= e(action_token('admin_master_data')) ?>">
                      <input type="hidden" name="type"         value="service">
                      <input type="hidden" name="service_id"   value="<?= e($service['service_id']) ?>">
                      <input type="hidden" name="active_tab"   value="services">
                      <td><input type="text"   name="service_name" class="form-input" value="<?= e($service['service_name']) ?>"></td>
                      <td><input type="number" name="price"        class="form-input" value="<?= e((string) $service['price']) ?>" step="0.01" min="0"></td>
                      <td>
                        <button type="submit" name="action" value="update" class="btn btn-outline btn-sm">Update</button>
                        <button type="submit" name="action" value="delete" class="btn btn-outline btn-sm btn-outline--destructive" data-confirm="Are you sure you want to delete this service?">Delete</button>
                      </td>
                    </form>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ═══════════ ADD-ONS ═══════════ -->
      <div class="md-tab-panel <?= $activeTab === 'addons' ? 'md-tab-panel--active' : '' ?>" id="mdpanel-addons" role="tabpanel" aria-labelledby="mdtab-addons">
        <div class="admin-section">
          <div class="admin-section-header">
            <h2 class="admin-section-title">Add-ons</h2>
          </div>
          <div class="admin-section-body">
            <form method="post" action="<?= $basePath ?>actions.php?action=admin_master_data" class="admin-form">
              <input type="hidden" name="csrf_token"   value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="action_token" value="<?= e(action_token('admin_master_data')) ?>">
              <input type="hidden" name="type"         value="add_on">
              <input type="hidden" name="action"       value="create">
              <input type="hidden" name="active_tab"   value="addons">
              <div class="admin-form-row">
                <input type="text"   name="category"    class="form-input" placeholder="Category" required>
                <input type="text"   name="name"        class="form-input" placeholder="Add-on Name" required>
                <input type="text"   name="description" class="form-input" placeholder="Description" required>
                <input type="number" name="price"       class="form-input" placeholder="Price" step="0.01" min="0" required>
                <button type="submit" class="btn btn-primary">Add Add-on</button>
              </div>
            </form>
          </div>
          <div class="admin-table-wrap">
            <table class="admin-table">
              <thead><tr><th>Category</th><th>Name</th><th>Description</th><th>Price</th><th>Actions</th></tr></thead>
              <tbody>
                <?php foreach ($addOns as $addOn): ?>
                  <tr>
                    <form method="post" action="<?= $basePath ?>actions.php?action=admin_master_data">
                      <input type="hidden" name="csrf_token"   value="<?= e(csrf_token()) ?>">
                      <input type="hidden" name="action_token" value="<?= e(action_token('admin_master_data')) ?>">
                      <input type="hidden" name="type"         value="add_on">
                      <input type="hidden" name="add_on_id"    value="<?= e($addOn['add_on_id']) ?>">
                      <input type="hidden" name="active_tab"   value="addons">
                      <td><input type="text"   name="category"    class="form-input" value="<?= e($addOn['category']) ?>"></td>
                      <td><input type="text"   name="name"        class="form-input" value="<?= e($addOn['name']) ?>"></td>
                      <td><input type="text"   name="description" class="form-input" value="<?= e($addOn['description']) ?>"></td>
                      <td><input type="number" name="price"       class="form-input" value="<?= e((string) $addOn['price']) ?>" step="0.01" min="0"></td>
                      <td>
                        <button type="submit" name="action" value="update" class="btn btn-outline btn-sm">Update</button>
                        <button type="submit" name="action" value="delete" class="btn btn-outline btn-sm btn-outline--destructive" data-confirm="Are you sure you want to delete this add-on?">Delete</button>
                      </td>
                    </form>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ═══════════ DINING ZONES ═══════════ -->
      <div class="md-tab-panel <?= $activeTab === 'zones' ? 'md-tab-panel--active' : '' ?>" id="mdpanel-zones" role="tabpanel" aria-labelledby="mdtab-zones">
        <div class="admin-section">
          <div class="admin-section-header">
            <h2 class="admin-section-title">Dining Zones</h2>
          </div>
          <div class="admin-section-body">
            <form method="post" action="<?= $basePath ?>actions.php?action=admin_master_data" class="admin-form">
              <input type="hidden" name="csrf_token"   value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="action_token" value="<?= e(action_token('admin_master_data')) ?>">
              <input type="hidden" name="type"         value="zone">
              <input type="hidden" name="action"       value="create">
              <input type="hidden" name="active_tab"   value="zones">
              <div class="admin-form-row">
                <input type="text" name="zone_name" class="form-input" placeholder="Zone Name (e.g. Garden)" required>
                <button type="submit" class="btn btn-primary">Add Zone</button>
              </div>
            </form>
          </div>
          <div class="admin-table-wrap">
            <table class="admin-table">
              <thead><tr><th>Name</th><th>Actions</th></tr></thead>
              <tbody>
                <?php foreach ($zones as $zone): ?>
                  <tr>
                    <form method="post" action="<?= $basePath ?>actions.php?action=admin_master_data">
                      <input type="hidden" name="csrf_token"   value="<?= e(csrf_token()) ?>">
                      <input type="hidden" name="action_token" value="<?= e(action_token('admin_master_data')) ?>">
                      <input type="hidden" name="type"         value="zone">
                      <input type="hidden" name="zone_id"      value="<?= e($zone['zone_id']) ?>">
                      <input type="hidden" name="active_tab"   value="zones">
                      <td><input type="text" name="zone_name" class="form-input" value="<?= e($zone['zone_name']) ?>"></td>
                      <td>
                        <button type="submit" name="action" value="update" class="btn btn-outline btn-sm">Update</button>
                        <button type="submit" name="action" value="delete" class="btn btn-outline btn-sm btn-outline--destructive" data-confirm="Are you sure you want to delete this dining zone?">Delete</button>
                      </td>
                    </form>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- ═══════════ TABLES ═══════════ -->
      <div class="md-tab-panel <?= $activeTab === 'tables' ? 'md-tab-panel--active' : '' ?>" id="mdpanel-tables" role="tabpanel" aria-labelledby="mdtab-tables">
        <div class="admin-section">
          <div class="admin-section-header">
            <h2 class="admin-section-title">Tables</h2>
          </div>
          <div class="admin-section-body">
            <form method="post" action="<?= $basePath ?>actions.php?action=admin_master_data" class="admin-form">
              <input type="hidden" name="csrf_token"   value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="action_token" value="<?= e(action_token('admin_master_data')) ?>">
              <input type="hidden" name="type"         value="table">
              <input type="hidden" name="action"       value="create">
              <input type="hidden" name="active_tab"   value="tables">
              <div class="admin-form-row">
                <select name="zone_id" class="form-select" required>
                  <option value="">Select Zone</option>
                  <?php foreach ($zones as $zone): ?>
                    <option value="<?= e($zone['zone_id']) ?>"><?= e($zone['zone_name']) ?></option>
                  <?php endforeach; ?>
                </select>
                <input type="number" name="capacity"           class="form-input" placeholder="Cap" min="1" required>
                <input type="text"   name="seating_preference" class="form-input" placeholder="Seating Preference (Window, etc.)" required>
                <button type="submit" class="btn btn-primary">Add Table</button>
              </div>
            </form>
          </div>
          <div class="admin-table-wrap">
            <!-- Zone filter -->
            <div class="md-zone-filter">
              <label class="md-zone-filter-label" for="tableZoneFilter">Filter by zone:</label>
              <select id="tableZoneFilter" class="form-select md-zone-select">
                <option value="">All Zones</option>
                <?php foreach ($zones as $zone): ?>
                  <option value="<?= e($zone['zone_id']) ?>"><?= e($zone['zone_name']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <table class="admin-table">
              <thead><tr><th>Zone</th><th>Seating Preference</th><th>Cap</th><th>Actions</th></tr></thead>
              <tbody id="tablesTbody">
                <?php if (empty($tables)): ?>
                  <tr id="tablesEmptyMsg"><td colspan="4" class="md-empty-cell">No tables yet. Use the form above to add one.</td></tr>
                <?php else: ?>
                <?php foreach ($tables as $table): ?>
                  <tr>
                    <form method="post" action="<?= $basePath ?>actions.php?action=admin_master_data">
                      <input type="hidden" name="csrf_token"   value="<?= e(csrf_token()) ?>">
                      <input type="hidden" name="action_token" value="<?= e(action_token('admin_master_data')) ?>">
                      <input type="hidden" name="type"         value="table">
                      <input type="hidden" name="table_id"     value="<?= e($table['table_id']) ?>">
                      <input type="hidden" name="active_tab"   value="tables">
                      <td>
                        <select name="zone_id" class="form-select">
                          <?php foreach ($zones as $zone): ?>
                            <option value="<?= e($zone['zone_id']) ?>" <?= ((int) $zone['zone_id'] === (int) $table['zone_id']) ? 'selected' : '' ?>><?= e($zone['zone_name']) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </td>
                      <td><input type="text"   name="seating_preference" class="form-input" value="<?= e($table['seating_preference'] ?? '') ?>" placeholder="Window, Bar, Outdoor" required></td>
                      <td><input type="number" name="capacity"           class="form-input" value="<?= e((string) $table['capacity']) ?>" min="1"></td>
                      <td>
                        <button type="submit" name="action" value="update" class="btn btn-outline btn-sm">Update</button>
                        <button type="submit" name="action" value="delete" class="btn btn-outline btn-sm btn-outline--destructive" data-confirm="Are you sure you want to delete this table?">Delete</button>
                      </td>
                    </form>
                  </tr>
                <?php endforeach; ?>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div><!-- /.admin-content -->
  </main>
</div><!-- /.admin-layout -->

<script src="<?= $basePath ?>js/admin.js"></script>
<script src="<?= $basePath ?>js/master-data-tabs.js"></script>
</body>
</html>
