<?php
/**
 * Single entry point: API (path=...), login page, or dashboard by role.
 */
require_once __DIR__ . '/src/config/config.php';

// API: index.php?api=1&path=common/login
if (!empty($_GET['api']) && $_GET['api'] === '1') {
    $path = isset($_GET['path']) ? trim($_GET['path']) : '';
    $path = str_replace(['..', '\\'], '', $path);
    $parts = $path !== '' ? explode('/', $path) : [];
    if (count($parts) >= 2) {
        $file = __DIR__ . '/src/api/' . $parts[0] . '/' . $parts[1] . '.php';
        if (is_file($file)) {
            require_once __DIR__ . '/src/auth/auth.php';
            require $file;
            exit;
        }
    }
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid or missing path']);
    exit;
}

require_once __DIR__ . '/src/auth/auth.php';

if (!auth_check()) {
    require __DIR__ . '/src/auth/login.php';
    exit;
}

$role = $_SESSION['role_name'];
$routes = [
    'admin' => 'admin.php',
    'customer_rep' => 'customer_rep.php',
    'workshop_supervisor' => 'workshop_supervisor.php',
    'stores_manager' => 'stores_manager.php',
    'ops_manager' => 'ops_manager.php',
    'accountant' => 'accountant.php',
    'customer' => 'customer.php',
];

$requested = isset($_GET['dashboard']) ? trim($_GET['dashboard']) : '';
if ($requested === '' || !isset($routes[$requested]) || $requested !== $role) {
    header('Location: index.php?dashboard=' . rawurlencode($role));
    exit;
}

$file = $routes[$requested];
require __DIR__ . '/src/routes/' . $file;
