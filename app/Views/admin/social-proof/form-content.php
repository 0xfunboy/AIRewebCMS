<?php
use App\Core\View;
/** @var array $item */
/** @var array $errors */
/** @var string $formAction */
/** @var string $submitLabel */
/** @var string $csrfToken */
?>

<section class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-acc"><?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-sm text-muted">Highlight tweets, testimonials, and media shout-outs.</p>
        </div>
        <a href="/admin/social-proof" class="text-sm text-muted hover:text-acc transition">Back to list</a>
    </div>

    <?php if ($errors): ?>
        <div class="card border-red-500/40 bg-red-500/10 text-red-100 text-sm space-y-2">
            <p class="font-semibold">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-1 marker:text-red-300">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>" class="space-y-6" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <label class="text-sm text-muted flex flex-col gap-2 md:col-span-2">
                <span>Author Name</span>
                <input type="text" name="author_name" required
                       value="<?= htmlspecialchars($item['author_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
            </label>
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Content Type</span>
                <select name="content_type" class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
                    <?php foreach (['Tweet', 'Testimonial', 'Media'] as $type): ?>
                        <option value="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>"
                            <?= (($item['content_type'] ?? 'Tweet') === $type) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Author Handle</span>
                <input type="text" name="author_handle" required
                       value="<?= htmlspecialchars($item['author_handle'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"
                       placeholder="@username">
            </label>
            <div>
                <?php View::renderPartial('admin/partials/media-input', [
                    'label' => 'Avatar Image',
                    'name' => 'author_avatar_url',
                    'uploadName' => 'author_avatar_upload',
                    'current' => $item['author_avatar_url'] ?? '',
                    'accept' => '.png,.jpg,.jpeg,.webp,.svg',
                    'helper' => 'Square avatars and transparent PNGs look best.',
                ]); ?>
            </div>
        </div>

        <label class="text-sm text-muted flex flex-col gap-2">
            <span>Content</span>
            <textarea name="content" rows="4" required
                      class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"><?= htmlspecialchars($item['content'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </label>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Link</span>
                <input type="url" name="link" required
                       value="<?= htmlspecialchars($item['link'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
            </label>
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Sort Order</span>
                <input type="number" name="sort_order" min="0"
                       value="<?= htmlspecialchars((string)($item['sort_order'] ?? 0), ENT_QUOTES, 'UTF-8') ?>"
                       class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
            </label>
        </div>

        <div class="flex items-center gap-3 pt-4">
            <button type="submit" class="bg-pri text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-red-500/80 transition">
                <?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8') ?>
            </button>
            <a href="/admin/social-proof" class="text-sm text-muted hover:text-acc transition">Cancel</a>
        </div>
    </form>
</section>
