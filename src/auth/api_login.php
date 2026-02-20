<?php
/**
 * API login: JSON response. Include from public/api/login.php (config + auth already loaded).
 */
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($username === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Username and password required']);
    exit;
}

if (auth_login($username, $password)) {
    echo json_encode(['success' => true, 'role' => $_SESSION['role_name']]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
}
