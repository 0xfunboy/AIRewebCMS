<?php
/** @var string $title */
/** @var string $contentTemplate */
/** @var array $contentData */

use App\Core\View;
use App\Services\Cms\ContentRepository;
use App\Support\Media;

$pageTitle = isset($title) ? $title . ' | AIRewardrop Admin' : 'Admin | AIRewardrop';

$repository = new ContentRepository();
$settings = $repository->getSettings();
$adminSiteLogo = Media::siteLogoUrl($settings['site_logo'] ?? '');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
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
                        card: 'rgba(255,255,255,0.04)',
                        border: 'rgba(255,255,255,0.08)',
                        txt: '#f5f7ff',
                        muted: '#a8acc6',
                        pri: '#f03a3a',
                        acc: '#ffffff',
                        cy: '#35e0ff',
                    }
                }
            }
        };
    </script>
    <link rel="stylesheet" href="/assets/css/admin.css">
    <script src="/assets/js/admin.js?v=legacy-safe" defer></script>
</head>
<body class="bg-bg text-txt min-h-screen flex">
    <?php View::renderPartial('partials/admin-sidebar', [
        'siteLogo' => $adminSiteLogo,
    ]); ?>
    <div class="flex-1 min-h-screen flex flex-col">
        <?php View::renderPartial('partials/admin-header'); ?>
        <main class="flex-1 p-8">
            <?php View::renderPartial($contentTemplate, $contentData ?? []); ?>
        </main>
    </div>
</body>
</html>
