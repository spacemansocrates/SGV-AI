<?php
/**
 * Get one ticket. GET index.php?api=1&path=customer_rep/ticket_get&ticket_id=1
 * Response includes customer snippet when ticket has customer_id.
 */
require_once __DIR__ . '/../../core/helpers.php';
auth_require_role(['customer_rep', 'workshop_supervisor', 'stores_manager', 'ops_manager', 'accountant']);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    return api_error('Method not allowed', 405);
}

$ticketId = isset($_GET['ticket_id']) ? (int) $_GET['ticket_id'] : 0;
if ($ticketId <= 0) {
    return api_error('ticket_id is required', 400);
}

global $pdo;
$stmt = $pdo->prepare('SELECT t.ticket_id, t.ticket_no, t.customer_id, t.bike_model, t.bike_vin, t.bike_mileage, t.complaint_description, t.warranty_flag, t.status, t.current_assignee_role, t.created_by, t.created_at, t.updated_at FROM tickets t WHERE t.ticket_id = ?');
$stmt->execute([$ticketId]);
$ticket = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$ticket) {
    return api_error('Ticket not found', 404);
}

// Include ticket creator (user who created the ticket)
if (!empty($ticket['created_by'])) {
    $u = $pdo->prepare('SELECT user_id, username, full_name FROM users WHERE user_id = ?');
    $u->execute([$ticket['created_by']]);
    $ticket['created_by_user'] = $u->fetch(PDO::FETCH_ASSOC);
}

if (!empty($ticket['customer_id'])) {
    $cust = $pdo->prepare('SELECT customer_id, name, phone, email, address, customer_type, created_by, created_at, updated_at FROM customers WHERE customer_id = ?');
    $cust->execute([$ticket['customer_id']]);
    $ticket['customer'] = $cust->fetch(PDO::FETCH_ASSOC);
    // Include customer creator (user who created the customer)
    if ($ticket['customer'] && !empty($ticket['customer']['created_by'])) {
        $cu = $pdo->prepare('SELECT user_id, username, full_name FROM users WHERE user_id = ?');
        $cu->execute([$ticket['customer']['created_by']]);
        $ticket['customer']['created_by_user'] = $cu->fetch(PDO::FETCH_ASSOC);
    }
}

return api_return(['ticket' => $ticket]);
