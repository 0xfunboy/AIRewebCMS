<?php
declare(strict_types=1);

namespace App\Support;

final class Uploads
{
    private const ALLOWED_EXT = ['png', 'jpg', 'jpeg', 'webp', 'svg', 'ico'];
    private const MAX_BYTES = 5_000_000;

    /**
     * @return array{path:string,width:?int,height:?int}
     */
    public static function store(array $file, string $nameHint): array
    {
        if (!isset($file['tmp_name'], $file['error'], $file['size'])) {
            throw new \RuntimeException('Invalid file upload payload.');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Upload error: ' . self::errorMessage($file['error']));
        }

        if ($file['size'] > self::MAX_BYTES) {
            throw new \RuntimeException('File exceeds the 5 MB limit.');
        }

        $originalName = (string)($file['name'] ?? 'upload');
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXT, true)) {
            throw new \RuntimeException('File type not allowed.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']) ?: '';
        if (!self::isAllowedMime($mime, $ext)) {
            throw new \RuntimeException('File format not recognized.');
        }

        $dimensions = [null, null];
        if ($ext !== 'svg') {
            $info = @getimagesize($file['tmp_name']);
            if (!$info) {
                if ($ext === 'ico') {
                    $dimensions = [null, null];
                } else {
                    throw new \RuntimeException('Invalid image file.');
                }
            } else {
                $dimensions = [$info[0], $info[1]];
            }
        } else {
            self::validateSvg($file['tmp_name']);
        }

        $slug = self::slugify($nameHint);
        $hash = substr(sha1_file($file['tmp_name']) ?: bin2hex(random_bytes(8)), 0, 10);
        $filename = sprintf('%s-%s.%s', $slug, $hash, $ext);

        $relativeDir = 'uploads/' . date('Y/m');
        $basePath = dirname(__DIR__, 2) . '/public/' . $relativeDir;
        if (!is_dir($basePath) && !mkdir($basePath, 0775, true) && !is_dir($basePath)) {
            throw new \RuntimeException('Unable to create upload directory.');
        }

        $target = $basePath . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            throw new \RuntimeException('Unable to save uploaded file.');
        }

        return [
            'path' => $relativeDir . '/' . $filename,
            'width' => $dimensions[0],
            'height' => $dimensions[1],
        ];
    }

    private static function isAllowedMime(string $mime, string $ext): bool
    {
        $mime = strtolower($mime);
        $map = [
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
        ];

        if (!isset($map[$ext])) {
            return false;
        }

        if ($ext === 'ico') {
            return str_starts_with($mime, 'image/x-icon') || str_starts_with($mime, 'image/vnd.microsoft.icon');
        }

        return str_starts_with($mime, $map[$ext]);
    }

    private static function validateSvg(string $path): void
    {
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new \RuntimeException('Unable to read SVG file.');
        }

        $lower = strtolower($contents);
        if (str_contains($lower, '<script') || preg_match('/on\w+=/i', $contents)) {
            throw new \RuntimeException('SVG contains disallowed scripts.');
        }
    }

    private static function errorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'File too large.',
            UPLOAD_ERR_PARTIAL => 'Upload was incomplete.',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Temporary folder is missing.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write to disk.',
            UPLOAD_ERR_EXTENSION => 'Upload blocked by a PHP extension.',
            default => 'Unknown upload error.',
        };
    }

    public static function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? $value;
        $value = trim($value, '-');
        return $value !== '' ? $value : 'image';
    }
}
