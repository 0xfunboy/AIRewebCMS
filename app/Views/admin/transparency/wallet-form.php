<?php
use App\Core\View;

/** @var string $title */
/** @var array $wallet */
/** @var array $errors */
/** @var string $formAction */
/** @var string $submitLabel */
/** @var string $csrfToken */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/transparency/wallet-form-content',
    'contentData' => [
        'wallet' => $wallet,
        'errors' => $errors,
        'formAction' => $formAction,
        'submitLabel' => $submitLabel,
        'csrfToken' => $csrfToken,
    ],
]);
