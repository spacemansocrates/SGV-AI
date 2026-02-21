<?php
/**
 * Shared helpers for the app (used by API and optionally by routes).
 */

/**
 * Get request body as associative array (POST or JSON).
 */
function get_request_body() {
    if (!empty($_POST)) {
        return $_POST;
    }
    $ct = $_SERVER['CONTENT_TYPE'] ?? '';
    if (strpos($ct, 'application/json') !== false) {
        $raw = file_get_contents('php://input');
        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : [];
    }
    return [];
}

/**
 * Send JSON response and exit.
 */
function json_response($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

/**
 * Return data for internal (route) call, or send JSON and exit for API requests.
 * Use this at the end of API endpoints so they work both when:
 * - Called via HTTP (api=1&path=...) → sends JSON.
 * - Included from a route with $api_internal_call = true → returns the array.
 * Example: return api_return(['customers' => $customers]);
 */
function api_return($data) {
    global $api_internal_call;
    if (!empty($api_internal_call)) {
        return $data;
    }
    json_response($data);
}

/**
 * Return error array for internal call, or send JSON error and exit for API requests.
 * Use in endpoints that are included from routes (e.g. create/update forms).
 * Example: return api_error('name is required', 400);
 */
function api_error($message, $code = 400) {
    global $api_internal_call;
    $payload = ['error' => $message];
    if (!empty($api_internal_call)) {
        return $payload + ['code' => $code];
    }
    json_response($payload, $code);
}
