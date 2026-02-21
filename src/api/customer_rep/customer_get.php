<?php
/**
 * Get one customer. GET index.php?api=1&path=customer_rep/customer_get&customer_id=1
 */
require_once __DIR__ . '/../../core/helpers.php';
auth_require_role(['customer_rep', 'workshop_supervisor', 'stores_manager', 'ops_manager', 'accountant', 'customer']);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    return api_error('Method not allowed', 405);
}

$customerId = isset($_GET['customer_id']) ? (int) $_GET['customer_id'] : 0;
if ($customerId <= 0) {
    return api_error('customer_id is required', 400);
}
if ($_SESSION['role_name'] === 'customer' && (empty($_SESSION['customer_id']) || (int) $_SESSION['customer_id'] !== $customerId)) {
    return api_error('Forbidden', 403);
}

global $pdo;
$stmt = $pdo->prepare('SELECT customer_id, name, phone, email, address, customer_type, created_by, created_at, updated_at FROM customers WHERE customer_id = ?');
$stmt->execute([$customerId]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$customer) {
    return api_error('Customer not found', 404);
}

if (!empty($customer['created_by'])) {
    $u = $pdo->prepare('SELECT user_id, username, full_name FROM users WHERE user_id = ?');
    $u->execute([$customer['created_by']]);
    $customer['created_by_user'] = $u->fetch(PDO::FETCH_ASSOC);
}

return api_return(['customer' => $customer]);
