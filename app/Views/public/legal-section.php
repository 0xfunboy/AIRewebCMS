<?php
use App\Core\View;

/** @var array $settings */
/** @var array $section */

View::renderPartial('layouts/main', [
    'title' => (string)($section['title'] ?? 'Legal'),
    'contentTemplate' => 'public/legal-section-content',
    'contentData' => compact('settings', 'section'),
]);
