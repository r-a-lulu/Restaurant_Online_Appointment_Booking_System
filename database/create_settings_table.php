<?php
/**
 * Create system_settings table migration
 */
require_once __DIR__ . '/../includes/security.php';

try {
    $pdo = db();
    
    $pdo->exec("CREATE TABLE IF NOT EXISTS system_settings (
        setting_id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(100) NOT NULL UNIQUE,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    
    // Insert default values
    $defaults = [
        ['restaurant_name', 'Eudaimonia'],
        ['restaurant_email', 'hello@eudaimonia.com'],
        ['restaurant_phone', '+1 (555) 000-1234'],
        ['restaurant_address', '12 Harmony Lane, New York, NY'],
        ['restaurant_description', 'A contemporary dining experience rooted in timeless hospitality.'],
        ['notify_new_reservation', '1'],
        ['notify_cancellation', '1'],
        ['notify_daily_summary', '0'],
        ['maintenance_mode', '0'],
        ['maintenance_message', "We're temporarily offline for maintenance. Please check back shortly."]
    ];
    
    $stmt = $pdo->prepare("INSERT IGNORE INTO system_settings (setting_key, setting_value) VALUES (?, ?)");
    foreach ($defaults as $default) {
        $stmt->execute($default);
    }
    
    echo "system_settings table created with defaults.\n";
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
