<?php
require_once 'config.php';

// ── Auth guard ────────────────────────────────────────────────
if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// ── Handle logout ─────────────────────────────────────────────
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header('Location: index.php');
    exit;
}

// ── Fetch user info ───────────────────────────────────────────
$stmt = $pdo->prepare("SELECT id, email, created_at, last_login FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) { session_destroy(); header('Location: index.php'); exit; }

// ── Derived display values ────────────────────────────────────
$email      = htmlspecialchars($user['email']);
$avatar     = strtoupper(substr($user['email'], 0, 1));
$joinDate   = date('M j, Y', strtotime($user['created_at']));
$lastLogin  = $user['last_login'] ? date('M j, Y · H:i', strtotime($user['last_login'])) : 'First login';
$sessionAge = round((time() - $_SESSION['login_time']) / 60);
$emailMask  = substr($email, 0, 3) . '****' . strstr($email, '@');
$uid        = (int) $user['id'];

// ── Saved passwords count ─────────────────────────────────────
$cntStmt = $pdo->prepare("SELECT COUNT(*) FROM passwords WHERE user_id = ?");
$cntStmt->execute([$uid]);
$pwCount = (int) $cntStmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Senvix — Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="dashboard.css">
</head>
<body>

  <!-- Animated Background (unchanged) -->
  <div class="bg-canvas"></div>
  <div class="orb orb-1"></div>
  <div class="orb orb-2"></div>
  <div class="orb orb-3"></div>
  <div class="grid-lines"></div>
  <div class="code-rain"></div>

  <!-- Navigation (unchanged) -->
  <nav class="nav-bar">
    <span class="nav-brand">&#128274; Senvix</span>
    <div class="nav-right">
      <div class="user-badge">
        <div class="avatar"><?= $avatar ?></div>
        <span class="user-name"><?= $email ?></span>
      </div>
      <a href="?logout=1" class="btn-logout">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
        Logout
      </a>
    </div>
  </nav>

  <!-- Sidebar -->
  <aside class="vault-sidebar" id="vaultSidebar">
    <div class="sidebar-header">
      <span class="sidebar-title">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        Recent Vault
      </span>
      <span class="sidebar-count" id="sidebarCount">0</span>
    </div>
    <div class="sidebar-list" id="sidebarList">
      <div class="sidebar-empty">No passwords saved yet.</div>
    </div>
  </aside>

  <!-- Dashboard Content -->
  <div class="dash-content has-sidebar">
    <div class="dash-inner">

      <!-- Welcome (unchanged) -->
      <div class="dash-welcome">
        <h2><span class="status-dot"></span>Welcome back!</h2>
        <p>Your vault is secure and all systems are operational.</p>
      </div>

      <!-- Stat Cards -->
      <div class="dashboard-grid">
        <div class="stat-card">
          <div class="stat-label">Session Status</div>
          <div class="stat-value" style="font-size:20px;"><span class="badge badge-green">&#9679; Active</span></div>
          <div class="stat-sub">Authenticated &amp; encrypted</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Session Duration</div>
          <div class="stat-value" id="liveClock">--:--:--</div>
          <div class="stat-sub">Live clock</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Saved Passwords</div>
          <div class="stat-value" id="vaultCountStat"><?= $pwCount ?></div>
          <div class="stat-sub">In your vault</div>
        </div>
        <div class="stat-card">
          <div class="stat-label">Last Login</div>
          <div class="stat-value" style="font-size:14px;"><?= $lastLogin ?></div>
          <div class="stat-sub">Previous session</div>
        </div>
      </div>


      <!-- ══ PASSWORD ANALYZER & GENERATOR ═══════════════════ -->
      <div class="section-divider"><span>Password Analyzer &amp; Generator</span></div>

      <div class="analyzer-layout">

        <!-- Analyzer -->
        <div class="security-panel analyzer-panel">
          <div class="panel-header">
            <div class="panel-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </div>
            <div>
              <div class="panel-title">Password Analyzer</div>
              <div class="panel-sub">Live strength &amp; entropy analysis</div>
            </div>
          </div>

          <div class="analyzer-input-wrap">
            <input type="password" id="analyzerInput" class="analyzer-input"
                   placeholder="Type or paste a password…" autocomplete="off">
            <button class="toggle-analyzer-vis" id="toggleAnalyzerVis" aria-label="Show/hide">
              <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
              <svg class="eye-close" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" style="display:none"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
            </button>
          </div>

          <!-- Score Ring -->
          <div class="score-wrap" id="scoreWrap">
            <svg class="score-ring" viewBox="0 0 120 120">
              <circle class="score-ring-bg" cx="60" cy="60" r="50"/>
              <circle class="score-ring-fill" id="scoreRingFill" cx="60" cy="60" r="50"
                      stroke-dasharray="314" stroke-dashoffset="314"/>
            </svg>
            <div class="score-center">
              <div class="score-number" id="scoreNumber">0</div>
              <div class="score-label">Score</div>
            </div>
          </div>

          <!-- Checklist -->
          <ul class="check-list" id="checkList">
            <li class="check-item" data-key="len8">  <span class="ci-icon">&#10060;</span> 8+ characters</li>
            <li class="check-item" data-key="len12"> <span class="ci-icon">&#10060;</span> 12+ characters</li>
            <li class="check-item" data-key="upper"> <span class="ci-icon">&#10060;</span> Uppercase letter</li>
            <li class="check-item" data-key="lower"> <span class="ci-icon">&#10060;</span> Lowercase letter</li>
            <li class="check-item" data-key="digit"> <span class="ci-icon">&#10060;</span> Number</li>
            <li class="check-item" data-key="symbol"><span class="ci-icon">&#10060;</span> Symbol (!@#…)</li>
          </ul>

          <!-- Meta -->
          <div class="analyzer-meta" id="analyzerMeta">
            <div class="meta-item"><span class="meta-key">Strength</span><span class="meta-val" id="metaStrength">—</span></div>
            <div class="meta-item"><span class="meta-key">Entropy</span><span class="meta-val" id="metaEntropy">0 bits</span></div>
            <div class="meta-item"><span class="meta-key">Length</span><span class="meta-val" id="metaLength">0 chars</span></div>
          </div>

          <!-- Suggestions -->
          <div class="suggestions-box" id="suggestionsBox" style="display:none">
            <div class="suggestions-title">&#128161; Suggestions</div>
            <ul class="suggestions-list" id="suggestionsList"></ul>
          </div>

          <!-- Save Analyzer Password to Vault -->
          <div class="save-form" id="analyzerSaveForm" style="display:none">
            <div class="save-divider">Save to Vault</div>
            <div class="save-row">
              <input type="text" id="analyzerSaveLabel" class="save-input"
                     placeholder="Label (e.g. Facebook, Gmail…)" maxlength="255">
              <button class="btn-save-vault" id="btnAnalyzerSave">&#128190; Save</button>
            </div>
            <div class="save-status" id="analyzerSaveStatus"></div>
          </div>
        </div>

        <!-- Generator -->
        <div class="security-panel generator-panel">
          <div class="panel-header">
            <div class="panel-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg>
            </div>
            <div>
              <div class="panel-title">Smart Generator</div>
              <div class="panel-sub">Generate &amp; save strong passwords</div>
            </div>
          </div>

          <div class="gen-options">
            <div class="gen-row">
              <label class="gen-label">Length: <strong id="genLenVal">16</strong></label>
              <input type="range" id="genLength" min="8" max="64" value="16" class="gen-slider">
            </div>
            <div class="gen-checks">
              <label class="gen-check"><input type="checkbox" id="genUpper"   checked> Uppercase</label>
              <label class="gen-check"><input type="checkbox" id="genLower"   checked> Lowercase</label>
              <label class="gen-check"><input type="checkbox" id="genDigits"  checked> Numbers</label>
              <label class="gen-check"><input type="checkbox" id="genSymbols" checked> Symbols</label>
            </div>
          </div>

          <div class="gen-output-wrap">
            <div class="gen-output" id="genOutput">Click generate&hellip;</div>
            <button class="gen-copy-btn" id="genCopyBtn" title="Copy">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            </button>
          </div>

          <div class="gen-strength-bar-wrap">
            <div class="gen-strength-track"><div class="gen-strength-fill" id="genStrengthFill"></div></div>
            <span class="gen-strength-label" id="genStrengthLabel">—</span>
          </div>

          <button class="btn-generate" id="btnGenerate">&#9889; Generate Password</button>

          <div class="save-form" id="saveForm" style="display:none">
            <div class="save-divider">Save to Vault</div>
            <div class="save-row">
              <input type="text" id="saveLabel" class="save-input"
                     placeholder="Label (e.g. Gmail, Netflix…)" maxlength="255">
              <button class="btn-save-vault" id="btnSaveVault">&#128190; Save</button>
            </div>
            <div class="save-status" id="saveStatus"></div>
          </div>

          <div style="margin-top:14px">
            <button class="btn-suggest" id="btnSuggestFromAnalyzer">
              &#129504; Suggest stronger from analyzer
            </button>
          </div>
        </div>
      </div><!-- /analyzer-layout -->

      <!-- ══ VAULT TABLE ════════════════════════════════════ -->
      <div class="section-divider"><span>Password Vault</span></div>

      <div class="security-panel vault-panel">
        <div class="vault-toolbar">
          <div class="vault-search-wrap">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" id="vaultSearch" class="vault-search" placeholder="Search vault…">
          </div>
          <span class="vault-total">Total: <strong id="vaultTotal">0</strong></span>
        </div>

        <div class="vault-table-wrap">
          <table class="vault-table" id="vaultTable">
            <thead>
              <tr>
                <th>Label</th>
                <th>Password</th>
                <th>Strength</th>
                <th>Entropy</th>
                <th>Saved</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody id="vaultTbody">
              <tr><td colspan="6" class="vault-empty">Loading vault…</td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- ══ SECURITY TIPS ══════════════════════════════════ -->
      <div class="section-divider"><span>Security Tips</span></div>

      <div class="tips-grid">
        <div class="tip-card tip-green">
          <div class="tip-icon">&#128260;</div>
          <div class="tip-title">Use Unique Passwords</div>
          <div class="tip-body">Never reuse a password across sites. One breach won't cascade into many.</div>
        </div>
        <div class="tip-card tip-blue">
          <div class="tip-icon">&#128207;</div>
          <div class="tip-title">Length Beats Complexity</div>
          <div class="tip-body">A 20-character passphrase is stronger than a 10-character symbol soup.</div>
        </div>
        <div class="tip-card tip-yellow">
          <div class="tip-icon">&#128683;</div>
          <div class="tip-title">Avoid Personal Info</div>
          <div class="tip-body">Birthdays, names, and pet names are the first things attackers try.</div>
        </div>
        <div class="tip-card tip-green">
          <div class="tip-icon">&#128272;</div>
          <div class="tip-title">Enable 2FA Everywhere</div>
          <div class="tip-body">Two-factor authentication blocks 99% of automated attacks instantly.</div>
        </div>
        <div class="tip-card tip-blue">
          <div class="tip-icon">&#127922;</div>
          <div class="tip-title">Use a Passphrase</div>
          <div class="tip-body">Four random words make a memorable, mathematically strong password.</div>
        </div>
        <div class="tip-card tip-yellow">
          <div class="tip-icon">&#128260;</div>
          <div class="tip-title">Rotate Regularly</div>
          <div class="tip-body">Change critical passwords every 6&ndash;12 months and after any known breach.</div>
        </div>
      </div>

    </div><!-- /dash-inner -->
  </div><!-- /dash-content -->

  <!-- Modal -->
  <div class="modal-overlay" id="modalOverlay">
    <div class="modal-box">
      <div class="modal-title" id="modalTitle">Password Details</div>
      <div class="modal-pw" id="modalPw">&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;</div>
      <div class="modal-actions">
        <button class="modal-copy" id="modalCopy">&#128203; Copy</button>
        <button class="modal-close" id="modalClose">&#10005; Close</button>
      </div>
    </div>
  </div>

  <script src="script.js"></script>
  <script src="vault.js"></script>
</body>
</html>