<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Services\Security\Csrf;
use App\Support\Flash;
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
            'title' => 'Settings',
            'settings' => $settings,
            'notice' => Flash::pull('admin.settings.notice'),
            'error' => Flash::pull('admin.settings.error'),
            'csrfToken' => Csrf::token(),
        ]);
    }

    public function update(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        $settings = $_POST['settings'] ?? [];
        if (!is_array($settings)) {
            Flash::set('admin.settings.error', 'Invalid settings payload.');
            $this->redirect('/admin/settings');
        }

        $newKey = trim((string)($_POST['new_setting_key'] ?? ''));
        $newValue = (string)($_POST['new_setting_value'] ?? '');

        $this->db->beginTransaction();
        try {
            $update = $this->db->prepare('UPDATE settings SET setting_value = :value WHERE setting_key = :key');
            foreach ($settings as $key => $value) {
                $update->execute([
                    'key' => $key,
                    'value' => (string)$value,
                ]);
            }

            if ($newKey !== '') {
                $insert = $this->db->prepare(
                    'INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)
                     ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)'
                );
                $insert->execute([
                    'key' => $newKey,
                    'value' => $newValue,
                ]);
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            Flash::set('admin.settings.error', 'Unable to save settings.');
            $this->redirect('/admin/settings');
        }

        Flash::set('admin.settings.notice', 'Settings updated.');
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
}
