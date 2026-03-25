<?php
$f = 'pages/admin/floor.php';
$c = file_get_contents($f);

// Fix array
$s = "'label' => \$t['table_number'],";
$r = "'label' => \$t['table_number'],\n      'table_id' => \$t['table_id'],";
$c = str_replace($s, $r, $c);

// Fix status override
$s2 = "\$status = 'reserved';\n    }\n";
$r2 = "\$status = 'reserved';\n    }\n    if (!empty(\$t['current_status']) && \$t['current_status'] !== 'available') { \$status = \$t['current_status']; }\n";
// Sometimes Windows has \r\n
$s3 = "\$status = 'reserved';\r\n    }\r\n";
$r3 = "\$status = 'reserved';\r\n    }\r\n    if (!empty(\$t['current_status']) && \$t['current_status'] !== 'available') { \$status = \$t['current_status']; }\r\n";

$c = str_replace($s2, $r2, $c);
$c = str_replace($s3, $r3, $c);

file_put_contents($f, $c);
echo "Patched.";
