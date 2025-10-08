<?php
use App\Core\View;

/** @var array $partners */

View::renderPartial('layouts/main', [
    'title' => 'Partners',
    'contentTemplate' => 'public/partners-content',
    'contentData' => compact('partners'),
]);
