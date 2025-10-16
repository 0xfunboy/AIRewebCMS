<?php
/** @var array $item */
/** @var array $errors */
/** @var string $formAction */
/** @var string $submitLabel */
/** @var string $csrfToken */
?>

<section class="max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-acc"><?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="text-sm text-muted">FAQ entries appear on the public FAQ page.</p>
        </div>
        <a href="/admin/faq" class="text-sm text-muted hover:text-acc transition">Back to list</a>
    </div>

    <?php if ($errors): ?>
        <div class="card border-red-500/40 bg-red-500/10 text-red-100 text-sm space-y-2">
            <p class="font-semibold">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-1 marker:text-red-300">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8'); ?>" class="space-y-5">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

        <label class="text-sm text-muted flex flex-col gap-2">
            <span>Question</span>
            <input type="text" name="question" required
                   value="<?= htmlspecialchars($item['question'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
        </label>

        <label class="text-sm text-muted flex flex-col gap-2">
            <span>Answer</span>
            <textarea name="answer" rows="4" required
                      class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"><?= htmlspecialchars($item['answer'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </label>

        <label class="text-sm text-muted flex flex-col gap-2 w-full md:w-40">
            <span>Sort Order</span>
            <input type="number" name="sort_order" min="0"
                   value="<?= htmlspecialchars((string)($item['sort_order'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"
                   class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
        </label>

        <div class="flex items-center gap-3 pt-4">
            <button type="submit" class="bg-pri text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-red-500/80 transition">
                <?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8'); ?>
            </button>
            <a href="/admin/faq" class="text-sm text-muted hover:text-acc transition">Cancel</a>
        </div>
    </form>
</section>
