<?php
use App\Core\View;

View::renderPartial('layouts/main', [
    'title' => 'API & Plugins',
    'contentTemplate' => 'public/api-plugins-content',
    'contentData' => [],
]);
