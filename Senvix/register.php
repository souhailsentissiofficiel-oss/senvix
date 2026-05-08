<?php
require_once 'config.php';

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST['email']   ?? '');
    $password =       $_POST['password']        ?? '';
    $confirm  =       $_POST['confirm_password'] ?? '';

    // ── Server-side validation ──
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif (empty($_POST['terms'])) {
        $error = 'You must accept the Terms of Service.';
    } else {
        // Check if email already exists (prepared statement)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = 'This email is already registered. Please sign in.';
        } else {
            // Hash password securely
            $hashed = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

            // Insert new user (prepared statement)
            $insert = $pdo->prepare("INSERT INTO users (email, password, created_at) VALUES (?, ?, NOW())");
            if ($insert->execute([$email, $hashed])) {
                $success = 'Account created successfully! You can now sign in.';
            } else {
                $error = 'Something went wrong. Please try again.';
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
  <title>Senvix — Create Account</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <!-- Animated Background -->
  <div class="bg-canvas"></div>
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="orb orb-3"></div>
  <div class="grid-lines"></div>
  <div class="code-rain"></div>

  <!-- Register Page -->
  <div class="page">
    <div class="glass-card" style="max-width:460px;">
      <div class="glow-border"></div>

      <!-- Brand -->
      <div class="brand">
        <div class="brand-icon">
          <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
        </div>
        <h1>Create Account</h1>
        <p>Join Senvix · Free forever</p>
      </div>

      <!-- Alerts -->
      <?php if (!empty($error)): ?>
      <div class="alert alert-error">
        <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        <?= htmlspecialchars($error) ?>
      </div>
      <?php endif; ?>

      <?php if (!empty($success)): ?>
      <div class="alert alert-success">
        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
        <?= htmlspecialchars($success) ?>
        <a href="index.php" style="color:var(--green-light);margin-left:8px;font-weight:600;">Sign In</a>
      </div>
      <?php endif; ?>

      <!-- Registration Form -->
      <?php if (empty($success)): ?>
      <form id="registerForm" action="register.php" method="POST" novalidate>

        <div class="form-group">
          <label for="email">Email Address</label>
          <div class="input-wrap">
            <svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
            <input type="email" id="email" name="email"
                   placeholder="you@example.com"
                   autocomplete="email"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
          </div>
        </div>

        <div class="form-group">
          <label for="password">Password</label>
          <div class="input-wrap">
            <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            <input type="password" id="password" name="password"
                   placeholder="Min. 8 characters"
                   autocomplete="new-password"
                   data-strength="true">
            <button type="button" class="toggle-pw" data-target="password" aria-label="Toggle password">
              <svg class="eye-open" viewBox="0 0 24 24" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg class="eye-close" viewBox="0 0 24 24" stroke-linecap="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
          <!-- Password strength meter -->
          <div class="pw-strength">
            <div class="pw-bars">
              <div class="pw-bar"></div>
              <div class="pw-bar"></div>
              <div class="pw-bar"></div>
              <div class="pw-bar"></div>
            </div>
            <span class="pw-label">Strength: —</span>
          </div>
        </div>

        <div class="form-group">
          <label for="confirm_password">Confirm Password</label>
          <div class="input-wrap">
            <svg viewBox="0 0 24 24"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            <input type="password" id="confirm_password" name="confirm_password"
                   placeholder="Repeat password"
                   autocomplete="new-password">
            <button type="button" class="toggle-pw" data-target="confirm_password" aria-label="Toggle confirm">
              <svg class="eye-open" viewBox="0 0 24 24" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg class="eye-close" viewBox="0 0 24 24" stroke-linecap="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
        </div>

        <!-- Terms -->
        <div class="check-row">
          <input type="checkbox" id="terms" name="terms" value="1"
                 <?= !empty($_POST['terms']) ? 'checked' : '' ?>>
          <label for="terms">I agree to the <a href="#" style="color:var(--green-glow)">Terms of Service</a> &amp; <a href="#" style="color:var(--green-glow)">Privacy Policy</a></label>
        </div>

        <button type="submit" class="btn-primary">
          <span class="btn-text">Create Account</span>
          <div class="btn-loader"></div>
        </button>
      </form>
      <?php endif; ?>

      <div class="divider">or</div>

      <div class="auth-link">
        Already have an account?
        <a href="index.php" data-transition>Sign In &rarr;</a>
      </div>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>