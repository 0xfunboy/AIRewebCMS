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
use App\Controllers\Admin\CaseStudiesController;
use App\Controllers\Admin\FaqController;
use App\Controllers\Admin\PressController;
use App\Controllers\Admin\LegalController;
use App\Controllers\Admin\TransparencyController;
use App\Controllers\Admin\NavigationController;

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

$router->get('/admin/case-studies', [CaseStudiesController::class, 'index'], [$requireAdmin]);
$router->get('/admin/case-studies/create', [CaseStudiesController::class, 'create'], [$requireAdmin]);
$router->post('/admin/case-studies/store', [CaseStudiesController::class, 'store'], [$requireAdmin]);
$router->get('/admin/case-studies/edit/{id}', [CaseStudiesController::class, 'edit'], [$requireAdmin]);
$router->post('/admin/case-studies/update/{id}', [CaseStudiesController::class, 'update'], [$requireAdmin]);
$router->post('/admin/case-studies/delete/{id}', [CaseStudiesController::class, 'destroy'], [$requireAdmin]);

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

$router->get('/admin/faq', [FaqController::class, 'index'], [$requireAdmin]);
$router->get('/admin/faq/create', [FaqController::class, 'create'], [$requireAdmin]);
$router->post('/admin/faq/store', [FaqController::class, 'store'], [$requireAdmin]);
$router->get('/admin/faq/edit/{id}', [FaqController::class, 'edit'], [$requireAdmin]);
$router->post('/admin/faq/update/{id}', [FaqController::class, 'update'], [$requireAdmin]);
$router->post('/admin/faq/delete/{id}', [FaqController::class, 'destroy'], [$requireAdmin]);

$router->get('/admin/press', [PressController::class, 'index'], [$requireAdmin]);
$router->get('/admin/press/create', [PressController::class, 'create'], [$requireAdmin]);
$router->post('/admin/press/store', [PressController::class, 'store'], [$requireAdmin]);
$router->get('/admin/press/edit/{id}', [PressController::class, 'edit'], [$requireAdmin]);
$router->post('/admin/press/update/{id}', [PressController::class, 'update'], [$requireAdmin]);
$router->post('/admin/press/delete/{id}', [PressController::class, 'destroy'], [$requireAdmin]);

$router->get('/admin/legal', [LegalController::class, 'index'], [$requireAdmin]);
$router->get('/admin/legal/create', [LegalController::class, 'create'], [$requireAdmin]);
$router->post('/admin/legal/store', [LegalController::class, 'store'], [$requireAdmin]);
$router->get('/admin/legal/edit/{id}', [LegalController::class, 'edit'], [$requireAdmin]);
$router->post('/admin/legal/update/{id}', [LegalController::class, 'update'], [$requireAdmin]);
$router->post('/admin/legal/delete/{id}', [LegalController::class, 'destroy'], [$requireAdmin]);

$router->get('/admin/transparency', [TransparencyController::class, 'index'], [$requireAdmin]);
$router->get('/admin/transparency/wallets/create', [TransparencyController::class, 'createWallet'], [$requireAdmin]);
$router->post('/admin/transparency/wallets/store', [TransparencyController::class, 'storeWallet'], [$requireAdmin]);
$router->get('/admin/transparency/wallets/edit/{id}', [TransparencyController::class, 'editWallet'], [$requireAdmin]);
$router->post('/admin/transparency/wallets/update/{id}', [TransparencyController::class, 'updateWallet'], [$requireAdmin]);
$router->post('/admin/transparency/wallets/delete/{id}', [TransparencyController::class, 'destroyWallet'], [$requireAdmin]);
$router->get('/admin/transparency/reports/create', [TransparencyController::class, 'createReport'], [$requireAdmin]);
$router->post('/admin/transparency/reports/store', [TransparencyController::class, 'storeReport'], [$requireAdmin]);
$router->get('/admin/transparency/reports/edit/{id}', [TransparencyController::class, 'editReport'], [$requireAdmin]);
$router->post('/admin/transparency/reports/update/{id}', [TransparencyController::class, 'updateReport'], [$requireAdmin]);
$router->post('/admin/transparency/reports/delete/{id}', [TransparencyController::class, 'destroyReport'], [$requireAdmin]);

$router->get('/admin/navigation', [NavigationController::class, 'index'], [$requireAdmin]);
$router->post('/admin/navigation/group/{id}', [NavigationController::class, 'updateGroup'], [$requireAdmin]);
$router->post('/admin/navigation/item/{id}', [NavigationController::class, 'updateItem'], [$requireAdmin]);

$router->get('/admin/media', [MediaController::class, 'index'], [$requireAdmin]);
$router->post('/admin/media/mirror', [MediaController::class, 'mirror'], [$requireAdmin]);
$router->post('/admin/media/optimize', [MediaController::class, 'optimize'], [$requireAdmin]);
$router->post('/admin/media/upload', [MediaController::class, 'upload'], [$requireAdmin]);
$router->get('/admin/media/list', [MediaController::class, 'listing'], [$requireAdmin]);
$router->post('/admin/media/delete', [MediaController::class, 'delete'], [$requireAdmin]);
$router->post('/admin/media/replace', [MediaController::class, 'replace'], [$requireAdmin]);

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
