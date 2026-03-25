<?php
require_once __DIR__ . '/../includes/security.php';
try {
    $pdo = db();
    $stmt = $pdo->query("SHOW PROCEDURE STATUS WHERE Db = 'restaurant_booking_v1' AND Name = 'sp_get_available_slots'");
    $proc = $stmt->fetch();
    if ($proc) {
        echo "Stored procedure sp_get_available_slots exists.\n";
    } else {
        echo "Stored procedure sp_get_available_slots NOT found.\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
