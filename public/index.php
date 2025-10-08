<?php
declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Router;
use App\Controllers\PageController;
use App\Controllers\AuthController;
use App\Controllers\ContactController;

$router = new Router();

$router->get('/', [PageController::class, 'home']);
$router->get('/products', [PageController::class, 'products']);
$router->get('/products/{slug}', [PageController::class, 'product']);
$router->get('/agents', [PageController::class, 'agents']);
$router->get('/team', [PageController::class, 'team']);
$router->get('/partners', [PageController::class, 'partners']);
$router->get('/clients', [PageController::class, 'clients']);
$router->get('/commands', [PageController::class, 'commands']);
$router->get('/social-proof', [PageController::class, 'socialProof']);
$router->get('/roadmap', [PageController::class, 'roadmap']);
$router->get('/tokenomics', [PageController::class, 'tokenomics']);
$router->get('/transparency', [PageController::class, 'transparency']);
$router->get('/api-plugins', [PageController::class, 'apiPlugins']);
$router->get('/press', [PageController::class, 'press']);
$router->get('/legal', [PageController::class, 'legal']);
$router->get('/faq', [PageController::class, 'faq']);
$router->get('/contact', [PageController::class, 'contact']);
$router->post('/contact', [ContactController::class, 'submit']);
$router->get('/blog', [PageController::class, 'blog']);
$router->get('/blog/{slug}', [PageController::class, 'blogPost']);
$router->get('/auth/nonce', [AuthController::class, 'issueNonce']);
$router->post('/auth/verify', [AuthController::class, 'verify']);
$router->post('/auth/logout', [AuthController::class, 'logout']);
$router->get('/login', [AuthController::class, 'showLogin']);

// TODO: register additional public routes (products, agents, blog, etc.)

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
