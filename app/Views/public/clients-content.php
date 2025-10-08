<?php
/** @var array $caseStudies */
?>

<div class="space-y-16">
    <div data-animate>
        <?php \App\Core\View::renderPartial('partials/section-title', [
            'title' => 'Client Success Stories',
            'subtitle' => 'Deliverables that proved our framework across chains, communities, and partner ecosystems.',
        ]); ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($caseStudies as $index => $study): ?>
            <div data-animate data-animate-delay="<?= $index * 120; ?>">
                <div class="bg-glass border border-stroke rounded-lg overflow-hidden shadow-deep backdrop-blur-lg h-full flex flex-col">
                    <img loading="lazy" src="<?= htmlspecialchars($study['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($study['title'], ENT_QUOTES, 'UTF-8'); ?>" class="w-full h-56 object-cover" />
                    <div class="p-6 flex flex-col space-y-3 flex-grow">
                        <div class="text-sm font-semibold text-cy uppercase tracking-wide"><?= htmlspecialchars($study['chain'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <h3 class="text-xl font-bold text-acc"><?= htmlspecialchars($study['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                        <p class="text-muted text-sm leading-relaxed flex-grow"><?= htmlspecialchars($study['summary'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <div class="text-sm text-muted">Client: <span class="text-txt font-semibold"><?= htmlspecialchars($study['client'], ENT_QUOTES, 'UTF-8'); ?></span></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
