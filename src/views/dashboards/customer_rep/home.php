<h1>Customer Rep Dashboard</h1>
<p>Welcome. Manage customers and tickets here. All data uses customer_rep endpoints.</p>
<?php require __DIR__ . '/../../components/user_details.php'; ?>

<?php if (!empty($me)) : ?>
<p style="color: #666;">Endpoint: customer_rep/me — Role: <?php echo htmlspecialchars($me['role'] ?? ''); ?>, User ID: <?php echo (int) ($me['user_id'] ?? 0); ?></p>
<?php endif; ?>

<?php
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
if (isset($_SESSION['flash_success'])) unset($_SESSION['flash_success']);
if (isset($_SESSION['flash_error'])) unset($_SESSION['flash_error']);
if ($flash_success) echo '<p style="color: green; padding: 0.5rem; background: #e8f5e9;">' . htmlspecialchars($flash_success) . '</p>';
if ($flash_error) echo '<p style="color: #c62828; padding: 0.5rem; background: #ffebee;">' . htmlspecialchars($flash_error) . '</p>';
if (isset($customer_error)) echo '<p style="color: #c62828; padding: 0.5rem; background: #ffebee;">' . htmlspecialchars($customer_error) . '</p>';
if (isset($ticket_error)) echo '<p style="color: #c62828; padding: 0.5rem; background: #ffebee;">' . htmlspecialchars($ticket_error) . '</p>';
?>

<?php if (!empty($customer)) : ?>
<section class="view-customer" style="margin-top: 2rem; padding: 1rem; border: 1px solid #ccc;">
    <h2>View / Edit customer (customer_rep/customer_get, customer_rep/customer_update)</h2>
    <p><strong>#<?php echo (int) $customer['customer_id']; ?></strong> <?php echo htmlspecialchars($customer['name'] ?? ''); ?> — <?php echo htmlspecialchars($customer['email'] ?? '—'); ?> — <?php echo htmlspecialchars($customer['phone'] ?? '—'); ?></p>
    <form method="post" action="index.php?dashboard=customer_rep" style="max-width: 500px; margin-top: 0.5rem;">
        <input type="hidden" name="update_customer" value="1">
        <input type="hidden" name="customer_id" value="<?php echo (int) $customer['customer_id']; ?>">
        <p style="margin: 0.5rem 0;"><label for="edit_name">Name</label><br><input type="text" id="edit_name" name="name" value="<?php echo htmlspecialchars($customer['name'] ?? ''); ?>" maxlength="255" style="width: 100%; padding: 0.4rem; box-sizing: border-box;"></p>
        <p style="margin: 0.5rem 0;"><label for="edit_phone">Phone</label><br><input type="text" id="edit_phone" name="phone" value="<?php echo htmlspecialchars($customer['phone'] ?? ''); ?>" maxlength="50" style="width: 100%; padding: 0.4rem; box-sizing: border-box;"></p>
        <p style="margin: 0.5rem 0;"><label for="edit_email">Email</label><br><input type="email" id="edit_email" name="email" value="<?php echo htmlspecialchars($customer['email'] ?? ''); ?>" maxlength="255" style="width: 100%; padding: 0.4rem; box-sizing: border-box;"></p>
        <p style="margin: 0.5rem 0;"><label for="edit_address">Address</label><br><textarea id="edit_address" name="address" rows="2" style="width: 100%; padding: 0.4rem; box-sizing: border-box;"><?php echo htmlspecialchars($customer['address'] ?? ''); ?></textarea></p>
        <p style="margin: 0.5rem 0;"><label for="edit_customer_type">Type</label><br><select id="edit_customer_type" name="customer_type" style="padding: 0.4rem;"><option value="individual" <?php echo ($customer['customer_type'] ?? '') === 'individual' ? 'selected' : ''; ?>>Individual</option><option value="corporate" <?php echo ($customer['customer_type'] ?? '') === 'corporate' ? 'selected' : ''; ?>>Corporate</option></select></p>
        <p style="margin: 0.5rem 0;"><button type="submit" style="padding: 0.5rem 1rem;">Update customer</button> <a href="index.php?dashboard=customer_rep">Back to list</a></p>
    </form>
</section>
<?php endif; ?>

