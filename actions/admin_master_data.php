<?php
if (!defined('FRONT_CONTROLLER')) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}
require_once __DIR__ . '/../includes/security.php';
start_secure_session();
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('HTTP/1.1 405 Method Not Allowed');
    exit();
}

$csrf = $_POST['csrf_token'] ?? '';
if (!verify_csrf($csrf)) {
    set_flash('admin_error', 'Invalid security token.');
    redirect('../pages/admin/master-data.php');
}

$type = clean_string($_POST['type'] ?? '');
$action = clean_string($_POST['action'] ?? '');

try {
    $pdo = db();

    if ($type === 'service') {
        if ($action === 'create') {
            $name = clean_string($_POST['service_name'] ?? '');
            $price = (float) ($_POST['price'] ?? 0);
            $stmt = $pdo->prepare('CALL sp_services_create(:name, :price)');
            $stmt->execute([':name' => $name, ':price' => $price]);
            $stmt->closeCursor();
        } elseif ($action === 'update') {
            $id = (int) ($_POST['service_id'] ?? 0);
            $name = clean_string($_POST['service_name'] ?? '');
            $price = (float) ($_POST['price'] ?? 0);
            $stmt = $pdo->prepare('CALL sp_services_update(:id, :name, :price)');
            $stmt->execute([':id' => $id, ':name' => $name, ':price' => $price]);
            $stmt->closeCursor();
        } elseif ($action === 'delete') {
            $id = (int) ($_POST['service_id'] ?? 0);
            $stmt = $pdo->prepare('CALL sp_services_delete(:id)');
            $stmt->execute([':id' => $id]);
            $stmt->closeCursor();
        }
    }

    if ($type === 'add_on') {
        if ($action === 'create') {
            $category = clean_string($_POST['category'] ?? '');
            $name = clean_string($_POST['name'] ?? '');
            $description = clean_string($_POST['description'] ?? '');
            $price = (float) ($_POST['price'] ?? 0);
            $stmt = $pdo->prepare('CALL sp_add_ons_create(:category, :name, :description, :price)');
            $stmt->execute([
                ':category' => $category,
                ':name' => $name,
                ':description' => $description,
                ':price' => $price,
            ]);
            $stmt->closeCursor();
        } elseif ($action === 'update') {
            $id = (int) ($_POST['add_on_id'] ?? 0);
            $category = clean_string($_POST['category'] ?? '');
            $name = clean_string($_POST['name'] ?? '');
            $description = clean_string($_POST['description'] ?? '');
            $price = (float) ($_POST['price'] ?? 0);
            $stmt = $pdo->prepare('CALL sp_add_ons_update(:id, :category, :name, :description, :price)');
            $stmt->execute([
                ':id' => $id,
                ':category' => $category,
                ':name' => $name,
                ':description' => $description,
                ':price' => $price,
            ]);
            $stmt->closeCursor();
        } elseif ($action === 'delete') {
            $id = (int) ($_POST['add_on_id'] ?? 0);
            $stmt = $pdo->prepare('CALL sp_add_ons_delete(:id)');
            $stmt->execute([':id' => $id]);
            $stmt->closeCursor();
        }
    }

    if ($type === 'zone') {
        if ($action === 'create') {
            $name = clean_string($_POST['zone_name'] ?? '');
            $stmt = $pdo->prepare('CALL sp_dining_zones_create(:name)');
            $stmt->execute([':name' => $name]);
            $stmt->closeCursor();
        } elseif ($action === 'update') {
            $id = (int) ($_POST['zone_id'] ?? 0);
            $name = clean_string($_POST['zone_name'] ?? '');
            $stmt = $pdo->prepare('CALL sp_dining_zones_update(:id, :name)');
            $stmt->execute([':id' => $id, ':name' => $name]);
            $stmt->closeCursor();
        } elseif ($action === 'delete') {
            $id = (int) ($_POST['zone_id'] ?? 0);
            $stmt = $pdo->prepare('CALL sp_dining_zones_delete(:id)');
            $stmt->execute([':id' => $id]);
            $stmt->closeCursor();
        }
    }

    if ($type === 'table') {
        if ($action === 'create') {
            $zoneId = (int) ($_POST['zone_id'] ?? 0);
            $tableNumber = clean_string($_POST['table_number'] ?? '');
            $capacity = (int) ($_POST['capacity'] ?? 0);
            $stmt = $pdo->prepare('CALL sp_tables_create(:zone_id, :table_number, :capacity)');
            $stmt->execute([':zone_id' => $zoneId, ':table_number' => $tableNumber, ':capacity' => $capacity]);
            $stmt->closeCursor();
        } elseif ($action === 'update') {
            $id = (int) ($_POST['table_id'] ?? 0);
            $zoneId = (int) ($_POST['zone_id'] ?? 0);
            $tableNumber = clean_string($_POST['table_number'] ?? '');
            $capacity = (int) ($_POST['capacity'] ?? 0);
            $stmt = $pdo->prepare('CALL sp_tables_update(:id, :zone_id, :table_number, :capacity)');
            $stmt->execute([':id' => $id, ':zone_id' => $zoneId, ':table_number' => $tableNumber, ':capacity' => $capacity]);
            $stmt->closeCursor();
        } elseif ($action === 'delete') {
            $id = (int) ($_POST['table_id'] ?? 0);
            $stmt = $pdo->prepare('CALL sp_tables_delete(:id)');
            $stmt->execute([':id' => $id]);
            $stmt->closeCursor();
        }
    }

    set_flash('admin_success', 'Changes saved successfully.');
    redirect('../pages/admin/master-data.php');
} catch (PDOException $e) {
    set_flash('admin_error', safe_error_message($e));
    redirect('../pages/admin/master-data.php');
}

