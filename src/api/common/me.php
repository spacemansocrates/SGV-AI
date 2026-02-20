<?php
/**
 * Current user info for any logged-in user. Returns full user from session.
 * Call: index.php?api=1&path=common/me
 */
require_once __DIR__ . '/../../core/helpers.php';

auth_require();

$user = $_SESSION['user'] ?? null;
if (!$user) {
    json_response(['error' => 'Not logged in'], 401);
}
json_response($user);

