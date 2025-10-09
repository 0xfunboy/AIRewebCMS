<?php
/** @var array $product */

use App\Support\AdminMode;

$ctaLink = $product['cta_link'] ?? null;
$ctaText = $product['cta_text'] ?? null;
$features = $product['features'] ?? [];
$productId = $product['slug'] ?? (string)($product['id'] ?? '');
?>

<div>
    <div class="text-center mb-12" data-animate>
        <a href="/products" class="text-pri font-semibold hover:underline mb-4 inline-block">&larr; Back to Products</a>
        <h1 class="text-4xl md:text-5xl font-extrabold text-acc tracking-tight"<?= AdminMode::dataAttrs('products', 'hero_title', $productId); ?><?= AdminMode::isAdmin() ? ' class="admin-editable-text"' : ''; ?>>
            <?= htmlspecialchars($product['hero_title'] ?: $product['name'], ENT_QUOTES, 'UTF-8'); ?>
        </h1>
        <?php if (!empty($product['hero_subtitle'])): ?>
            <p class="mt-4 max-w-3xl mx-auto text-lg text-muted"<?= AdminMode::dataAttrs('products', 'hero_subtitle', $productId); ?><?= AdminMode::isAdmin() ? ' class="admin-editable-text"' : ''; ?>>
                <?= htmlspecialchars($product['hero_subtitle'], ENT_QUOTES, 'UTF-8'); ?>
            </p>
        <?php endif; ?>
    </div>

    <div class="bg-glass border border-stroke rounded-lg p-8 md:p-12" data-animate>
        <div class="grid md:grid-cols-2 gap-12 items-start">
            <div class="space-y-4 prose prose-invert max-w-none"<?= AdminMode::dataAttrs('products', 'content_html', $productId, 'html'); ?>>
                <?= $product['content_html'] ?? '<p class="text-muted">Detailed description coming soon.</p>'; ?>
            </div>
            <?php if (!empty($features)): ?>
                <div>
                    <h3 class="text-2xl font-bold text-acc mb-4">Key Features</h3>
                    <ul class="space-y-3">
                        <?php foreach ($features as $feature): ?>
                            <li class="flex items-start gap-3">
                                <svg class="h-6 w-6 text-cy flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                <span class="text-txt text-sm md:text-base"><?= htmlspecialchars($feature, ENT_QUOTES, 'UTF-8'); ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <?php if ($ctaLink && $ctaText): ?>
        <div class="mt-12 text-center" data-animate>
            <a href="<?= htmlspecialchars($ctaLink, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="bg-pri text-white font-bold py-3 px-8 rounded-md hover:bg-pri-700 transition-colors text-lg"<?= AdminMode::dataAttrs('products', 'cta_link', $productId, 'url'); ?>>
                <span<?= AdminMode::dataAttrs('products', 'cta_text', $productId); ?><?= AdminMode::isAdmin() ? ' class="admin-editable-text"' : ''; ?>>
                    <?= htmlspecialchars($ctaText, ENT_QUOTES, 'UTF-8'); ?>
                </span>
            </a>
        </div>
    <?php endif; ?>
</div>
