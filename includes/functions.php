<?php
/**
 * PC POA - Shared helper functions
 */

/**
 * SECURITY: Harden session cookie settings BEFORE the session starts.
 * - HttpOnly: JavaScript cannot read the session cookie (mitigates XSS cookie theft).
 * - SameSite=Lax: cookie is not sent on most cross-site requests (mitigates CSRF).
 * - Secure: only set when the site is actually served over HTTPS, so this does
 *   NOT break local XAMPP/HTTP development - it activates automatically once
 *   you deploy behind HTTPS.
 * This does not change any page behaviour, design, or existing session data.
 */
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'domain'   => '',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

require_once __DIR__ . '/../config/database.php';

/**
 * SECURITY: send protective HTTP headers on every page, tuned to still allow
 * the exact CDN assets (Bootstrap, Font Awesome, Google Fonts) and the
 * Google Maps embed on the Contact page that the site already uses - so
 * nothing currently on the site breaks. Skipped automatically if headers
 * were already sent (e.g. during CLI/testing) so it never causes a fatal error.
 */
function send_security_headers(): void
{
    if (headers_sent()) {
        return;
    }

    $csp = implode('; ', [
        "default-src 'self'",
        "script-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://code.jquery.com",
        "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com",
        "font-src 'self' https://fonts.gstatic.com https://cdnjs.cloudflare.com",
        "img-src 'self' data: https:",
        "media-src 'self'",
        "frame-src https://www.google.com",
        "connect-src 'self'",
        "object-src 'none'",
        "base-uri 'self'",
        "form-action 'self'",
    ]);

    header("Content-Security-Policy: $csp");
    header('X-Content-Type-Options: nosniff');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('X-Frame-Options: SAMEORIGIN');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

    // Only sent once HTTPS is actually in use, so local XAMPP/HTTP testing is unaffected.
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    if ($isHttps) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
    }
}
send_security_headers();

/* =========================================================
   CSRF PROTECTION
   Every POST form on the site includes a hidden token via
   csrf_field(); every POST handler calls csrf_verify() before
   acting on the submitted data. This does not change what a
   form looks like or how it behaves for a real visitor - it
   only rejects submissions that didn't originate from our own
   pages (the classic CSRF attack).
   ========================================================= */

function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/**
 * Call this at the top of every POST handler, right after the
 * REQUEST_METHOD === 'POST' check. Stops processing with a safe,
 * on-brand error message if the token is missing or wrong.
 */
function csrf_verify(): void
{
    $submitted = $_POST['csrf_token'] ?? '';
    $expected = $_SESSION['csrf_token'] ?? '';

    if ($submitted === '' || $expected === '' || !hash_equals($expected, $submitted)) {
        http_response_code(403);
        die('<div style="font-family:sans-serif;max-width:480px;margin:80px auto;text-align:center;">'
            . '<h2 style="color:#0a3d1f;">Security check failed</h2>'
            . '<p>Your session may have expired or this form was submitted from an untrusted source. '
            . 'Please go back, refresh the page, and try again.</p>'
            . '<a href="' . BASE_URL . 'index.php" style="color:#0e7a34;">Return to PC Poa</a>'
            . '</div>');
    }
}

/**
 * Load site-wide settings (logo, social links, contact info, location).
 * These are managed ONLY from the admin panel (admin/settings.php).
 */
function get_settings(PDO $pdo): array
{
    static $settings = null;
    if ($settings === null) {
        $stmt = $pdo->query("SELECT * FROM settings WHERE id = 1 LIMIT 1");
        $settings = $stmt->fetch() ?: [];
    }
    return $settings;
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function money(float $amount): string
{
    return number_format($amount, 0) . ' TZS';
}

function redirect(string $path): void
{
    header('Location: ' . BASE_URL . ltrim($path, '/'));
    exit;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user_id']);
}

function is_admin(): bool
{
    return is_logged_in() && ($_SESSION['role'] ?? '') === 'admin';
}

function require_login(): void
{
    if (!is_logged_in()) {
        redirect('login.php');
    }
}

function require_admin(): void
{
    if (!is_admin()) {
        redirect('../login.php');
    }
}

/**
 * Handle an uploaded image file. Returns the relative path on success, or null.
 *
 * SECURITY: validates the REAL file content (not just the filename extension)
 * using getimagesize(), which only succeeds for genuine image data. This
 * blocks disguised/malicious files (e.g. a .php file renamed to .jpg) from
 * ever being accepted, on top of the extension whitelist below.
 */
function handle_image_upload(array $file, string $destFolder, ?string $oldFile = null): ?string
{
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // SECURITY: hard cap on upload size (5 MB) to prevent disk-filling / DoS uploads.
    $maxBytes = 5 * 1024 * 1024;
    if ($file['size'] > $maxBytes) {
        return null;
    }

    $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    // SECURITY: confirm the uploaded file is actually a valid image by asking
    // PHP's image library to read its dimensions - a renamed .php/.exe file
    // will fail this check and get rejected, even though its extension is ".jpg".
    $imageInfo = @getimagesize($file['tmp_name']);
    if ($imageInfo === false) {
        return null;
    }
    $allowedMime = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
    if (!in_array($imageInfo['mime'], $allowedMime, true)) {
        return null;
    }

    if (!is_dir($destFolder)) {
        mkdir($destFolder, 0755, true);
    }
    ensure_uploads_are_not_executable($destFolder);

    $filename = uniqid('img_', true) . '.' . $ext;
    $target = rtrim($destFolder, '/') . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        // Remove old file if it exists and belongs to uploads folder
        if ($oldFile && strpos($oldFile, 'uploads/') === 0 && file_exists(__DIR__ . '/../' . $oldFile)) {
            @unlink(__DIR__ . '/../' . $oldFile);
        }
        return str_replace(__DIR__ . '/../', '', $target);
    }

    return null;
}

