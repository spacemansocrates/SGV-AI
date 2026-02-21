<?php
/**
 * Accountant dashboard.
 */
auth_require_role(['accountant']);

$currentUser = $_SESSION['user'] ?? null;

$pageTitle = 'Accountant Dashboard';
$base = '';
require __DIR__ . '/../views/layouts/header.php';
require __DIR__ . '/../views/dashboards/accountant/home.php';
require __DIR__ . '/../views/layouts/footer.php';
