<?php
use App\Core\View;

/** @var string $title */
/** @var array $studies */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/case-studies/index-content',
    'contentData' => [
        'studies' => $studies,
        'notice' => $notice,
        'error' => $error,
        'csrfToken' => $csrfToken,
    ],
]);
