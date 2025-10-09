<?php
/** @var string $title */
/** @var string $contentTemplate */
/** @var array $contentData */

use App\Core\View;
use App\Support\AdminMode;
use App\Services\Cms\ContentRepository;
use App\Services\Security\Csrf;

$pageTitle = isset($title) ? $title . ' | AIRewardrop' : 'AIRewardrop';
$contentRepository = new ContentRepository();
$layoutSettings = $contentRepository->getSettings();

$isAdmin = AdminMode::isAdmin();
$adminModeEnabled = AdminMode::isEnabled();
$adminCsrf = $isAdmin ? Csrf::token() : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        bg: '#0b0b12',
                        bg2: '#121226',
                        txt: '#ececf1',
                        muted: '#a8a8bb',
                        stroke: 'rgba(255, 255, 255, 0.14)',
                        glass: 'rgba(255, 255, 255, 0.06)',
                        pri: '#f03a3a',
                        'pri-700': '#c92a2a',
                        acc: '#ffffff',
                        cy: '#35e0ff',
                        yl: '#ffd84d',
                    },
                    boxShadow: {
                        deep: '0 8px 28px rgba(0,0,0,.35)',
                    }
                }
            }
        };
    </script>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script defer data-domain="airewardrop.xyz" src="https://plausible.io/js/script.js"></script>
    <script type="module" src="/assets/js/animate.js" defer></script>
    <?php if ($isAdmin && $adminCsrf): ?>
        <meta name="csrf-token" content="<?= htmlspecialchars($adminCsrf, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>
    <?php if ($isAdmin): ?>
        <link rel="stylesheet" href="/assets/admin/admin.css">
    <?php endif; ?>
</head>
<body class="min-h-screen bg-bg text-txt font-sans relative">
    <div class="absolute inset-0 -z-10 h-full w-full bg-bg bg-[radial-gradient(1200px_800px_at_70%_-10%,rgba(240,58,58,0.18),transparent_60%),radial-gradient(900px_600px_at_-10%_30%,rgba(53,224,255,0.16),transparent_60%),linear-gradient(180deg,var(--bg),var(--bg2))]"></div>
    <?php if ($isAdmin): ?>
        <?php View::renderPartial('partials/admin-toolbar', [
            'enabled' => $adminModeEnabled,
        ]); ?>
    <?php endif; ?>
    <?php View::renderPartial('partials/header', ['settings' => $layoutSettings]); ?>
    <main class="container mx-auto max-w-6xl px-4 py-8 pt-28 space-y-16">
        <?php View::renderPartial($contentTemplate, $contentData ?? []); ?>
    </main>
    <?php View::renderPartial('partials/footer'); ?>
    <?php if ($isAdmin): ?>
        <script>
            window.ADMIN_CONTEXT = {
                enabled: <?= $adminModeEnabled ? 'true' : 'false'; ?>,
                csrf: <?= json_encode($adminCsrf, JSON_THROW_ON_ERROR); ?>,
                endpoints: {
                    toggle: '/admin/api/toggle-mode',
                    update: '/admin/api/update-field',
                    upload: '/admin/api/upload-image'
                }
            };
        </script>
        <script type="module" src="/assets/admin/admin.js" defer></script>
    <?php endif; ?>
</body>
</html>
