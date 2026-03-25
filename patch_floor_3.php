<?php
$floor_file = 'pages/admin/floor.php';
$content = file_get_contents($floor_file);

// Ensure current_status overrides status_rank
$pattern = "/(\\\$status = 'reserved';\s*\})/ius";
$replacement = "$1\n    if (!empty(\$t['current_status']) && \$t['current_status'] !== 'available') { \$status = \$t['current_status']; }";

if (strpos($content, "\$t['current_status'] !== 'available'") === false) {
    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, $replacement, $content);
        file_put_contents($floor_file, $content);
        echo "floor.php safely patched for current_status override.\n";
    } else {
        echo "floor.php pattern not found.\n";
    }
} else {
    echo "floor.php already has current_status override.\n";
}
