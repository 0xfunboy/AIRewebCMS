<?php
use App\Core\View;

/** @var array $settings */

View::renderPartial('layouts/main', [
    'title' => 'Transparency',
    'contentTemplate' => 'public/transparency-content',
    'contentData' => compact('settings'),
]);
