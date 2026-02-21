<?php
/**
 * List tickets (inquiries). GET index.php?api=1&path=customer_rep/tickets_list
 * Optional: customer_id, limit, offset
 */
require_once __DIR__ . '/../../core/helpers.php';
auth_require_role(['customer_rep', 'workshop_supervisor', 'stores_manager', 'ops_manager', 'accountant', 'customer']);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['error' => 'Method not allowed'], 405);
}

global $pdo;
if ($_SESSION['role_name'] === 'customer') {
    $customerId = !empty($_SESSION['customer_id']) ? (int) $_SESSION['customer_id'] : null;
    if ($customerId === null || $customerId <= 0) {
        return api_return(['tickets' => []]);
    }
} else {
    $customerId = isset($_GET['customer_id']) ? (int) $_GET['customer_id'] : null;
}
$limit = isset($_GET['limit']) ? min(100, max(1, (int) $_GET['limit'])) : 50;
$offset = isset($_GET['offset']) ? max(0, (int) $_GET['offset']) : 0;

$baseSelect = 'SELECT t.ticket_id, t.ticket_no, t.customer_id, t.bike_model, t.bike_vin, t.bike_mileage, t.complaint_description, t.warranty_flag, t.status, t.created_by, t.created_at, t.updated_at,
  tu.user_id AS created_by_user_id, tu.username AS created_by_username, tu.full_name AS created_by_full_name,
  c.customer_id AS c_customer_id, c.name AS c_name, c.phone AS c_phone, c.email AS c_email, c.address AS c_address, c.customer_type AS c_customer_type, c.created_by AS c_created_by, c.created_at AS c_created_at, c.updated_at AS c_updated_at,
  cu.user_id AS c_created_by_user_id, cu.username AS c_created_by_username, cu.full_name AS c_created_by_full_name
FROM tickets t
LEFT JOIN users tu ON t.created_by = tu.user_id
LEFT JOIN customers c ON t.customer_id = c.customer_id
LEFT JOIN users cu ON c.created_by = cu.user_id';
$orderLimit = ' ORDER BY t.created_at DESC LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;

if ($customerId !== null && $customerId > 0) {
    $stmt = $pdo->prepare($baseSelect . ' WHERE t.customer_id = ?' . $orderLimit);
    $stmt->execute([$customerId]);
} else {
    $stmt = $pdo->query($baseSelect . $orderLimit);
}
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build ticket list with created_by_user and customer (with created_by_user)
$tickets = [];
foreach ($rows as $r) {
    $ticket = [
        'ticket_id' => $r['ticket_id'],
        'ticket_no' => $r['ticket_no'],
        'customer_id' => $r['customer_id'],
        'bike_model' => $r['bike_model'],
        'bike_vin' => $r['bike_vin'],
        'bike_mileage' => $r['bike_mileage'],
        'complaint_description' => $r['complaint_description'],
        'warranty_flag' => $r['warranty_flag'],
        'status' => $r['status'],
        'created_by' => $r['created_by'],
        'created_at' => $r['created_at'],
        'updated_at' => $r['updated_at'],
    ];
    if (!empty($r['created_by_user_id'])) {
        $ticket['created_by_user'] = [
            'user_id' => (int) $r['created_by_user_id'],
            'username' => $r['created_by_username'],
            'full_name' => $r['created_by_full_name'],
        ];
    }
    if (!empty($r['c_customer_id'])) {
        $ticket['customer'] = [
            'customer_id' => (int) $r['c_customer_id'],
            'name' => $r['c_name'],
            'phone' => $r['c_phone'],
            'email' => $r['c_email'],
            'address' => $r['c_address'],
            'customer_type' => $r['c_customer_type'],
            'created_by' => $r['c_created_by'],
            'created_at' => $r['c_created_at'],
            'updated_at' => $r['c_updated_at'],
        ];
        if (!empty($r['c_created_by_user_id'])) {
            $ticket['customer']['created_by_user'] = [
                'user_id' => (int) $r['c_created_by_user_id'],
                'username' => $r['c_created_by_username'],
                'full_name' => $r['c_created_by_full_name'],
            ];
        }
    }
    $tickets[] = $ticket;
}

return api_return(['tickets' => $tickets]);
