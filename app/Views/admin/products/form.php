<?php
use App\Core\View;

/** @var string $title */
/** @var array $product */
/** @var string $featureText */
/** @var array $errors */
/** @var string $formAction */
/** @var string $submitLabel */
/** @var string $mode */
/** @var string $csrfToken */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/products/form-content',
    'contentData' => [
        'product' => $product,
        'featureText' => $featureText,
        'errors' => $errors,
        'formAction' => $formAction,
        'submitLabel' => $submitLabel,
        'mode' => $mode,
        'csrfToken' => $csrfToken,
    ],
]);
