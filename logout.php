<?php
/**
 * Logout: clear session and redirect to index (login page).
 */
require_once __DIR__ . '/src/config/config.php';
require_once __DIR__ . '/src/auth/auth.php';

auth_logout();
header('Location: index.php');
exit;
