<?php
\App\Core\View::renderPartial('layouts/main', [
    'title' => 'Social Proof',
    'contentTemplate' => 'public/social-proof-content',
    'contentData' => compact('items'),
]);
