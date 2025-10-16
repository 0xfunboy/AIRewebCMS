<?php
use App\Core\View;

/** @var string $title */
/** @var array $groups */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/navigation/index-content',
    'contentData' => [
        'groups' => $groups,
        'notice' => $notice,
        'error' => $error,
        'csrfToken' => $csrfToken,
    ],
]);
