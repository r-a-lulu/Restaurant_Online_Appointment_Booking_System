<?php
/**
 * Process Settings Save — actions/process_settings.php
 */
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
$actionToken = $_POST['action_token'] ?? '';

if (!verify_csrf($csrf) || !verify_action_token('save_settings', $actionToken)) {
    set_flash('settings_error', 'Security token invalid. Please try again.');
    header('Location: ../pages/admin/settings.php');
    exit();
}

$section = $_POST['section'] ?? '';

try {
    $pdo = db();
    
    switch ($section) {
        case 'general':
            $fields = [
                'restaurant_name',
                'restaurant_email',
                'restaurant_phone',
                'restaurant_address',
                'restaurant_description'
            ];
            break;
            
        case 'notifications':
            $fields = [
                'notify_new_reservation',
                'notify_cancellation',
                'notify_daily_summary'
            ];
            // Checkboxes - set to 0 if not present
            foreach ($fields as $field) {
                $_POST[$field] = isset($_POST[$field]) ? '1' : '0';
            }
            break;
            
        case 'maintenance':
            $fields = [
                'maintenance_mode',
                'maintenance_message'
            ];
            // Checkbox - set to 0 if not present
            $_POST['maintenance_mode'] = isset($_POST['maintenance_mode']) ? '1' : '0';
            break;
            
        default:
            set_flash('settings_error', 'Invalid settings section.');
            header('Location: ../pages/admin/settings.php');
            exit();
    }
    
    // Upsert each setting
    $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) 
                          VALUES (?, ?) 
                          ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");

    $textareaFields = ['restaurant_description', 'maintenance_message'];
    
    foreach ($fields as $field) {
        $value = $_POST[$field] ?? '';
        if (in_array($field, $textareaFields, true)) {
            $value = trim(str_replace(["\r\n", "\r"], "\n", (string) $value));
        } else {
            $value = clean_string((string) $value);
        }
        $stmt->execute([$field, $value]);
    }
    
    set_flash('settings_success', 'Settings saved successfully.');
    
} catch (PDOException $e) {
    set_flash('settings_error', 'Error saving settings: ' . safe_error_message($e));
}

header('Location: ../pages/admin/settings.php');
exit();
