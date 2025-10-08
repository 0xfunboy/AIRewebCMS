<?php
/** @var array $partners */

$active = array_values(array_filter($partners, fn ($partner) => ($partner['status'] ?? '') === 'Active'));
$discussion = array_values(array_filter($partners, fn ($partner) => ($partner['status'] ?? '') === 'In Discussion'));
?>

<div class="space-y-16">
    <div data-animate>
        <?php \App\Core\View::renderPartial('partials/section-title', [
            'title' => 'Trusted Collaborations',
            'subtitle' => 'We work with leading protocols, trading desks, and infrastructure teams across ecosystems.',
        ]); ?>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($active as $index => $partner): ?>
            <div data-animate data-animate-delay="<?= $index * 100; ?>">
                <a href="<?= htmlspecialchars($partner['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="block bg-glass border border-stroke rounded-lg p-6 text-center hover:border-pri/50 transition-all duration-300 transform hover:-translate-y-1 shadow-deep backdrop-blur-lg h-full">
                    <img loading="lazy" src="<?= htmlspecialchars($partner['logo_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($partner['name'], ENT_QUOTES, 'UTF-8'); ?>" class="h-16 mx-auto mb-4 object-contain" />
                    <h3 class="font-bold text-lg text-acc"><?= htmlspecialchars($partner['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="text-muted text-sm mt-2 leading-relaxed"><?= htmlspecialchars($partner['summary'], ENT_QUOTES, 'UTF-8'); ?></p>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($discussion): ?>
        <div data-animate class="mt-20">
            <h2 class="text-2xl font-bold text-acc mb-6 text-center">In Discussion</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 max-w-4xl mx-auto">
                <?php foreach ($discussion as $index => $partner): ?>
                    <div data-animate data-animate-delay="<?= $index * 100; ?>">
                        <a href="<?= htmlspecialchars($partner['url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="block bg-glass border border-dashed border-stroke rounded-lg p-6 text-center hover:border-pri/50 transition-all duration-300 transform hover:-translate-y-1 shadow-deep backdrop-blur-lg h-full">
                            <img loading="lazy" src="<?= htmlspecialchars($partner['logo_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($partner['name'], ENT_QUOTES, 'UTF-8'); ?>" class="h-16 mx-auto mb-4 object-contain" />
                            <div class="flex justify-center mb-3">
                                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-amber-400/20 text-amber-400 uppercase tracking-wide">Exploring</span>
                            </div>
                            <h3 class="font-bold text-lg text-acc mb-2"><?= htmlspecialchars($partner['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="text-muted text-sm leading-relaxed"><?= htmlspecialchars($partner['summary'], ENT_QUOTES, 'UTF-8'); ?></p>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>
