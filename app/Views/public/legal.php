<?php
use App\Core\View;

/** @var array $settings */

View::renderPartial('layouts/main', [
    'title' => 'Legal',
    'contentTemplate' => 'public/legal-content',
    'contentData' => compact('settings'),
]);
