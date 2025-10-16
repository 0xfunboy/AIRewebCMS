<?php
use App\Core\View;

/** @var string $title */
/** @var array $wallets */
/** @var array $reports */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/transparency/index-content',
    'contentData' => [
        'wallets' => $wallets,
        'reports' => $reports,
        'notice' => $notice,
        'error' => $error,
        'csrfToken' => $csrfToken,
    ],
]);
