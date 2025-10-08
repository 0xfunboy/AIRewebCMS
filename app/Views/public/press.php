<?php
use App\Core\View;

/** @var array $assets */

View::renderPartial('layouts/main', [
    'title' => 'Press Kit',
    'contentTemplate' => 'public/press-content',
    'contentData' => compact('assets'),
]);
