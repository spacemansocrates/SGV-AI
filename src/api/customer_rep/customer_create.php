<?php
/**
 * Create customer. POST index.php?api=1&path=customer_rep/customer_create
 * Body: name (required), phone, email, address, customer_type
 */
require_once __DIR__ . '/../../core/helpers.php';
auth_require_role(['customer_rep', 'workshop_supervisor', 'stores_manager', 'ops_manager', 'accountant']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return api_error('Method not allowed', 405);
}

$body = get_request_body();
$name = isset($body['name']) ? trim($body['name']) : '';
if ($name === '') {
    return api_error('name is required', 400);
}

$phone = isset($body['phone']) ? trim($body['phone']) : null;
$email = isset($body['email']) ? trim($body['email']) : null;
$address = isset($body['address']) ? trim($body['address']) : null;
$customerType = isset($body['customer_type']) ? trim($body['customer_type']) : 'individual';
$createdBy = !empty($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;

global $pdo;
$stmt = $pdo->prepare('INSERT INTO customers (name, phone, email, address, customer_type, created_by) VALUES (?, ?, ?, ?, ?, ?)');
$stmt->execute([$name, $phone, $email, $address, $customerType, $createdBy]);
$customerId = (int) $pdo->lastInsertId();

$stmt = $pdo->prepare('SELECT customer_id, name, phone, email, address, customer_type, created_by, created_at, updated_at FROM customers WHERE customer_id = ?');
$stmt->execute([$customerId]);
$customer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!empty($customer['created_by'])) {
    $u = $pdo->prepare('SELECT user_id, username, full_name FROM users WHERE user_id = ?');
    $u->execute([$customer['created_by']]);
    $customer['created_by_user'] = $u->fetch(PDO::FETCH_ASSOC);
}

return api_return(['customer' => $customer, 'customer_id' => $customerId]);
