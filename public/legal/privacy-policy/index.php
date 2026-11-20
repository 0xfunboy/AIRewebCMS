<?php
declare(strict_types=1);

require dirname(__DIR__, 3) . '/app/bootstrap.php';

$controller = new \App\Controllers\PageController();
$controller->legalSection('privacy-policy');
