<?php
/**
 * Shared HTML <head> — included at the top of every page.
 * 
 * Required variables (set before including):
 *   $pageTitle  — string, page title suffix (e.g., "About")
 *   $pageCSS    — array, extra CSS filenames to load (e.g., ['home.css'])
 * 
 * Optional:
 *   $navStyle   — string, 'transparent' (default, for hero pages) or 'solid' (for inner pages)
 */

$pageTitle = $pageTitle ?? 'Welcome';
$pageCSS   = $pageCSS ?? [];
$navStyle  = $navStyle ?? 'transparent';

/* Resolve base path relative to current file */
$basePath = '';
// Count the number of slashes in the SCRIPT_NAME, minus 1 for the leading slash
$depth = substr_count(str_replace('\\', '/', $_SERVER['SCRIPT_NAME']), '/') - 1;
if ($depth > 0) {
    $basePath = str_repeat('../', $depth);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Eudaimonia — An exquisite dining experience. Reserve your table today.">
  <title><?= htmlspecialchars($pageTitle) ?> | Eudaimonia</title>

  <!-- Favicon -->
  <link rel="icon" href="<?= $basePath ?>assets/images/icon.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="<?= $basePath ?>assets/images/apple-icon.png">

  <!-- Core CSS (order matters) -->
  <link rel="stylesheet" href="<?= $basePath ?>css/variables.css">
  <link rel="stylesheet" href="<?= $basePath ?>css/reset.css">
  <link rel="stylesheet" href="<?= $basePath ?>css/typography.css">
  <link rel="stylesheet" href="<?= $basePath ?>css/base.css">
  <link rel="stylesheet" href="<?= $basePath ?>css/components.css">
  <link rel="stylesheet" href="<?= $basePath ?>css/nav.css">
  <link rel="stylesheet" href="<?= $basePath ?>css/footer.css">

  <!-- Page-specific CSS -->
  <?php foreach ($pageCSS as $cssFile): ?>
  <link rel="stylesheet" href="<?= $basePath ?>css/<?= htmlspecialchars($cssFile) ?>">
  <?php endforeach; ?>
</head>
<body>
