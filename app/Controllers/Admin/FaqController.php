<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\FaqItem;
use App\Services\Security\Csrf;
use App\Support\Flash;
use PDO;

final class FaqController extends Controller
{
    private FaqItem $faqs;
    private PDO $db;

    public function __construct()
    {
        $this->faqs = new FaqItem();
        $this->db = Database::connection();
    }

    public function index(): void
    {
        $stmt = $this->db->query('SELECT * FROM faq_items ORDER BY sort_order ASC, id ASC');
        $this->view('admin/faq/index', [
            'title' => 'FAQ',
            'items' => $stmt->fetchAll() ?: [],
            'notice' => Flash::pull('admin.faq.notice'),
            'error' => Flash::pull('admin.faq.error'),
            'csrfToken' => Csrf::token(),
        ]);
    }

    public function create(): void
    {
        $this->renderForm($this->defaultItem(), [], 'Create FAQ Entry', '/admin/faq/store');
    }

    public function store(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, '/admin/faq/create');

        [$item, $errors] = $this->prepareInput($_POST);
        if ($errors) {
            $this->renderForm($item, $errors, 'Create FAQ Entry', '/admin/faq/store');
            return;
        }

        $this->faqs->create($item);
        Flash::set('admin.faq.notice', 'FAQ entry created.');
        $this->redirect('/admin/faq');
    }

    public function edit(string $id): void
    {
        $item = $this->faqs->find($id);
        if (!$item) {
            Flash::set('admin.faq.error', 'FAQ entry not found.');
            $this->redirect('/admin/faq');
        }

        $this->renderForm($item, [], 'Edit FAQ Entry', "/admin/faq/update/{$id}");
    }

    public function update(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, "/admin/faq/edit/{$id}");

        if (!$this->faqs->find($id)) {
            Flash::set('admin.faq.error', 'FAQ entry not found.');
            $this->redirect('/admin/faq');
        }

        [$item, $errors] = $this->prepareInput($_POST);
        if ($errors) {
            $item['id'] = $id;
            $this->renderForm($item, $errors, 'Edit FAQ Entry', "/admin/faq/update/{$id}");
            return;
        }

        $this->faqs->update($id, $item);
        Flash::set('admin.faq.notice', 'FAQ entry updated.');
        $this->redirect('/admin/faq');
    }

    public function destroy(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        if (!$this->faqs->find($id)) {
            Flash::set('admin.faq.error', 'FAQ entry not found.');
            $this->redirect('/admin/faq');
        }

        $this->faqs->delete($id);
        Flash::set('admin.faq.notice', 'FAQ entry deleted.');
        $this->redirect('/admin/faq');
    }

    private function renderForm(array $item, array $errors, string $title, string $action): void
    {
        $this->view('admin/faq/form', [
            'title' => $title,
            'item' => $item,
            'errors' => $errors,
            'formAction' => $action,
            'submitLabel' => str_contains($title, 'Edit') ? 'Save Changes' : 'Create FAQ Entry',
            'csrfToken' => Csrf::token(),
        ]);
    }

    /**
     * @return array{0: array, 1: array}
     */
    private function prepareInput(array $source): array
    {
        $item = [
            'question' => trim((string)($source['question'] ?? '')),
            'answer' => trim((string)($source['answer'] ?? '')),
            'sort_order' => max(0, (int)($source['sort_order'] ?? 0)),
        ];

        $errors = [];
        if ($item['question'] === '') {
            $errors[] = 'Question is required.';
        }

        if ($item['answer'] === '') {
            $errors[] = 'Answer is required.';
        }

        return [$item, $errors];
    }

    private function defaultItem(): array
    {
        return [
            'question' => '',
            'answer' => '',
            'sort_order' => 0,
        ];
    }

    private function assertValidCsrf(?string $token, ?string $redirect = null): void
    {
        if (!Csrf::verify((string)$token)) {
            Flash::set('admin.faq.error', 'Invalid CSRF token.');
            $this->redirect($redirect ?? '/admin/faq');
        }
    }
}
