<?php

require_once '../includes/security.php';
start_secure_session();

session_unset();
session_destroy();

redirect('../pages/login.php');
