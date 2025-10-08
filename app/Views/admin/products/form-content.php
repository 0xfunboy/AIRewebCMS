<?php
/** @var array $product */
/** @var string $featureText */
/** @var array $errors */
/** @var string $formAction */
/** @var string $submitLabel */
/** @var string $mode */
/** @var string $csrfToken */
?>

<section class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-acc">
                <?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="text-sm text-muted">Manage hero data, copy, and feature bullets for this product.</p>
        </div>
        <a href="/admin/products" class="text-sm text-muted hover:text-acc transition">Back to list</a>
    </div>

    <?php if (!empty($errors)): ?>
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Name</span>
                <input type="text" name="name" required
                       value="<?= htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
            </label>
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Slug</span>
                <input type="text" name="slug" required
                       value="<?= htmlspecialchars($product['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"
                       placeholder="example-product">
            </label>
        </div>

        <label class="text-sm text-muted flex flex-col gap-2">
            <span>Description</span>
            <textarea name="description" rows="3" required
                      class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"><?= htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </label>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Icon Key</span>
                <input type="text" name="icon_key" required
                       value="<?= htmlspecialchars($product['icon_key'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"
                       placeholder="bolt">
            </label>
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>External Link</span>
                <input type="url" name="external_link"
                       value="<?= htmlspecialchars($product['external_link'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"
                       placeholder="https://example.com">
            </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Hero Title</span>
                <input type="text" name="hero_title"
                       value="<?= htmlspecialchars($product['hero_title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
            </label>
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Hero Subtitle</span>
                <input type="text" name="hero_subtitle"
                       value="<?= htmlspecialchars($product['hero_subtitle'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
            </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>CTA Text</span>
                <input type="text" name="cta_text"
                       value="<?= htmlspecialchars($product['cta_text'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
            </label>
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>CTA Link</span>
                <input type="url" name="cta_link"
                       value="<?= htmlspecialchars($product['cta_link'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                       class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
            </label>
        </div>

        <label class="text-sm text-muted flex flex-col gap-2 w-full md:w-40">
            <span>Featured Order</span>
            <input type="number" name="featured_order" min="0"
                   value="<?= htmlspecialchars((string)($product['featured_order'] ?? 0), ENT_QUOTES, 'UTF-8') ?>"
                   class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
        </label>

        <label class="text-sm text-muted flex flex-col gap-2">
            <span>Feature Bullets (one per line)</span>
            <textarea name="features" rows="5" required
                      class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"
                      placeholder="Fast integrations&#10;Enterprise support&#10;Security-first"><?= htmlspecialchars($featureText, ENT_QUOTES, 'UTF-8') ?></textarea>
        </label>

        <label class="text-sm text-muted flex flex-col gap-2">
            <span>Detailed Content (HTML allowed)</span>
            <textarea name="content_html" rows="10"
                      class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none monospace"><?= htmlspecialchars($product['content_html'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
        </label>

        <div class="flex items-center gap-3 pt-4">
            <button type="submit" class="bg-pri text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-red-500/80 transition">
                <?= htmlspecialchars($submitLabel, ENT_QUOTES, 'UTF-8') ?>
            </button>
            <a href="/admin/products" class="text-sm text-muted hover:text-acc transition">Cancel</a>
        </div>
    </form>
</section>
