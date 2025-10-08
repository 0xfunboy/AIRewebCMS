<?php
use App\Core\View;

/** @var array $post */

View::renderPartial('layouts/main', [
    'title' => $post['title'] ?? 'Blog',
    'contentTemplate' => 'public/blog-post-content',
    'contentData' => compact('post'),
]);