<?php if (!empty($ticket)) : ?>
<section class="view-ticket" style="margin-top: 2rem; padding: 1rem; border: 1px solid #ccc;">
    <h2>View ticket (customer_rep/ticket_get)</h2>
    <p><strong>#<?php echo (int) $ticket['ticket_id']; ?></strong> <?php echo htmlspecialchars($ticket['ticket_no'] ?? ''); ?> — <?php echo htmlspecialchars($ticket['status'] ?? ''); ?> — <?php echo htmlspecialchars($ticket['created_at'] ?? ''); ?></p>
    <p><strong>Customer:</strong> <?php echo !empty($ticket['customer']['name']) ? htmlspecialchars($ticket['customer']['name']) . ' (#' . (int) $ticket['customer_id'] . ')' : (int) $ticket['customer_id']; ?></p>
    <p><strong>Complaint:</strong> <?php echo htmlspecialchars($ticket['complaint_description'] ?? '—'); ?></p>
    <p><strong>Bike:</strong> <?php echo htmlspecialchars($ticket['bike_model'] ?? '—'); ?> / VIN: <?php echo htmlspecialchars($ticket['bike_vin'] ?? '—'); ?> / Mileage: <?php echo htmlspecialchars($ticket['bike_mileage'] ?? '—'); ?> / Warranty: <?php echo !empty($ticket['warranty_flag']) ? 'Yes' : 'No'; ?></p>
    <p><strong>Created by:</strong> <?php echo htmlspecialchars($ticket['created_by_user']['full_name'] ?? '—'); ?></p>
    <p><a href="index.php?dashboard=customer_rep">Back to list</a></p>
</section>
<?php endif; ?>

<section class="create-customer" style="margin-top: 2rem;">
    <h2>Create customer</h2>
    <p style="color: #666;">Endpoint: POST customer_rep/customer_create</p>
    <form method="post" action="index.php?dashboard=customer_rep" style="max-width: 500px; margin-top: 0.5rem;">
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

<section class="customers" style="margin-top: 2rem;">
    <h2>Customers</h2>
    <p style="color: #666;">Endpoint: GET customer_rep/customers_list. <?php echo count($customers); ?> customer(s)</p>
    <form method="get" action="index.php" style="margin-bottom: 0.5rem;">
        <input type="hidden" name="dashboard" value="customer_rep">
        <input type="search" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="Search name, email, phone" style="padding: 0.4rem; width: 280px;">
        <button type="submit" style="padding: 0.4rem 0.75rem;">Search</button>
    </form>
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
                <th style="padding: 0.5rem; border: 1px solid #ccc;">View</th>
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
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><a href="index.php?dashboard=customer_rep&customer_id=<?php echo (int) $c['customer_id']; ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>

<section class="create-ticket" style="margin-top: 2rem;">
    <h2>Create ticket</h2>
    <p style="color: #666;">Endpoint: POST customer_rep/ticket_create</p>
    <form method="post" action="index.php?dashboard=customer_rep" style="max-width: 500px; margin-top: 0.5rem;">
        <input type="hidden" name="create_ticket" value="1">
        <p style="margin: 0.5rem 0;">
            <label for="ticket_customer_id">Customer <span style="color: #c62828;">*</span></label><br>
            <select id="ticket_customer_id" name="customer_id" required style="width: 100%; padding: 0.4rem; box-sizing: border-box;">
                <option value="">— Select customer —</option>
                <?php foreach ($customers as $c) : ?>
                <option value="<?php echo (int) $c['customer_id']; ?>"><?php echo htmlspecialchars($c['name'] ?? '') . ' (#' . (int) $c['customer_id'] . ')'; ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p style="margin: 0.5rem 0;">
            <label for="complaint_description">Complaint / description <span style="color: #c62828;">*</span></label><br>
            <textarea id="complaint_description" name="complaint_description" required rows="3" style="width: 100%; padding: 0.4rem; box-sizing: border-box;"></textarea>
        </p>
        <p style="margin: 0.5rem 0;">
            <label for="bike_model">Bike model</label><br>
            <input type="text" id="bike_model" name="bike_model" maxlength="100" style="width: 100%; padding: 0.4rem; box-sizing: border-box;">
        </p>
        <p style="margin: 0.5rem 0;">
            <label for="bike_vin">Bike VIN</label><br>
            <input type="text" id="bike_vin" name="bike_vin" maxlength="50" style="width: 100%; padding: 0.4rem; box-sizing: border-box;">
        </p>
        <p style="margin: 0.5rem 0;">
            <label for="bike_mileage">Bike mileage</label><br>
            <input type="number" id="bike_mileage" name="bike_mileage" min="0" style="width: 100%; padding: 0.4rem; box-sizing: border-box;">
        </p>
        <p style="margin: 0.5rem 0;">
            <label><input type="checkbox" name="warranty_flag" value="1"> Warranty</label>
        </p>
        <p style="margin: 0.5rem 0;">
            <button type="submit" style="padding: 0.5rem 1rem;">Create ticket</button>
        </p>
    </form>
</section>

<section class="tickets" style="margin-top: 2rem;">
    <h2>Tickets</h2>
    <p style="color: #666;">Endpoint: GET customer_rep/tickets_list. <?php echo count($tickets); ?> ticket(s)</p>
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
                <th style="padding: 0.5rem; border: 1px solid #ccc;">View</th>
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
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><a href="index.php?dashboard=customer_rep&ticket_id=<?php echo (int) $t['ticket_id']; ?>">View</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>
