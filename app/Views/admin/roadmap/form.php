<?php
use App\Core\View;

/** @var string $title */
/** @var array $phase */
/** @var array $errors */
/** @var string $formAction */
/** @var string $submitLabel */
/** @var string $csrfToken */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/roadmap/form-content',
    'contentData' => [
        'phase' => $phase,
        'errors' => $errors,
        'formAction' => $formAction,
        'submitLabel' => $submitLabel,
        'csrfToken' => $csrfToken,
    ],
]);
