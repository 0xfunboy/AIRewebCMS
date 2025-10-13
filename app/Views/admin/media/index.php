<?php
use App\Core\View;

/** @var string $title */
/** @var array $media */
/** @var string $csrfToken */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/media/index-content',
    'contentData' => [
        'media' => $media,
        'csrfToken' => $csrfToken,
        'notice' => $notice ?? null,
        'error' => $error ?? null,
    ],
]);
