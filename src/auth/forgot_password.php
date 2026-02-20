<?php
/**
 * Forgot password: user submits email/username; we show a message that admin will assist.
 * Expects config + auth already loaded by the caller.
 */
$forgot_success = false;
$forgot_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    if ($email === '') {
        $forgot_error = 'Please enter your email address.';
    } else {
        $forgot_success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password – SGV-AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body class="min-h-screen flex">
    <div class="flex min-h-screen w-full flex-col md:flex-row">
        <div class="login-left relative flex w-full flex-col overflow-hidden p-8 text-white md:w-[50%] md:px-[8rem] md:pt-[8rem]">
            <div class="relative z-10 flex flex-1 flex-col items-start justify-center text-left">
                <h1 class="text-2xl font-bold leading-tight md:text-3xl">Reset your password</h1>
                <p class="mt-4 max-w-xs text-left text-sm leading-relaxed opacity-90">Enter the email for your account and we’ll have an administrator assist you with a password reset.</p>
                <p class="mt-auto pt-8 text-left text-xs opacity-80">© <?php echo date('Y'); ?> SGV-AI. All rights reserved.</p>
            </div>
        </div>
        <div class="flex w-full flex-col justify-center bg-white p-8 shadow-[_-4px_0_24px_rgba(0,0,0,0.06)] md:w-[50%] md:p-[10rem]">
            <img src="assets/logo.png" alt="SGV-AI" class="h-16 w-auto object-contain object-left md:h-12 mb-8">
            <h2 class="text-xl font-semibold text-gray-900">Forgot password?</h2>
            <p class="mt-2 text-sm text-gray-500">Enter your account email below.</p>

            <?php if ($forgot_success) { ?>
                <div class="mt-6 rounded-lg bg-green-50 p-4 text-sm text-green-800">
                    Request received. An administrator will contact you to reset your password.
                </div>
                <p class="mt-6 text-center"><a href="index.php" class="login-link">Back to login</a></p>
            <?php } else { ?>
                <?php if ($forgot_error !== '') { echo '<p class="mt-4 text-sm text-red-600">' . htmlspecialchars($forgot_error) . '</p>'; } ?>
                <form method="post" action="forgot-password.php" class="mt-6 flex flex-col">
                    <div class="mb-6">
                        <input type="email" name="email" placeholder="Email address" required
                            class="login-input w-full py-2 text-base focus:outline-none focus:ring-0" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                    <button type="submit" class="login-btn-primary w-full rounded-lg py-3.5 px-5 font-semibold text-white text-base">
                        Submit request
                    </button>
                </form>
                <p class="mt-6 text-sm text-gray-500 text-center"><a href="index.php" class="login-link">Back to login</a></p>
            <?php } ?>
        </div>
    </div>
</body>
</html>
