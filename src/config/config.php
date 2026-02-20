<?php
/**
 * Load .env and create one database connection ($pdo).
 * Include this once at the top of any page that needs the database.
 */

$envFile = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . '.env';
if (is_file($envFile)) {
    $lines = @file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines) {
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || strpos($line, '#') === 0) {
                continue;
            }
            $parts = explode('=', $line, 2);
            if (count($parts) >= 2) {
                $name = trim($parts[0]);
                $value = trim($parts[1]);
                if ($name !== '') {
                    $_ENV[$name] = $value;
                }
            }
        }
    }
}

$dbHost = isset($_ENV['DB_HOST']) ? $_ENV['DB_HOST'] : '127.0.0.1';
$dbName = isset($_ENV['DB_NAME']) ? $_ENV['DB_NAME'] : 'sgv';
$dbUser = isset($_ENV['DB_USER']) ? $_ENV['DB_USER'] : 'root';
$dbPass = isset($_ENV['DB_PASS']) ? $_ENV['DB_PASS'] : '';
$dbCharset = isset($_ENV['DB_CHARSET']) ? $_ENV['DB_CHARSET'] : 'utf8mb4';

$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=$dbCharset";
$pdo = new PDO($dsn, $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

