<?php
$features = [
    [
        'title' => 'Access Key',
        'description' => 'AIR3 is the access key for premium features. Hold to use, lock or stake to unlock advanced capabilities.'
    ],
    [
        'title' => 'Utility Driven',
        'description' => 'Pay for specific services, access marketplace credits, and participate in a feature-rich ecosystem.'
    ],
    [
        'title' => 'Deflationary by Design',
        'description' => 'Revenue from services and scheduled operations support a buyback & burn mechanism, creating a sustainable loop.'
    ],
];
?>

<div class="space-y-16">
    <div data-animate>
        <?php \App\Core\View::renderPartial('partials/section-title', [
            'title' => 'AIR3 Tokenomics',
            'subtitle' => 'A utility token designed for long-term sustainability and ecosystem growth.',
        ]); ?>
    </div>

    <section class="max-w-5xl mx-auto space-y-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6" data-animate>
            <?php foreach ($features as $index => $feature): ?>
                <div class="bg-glass border border-stroke rounded-lg p-6 text-center shadow-deep" data-animate data-animate-delay="<?= $index * 80; ?>">
                    <div class="h-12 w-12 mx-auto mb-4 rounded-full bg-pri/10 flex items-center justify-center text-pri text-lg font-bold">
                        <?= htmlspecialchars(substr($feature['title'], 0, 1), ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <h3 class="text-xl font-bold text-acc mb-2"><?= htmlspecialchars($feature['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="text-muted text-sm leading-relaxed"><?= htmlspecialchars($feature['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="bg-glass border border-stroke rounded-lg p-8 shadow-deep backdrop-blur-lg" data-animate data-animate-delay="200">
            <div class="flex flex-col md:flex-row items-center text-center md:text-left gap-6">
                <div class="h-16 w-16 rounded-full bg-pri/10 flex items-center justify-center text-pri text-2xl font-bold">🔥</div>
                <div class="space-y-3">
                    <h3 class="text-2xl font-bold text-acc">Buyback & Burn Mechanism</h3>
                    <p class="text-muted text-sm leading-relaxed">
                        A portion of all revenue generated across the AIRewardrop ecosystem—from white-label service fees, marketplace transactions, and dApp module usage—is used to buy back $AIR3 tokens on the open market. These tokens are then permanently removed from circulation (burned), reducing the total supply over time.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-glass border border-stroke rounded-lg p-10 text-center shadow-deep" data-animate data-animate-delay="320">
            <h3 class="text-2xl font-bold text-acc">Sustainable Loop</h3>
            <p class="text-muted mt-4 max-w-3xl mx-auto leading-relaxed">
                With a fixed supply and ever-increasing utility through new products and services, the AIR3 token is engineered for a healthy, self-sustaining economic model. In partnership with platforms like <a href="https://meteora.ag" class="text-cy hover:underline">Meteora</a>, we ensure deep liquidity and reward our holders.
            </p>
            <p class="text-xs text-muted/60 mt-6">
                Disclaimer: This is not financial advice. The AIR3 token is a utility token for accessing services within the AIRewardrop ecosystem. Always do your own research (DYOR).
            </p>
        </div>
    </section>
</div>
