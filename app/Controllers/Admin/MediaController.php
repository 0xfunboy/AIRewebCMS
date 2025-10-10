<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Services\Admin\MediaOptimizer;
use App\Services\Security\Csrf;
use App\Support\Flash;
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
            $this->respond(['error' => $e->getMessage()], false, 'Image optimization failed.', 500);
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
            $path = '/' . ltrim($stored['path'], '/');
            $payload = [
                'path' => $path,
                'width' => $stored['width'],
                'height' => $stored['height'],
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
     * @return array<int, array{path:string,url:string,size:int,modified:int,type:string}>
     */
    private function gatherMedia(): array
    {
        $root = dirname(__DIR__, 2) . '/public/media';
        if (!is_dir($root)) {
            return [];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $relativePath = str_replace($root, '', $file->getPathname());
            $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
            $extension = strtolower($file->getExtension());

            $files[] = [
                'path' => $relativePath,
                'url' => '/media/' . $relativePath,
                'size' => $file->getSize(),
                'modified' => $file->getMTime(),
                'type' => $extension,
            ];
        }

        usort(
            $files,
            static fn(array $a, array $b) => $b['modified'] <=> $a['modified']
        );

        return $files;
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
