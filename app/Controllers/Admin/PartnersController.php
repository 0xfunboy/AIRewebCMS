<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Partner;
use App\Services\Security\Csrf;
use App\Support\Flash;
use App\Support\Uploads;
use PDO;

final class PartnersController extends Controller
{
    private Partner $partners;
    private PDO $db;

    public function __construct()
    {
        $this->partners = new Partner();
        $this->db = Database::connection();
    }

    public function index(): void
    {
        $stmt = $this->db->query('SELECT * FROM partners ORDER BY featured_order ASC, name ASC');
        $this->view('admin/partners/index', [
            'title' => 'Partners',
            'partners' => $stmt->fetchAll() ?: [],
            'notice' => Flash::pull('admin.partners.notice'),
            'error' => Flash::pull('admin.partners.error'),
            'csrfToken' => Csrf::token(),
        ]);
    }

    public function create(): void
    {
        $this->renderForm($this->defaultPartner(), [], 'Create Partner', '/admin/partners/store');
    }

    public function store(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, '/admin/partners/create');

        [$partner, $errors] = $this->prepareInput($_POST, $_FILES);
        if ($errors) {
            $this->renderForm($partner, $errors, 'Create Partner', '/admin/partners/store');
            return;
        }

        $this->partners->create($partner);
        Flash::set('admin.partners.notice', 'Partner created successfully.');
        $this->redirect('/admin/partners');
    }

    public function edit(string $id): void
    {
        $partner = $this->partners->find($id);
        if (!$partner) {
            Flash::set('admin.partners.error', 'Partner not found.');
            $this->redirect('/admin/partners');
        }

        $this->renderForm($partner, [], 'Edit Partner', "/admin/partners/update/{$id}");
    }

    public function update(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, "/admin/partners/edit/{$id}");

        if (!$this->partners->find($id)) {
            Flash::set('admin.partners.error', 'Partner not found.');
            $this->redirect('/admin/partners');
        }

        [$partner, $errors] = $this->prepareInput($_POST, $_FILES);
        if ($errors) {
            $partner['id'] = $id;
            $this->renderForm($partner, $errors, 'Edit Partner', "/admin/partners/update/{$id}");
            return;
        }

        $this->partners->update($id, $partner);
        Flash::set('admin.partners.notice', 'Partner updated successfully.');
        $this->redirect('/admin/partners');
    }

    public function destroy(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        if (!$this->partners->find($id)) {
            Flash::set('admin.partners.error', 'Partner not found.');
            $this->redirect('/admin/partners');
        }

        $this->partners->delete($id);
        Flash::set('admin.partners.notice', 'Partner deleted.');
        $this->redirect('/admin/partners');
    }

    private function renderForm(array $partner, array $errors, string $title, string $action): void
    {
        $this->view('admin/partners/form', [
            'title' => $title,
            'partner' => $partner,
            'errors' => $errors,
            'formAction' => $action,
            'submitLabel' => str_contains($title, 'Edit') ? 'Save Changes' : 'Create Partner',
            'csrfToken' => Csrf::token(),
        ]);
    }

    /**
     * @return array{0: array, 1: array}
     */
    private function prepareInput(array $source, array $files = []): array
    {
        $logoUrl = trim((string)($source['logo_url'] ?? ''));
        $partner = [
            'name' => trim((string)($source['name'] ?? '')),
            'logo_url' => $logoUrl,
            'url' => trim((string)($source['url'] ?? '')),
            'summary' => trim((string)($source['summary'] ?? '')),
            'status' => trim((string)($source['status'] ?? 'Active')),
            'featured_order' => max(0, (int)($source['featured_order'] ?? 0)),
        ];

        $uploadError = null;
        $hasUpload = $this->hasFileUpload($files['logo_upload'] ?? null);
        if ($hasUpload) {
            try {
                $partner['logo_url'] = $this->storeUploadedFile($files['logo_upload'], $partner['name'] ?: 'partner-logo');
            } catch (\Throwable $e) {
                $uploadError = $e->getMessage();
                $partner['logo_url'] = $logoUrl;
            }
        }

        $isUploadValid = $hasUpload && $uploadError === null;
        $errors = $this->validate($partner, $isUploadValid);
        if ($uploadError !== null) {
            $errors[] = $uploadError;
        }

        return [$partner, $errors];
    }

    private function validate(array $partner, bool $hasUpload): array
    {
        $errors = [];

        if ($partner['name'] === '') {
            $errors[] = 'Name is required.';
        }

        if ($partner['logo_url'] === '' && !$hasUpload) {
            $errors[] = 'Provide a logo URL or upload a new logo.';
        }

        if ($partner['url'] === '') {
            $errors[] = 'Website URL is required.';
        }

        if ($partner['summary'] === '') {
            $errors[] = 'Summary is required.';
        }

        if (!in_array($partner['status'], ['Active', 'In Discussion'], true)) {
            $errors[] = 'Status must be Active or In Discussion.';
        }

        return $errors;
    }

    private function defaultPartner(): array
    {
        return [
            'name' => '',
            'logo_url' => '',
            'url' => '',
            'summary' => '',
            'status' => 'Active',
            'featured_order' => 0,
        ];
    }

    private function hasFileUpload(mixed $file): bool
    {
        return is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    }

    private function storeUploadedFile(array $file, string $nameHint): string
    {
        $stored = Uploads::store($file, $nameHint);
        return '/' . ltrim($stored['path'], '/');
    }

    private function assertValidCsrf(?string $token, ?string $redirect = null): void
    {
        if (Csrf::verify($token)) {
            return;
        }

        Flash::set('admin.partners.error', 'Session expired, please try again.');
        $this->redirect($redirect ?? '/admin/partners');
    }
}
