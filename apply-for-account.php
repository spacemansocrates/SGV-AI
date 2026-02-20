<?php
/**
 * Apply for account entry: submit application for admin approval.
 */
require_once __DIR__ . '/src/config/config.php';
require_once __DIR__ . '/src/auth/auth.php';

if (auth_check()) {
    header('Location: index.php');
    exit;
}

require __DIR__ . '/src/auth/apply_for_account.php';
