<?php
declare(strict_types=1);

namespace App\Support;

final class Uploads
{
    private const ALLOWED_EXT = ['png', 'jpg', 'jpeg', 'webp', 'svg', 'ico'];
    private const MAX_BYTES = 5_000_000;
    private const BASE_DIR = 'media';

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

        return self::processFile(
            $file['tmp_name'],
            (string)($file['name'] ?? 'upload'),
            $nameHint,
            true,
            (int)($file['size'] ?? 0)
        );
    }

    /**
     * Store a file already present on disk (e.g. downloaded remotely).
     *
     * @return array{path:string,width:?int,height:?int}
     */
    public static function storeFromPath(string $path, string $originalName, string $nameHint): array
    {
        if (!is_file($path)) {
            throw new \RuntimeException('The provided path is not a file.');
        }

        $size = filesize($path);
        if ($size === false) {
            throw new \RuntimeException('Unable to determine file size.');
        }

        return self::processFile($path, $originalName, $nameHint, false, (int)$size);
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

    private static function processFile(string $sourcePath, string $originalName, string $nameHint, bool $isUploaded, int $size): array
    {
        if ($size > self::MAX_BYTES) {
            throw new \RuntimeException('File exceeds the 5 MB limit.');
        }

        $originalName = $originalName !== '' ? $originalName : 'upload';
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if ($ext === '') {
            $ext = self::detectExtensionFromMime($sourcePath);
        }

        if ($ext === '') {
            throw new \RuntimeException('File type not allowed.');
        }

        if (!in_array($ext, self::ALLOWED_EXT, true)) {
            throw new \RuntimeException('File type not allowed.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($sourcePath) ?: '';
        if (!self::isAllowedMime($mime, $ext)) {
            throw new \RuntimeException('File format not recognized.');
        }

        $dimensions = [null, null];
        $sanitizedSvg = null;
        if ($ext !== 'svg') {
            $info = @getimagesize($sourcePath);
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
            $sanitizedSvg = self::sanitizeSvgContents($sourcePath);
        }

        $slug = self::slugify($nameHint);
        $hash = substr(sha1_file($sourcePath) ?: bin2hex(random_bytes(8)), 0, 10);
        $filename = sprintf('%s-%s.%s', $slug, $hash, $ext);

        $relativeDir = self::BASE_DIR . '/' . date('Y/m');
        $basePath = dirname(__DIR__, 2) . '/public/' . $relativeDir;
        if (!is_dir($basePath) && !mkdir($basePath, 0775, true) && !is_dir($basePath)) {
            throw new \RuntimeException('Unable to create upload directory.');
        }

        $target = $basePath . '/' . $filename;
        if ($isUploaded) {
            if (!move_uploaded_file($sourcePath, $target)) {
                throw new \RuntimeException('Unable to save uploaded file.');
            }
        } else {
            if (!copy($sourcePath, $target)) {
                throw new \RuntimeException('Unable to copy file into media library.');
            }
        }

        if ($ext === 'svg' && $sanitizedSvg !== null) {
            if (file_put_contents($target, $sanitizedSvg, LOCK_EX) === false) {
                @unlink($target);
                throw new \RuntimeException('Unable to write sanitized SVG.');
            }

            $converted = self::convertSvgToPng($sanitizedSvg, $slug, $hash, $basePath);
            if ($converted !== null) {
                @unlink($target);
                $filename = $converted['filename'];
                $target = $basePath . '/' . $filename;
                $dimensions = [$converted['width'], $converted['height']];
            }
        }

        return [
            'path' => $relativeDir . '/' . $filename,
            'width' => $dimensions[0],
            'height' => $dimensions[1],
        ];
    }

    private static function sanitizeSvgContents(string $path): string
    {
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new \RuntimeException('Unable to read SVG file.');
        }

        $doc = new \DOMDocument();
        $previous = libxml_use_internal_errors(true);
        $loaded = $doc->loadXML($contents, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NONET);
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$loaded || !$doc->documentElement || strtolower($doc->documentElement->tagName) !== 'svg') {
            throw new \RuntimeException('Invalid SVG content.');
        }

        $allowedElements = [
            'svg', 'g', 'defs', 'lineargradient', 'radialgradient', 'stop', 'path', 'rect', 'circle',
            'ellipse', 'line', 'polyline', 'polygon', 'text', 'tspan', 'clippath', 'mask', 'use',
            'symbol', 'view', 'title', 'desc', 'metadata', 'pattern', 'image'
        ];

        $attributeAllowlist = [
            '*' => ['id', 'class', 'fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'stroke-dasharray',
                'stroke-miterlimit', 'opacity', 'transform', 'clip-path', 'clip-rule', 'fill-rule'],
            'svg' => ['viewBox', 'xmlns', 'xmlns:xlink', 'width', 'height'],
            'path' => ['d'],
            'rect' => ['x', 'y', 'width', 'height', 'rx', 'ry'],
            'circle' => ['cx', 'cy', 'r'],
            'ellipse' => ['cx', 'cy', 'rx', 'ry'],
            'line' => ['x1', 'y1', 'x2', 'y2'],
            'polyline' => ['points'],
            'polygon' => ['points'],
            'text' => ['x', 'y'],
            'tspan' => ['x', 'y'],
            'lineargradient' => ['x1', 'y1', 'x2', 'y2', 'gradientUnits', 'gradientTransform'],
            'radialgradient' => ['cx', 'cy', 'r', 'fx', 'fy', 'gradientUnits', 'gradientTransform'],
            'stop' => ['offset', 'stop-color', 'stop-opacity'],
            'use' => ['xlink:href', 'href', 'x', 'y', 'width', 'height'],
            'image' => ['x', 'y', 'width', 'height', 'href', 'xlink:href']
        ];

        self::sanitizeSvgNode($doc->documentElement, $allowedElements, $attributeAllowlist);

        return $doc->saveXML($doc->documentElement) ?: '';
    }

    private static function sanitizeSvgNode(\DOMNode $node, array $allowedElements, array $attributeAllowlist): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof \DOMElement) {
                $tag = strtolower($child->tagName);
                if (!in_array($tag, $allowedElements, true)) {
                    $child->parentNode?->removeChild($child);
                    continue;
                }

                self::sanitizeSvgAttributes($child, $tag, $attributeAllowlist);
                self::sanitizeSvgNode($child, $allowedElements, $attributeAllowlist);
            } elseif ($child instanceof \DOMComment) {
                $child->parentNode?->removeChild($child);
            }
        }
    }

    private static function sanitizeSvgAttributes(\DOMElement $element, string $tag, array $attributeAllowlist): void
    {
        $allowed = array_merge($attributeAllowlist['*'] ?? [], $attributeAllowlist[strtolower($tag)] ?? []);

        /** @var \DOMAttr $attribute */
        foreach (iterator_to_array($element->attributes) as $attribute) {
            $name = strtolower($attribute->name);
            $value = trim($attribute->value);

            if (str_starts_with($name, 'on')) {
                $element->removeAttributeNode($attribute);
                continue;
            }

            if (!in_array($name, $allowed, true)) {
                $element->removeAttributeNode($attribute);
                continue;
            }

            if ($name === 'href' || $name === 'xlink:href') {
                $lower = strtolower($value);
                if ($value === '' || str_starts_with($lower, 'javascript:') || str_starts_with($lower, 'data:') || str_starts_with($lower, 'http')) {
                    $element->removeAttributeNode($attribute);
                    continue;
                }

                if ($value[0] !== '#' && !preg_match('/^data:image\//i', $value)) {
                    $element->removeAttributeNode($attribute);
                    continue;
                }
            }

            if ($name === 'style') {
                $element->removeAttributeNode($attribute);
            }
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

    /**
     * @return array{filename:string,width:int,height:int}|null
     */
    private static function convertSvgToPng(string $svgContent, string $slug, string $hash, string $basePath): ?array
    {
        if (!class_exists(\Imagick::class)) {
            return null;
        }

        $imagick = new \Imagick();
        try {
            $imagick->setBackgroundColor('transparent');
            $imagick->setResolution(300, 300);
            $imagick->readImageBlob($svgContent);
            $imagick->setImageFormat('png32');

            $pngFilename = sprintf('%s-%s.png', $slug, $hash);
            $pngPath = $basePath . '/' . $pngFilename;
            if (!$imagick->writeImage($pngPath)) {
                return null;
            }

            return [
                'filename' => $pngFilename,
                'width' => $imagick->getImageWidth(),
                'height' => $imagick->getImageHeight(),
            ];
        } catch (\Throwable) {
            return null;
        } finally {
            $imagick->clear();
            $imagick->destroy();
        }
    }

    private static function detectExtensionFromMime(string $path): string
    {
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($path) ?: '';
        return match ($mime) {
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/webp' => 'webp',
            'image/svg+xml' => 'svg',
            'image/x-icon', 'image/vnd.microsoft.icon' => 'ico',
            default => '',
        };
    }
}
