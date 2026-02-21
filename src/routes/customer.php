<?php
/**
 * Customer portal. Logged-in customers can view their profile and create tickets.
 */
auth_require_role(['customer']);

$currentUser = $_SESSION['user'] ?? null;
$customerId = !empty($_SESSION['customer_id']) ? (int) $_SESSION['customer_id'] : 0;

if ($customerId <= 0) {
    $pageTitle = 'Customer Portal';
    $base = '';
    require __DIR__ . '/../views/layouts/header.php';
    echo '<p style="color: #c62828;">Your account is not linked to a customer record. Please contact support.</p>';
    require __DIR__ . '/../views/layouts/footer.php';
    exit;
}

$api_internal_call = true;

// POST: create ticket (customer_id from session in ticket_create)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['create_ticket'])) {
    try {
        $create_result = include __DIR__ . '/../api/customer_rep/ticket_create.php';
    } catch (Throwable $e) {
        $create_result = ['error' => 'Create failed: ' . $e->getMessage()];
    }
    unset($api_internal_call);
    if (!empty($create_result['error'])) {
        $_SESSION['flash_error'] = $create_result['error'];
    } elseif (is_array($create_result) && !empty($create_result['ticket_id'])) {
        $_SESSION['flash_success'] = 'Ticket created successfully.';
    } else {
        $_SESSION['flash_error'] = 'Ticket could not be created. Please try again.';
    }
    header('Location: index.php?dashboard=customer');
    exit;
}

// GET: own customer (customer_rep/customer_get)
$_GET['customer_id'] = $customerId;
$customer_result = include __DIR__ . '/../api/customer_rep/customer_get.php';
$customer = (is_array($customer_result) && !empty($customer_result['customer'])) ? $customer_result['customer'] : null;

// GET: own tickets (customer_rep/tickets_list)
$_GET['customer_id'] = $customerId;
$_GET['limit'] = 50;
$tickets_data = include __DIR__ . '/../api/customer_rep/tickets_list.php';
$tickets = $tickets_data['tickets'] ?? [];

unset($api_internal_call);

$pageTitle = 'Customer Portal';
$base = '';
require __DIR__ . '/../views/layouts/header.php';
require __DIR__ . '/../views/dashboards/customer/home.php';
require __DIR__ . '/../views/layouts/footer.php';
