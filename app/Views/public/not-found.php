<?php
use App\Core\View;

View::renderPartial('layouts/main', [
    'title' => 'Not Found',
    'contentTemplate' => 'public/not-found-content',
    'contentData' => [],
]);
