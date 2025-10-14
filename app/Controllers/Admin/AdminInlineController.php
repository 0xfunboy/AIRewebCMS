<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Container;
use App\Core\Controller;
use App\Core\Database;
use App\Core\Response;
use App\Services\Security\Csrf;
use App\Support\AdminMode;
use App\Support\HtmlSanitizer;
use App\Support\Media;
use App\Support\Uploads;
use PDO;

final class AdminInlineController extends Controller
{
    private PDO $db;

    /** @var array<string, array{table:string, id_column:string, allowed:array<string,string>, id_is_numeric?:bool}> */
    private array $modelMap = [
        'settings' => [
            'table' => 'settings',
            'id_column' => 'setting_key',
            'allowed' => [
                'site_name' => 'string',
                'site_tagline' => 'text',
                'roadmap_vision' => 'html',
                'contact_email' => 'string',
                'business_telegram' => 'url',
                'hero_title_home' => 'string',
                'hero_subtitle_home' => 'text',
                'hero_badge_home' => 'text',
                'hero_image_home' => 'image',
                'site_logo' => 'image',
                'favicon_path' => 'image',
                'og_image' => 'image',
                'seo_meta_title' => 'string',
                'seo_meta_description' => 'text',
                'seo_social_title' => 'string',
                'seo_social_description' => 'text',
                'seo_twitter_description' => 'text',
                'seo_telegram_description' => 'text',
                'seo_discord_description' => 'text',
                'seo_share_image' => 'image',
            ],
        ],
        'products' => [
            'table' => 'products',
            'id_column' => 'slug',
            'allowed' => [
                'name' => 'string',
                'description' => 'text',
                'hero_title' => 'string',
                'hero_subtitle' => 'text',
                'cta_text' => 'string',
                'cta_link' => 'url',
                'content_html' => 'html',
                'icon_key' => 'string',
                'external_link' => 'url',
            ],
        ],
        'agents' => [
            'table' => 'agents',
            'id_column' => 'id',
            'id_is_numeric' => true,
            'allowed' => [
                'name' => 'string',
                'chain' => 'string',
                'status' => 'string',
                'summary' => 'text',
                'site_url' => 'url',
                'image_url' => 'image',
                'badge' => 'string',
            ],
        ],
        'partners' => [
            'table' => 'partners',
            'id_column' => 'id',
            'id_is_numeric' => true,
            'allowed' => [
                'name' => 'string',
                'summary' => 'text',
                'url' => 'url',
                'status' => 'string',
                'logo_url' => 'image',
            ],
        ],
    ];

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function toggleMode(): void
    {
        if (!AdminMode::isAdmin()) {
            Response::json(['ok' => false, 'error' => 'Unauthorized'], 403);
            return;
        }

        $payload = json_decode(file_get_contents('php://input') ?: '[]', true);
        $enabled = !empty($payload['enabled']);
        $csrf = $payload['csrf'] ?? '';

        if (!Csrf::verify(is_string($csrf) ? $csrf : null)) {
            Response::json(['ok' => false, 'error' => 'Invalid CSRF token'], 403);
            return;
        }

        if ($enabled) {
            AdminMode::enable();
        } else {
            AdminMode::disable();
        }

        Response::json([
            'ok' => true,
            'enabled' => AdminMode::isEnabled(),
            'csrf' => Csrf::token(),
        ]);
    }

