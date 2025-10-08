<?php
use App\Core\View;

/** @var array $products */
?>

<div data-animate>
    <?php View::renderPartial('partials/section-title', [
        'title' => 'Our Product Ecosystem',
        'subtitle' => 'A suite of powerful, interconnected tools designed to enhance your on-chain experience, from analysis to execution.',
    ]); ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($products as $index => $product): ?>
            <div data-animate data-animate-delay="<?= $index * 100 ?>" class="h-full">
                <?php if (!empty($product['external_link'])): ?>
                    <a href="<?= htmlspecialchars($product['external_link'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="block h-full">
                        <?php View::renderPartial('public/partials/product-card', ['product' => $product]); ?>
                    </a>
                <?php else: ?>
                    <a href="/products/<?= htmlspecialchars($product['slug'], ENT_QUOTES, 'UTF-8'); ?>" class="block h-full">
                        <?php View::renderPartial('public/partials/product-card', ['product' => $product]); ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
