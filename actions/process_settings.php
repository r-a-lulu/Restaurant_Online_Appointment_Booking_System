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
    header('Location: pages/admin/settings.php');
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
                'restaurant_description',
                'reservation_confirmation_note'
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

        case 'floor':
            $fields = [
                'floor_manual_occupied_minutes'
            ];
            $minutes = isset($_POST['floor_manual_occupied_minutes']) ? (int) $_POST['floor_manual_occupied_minutes'] : 120;
            if ($minutes < 5) {
                $minutes = 5;
            }
            if ($minutes > 720) {
                $minutes = 720;
            }
            $_POST['floor_manual_occupied_minutes'] = (string) $minutes;
            break;

        case 'booking':
            $fields = [
                'reservation_duration_minutes'
            ];
            $minutes = isset($_POST['reservation_duration_minutes']) ? (int) $_POST['reservation_duration_minutes'] : 90;
            if ($minutes < 30) {
                $minutes = 30;
            }
            if ($minutes > 240) {
                $minutes = 240;
            }
            $_POST['reservation_duration_minutes'] = (string) $minutes;
            break;
            
        default:
            set_flash('settings_error', 'Invalid settings section.');
            header('Location: pages/admin/settings.php');
            exit();
    }
    
    // Upsert each setting
    $stmt = $pdo->prepare("INSERT INTO system_settings (setting_key, setting_value) 
                          VALUES (?, ?) 
                          ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");

    $textareaFields = ['restaurant_description', 'maintenance_message', 'reservation_confirmation_note'];
    
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
    set_flash('settings_error', 'We could not save the settings right now. Please try again.');
}

header('Location: pages/admin/settings.php');
exit();
