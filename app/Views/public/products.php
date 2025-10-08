<?php
use App\Core\View;

/** @var array $products */

View::renderPartial('layouts/main', [
    'title' => 'Products',
    'contentTemplate' => 'public/products-content',
    'contentData' => compact('products'),
]);
