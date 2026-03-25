<?php
$f = 'pages/dashboard/book.php';
$c = file_get_contents($f);
$c = str_replace(' style="max-width: 72rem;"', '', $c);
file_put_contents($f, $c);
echo "book.php fixed.\n";
