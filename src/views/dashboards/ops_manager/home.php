<h1>Operations Manager Dashboard</h1>
<p>Welcome. Oversee tickets, approvals, and operations here.</p>
<?php require __DIR__ . '/../../components/user_details.php'; ?>

<?php
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
if (isset($_SESSION['flash_success'])) unset($_SESSION['flash_success']);
if (isset($_SESSION['flash_error'])) unset($_SESSION['flash_error']);
if ($flash_success) echo '<p style="color: green; padding: 0.5rem; background: #e8f5e9;">' . htmlspecialchars($flash_success) . '</p>';
if ($flash_error) echo '<p style="color: #c62828; padding: 0.5rem; background: #ffebee;">' . htmlspecialchars($flash_error) . '</p>';
?>

<section class="create-customer" style="margin-top: 2rem;">
    <h2>Create new customer</h2>
    <p style="color: #666;">Uses endpoint: POST customer_rep/customer_create</p>
    <form method="post" action="index.php?dashboard=ops_manager" style="max-width: 500px; margin-top: 0.5rem;">
        <input type="hidden" name="create_customer" value="1">
        <p style="margin: 0.5rem 0;">
            <label for="name">Name <span style="color: #c62828;">*</span></label><br>
            <input type="text" id="name" name="name" required maxlength="255" style="width: 100%; padding: 0.4rem; box-sizing: border-box;">
        </p>
        <p style="margin: 0.5rem 0;">
            <label for="phone">Phone</label><br>
            <input type="text" id="phone" name="phone" maxlength="50" style="width: 100%; padding: 0.4rem; box-sizing: border-box;">
        </p>
        <p style="margin: 0.5rem 0;">
            <label for="email">Email</label><br>
            <input type="email" id="email" name="email" maxlength="255" style="width: 100%; padding: 0.4rem; box-sizing: border-box;">
        </p>
        <p style="margin: 0.5rem 0;">
            <label for="address">Address</label><br>
            <textarea id="address" name="address" rows="2" style="width: 100%; padding: 0.4rem; box-sizing: border-box;"></textarea>
        </p>
        <p style="margin: 0.5rem 0;">
            <label for="customer_type">Type</label><br>
            <select id="customer_type" name="customer_type" style="padding: 0.4rem;">
                <option value="individual">Individual</option>
                <option value="corporate">Corporate</option>
            </select>
        </p>
        <p style="margin: 0.5rem 0;">
            <button type="submit" style="padding: 0.5rem 1rem;">Create customer</button>
        </p>
    </form>
</section>

<section class="system-users" style="margin-top: 2rem;">
    <h2>System users</h2>
    <p style="color: #666;"><?php echo count($users); ?> user(s)</p>
    <?php if (empty($users)) : ?>
        <p>No users found.</p>
    <?php else : ?>
    <table style="border-collapse: collapse; width: 100%; max-width: 800px; margin-top: 0.5rem;">
        <thead>
            <tr style="background: #eee; text-align: left;">
                <th style="padding: 0.5rem; border: 1px solid #ccc;">ID</th>
                <th style="padding: 0.5rem; border: 1px solid #ccc;">Username</th>
                <th style="padding: 0.5rem; border: 1px solid #ccc;">Full name</th>
                <th style="padding: 0.5rem; border: 1px solid #ccc;">Email</th>
                <th style="padding: 0.5rem; border: 1px solid #ccc;">Role</th>
                <th style="padding: 0.5rem; border: 1px solid #ccc;">Active</th>
                <th style="padding: 0.5rem; border: 1px solid #ccc;">Last login</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $u) : ?>
            <tr>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo (int) $u['user_id']; ?></td>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo htmlspecialchars($u['username'] ?? ''); ?></td>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo htmlspecialchars($u['full_name'] ?? ''); ?></td>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo htmlspecialchars($u['email'] ?? '—'); ?></td>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo htmlspecialchars($u['role_name'] ?? ''); ?></td>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo !empty($u['is_active']) ? 'Yes' : 'No'; ?></td>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo htmlspecialchars($u['last_login'] ?? '—'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>

<section class="customers" style="margin-top: 2rem;">
    <h2>Customers</h2>
    <p style="color: #666;"><?php echo count($customers); ?> customer(s)</p>
    <?php if (empty($customers)) : ?>
        <p>No customers found.</p>
    <?php else : ?>
    <table style="border-collapse: collapse; width: 100%; max-width: 900px; margin-top: 0.5rem;">
        <thead>
            <tr style="background: #eee; text-align: left;">
                <th style="padding: 0.5rem; border: 1px solid #ccc;">ID</th>
                <th style="padding: 0.5rem; border: 1px solid #ccc;">Name</th>
                <th style="padding: 0.5rem; border: 1px solid #ccc;">Email</th>
                <th style="padding: 0.5rem; border: 1px solid #ccc;">Phone</th>
                <th style="padding: 0.5rem; border: 1px solid #ccc;">Type</th>
                <th style="padding: 0.5rem; border: 1px solid #ccc;">Created</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($customers as $c) : ?>
            <tr>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo (int) $c['customer_id']; ?></td>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo htmlspecialchars($c['name'] ?? ''); ?></td>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo htmlspecialchars($c['email'] ?? '—'); ?></td>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo htmlspecialchars($c['phone'] ?? '—'); ?></td>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo htmlspecialchars($c['customer_type'] ?? '—'); ?></td>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo htmlspecialchars($c['created_at'] ?? '—'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>

<section class="tickets" style="margin-top: 2rem;">
    <h2>Tickets</h2>
    <p style="color: #666;"><?php echo count($tickets); ?> ticket(s)</p>
    <?php if (empty($tickets)) : ?>
        <p>No tickets found.</p>
    <?php else : ?>
    <table style="border-collapse: collapse; width: 100%; max-width: 100%; margin-top: 0.5rem;">
        <thead>
            <tr style="background: #eee; text-align: left;">
                <th style="padding: 0.5rem; border: 1px solid #ccc;">ID</th>
                <th style="padding: 0.5rem; border: 1px solid #ccc;">Ticket #</th>
                <th style="padding: 0.5rem; border: 1px solid #ccc;">Customer</th>
                <th style="padding: 0.5rem; border: 1px solid #ccc;">Status</th>
                <th style="padding: 0.5rem; border: 1px solid #ccc;">Created</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($tickets as $t) : ?>
            <tr>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo (int) $t['ticket_id']; ?></td>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo htmlspecialchars($t['ticket_no'] ?? ''); ?></td>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php
                    if (!empty($t['customer']['name'])) {
                        echo htmlspecialchars($t['customer']['name']) . ' (#'. (int) $t['customer_id'] . ')';
                    } else {
                        echo (int) $t['customer_id'];
                    }
                ?></td>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo htmlspecialchars($t['status'] ?? '—'); ?></td>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo htmlspecialchars($t['created_at'] ?? '—'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>
