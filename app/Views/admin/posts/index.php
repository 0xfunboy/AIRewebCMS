<?php
use App\Core\View;

/** @var string $title */
/** @var array $posts */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/posts/index-content',
    'contentData' => [
        'posts' => $posts,
        'notice' => $notice,
        'error' => $error,
        'csrfToken' => $csrfToken,
    ],
]);
