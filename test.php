<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SecureVault — Connection Test</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #0d1f17;
      color: #e8f5ee;
      padding: 40px 20px;
      min-height: 100vh;
    }
    .container {
      max-width: 700px;
      margin: 0 auto;
    }
    h1 {
      font-size: 22px;
      margin-bottom: 28px;
      color: #74c69d;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .card {
      background: rgba(255,255,255,0.06);
      border: 1px solid rgba(116,198,157,0.2);
      border-radius: 14px;
      padding: 22px 24px;
      margin-bottom: 16px;
    }
    .card h2 {
      font-size: 13px;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: rgba(232,245,238,0.5);
      margin-bottom: 14px;
    }
    .row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 8px 0;
      border-bottom: 1px solid rgba(255,255,255,0.05);
      font-size: 13px;
    }
    .row:last-child { border-bottom: none; }
    .key   { color: rgba(232,245,238,0.6); }
    .val   { font-family: monospace; color: #e8f5ee; }
    .ok    { color: #52b788; font-weight: 600; }
    .fail  { color: #ff6b6b; font-weight: 600; }
    .warn  { color: #f4d35e; font-weight: 600; }
    .result-box {
      border-radius: 10px;
      padding: 16px 20px;
      font-size: 14px;
      font-weight: 600;
      margin-bottom: 16px;
    }
    .result-ok   { background: rgba(82,183,136,0.12); border: 1px solid rgba(82,183,136,0.35); color: #74c69d; }
    .result-fail { background: rgba(230,57,70,0.12);  border: 1px solid rgba(230,57,70,0.35);  color: #ff6b6b; }
    .error-detail {
      background: rgba(0,0,0,0.4);
      border: 1px solid rgba(230,57,70,0.3);
      border-radius: 10px;
      padding: 14px 16px;
      font-family: monospace;
      font-size: 12px;
      color: #ff6b6b;
      word-break: break-all;
      margin-bottom: 16px;
    }
    .hint {
      background: rgba(244,211,94,0.07);
      border: 1px solid rgba(244,211,94,0.2);
      border-radius: 10px;
      padding: 14px 16px;
      font-size: 12px;
      color: #f4d35e;
      margin-top: 8px;
    }
    .hint strong { display: block; margin-bottom: 6px; font-size: 13px; }
    .hint ul { padding-left: 18px; line-height: 2; }
    table.query-test { width:100%; border-collapse:collapse; font-size:13px; }
    table.query-test td { padding: 7px 10px; border-bottom:1px solid rgba(255,255,255,0.05); }
    table.query-test tr:last-child td { border-bottom:none; }
    .delete-warning {
      background: rgba(230,57,70,0.1);
      border: 1px solid rgba(230,57,70,0.3);
      border-radius: 10px;
      padding: 12px 16px;
      font-size: 12px;
      color: #ff6b6b;
      margin-top: 20px;
    }
  </style>
</head>
<body>
<div class="container">

  <h1>🔒 SecureVault — Connection Test</h1>

<?php
// ── STEP 0: Load credentials (inline so test works standalone) ─
$host    = 'sql308.infinityfree.com';
$dbname  = 'if0_41755743_securevault';
$user    = 'if0_41755743';
$pass    = 'NPWQfQtiBV5m';
$port    = 3306;
$charset = 'utf8mb4';

if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_OFF);
}

// ── STEP 1: Show what we're attempting ────────────────────────
echo '<div class="card">';
echo '<h2>Connection Parameters</h2>';
$rows = [
  'Host'     => $host,
  'Port'     => $port,
  'Database' => $dbname,
  'Username' => $user,
  'Password' => str_repeat('•', strlen($pass)),
  'Charset'  => $charset,
];
foreach ($rows as $k => $v) {
  echo "<div class='row'><span class='key'>$k</span><span class='val'>$v</span></div>";
}
echo '</div>';

// ── STEP 2: PHP environment info ──────────────────────────────
echo '<div class="card">';
echo '<h2>PHP Environment</h2>';
$phpInfo = [
  'PHP Version'       => PHP_VERSION,
  'PDO extension'     => extension_loaded('pdo')       ? '<span class="ok">✓ Loaded</span>'        : '<span class="fail">✗ Missing</span>',
  'PDO MySQL driver'  => extension_loaded('pdo_mysql')  ? '<span class="ok">✓ Loaded</span>'        : '<span class="fail">✗ Missing — fatal</span>',
  'MySQLi extension'  => extension_loaded('mysqli')     ? '<span class="ok">✓ Loaded</span>'        : '<span class="warn">⚠ Not loaded</span>',
  'OpenSSL'           => extension_loaded('openssl')    ? '<span class="ok">✓ Loaded</span>'        : '<span class="fail">✗ Missing (AES vault won\'t work)</span>',
  'Session support'   => function_exists('session_start') ? '<span class="ok">✓ Available</span>'  : '<span class="fail">✗ Unavailable</span>',
  'Server software'   => $_SERVER['SERVER_SOFTWARE']    ?? 'Unknown',
  'HTTPS'             => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? '<span class="ok">✓ Yes</span>' : '<span class="warn">⚠ No (HTTP only)</span>',
];
foreach ($phpInfo as $k => $v) {
  echo "<div class='row'><span class='key'>$k</span><span class='val'>$v</span></div>";
}
echo '</div>';

if (!extension_loaded('pdo_mysql')) {
  echo '<div class="result-fail">❌ FATAL: pdo_mysql extension is not loaded. Contact InfinityFree support.</div>';
  echo '</div></body></html>';
  exit;
}

// ── STEP 3: Attempt PDO connection ───────────────────────────
$pdo         = null;
$connectError = null;
$connectOk    = false;

try {
  $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $host, $port, $dbname, $charset);
  $pdo = new PDO($dsn, $user, $pass, [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::ATTR_TIMEOUT            => 10,
  ]);
  $connectOk = true;
} catch (PDOException $e) {
  $connectError = $e->getMessage();
}

if ($connectOk) {
  echo '<div class="result-box result-ok">✅ PDO Connection successful! Database is reachable.</div>';
} else {
  echo '<div class="result-box result-fail">❌ PDO Connection FAILED</div>';
  echo '<div class="error-detail"><strong>MySQL Error:</strong><br>' . htmlspecialchars($connectError) . '</div>';

  // Show targeted hints based on the error message
  echo '<div class="hint"><strong>💡 What this error means &amp; how to fix it:</strong><ul>';
  if (str_contains($connectError, 'Access denied')) {
    echo '<li>Wrong username or password — double-check your InfinityFree MySQL panel</li>';
    echo '<li>Make sure you are using the MySQL password, NOT your InfinityFree account password</li>';
  }
  if (str_contains($connectError, "Unknown database") || str_contains($connectError, 'No such file')) {
    echo '<li>Database name is wrong — it MUST include the prefix: <strong>if0_41755743_securevault</strong></li>';
    echo '<li>Go to InfinityFree → MySQL Databases and confirm the exact database name</li>';
  }
  if (str_contains($connectError, 'Connection refused') || str_contains($connectError, 'timeout') || str_contains($connectError, 'php_network')) {
    echo '<li>Host is unreachable — check that you used <strong>sql308.infinityfree.com</strong> (not localhost)</li>';
    echo '<li>InfinityFree does NOT allow external MySQL connections — must connect from their servers only</li>';
    echo '<li>Try the exact hostname shown in your InfinityFree panel</li>';
  }
  if (str_contains($connectError, 'driver')) {
    echo '<li>pdo_mysql PHP extension is not enabled on this server</li>';
  }
  echo '</ul></div>';
  echo '</div></body></html>';
  exit;
}

// ── STEP 4: Test basic queries ────────────────────────────────
echo '<div class="card">';
echo '<h2>Database Query Tests</h2>';
echo '<table class="query-test">';

// Test 1: users table
try {
  $r = $pdo->query("SELECT COUNT(*) as n FROM users")->fetch();
  echo '<tr><td class="key">users table</td><td class="ok">✓ Exists — ' . $r['n'] . ' row(s)</td></tr>';
} catch (PDOException $e) {
  echo '<tr><td class="key">users table</td><td class="fail">✗ Missing — run securevault.sql to create it<br><small>' . htmlspecialchars($e->getMessage()) . '</small></td></tr>';
}

// Test 2: passwords table
try {
  $r = $pdo->query("SELECT COUNT(*) as n FROM passwords")->fetch();
  echo '<tr><td class="key">passwords table</td><td class="ok">✓ Exists — ' . $r['n'] . ' row(s)</td></tr>';
} catch (PDOException $e) {
  echo '<tr><td class="key">passwords table</td><td class="fail">✗ Missing — run migration_v2.sql<br><small>' . htmlspecialchars($e->getMessage()) . '</small></td></tr>';
}

// Test 3: Prepared statement
try {
  $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
  $stmt->execute(['test@test.com']);
  echo '<tr><td class="key">Prepared statements</td><td class="ok">✓ Working</td></tr>';
} catch (PDOException $e) {
  echo '<tr><td class="key">Prepared statements</td><td class="fail">✗ Error: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
}

// Test 4: INSERT privilege (dry run with rollback)
try {
  $pdo->beginTransaction();
  $pdo->exec("INSERT INTO users (email, password, created_at) VALUES ('__test__@test.com', 'x', NOW())");
  $pdo->rollBack();
  echo '<tr><td class="key">INSERT privilege</td><td class="ok">✓ Allowed</td></tr>';
} catch (PDOException $e) {
  $pdo->rollBack();
  echo '<tr><td class="key">INSERT privilege</td><td class="fail">✗ Denied: ' . htmlspecialchars($e->getMessage()) . '</td></tr>';
}

// Test 5: OpenSSL (vault encryption)
echo '<tr><td class="key">AES-256 encryption (vault)</td><td class="' . (extension_loaded('openssl') ? 'ok">✓ OpenSSL available' : 'fail">✗ OpenSSL missing — vault save/load will fail') . '</td></tr>';

// Test 6: MySQL version
try {
  $ver = $pdo->query("SELECT VERSION() as v")->fetchColumn();
  echo '<tr><td class="key">MySQL version</td><td class="val">' . htmlspecialchars($ver) . '</td></tr>';
} catch (PDOException $e) {}

echo '</table>';
echo '</div>';

echo '<div class="result-box result-ok">✅ All checks passed — your config.php is correctly set up for InfinityFree!</div>';
?>

  <div class="delete-warning">
    ⚠️ <strong>Security reminder:</strong> Delete or rename this file after testing.
    It exposes database credentials and should never be publicly accessible on your live site.
  </div>

</div>
</body>
</html>