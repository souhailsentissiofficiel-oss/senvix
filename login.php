<?php
require_once 'config.php';

// ── Login handler ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$email    = trim($_POST['email']    ?? '');
$password =       $_POST['password'] ?? '';

// ── Basic validation ───────────────────────────────────────────
if (empty($email) || empty($password)) {
    $_SESSION['error'] = 'Please fill in all fields.';
    header('Location: index.php');
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Invalid email format.';
    header('Location: index.php');
    exit;
}

// ── Fetch user by email (prepared statement — SQL injection safe) ──
$stmt = $pdo->prepare("SELECT id, email, password FROM users WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// ── Verify password (timing-safe via password_verify) ───────────
if (!$user || !password_verify($password, $user['password'])) {
    // Generic message to prevent user enumeration
    $_SESSION['error'] = 'Incorrect email or password.';
    header('Location: index.php');
    exit;
}

// ── Rehash if needed (future-proof) ────────────────────────────
if (password_needs_rehash($user['password'], PASSWORD_BCRYPT, ['cost' => 12])) {
    $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$newHash, $user['id']]);
}

// ── Regenerate session ID to prevent fixation ──────────────────
session_regenerate_id(true);

// ── Store session ──────────────────────────────────────────────
$_SESSION['user_id']    = $user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['logged_in']  = true;
$_SESSION['login_time'] = time();

// ── Update last login timestamp ────────────────────────────────
$pdo->prepare("UPDATE users SET last_login = NOW() WHERE id = ?")->execute([$user['id']]);

// ── Redirect to dashboard ──────────────────────────────────────
header('Location: dashboard.php');
exit;