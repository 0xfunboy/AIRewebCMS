<?php
/** @var array $faqs */
?>

<div class="space-y-12">
    <div data-animate>
        <?php \App\Core\View::renderPartial('partials/section-title', [
            'title' => 'Frequently Asked Questions',
            'subtitle' => 'Answers to the most common questions about AIRewardrop, AIR3, and our ecosystem.',
        ]); ?>
    </div>

    <div class="space-y-4" data-animate>
        <?php foreach ($faqs as $index => $faq): ?>
            <details class="bg-glass border border-stroke rounded-xl p-5" <?= $index === 0 ? 'open' : ''; ?>>
                <summary class="cursor-pointer text-lg font-semibold text-acc flex items-center justify-between">
                    <?= htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8'); ?>
                </summary>
                <p class="text-sm text-muted leading-relaxed mt-4">
                    <?= htmlspecialchars($faq['answer'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
            </details>
        <?php endforeach; ?>
    </div>
</div>
