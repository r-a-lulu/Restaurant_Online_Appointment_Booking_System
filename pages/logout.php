<?php
/**
 * Log Out
 */
require_once '../includes/security.php';
start_secure_session();

// Destroy session data
session_unset();
session_destroy();

// Redirect to login page
redirect('login.php');
