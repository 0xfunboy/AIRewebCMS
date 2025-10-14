<?php
declare(strict_types=1);

namespace App\Services\Admin;

use App\Support\Media;
use App\Support\Uploads;
use PDO;

final class MediaOptimizer
{
    private PDO $db;

    /** @var array<string,array{primary:string,columns:string[]}> */
    private array $tableMap = [
        'partners' => ['primary' => 'id', 'columns' => ['logo_url', 'badge_logo_url']],
        'agents' => ['primary' => 'id', 'columns' => ['image_url']],
        'team_members' => ['primary' => 'id', 'columns' => ['avatar_url']],
        'social_proof_items' => ['primary' => 'id', 'columns' => ['author_avatar_url']],
        'blog_posts' => ['primary' => 'id', 'columns' => ['image_url']],
    ];

    private string $mediaRoot;

    public function __construct(PDO $db)
    {
        $this->db = $db;
        $this->mediaRoot = dirname(__DIR__, 3) . '/public/media';
    }

    /**
     * @return array{
     *     steps: array<int,array<string,mixed>>,
     *     phase1: array{processed:int,total:int,errors:int},
     *     phase2: array{processed:int,total:int,errors:int}
     * }
     */
    public function run(): array
    {
        $steps = [];

        $phase1 = $this->mirrorRemoteAssets($steps);
        $phase2 = $this->convertLocalImagesToWebp($steps);

        return [
            'steps' => $steps,
            'phase1' => $phase1,
            'phase2' => $phase2,
        ];
    }

    /**
     * Execute only the remote mirroring phase.
     *
     * @return array{steps:array<int,array<string,mixed>>,processed:int,total:int,errors:int}
     */
    public function mirror(): array
    {
        $steps = [];
        $phase = $this->mirrorRemoteAssets($steps);

        return [
            'steps' => $steps,
            'processed' => $phase['processed'],
            'total' => $phase['total'],
            'errors' => $phase['errors'],
        ];
    }

    /**
     * Execute only the local WebP optimization phase.
     *
     * @return array{steps:array<int,array<string,mixed>>,processed:int,total:int,errors:int}
     */
    public function optimize(): array
    {
        $steps = [];
        $phase = $this->convertLocalImagesToWebp($steps);

        return [
            'steps' => $steps,
            'processed' => $phase['processed'],
            'total' => $phase['total'],
            'errors' => $phase['errors'],
        ];
    }

    /**
     * @param array<int,array<string,mixed>> $steps
     * @return array{processed:int,total:int,errors:int}
     */
    private function mirrorRemoteAssets(array &$steps): array
    {
        $tasks = $this->collectRemoteReferences();
        $total = count($tasks);
        $processed = 0;
        $errors = 0;

        $counter = 0;
        foreach ($tasks as $task) {
            $counter++;

            try {
                $newPath = $this->downloadRemoteAsset($task);
                if ($newPath !== null) {
                    $processed++;
                    $steps[] = $this->buildStep(
                        1,
                        $counter,
                        $total,
                        sprintf('Mirrored %s', $task['label'])
                    );
                } else {
                    $steps[] = $this->buildStep(
                        1,
                        $counter,
                        $total,
                        sprintf('Skipped %s (already local)', $task['label']),
                        'skip'
                    );
                }
            } catch (\Throwable $e) {
                $errors++;
                $steps[] = $this->buildStep(
                    1,
                    $counter,
                    $total,
                    sprintf('Failed %s: %s', $task['label'], $e->getMessage()),
                    'error'
                );
            }
        }

        return [
            'processed' => $processed,
            'total' => $total,
            'errors' => $errors,
        ];
    }

    /**
     * @param array<int,array<string,mixed>> $steps
     * @return array{processed:int,total:int,errors:int}
     */
    private function convertLocalImagesToWebp(array &$steps): array
    {
        if (!is_dir($this->mediaRoot)) {
            return [
                'processed' => 0,
                'total' => 0,
                'errors' => 0,
            ];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->mediaRoot, \FilesystemIterator::SKIP_DOTS)
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $extension = strtolower($file->getExtension());
            if (!in_array($extension, ['png', 'jpg', 'jpeg', 'webp'], true)) {
                continue;
            }

            if ($extension === 'webp') {
                continue;
            }

            $files[] = $file->getPathname();
        }

