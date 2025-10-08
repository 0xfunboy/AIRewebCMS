<?php
use App\Core\View;

View::renderPartial('layouts/main', [
    'title' => 'Tokenomics',
    'contentTemplate' => 'public/tokenomics-content',
    'contentData' => [],
]);
