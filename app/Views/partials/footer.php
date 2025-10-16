<?php
use App\Support\Media;

$navigation = $navigation ?? [];
$groupMap = [];
foreach ($navigation as $group) {
    if (empty($group['group_key'])) {
        continue;
    }
    $groupMap[$group['group_key']] = $group;
}

$defaultColumns = [
    'footer_navigate' => [
        'title' => 'Navigate',
        'items' => [
            ['label' => 'Products', 'url' => '/products'],
            ['label' => 'Agents', 'url' => '/agents'],
            ['label' => 'Roadmap', 'url' => '/roadmap'],
            ['label' => 'Clients', 'url' => '/clients'],
        ],
    ],
    'footer_resources' => [
        'title' => 'Resources',
        'items' => [
            ['label' => 'User Manual', 'url' => '/commands'],
            ['label' => 'Tokenomics', 'url' => '/tokenomics'],
            ['label' => 'Social Proof', 'url' => '/social-proof'],
            ['label' => 'Transparency', 'url' => '/transparency'],
            ['label' => 'API & Plugins', 'url' => '/api-plugins'],
            ['label' => 'Press Kit', 'url' => '/press'],
            ['label' => 'FAQ', 'url' => '/faq'],
        ],
    ],
    'footer_community' => [
        'title' => 'Community',
        'items' => [
            ['label' => 'Telegram Channel', 'url' => 'https://t.me/AIRewardrop', 'is_external' => true],
            ['label' => 'Telegram Community', 'url' => 'https://t.me/AIR3Community', 'is_external' => true],
            ['label' => 'Discord', 'url' => 'https://discord.gg/S4f87VdsHt', 'is_external' => true],
        ],
    ],
    'footer_legal' => [
        'title' => 'Legal',
        'items' => [
            ['label' => 'Terms of Service', 'url' => '/legal'],
            ['label' => 'Privacy Policy', 'url' => '/legal'],
            ['label' => 'Cookie Policy', 'url' => '/legal'],
        ],
    ],
];

$defaultSocial = [
    ['label' => 'X / Twitter', 'url' => 'https://x.com/AIRewardrop', 'icon_key' => 'twitter', 'is_external' => true],
    ['label' => 'Telegram', 'url' => 'https://t.me/AIR3Community', 'icon_key' => 'telegram', 'is_external' => true],
    ['label' => 'Discord', 'url' => 'https://discord.gg/S4f87VdsHt', 'icon_key' => 'discord', 'is_external' => true],
    ['label' => 'YouTube', 'url' => 'https://www.youtube.com/@AIRewardrop', 'icon_key' => 'youtube', 'is_external' => true],
    ['label' => 'Twitch', 'url' => 'https://www.twitch.tv/airewardrop', 'icon_key' => 'twitch', 'is_external' => true],
    ['label' => 'TikTok', 'url' => 'https://www.tiktok.com/@airewardrop', 'icon_key' => 'tiktok', 'is_external' => true],
    ['label' => 'Instagram', 'url' => 'https://www.instagram.com/airewardrop/', 'icon_key' => 'instagram', 'is_external' => true],
];

$normalizeItems = static function (array $items): array {
    $normalized = [];
    foreach ($items as $item) {
        $normalized[] = [
            'label' => (string)($item['label'] ?? ''),
            'url' => (string)($item['url'] ?? '#'),
            'is_external' => !empty($item['is_external']),
            'icon_key' => $item['icon_key'] ?? null,
        ];
    }
    return $normalized;
};

$columnOrder = ['footer_navigate', 'footer_resources', 'footer_community', 'footer_legal'];
$columns = [];
foreach ($columnOrder as $key) {
    $default = $defaultColumns[$key];
    $group = $groupMap[$key] ?? null;
    $items = $group ? $normalizeItems($group['items'] ?? []) : [];
    if (empty($items)) {
        $items = $normalizeItems($default['items']);
    }
    $columns[] = [
        'title' => (string)($group['title'] ?? $default['title']),
        'items' => $items,
    ];
}

$socialGroup = $groupMap['footer_social']['items'] ?? [];
$socialLinks = $normalizeItems($socialGroup);
if (empty($socialLinks)) {
    $socialLinks = $normalizeItems($defaultSocial);
}

$siteLogoUrl = $siteLogo ?? '';
if ($siteLogoUrl === '') {
    $siteLogoUrl = Media::assetSvg('logo/site-logo.svg');
}
?>
<footer class="border-t border-stroke bg-bg2">
    <div class="container mx-auto max-w-6xl px-4 py-12">
        <div class="grid gap-8 md:grid-cols-3">
            <div class="flex flex-col items-start gap-4">
                <a href="/" class="flex items-center gap-2 text-xl font-bold text-acc">
                    <?php if ($siteLogoUrl !== ''): ?>
                        <img src="<?= htmlspecialchars($siteLogoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="AIRewardrop" class="h-8 w-auto">
                    <?php else: ?>
                        <?= icon_svg('logo', 'h-8 w-8 text-pri'); ?>
                    <?php endif; ?>
                    <span>AIRewardrop</span>
                </a>
                <p class="text-muted text-sm max-w-xs">
                    Autonomous agent infrastructure for crypto.
                </p>
                <a href="/blog" class="text-sm font-semibold text-txt hover:text-pri transition-colors">
                    Our Blog &rarr;
                </a>
            </div>
            <div class="md:col-span-2 grid grid-cols-2 sm:grid-cols-4 gap-8">
                <?php foreach ($columns as $column): ?>
                    <div>
                        <h3 class="font-bold text-acc mb-4"><?= htmlspecialchars($column['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <ul class="space-y-2">
                            <?php foreach ($column['items'] as $link): ?>
                                <?php if ($link['is_external']): ?>
                                    <li><a href="<?= htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="text-muted hover:text-pri text-sm"><?= htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8'); ?></a></li>
                                <?php else: ?>
                                    <li><a href="<?= htmlspecialchars($link['url'], ENT_QUOTES, 'UTF-8'); ?>" class="text-muted hover:text-pri text-sm"><?= htmlspecialchars($link['label'], ENT_QUOTES, 'UTF-8'); ?></a></li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="mt-12 pt-8 border-t border-stroke flex flex-col sm:flex-row justify-between items-center gap-4">
            <p class="text-muted text-xs text-center sm:text-left">
                &copy; <?= date('Y'); ?> AIRewardrop. All rights reserved.<br>
                Disclaimer: Not financial advice. Always do your own research.
            </p>
            <div class="flex items-center gap-4">
                <?php foreach ($socialLinks as $item): ?>
                    <?php $icon = $item['icon_key'] ?? ''; ?>
                    <a href="<?= htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>" class="text-muted hover:text-pri transition-colors">
                        <?= icon_svg($icon !== '' ? $icon : 'check-circle', 'h-6 w-6'); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</footer>
