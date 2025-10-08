<?php
use App\Core\View;

/** @var string $title */
/** @var array $partners */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/partners/index-content',
    'contentData' => [
        'partners' => $partners,
        'notice' => $notice,
        'error' => $error,
        'csrfToken' => $csrfToken,
    ],
]);
