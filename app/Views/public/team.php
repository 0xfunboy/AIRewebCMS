<?php
use App\Core\View;

/** @var array $team */

View::renderPartial('layouts/main', [
    'title' => 'Team',
    'contentTemplate' => 'public/team-content',
    'contentData' => compact('team'),
]);
