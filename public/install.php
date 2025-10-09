<?php
declare(strict_types=1);

$projectRoot = dirname(__DIR__);
$envPath = $projectRoot . '/.env.php';
$lockFile = $projectRoot . '/storage/install.lock';
$schemaPath = $projectRoot . '/database/schema.sql';
$errors = [];
$successMessages = [];

if (file_exists($lockFile)) {
    $successMessages[] = 'L\'applicazione risulta già installata. Rimuovi il file <code>storage/install.lock</code> se vuoi rieseguire la procedura.';
}

if (!file_exists($envPath)) {
    $errors[] = 'File <code>.env.php</code> mancante. Copia <code>.env.example.php</code> e aggiorna le credenziali prima di procedere.';
}

if (!file_exists($schemaPath)) {
    $errors[] = 'File <code>database/schema.sql</code> mancante.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors) && !file_exists($lockFile)) {
    try {
        $config = require $envPath;
        if (!isset($config['database'])) {
            throw new RuntimeException('Configurazione database non trovata in .env.php');
        }

        $db = $config['database'];
        $dsn = '';

        if (!empty($db['socket'])) {
            $dsn = sprintf('mysql:unix_socket=%s;dbname=%s;charset=%s',
                $db['socket'],
                $db['database'],
                $db['charset'] ?? 'utf8mb4'
            );
        } else {
            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $db['host'] ?? '127.0.0.1',
                $db['port'] ?? 3306,
                $db['database'],
                $db['charset'] ?? 'utf8mb4'
            );
        }

        $pdo = new PDO($dsn, $db['username'], $db['password'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => true,
        ]);

        $schemaSql = file_get_contents($schemaPath);
        if ($schemaSql === false) {
            throw new RuntimeException('Impossibile leggere il file schema.sql');
        }

        $statements = array_filter(array_map('trim', preg_split('/;\s*\n/', $schemaSql)));
        foreach ($statements as $statement) {
            if ($statement !== '') {
                $pdo->exec($statement);
            }
        }

        require $projectRoot . '/app/bootstrap.php';

        if (!file_exists(dirname($lockFile))) {
            mkdir(dirname($lockFile), 0775, true);
        }
        file_put_contents($lockFile, 'Installed at ' . date(DATE_W3C));

        $successMessages[] = 'Installazione completata con successo! Per sicurezza, elimina o rinomina <code>public/install.php</code>.';
    } catch (Throwable $e) {
        $errors[] = 'Errore durante l\'installazione: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <title>Installazione AIRewebCMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family: Arial, sans-serif; background:#0b0b12; color:#f5f7ff; margin:0; padding:0; }
        .container { max-width: 720px; margin: 60px auto; padding: 40px; background: rgba(15,15,30,0.85); border-radius: 12px; border:1px solid rgba(255,255,255,0.08); }
        h1 { margin-top: 0; font-size: 28px; }
        ul { padding-left: 20px; }
        .alert { padding: 16px; border-radius: 8px; margin-bottom: 24px; }
        .alert.error { background: rgba(240,58,58,0.1); border:1px solid rgba(240,58,58,0.4); color:#ff9e9e; }
        .alert.success { background: rgba(56,189,248,0.1); border:1px solid rgba(56,189,248,0.4); color:#c4f0ff; }
        .actions { margin-top: 32px; display:flex; gap: 16px; }
        button { background:#f03a3a; border:none; color:#fff; padding:12px 24px; border-radius:8px; font-size:16px; cursor:pointer; }
        button:disabled { opacity:0.5; cursor:not-allowed; }
        a { color:#35e0ff; }
        code { background: rgba(255,255,255,0.08); padding:2px 4px; border-radius:4px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Installazione AIRewebCMS</h1>
        <p>Questa procedura crea tutte le tabelle MySQL e popola i contenuti iniziali.</p>

        <?php if ($errors): ?>
            <div class="alert error">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?= $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($successMessages): ?>
            <div class="alert success">
                <ul>
                    <?php foreach ($successMessages as $message): ?>
                        <li><?= $message; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <h2>Prerequisiti</h2>
        <ul>
            <li>File <code>.env.php</code> configurato con le credenziali del database.</li>
            <li>Directory <code>storage/</code> scrivibile dal web server.</li>
            <li>Database MySQL raggiungibile.</li>
        </ul>

        <form method="post" class="actions">
            <button type="submit" <?= (!empty($errors) || file_exists($lockFile)) ? 'disabled' : ''; ?>>
                Avvia installazione
            </button>
        </form>

        <?php if (file_exists($lockFile)): ?>
            <p>Se devi reinstallare, elimina <code><?= htmlspecialchars($lockFile, ENT_QUOTES, 'UTF-8'); ?></code> e ricarica questa pagina.</p>
        <?php endif; ?>
    </div>
</body>
</html>
