<?php
use App\Core\View;

/** @var string $title */
/** @var array $settings */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/settings/index-content',
    'contentData' => [
        'settings' => $settings,
        'notice' => $notice,
        'error' => $error,
        'csrfToken' => $csrfToken,
    ],
]);