    public function updateField(): void
    {
        if (!AdminMode::isAdmin()) {
            Response::json(['ok' => false, 'error' => 'Unauthorized'], 403);
            return;
        }

        $payload = json_decode(file_get_contents('php://input') ?: '[]', true);
        if (!is_array($payload)) {
            Response::json(['ok' => false, 'error' => 'Invalid payload'], 400);
            return;
        }

        $csrf = (string)($payload['csrf'] ?? '');
        if (!Csrf::verify($csrf)) {
            Response::json(['ok' => false, 'error' => 'Invalid CSRF token'], 403);
            return;
        }

        $model = $payload['model'] ?? '';
        $key = $payload['key'] ?? '';
        $id = $payload['id'] ?? null;
        $value = $payload['value'] ?? '';

        if (!is_string($model) || !is_string($key)) {
            Response::json(['ok' => false, 'error' => 'Missing model or key'], 422);
            return;
        }

        $model = strtolower($model);
        if (!isset($this->modelMap[$model])) {
            Response::json(['ok' => false, 'error' => 'Model not allowed'], 422);
            return;
        }

        $meta = $this->modelMap[$model];
        if (!isset($meta['allowed'][$key])) {
            Response::json(['ok' => false, 'error' => 'Field not editable'], 422);
            return;
        }

        if ($model !== 'settings') {
            if ($id === null || (!is_string($id) && !is_numeric($id))) {
                Response::json(['ok' => false, 'error' => 'Missing id'], 422);
                return;
            }
        }

        $type = $meta['allowed'][$key];
        $cleanValue = $this->sanitizeValue($value, $type);
        $idValue = $this->normalizeId($id, !empty($meta['id_is_numeric']));

        try {
            $oldValue = $this->performUpdate($model, $key, $cleanValue, $idValue);
            $this->logChange($model, $key, $idValue, $oldValue, $cleanValue);
        } catch (\Throwable $e) {
            Response::json(['ok' => false, 'error' => $e->getMessage()], 500);
            return;
        }

        Response::json([
            'ok' => true,
            'value' => $cleanValue,
            'csrf' => Csrf::token(),
        ]);
    }

    public function uploadImage(): void
    {
        if (!AdminMode::isAdmin()) {
            Response::json(['ok' => false, 'error' => 'Unauthorized'], 403);
            return;
        }

        $model = $_POST['model'] ?? '';
        $key = $_POST['key'] ?? '';
        $id = $_POST['id'] ?? null;
        $csrf = $_POST['csrf'] ?? '';

        if (!Csrf::verify(is_string($csrf) ? $csrf : null)) {
            Response::json(['ok' => false, 'error' => 'Invalid CSRF token'], 403);
            return;
        }

        if (!isset($_FILES['file'])) {
            Response::json(['ok' => false, 'error' => 'Missing file'], 422);
            return;
        }

        $model = is_string($model) ? strtolower($model) : '';
        if (!isset($this->modelMap[$model])) {
            Response::json(['ok' => false, 'error' => 'Model not allowed'], 422);
            return;
        }

        $meta = $this->modelMap[$model];
        if (!isset($meta['allowed'][$key]) || $meta['allowed'][$key] !== 'image') {
            Response::json(['ok' => false, 'error' => 'Field not editable'], 422);
            return;
        }

        if ($model !== 'settings') {
            if ($id === null || (!is_string($id) && !is_numeric($id))) {
                Response::json(['ok' => false, 'error' => 'Missing id'], 422);
                return;
            }
        }

        $idValue = $this->normalizeId($id, !empty($meta['id_is_numeric']));

        try {
            $stored = Uploads::store($_FILES['file'], ($model === 'settings' ? $key : (string)$idValue) . '-' . $key);
            $storedPath = Media::normalizeMediaPath($stored['path']);
            if ($model === 'settings' && $key === 'site_logo') {
                $promoted = Media::promoteToSvgLibrary($stored['path'], 'logo/site-logo');
                if ($promoted !== null) {
                    $storedPath = $promoted;
                }
            }
            $oldValue = $this->performUpdate($model, $key, $storedPath, $idValue);
            $this->logChange($model, $key, $idValue, $oldValue, $storedPath);
        } catch (\Throwable $e) {
            Response::json(['ok' => false, 'error' => $e->getMessage()], 400);
            return;
        }

        Response::json([
            'ok' => true,
            'path' => $storedPath,
            'width' => $stored['width'],
            'height' => $stored['height'],
            'cache_buster' => '?v=' . time(),
            'csrf' => Csrf::token(),
        ]);
    }

