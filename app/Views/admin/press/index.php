<?php
use App\Core\View;

/** @var string $title */
/** @var array $assets */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */
/** @var array $types */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/press/index-content',
    'contentData' => [
        'assets' => $assets,
        'notice' => $notice,
        'error' => $error,
        'csrfToken' => $csrfToken,
        'types' => $types,
    ],
]);
