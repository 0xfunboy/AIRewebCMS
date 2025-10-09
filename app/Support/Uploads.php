<?php
declare(strict_types=1);

namespace App\Support;

final class Uploads
{
    private const ALLOWED_EXT = ['png', 'jpg', 'jpeg', 'webp', 'svg'];
    private const MAX_BYTES = 5_000_000;

    /**
     * @return array{path:string,width:?int,height:?int}
     */
    public static function store(array $file, string $nameHint): array
    {
        if (!isset($file['tmp_name'], $file['error'], $file['size'])) {
            throw new \RuntimeException('File upload non valido.');
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Errore upload: ' . self::errorMessage($file['error']));
        }

        if ($file['size'] > self::MAX_BYTES) {
            throw new \RuntimeException('Il file supera il limite di 5 MB.');
        }

        $originalName = (string)($file['name'] ?? 'upload');
        $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($ext, self::ALLOWED_EXT, true)) {
            throw new \RuntimeException('Estensione non permessa.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']) ?: '';
        if (!self::isAllowedMime($mime, $ext)) {
            throw new \RuntimeException('Formato non riconosciuto.');
        }

        $dimensions = [null, null];
        if ($ext !== 'svg') {
            $info = @getimagesize($file['tmp_name']);
            if (!$info) {
                throw new \RuntimeException('Immagine non valida.');
            }
            $dimensions = [$info[0], $info[1]];
        } else {
            self::validateSvg($file['tmp_name']);
        }

        $slug = self::slugify($nameHint);
        $hash = substr(sha1_file($file['tmp_name']) ?: bin2hex(random_bytes(8)), 0, 10);
        $filename = sprintf('%s-%s.%s', $slug, $hash, $ext);

        $relativeDir = 'uploads/' . date('Y/m');
        $basePath = dirname(__DIR__, 2) . '/public/' . $relativeDir;
        if (!is_dir($basePath) && !mkdir($basePath, 0775, true) && !is_dir($basePath)) {
            throw new \RuntimeException('Impossibile creare la cartella upload.');
        }

        $target = $basePath . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            throw new \RuntimeException('Impossibile salvare il file caricato.');
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
        ];

        return isset($map[$ext]) && str_starts_with($mime, $map[$ext]);
    }

    private static function validateSvg(string $path): void
    {
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new \RuntimeException('Impossibile leggere lo SVG.');
        }

        $lower = strtolower($contents);
        if (str_contains($lower, '<script') || preg_match('/on\w+=/i', $contents)) {
            throw new \RuntimeException('Lo SVG contiene script non ammessi.');
        }
    }

    private static function errorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'File troppo grande.',
            UPLOAD_ERR_PARTIAL => 'Upload parziale.',
            UPLOAD_ERR_NO_FILE => 'Nessun file inviato.',
            UPLOAD_ERR_NO_TMP_DIR => 'Cartella temporanea mancante.',
            UPLOAD_ERR_CANT_WRITE => 'Impossibile scrivere su disco.',
            UPLOAD_ERR_EXTENSION => 'Upload bloccato da un\'estensione PHP.',
            default => 'Errore sconosciuto.',
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
