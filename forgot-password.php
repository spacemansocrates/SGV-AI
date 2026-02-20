<?php
/**
 * Forgot password entry: show form and success message.
 */
require_once __DIR__ . '/src/config/config.php';
require_once __DIR__ . '/src/auth/auth.php';

if (auth_check()) {
    header('Location: index.php');
    exit;
}

require __DIR__ . '/src/auth/forgot_password.php';
