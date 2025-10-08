<?php
use App\Core\View;

/** @var string $title */
/** @var array $stats */
/** @var array $recentSessions */

View::renderPartial('layouts/admin', [
    'title' => $title,
    'contentTemplate' => 'admin/dashboard-content',
    'contentData' => [
        'stats' => $stats,
        'recentSessions' => $recentSessions,
    ],
]);
