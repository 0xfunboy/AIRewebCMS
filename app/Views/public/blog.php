<?php
use App\Core\View;

/** @var array $posts */

View::renderPartial('layouts/main', [
    'title' => 'Blog',
    'contentTemplate' => 'public/blog-content',
    'contentData' => compact('posts'),
]);
