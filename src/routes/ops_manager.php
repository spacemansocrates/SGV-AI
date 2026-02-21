<?php
/**
 * Operations Manager dashboard (Refactored)
 */
auth_require_role(['ops_manager']);

$currentUser = $_SESSION['user'] ?? null;

// Internal call: API files return arrays instead of JSON (no extra HTTP, single request)
$api_internal_call = true;

// Handle Create Customer form POST (uses the same endpoint via include)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['create_customer'])) {
    try {
        $create_result = include __DIR__ . '/../api/customer_rep/customer_create.php';
    } catch (Throwable $e) {
        $create_result = ['error' => 'Create failed: ' . $e->getMessage()];
    }
    unset($api_internal_call);
    if (!empty($create_result['error'])) {
        $_SESSION['flash_error'] = $create_result['error'];
    } elseif (is_array($create_result) && !empty($create_result['customer_id'])) {
        $_SESSION['flash_success'] = 'Customer created successfully.';
    } else {
        $_SESSION['flash_error'] = 'Customer could not be created. Please try again.';
    }
    header('Location: index.php?dashboard=ops_manager');
    exit;
}

// Fetch customers
$customers_data = include __DIR__ . '/../api/customer_rep/customers_list.php';
$customers = $customers_data['customers'] ?? [];

$_GET['limit'] = 25;
$tickets_data = include __DIR__ . '/../api/customer_rep/tickets_list.php';
$tickets = $tickets_data['tickets'] ?? [];

$users_data = include __DIR__ . '/../api/ops_manager/users_list.php';
$users = $users_data['users'] ?? [];

unset($api_internal_call);

$pageTitle = 'Operations Manager Dashboard';
$base = '';

require __DIR__ . '/../views/layouts/header.php';
require __DIR__ . '/../views/dashboards/ops_manager/home.php';
require __DIR__ . '/../views/layouts/footer.php';
