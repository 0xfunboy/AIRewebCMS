<?php
/** @var array $wallet */
/** @var array $errors */
/** @var string $formAction */
/** @var string $submitLabel */
/** @var string $csrfToken */
?>

<section class="max-w-2xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-acc"><?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="text-sm text-muted">Wallet entries appear as copyable disclosures on the Transparency page.</p>
        </div>
        <a href="/admin/transparency" class="text-sm text-muted hover:text-acc transition">Back to list</a>
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
            <span>Label</span>
            <input type="text" name="label" required
                   value="<?= htmlspecialchars($wallet['label'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
        </label>

        <label class="text-sm text-muted flex flex-col gap-2">
            <span>Wallet Address</span>
            <input type="text" name="wallet_address" required
                   value="<?= htmlspecialchars($wallet['wallet_address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none font-mono text-sm">
            <span class="text-xs text-muted">Addresses will be shown exactly as entered. Use uppercase for checksum accuracy.</span>
        </label>

        <label class="text-sm text-muted flex flex-col gap-2 w-full md:w-40">
            <span>Sort Order</span>
            <input type="number" name="sort_order" min="0"
                   value="<?= htmlspecialchars((string)($wallet['sort_order'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"
                   class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
        </label>

        <div class="flex items-center gap-3 pt-4">
            <button type="submit" class="bg-pri text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-red-500/80 transition">
                <?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8'); ?>
            </button>
            <a href="/admin/transparency" class="text-sm text-muted hover:text-acc transition">Cancel</a>
        </div>
    </form>
</section>
