<?php
/**
 * Stores Manager dashboard.
 */
auth_require_role(['stores_manager']);

$currentUser = $_SESSION['user'] ?? null;

$pageTitle = 'Stores Manager Dashboard';
$base = '';
require __DIR__ . '/../views/layouts/header.php';
require __DIR__ . '/../views/dashboards/stores_manager/home.php';
require __DIR__ . '/../views/layouts/footer.php';
