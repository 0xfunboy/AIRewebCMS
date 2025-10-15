<?php
declare(strict_types=1);

$projectRoot = dirname(__DIR__);
$envPath = $projectRoot . '/.env.php';
$lockFile = $projectRoot . '/storage/install.lock';
$schemaPath = $projectRoot . '/database/schema.sql';

$messages = [];
$errors = [];

function default_config(): array
{
    return [
        'app' => [
            'name' => 'AIRewardrop',
            'env' => 'production',
            'debug' => false,
            'url' => 'https://example.com',
            'timezone' => 'UTC',
            'key' => 'base64:' . base64_encode(random_bytes(32)),
            'session_name' => 'aire_session',
        ],
        'database' => [
            'host' => '127.0.0.1',
            'port' => 3306,
            'database' => 'airewardrop',
            'username' => 'aire_user',
            'password' => 'secret',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
        ],
        'wallet' => [
            'allowed_addresses' => [],
            'nonce_ttl' => 300,
            'project_id' => '',
            'rpc_url' => 'https://rpc.ankr.com/eth',
        ],
        'mail' => [
            'driver' => 'smtp',
            'host' => 'localhost',
            'port' => 587,
            'username' => '',
            'password' => '',
            'encryption' => 'tls',
            'from_address' => 'noreply@example.com',
            'from_name' => 'AIRewardrop',
        ],
    ];
}

function export_config(array $config, int $level = 0): string
{
    $indent = str_repeat('    ', $level);
    $nextIndent = str_repeat('    ', $level + 1);
    $lines = ['['];
    foreach ($config as $key => $value) {
        $line = $nextIndent . var_export((string)$key, true) . ' => ';
        if (is_array($value)) {
            $line .= export_config($value, $level + 1);
        } elseif (is_bool($value)) {
            $line .= $value ? 'true' : 'false';
        } elseif (is_int($value) || is_float($value)) {
            $line .= (string)$value;
        } elseif ($value === null) {
            $line .= 'null';
        } else {
            $line .= var_export($value, true);
        }
        $lines[] = $line . ',';
    }
    $lines[] = $indent . ']';
    return implode("\n", $lines);
}

function parse_addresses(string $input): array
{
    $parts = preg_split('/[\r\n,]+/', $input);
    $parts = array_map(static fn(string $value) => strtolower(trim($value)), $parts ?: []);
    $parts = array_filter($parts, static fn(string $value) => $value !== '');
    return array_values(array_unique($parts));
}

function load_config(string $envPath): array
{
    if (file_exists($envPath)) {
        $config = require $envPath;
        if (is_array($config)) {
            return array_replace_recursive(default_config(), $config);
        }
    }
    return default_config();
}

function build_pdo(array $config): PDO
{
    $db = $config['database'];
    if (!empty($db['socket'])) {
        $dsn = sprintf(
            'mysql:unix_socket=%s;dbname=%s;charset=%s',
            $db['socket'],
            $db['database'],
            $db['charset'] ?? 'utf8mb4'
        );
    } else {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $db['host'] ?? '127.0.0.1',
            $db['port'] ?? 3306,
            $db['database'],
            $db['charset'] ?? 'utf8mb4'
        );
    }

    return new PDO(
        $dsn,
        $db['username'],
        $db['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => true,
        ]
    );
}

