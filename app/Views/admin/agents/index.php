<?php
use App\Core\View;

/** @var string $title */
/** @var array $agents */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/agents/index-content',
    'contentData' => [
        'agents' => $agents,
        'notice' => $notice,
        'error' => $error,
        'csrfToken' => $csrfToken,
    ],
]);
