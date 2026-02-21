<?php
/**
 * Admin dashboard. Included from index.php when role is admin.
 */
auth_require_role(['admin']);

$currentUser = $_SESSION['user'] ?? null;

$pageTitle = 'Admin Dashboard';
$base = '';
require __DIR__ . '/../views/layouts/header.php';
require __DIR__ . '/../views/dashboards/admin/home.php';
require __DIR__ . '/../views/layouts/footer.php';
