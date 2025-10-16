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
    /** @var array<string,array{primary:string,columns:string[]}> */
    private array $referenceMap = [
        'partners' => ['primary' => 'id', 'columns' => ['logo_url', 'badge_logo_url']],
        'agents' => ['primary' => 'id', 'columns' => ['image_url']],
        'team_members' => ['primary' => 'id', 'columns' => ['avatar_url']],
        'social_proof_items' => ['primary' => 'id', 'columns' => ['author_avatar_url']],
        'blog_posts' => ['primary' => 'id', 'columns' => ['image_url']],
    ];

    /** @var array<string,bool>|null */
    private ?array $referenceCache = null;

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

    public function listing(): void
    {
        Response::json([
            'ok' => true,
            'media' => $this->gatherMedia(),
        ]);
    }

    public function delete(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);
        $path = (string)($_POST['path'] ?? '');
        $relative = $this->normalizeRelativePath($path);
        if ($relative === null) {
            $this->respond(['error' => 'Invalid media path.'], false, 'Invalid media path.', 422);
        }

        if ($this->isMediaReferenced($relative)) {
            $this->respond(['error' => 'This media asset is still referenced. Update references before deleting.'], false, 'Asset still in use.', 409);
        }

        $absolute = dirname(__DIR__, 3) . '/public/' . $relative;
        if (!is_file($absolute)) {
            $this->respond(['error' => 'File not found on disk.'], false, 'File not found.', 404);
        }

        $this->removeFileSet($absolute);
        $this->referenceCache = null;

        Response::json([
            'ok' => true,
            'message' => 'Media removed.',
        ]);
    }

    public function replace(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        $path = (string)($_POST['path'] ?? '');
        $relative = $this->normalizeRelativePath($path);
        if ($relative === null) {
            $this->respond(['error' => 'Invalid media path.'], false, 'Invalid media path.', 422);
        }

        if (!isset($_FILES['file']) || ($_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            $this->respond(['error' => 'Select a file to upload.'], false, 'Select a file to upload.', 422);
        }

        $absoluteOld = dirname(__DIR__, 3) . '/public/' . $relative;
        if (!is_file($absoluteOld)) {
            $this->respond(['error' => 'Existing file not found.'], false, 'Existing file not found.', 404);
        }

        try {
            $result = Uploads::overwrite($_FILES['file'], $relative);
            $this->referenceCache = null;

            $variantsPayload = [];
            foreach ($result['variants'] as $format => $variantInfo) {
                $variantsPayload[$format] = [
                    'path' => Media::normalizeMediaPath($variantInfo['path']),
                    'width' => $variantInfo['width'],
                    'height' => $variantInfo['height'],
                ];
            }

            Response::json([
                'ok' => true,
                'message' => 'Media replaced.',
                'path' => Media::normalizeMediaPath($relative),
                'width' => $result['width'],
                'height' => $result['height'],
                'variants' => $variantsPayload,
            ]);
        } catch (\Throwable $e) {
            $this->respond(['error' => $e->getMessage()], false, 'Replace operation failed.', 400);
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
                    'width' => $item['width'],
                    'height' => $item['height'],
                ];
            }

            unset($primary['group']);
            $primary['variants'] = $variants;
            $primary['modified'] = $latestModified;
            $primary['in_use'] = $this->isMediaReferenced($primary['path']);
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

            $width = null;
            $height = null;
            if (in_array($extension, ['png', 'jpg', 'jpeg', 'webp', 'gif'], true)) {
                $info = @getimagesize($path);
                if ($info) {
                    $width = (int)$info[0];
                    $height = (int)$info[1];
                }
            }

            $files[] = [
                'path' => $relativePath,
                'url' => '/' . $relativePath,
                'size' => filesize($path) ?: 0,
                'modified' => filemtime($path) ?: 0,
                'type' => $extension,
                'group' => $groupPath,
                'width' => $width,
                'height' => $height,
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

    private function buildStep(int $phase, int $current, int $total, string $message, string $status = 'ok'): array
    {
        return [
            'phase' => $phase,
            'current' => $current,
            'total' => max(1, $total),
            'message' => $message,
            'status' => $status,
        ];
    }

    private function normalizeRelativePath(string $value): ?string
    {
        $trimmed = trim(str_replace('\\', '/', $value));
        if ($trimmed === '') {
            return null;
        }
        if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
            return null;
        }
        $trimmed = ltrim($trimmed, '/');
        if (!str_starts_with($trimmed, 'media/')) {
            return null;
        }
        return $trimmed;
    }

    private function getReferencedMedia(): array
    {
        if ($this->referenceCache !== null) {
            return $this->referenceCache;
        }

        $db = Database::connection();
        $map = [];

        $settings = $db->query("SELECT setting_value FROM settings WHERE setting_value LIKE '/media/%' OR setting_value LIKE 'media/%'");
        if ($settings) {
            foreach ($settings->fetchAll(\PDO::FETCH_COLUMN) as $value) {
                $normalized = $this->normalizeRelativePath((string)$value);
                if ($normalized !== null) {
                    $map[$normalized] = true;
                }
            }
        }

        foreach ($this->referenceMap as $table => $meta) {
            foreach ($meta['columns'] as $column) {
                $sql = sprintf(
                    "SELECT %s AS value FROM %s WHERE %s LIKE '/media/%%' OR %s LIKE 'media/%%'",
                    $column,
                    $table,
                    $column,
                    $column
                );
                $stmt = $db->query($sql);
                if (!$stmt) {
                    continue;
                }
                foreach ($stmt->fetchAll(\PDO::FETCH_COLUMN) as $value) {
                    $normalized = $this->normalizeRelativePath((string)$value);
                    if ($normalized !== null) {
                        $map[$normalized] = true;
                    }
                }
            }
        }

        return $this->referenceCache = $map;
    }

    private function isMediaReferenced(string $relativePath): bool
    {
        $normalized = $this->normalizeRelativePath($relativePath);
        if ($normalized === null) {
            return false;
        }

        $references = $this->getReferencedMedia();
        return isset($references[$normalized]);
    }

    private function removeFileSet(string $absolutePath): void
    {
        if (is_file($absolutePath)) {
            @unlink($absolutePath);
        }

        $base = preg_replace('/\\.[^.]+$/', '', $absolutePath);
        if (!$base) {
            return;
        }

        foreach (glob($base . '.*') ?: [] as $candidate) {
            if (is_file($candidate)) {
                @unlink($candidate);
            }
        }
    }

    private function updateReferences(string $oldRelative, string $newRelative): void
    {
        $db = Database::connection();

        $oldNormalized = $this->normalizeRelativePath($oldRelative);
        $newNormalized = $this->normalizeRelativePath($newRelative);
        if ($oldNormalized === null || $newNormalized === null) {
            return;
        }

        $oldFull = '/' . $oldNormalized;
        $newFull = '/' . $newNormalized;

        $stmt = $db->prepare('UPDATE settings SET setting_value = :new WHERE setting_value = :old');
        $stmt->execute(['new' => $newFull, 'old' => $oldFull]);
        $stmt->execute(['new' => $newNormalized, 'old' => $oldNormalized]);

        foreach ($this->referenceMap as $table => $meta) {
            foreach ($meta['columns'] as $column) {
                $sql = sprintf('UPDATE %s SET %s = :new WHERE %s = :old', $table, $column, $column);
                $statement = $db->prepare($sql);
                $statement->execute(['new' => $newFull, 'old' => $oldFull]);
                $statement->execute(['new' => $newNormalized, 'old' => $oldNormalized]);
            }
        }
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
