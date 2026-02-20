<?php
/**
 * Logged-in user details. Expects $currentUser (array from users + role_name).
 */
if (empty($currentUser)) return;
?>
<section class="user-details" style="margin-top: 1.5rem;">
    <h2>Logged-in user</h2>
    <dl style="display: grid; grid-template-columns: auto 1fr; gap: 0.25rem 1.5rem; margin: 0;">
        <dt>User ID</dt>
        <dd><?php echo htmlspecialchars((string) $currentUser['user_id']); ?></dd>
        <dt>Username</dt>
        <dd><?php echo htmlspecialchars($currentUser['username'] ?? ''); ?></dd>
        <dt>Full name</dt>
        <dd><?php echo htmlspecialchars($currentUser['full_name'] ?? ''); ?></dd>
        <dt>Email</dt>
        <dd><?php echo htmlspecialchars($currentUser['email'] ?? '—'); ?></dd>
        <dt>Phone</dt>
        <dd><?php echo htmlspecialchars($currentUser['phone'] ?? '—'); ?></dd>
        <dt>Role</dt>
        <dd><?php echo htmlspecialchars($currentUser['role_name'] ?? ''); ?></dd>
        <dt>Active</dt>
        <dd><?php echo !empty($currentUser['is_active']) ? 'Yes' : 'No'; ?></dd>
        <dt>Last login</dt>
        <dd><?php echo !empty($currentUser['last_login']) ? htmlspecialchars($currentUser['last_login']) : '—'; ?></dd>
        <dt>Created at</dt>
        <dd><?php echo htmlspecialchars($currentUser['created_at'] ?? '—'); ?></dd>
        <dt>Updated at</dt>
        <dd><?php echo htmlspecialchars($currentUser['updated_at'] ?? '—'); ?></dd>
    </dl>
</section>
