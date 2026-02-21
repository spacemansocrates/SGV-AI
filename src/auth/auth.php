<?php
/**
 * Simple auth helpers. Include config.php before this (so $pdo exists).
 * Session is started automatically when this file is included.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Try to log in. Returns true on success, false otherwise.
 * Regenerates session id on success to prevent session fixation.
 */
function auth_login($username, $password) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT user_id, role_id, password_hash FROM users WHERE username = ? AND is_active = 1 LIMIT 1');
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user || !password_verify($password, $user['password_hash'])) {
        return false;
    }
    session_regenerate_id(true);
    $roleName = get_role_name((int) $user['role_id']);
    $userId = (int) $user['user_id'];
    $_SESSION['user_id'] = $userId;
    $_SESSION['role_name'] = $roleName;
    $pdo->prepare('UPDATE users SET last_login = NOW() WHERE user_id = ?')->execute([$userId]);
    $stmt = $pdo->prepare('SELECT u.user_id, u.customer_id, u.username, u.email, u.full_name, u.phone, u.is_active, u.last_login, u.created_at, u.updated_at, r.role_name FROM users u JOIN roles r ON u.role_id = r.role_id WHERE u.user_id = ? LIMIT 1');
    $stmt->execute([$userId]);
    $_SESSION['user'] = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($_SESSION['role_name'] === 'customer' && !empty($_SESSION['user']['customer_id'])) {
        $_SESSION['customer_id'] = (int) $_SESSION['user']['customer_id'];
    }
    return true;
}

/**
 * Get role name from role_id.
 */
function get_role_name($roleId) {
    global $pdo;
    $stmt = $pdo->prepare('SELECT role_name FROM roles WHERE role_id = ? LIMIT 1');
    $stmt->execute([$roleId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['role_name'] : null;
}

/**
 * Check if user is logged in.
 */
function auth_check() {
    return !empty($_SESSION['user_id']) && !empty($_SESSION['role_name']);
}

/**
 * Redirect to index (login page) if not logged in.
 * When called from API (GET api=1), returns JSON 401 instead of redirect.
 */
function auth_require() {
    if (!auth_check()) {
        if (!empty($_GET['api']) && $_GET['api'] === '1') {
            http_response_code(401);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Not logged in']);
            exit;
        }
        header('Location: index.php');
        exit;
    }
}

/**
 * Require one of the given roles; 403 otherwise.
 * When called from API (GET api=1), returns JSON 403 instead of plain text.
 */
function auth_require_role($allowedRoles) {
    auth_require();
    if (!in_array($_SESSION['role_name'], $allowedRoles, true)) {
        if (!empty($_GET['api']) && $_GET['api'] === '1') {
            http_response_code(403);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['error' => 'Forbidden']);
            exit;
        }
        http_response_code(403);
        echo 'Forbidden';
        exit;
    }
}

/**
 * Log out and optionally redirect.
 */
function auth_logout($redirectTo = null) {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    if ($redirectTo !== null) {
        header('Location: ' . $redirectTo);
        exit;
    }
}

