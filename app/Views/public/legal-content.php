<?php
/** @var array $settings */
/** @var array $sections */

$contactEmail = $settings['contact_email'] ?? 'legal@airewardrop.xyz';
$sections = $sections ?? [];
?>

<div class="space-y-16">
    <div data-animate>
        <?php \App\Core\View::renderPartial('partials/section-title', [
            'title' => 'Legal Center',
            'subtitle' => 'Terms, privacy, and cookie policies for the AIRewardrop ecosystem.',
        ]); ?>
    </div>

    <section class="space-y-12 max-w-4xl mx-auto text-sm leading-relaxed text-muted">
        <?php if (empty($sections)): ?>
            <article class="bg-glass border border-stroke rounded-lg p-8 shadow-deep" data-animate>
                <h2 class="text-2xl font-bold text-acc mb-4">Legal content coming soon</h2>
                <p class="mb-0">Our legal documentation is being prepared. For urgent requests reach out to <a href="mailto:<?= htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8'); ?>" class="text-cy hover:underline"><?= htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8'); ?></a>.</p>
            </article>
        <?php else: ?>
            <?php foreach ($sections as $index => $section): ?>
                <?php
                $content = (string)($section['content_html'] ?? '');
                $content = str_replace('{{contact_email}}', $contactEmail, $content);
                ?>
                <article class="bg-glass border border-stroke rounded-lg p-8 shadow-deep" data-animate data-animate-delay="<?= $index * 120; ?>">
                    <h2 class="text-2xl font-bold text-acc mb-4"><?= htmlspecialchars($section['title'], ENT_QUOTES, 'UTF-8'); ?></h2>
                    <div class="space-y-4 legal-section-content">
                        <?= $content; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </section>
</div>
