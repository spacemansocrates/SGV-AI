<?php
/**
 * Workshop Supervisor dashboard.
 */
auth_require_role(['workshop_supervisor']);

$currentUser = $_SESSION['user'] ?? null;

$pageTitle = 'Workshop Supervisor Dashboard';
$base = '';
require __DIR__ . '/../views/layouts/header.php';
require __DIR__ . '/../views/dashboards/workshop_supervisor/home.php';
require __DIR__ . '/../views/layouts/footer.php';
