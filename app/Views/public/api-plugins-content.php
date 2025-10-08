<?php
$features = [
    'Real-time price and data feeds',
    'On-demand chart generation endpoints',
    'Sentiment analysis scores',
    'Custom agent command integration',
    'White-label agent process',
];
?>

<div class="space-y-16">
    <div data-animate>
        <?php \App\Core\View::renderPartial('partials/section-title', [
            'title' => 'Integrate Our Agent Infrastructure',
            'subtitle' => 'Leverage our powerful data and analytics engine within your own dApp, platform, or community.',
        ]); ?>
    </div>

    <section class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8 items-stretch" data-animate>
        <div class="bg-glass border border-stroke rounded-lg p-8 shadow-deep space-y-6">
            <h3 class="text-2xl font-bold text-acc">Partner Toolkit</h3>
            <p class="text-muted text-sm leading-relaxed">
                We offer a range of integration options, from simple API plugins for data to full white-label deployments of our agent framework. If you&rsquo;re building in Web3, our tools can give you a competitive edge.
            </p>
            <ul class="space-y-3">
                <?php foreach ($features as $feature): ?>
                    <li class="flex items-center gap-3 text-sm">
                        <span class="text-cy">✔</span>
                        <span class="text-txt"><?= htmlspecialchars($feature, ENT_QUOTES, 'UTF-8'); ?></span>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="bg-bg2 border border-stroke rounded-lg p-8 text-center flex flex-col justify-center gap-6 shadow-deep" data-animate data-animate-delay="140">
            <div>
                <h4 class="text-xl font-bold text-acc">Ready to Build?</h4>
                <p class="text-muted text-sm mt-2">Let&rsquo;s discuss how our technology can accelerate your project&rsquo;s growth.</p>
            </div>
            <a href="/contact" class="inline-flex items-center justify-center bg-pri text-white font-semibold py-3 px-6 rounded-md hover:bg-pri-700 transition-transform duration-200 hover:-translate-y-0.5">
                Contact Our Team
            </a>
        </div>
    </section>
</div>