        $total = count($files);
        $processed = 0;
        $errors = 0;
        $counter = 0;

        foreach ($files as $path) {
            $counter++;
            $relative = ltrim(str_replace($this->mediaRoot, '', $path), '/');
            $publicOld = '/' . ltrim(str_replace('\\', '/', 'media/' . $relative), '/');

            try {
                $result = $this->convertFileToWebp($path);
                if ($result === null) {
                    $steps[] = $this->buildStep(
                        2,
                        $counter,
                        $total,
                        sprintf('Skipped %s', $publicOld),
                        'skip'
                    );
                    continue;
                }

                [$publicNew, $width, $height] = $result;
                $this->updateAllReferences($publicOld, $publicNew);

                $steps[] = $this->buildStep(
                    2,
                    $counter,
                    $total,
                    sprintf('Optimized %s → %s', $publicOld, $publicNew)
                );
                $processed++;
            } catch (\Throwable $e) {
                $errors++;
                $steps[] = $this->buildStep(
                    2,
                    $counter,
                    $total,
                    sprintf('Failed %s: %s', $publicOld, $e->getMessage()),
                    'error'
                );
            }
        }

        return [
            'processed' => $processed,
            'total' => $total,
            'errors' => $errors,
        ];
    }

    /**
     * @return array<int,array{table:string,column?:string,key?:string,id?:int,value:string,label:string}>
     */
    private function collectRemoteReferences(): array
    {
        $tasks = [];

        $settingsStmt = $this->db->query(
            "SELECT setting_key, setting_value FROM settings
             WHERE setting_value LIKE 'http%' AND (
                setting_key LIKE '%image%' OR
                setting_key LIKE '%logo%' OR
                setting_key LIKE '%icon%' OR
                setting_key LIKE '%favicon%'
             )"
        );

        if ($settingsStmt) {
            foreach ($settingsStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $tasks[] = [
                    'table' => 'settings',
                    'key' => $row['setting_key'],
                    'value' => $row['setting_value'],
                    'label' => sprintf('settings.%s', $row['setting_key']),
                ];
            }
        }

        foreach ($this->tableMap as $table => $meta) {
            $primary = $meta['primary'];
            foreach ($meta['columns'] as $column) {
                $stmt = $this->db->prepare("SELECT {$primary} AS id, {$column} AS url FROM {$table} WHERE {$column} LIKE 'http%'");
                $stmt->execute();
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                    if (empty($row['url'])) {
                        continue;
                    }
                    $tasks[] = [
                        'table' => $table,
                        'id' => (int)$row['id'],
                        'column' => $column,
                        'value' => $row['url'],
                        'label' => sprintf('%s#%d.%s', $table, (int)$row['id'], $column),
                    ];
                }
            }
        }

        return $tasks;
    }

    /**
     * @param array{table:string,column?:string,key?:string,id?:int,value:string,label:string} $task
     */
    private function downloadRemoteAsset(array $task): ?string
    {
        $url = $task['value'];
        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            return null;
        }

        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'follow_location' => 1,
            ],
        ]);

        $contents = @file_get_contents($url, false, $context);
        if ($contents === false) {
            throw new \RuntimeException('Unable to download remote asset.');
        }

        $tmp = tempnam(sys_get_temp_dir(), 'media');
        if ($tmp === false || file_put_contents($tmp, $contents) === false) {
            throw new \RuntimeException('Unable to write temporary file.');
        }

        try {
            $basename = basename(parse_url($url, PHP_URL_PATH) ?? '') ?: 'remote';
            $stored = Uploads::storeFromPath($tmp, $basename, $task['label']);
        } finally {
            @unlink($tmp);
        }

        $newPath = Media::normalizeMediaPath($stored['path']);
        $this->updateReference($task, $newPath);

        return $newPath;
    }

    /**
     * @param array{table:string,column?:string,key?:string,id?:int,label:string} $task
     */
    private function updateReference(array $task, string $newPath): void
    {
        if ($task['table'] === 'settings') {
            $stmt = $this->db->prepare('UPDATE settings SET setting_value = :value WHERE setting_key = :key');
            $stmt->execute([
                'value' => $newPath,
                'key' => $task['key'],
            ]);
            return;
        }

        $primary = $this->tableMap[$task['table']]['primary'];
        $stmt = $this->db->prepare(
            sprintf('UPDATE %s SET %s = :value WHERE %s = :id', $task['table'], $task['column'], $primary)
        );
        $stmt->execute([
            'value' => $newPath,
            'id' => $task['id'],
        ]);
    }

    private function convertFileToWebp(string $absolutePath): ?array
    {
        $extension = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));
        $targetPath = preg_replace('/\.[^.]+$/', '.webp', $absolutePath) ?: ($absolutePath . '.webp');
        $publicNew = '/media/' . ltrim(str_replace($this->mediaRoot, '', $targetPath), '/');
        $width = null;
        $height = null;

        if (!class_exists(\Imagick::class) && !function_exists('imagewebp')) {
            throw new \RuntimeException('WebP conversion requires Imagick or GD with WebP support.');
        }

        if (class_exists(\Imagick::class)) {
            $imagick = new \Imagick();
            try {
                $imagick->readImage($absolutePath);
                $imagick->setFormat('webp');
                $imagick->setOption('webp:method', '6');
                if ($extension === 'png') {
                    $imagick->setOption('webp:lossless', 'true');
                }
                if (!$imagick->writeImage($targetPath)) {
                    throw new \RuntimeException('Imagick failed to write webp file.');
                }
                $width = $imagick->getImageWidth();
                $height = $imagick->getImageHeight();
            } finally {
                $imagick->clear();
                $imagick->destroy();
            }
        } else {
            switch ($extension) {
                case 'jpeg':
                case 'jpg':
                    if (!function_exists('imagecreatefromjpeg')) {
                        throw new \RuntimeException('GD JPEG support is not available.');
                    }
                    $image = @imagecreatefromjpeg($absolutePath);
                    break;
                case 'png':
                    if (!function_exists('imagecreatefrompng')) {
                        throw new \RuntimeException('GD PNG support is not available.');
                    }
                    $image = @imagecreatefrompng($absolutePath);
                    if ($image) {
                        imagepalettetotruecolor($image);
                        imagealphablending($image, true);
                        imagesavealpha($image, true);
                    }
                    break;
                default:
                    throw new \RuntimeException('Conversion requires Imagick for this format.');
            }

            if (!$image) {
                throw new \RuntimeException('Unable to create image resource.');
            }

            if (!function_exists('imagewebp')) {
                imagedestroy($image);
                throw new \RuntimeException('GD WebP support is not available.');
            }

            $width = imagesx($image);
            $height = imagesy($image);
            if (!@imagewebp($image, $targetPath, 85)) {
                imagedestroy($image);
                throw new \RuntimeException('GD failed to write webp file.');
            }
            imagedestroy($image);
        }

        @unlink($absolutePath);

        return [$publicNew, $width ?? null, $height ?? null];
    }

    private function updateAllReferences(string $oldPath, string $newPath): void
    {
        $this->db
            ->prepare('UPDATE settings SET setting_value = :new WHERE setting_value = :old')
            ->execute(['new' => $newPath, 'old' => $oldPath]);

        $plainOld = ltrim($oldPath, '/');
        $plainNew = ltrim($newPath, '/');
        if ($plainOld !== $oldPath) {
            $this->db
                ->prepare('UPDATE settings SET setting_value = :new WHERE setting_value = :old')
                ->execute(['new' => $plainNew, 'old' => $plainOld]);
        }

        foreach ($this->tableMap as $table => $meta) {
            foreach ($meta['columns'] as $column) {
                $stmt = $this->db->prepare(
                    sprintf('UPDATE %s SET %s = :new WHERE %s = :old', $table, $column, $column)
                );
                $stmt->execute([
                    'new' => $newPath,
                    'old' => $oldPath,
                ]);

                if ($plainOld !== $oldPath) {
                    $stmt->execute([
                        'new' => $plainNew,
                        'old' => $plainOld,
                    ]);
                }
            }
        }
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
}
