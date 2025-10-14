<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Services\Admin\MediaOptimizer;
use App\Services\Security\Csrf;
use App\Support\Flash;
use App\Support\Media;
use App\Support\Uploads;

final class MediaController extends Controller
{
    public function index(): void
    {
        $library = $this->gatherMedia();

        $this->view('admin/media/index', [
            'title' => 'Media Library',
            'media' => $library,
            'csrfToken' => Csrf::token(),
            'notice' => Flash::pull('admin.media.notice'),
            'error' => Flash::pull('admin.media.error'),
        ]);
    }

    public function mirror(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        $optimizer = new MediaOptimizer(Database::connection());
        try {
            $report = $optimizer->mirror();
            $message = sprintf(
                'Mirrored %d of %d assets (errors: %d).',
                $report['processed'],
                $report['total'],
                $report['errors']
            );
            $this->respond($report, true, $message);
        } catch (\Throwable $e) {
            $this->respond(['error' => $e->getMessage()], false, 'Failed to mirror remote assets.', 500);
        }
    }

    public function optimize(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        $optimizer = new MediaOptimizer(Database::connection());
        try {
            $report = $optimizer->optimize();
            $message = sprintf(
                'Optimized %d of %d files to WebP (errors: %d).',
                $report['processed'],
                $report['total'],
                $report['errors']
            );
            $this->respond($report, true, $message);
        } catch (\Throwable $e) {
            $errorMessage = $e->getMessage() ?: 'Image optimization failed.';
            $this->respond(['error' => $errorMessage], false, $errorMessage, 500);
        }
    }

    public function upload(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        if (!isset($_FILES['file']) || ($_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            $this->respond([], false, 'Select a file to upload.', 422);
        }

        $label = trim((string)($_POST['label'] ?? ($_FILES['file']['name'] ?? 'media')));

        try {
            $stored = Uploads::store($_FILES['file'], $label !== '' ? $label : 'media');
            $path = Media::normalizeMediaPath($stored['path']);
            $variants = [];
            foreach ($stored['variants'] as $format => $variant) {
                $variants[$format] = [
                    'path' => Media::normalizeMediaPath($variant['path']),
                    'width' => $variant['width'],
                    'height' => $variant['height'],
                ];
            }
            $payload = [
                'path' => $path,
                'width' => $stored['width'],
                'height' => $stored['height'],
                'variants' => $variants,
                'steps' => [[
                    'phase' => 0,
                    'current' => 1,
                    'total' => 1,
                    'message' => sprintf('Uploaded %s', basename($path)),
                    'status' => 'ok',
                ]],
            ];
            $message = sprintf('Uploaded %s.', basename($path));
            $this->respond($payload, true, $message);
        } catch (\Throwable $e) {
            $this->respond(['error' => $e->getMessage()], false, 'Upload failed.', 400);
        }
    }

    /**
     * @return array<int, array{
     *     path:string,
     *     url:string,
     *     size:int,
     *     modified:int,
     *     type:string,
     *     variants:array<string,array{path:string,url:string,size:int,modified:int}>
     * }>
     */
    private function gatherMedia(): array
    {
        $root = dirname(__DIR__, 3) . '/public/media';
        if (!is_dir($root)) {
            return [];
        }

        $files = [];
        $publicRoot = rtrim(dirname(__DIR__, 3) . '/public', '/');
        $this->scanMediaDirectory($root, $files, strlen($publicRoot) + 1);

        $grouped = [];
        foreach ($files as $file) {
            $groupKey = $file['group'] ?? $file['path'];
            $grouped[$groupKey][] = $file;
        }

        $result = [];
        foreach ($grouped as $groupKey => $items) {
            $primaryIndex = $this->selectPrimaryMediaIndex($items);
            $primary = $items[$primaryIndex];

            $variants = [];
            $latestModified = $primary['modified'];

            foreach ($items as $index => $item) {
                $latestModified = max($latestModified, $item['modified']);
                if ($index === $primaryIndex) {
                    continue;
                }
                $variants[$item['type']] = [
                    'path' => $item['path'],
                    'url' => $item['url'],
                    'size' => $item['size'],
                    'modified' => $item['modified'],
                ];
            }

            unset($primary['group']);
            $primary['variants'] = $variants;
            $primary['modified'] = $latestModified;
            $result[] = $primary;
        }

        usort(
            $result,
            static fn(array $a, array $b) => $b['modified'] <=> $a['modified']
        );

        return $result;
    }

    private function scanMediaDirectory(string $dir, array &$files, int $rootLength): void
    {
        $items = @scandir($dir);
        if ($items === false) {
            \App\Core\Logger::debug('Media scan skipped directory', ['directory' => $dir]);
            return;
        }

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->scanMediaDirectory($path, $files, $rootLength);
                continue;
            }

            if (!is_file($path)) {
                continue;
            }

            $relativePath = substr($path, $rootLength);
            $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            $groupPath = $relativePath;
            $dotPos = strrpos($groupPath, '.');
            if ($dotPos !== false) {
                $groupPath = substr($groupPath, 0, $dotPos);
            }

            $files[] = [
                'path' => $relativePath,
                'url' => '/' . $relativePath,
                'size' => filesize($path) ?: 0,
                'modified' => filemtime($path) ?: 0,
                'type' => $extension,
                'group' => $groupPath,
            ];
        }
    }

    /**
     * @param array<int, array{type:string,modified:int}> $items
     */
    private function selectPrimaryMediaIndex(array $items): int
    {
        $priority = [
            'svg' => 0,
            'webp' => 1,
            'png' => 2,
            'jpg' => 3,
            'jpeg' => 3,
            'ico' => 4,
        ];

        $bestIndex = 0;
        $bestScore = PHP_INT_MAX;

        foreach ($items as $index => $item) {
            $type = $item['type'] ?? '';
            $score = $priority[$type] ?? 10;
            if ($score < $bestScore) {
                $bestScore = $score;
                $bestIndex = $index;
                continue;
            }

            if ($score === $bestScore && $item['modified'] > ($items[$bestIndex]['modified'] ?? 0)) {
                $bestIndex = $index;
            }
        }

        return $bestIndex;
    }

    private function assertValidCsrf(?string $token): void
    {
        if (Csrf::verify($token)) {
            return;
        }

        $this->respond([], false, 'Invalid CSRF token.', 403);
    }

    private function respond(array $payload, bool $success, string $message, int $statusCode = 200): void
    {
        if ($this->wantsJson()) {
            $body = ['ok' => $success, 'message' => $message] + $payload;
            Response::json($body, $success ? $statusCode : ($statusCode >= 400 ? $statusCode : 400));
            return;
        }

        $key = $success ? 'admin.media.notice' : 'admin.media.error';
        Flash::set($key, $message);
        $this->redirect('/admin/media');
    }

    private function wantsJson(): bool
    {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
        return str_contains($accept, 'application/json') || strcasecmp($requestedWith, 'XMLHttpRequest') === 0;
    }
}
