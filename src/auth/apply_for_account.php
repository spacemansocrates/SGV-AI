<?php
/**
 * Apply for account: user submits details; stored as pending for admin to approve.
 * Expects config already loaded by the caller. No auth required.
 */
$apply_success = false;
$apply_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $desired_username = isset($_POST['desired_username']) ? trim($_POST['desired_username']) : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if ($full_name === '' || $email === '' || $desired_username === '') {
        $apply_error = 'Please fill in full name, email, and desired username.';
    } else {
        global $pdo;
        try {
            $stmt = $pdo->prepare('INSERT INTO account_requests (full_name, email, desired_username, message, status) VALUES (?, ?, ?, ?, ?)');
            $stmt->execute([$full_name, $email, $desired_username, $message ?: null, 'pending']);
            $apply_success = true;
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                $apply_error = 'This email or username may already be requested or in use. Try another.';
            } else {
                $apply_error = 'Something went wrong. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply for Account – SGV-AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body class="min-h-screen flex">
    <div class="flex min-h-screen w-full flex-col md:flex-row">
        <div class="login-left relative flex w-full flex-col overflow-hidden p-8 text-white md:w-[50%] md:px-[8rem] md:pt-[8rem]">
            <div class="relative z-10 flex flex-1 flex-col items-start justify-center text-left">
                <h1 class="text-2xl font-bold leading-tight md:text-3xl">Apply for an account</h1>
                <p class="mt-4 max-w-xs text-left text-sm leading-relaxed opacity-90">Request access to SGV-AI. An administrator will review your application and create your account when approved.</p>
                <p class="mt-auto pt-8 text-left text-xs opacity-80">© <?php echo date('Y'); ?> SGV-AI. All rights reserved.</p>
            </div>
        </div>
        <div class="flex w-full flex-col justify-center bg-white px-8 shadow-[_-4px_0_24px_rgba(0,0,0,0.06)] md:w-[50%] md:px-[10rem] md:py-[4rem]">
            <img src="assets/logo.png" alt="SGV-AI" class="h-16 w-auto object-contain object-left md:h-12 mb-8">
            <h2 class="text-xl font-semibold text-gray-900">Request account access</h2>
            <p class="mt-2 text-sm text-gray-500">Fill in the form below. An admin will approve your account.</p>

            <?php if ($apply_success) { ?>
                <div class="mt-6 rounded-lg bg-green-50 p-4 text-sm text-green-800">
                    Application received. You will be notified when an administrator approves your account.
                </div>
                <p class="mt-6 text-center"><a href="index.php" class="login-link">Back to login</a></p>
            <?php } else { ?>
                <?php if ($apply_error !== '') { echo '<p class="mt-4 text-sm text-red-600">' . htmlspecialchars($apply_error) . '</p>'; } ?>
                <form method="post" action="apply-for-account.php" class="mt-6 flex flex-col">
                    <div class="mb-6">
                        <input type="text" name="full_name" placeholder="Full name" required
                            class="login-input w-full py-2 text-base focus:outline-none focus:ring-0" value="<?php echo htmlspecialchars($_POST['full_name'] ?? ''); ?>">
                    </div>
                    <div class="mb-6">
                        <input type="email" name="email" placeholder="Email address" required
                            class="login-input w-full py-2 text-base focus:outline-none focus:ring-0" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    <div class="mb-6">
                        <input type="text" name="desired_username" placeholder="Desired username" required
                            class="login-input w-full py-2 text-base focus:outline-none focus:ring-0" value="<?php echo htmlspecialchars($_POST['desired_username'] ?? ''); ?>">
                    </div>
                    <div class="mb-6">
                        <textarea name="message" placeholder="Why do you need access? (optional)" rows="3"
                            class="login-input w-full resize-none py-2 text-base focus:outline-none focus:ring-0"><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="login-btn-primary w-full rounded-lg py-3.5 px-5 font-semibold text-white text-base">
                        Submit application
                    </button>
                </form>
                <p class="mt-6 text-sm text-gray-500 text-center"><a href="index.php" class="login-link">Back to login</a></p>
            <?php } ?>
        </div>
    </div>
</body>
</html>
