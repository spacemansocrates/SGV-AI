<?php
/**
 * Create ticket (log inquiry). POST index.php?api=1&path=customer_rep/ticket_create
 * Body: customer_id (required), complaint_description (required), bike_model, bike_vin, bike_mileage, warranty_flag
 */
require_once __DIR__ . '/../../core/helpers.php';
auth_require_role(['customer_rep', 'workshop_supervisor', 'stores_manager', 'ops_manager', 'accountant', 'customer']);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return api_error('Method not allowed', 405);
}

$body = get_request_body();
$complaintDescription = isset($body['complaint_description']) ? trim($body['complaint_description']) : '';

// Customer role: must use their own customer_id from session (ignore body)
if ($_SESSION['role_name'] === 'customer') {
    $customerId = !empty($_SESSION['customer_id']) ? (int) $_SESSION['customer_id'] : 0;
    if ($customerId <= 0) {
        return api_error('Customer account not linked. Please contact support.', 403);
    }
} else {
    $customerId = isset($body['customer_id']) ? (int) $body['customer_id'] : 0;
    if ($customerId <= 0) {
        return api_error('customer_id is required', 400);
    }
}

if ($complaintDescription === '') {
    return api_error('complaint_description is required', 400);
}

$bikeModel = isset($body['bike_model']) ? trim($body['bike_model']) : null;
$bikeVin = isset($body['bike_vin']) ? trim($body['bike_vin']) : null;
$bikeMileage = isset($body['bike_mileage']) ? (int) $body['bike_mileage'] : null;
$warrantyFlag = isset($body['warranty_flag']) ? (int) (bool) $body['warranty_flag'] : 0;
$createdBy = (int) $_SESSION['user_id'];

global $pdo;
$check = $pdo->prepare('SELECT customer_id FROM customers WHERE customer_id = ?');
$check->execute([$customerId]);
if (!$check->fetch()) {
    return api_error('Customer not found', 404);
}

$ticketNoPlaceholder = 'TKT-' . date('Ymd') . '-T' . uniqid();
$stmt = $pdo->prepare('INSERT INTO tickets (ticket_no, customer_id, bike_model, bike_vin, bike_mileage, complaint_description, warranty_flag, status, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
$stmt->execute([$ticketNoPlaceholder, $customerId, $bikeModel, $bikeVin, $bikeMileage, $complaintDescription, $warrantyFlag, 'Submitted', $createdBy]);
$ticketId = (int) $pdo->lastInsertId();

$finalTicketNo = 'TKT-' . date('Ymd') . '-' . $ticketId;
$pdo->prepare('UPDATE tickets SET ticket_no = ? WHERE ticket_id = ?')->execute([$finalTicketNo, $ticketId]);

$row = $pdo->prepare('SELECT t.ticket_id, t.ticket_no, t.customer_id, t.bike_model, t.bike_vin, t.bike_mileage, t.complaint_description, t.warranty_flag, t.status, t.created_by, t.created_at, t.updated_at FROM tickets t WHERE t.ticket_id = ?');
$row->execute([$ticketId]);
$ticket = $row->fetch(PDO::FETCH_ASSOC);

return api_return(['ticket' => $ticket, 'ticket_id' => $ticketId]);
