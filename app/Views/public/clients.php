<?php
\App\Core\View::renderPartial('layouts/main', [
    'title' => 'Clients',
    'contentTemplate' => 'public/clients-content',
    'contentData' => compact('caseStudies'),
]);
