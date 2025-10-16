<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\LegalSection;
use App\Services\Security\Csrf;
use App\Support\Flash;
use App\Support\HtmlSanitizer;
use PDO;

final class LegalController extends Controller
{
    private LegalSection $sections;
    private PDO $db;

    public function __construct()
    {
        $this->sections = new LegalSection();
        $this->db = Database::connection();
    }

    public function index(): void
    {
        $stmt = $this->db->query('SELECT * FROM legal_sections ORDER BY sort_order ASC, id ASC');
        $this->view('admin/legal/index', [
            'title' => 'Legal Sections',
            'sections' => $stmt->fetchAll() ?: [],
            'notice' => Flash::pull('admin.legal.notice'),
            'error' => Flash::pull('admin.legal.error'),
            'csrfToken' => Csrf::token(),
        ]);
    }

    public function create(): void
    {
        $this->renderForm($this->defaultSection(), [], 'Create Legal Section', '/admin/legal/store');
    }

    public function store(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, '/admin/legal/create');

        [$section, $errors] = $this->prepareInput($_POST);
        if ($errors) {
            $this->renderForm($section, $errors, 'Create Legal Section', '/admin/legal/store');
            return;
        }

        $this->sections->create($section);
        Flash::set('admin.legal.notice', 'Legal section created.');
        $this->redirect('/admin/legal');
    }

    public function edit(string $id): void
    {
        $section = $this->sections->find($id);
        if (!$section) {
            Flash::set('admin.legal.error', 'Legal section not found.');
            $this->redirect('/admin/legal');
        }

        $this->renderForm($section, [], 'Edit Legal Section', "/admin/legal/update/{$id}");
    }

    public function update(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, "/admin/legal/edit/{$id}");

        if (!$this->sections->find($id)) {
            Flash::set('admin.legal.error', 'Legal section not found.');
            $this->redirect('/admin/legal');
        }

        [$section, $errors] = $this->prepareInput($_POST);
        if ($errors) {
            $section['id'] = $id;
            $this->renderForm($section, $errors, 'Edit Legal Section', "/admin/legal/update/{$id}");
            return;
        }

        $this->sections->update($id, $section);
        Flash::set('admin.legal.notice', 'Legal section updated.');
        $this->redirect('/admin/legal');
    }

    public function destroy(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        if (!$this->sections->find($id)) {
            Flash::set('admin.legal.error', 'Legal section not found.');
            $this->redirect('/admin/legal');
        }

        $this->sections->delete($id);
        Flash::set('admin.legal.notice', 'Legal section deleted.');
        $this->redirect('/admin/legal');
    }

    private function renderForm(array $section, array $errors, string $title, string $action): void
    {
        $this->view('admin/legal/form', [
            'title' => $title,
            'section' => $section,
            'errors' => $errors,
            'formAction' => $action,
            'submitLabel' => str_contains($title, 'Edit') ? 'Save Changes' : 'Create Legal Section',
            'csrfToken' => Csrf::token(),
        ]);
    }

    /**
     * @return array{0: array, 1: array}
     */
    private function prepareInput(array $source): array
    {
        $rawContent = trim((string)($source['content_html'] ?? ''));
        $section = [
            'title' => trim((string)($source['title'] ?? '')),
            'content_html' => HtmlSanitizer::sanitize($rawContent),
            'sort_order' => max(0, (int)($source['sort_order'] ?? 0)),
        ];

        $errors = [];

        if ($section['title'] === '') {
            $errors[] = 'Title is required.';
        }

        if ($section['content_html'] === '') {
            $errors[] = 'Provide content for the section.';
        }

        return [$section, $errors];
    }

    private function defaultSection(): array
    {
        return [
            'title' => '',
            'content_html' => '',
            'sort_order' => 0,
        ];
    }

    private function assertValidCsrf(?string $token, ?string $redirect = null): void
    {
        if (!Csrf::verify((string)$token)) {
            Flash::set('admin.legal.error', 'Invalid CSRF token.');
            $this->redirect($redirect ?? '/admin/legal');
        }
    }
}
