<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'SGV-AI'; ?></title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 1rem; }
        nav { margin-bottom: 1rem; }
        nav a { margin-right: 1rem; }
    </style>
</head>
<body>
    <nav>
        <a href="<?php echo isset($base) && $base !== '' ? $base . '/' : ''; ?>index.php?dashboard=<?php echo rawurlencode($_SESSION['role_name'] ?? ''); ?>">Home</a>
        <a href="<?php echo isset($base) && $base !== '' ? $base . '/' : ''; ?>logout.php">Logout</a>
        <span> (<?php echo htmlspecialchars($_SESSION['role_name'] ?? ''); ?>)</span>
    </nav>

