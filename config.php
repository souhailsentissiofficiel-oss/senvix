<?php
// ============================================================
//  config.php — Fixed for InfinityFree hosting
//
//  BUGS FIXED (compared to your XAMPP version):
//  1. DB_HOST  was 'localhost'    → must be 'sql308.infinityfree.com'
//  2. DB_NAME  was 'securevault' → must be 'if0_41755743_securevault'
//  3. DB_USER  was 'root'        → must be 'if0_41755743'
//  4. DB_PASS  was 'mamababa'    → must be your InfinityFree MySQL password
//
//  HOW TO FIND THESE VALUES:
//  InfinityFree panel → MySQL Databases → Connection Details
// ============================================================

// ── Set DEBUG = true to see the real MySQL error in browser ──
// !! Switch back to false before sharing your URL publicly !!
define('DB_DEBUG', false);

// ── Your InfinityFree MySQL credentials ──────────────────────
define('DB_HOST',    'mysql.railway.internal');   // from your panel screenshot
define('DB_NAME',    'senvix');  // PREFIX_databasename format
define('DB_USER',    'root');              // your MySQL username
define('DB_PASS',    'IJFrOjcYaWiCJFOKjtqEnVgnzVpyGZeu');              // your MySQL password (from panel)
define('DB_CHARSET', 'utf8mb4');
define('DB_PORT',    3306);

// ── InfinityFree: disable MySQLi strict reporting ────────────
// Required on InfinityFree — without this, some PHP versions
// throw misleading fatal errors before PDO even connects.
if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_OFF);
}

// ── Secure session settings ──────────────────────────────────
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure',   0);   // set to 1 if your site has HTTPS
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.gc_maxlifetime',  3600);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ── PDO Connection ────────────────────────────────────────────
// InfinityFree REQUIRES the port to be specified explicitly in the DSN.
// Omitting it causes silent connection failures on their shared servers.
try {
    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_CHARSET
    );

    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_TIMEOUT            => 10,   // 10-second connect timeout
    ]);

} catch (PDOException $e) {
    // Log the real error server-side (visible in InfinityFree error logs)
    error_log('[SecureVault] DB Error: ' . $e->getMessage());

    if (DB_DEBUG) {
        // DEBUG MODE: show real error in browser — turn off before going live!
        $msg = 'DB Error [DEBUG]: ' . htmlspecialchars($e->getMessage());
    } else {
        $msg = 'Database connection failed. Please contact support.';
    }

    // Return JSON if called via AJAX, plain text otherwise
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
           || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');

    if ($isAjax || str_contains($_SERVER['SCRIPT_NAME'], 'vault-api')) {
        header('Content-Type: application/json');
        die(json_encode(['error' => $msg]));
    }
    die('<p style="font-family:sans-serif;color:red;padding:20px">' . $msg . '</p>');
}

// ── Pull flash messages ───────────────────────────────────────
$error   = $_SESSION['error']   ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);