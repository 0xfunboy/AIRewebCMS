<?php
/** @var array $commands */

$v4Features = [
    ['title' => 'Boosted Token Scanners', 'description' => 'Instantly spot tokens accelerating on volume, hype, and momentum.'],
    ['title' => 'Real-time Prices', 'description' => 'Live quotes for any token (by ticker or contract address) enriched with a short analytical read.'],
    ['title' => 'Ready-to-use Technical Charts', 'description' => 'Auto-generated charts with key indicators, perfect for reports and quick posts.'],
    ['title' => 'Instant Fundamental Snapshots', 'description' => 'Market cap, supply, liquidity, and core metrics at a glance.'],
    ['title' => 'Deep Asset & Trend Analysis', 'description' => 'Blends on-chain, trading, and sentiment data for a complete forward/backward look.'],
    ['title' => 'Trending & Social Mentions', 'description' => 'See which tokens dominate the conversation and where they’re spiking.'],
    ['title' => 'Live Market Sentiment', 'description' => 'Millions of posts parsed to give you a real-time thermometer of investor mood.'],
    ['title' => 'Breaking News Feed', 'description' => 'Curated updates that can actually move the market.'],
    ['title' => 'Fresh Listings Detector', 'description' => 'Get pinged when a token lists to gain a crucial time edge.'],
    ['title' => 'Capital Flow & Volume Heatmap', 'description' => 'Follow liquidity rotations across chains and venues to catch pivots early.'],
    ['title' => 'Dashboards', 'description' => 'Cross-reference social buzz with trading volume to surface the assets that matter now.'],
];

$upcoming = [
    ['title' => 'Stake AIR3', 'description' => 'Invite a custom community agent into your Discord server or Telegram group.'],
    ['title' => 'Pay per feature', 'description' => 'Unlock pro analytics, scheduled posts, voice reads, and premium reports.'],
    ['title' => 'Burn AIR3', 'description' => 'Deploy white-label agents for partner projects.'],
    ['title' => 'Private instances', 'description' => 'Personal companions with dedicated memory and filtered outputs for holders.'],
];
?>

<div class="space-y-16">
    <div data-animate>
        <?php \App\Core\View::renderPartial('partials/section-title', [
            'title' => 'User Manual',
            'subtitle' => 'A complete guide to using the AIR3 V4 Engine, from baseline commands to the upcoming dApp rollout.',
        ]); ?>
    </div>

    <section class="max-w-4xl mx-auto space-y-16" data-animate>
        <div class="text-center space-y-4">
            <h2 class="text-2xl font-bold text-acc">What Makes AIR3 Special?</h2>
            <p class="text-muted max-w-3xl mx-auto">AIR3 combines innovation, community closeness, fast insights, and transparent growth. It’s more than a bot; it’s a 24/7 market analyst with cutting-edge AI, live interactions, and a hyper-realistic Metahuman avatar.</p>
        </div>

        <div>
            <h2 class="text-2xl font-bold text-acc mb-6 text-center">Key Features (Engine V4)</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($v4Features as $index => $feature): ?>
                    <div class="bg-glass border border-stroke rounded-lg p-6" data-animate data-animate-delay="<?= $index * 80; ?>">
                        <h3 class="font-semibold text-acc"><?= htmlspecialchars($feature['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="text-muted text-sm mt-2 leading-relaxed"><?= htmlspecialchars($feature['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div>
            <h2 class="text-2xl font-bold text-acc mb-6 text-center">How to Use: Query Syntax</h2>
            <div class="bg-glass border border-stroke rounded-lg p-6 space-y-6">
                <p class="text-muted text-center">Tag <code class="bg-bg2 px-2 py-1 rounded text-pri font-semibold">@AIRewardrop</code> on X / Twitter, seguito da un verbo d’azione e da un <code class="bg-bg2 px-2 py-1 rounded text-yl font-semibold">$TICKER</code> o indirizzo del contratto.</p>
                <div class="bg-bg2 p-6 rounded-lg font-mono text-sm space-y-4">
                    <div>
                        <p class="text-gray-400">// Request a 1-hour chart for $CRV</p>
                        <p><span class="text-pri">@AIRewardrop</span> <span class="text-cy">chart</span> <span class="text-yl">$CRV 1h</span></p>
                    </div>
                    <div>
                        <p class="text-gray-400">// Get fundamental data for $XRP</p>
                        <p><span class="text-pri">@AIRewardrop</span> <span class="text-cy">fundamental</span> <span class="text-yl">$XRP</span></p>
                    </div>
                    <div>
                        <p class="text-gray-400">// Analyze a token via contract address</p>
                        <p><span class="text-pri">@AIRewardrop</span> <span class="text-cy">analysis</span> <span class="text-yl">0x...</span></p>
                    </div>
                </div>
                <div class="text-center text-sm text-muted">
                    Common action words: <code class="bg-bg2 px-1 rounded">chart</code>, <code class="bg-bg2 px-1 rounded">analysis</code>, <code class="bg-bg2 px-1 rounded">price</code>, <code class="bg-bg2 px-1 rounded">sentiment</code>, <code class="bg-bg2 px-1 rounded">news</code>, <code class="bg-bg2 px-1 rounded">audit</code>, <code class="bg-bg2 px-1 rounded">fundamental</code>, <code class="bg-bg2 px-1 rounded">rise in mention</code>, <code class="bg-bg2 px-1 rounded">most mentioned</code>, e molti altri.
                </div>
            </div>
        </div>

        <div>
            <h2 class="text-2xl font-bold text-acc mb-6 text-center">Command Reference</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <?php foreach ($commands as $index => $command): ?>
                    <div class="bg-glass border border-stroke rounded-lg p-5" data-animate data-animate-delay="<?= $index * 60; ?>">
                        <h3 class="font-semibold text-acc"><?= htmlspecialchars($command['command'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="text-muted text-sm mt-2 leading-relaxed"><?= htmlspecialchars($command['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div>
            <h2 class="text-2xl font-bold text-acc mb-6 text-center">What’s Next: The dApp Rollout</h2>
            <div class="bg-glass border border-stroke rounded-lg p-6 space-y-6">
                <div>
                    <h3 class="font-bold text-lg text-acc">AIRtrak: Verifiable Trading</h3>
                    <p class="text-muted text-sm mt-2 leading-relaxed">AIRtrak è il primo modulo: un web app che rende verificabile il trading del nostro agente con posizioni attive/pending, PnL, win-rate e report condivisibili.</p>
                </div>
                <div class="border-t border-stroke"></div>
                <div>
                    <h3 class="font-bold text-lg text-acc">Road to the Vault: Utility Expansion</h3>
                    <p class="text-muted text-sm mt-2 mb-4 leading-relaxed">Dopo AIRtrak, una sequenza di release porterà online l’intero stack utility del token:</p>
                    <ul class="space-y-3">
                        <?php foreach ($upcoming as $feature): ?>
                            <li class="flex items-start gap-3">
                                <?= icon_svg('check-circle', 'h-5 w-5 text-cy flex-shrink-0 mt-1'); ?>
                                <div>
                                    <strong class="text-txt text-sm"><?= htmlspecialchars($feature['title'], ENT_QUOTES, 'UTF-8'); ?>:</strong>
                                    <span class="text-muted text-sm ml-1"><?= htmlspecialchars($feature['description'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>
