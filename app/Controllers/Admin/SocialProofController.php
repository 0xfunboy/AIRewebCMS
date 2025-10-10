<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\SocialProofItem;
use App\Services\Security\Csrf;
use App\Support\Flash;
use App\Support\Uploads;
use PDO;

final class SocialProofController extends Controller
{
    private SocialProofItem $items;
    private PDO $db;

    public function __construct()
    {
        $this->items = new SocialProofItem();
        $this->db = Database::connection();
    }

    public function index(): void
    {
        $stmt = $this->db->query('SELECT * FROM social_proof_items ORDER BY sort_order ASC, id ASC');
        $this->view('admin/social-proof/index', [
            'title' => 'Social Proof',
            'items' => $stmt->fetchAll() ?: [],
            'notice' => Flash::pull('admin.social.notice'),
            'error' => Flash::pull('admin.social.error'),
            'csrfToken' => Csrf::token(),
        ]);
    }

    public function create(): void
    {
        $this->renderForm($this->defaultItem(), [], 'Create Entry', '/admin/social-proof/store');
    }

    public function store(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, '/admin/social-proof/create');

        [$item, $errors] = $this->prepareInput($_POST, $_FILES);
        if ($errors) {
            $this->renderForm($item, $errors, 'Create Entry', '/admin/social-proof/store');
            return;
        }

        $this->items->create($item);
        Flash::set('admin.social.notice', 'Entry created.');
        $this->redirect('/admin/social-proof');
    }

    public function edit(string $id): void
    {
        $item = $this->items->find($id);
        if (!$item) {
            Flash::set('admin.social.error', 'Entry not found.');
            $this->redirect('/admin/social-proof');
        }

        $this->renderForm($item, [], 'Edit Entry', "/admin/social-proof/update/{$id}");
    }

    public function update(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, "/admin/social-proof/edit/{$id}");

        if (!$this->items->find($id)) {
            Flash::set('admin.social.error', 'Entry not found.');
            $this->redirect('/admin/social-proof');
        }

        [$item, $errors] = $this->prepareInput($_POST, $_FILES);
        if ($errors) {
            $item['id'] = $id;
            $this->renderForm($item, $errors, 'Edit Entry', "/admin/social-proof/update/{$id}");
            return;
        }

        $this->items->update($id, $item);
        Flash::set('admin.social.notice', 'Entry updated.');
        $this->redirect('/admin/social-proof');
    }

    public function destroy(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        if (!$this->items->find($id)) {
            Flash::set('admin.social.error', 'Entry not found.');
            $this->redirect('/admin/social-proof');
        }

        $this->items->delete($id);
        Flash::set('admin.social.notice', 'Entry removed.');
        $this->redirect('/admin/social-proof');
    }

    private function renderForm(array $item, array $errors, string $title, string $action): void
    {
        $this->view('admin/social-proof/form', [
            'title' => $title,
            'item' => $item,
            'errors' => $errors,
            'formAction' => $action,
            'submitLabel' => str_contains($title, 'Edit') ? 'Save Changes' : 'Create Entry',
            'csrfToken' => Csrf::token(),
        ]);
    }

    /**
     * @return array{0: array, 1: array}
     */
    private function prepareInput(array $source, array $files = []): array
    {
        $avatarUrl = trim((string)($source['author_avatar_url'] ?? ''));
        $item = [
            'content_type' => trim((string)($source['content_type'] ?? 'Tweet')),
            'author_name' => trim((string)($source['author_name'] ?? '')),
            'author_handle' => trim((string)($source['author_handle'] ?? '')),
            'author_avatar_url' => $avatarUrl,
            'content' => trim((string)($source['content'] ?? '')),
            'link' => trim((string)($source['link'] ?? '')),
            'sort_order' => max(0, (int)($source['sort_order'] ?? 0)),
        ];

        $uploadError = null;
        $hasUpload = $this->hasFileUpload($files['author_avatar_upload'] ?? null);
        if ($hasUpload) {
            try {
                $item['author_avatar_url'] = $this->storeUploadedFile($files['author_avatar_upload'], $item['author_name'] ?: 'social-proof-avatar');
            } catch (\Throwable $e) {
                $uploadError = $e->getMessage();
                $item['author_avatar_url'] = $avatarUrl;
            }
        }

        $isUploadValid = $hasUpload && $uploadError === null;

        $errors = $this->validate($item, $isUploadValid);
        if ($uploadError !== null) {
            $errors[] = $uploadError;
        }

        return [$item, $errors];
    }

    private function validate(array $item, bool $hasUpload): array
    {
        $errors = [];

        if (!in_array($item['content_type'], ['Tweet', 'Testimonial', 'Media'], true)) {
            $errors[] = 'Content type must be Tweet, Testimonial, or Media.';
        }

        if ($item['author_name'] === '') {
            $errors[] = 'Author name is required.';
        }

        if ($item['author_handle'] === '') {
            $errors[] = 'Author handle is required.';
        }

        if ($item['author_avatar_url'] === '' && !$hasUpload) {
            $errors[] = 'Provide an author avatar URL or upload a new image.';
        }

        if ($item['content'] === '') {
            $errors[] = 'Content text is required.';
        }

        if ($item['link'] === '') {
            $errors[] = 'Link is required.';
        }

        return $errors;
    }

    private function defaultItem(): array
    {
        return [
            'content_type' => 'Tweet',
            'author_name' => '',
            'author_handle' => '',
            'author_avatar_url' => '',
            'content' => '',
            'link' => '',
            'sort_order' => 0,
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

        Flash::set('admin.social.error', 'Session expired, please try again.');
        $this->redirect($redirect ?? '/admin/social-proof');
    }
}
