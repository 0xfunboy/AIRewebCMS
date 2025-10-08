<?php
/** @var array $phases */
/** @var array $tracks */
/** @var string $vision */
?>

<div class="space-y-24">
    <div data-animate>
        <?php \App\Core\View::renderPartial('partials/section-title', [
            'title' => 'Company Roadmap',
            'subtitle' => 'Our transparent, multi-phase plan for building the world\'s leading autonomous agent infrastructure for crypto.',
        ]); ?>
    </div>

    <section class="max-w-4xl mx-auto text-center bg-glass border border-stroke rounded-2xl p-10 shadow-deep" data-animate>
        <h2 class="text-3xl md:text-4xl font-extrabold text-acc mb-4">Our Vision</h2>
        <p class="text-muted leading-relaxed whitespace-pre-line"><?= htmlspecialchars($vision, ENT_QUOTES, 'UTF-8'); ?></p>
    </section>

    <div class="space-y-16">
        <?php foreach ($phases as $index => $phase): ?>
            <?php
                $glow = '';
                $cardBorder = 'border-stroke';
                if ($index === 2) {
                    $glow = 'bg-pri/20';
                    $cardBorder = 'border-pri/30 shadow-[0_0_40px_rgba(240,58,58,0.35)]';
                } elseif ($index === 0 || $index === 1) {
                    $glow = 'bg-emerald-500/15';
                    $cardBorder = 'border-emerald-400/20 shadow-[0_0_30px_rgba(16,185,129,0.25)]';
                }
            ?>
            <section class="relative" data-animate data-animate-delay="<?= $index * 120 ?>">
                <?php if ($glow !== ''): ?>
                    <div class="absolute -inset-x-10 -inset-y-6 blur-3xl rounded-full -z-10 <?= $glow; ?>"></div>
                <?php endif; ?>

                <div class="text-center mb-10">
                    <h3 class="text-3xl font-extrabold text-acc tracking-tight"><?= htmlspecialchars($phase['phase_label'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="text-cy font-semibold mt-2"><?= htmlspecialchars($phase['timeline'], ENT_QUOTES, 'UTF-8'); ?></p>
                    <p class="text-muted max-w-2xl mx-auto mt-3 leading-relaxed"><?= htmlspecialchars($phase['goal'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>

                <div class="max-w-3xl mx-auto bg-glass border <?= $cardBorder; ?> rounded-2xl p-8 shadow-deep">
                    <ul class="space-y-6">
                        <?php foreach ($phase['items'] as $itemIndex => $item): ?>
                            <li class="flex items-start gap-4" data-animate data-animate-delay="<?= $itemIndex * 80; ?>">
                                <?= icon_svg('check-circle', 'h-6 w-6 flex-shrink-0 mt-1 text-pri'); ?>
                                <div>
                                    <h4 class="font-semibold text-acc text-lg"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></h4>
                                    <p class="text-muted text-sm leading-relaxed whitespace-pre-line"><?= htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </section>
        <?php endforeach; ?>
    </div>

    <?php if ($tracks): ?>
        <section class="max-w-3xl mx-auto bg-glass border border-stroke rounded-2xl p-8 shadow-deep" data-animate>
            <div class="text-center mb-8">
                <h3 class="text-3xl font-extrabold text-acc tracking-tight">Always-On Tracks</h3>
                <p class="text-muted max-w-2xl mx-auto mt-3">These are continuous workstreams that support the execution of every roadmap phase.</p>
            </div>
            <ul class="space-y-4">
                <?php foreach ($tracks as $itemIndex => $track): ?>
                    <li class="flex items-start gap-4" data-animate data-animate-delay="<?= $itemIndex * 80; ?>">
                        <?= icon_svg('check-circle', 'h-5 w-5 flex-shrink-0 mt-1 text-yl'); ?>
                        <p class="text-txt text-sm leading-relaxed"><?= htmlspecialchars($track, ENT_QUOTES, 'UTF-8'); ?></p>
                    </li>
                <?php endforeach; ?>
            </ul>
        </section>
    <?php endif; ?>
</div>
