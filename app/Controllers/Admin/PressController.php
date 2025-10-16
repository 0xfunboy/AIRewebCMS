<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\PressAsset;
use App\Services\Security\Csrf;
use App\Support\Flash;
use PDO;

final class PressController extends Controller
{
    private PressAsset $assets;
    private PDO $db;

    private const TYPES = ['Logo', 'Brand Guide', 'One-Pager'];

    public function __construct()
    {
        $this->assets = new PressAsset();
        $this->db = Database::connection();
    }

    public function index(): void
    {
        $stmt = $this->db->query('SELECT * FROM press_assets ORDER BY sort_order ASC, asset_type ASC, id ASC');
        $this->view('admin/press/index', [
            'title' => 'Press Assets',
            'assets' => $stmt->fetchAll() ?: [],
            'notice' => Flash::pull('admin.press.notice'),
            'error' => Flash::pull('admin.press.error'),
            'csrfToken' => Csrf::token(),
            'types' => self::TYPES,
        ]);
    }

    public function create(): void
    {
        $this->renderForm($this->defaultAsset(), [], 'Create Press Asset', '/admin/press/store');
    }

    public function store(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, '/admin/press/create');

        [$asset, $errors] = $this->prepareInput($_POST);
        if ($errors) {
            $this->renderForm($asset, $errors, 'Create Press Asset', '/admin/press/store');
            return;
        }

        $this->assets->create($asset);
        Flash::set('admin.press.notice', 'Press asset created.');
        $this->redirect('/admin/press');
    }

    public function edit(string $id): void
    {
        $asset = $this->assets->find($id);
        if (!$asset) {
            Flash::set('admin.press.error', 'Press asset not found.');
            $this->redirect('/admin/press');
        }

        $this->renderForm($asset, [], 'Edit Press Asset', "/admin/press/update/{$id}");
    }

    public function update(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, "/admin/press/edit/{$id}");

        if (!$this->assets->find($id)) {
            Flash::set('admin.press.error', 'Press asset not found.');
            $this->redirect('/admin/press');
        }

        [$asset, $errors] = $this->prepareInput($_POST);
        if ($errors) {
            $asset['id'] = $id;
            $this->renderForm($asset, $errors, 'Edit Press Asset', "/admin/press/update/{$id}");
            return;
        }

        $this->assets->update($id, $asset);
        Flash::set('admin.press.notice', 'Press asset updated.');
        $this->redirect('/admin/press');
    }

    public function destroy(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        if (!$this->assets->find($id)) {
            Flash::set('admin.press.error', 'Press asset not found.');
            $this->redirect('/admin/press');
        }

        $this->assets->delete($id);
        Flash::set('admin.press.notice', 'Press asset deleted.');
        $this->redirect('/admin/press');
    }

    private function renderForm(array $asset, array $errors, string $title, string $action): void
    {
        $this->view('admin/press/form', [
            'title' => $title,
            'asset' => $asset,
            'errors' => $errors,
            'formAction' => $action,
            'submitLabel' => str_contains($title, 'Edit') ? 'Save Changes' : 'Create Press Asset',
            'csrfToken' => Csrf::token(),
            'types' => self::TYPES,
        ]);
    }

    /**
     * @return array{0: array, 1: array}
     */
    private function prepareInput(array $source): array
    {
        $asset = [
            'asset_type' => trim((string)($source['asset_type'] ?? 'Logo')),
            'label' => trim((string)($source['label'] ?? '')),
            'file_path' => trim((string)($source['file_path'] ?? '')),
            'sort_order' => max(0, (int)($source['sort_order'] ?? 0)),
        ];

        $errors = [];

        if (!in_array($asset['asset_type'], self::TYPES, true)) {
            $errors[] = 'Select a valid asset type.';
        }

        if ($asset['label'] === '') {
            $errors[] = 'Label is required.';
        }

        if ($asset['file_path'] === '') {
            $errors[] = 'Provide a file path or URL.';
        }

        if ($asset['file_path'] !== '' && !str_starts_with($asset['file_path'], 'http') && !str_starts_with($asset['file_path'], '/')) {
            $asset['file_path'] = '/' . ltrim($asset['file_path'], '/');
        }

        return [$asset, $errors];
    }

    private function defaultAsset(): array
    {
        return [
            'asset_type' => 'Logo',
            'label' => '',
            'file_path' => '',
            'sort_order' => 0,
        ];
    }

    private function assertValidCsrf(?string $token, ?string $redirect = null): void
    {
        if (!Csrf::verify((string)$token)) {
            Flash::set('admin.press.error', 'Invalid CSRF token.');
            $this->redirect($redirect ?? '/admin/press');
        }
    }
}
