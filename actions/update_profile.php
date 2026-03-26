<?php
if (!defined('FRONT_CONTROLLER')) {
    header('HTTP/1.1 403 Forbidden');
    exit;
}
require_login();
if (!verify_csrf() || !verify_action_token('update_profile')) {
    set_flash('dash_error', 'Invalid security token.');
    redirect('pages/dashboard/profile.php');
}

$firstName = trim($_POST['first_name'] ?? '');
$lastName = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if (empty($firstName) || empty($lastName) || empty($email)) {
    set_flash('dash_error', 'First name, last name, and email are required.');
    redirect('pages/dashboard/profile.php');
}

try {
    $pdo = db();
    
    // Check if email already exists for another user
    $check = $pdo->prepare('SELECT user_id FROM users WHERE email = ? AND user_id != ?');
    $check->execute([$email, $_SESSION['user_id']]);
    if ($check->fetch()) {
        set_flash('dash_error', 'Email address is already in use by another account.');
        redirect('pages/dashboard/profile.php');
    }

    $stmt = $pdo->prepare('UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE user_id = ?');
    $stmt->execute([$firstName, $lastName, $email, $phone, $_SESSION['user_id']]);

    // Update Session
    $_SESSION['first_name'] = $firstName;
    $_SESSION['last_name'] = $lastName;
    $_SESSION['email'] = $email;
    $_SESSION['phone'] = $phone;

    set_flash('dash_success', 'Profile updated successfully.');
    redirect('pages/dashboard/profile.php');

} catch (PDOException $e) {
    error_log('Profile update failed for user ' . (int) $_SESSION['user_id'] . ': ' . $e->getMessage());
    set_flash('dash_error', 'We could not update your profile right now. Please try again.');
    redirect('pages/dashboard/profile.php');
}
