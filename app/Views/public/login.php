<?php
use App\Core\View;

/** @var string|null $notice */
/** @var string $projectId */
/** @var string $rpcUrl */

View::renderPartial('layouts/main', [
    'title' => 'Reserved Area',
    'contentTemplate' => 'public/login-content',
    'contentData' => [
        'notice' => $notice ?? null,
        'projectId' => $projectId ?? '',
        'rpcUrl' => $rpcUrl ?? '',
    ],
]);
