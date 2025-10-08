<?php
use App\Core\View;

/** @var string $title */
/** @var array $partner */
/** @var array $errors */
/** @var string $formAction */
/** @var string $submitLabel */
/** @var string $csrfToken */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/partners/form-content',
    'contentData' => [
        'partner' => $partner,
        'errors' => $errors,
        'formAction' => $formAction,
        'submitLabel' => $submitLabel,
        'csrfToken' => $csrfToken,
    ],
]);
