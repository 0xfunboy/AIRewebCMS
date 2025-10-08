<?php
use App\Core\View;

/** @var string $title */
/** @var array $phase */
/** @var array $items */
/** @var array $errors */
/** @var string $csrfToken */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/roadmap/items-content',
    'contentData' => [
        'phase' => $phase,
        'items' => $items,
        'errors' => $errors,
        'csrfToken' => $csrfToken,
    ],
]);
