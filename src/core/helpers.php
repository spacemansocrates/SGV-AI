<?php
/**
 * Shared helpers for the app (used by API and optionally by dashboards).
 */

/**
 * Send JSON response and exit.
 */
function json_response($data, $code = 200) {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}
