<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Services\Security\Csrf;
use App\Support\Flash;
use App\Support\Media;
use App\Support\Uploads;
use PDO;

final class SettingsController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function index(): void
    {
        $stmt = $this->db->query('SELECT setting_key, setting_value FROM settings ORDER BY setting_key ASC');
        $settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];

        $this->view('admin/settings/index', [
            'title' => 'Site Settings',
            'settings' => $settings,
            'notice' => Flash::pull('admin.settings.notice'),
            'error' => Flash::pull('admin.settings.error'),
            'csrfToken' => Csrf::token(),
        ]);
    }

    public function update(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        try {
            $action = $this->resolveAction($_POST['action'] ?? null);
            $message = match ($action) {
                'general' => $this->handleGeneralUpdate(),
                'brand' => $this->handleBrandUpdate(),
                'seo' => $this->handleSeoUpdate(),
                default => throw new \InvalidArgumentException('Unknown settings action.'),
            };
            Flash::set('admin.settings.notice', $message);
        } catch (\InvalidArgumentException $e) {
            Flash::set('admin.settings.error', $e->getMessage());
        } catch (\Throwable $e) {
            Flash::set('admin.settings.error', 'Unable to save settings. Please try again.');
        }

        $this->redirect('/admin/settings');
    }

    private function assertValidCsrf(?string $token): void
    {
        if (Csrf::verify($token)) {
            return;
        }

        Flash::set('admin.settings.error', 'Session expired, please try again.');
        $this->redirect('/admin/settings');
    }

    private function resolveAction(mixed $action): string
    {
        if (!is_string($action)) {
            return '';
        }

        return strtolower(trim($action));
    }

    private function handleGeneralUpdate(): string
    {
        $payload = [
            'site_name' => $this->sanitizeShortText($_POST['site_name'] ?? ''),
            'site_tagline' => $this->sanitizeShortText($_POST['site_tagline'] ?? '', 160),
            'contact_email' => $this->sanitizeEmail($_POST['contact_email'] ?? ''),
            'business_telegram' => $this->sanitizeUrl($_POST['business_telegram'] ?? ''),
        ];

        $this->persistSettings($payload);
        return 'General settings saved.';
    }

    private function handleBrandUpdate(): string
    {
        $updates = [];

        if (isset($_FILES['site_logo']) && is_array($_FILES['site_logo']) && $this->hasFileUpload($_FILES['site_logo'])) {
            $stored = Uploads::store($_FILES['site_logo'], 'site-logo');
            $promoted = Media::promoteToSvgLibrary($stored['path'], 'logo/site-logo');
            $updates['site_logo'] = $promoted ?? Media::normalizeMediaPath($stored['path']);
        }

        if (isset($_FILES['favicon']) && is_array($_FILES['favicon']) && $this->hasFileUpload($_FILES['favicon'])) {
            $stored = Uploads::store($_FILES['favicon'], 'favicon');
            $updates['favicon_path'] = Media::normalizeMediaPath($stored['path']);
        }

        if (empty($updates)) {
            throw new \InvalidArgumentException('Upload a logo or favicon before saving.');
        }

        $this->persistSettings($updates);
        return 'Brand assets updated.';
    }

    private function handleSeoUpdate(): string
    {
        $payload = [
            'seo_meta_title' => $this->sanitizeShortText($_POST['seo_meta_title'] ?? '', 120),
            'seo_social_title' => $this->sanitizeShortText($_POST['seo_social_title'] ?? '', 120),
            'seo_meta_description' => $this->sanitizeLongText($_POST['seo_meta_description'] ?? ''),
            'seo_social_description' => $this->sanitizeLongText($_POST['seo_social_description'] ?? ''),
            'seo_twitter_description' => $this->sanitizeLongText($_POST['seo_twitter_description'] ?? '', 280),
            'seo_telegram_description' => $this->sanitizeLongText($_POST['seo_telegram_description'] ?? '', 512),
            'seo_discord_description' => $this->sanitizeLongText($_POST['seo_discord_description'] ?? '', 512),
        ];

        if (isset($_FILES['seo_share_image']) && is_array($_FILES['seo_share_image']) && $this->hasFileUpload($_FILES['seo_share_image'])) {
            $stored = Uploads::store($_FILES['seo_share_image'], 'seo-share-image');
            $path = Media::normalizeMediaPath($stored['path']);
            $payload['seo_share_image'] = $path;
            $payload['og_image'] = $path;
        }

        $this->persistSettings($payload);
        return 'SEO settings saved.';
    }


    private function hasFileUpload(array $file): bool
    {
        $error = $file['error'] ?? UPLOAD_ERR_NO_FILE;
        return $error !== UPLOAD_ERR_NO_FILE;
    }

    /**
     * @param array<string,string> $settings
     */
    private function persistSettings(array $settings): void
    {
        $filtered = array_filter(
            $settings,
            static fn($value, $key) => is_string($key),
            ARRAY_FILTER_USE_BOTH
        );

        if (empty($filtered)) {
            return;
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO settings (setting_key, setting_value)
                 VALUES (:key, :value)
                 ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
            );

            foreach ($filtered as $key => $value) {
                $stmt->execute([
                    'key' => $key,
                    'value' => $value,
                ]);
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function sanitizeShortText(mixed $value, int $max = 80): string
    {
        if (!is_string($value)) {
            return '';
        }

        $clean = strip_tags(trim($value));
        return mb_substr($clean, 0, $max);
    }

    private function sanitizeLongText(mixed $value, int $max = 500): string
    {
        if (!is_string($value)) {
            return '';
        }

        $clean = strip_tags(trim($value));
        return mb_substr($clean, 0, $max);
    }

    private function sanitizeEmail(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }

        $email = filter_var(trim($value), FILTER_VALIDATE_EMAIL);
        return $email !== false ? $email : '';
    }

    private function sanitizeUrl(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }

        $normalized = trim($value);
        if ($normalized === '') {
            return '';
        }

        $normalized = filter_var($normalized, FILTER_SANITIZE_URL) ?: '';
        if ($normalized === '') {
            return '';
        }

        if (!preg_match('/^https?:\/\//i', $normalized)) {
            $normalized = 'https://' . ltrim($normalized, '/');
        }

        return mb_substr($normalized, 0, 500);
    }
}
