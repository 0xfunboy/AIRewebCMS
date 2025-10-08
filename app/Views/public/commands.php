<?php
\App\Core\View::renderPartial('layouts/main', [
    'title' => 'User Manual',
    'contentTemplate' => 'public/commands-content',
    'contentData' => compact('commands'),
]);
