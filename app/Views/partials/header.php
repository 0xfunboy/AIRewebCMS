<?php
use App\Core\View;
use App\Support\AdminMode;

$settings = $settings ?? [];
$siteName = $settings['site_name'] ?? 'AIRewardrop';
$siteLogo = $siteLogo ?? '';
?>
<header class="site-header fixed top-0 left-0 right-0 z-50 bg-bg/80 backdrop-blur-lg border-b border-stroke transition-all">
    <div class="container mx-auto max-w-6xl px-4">
        <div class="flex h-20 items-center justify-between">
            <a href="/" class="flex items-center gap-2 text-xl font-bold text-acc">
                <?php if ($siteLogo): ?>
                    <img src="<?= htmlspecialchars($siteLogo, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>" class="h-9 w-auto"<?= AdminMode::dataAttrs('settings', 'site_logo', null, 'image'); ?>>
                <?php else: ?>
                    <span class="inline-flex h-9 w-9 items-center justify-center text-pri" data-alt="<?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>"<?= AdminMode::dataAttrs('settings', 'site_logo', null, 'image'); ?>>
                        <?php View::renderPartial('partials/logo', ['class' => 'h-8 w-8 text-pri']); ?>
                    </span>
                <?php endif; ?>
                <span<?= AdminMode::dataAttrs('settings', 'site_name'); ?><?= AdminMode::isAdmin() ? ' class="admin-editable-text"' : ''; ?>>
                    <?= htmlspecialchars($siteName, ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </a>
            <nav class="hidden md:flex items-center gap-6 text-sm font-semibold">
                <a href="/" class="text-txt hover:text-pri transition-colors">Home</a>
                <a href="/products" class="text-txt hover:text-pri transition-colors">Products</a>
                <a href="/agents" class="text-txt hover:text-pri transition-colors">Agents</a>
                <a href="/roadmap" class="text-txt hover:text-pri transition-colors">Roadmap</a>
                <a href="/partners" class="text-txt hover:text-pri transition-colors">Partners</a>
                <div class="relative" data-dropdown>
                    <button type="button" data-dropdown-toggle class="flex items-center gap-1 text-txt hover:text-pri transition-colors">
                        More
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    <div class="hidden absolute right-0 mt-3 w-52 rounded-lg border border-stroke bg-bg2 shadow-deep" data-dropdown-panel>
                        <a href="/clients" class="block px-4 py-2 text-sm text-txt hover:bg-stroke hover:text-pri transition">Clients</a>
                        <a href="/team" class="block px-4 py-2 text-sm text-txt hover:bg-stroke hover:text-pri transition">Team</a>
                        <a href="/commands" class="block px-4 py-2 text-sm text-txt hover:bg-stroke hover:text-pri transition">User Manual</a>
                        <a href="/social-proof" class="block px-4 py-2 text-sm text-txt hover:bg-stroke hover:text-pri transition">Social Proof</a>
                        <a href="/faq" class="block px-4 py-2 text-sm text-txt hover:bg-stroke hover:text-pri transition">FAQ</a>
                    </div>
                </div>
            </nav>
            <div class="hidden md:flex items-center gap-4">
                <a href="/login" class="bg-pri text-white font-bold py-2 px-4 rounded-md hover:bg-pri-700 transition-transform ease-in-out duration-200 hover:-translate-y-0.5">
                    Reserved Area
                </a>
            </div>
            <div class="md:hidden">
                <button type="button" class="text-txt" data-toggle-mobile-nav>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
    <div class="md:hidden hidden border-t border-stroke bg-bg2" data-mobile-nav>
        <nav class="flex flex-col gap-4 px-4 py-6 text-sm font-semibold text-center">
            <a href="/" class="text-txt hover:text-pri transition-colors">Home</a>
            <a href="/products" class="text-txt hover:text-pri transition-colors">Products</a>
            <a href="/agents" class="text-txt hover:text-pri transition-colors">Agents</a>
            <a href="/roadmap" class="text-txt hover:text-pri transition-colors">Roadmap</a>
            <a href="/partners" class="text-txt hover:text-pri transition-colors">Partners</a>
            <a href="/clients" class="text-txt hover:text-pri transition-colors">Clients</a>
            <a href="/team" class="text-txt hover:text-pri transition-colors">Team</a>
            <a href="/commands" class="text-txt hover:text-pri transition-colors">User Manual</a>
            <a href="/social-proof" class="text-txt hover:text-pri transition-colors">Social Proof</a>
            <a href="/faq" class="text-txt hover:text-pri transition-colors">FAQ</a>
            <a href="/login" class="mt-4 bg-pri text-white font-bold py-3 rounded-md hover:bg-pri-700 transition-colors">Reserved Area</a>
        </nav>
    </div>
</header>
