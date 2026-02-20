<?php
/**
 * Customer rep dashboard.
 */
auth_require_role(['customer_rep']);

$currentUser = $_SESSION['user'] ?? null;

$pageTitle = 'Dashboard';
$base = '';
require __DIR__ . '/../views/layouts/header.php';
require __DIR__ . '/../views/dashboards/customer_rep/home.php';
require __DIR__ . '/../views/layouts/footer.php';
