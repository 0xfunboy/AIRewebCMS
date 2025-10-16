<?php
use App\Core\View;

/** @var string $title */
/** @var array $sections */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/legal/index-content',
    'contentData' => [
        'sections' => $sections,
        'notice' => $notice,
        'error' => $error,
        'csrfToken' => $csrfToken,
    ],
]);
