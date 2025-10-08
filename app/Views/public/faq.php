<?php
use App\Core\View;

/** @var array $faqs */

View::renderPartial('layouts/main', [
    'title' => 'FAQ',
    'contentTemplate' => 'public/faq-content',
    'contentData' => compact('faqs'),
]);
