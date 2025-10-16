<?php
use App\Core\View;

/** @var array $study */
/** @var array $errors */
/** @var string $formAction */
/** @var string $submitLabel */
/** @var string $csrfToken */
?>

<section class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-acc"><?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8'); ?></h1>
            <p class="text-sm text-muted">Update the case studies highlighted on the Clients page.</p>
        </div>
        <a href="/admin/case-studies" class="text-sm text-muted hover:text-acc transition">Back to list</a>
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

    <form method="post" action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8'); ?>" class="space-y-6" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Client Name</span>
                <input type="text" name="client" required
                       value="<?= htmlspecialchars($study['client'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
            </label>
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Chain / Ecosystem</span>
                <input type="text" name="chain" required
                       value="<?= htmlspecialchars($study['chain'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                       class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
            </label>
        </div>

        <label class="text-sm text-muted flex flex-col gap-2">
            <span>Headline</span>
            <input type="text" name="title" required
                   value="<?= htmlspecialchars($study['title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                   class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
        </label>

        <label class="text-sm text-muted flex flex-col gap-2">
            <span>Summary</span>
            <textarea name="summary" rows="4" required
                      class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"><?= htmlspecialchars($study['summary'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </label>

        <div>
            <?php View::renderPartial('admin/partials/media-input', [
                'label' => 'Hero Image',
                'name' => 'image_url',
                'uploadName' => 'image_upload',
                'current' => $study['image_url'] ?? '',
                'helper' => 'Landscape images (16:9) look best. Uploading a new image replaces the current one.',
            ]); ?>
        </div>

        <label class="text-sm text-muted flex flex-col gap-2 w-full md:w-40">
            <span>Sort Order</span>
            <input type="number" name="sort_order" min="0"
                   value="<?= htmlspecialchars((string)($study['sort_order'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>"
                   class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
        </label>

        <div class="flex items-center gap-3 pt-4">
            <button type="submit" class="bg-pri text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-red-500/80 transition">
                <?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8'); ?>
            </button>
            <a href="/admin/case-studies" class="text-sm text-muted hover:text-acc transition">Cancel</a>
        </div>
    </form>
</section>
