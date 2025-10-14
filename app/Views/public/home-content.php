<?php
use App\Core\View;
use App\Support\AdminMode;
use App\Support\Media;

/** @var array $settings */
/** @var array $products */
/** @var array $agents */
/** @var array $partners */

$tagline = $settings['site_tagline'] ?? 'Autonomous Agent Infrastructure for Crypto';
$heroTitle = $settings['hero_title_home'] ?? $tagline;
$heroSubtitle = $settings['hero_subtitle_home'] ?? 'AIRewardrop designs, ships, and operates always-on agents across multiple chains - live charts, on-chain analytics, and tokenized dApps. We’re the builders behind AIR3 and the Agent Swarm.';
$heroImageSetting = trim((string)($settings['hero_image_home'] ?? ''));
if ($heroImageSetting === '') {
    $heroImage = Media::assetSvg('hero/hero-default.svg');
} elseif (str_starts_with($heroImageSetting, 'http://') || str_starts_with($heroImageSetting, 'https://')) {
    $heroImage = $heroImageSetting;
} else {
    $heroImage = Media::normalizeMediaPath($heroImageSetting);
}
$heroBadge = $settings['hero_badge_home'] ?? '#4 Most Credible Agent by Ethos Network (Q4 2025).';

$productsList = array_values($products);
$liveAgents = array_values(array_filter($agents, fn ($agent) => ($agent['status'] ?? '') === 'Live'));
$activePartners = array_values(array_filter($partners, fn ($partner) => ($partner['status'] ?? '') === 'Active'));
?>

