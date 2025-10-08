<?php
use App\Core\View;

/** @var string $title */
/** @var array $member */
/** @var array $errors */
/** @var string $formAction */
/** @var string $submitLabel */
/** @var string $csrfToken */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/team/form-content',
    'contentData' => [
        'member' => $member,
        'errors' => $errors,
        'formAction' => $formAction,
        'submitLabel' => $submitLabel,
        'csrfToken' => $csrfToken,
    ],
]);
