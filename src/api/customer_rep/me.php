<?php
/**
 * Customer rep API example: current user info. index.php?api=1&path=customer_rep/me
 */
require_once __DIR__ . '/../../core/helpers.php';
auth_require_role(['customer_rep', 'workshop_supervisor', 'stores_manager', 'ops_manager', 'accountant']);
return api_return(['role' => $_SESSION['role_name'], 'user_id' => $_SESSION['user_id']]);
