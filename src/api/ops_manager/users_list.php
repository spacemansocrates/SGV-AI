<?php
/**
 * List all system users. GET index.php?api=1&path=ops_manager/users_list
 * Ops manager (and admin) only. Excludes password_hash.
 */
require_once __DIR__ . '/../../core/helpers.php';
auth_require_role(['ops_manager', 'admin']);

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    json_response(['error' => 'Method not allowed'], 405);
}

global $pdo;
$stmt = $pdo->query('
    SELECT u.user_id, u.role_id, u.username, u.email, u.full_name, u.phone, u.is_active, u.last_login, u.created_at, u.updated_at,
           r.role_name
    FROM users u
    JOIN roles r ON u.role_id = r.role_id
    ORDER BY u.username ASC
');
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

return api_return(['users' => $users]);
