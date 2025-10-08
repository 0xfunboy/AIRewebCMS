<?php
/** @var array $team */
?>

<div class="space-y-16">
    <div data-animate>
        <?php \App\Core\View::renderPartial('partials/section-title', [
            'title' => 'The Builders',
            'subtitle' => 'Cross-disciplinary founders and operators delivering AI agents, dApps, and trading infrastructure.',
        ]); ?>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <?php foreach ($team as $index => $member): ?>
            <div class="bg-glass border border-stroke rounded-lg p-6 text-center space-y-4 shadow-deep backdrop-blur-lg" data-animate data-animate-delay="<?= $index * 100 ?>">
                <img loading="lazy" src="<?= htmlspecialchars($member['avatar_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8'); ?>" class="h-28 w-28 rounded-full mx-auto object-cover border border-stroke" />
                <div>
                    <h3 class="text-xl font-bold text-acc"><?= htmlspecialchars($member['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                    <p class="text-sm text-cy font-semibold mt-1"><?= htmlspecialchars($member['role'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
                <p class="text-sm text-muted leading-relaxed">
                    <?= htmlspecialchars($member['bio'], ENT_QUOTES, 'UTF-8'); ?>
                </p>
                <div class="flex justify-center gap-4 text-sm">
                    <?php if (!empty($member['telegram_url'])): ?>
                        <a href="<?= htmlspecialchars($member['telegram_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="text-muted hover:text-pri transition">Telegram</a>
                    <?php endif; ?>
                    <?php if (!empty($member['x_url'])): ?>
                        <a href="<?= htmlspecialchars($member['x_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="text-muted hover:text-pri transition">X</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
