<?php
/**
 * Operations Manager dashboard.
 */
auth_require_role(['ops_manager']);

$currentUser = $_SESSION['user'] ?? null;

$pageTitle = 'Operations Manager Dashboard';
$base = '';
require __DIR__ . '/../views/layouts/header.php';
require __DIR__ . '/../views/dashboards/ops_manager/home.php';
require __DIR__ . '/../views/layouts/footer.php';
