<h1>Customer Portal</h1>
<p>Create a service ticket or view your existing tickets.</p>
<?php require __DIR__ . '/../../components/user_details.php'; ?>

<?php
$flash_success = $_SESSION['flash_success'] ?? null;
$flash_error = $_SESSION['flash_error'] ?? null;
if (isset($_SESSION['flash_success'])) unset($_SESSION['flash_success']);
if (isset($_SESSION['flash_error'])) unset($_SESSION['flash_error']);
if ($flash_success) echo '<p style="color: green; padding: 0.5rem; background: #e8f5e9;">' . htmlspecialchars($flash_success) . '</p>';
if ($flash_error) echo '<p style="color: #c62828; padding: 0.5rem; background: #ffebee;">' . htmlspecialchars($flash_error) . '</p>';
?>

<?php if (!empty($customer)) : ?>
<section class="my-details" style="margin-top: 2rem;">
    <h2>My details</h2>
    <p><strong><?php echo htmlspecialchars($customer['name'] ?? ''); ?></strong> — <?php echo htmlspecialchars($customer['email'] ?? '—'); ?> — <?php echo htmlspecialchars($customer['phone'] ?? '—'); ?></p>
</section>
<?php endif; ?>

<section class="create-ticket" style="margin-top: 2rem;">
    <h2>Create a ticket</h2>
    <p style="color: #666;">Describe your issue. Your account is linked to your customer record.</p>
    <form method="post" action="index.php?dashboard=customer" style="max-width: 500px; margin-top: 0.5rem;">
        <input type="hidden" name="create_ticket" value="1">
        <p style="margin: 0.5rem 0;">
            <label for="complaint_description">Complaint / description <span style="color: #c62828;">*</span></label><br>
            <textarea id="complaint_description" name="complaint_description" required rows="3" style="width: 100%; padding: 0.4rem; box-sizing: border-box;" placeholder="Describe the issue..."></textarea>
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
            <label><input type="checkbox" name="warranty_flag" value="1"> Under warranty</label>
        </p>
        <p style="margin: 0.5rem 0;">
            <button type="submit" style="padding: 0.5rem 1rem;">Submit ticket</button>
        </p>
    </form>
</section>

<section class="my-tickets" style="margin-top: 2rem;">
    <h2>My tickets</h2>
    <p style="color: #666;"><?php echo count($tickets); ?> ticket(s)</p>
    <?php if (empty($tickets)) : ?>
        <p>No tickets yet.</p>
    <?php else : ?>
    <table style="border-collapse: collapse; width: 100%; max-width: 800px; margin-top: 0.5rem;">
        <thead>
            <tr style="background: #eee; text-align: left;">
                <th style="padding: 0.5rem; border: 1px solid #ccc;">Ticket #</th>
                <th style="padding: 0.5rem; border: 1px solid #ccc;">Status</th>
                <th style="padding: 0.5rem; border: 1px solid #ccc;">Created</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($tickets as $t) : ?>
            <tr>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo htmlspecialchars($t['ticket_no'] ?? ''); ?></td>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo htmlspecialchars($t['status'] ?? '—'); ?></td>
                <td style="padding: 0.5rem; border: 1px solid #ccc;"><?php echo htmlspecialchars($t['created_at'] ?? '—'); ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</section>
