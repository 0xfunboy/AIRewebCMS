<?php
/** @var array $items */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */
?>

<section class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-acc">FAQ</h1>
            <p class="text-xs text-muted mt-1">Answers shown on the FAQ page.</p>
        </div>
        <a href="/admin/faq/create" class="inline-flex items-center px-4 py-2 rounded-md bg-pri text-white text-sm font-medium hover:bg-red-500/80 transition">
            New FAQ Entry
        </a>
    </div>

    <?php if ($notice): ?>
        <div class="card border-emerald-500/40 bg-emerald-500/10 text-emerald-100 text-sm">
            <?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="card border-red-500/40 bg-red-500/10 text-red-100 text-sm">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($items)): ?>
        <div class="card text-sm text-muted">
            <p>No FAQ entries yet. <a href="/admin/faq/create" class="text-acc underline">Add the first answer</a>.</p>
        </div>
    <?php else: ?>
        <div class="card divide-y divide-border/60">
            <?php foreach ($items as $item): ?>
                <div class="py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <p class="font-semibold text-acc"><?= htmlspecialchars($item['question'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <p class="text-sm text-muted mt-1"><?= htmlspecialchars($item['answer'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-muted bg-bg2 border border-stroke rounded px-2 py-1">Order <?= htmlspecialchars((string)($item['sort_order'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></span>
                        <a href="/admin/faq/edit/<?= urlencode((string)$item['id']); ?>" class="text-cy hover:underline text-sm">Edit</a>
                        <form method="post" action="/admin/faq/delete/<?= urlencode((string)$item['id']); ?>" onsubmit="return confirm('Delete this FAQ entry?');">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                            <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
