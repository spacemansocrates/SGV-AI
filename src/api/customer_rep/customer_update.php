<?php
/**
 * Update customer. POST index.php?api=1&path=customer_rep/customer_update
 * Body: customer_id (required), name, phone, email, address, customer_type
 */
require_once __DIR__ . '/../../core/helpers.php';
auth_require_role(['customer_rep', 'workshop_supervisor', 'stores_manager', 'ops_manager', 'accountant']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return api_error('Method not allowed', 405);
}

$body = get_request_body();
$customerId = isset($body['customer_id']) ? (int) $body['customer_id'] : 0;
if ($customerId <= 0) {
    return api_error('customer_id is required', 400);
}

global $pdo;
$stmt = $pdo->prepare('SELECT customer_id FROM customers WHERE customer_id = ?');
$stmt->execute([$customerId]);
if (!$stmt->fetch()) {
    return api_error('Customer not found', 404);
}

$name = isset($body['name']) ? trim($body['name']) : null;
$phone = isset($body['phone']) ? trim($body['phone']) : null;
$email = isset($body['email']) ? trim($body['email']) : null;
$address = isset($body['address']) ? trim($body['address']) : null;
$customerType = isset($body['customer_type']) ? trim($body['customer_type']) : null;

$updates = [];
$params = [];
if ($name !== null) { $updates[] = 'name = ?'; $params[] = $name; }
if ($phone !== null) { $updates[] = 'phone = ?'; $params[] = $phone; }
if ($email !== null) { $updates[] = 'email = ?'; $params[] = $email; }
if ($address !== null) { $updates[] = 'address = ?'; $params[] = $address; }
if ($customerType !== null) { $updates[] = 'customer_type = ?'; $params[] = $customerType; }

$fetchCustomer = function () use ($pdo, $customerId) {
    $stmt = $pdo->prepare('SELECT customer_id, name, phone, email, address, customer_type, created_by, created_at, updated_at FROM customers WHERE customer_id = ?');
    $stmt->execute([$customerId]);
    $customer = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!empty($customer['created_by'])) {
        $u = $pdo->prepare('SELECT user_id, username, full_name FROM users WHERE user_id = ?');
        $u->execute([$customer['created_by']]);
        $customer['created_by_user'] = $u->fetch(PDO::FETCH_ASSOC);
    }
    return $customer;
};

if (!empty($updates)) {
    $params[] = $customerId;
    $sql = 'UPDATE customers SET ' . implode(', ', $updates) . ' WHERE customer_id = ?';
    $pdo->prepare($sql)->execute($params);
}

return api_return(['customer' => $fetchCustomer()]);
