<?php
declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Router;
use App\Middleware\RequireAdmin;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ProductsController;
use App\Controllers\Admin\AdminInlineController;
use App\Middleware\AdminApiGuard;
use App\Controllers\Admin\PostsController;
use App\Controllers\Admin\AgentsController;
use App\Controllers\Admin\PartnersController;
use App\Controllers\Admin\TeamController;
use App\Controllers\Admin\SocialProofController;
use App\Controllers\Admin\RoadmapController;
use App\Controllers\Admin\SettingsController;
use App\Controllers\Admin\MediaController;

$router = new Router();

$requireAdmin = new RequireAdmin();

$router->get('/admin/dashboard', [DashboardController::class, 'index'], [$requireAdmin]);
$router->get('/admin/products', [ProductsController::class, 'index'], [$requireAdmin]);
$router->get('/admin/products/create', [ProductsController::class, 'create'], [$requireAdmin]);
$router->post('/admin/products/store', [ProductsController::class, 'store'], [$requireAdmin]);
$router->get('/admin/products/edit/{id}', [ProductsController::class, 'edit'], [$requireAdmin]);
$router->post('/admin/products/update/{id}', [ProductsController::class, 'update'], [$requireAdmin]);
$router->post('/admin/products/delete/{id}', [ProductsController::class, 'destroy'], [$requireAdmin]);

$apiGuard = new AdminApiGuard();
$router->post('/admin/api/update-field', [AdminInlineController::class, 'updateField'], [$apiGuard]);
$router->post('/admin/api/upload-image', [AdminInlineController::class, 'uploadImage'], [$apiGuard]);
$router->post('/admin/api/toggle-mode', [AdminInlineController::class, 'toggleMode'], [$apiGuard]);

$router->get('/admin/posts', [PostsController::class, 'index'], [$requireAdmin]);
$router->get('/admin/posts/create', [PostsController::class, 'create'], [$requireAdmin]);
$router->post('/admin/posts/store', [PostsController::class, 'store'], [$requireAdmin]);
$router->get('/admin/posts/edit/{id}', [PostsController::class, 'edit'], [$requireAdmin]);
$router->post('/admin/posts/update/{id}', [PostsController::class, 'update'], [$requireAdmin]);
$router->post('/admin/posts/delete/{id}', [PostsController::class, 'destroy'], [$requireAdmin]);

$router->get('/admin/agents', [AgentsController::class, 'index'], [$requireAdmin]);
$router->get('/admin/agents/create', [AgentsController::class, 'create'], [$requireAdmin]);
$router->post('/admin/agents/store', [AgentsController::class, 'store'], [$requireAdmin]);
$router->get('/admin/agents/edit/{id}', [AgentsController::class, 'edit'], [$requireAdmin]);
$router->post('/admin/agents/update/{id}', [AgentsController::class, 'update'], [$requireAdmin]);
$router->post('/admin/agents/delete/{id}', [AgentsController::class, 'destroy'], [$requireAdmin]);

$router->get('/admin/partners', [PartnersController::class, 'index'], [$requireAdmin]);
$router->get('/admin/partners/create', [PartnersController::class, 'create'], [$requireAdmin]);
$router->post('/admin/partners/store', [PartnersController::class, 'store'], [$requireAdmin]);
$router->get('/admin/partners/edit/{id}', [PartnersController::class, 'edit'], [$requireAdmin]);
$router->post('/admin/partners/update/{id}', [PartnersController::class, 'update'], [$requireAdmin]);
$router->post('/admin/partners/delete/{id}', [PartnersController::class, 'destroy'], [$requireAdmin]);

$router->get('/admin/team', [TeamController::class, 'index'], [$requireAdmin]);
$router->get('/admin/team/create', [TeamController::class, 'create'], [$requireAdmin]);
$router->post('/admin/team/store', [TeamController::class, 'store'], [$requireAdmin]);
$router->get('/admin/team/edit/{id}', [TeamController::class, 'edit'], [$requireAdmin]);
$router->post('/admin/team/update/{id}', [TeamController::class, 'update'], [$requireAdmin]);
$router->post('/admin/team/delete/{id}', [TeamController::class, 'destroy'], [$requireAdmin]);

$router->get('/admin/social-proof', [SocialProofController::class, 'index'], [$requireAdmin]);
$router->get('/admin/social-proof/create', [SocialProofController::class, 'create'], [$requireAdmin]);
$router->post('/admin/social-proof/store', [SocialProofController::class, 'store'], [$requireAdmin]);
$router->get('/admin/social-proof/edit/{id}', [SocialProofController::class, 'edit'], [$requireAdmin]);
$router->post('/admin/social-proof/update/{id}', [SocialProofController::class, 'update'], [$requireAdmin]);
$router->post('/admin/social-proof/delete/{id}', [SocialProofController::class, 'destroy'], [$requireAdmin]);

$router->get('/admin/media', [MediaController::class, 'index'], [$requireAdmin]);
$router->post('/admin/media/mirror', [MediaController::class, 'mirror'], [$requireAdmin]);
$router->post('/admin/media/optimize', [MediaController::class, 'optimize'], [$requireAdmin]);
$router->post('/admin/media/upload', [MediaController::class, 'upload'], [$requireAdmin]);

$router->get('/admin/roadmap', [RoadmapController::class, 'index'], [$requireAdmin]);
$router->get('/admin/roadmap/create', [RoadmapController::class, 'create'], [$requireAdmin]);
$router->post('/admin/roadmap/store', [RoadmapController::class, 'store'], [$requireAdmin]);
$router->get('/admin/roadmap/edit/{id}', [RoadmapController::class, 'edit'], [$requireAdmin]);
$router->post('/admin/roadmap/update/{id}', [RoadmapController::class, 'update'], [$requireAdmin]);
$router->post('/admin/roadmap/delete/{id}', [RoadmapController::class, 'destroy'], [$requireAdmin]);
$router->get('/admin/roadmap/{id}/items', [RoadmapController::class, 'items'], [$requireAdmin]);
$router->post('/admin/roadmap/{id}/items', [RoadmapController::class, 'updateItems'], [$requireAdmin]);
$router->post('/admin/roadmap/tracks', [RoadmapController::class, 'updateTracks'], [$requireAdmin]);

$router->get('/admin/settings', [SettingsController::class, 'index'], [$requireAdmin]);
$router->post('/admin/settings', [SettingsController::class, 'update'], [$requireAdmin]);

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
