<?php
/**
 * Customer rep dashboard. Uses all customer_rep endpoints via include.
 * Endpoints: me, customers_list, customer_create, customer_get, customer_update, tickets_list, ticket_create, ticket_get.
 */
auth_require_role(['customer_rep']);

$currentUser = $_SESSION['user'] ?? null;

$api_internal_call = true;

// POST: customer_rep/customer_create
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
    header('Location: index.php?dashboard=customer_rep');
    exit;
}

// POST: customer_rep/ticket_create
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
    header('Location: index.php?dashboard=customer_rep');
    exit;
}

// POST: customer_rep/customer_update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['update_customer'])) {
    try {
        $update_result = include __DIR__ . '/../api/customer_rep/customer_update.php';
    } catch (Throwable $e) {
        $update_result = ['error' => 'Update failed: ' . $e->getMessage()];
    }
    unset($api_internal_call);
    if (!empty($update_result['error'])) {
        $_SESSION['flash_error'] = $update_result['error'];
    } elseif (is_array($update_result) && !empty($update_result['customer'])) {
        $_SESSION['flash_success'] = 'Customer updated successfully.';
    } else {
        $_SESSION['flash_error'] = 'Customer could not be updated. Please try again.';
    }
    header('Location: index.php?dashboard=customer_rep' . (isset($_POST['customer_id']) ? '&customer_id=' . (int) $_POST['customer_id'] : ''));
    exit;
}

// GET: customer_rep/me
$me_data = include __DIR__ . '/../api/customer_rep/me.php';
$me = is_array($me_data) ? $me_data : [];

// GET: customer_rep/customer_get (when customer_id in query)
$customer = null;
$customer_error = null;
if (!empty($_GET['customer_id']) && (int) $_GET['customer_id'] > 0) {
    $_GET['customer_id'] = (int) $_GET['customer_id'];
    $customer_result = include __DIR__ . '/../api/customer_rep/customer_get.php';
    if (is_array($customer_result) && !empty($customer_result['customer'])) {
        $customer = $customer_result['customer'];
    } elseif (is_array($customer_result) && !empty($customer_result['error'])) {
        $customer_error = $customer_result['error'];
    }
}

// GET: customer_rep/ticket_get (when ticket_id in query)
$ticket = null;
$ticket_error = null;
if (!empty($_GET['ticket_id']) && (int) $_GET['ticket_id'] > 0) {
    $_GET['ticket_id'] = (int) $_GET['ticket_id'];
    $ticket_result = include __DIR__ . '/../api/customer_rep/ticket_get.php';
    if (is_array($ticket_result) && !empty($ticket_result['ticket'])) {
        $ticket = $ticket_result['ticket'];
    } elseif (is_array($ticket_result) && !empty($ticket_result['error'])) {
        $ticket_error = $ticket_result['error'];
    }
}

// GET: customer_rep/customers_list
$_GET['search'] = isset($_GET['search']) ? trim($_GET['search']) : '';
$customers_data = include __DIR__ . '/../api/customer_rep/customers_list.php';
$customers = $customers_data['customers'] ?? [];

// GET: customer_rep/tickets_list
$_GET['limit'] = 25;
$tickets_data = include __DIR__ . '/../api/customer_rep/tickets_list.php';
$tickets = $tickets_data['tickets'] ?? [];

unset($api_internal_call);

$pageTitle = 'Dashboard';
$base = '';
require __DIR__ . '/../views/layouts/header.php';
require __DIR__ . '/../views/dashboards/customer_rep/home.php';
require __DIR__ . '/../views/layouts/footer.php';
