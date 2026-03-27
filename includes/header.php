<?php
/**
 * Shared HTML head included at the top of every page.
 */

$pageTitle = $pageTitle ?? 'Welcome';
$pageCSS   = $pageCSS ?? [];
$navStyle  = $navStyle ?? 'transparent';
$basePath  = $basePath ?? './';

require_once __DIR__ . '/security.php';
start_secure_session();

$siteName = get_setting('restaurant_name', 'Eudaimonia');
$siteDescription = get_setting('restaurant_description', 'An exquisite dining experience where every detail is crafted to create moments of pure joy and connection.');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= htmlspecialchars($siteName . ' - ' . $siteDescription, ENT_QUOTES, 'UTF-8') ?>">
  <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?> | <?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8') ?></title>

  <link rel="apple-touch-icon" href="<?= $basePath ?>assets/images/apple-icon.png">
  <link rel="icon" href="<?= $basePath ?>assets/images/icon.svg" type="image/svg+xml">

  <link rel="stylesheet" href="<?= $basePath ?>css/variables.css">
  <link rel="stylesheet" href="<?= $basePath ?>css/reset.css">
  <link rel="stylesheet" href="<?= $basePath ?>css/typography.css">
  <link rel="stylesheet" href="<?= $basePath ?>css/base.css">
  <link rel="stylesheet" href="<?= $basePath ?>css/components.css">
  <link rel="stylesheet" href="<?= $basePath ?>css/nav.css">
  <link rel="stylesheet" href="<?= $basePath ?>css/footer.css">

  <?php foreach ($pageCSS as $cssFile): ?>
  <link rel="stylesheet" href="<?= $basePath ?>css/<?= htmlspecialchars($cssFile, ENT_QUOTES, 'UTF-8') ?>">
  <?php endforeach; ?>

  <script src="<?= $basePath ?>js/ui-confirm.js" defer></script>
</head>
<body>
