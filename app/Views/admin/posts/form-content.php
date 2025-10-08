<?php
/** @var array $post */
/** @var array $errors */
/** @var string $formAction */
/** @var string $submitLabel */
/** @var string $csrfToken */
?>

<section class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-acc"><?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-sm text-muted">Publish long-form updates for the public blog.</p>
        </div>
        <a href="/admin/posts" class="text-sm text-muted hover:text-acc transition">Back to list</a>
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

    <form method="post" action="<?= htmlspecialchars($formAction, ENT_QUOTES, 'UTF-8') ?>" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

        <label class="text-sm text-muted flex flex-col gap-2">
            <span>Title</span>
            <input type="text" name="title" required data-slug-source="post-slug"
                   value="<?= htmlspecialchars($post['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
        </label>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Slug</span>
                <input id="post-slug" type="text" name="slug" required
                       value="<?= htmlspecialchars($post['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"
                       placeholder="example-post">
            </label>
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Published Date</span>
                <input type="date" name="published_at" required
                       value="<?= htmlspecialchars($post['published_at'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
            </label>
        </div>

        <label class="text-sm text-muted flex flex-col gap-2">
            <span>Hero Image URL</span>
            <input type="url" name="image_url" required
                   value="<?= htmlspecialchars($post['image_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
        </label>

        <label class="text-sm text-muted flex flex-col gap-2">
            <span>Snippet</span>
            <textarea name="snippet" rows="3" required
                      class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"><?= htmlspecialchars($post['snippet'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </label>

        <label class="text-sm text-muted flex flex-col gap-2">
            <span>Content (HTML allowed)</span>
            <textarea name="content_html" rows="12" required
                      class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none monospace"><?= htmlspecialchars($post['content_html'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </label>

        <label class="flex items-center gap-2 text-sm text-muted">
            <input type="checkbox" name="is_published" value="1"
                   <?= !empty($post['is_published']) ? 'checked' : '' ?>>
            <span>Published</span>
        </label>

        <div class="flex items-center gap-3 pt-4">
            <button type="submit" class="bg-pri text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-red-500/80 transition">
                <?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8') ?>
            </button>
            <a href="/admin/posts" class="text-sm text-muted hover:text-acc transition">Cancel</a>
        </div>
    </form>
</section>
