<?php
/**
 * Web login: show form or handle POST. Include from public/index.php when not logged in.
 * Expects $pdo and auth functions (config + auth.php already loaded by index.php).
 */ 
$login_error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    if ($username === '' || $password === '') {
        $login_error = 'Please enter username and password.';
    } elseif (auth_login($username, $password)) {
        header('Location: index.php?dashboard=' . rawurlencode($_SESSION['role_name']));
        exit;
    } else {
        $login_error = 'Invalid username or password.';
    }
}

if (auth_check()) {
    header('Location: index.php?dashboard=' . rawurlencode($_SESSION['role_name']));
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – SGV-AI</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body class="min-h-screen flex">
    <div class="flex min-h-screen w-full flex-col md:flex-row">
        <!-- Left panel -->
        <div class="login-left relative flex w-full flex-col items-start justify-center overflow-hidden p-8 text-white md:w-[50%] md:px-[8rem] md:pt-[8rem]">
            <div class="relative z-10 flex flex-1 flex-col items-start justify-center text-left">
                <!-- logo -->
                <!-- <img src="assets/logo.png" alt="SGV-AI" class="h-16 w-auto object-contain object-left md:h-12 mb-8"> -->
                <h1 class="text-2xl font-bold leading-tight md:text-3xl">Hello SGV-AI!</h1>
                <p class="mt-4 max-w-xs text-left text-sm leading-relaxed opacity-90 md:text-[0.9375rem]">Skip repetitive and manual tasks. Get more done through automation and save time every day.</p>
                <p class="mt-auto pt-8 text-left text-xs opacity-80">© <?php echo date('Y'); ?> SGV-AI. All rights reserved.</p>
            </div>
        </div>
        <!-- Right panel -->
        <div class="flex w-full flex-col bg-white p-8 shadow-[_-4px_0_24px_rgba(0,0,0,0.06)] md:w-[50%] md:p-[10rem]">
            <!-- logo -->
                <img src="assets/logo.png" alt="SGV-AI" class="h-16 w-auto object-contain object-left md:h-12 mb-8">
            <p class="mt-1 text-gray-700 text-lg">Welcome Back!</p>
            <p class="mt-4 mb-8 text-sm text-gray-500 leading-relaxed">
                Don't have an account? <a href="apply-for-account.php" class="login-link">Apply for an account</a>; an admin will approve your request.
            </p>
            <?php if ($login_error !== '') { echo '<p class="mb-4 text-sm text-red-600">' . htmlspecialchars($login_error) . '</p>'; } ?>
            <form method="post" action="index.php" class="flex flex-col">
                <div class="mb-6">
                    <input type="text" name="username" placeholder="Username" required autocomplete="username"
                        class="login-input w-full py-2 text-base focus:outline-none focus:ring-0">
                </div>
                <div class="mb-6">
                    <input type="password" name="password" placeholder="Password" required autocomplete="current-password"
                        class="login-input w-full py-2 text-base focus:outline-none focus:ring-0">
                </div>
                <button type="submit" class="login-btn-primary w-full rounded-lg py-3.5 px-5 font-semibold text-white text-base">
                    Login Now
                </button>
            </form>
            <p class="mt-6 text-sm text-gray-500 text-center">Forgot password? <a href="forgot-password.php" class="login-link">Click here</a></p>
        </div>
    </div>
</body>
</html>
