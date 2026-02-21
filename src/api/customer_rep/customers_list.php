<?php
/**
 * List customers. GET index.php?api=1&path=customer_rep/customers_list
 * Optional query: search (filters by name, email, phone)
 */
require_once __DIR__ . '/../../core/helpers.php';
auth_require_role(['customer_rep', 'workshop_supervisor', 'stores_manager', 'ops_manager', 'accountant']);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['error' => 'Method not allowed'], 405);
}

global $pdo;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

$baseSelect = 'SELECT c.customer_id, c.name, c.phone, c.email, c.address, c.customer_type, c.created_by, c.created_at, c.updated_at,
  u.user_id AS created_by_user_id, u.username AS created_by_username, u.full_name AS created_by_full_name
FROM customers c
LEFT JOIN users u ON c.created_by = u.user_id';

if ($search === '') {
    $stmt = $pdo->query($baseSelect . ' ORDER BY c.name ASC');
} else {
    $like = '%' . $search . '%';
    $stmt = $pdo->prepare($baseSelect . ' WHERE c.name LIKE ? OR c.email LIKE ? OR c.phone LIKE ? ORDER BY c.name ASC');
    $stmt->execute([$like, $like, $like]);
}
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$customers = [];
foreach ($rows as $r) {
    $customer = [
        'customer_id' => (int) $r['customer_id'],
        'name' => $r['name'],
        'phone' => $r['phone'],
        'email' => $r['email'],
        'address' => $r['address'],
        'customer_type' => $r['customer_type'],
        'created_by' => $r['created_by'],
        'created_at' => $r['created_at'],
        'updated_at' => $r['updated_at'],
    ];
    if (!empty($r['created_by_user_id'])) {
        $customer['created_by_user'] = [
            'user_id' => (int) $r['created_by_user_id'],
            'username' => $r['created_by_username'],
            'full_name' => $r['created_by_full_name'],
        ];
    }
    $customers[] = $customer;
}

return api_return(['customers' => $customers]);
