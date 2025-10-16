<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\CaseStudy;
use App\Services\Security\Csrf;
use App\Support\Flash;
use App\Support\Media;
use App\Support\Uploads;
use PDO;

final class CaseStudiesController extends Controller
{
    private CaseStudy $studies;
    private PDO $db;

    public function __construct()
    {
        $this->studies = new CaseStudy();
        $this->db = Database::connection();
    }

    public function index(): void
    {
        $stmt = $this->db->query('SELECT * FROM case_studies ORDER BY sort_order ASC, id ASC');
        $this->view('admin/case-studies/index', [
            'title' => 'Case Studies',
            'studies' => $stmt->fetchAll() ?: [],
            'notice' => Flash::pull('admin.case_studies.notice'),
            'error' => Flash::pull('admin.case_studies.error'),
            'csrfToken' => Csrf::token(),
        ]);
    }

    public function create(): void
    {
        $this->renderForm($this->defaultStudy(), [], 'Create Case Study', '/admin/case-studies/store');
    }

    public function store(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, '/admin/case-studies/create');

        [$study, $errors] = $this->prepareInput($_POST, $_FILES);
        if ($errors) {
            $this->renderForm($study, $errors, 'Create Case Study', '/admin/case-studies/store');
            return;
        }

        $this->studies->create($study);
        Flash::set('admin.case_studies.notice', 'Case study created.');
        $this->redirect('/admin/case-studies');
    }

    public function edit(string $id): void
    {
        $study = $this->studies->find($id);
        if (!$study) {
            Flash::set('admin.case_studies.error', 'Case study not found.');
            $this->redirect('/admin/case-studies');
        }

        $this->renderForm($study, [], 'Edit Case Study', "/admin/case-studies/update/{$id}");
    }

    public function update(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, "/admin/case-studies/edit/{$id}");

        if (!$this->studies->find($id)) {
            Flash::set('admin.case_studies.error', 'Case study not found.');
            $this->redirect('/admin/case-studies');
        }

        [$study, $errors] = $this->prepareInput($_POST, $_FILES);
        if ($errors) {
            $study['id'] = $id;
            $this->renderForm($study, $errors, 'Edit Case Study', "/admin/case-studies/update/{$id}");
            return;
        }

        $this->studies->update($id, $study);
        Flash::set('admin.case_studies.notice', 'Case study updated.');
        $this->redirect('/admin/case-studies');
    }

    public function destroy(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        if (!$this->studies->find($id)) {
            Flash::set('admin.case_studies.error', 'Case study not found.');
            $this->redirect('/admin/case-studies');
        }

        $this->studies->delete($id);
        Flash::set('admin.case_studies.notice', 'Case study deleted.');
        $this->redirect('/admin/case-studies');
    }

    private function renderForm(array $study, array $errors, string $title, string $action): void
    {
        $this->view('admin/case-studies/form', [
            'title' => $title,
            'study' => $study,
            'errors' => $errors,
            'formAction' => $action,
            'submitLabel' => str_contains($title, 'Edit') ? 'Save Changes' : 'Create Case Study',
            'csrfToken' => Csrf::token(),
        ]);
    }

    /**
     * @return array{0: array, 1: array}
     */
    private function prepareInput(array $source, array $files = []): array
    {
        $imageUrl = trim((string)($source['image_url'] ?? ''));
        $study = [
            'client' => trim((string)($source['client'] ?? '')),
            'chain' => trim((string)($source['chain'] ?? '')),
            'title' => trim((string)($source['title'] ?? '')),
            'summary' => trim((string)($source['summary'] ?? '')),
            'image_url' => $imageUrl,
            'sort_order' => max(0, (int)($source['sort_order'] ?? 0)),
        ];

        $uploadError = null;
        $hasUpload = $this->hasFileUpload($files['image_upload'] ?? null);
        if ($hasUpload) {
            try {
                $study['image_url'] = $this->storeUploadedFile($files['image_upload'], $study['title'] ?: 'case-study');
            } catch (\Throwable $e) {
                $uploadError = $e->getMessage();
                $study['image_url'] = $imageUrl;
            }
        }

        $study['image_url'] = $this->normalizeImageValue($study['image_url']);

        $errors = $this->validate($study, $hasUpload && $uploadError === null);
        if ($uploadError !== null) {
            $errors[] = $uploadError;
        }

        return [$study, $errors];
    }

    private function validate(array $study, bool $hasUpload): array
    {
        $errors = [];

        if ($study['client'] === '') {
            $errors[] = 'Client name is required.';
        }

        if ($study['chain'] === '') {
            $errors[] = 'Chain is required.';
        }

        if ($study['title'] === '') {
            $errors[] = 'Title is required.';
        }

        if ($study['summary'] === '') {
            $errors[] = 'Summary is required.';
        }

        if ($study['image_url'] === '' && !$hasUpload) {
            $errors[] = 'Provide an image URL or upload an image.';
        }

        return $errors;
    }

    private function defaultStudy(): array
    {
        return [
            'client' => '',
            'chain' => '',
            'title' => '',
            'summary' => '',
            'image_url' => '',
            'sort_order' => 0,
        ];
    }

    private function hasFileUpload(mixed $file): bool
    {
        return is_array($file)
            && isset($file['tmp_name'])
            && is_uploaded_file($file['tmp_name'] ?? '')
            && ($file['error'] ?? \UPLOAD_ERR_NO_FILE) === \UPLOAD_ERR_OK;
    }

    private function storeUploadedFile(array $file, string $prefix): string
    {
        $stored = Uploads::storeImage($file, [
            'directory' => 'media/' . date('Y/m'),
            'prefix' => Media::slugify($prefix),
        ]);

        return $stored['path'] ?? '';
    }

    private function normalizeImageValue(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return $value;
        }

        if (!str_starts_with($value, '/')) {
            $value = '/' . $value;
        }

        return $value;
    }

    private function assertValidCsrf(?string $token, ?string $redirect = null): void
    {
        if (!Csrf::verify((string)$token)) {
            Flash::set('admin.case_studies.error', 'Invalid CSRF token.');
            $this->redirect($redirect ?? '/admin/case-studies');
        }
    }
}