<div class="space-y-24 md:space-y-32">
    <section class="pt-8 md:pt-16" data-animate>
        <div class="grid md:grid-cols-2 gap-8 items-center">
            <div class="text-center md:text-left">
                <p class="text-sm font-bold text-pri tracking-widest uppercase"<?= AdminMode::dataAttrs('settings', 'site_tagline'); ?><?= AdminMode::isAdmin() ? ' class="admin-editable-text"' : ''; ?>>
                    <?= htmlspecialchars($tagline, ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <h1 class="mt-4 text-4xl md:text-5xl font-extrabold text-acc tracking-tighter leading-tight"<?= AdminMode::dataAttrs('settings', 'hero_title_home'); ?><?= AdminMode::isAdmin() ? ' class="admin-editable-text"' : ''; ?>>
                    <?= htmlspecialchars($heroTitle, ENT_QUOTES, 'UTF-8'); ?>
                </h1>
                <p class="mt-6 max-w-xl mx-auto md:mx-0 text-lg text-muted"<?= AdminMode::dataAttrs('settings', 'hero_subtitle_home'); ?><?= AdminMode::isAdmin() ? ' class="admin-editable-text"' : ''; ?>>
                    <?= htmlspecialchars($heroSubtitle, ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <div class="mt-8 flex flex-col sm:flex-row justify-center md:justify-start gap-4">
                    <a href="/products" class="bg-pri text-white font-bold py-3 px-6 rounded-md hover:bg-pri-700 transition-all duration-200 ease-in-out hover:-translate-y-0.5">
                        View Products
                    </a>
                    <a href="/contact" class="bg-glass border border-stroke text-white font-bold py-3 px-6 rounded-md hover:bg-stroke transition-colors">
                        Talk to Our Team
                    </a>
                </div>
            </div>
            <div>
                <img src="<?= htmlspecialchars($heroImage, ENT_QUOTES, 'UTF-8'); ?>" alt="AI Agent" class="rounded-lg shadow-deep mx-auto" loading="lazy"<?= AdminMode::dataAttrs('settings', 'hero_image_home', null, 'image'); ?>>
            </div>
        </div>
    </section>

    <div class="text-center bg-glass border border-stroke rounded-lg p-4" data-animate>
        <p class="text-sm text-muted">
            <span class="font-bold text-yl"<?= AdminMode::dataAttrs('settings', 'hero_badge_home'); ?><?= AdminMode::isAdmin() ? ' class="admin-editable-text"' : ''; ?>>
                <?= htmlspecialchars($heroBadge, ENT_QUOTES, 'UTF-8'); ?>
            </span>
        </p>
    </div>

    <section data-animate>
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold text-acc tracking-tight">What We Build</h2>
            <p class="mt-4 max-w-2xl mx-auto text-muted">Our stack is designed to deliver a complete, end-to-end agent ecosystem.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-glass border border-stroke rounded-lg p-6 shadow-deep backdrop-blur-lg" data-animate>
                <?= icon_svg('chip', 'h-8 w-8 text-pri mb-4'); ?>
                <h3 class="font-bold text-lg text-acc">Live Intelligence</h3>
                <p class="text-muted text-sm mt-2">Agents that read social sentiment, analyze on-chain data, and deliver real-time insights via API or social commands.</p>
            </div>
            <div class="bg-glass border border-stroke rounded-lg p-6 shadow-deep backdrop-blur-lg" data-animate>
                <?= icon_svg('beaker', 'h-8 w-8 text-pri mb-4'); ?>
                <h3 class="font-bold text-lg text-acc">On-Chain Actions</h3>
                <p class="text-muted text-sm mt-2">Execution bots, staking vaults, and other dApp modules that allow agents to act on their analysis directly on-chain.</p>
            </div>
            <div class="bg-glass border border-stroke rounded-lg p-6 shadow-deep backdrop-blur-lg" data-animate>
                <?= icon_svg('sparkles', 'h-8 w-8 text-pri mb-4'); ?>
                <h3 class="font-bold text-lg text-acc">Verifiable Results</h3>
                <p class="text-muted text-sm mt-2">Tools like AIRtrak provide a transparent, immutable record of agent and trader performance, building trust through data.</p>
            </div>
        </div>
    </section>

    <section data-animate>
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold text-acc tracking-tight">Our Product Suite</h2>
            <p class="mt-4 max-w-2xl mx-auto text-muted">A comprehensive toolkit for the modern Web3 ecosystem.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach (array_slice($productsList, 0, 6) as $index => $product): ?>
                <div data-animate data-animate-delay="<?= $index * 100 ?>" class="h-full">
                    <?php if (!empty($product['external_link'])): ?>
                        <a href="<?= htmlspecialchars($product['external_link'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="block h-full">
                            <?php View::renderPartial('public/partials/product-card', ['product' => $product]); ?>
                        </a>
                    <?php else: ?>
                        <a href="/products/<?= htmlspecialchars($product['slug'], ENT_QUOTES, 'UTF-8'); ?>" class="block h-full">
                            <?php View::renderPartial('public/partials/product-card', ['product' => $product]); ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-8">
            <a href="/products" class="text-pri font-semibold hover:underline">
                Explore all products &rarr;
            </a>
        </div>
    </section>

    <section data-animate>
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold text-acc tracking-tight">Agents Live in the Wild</h2>
            <p class="mt-4 max-w-2xl mx-auto text-muted">Our agent framework is already deployed and active across multiple chains.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <?php foreach ($liveAgents as $index => $agent): ?>
                <div class="bg-glass border border-stroke rounded-lg overflow-hidden transition-all duration-300 transform hover:-translate-y-1 shadow-deep backdrop-blur-lg hover:border-pri/50" data-animate data-animate-delay="<?= $index * 100 ?>">
                    <img loading="lazy" src="<?= htmlspecialchars($agent['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($agent['name'], ENT_QUOTES, 'UTF-8'); ?>" class="w-full h-48 object-cover" />
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-xl text-acc"><?= htmlspecialchars($agent['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full bg-green-500/20 text-green-400">Live</span>
                        </div>
                        <div class="text-sm font-semibold text-cy mb-3"><?= htmlspecialchars($agent['chain'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <a href="<?= htmlspecialchars($agent['site_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="bg-pri/20 text-pri font-bold py-2 px-4 rounded-md text-sm hover:bg-pri/40 transition-colors w-full block text-center">
                            Visit Agent
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section data-animate>
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-extrabold text-acc tracking-tight">Trusted By The Best</h2>
            <p class="mt-4 max-w-2xl mx-auto text-muted">We collaborate with leading projects and platforms to push the boundaries of Web3.</p>
            <?php if (AdminMode::isAdmin()): ?>
                <a href="/admin/partners" class="inline-flex items-center gap-2 mt-4 text-sm font-semibold text-cy hover:text-pri transition-colors">
                    Manage partners →
                </a>
            <?php endif; ?>
        </div>
        <div class="flex flex-wrap justify-center items-center gap-x-12 gap-y-6">
            <?php foreach ($activePartners as $partner): ?>
                <?php
                $badgeLogo = $partner['badge_logo_url'] ?? '';
                if ($badgeLogo === '') {
                    $badgeLogo = $partner['logo_url'] ?? '';
                }
                ?>
                <a href="<?= htmlspecialchars($partner['url'], ENT_QUOTES, 'UTF-8'); ?>"
                   target="_blank"
                   rel="noopener noreferrer"
                   title="<?= htmlspecialchars($partner['name'], ENT_QUOTES, 'UTF-8'); ?>"
                   <?= AdminMode::dataAttrs('partners', 'url', $partner['id'], 'url'); ?>>
                    <img loading="lazy"
                         src="<?= htmlspecialchars($badgeLogo, ENT_QUOTES, 'UTF-8'); ?>"
                         alt="<?= htmlspecialchars($partner['name'], ENT_QUOTES, 'UTF-8'); ?>"
                         class="h-10 object-contain grayscale opacity-60 hover:grayscale-0 hover:opacity-100 transition-all"
                         <?= AdminMode::dataAttrs('partners', 'badge_logo_url', $partner['id'], 'image'); ?> />
                </a>
            <?php endforeach; ?>
        </div>
    </section>
</div>
