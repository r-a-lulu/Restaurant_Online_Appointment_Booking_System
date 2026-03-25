<?php
$floor_file = 'pages/admin/floor.php';
$content = file_get_contents($floor_file);

// Add table_id to $tablesByZone array
$pattern = "/('label'\s*=>\s*\\\$t\['table_number'\],)/ius";
$replacement = "$1\n      'table_id' => \$t['table_id'],";

if (preg_match($pattern, $content)) {
    $content = preg_replace($pattern, $replacement, $content);
    file_put_contents($floor_file, $content);
    echo "floor.php safely patched for table_id.";
} else {
    echo "floor.php pattern not found or already patched.";
}
