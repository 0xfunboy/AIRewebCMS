<?php
/** @var array $product */

$iconKey = $product['icon_key'] ?? 'chip';

?>

<div class="h-full bg-glass border border-stroke rounded-lg p-6 flex flex-col hover:border-pri/50 transition-all duration-300 transform hover:-translate-y-1 shadow-deep backdrop-blur-lg">
    <?= icon_svg($iconKey, 'h-8 w-8 text-pri mb-4'); ?>
    <h3 class="font-bold text-xl text-acc"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
    <p class="text-muted text-sm mt-2 flex-grow"><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8'); ?></p>
    <span class="mt-4 text-pri font-semibold text-sm">
        <?= empty($product['external_link']) ? 'Learn More' : 'Visit Site'; ?> &rarr;
    </span>
</div>
