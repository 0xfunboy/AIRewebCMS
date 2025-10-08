<?php
use App\Core\View;

/** @var array $agents */

View::renderPartial('layouts/main', [
    'title' => 'Agents',
    'contentTemplate' => 'public/agents-content',
    'contentData' => compact('agents'),
]);
