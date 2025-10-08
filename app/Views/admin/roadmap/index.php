<?php
use App\Core\View;

/** @var string $title */
/** @var array $phases */
/** @var array $tracks */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/roadmap/index-content',
    'contentData' => [
        'phases' => $phases,
        'tracks' => $tracks,
        'notice' => $notice,
        'error' => $error,
        'csrfToken' => $csrfToken,
    ],
]);