$config = load_config($envPath);
$posted = $config;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bool = static fn(string $key): bool => isset($_POST[$key]) && $_POST[$key] === '1';

    $posted = $config; // start from current config

    $posted['app']['name'] = trim($_POST['app_name'] ?? $config['app']['name']);
    $posted['app']['env'] = trim($_POST['app_env'] ?? $config['app']['env']);
    $posted['app']['debug'] = $bool('app_debug');
    $posted['app']['url'] = trim($_POST['app_url'] ?? $config['app']['url']);
    $posted['app']['timezone'] = trim($_POST['app_timezone'] ?? $config['app']['timezone']);
    $posted['app']['key'] = trim($_POST['app_key'] ?? $config['app']['key']);
    if ($posted['app']['key'] === '') {
        $posted['app']['key'] = 'base64:' . base64_encode(random_bytes(32));
        $messages[] = 'App key regenerated.';
    }
    $posted['app']['session_name'] = trim($_POST['app_session_name'] ?? $config['app']['session_name']);

    $posted['database']['host'] = trim($_POST['db_host'] ?? $config['database']['host']);
    $posted['database']['port'] = (int)($_POST['db_port'] ?? $config['database']['port']);
    $posted['database']['database'] = trim($_POST['db_name'] ?? $config['database']['database']);
    $posted['database']['username'] = trim($_POST['db_username'] ?? $config['database']['username']);
    $posted['database']['password'] = trim($_POST['db_password'] ?? $config['database']['password']);
    $posted['database']['charset'] = trim($_POST['db_charset'] ?? $config['database']['charset']);
    $posted['database']['collation'] = trim($_POST['db_collation'] ?? $config['database']['collation']);
    $posted['database']['socket'] = trim($_POST['db_socket'] ?? ($config['database']['socket'] ?? '')) ?: null;

    $posted['wallet']['allowed_addresses'] = parse_addresses($_POST['wallet_addresses'] ?? '');
    $posted['wallet']['nonce_ttl'] = (int)($_POST['wallet_nonce_ttl'] ?? $config['wallet']['nonce_ttl']);
    $posted['wallet']['project_id'] = trim($_POST['wallet_project_id'] ?? $config['wallet']['project_id']);
    $posted['wallet']['rpc_url'] = trim($_POST['wallet_rpc_url'] ?? $config['wallet']['rpc_url']);

    $posted['mail']['driver'] = trim($_POST['mail_driver'] ?? $config['mail']['driver']);
    $posted['mail']['host'] = trim($_POST['mail_host'] ?? $config['mail']['host']);
    $posted['mail']['port'] = (int)($_POST['mail_port'] ?? $config['mail']['port']);
    $posted['mail']['username'] = trim($_POST['mail_username'] ?? $config['mail']['username']);
    $posted['mail']['password'] = trim($_POST['mail_password'] ?? $config['mail']['password']);
    $posted['mail']['encryption'] = trim($_POST['mail_encryption'] ?? $config['mail']['encryption']);
    $posted['mail']['from_address'] = trim($_POST['mail_from_address'] ?? $config['mail']['from_address']);
    $posted['mail']['from_name'] = trim($_POST['mail_from_name'] ?? $config['mail']['from_name']);

    $configString = "<?php\n\nreturn " . export_config($posted) . ";\n";
    if (@file_put_contents($envPath, $configString) === false) {
        $errors[] = 'Impossibile scrivere il file .env.php. Controlla i permessi.';
    } else {
        @chmod($envPath, 0640);
        $messages[] = 'Configurazione salvata.';
        $config = $posted;
    }

    $actions = $_POST['actions'] ?? [];

    if (in_array('reset_lock', $actions, true) && file_exists($lockFile)) {
        if (@unlink($lockFile)) {
            $messages[] = 'File install.lock rimosso.';
        } else {
            $errors[] = 'Impossibile rimuovere install.lock. Verifica i permessi.';
        }
    }

    $pdo = null;
    if ((in_array('run_schema', $actions, true) || in_array('run_seed', $actions, true) || in_array('seed_admins', $actions, true)) && empty($errors)) {
        try {
            $pdo = build_pdo($config);
            $messages[] = 'Connessione al database riuscita.';
        } catch (Throwable $e) {
            $errors[] = 'Connessione al database fallita: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }

    if ($pdo && in_array('run_schema', $actions, true)) {
        if (!file_exists($schemaPath)) {
            $errors[] = 'File database/schema.sql non trovato.';
        } else {
            try {
                $schemaSql = file_get_contents($schemaPath);
                $statements = array_filter(array_map('trim', preg_split('/;\s*\n/', (string)$schemaSql)));
                foreach ($statements as $statement) {
                    if ($statement !== '') {
                        $pdo->exec($statement);
                    }
                }
                $messages[] = 'Schema del database applicato.';
            } catch (Throwable $e) {
                $errors[] = 'Error while running the schema: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
            }
        }
    }

    $bootstrapLoaded = false;
    if ($pdo && (in_array('run_seed', $actions, true) || in_array('seed_admins', $actions, true)) && empty($errors)) {
        require $projectRoot . '/app/bootstrap.php';
        $bootstrapLoaded = true;
    }

    if ($bootstrapLoaded && in_array('run_seed', $actions, true) && empty($errors)) {
        try {
            \App\Support\SeedImporter::ensureSeeded();
            $messages[] = 'Seed di base completato.';
        } catch (Throwable $e) {
            $errors[] = 'Error while seeding data: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }

    if ($bootstrapLoaded && in_array('seed_admins', $actions, true) && empty($errors)) {
        try {
            \App\Support\SeedImporter::seedAdminsFromAllowedAddresses();
            $messages[] = 'Wallet amministratori allineati con la configurazione attuale.';
        } catch (Throwable $e) {
            $errors[] = 'Error while seeding admin accounts: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
        }
    }

    if (empty($errors) && ($pdo || in_array('reset_lock', $actions, true))) {
        if (!is_dir(dirname($lockFile))) {
            @mkdir(dirname($lockFile), 0775, true);
        }
        if ($pdo && (in_array('run_schema', $actions, true) || in_array('run_seed', $actions, true) || in_array('seed_admins', $actions, true))) {
            file_put_contents($lockFile, 'Installed at ' . date(DATE_W3C));
        }
    }
}

$walletAddressesText = implode("\n", $config['wallet']['allowed_addresses']);
$lockExists = file_exists($lockFile);

function checked(bool $value): string { return $value ? 'checked' : ''; }
function selected(string $value, string $current): string { return $value === $current ? 'selected' : ''; }

?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Installazione AIRewebCMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background:#0b0b12; color:#f5f7ff; margin:0; padding:0 0 60px; }
        h1, h2, h3 { margin:0; }
        a { color:#35e0ff; }
        .container { max-width: 960px; margin: 48px auto; padding: 32px; background: rgba(17,17,34,0.92); border-radius: 18px; border:1px solid rgba(255,255,255,0.08); box-shadow:0 28px 60px rgba(0,0,0,0.4); }
        fieldset { border:1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 20px; margin: 24px 0; background: rgba(255,255,255,0.02); }
        legend { padding: 0 10px; font-weight: 600; text-transform: uppercase; font-size: 13px; letter-spacing: 0.08em; color:#a8acc6; }
        label { display:flex; flex-direction:column; gap:6px; font-size: 13px; text-transform: uppercase; letter-spacing: 0.05em; color:#a8acc6; }
        input[type="text"], input[type="number"], input[type="password"], input[type="url"], input[type="email"], textarea, select {
            background:#101023; border:1px solid rgba(255,255,255,0.08); border-radius: 8px; padding:10px 12px; color:#f5f7ff; font-size:14px;
        }
        textarea { min-height: 110px; resize: vertical; }
        .grid { display:grid; gap:16px; }
        .grid.two { grid-template-columns: repeat(auto-fit, minmax(240px,1fr)); }
        .messages { margin: 16px 0; display:grid; gap:10px; }
        .alert { padding:14px 16px; border-radius:10px; font-size:14px; }
        .alert.error { background:rgba(240,58,58,0.12); border:1px solid rgba(240,58,58,0.4); color:#ffb3b3; }
        .alert.success { background:rgba(53,224,255,0.12); border:1px solid rgba(53,224,255,0.35); color:#c2f6ff; }
        .actions { display:flex; flex-wrap:wrap; gap:12px; margin-top:24px; align-items:center; }
        button { background:#f03a3a; border:none; color:#fff; padding:12px 24px; border-radius:10px; font-weight:600; font-size:15px; cursor:pointer; transition:transform .15s ease; }
        button:hover { transform: translateY(-1px); }
        button.secondary { background:rgba(255,255,255,0.08); color:#f5f7ff; }
        .checkbox-list { display:grid; gap:10px; font-size:14px; }
        .checkbox-list label { flex-direction:row; align-items:center; justify-content:flex-start; gap:10px; text-transform:none; letter-spacing:normal; }
        .status { font-size:13px; color:#a8acc6; margin-top:6px; }
        @media (max-width: 720px) {
            .container { margin: 24px 16px; padding: 24px; }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>AIRewebCMS Installer</h1>
    <p class="status">File di configurazione: <code><?= htmlspecialchars($envPath, ENT_QUOTES, 'UTF-8'); ?></code><br>Install lock: <?= $lockExists ? '<strong style="color:#ffb74d;">presente</strong>' : '<span style="color:#8dd0ff;">non presente</span>'; ?></p>

    <?php if ($errors): ?>
        <div class="messages">
            <?php foreach ($errors as $error): ?>
                <div class="alert error"><?= $error; ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($messages && !$errors): ?>
        <div class="messages">
            <?php foreach ($messages as $message): ?>
                <div class="alert success"><?= $message; ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <fieldset>
            <legend>App</legend>
            <div class="grid two">
                <label>
                    Nome applicazione
                    <input type="text" name="app_name" value="<?= htmlspecialchars($config['app']['name'], ENT_QUOTES, 'UTF-8'); ?>">
                </label>
                <label>
                    Ambiente (env)
                    <input type="text" name="app_env" value="<?= htmlspecialchars($config['app']['env'], ENT_QUOTES, 'UTF-8'); ?>">
                </label>
                <label>
                    URL pubblico
                    <input type="url" name="app_url" value="<?= htmlspecialchars($config['app']['url'], ENT_QUOTES, 'UTF-8'); ?>">
                </label>
                <label>
                    Timezone
                    <input type="text" name="app_timezone" value="<?= htmlspecialchars($config['app']['timezone'], ENT_QUOTES, 'UTF-8'); ?>">
                </label>
                <label>
                    Session name
                    <input type="text" name="app_session_name" value="<?= htmlspecialchars($config['app']['session_name'], ENT_QUOTES, 'UTF-8'); ?>">
                </label>
                <label>
                    App key
                    <div style="display:flex; gap:8px;">
                        <input type="text" name="app_key" id="app-key" value="<?= htmlspecialchars($config['app']['key'], ENT_QUOTES, 'UTF-8'); ?>" style="flex:1;">
                        <button type="button" class="secondary" onclick="generateKey()">Rigenera</button>
                    </div>
                </label>
                <label style="flex-direction:row; align-items:center; margin-top:12px;">
                    <input type="hidden" name="app_debug" value="0">
                    <input type="checkbox" name="app_debug" value="1" <?= checked((bool)$config['app']['debug']); ?>>
                    <span>Debug attivo</span>
                </label>
            </div>
        </fieldset>

        <fieldset>
            <legend>Database</legend>
            <div class="grid two">
                <label>Host<input type="text" name="db_host" value="<?= htmlspecialchars($config['database']['host'], ENT_QUOTES, 'UTF-8'); ?>"></label>
                <label>Porta<input type="number" name="db_port" value="<?= htmlspecialchars((string)$config['database']['port'], ENT_QUOTES, 'UTF-8'); ?>"></label>
                <label>Database<input type="text" name="db_name" value="<?= htmlspecialchars($config['database']['database'], ENT_QUOTES, 'UTF-8'); ?>"></label>
                <label>Username DB<input type="text" name="db_username" value="<?= htmlspecialchars($config['database']['username'], ENT_QUOTES, 'UTF-8'); ?>"></label>
                <label>Password DB<input type="password" name="db_password" value="<?= htmlspecialchars($config['database']['password'], ENT_QUOTES, 'UTF-8'); ?>"></label>
                <label>Charset<input type="text" name="db_charset" value="<?= htmlspecialchars($config['database']['charset'], ENT_QUOTES, 'UTF-8'); ?>"></label>
                <label>Collation<input type="text" name="db_collation" value="<?= htmlspecialchars($config['database']['collation'], ENT_QUOTES, 'UTF-8'); ?>"></label>
                <label>Socket (opzionale)<input type="text" name="db_socket" value="<?= htmlspecialchars($config['database']['socket'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></label>
            </div>
        </fieldset>

        <fieldset>
            <legend>Wallet / Admin</legend>
            <div class="grid two">
                <label>Wallet autorizzati (uno per riga)
                    <textarea name="wallet_addresses" placeholder="0x123..."><?= htmlspecialchars($walletAddressesText, ENT_QUOTES, 'UTF-8'); ?></textarea>
                </label>
                <label>WalletConnect Project ID<input type="text" name="wallet_project_id" value="<?= htmlspecialchars($config['wallet']['project_id'], ENT_QUOTES, 'UTF-8'); ?>"></label>
                <label>RPC URL<input type="text" name="wallet_rpc_url" value="<?= htmlspecialchars($config['wallet']['rpc_url'], ENT_QUOTES, 'UTF-8'); ?>"></label>
                <label>Nonce TTL (secondi)<input type="number" name="wallet_nonce_ttl" value="<?= htmlspecialchars((string)$config['wallet']['nonce_ttl'], ENT_QUOTES, 'UTF-8'); ?>"></label>
            </div>
        </fieldset>

        <fieldset>
            <legend>Mail</legend>
            <div class="grid two">
                <label>Driver<input type="text" name="mail_driver" value="<?= htmlspecialchars($config['mail']['driver'], ENT_QUOTES, 'UTF-8'); ?>"></label>
                <label>Host SMTP<input type="text" name="mail_host" value="<?= htmlspecialchars($config['mail']['host'], ENT_QUOTES, 'UTF-8'); ?>"></label>
                <label>Porta<input type="number" name="mail_port" value="<?= htmlspecialchars((string)$config['mail']['port'], ENT_QUOTES, 'UTF-8'); ?>"></label>
                <label>Username<input type="text" name="mail_username" value="<?= htmlspecialchars($config['mail']['username'], ENT_QUOTES, 'UTF-8'); ?>"></label>
                <label>Password<input type="password" name="mail_password" value="<?= htmlspecialchars($config['mail']['password'], ENT_QUOTES, 'UTF-8'); ?>"></label>
                <label>Encryption<input type="text" name="mail_encryption" value="<?= htmlspecialchars($config['mail']['encryption'], ENT_QUOTES, 'UTF-8'); ?>"></label>
                <label>From address<input type="email" name="mail_from_address" value="<?= htmlspecialchars($config['mail']['from_address'], ENT_QUOTES, 'UTF-8'); ?>"></label>
                <label>From name<input type="text" name="mail_from_name" value="<?= htmlspecialchars($config['mail']['from_name'], ENT_QUOTES, 'UTF-8'); ?>"></label>
            </div>
        </fieldset>

        <fieldset>
            <legend>Azioni</legend>
            <div class="checkbox-list">
                <label><input type="checkbox" name="actions[]" value="run_schema"> Esegui/aggiorna schema database</label>
                <label><input type="checkbox" name="actions[]" value="run_seed"> Importa seed di base</label>
                <label><input type="checkbox" name="actions[]" value="seed_admins"> Allinea wallet admin da configurazione</label>
                <label><input type="checkbox" name="actions[]" value="reset_lock"> Rimuovi file install.lock</label>
            </div>
            <p class="status">Select one or more actions after saving changes. You can rerun the installer incrementally as many times as needed.</p>
        </fieldset>

        <div class="actions">
            <button type="submit">Save &amp; Apply</button>
            <button type="button" class="secondary" onclick="window.location.reload()">Cancel</button>
        </div>
    </form>
</div>
<script>
function generateKey() {
    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=';
    let key = '';
    const buffer = new Uint8Array(32);
    crypto.getRandomValues(buffer);
    let binary = '';
    buffer.forEach(b => binary += String.fromCharCode(b));
    key = 'base64:' + btoa(binary);
    const input = document.getElementById('app-key');
    if (input) {
        input.value = key;
    }
}
</script>
</body>
</html>
