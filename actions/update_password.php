<?php
if (!defined('FRONT_CONTROLLER')) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}
require_login();
if (!verify_csrf() || !verify_action_token('update_password')) {
    set_flash('dash_error', 'Invalid security token.');
    redirect('pages/dashboard/profile.php');
}

$current = $_POST['current_password'] ?? '';
$new = $_POST['new_password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

if (empty($current) || empty($new) || empty($confirm)) {
    set_flash('dash_error', 'All password fields are required.');
    redirect('pages/dashboard/profile.php');
}

if ($new !== $confirm) {
    set_flash('dash_error', 'New passwords do not match.');
    redirect('pages/dashboard/profile.php');
}

if (strlen($new) < 8) {
    set_flash('dash_error', 'New password must be at least 8 characters long.');
    redirect('pages/dashboard/profile.php');
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE user_id = ?');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($current, $user['password_hash'])) {
        set_flash('dash_error', 'Current password is incorrect.');
        redirect('pages/dashboard/profile.php');
    }

    $hash = password_hash($new, PASSWORD_DEFAULT);
    $update = $pdo->prepare('UPDATE users SET password_hash = ? WHERE user_id = ?');
    $update->execute([$hash, $_SESSION['user_id']]);

    set_flash('dash_success', 'Password updated successfully.');
    redirect('pages/dashboard/profile.php');

} catch (PDOException $e) {
    error_log('Password update failed for user ' . (int) $_SESSION['user_id'] . ': ' . $e->getMessage());
    set_flash('dash_error', 'We could not update your password right now. Please try again.');
    redirect('pages/dashboard/profile.php');
}
