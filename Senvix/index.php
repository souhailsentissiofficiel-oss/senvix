<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Senvix — Sign In</title>
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

  <!-- Login Page -->
  <div class="page">
    <div class="glass-card">
      <div class="glow-border"></div>

      <!-- Brand -->
      <div class="brand">
        <div class="brand-icon">
          <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
        </div>
        <h1>Senvix</h1>
        <p>Protected access · End-to-end encrypted</p>
      </div>

      <!-- PHP alert output -->
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
      </div>
      <?php endif; ?>

      <!-- Login Form -->
      <form id="loginForm" action="login.php" method="POST" novalidate>

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
                   placeholder="••••••••"
                   autocomplete="current-password">
            <button type="button" class="toggle-pw" data-target="password" aria-label="Toggle password">
              <svg class="eye-open" viewBox="0 0 24 24" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg class="eye-close" viewBox="0 0 24 24" stroke-linecap="round" style="display:none">
                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
              <line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>
        </div>

        <button type="submit" class="btn-primary">
          <span class="btn-text">Sign In</span>
          <div class="btn-loader"></div>
        </button>
      </form>

      <div class="divider">or</div>

      <div class="auth-link">
        Don't have an account?
        <a href="register.php" data-transition>Create Account &rarr;</a>
      </div>
    </div>
  </div>

  <script src="script.js"></script>
</body>
</html>