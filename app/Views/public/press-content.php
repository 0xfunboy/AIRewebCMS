<?php
/** @var array $assets */
?>

<div class="space-y-16">
    <div data-animate>
        <?php \App\Core\View::renderPartial('partials/section-title', [
            'title' => 'Press Kit',
            'subtitle' => 'Resources for media, partners, and anyone looking to feature AIRewardrop. For press inquiries, please contact us.',
        ]); ?>
    </div>

    <section class="max-w-3xl mx-auto bg-glass border border-stroke rounded-lg p-8 shadow-deep backdrop-blur" data-animate>
        <?php if (empty($assets)): ?>
            <p class="text-muted text-sm">Press assets will be available soon. Reach out to <a href="mailto:press@airewardrop.xyz" class="text-cy hover:underline">press@airewardrop.xyz</a> for immediate requests.</p>
        <?php else: ?>
            <ul class="divide-y divide-stroke">
                <?php foreach ($assets as $asset): ?>
                    <li class="flex flex-col md:flex-row md:items-center justify-between py-4 gap-4">
                        <div>
                            <p class="font-bold text-acc"><?= htmlspecialchars($asset['asset_type'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="text-muted text-sm"><?= htmlspecialchars($asset['label'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </div>
                        <a href="<?= htmlspecialchars($asset['file_path'], ENT_QUOTES, 'UTF-8'); ?>" download class="inline-flex items-center gap-2 bg-pri/20 text-pri font-semibold py-2 px-4 rounded-md text-sm hover:bg-pri/35 transition-colors">
                            <span>Download</span>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</div>
