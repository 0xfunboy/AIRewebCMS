<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Core\Response;
use App\Services\Security\Csrf;
use App\Support\AdminMode;
use Closure;

final class AdminApiGuard
{
    public function __invoke(array $params, Closure $next): mixed
    {
        if (!AdminMode::isAdmin()) {
            Response::json(['ok' => false, 'error' => 'Unauthorized'], 403);
            return null;
        }

        if (!AdminMode::throttle('admin_api', 60, 60)) {
            Response::json(['ok' => false, 'error' => 'Too many requests'], 429);
            return null;
        }

        return $next($params);
    }
}
