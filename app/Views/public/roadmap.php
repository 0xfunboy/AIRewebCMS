<?php
use App\Core\View;

/** @var array $phases */
/** @var array $tracks */
/** @var string $vision */

View::renderPartial('layouts/main', [
    'title' => 'Roadmap',
    'contentTemplate' => 'public/roadmap-content',
    'contentData' => compact('phases', 'tracks', 'vision'),
]);
