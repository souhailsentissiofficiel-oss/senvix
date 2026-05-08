<?php
/**
 * vault_api.php — AJAX endpoint for Password Vault
 * All responses are JSON. Auth-guarded.
 */
require_once 'config.php';

header('Content-Type: application/json');

// ── Auth guard ────────────────────────────────────────────────
if (empty($_SESSION['logged_in']) || empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$uid    = (int) $_SESSION['user_id'];
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// ── AES encryption helpers ────────────────────────────────────
// Key is derived from a server secret + user id (never store raw passwords)
define('VAULT_SECRET', 'sv_vault_key_change_me_in_prod_32chars!');


function vaultEncrypt(string $plain, int $uid): string {
    $key   = substr(hash('sha256', VAULT_SECRET . $uid, true), 0, 32);
    $iv    = random_bytes(16);
    $enc   = openssl_encrypt($plain, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $enc);
}

function vaultDecrypt(string $blob, int $uid): string {
    $key  = substr(hash('sha256', VAULT_SECRET . $uid, true), 0, 32);
    $raw  = base64_decode($blob);
    $iv   = substr($raw, 0, 16);
    $enc  = substr($raw, 16);
    return openssl_decrypt($enc, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv) ?: '';
}

// ── Route ─────────────────────────────────────────────────────
switch ($action) {

    // ── Save a new password ───────────────────────────────────
    case 'save':
        $label    = trim($_POST['label']    ?? '');
        $password = trim($_POST['password'] ?? '');
        $strength = (int)   ($_POST['strength'] ?? 0);
        $entropy  = (float) ($_POST['entropy']  ?? 0);

        if ($label === '' || $password === '') {
            echo json_encode(['error' => 'Label and password are required.']); exit;
        }
        if (strlen($label) > 255) {
            echo json_encode(['error' => 'Label too long (max 255 chars).']); exit;
        }

        $encrypted = vaultEncrypt($password, $uid);

        $stmt = $pdo->prepare(
            "INSERT INTO passwords (user_id, label, password, strength, entropy) VALUES (?, ?, ?, ?, ?)"
        );
        $stmt->execute([$uid, $label, $encrypted, $strength, round($entropy, 2)]);

        echo json_encode(['ok' => true, 'id' => $pdo->lastInsertId()]);
        break;

    // ── List all passwords for user ───────────────────────────
    case 'list':
        $search = trim($_GET['search'] ?? '');
        if ($search !== '') {
            $stmt = $pdo->prepare(
                "SELECT id, label, password, strength, entropy, created_at
                 FROM passwords WHERE user_id = ? AND label LIKE ?
                 ORDER BY created_at DESC"
            );
            $stmt->execute([$uid, '%' . $search . '%']);
        } else {
            $stmt = $pdo->prepare(
                "SELECT id, label, password, strength, entropy, created_at
                 FROM passwords WHERE user_id = ?
                 ORDER BY created_at DESC"
            );
            $stmt->execute([$uid]);
        }

        $rows = $stmt->fetchAll();
        foreach ($rows as &$row) {
            $row['password_plain'] = vaultDecrypt($row['password'], $uid);
            unset($row['password']); // don't send encrypted blob to client
        }

        echo json_encode(['ok' => true, 'data' => $rows]);
        break;

    // ── Delete a password ─────────────────────────────────────
    case 'delete':
        $id = (int) ($_POST['id'] ?? 0);
        if (!$id) { echo json_encode(['error' => 'Invalid ID']); exit; }

        $stmt = $pdo->prepare("DELETE FROM passwords WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $uid]);

        echo json_encode(['ok' => true]);
        break;

    // ── Count saved passwords ─────────────────────────────────
    case 'count':
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM passwords WHERE user_id = ?");
        $stmt->execute([$uid]);
        echo json_encode(['ok' => true, 'count' => (int) $stmt->fetchColumn()]);
        break;

    default:
        http_response_code(400);
        echo json_encode(['error' => 'Unknown action']);
}