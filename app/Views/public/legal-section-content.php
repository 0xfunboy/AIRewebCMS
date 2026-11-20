<?php
/** @var array $settings */
/** @var array $section */

$contactEmail = $settings['contact_email'] ?? 'legal@airewardrop.xyz';
$content = (string)($section['content_html'] ?? '');
$content = str_replace('{{contact_email}}', $contactEmail, $content);
?>

<div class="space-y-16">
    <div data-animate>
        <?php \App\Core\View::renderPartial('partials/section-title', [
            'title' => (string)($section['title'] ?? 'Legal'),
            'subtitle' => 'Legal documentation for the AIRewardrop ecosystem.',
        ]); ?>
    </div>

    <section class="max-w-4xl mx-auto text-sm leading-relaxed text-muted">
        <article class="bg-glass border border-stroke rounded-lg p-8 shadow-deep" data-animate>
            <div class="space-y-4 legal-section-content">
                <?= $content; ?>
            </div>
        </article>
    </section>
</div>