    private function sanitizeValue(mixed $value, string $type): string
    {
        $value = is_string($value) ? trim($value) : '';

        return match ($type) {
            'string' => mb_substr(strip_tags($value), 0, 500),
            'text' => mb_substr(strip_tags($value), 0, 5000),
            'html' => $this->sanitizeHtml($value),
            'url' => $this->sanitizeUrl($value),
            'image' => $this->sanitizeImagePath($value),
            default => $value,
        };
    }

    private function sanitizeImagePath(string $value): string
    {
        return $value === '' ? '' : Media::normalizeMediaPath($value);
    }

    private function sanitizeHtml(string $value): string
    {
        return HtmlSanitizer::sanitize($value);
    }

    private function sanitizeUrl(string $value): string
    {
        $sanitized = filter_var($value, FILTER_SANITIZE_URL) ?: '';
        if ($sanitized !== '' && !preg_match('/^https?:\/\//i', $sanitized)) {
            $sanitized = 'https://' . ltrim($sanitized, '/');
        }
        return mb_substr($sanitized, 0, 500);
    }

    private function normalizeId(mixed $id, bool $numeric): string|int|null
    {
        if ($id === null) {
            return null;
        }

        if ($numeric) {
            if (!is_numeric($id)) {
                throw new \InvalidArgumentException('Invalid identifier.');
            }
            return (int)$id;
        }

        if (!is_string($id) || $id === '') {
            throw new \InvalidArgumentException('Invalid identifier.');
        }

        return $id;
    }

    private function performUpdate(string $model, string $key, string $value, string|int|null $id): ?string
    {
        $meta = $this->modelMap[$model];
        $table = $meta['table'];
        $idColumn = $meta['id_column'];

        if ($model === 'settings') {
            $stmt = $this->db->prepare("SELECT setting_value FROM {$table} WHERE setting_key = :key LIMIT 1");
            $stmt->execute(['key' => $key]);
            $oldValue = $stmt->fetchColumn();

            $upsert = $this->db->prepare(
                "INSERT INTO {$table} (setting_key, setting_value) VALUES (:key, :value)
                 ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)"
            );
            $upsert->execute([
                'key' => $key,
                'value' => $value,
            ]);

            return $oldValue !== false ? (string)$oldValue : null;
        }

        $selector = $meta['id_is_numeric'] ?? false ? (int)$id : (string)$id;

        $stmt = $this->db->prepare("SELECT {$key} FROM {$table} WHERE {$idColumn} = :id LIMIT 1");
        $stmt->execute(['id' => $selector]);
        $oldValue = $stmt->fetchColumn();
        if ($oldValue === false) {
            throw new \RuntimeException('Record not found.');
        }

        if ($model === 'agents' && $key === 'status') {
            $allowed = ['Live', 'In Development'];
            if (!in_array($value, $allowed, true)) {
                throw new \RuntimeException('Invalid status value.');
            }
        }

        if ($model === 'partners' && $key === 'status') {
            $allowed = ['Active', 'In Discussion'];
            if (!in_array($value, $allowed, true)) {
                throw new \RuntimeException('Invalid status value.');
            }
        }

        $sql = "UPDATE {$table} SET {$key} = :value WHERE {$idColumn} = :id LIMIT 1";
        $update = $this->db->prepare($sql);
        $update->execute([
            'value' => $value,
            'id' => $selector,
        ]);

        return (string)$oldValue;
    }

    private function logChange(string $model, string $key, string|int|null $id, ?string $old, string $new): void
    {
        $wallet = AdminMode::wallet() ?? 'unknown';
        $stmt = $this->db->prepare(
            'INSERT INTO audit_log (admin_address, model, field_key, id_ref, old_value, new_value)
             VALUES (:addr, :model, :field, :id_ref, :old_value, :new_value)'
        );
        $stmt->execute([
            'addr' => $wallet,
            'model' => $model,
            'field' => $key,
            'id_ref' => $id !== null ? (string)$id : null,
            'old_value' => $old,
            'new_value' => $new,
        ]);
    }
}
