<?php
use App\Core\View;

/** @var array $settings */
/** @var string $csrfToken */
/** @var ?string $success */
/** @var ?string $error */

View::renderPartial('layouts/main', [
    'title' => 'Contact',
    'contentTemplate' => 'public/contact-content',
    'contentData' => compact('settings', 'csrfToken', 'success', 'error'),
]);
