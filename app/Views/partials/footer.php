<?php
$columns = [
    [
        'title' => 'Navigate',
        'links' => [
            ['label' => 'Products', 'url' => '/products'],
            ['label' => 'Agents', 'url' => '/agents'],
            ['label' => 'Roadmap', 'url' => '/roadmap'],
            ['label' => 'Clients', 'url' => '/clients'],
        ],
    ],
    [
        'title' => 'Resources',
        'links' => [
            ['label' => 'User Manual', 'url' => '/commands'],
            ['label' => 'Tokenomics', 'url' => '/tokenomics'],
            ['label' => 'Social Proof', 'url' => '/social-proof'],
            ['label' => 'Transparency', 'url' => '/transparency'],
            ['label' => 'API & Plugins', 'url' => '/api-plugins'],
            ['label' => 'Press Kit', 'url' => '/press'],
            ['label' => 'FAQ', 'url' => '/faq'],
        ],
    ],
    [
        'title' => 'Community',
        'links' => [
            ['label' => 'Telegram Channel', 'url' => 'https://t.me/AIRewardrop', 'external' => true],
            ['label' => 'Telegram Community', 'url' => 'https://t.me/AIR3Community', 'external' => true],
            ['label' => 'Discord', 'url' => 'https://discord.gg/S4f87VdsHt', 'external' => true],
        ],
    ],
    [
        'title' => 'Legal',
        'links' => [
            ['label' => 'Terms of Service', 'url' => '/legal'],
            ['label' => 'Privacy Policy', 'url' => '/legal'],
            ['label' => 'Cookie Policy', 'url' => '/legal'],
        ],
    ],
];

$social = [
    ['name' => 'X / Twitter', 'icon' => 'twitter', 'url' => 'https://x.com/AIRewardrop'],
    ['name' => 'Telegram', 'icon' => 'telegram', 'url' => 'https://t.me/AIR3Community'],
    ['name' => 'Discord', 'icon' => 'discord', 'url' => 'https://discord.gg/S4f87VdsHt'],
    ['name' => 'YouTube', 'icon' => 'youtube', 'url' => 'https://www.youtube.com/@AIRewardrop'],
    ['name' => 'Twitch', 'icon' => 'twitch', 'url' => 'https://www.twitch.tv/airewardrop'],
    ['name' => 'TikTok', 'icon' => 'tiktok', 'url' => 'https://www.tiktok.com/@airewardrop'],
    ['name' => 'Instagram', 'icon' => 'instagram', 'url' => 'https://www.instagram.com/airewardrop/'],
];
?>
<footer class="border-t border-stroke bg-bg2">
    <div class="container mx-auto max-w-6xl px-4 py-12">
        <div class="grid gap-8 md:grid-cols-3">
            <div class="flex flex-col items-start gap-4">
                <a href="/" class="flex items-center gap-2 text-xl font-bold text-acc">
                    <?= icon_svg('logo', 'h-8 w-8 text-pri'); ?>
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
                            <?php foreach ($column['links'] as $link): ?>
                                <?php if (!empty($link['external'])): ?>
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
                <?php foreach ($social as $item): ?>
                    <a href="<?= htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8'); ?>" class="text-muted hover:text-pri transition-colors">
                        <?= icon_svg($item['icon'], 'h-6 w-6'); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</footer>
