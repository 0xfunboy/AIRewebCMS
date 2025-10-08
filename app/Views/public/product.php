<?php
use App\Core\View;

/** @var array $product */

View::renderPartial('layouts/main', [
    'title' => $product['name'] ?? 'Product',
    'contentTemplate' => 'public/product-content',
    'contentData' => compact('product'),
]);