/**
 * Handle an uploaded video file. Returns the relative path on success, or null.
 *
 * SECURITY: validates the real file content via finfo (the file's actual
 * MIME type), not just its extension, and enforces a size cap.
 */
function handle_video_upload(array $file, string $destFolder): ?string
{
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    // SECURITY: hard cap on upload size (100 MB) to prevent disk-filling / DoS uploads.
    $maxBytes = 100 * 1024 * 1024;
    if ($file['size'] > $maxBytes) {
        return null;
    }

    $allowed = ['mp4', 'webm', 'ogg', 'mov'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, $allowed, true)) {
        return null;
    }

    // SECURITY: check the real MIME type via the file's binary content
    // (fileinfo/magic bytes), not the filename - blocks a renamed script.
    if (function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $realMime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        $allowedMime = ['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime', 'application/octet-stream'];
        // NOTE: some browsers report .mov/.webm as application/octet-stream;
        // combined with the extension whitelist above this remains safe.
        if (!in_array($realMime, $allowedMime, true)) {
            return null;
        }
    }

    if (!is_dir($destFolder)) {
        mkdir($destFolder, 0755, true);
    }
    ensure_uploads_are_not_executable($destFolder);

    $filename = uniqid('vid_', true) . '.' . $ext;
    $target = rtrim($destFolder, '/') . '/' . $filename;

    if (move_uploaded_file($file['tmp_name'], $target)) {
        return str_replace(__DIR__ . '/../', '', $target);
    }

    return null;
}

/**
 * SECURITY (defense in depth): drop an .htaccess into the given uploads
 * folder that disables script execution, so that even if a malicious file
 * somehow ended up there, the web server would never execute it as PHP.
 * This has no effect on serving images/videos normally (only blocks
 * execution of scripts), so existing uploads keep working exactly as before.
 * No-op if the file already exists or on hosts that ignore .htaccess
 * (e.g. Nginx) - in that case, configure the equivalent rule at the server level.
 */
function ensure_uploads_are_not_executable(string $folder): void
{
    $htaccess = rtrim($folder, '/') . '/.htaccess';
    if (!file_exists($htaccess)) {
        $rules = "# SECURITY: this folder only ever holds admin-uploaded images/videos.\n"
            . "# Disable script execution here so an uploaded file can never run as code.\n"
            . "<IfModule mod_php7.c>\n    php_flag engine off\n</IfModule>\n"
            . "<IfModule mod_php8.c>\n    php_flag engine off\n</IfModule>\n"
            . "AddHandler cgi-script .php .php3 .php4 .php5 .php7 .phtml .pl .py .jsp .asp .sh .cgi\n"
            . "Options -ExecCGI\n";
        @file_put_contents($htaccess, $rules);
    }
}

/* =========================================================
   SHOPPING CART (session based)
   ========================================================= */

function cart_add(int $productId, int $qty = 1): void
{
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $qty;
    } else {
        $_SESSION['cart'][$productId] = $qty;
    }
}

function cart_remove(int $productId): void
{
    unset($_SESSION['cart'][$productId]);
}

function cart_update(int $productId, int $qty): void
{
    if ($qty <= 0) {
        cart_remove($productId);
    } else {
        $_SESSION['cart'][$productId] = $qty;
    }
}

function cart_count(): int
{
    return isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
}

function cart_items(PDO $pdo): array
{
    if (empty($_SESSION['cart'])) {
        return [];
    }
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    // SECURITY/BUSINESS LOGIC: only pull products that are still active, so a
    // product an admin has hidden after it was added to a cart can't be checked out.
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders) AND status = 'active'");
    $stmt->execute($ids);
    $products = $stmt->fetchAll();

    $items = [];
    foreach ($products as $p) {
        $qty = $_SESSION['cart'][$p['id']] ?? 1;
        $items[] = [
            'product'  => $p,
            'quantity' => $qty,
            'subtotal' => $qty * $p['price'],
        ];
    }
    return $items;
}

function cart_total(PDO $pdo): float
{
    $total = 0;
    foreach (cart_items($pdo) as $item) {
        $total += $item['subtotal'];
    }
    return $total;
}

function cart_clear(): void
{
    unset($_SESSION['cart']);
}
