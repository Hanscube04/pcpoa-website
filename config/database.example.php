<?php
/**
 * PC POA - Database connection (PDO / MySQL)
 * Update these credentials to match your XAMPP / hosting environment.
 */

/**
 * SECURITY: environment flag controlling how much detail is shown on error.
 * Set to false before deploying to a live/production server. While true
 * (local development on XAMPP), PHP errors are shown on screen to help you
 * debug; once false, errors are hidden from visitors and only written to
 * the PHP error log instead - so no database structure, file paths, or
 * queries ever leak to the public.
 */
define('APP_DEBUG', true); // <-- set to false in production

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    // Optional: point PHP's error log somewhere writable on your server.
    // ini_set('error_log', __DIR__ . '/../logs/php-error.log');
}

define('DB_HOST', 'localhost');
define('DB_NAME', 'pcpoa_db');
define('DB_USER', 'pcpoa_app');
define('DB_PASS', 'Admni@12');

// Base URL of the site - update when deploying to a live domain
define('BASE_URL', '/PCPOA/');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // SECURITY: never show raw database error details (hostname, schema,
    // driver errors) to a site visitor - that's exactly what an attacker
    // probing for information wants to see. Log the real message instead,
    // and show only a generic, on-brand message on screen.
    error_log('PC Poa DB connection error: ' . $e->getMessage());
    if (APP_DEBUG) {
        die('Database connection failed: ' . htmlspecialchars($e->getMessage()));
    }
    http_response_code(500);
    die('<div style="font-family:sans-serif;max-width:480px;margin:80px auto;text-align:center;">'
        . '<h2 style="color:#0a3d1f;">We\'re having a technical issue</h2>'
        . '<p>PC Poa is temporarily unavailable. Please try again shortly, or contact us directly.</p>'
        . '</div>');
}

