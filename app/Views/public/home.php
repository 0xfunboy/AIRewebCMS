<?php
use App\Core\View;

/** @var array $settings */
/** @var array $products */
/** @var array $agents */
/** @var array $partners */

View::renderPartial('layouts/main', [
    'title' => 'AIRewardrop',
    'contentTemplate' => 'public/home-content',
    'contentData' => compact('settings', 'products', 'agents', 'partners'),
]);
