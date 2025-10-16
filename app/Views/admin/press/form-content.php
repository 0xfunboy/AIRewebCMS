<?php
/** @var array $asset */
/** @var array $errors */
/** @var string $formAction */
/** @var string $submitLabel */
/** @var string $csrfToken */
/** @var array $types */
?>

<section class="max-w-3xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-acc"><?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="text-sm text-muted">Manage downloadable assets for press and partners.</p>
        </div>
        <a href="/admin/press" class="text-sm text-muted hover:text-acc transition">Back to list</a>
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
            <span>Asset Type</span>
            <select name="asset_type" class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
                <?php foreach ($types as $type): ?>
                    <option value="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>" <?= (($asset['asset_type'] ?? 'Logo') === $type) ? 'selected' : ''; ?>>
                        <?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label class="text-sm text-muted flex flex-col gap-2">
            <span>Label</span>
            <input type="text" name="label" required
                   value="<?= htmlspecialchars($asset['label'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
        </label>

        <label class="text-sm text-muted flex flex-col gap-2">
            <span>File URL or Path</span>
            <input type="text" name="file_path" required
                   value="<?= htmlspecialchars($asset['file_path'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"
                   placeholder="https://example.com/asset.pdf or /media/files/asset.pdf">
            <span class="text-xs text-muted">Use absolute URLs or media paths beginning with <code>/media</code>.</span>
        </label>

        <label class="text-sm text-muted flex flex-col gap-2 w-full md:w-40">
            <span>Sort Order</span>
            <input type="number" name="sort_order" min="0"
                   value="<?= htmlspecialchars((string)($asset['sort_order'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"
                   class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
        </label>

        <div class="flex items-center gap-3 pt-4">
            <button type="submit" class="bg-pri text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-red-500/80 transition">
                <?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8'); ?>
            </button>
            <a href="/admin/press" class="text-sm text-muted hover:text-acc transition">Cancel</a>
        </div>
    </form>
</section>
