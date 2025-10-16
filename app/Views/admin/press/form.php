<?php
use App\Core\View;

/** @var string $title */
/** @var array $asset */
/** @var array $errors */
/** @var string $formAction */
/** @var string $submitLabel */
/** @var string $csrfToken */
/** @var array $types */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/press/form-content',
    'contentData' => [
        'asset' => $asset,
        'errors' => $errors,
        'formAction' => $formAction,
        'submitLabel' => $submitLabel,
        'csrfToken' => $csrfToken,
        'types' => $types,
    ],
]);
