<?php
/**
 * API logout. Called via api.php?path=common/logout.
 * Config and auth already loaded by api.php.
 */
require_once __DIR__ . '/../../core/helpers.php';

auth_logout();
json_response(['success' => true]);
