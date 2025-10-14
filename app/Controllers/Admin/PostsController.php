<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\BlogPost;
use App\Services\Security\Csrf;
use App\Support\Flash;
use App\Support\Media;
use App\Support\Uploads;
use PDO;

final class PostsController extends Controller
{
    private BlogPost $posts;
    private PDO $db;

    public function __construct()
    {
        $this->posts = new BlogPost();
        $this->db = Database::connection();
    }

    public function index(): void
    {
        $stmt = $this->db->query('SELECT * FROM blog_posts ORDER BY published_at DESC, id DESC');
        $posts = $stmt->fetchAll() ?: [];

        $this->view('admin/posts/index', [
            'title' => 'Blog Posts',
            'posts' => $posts,
            'notice' => Flash::pull('admin.posts.notice'),
            'error' => Flash::pull('admin.posts.error'),
            'csrfToken' => Csrf::token(),
        ]);
    }

    public function create(): void
    {
        $this->renderForm($this->defaultPost(), [], 'Create Post', '/admin/posts/store', 'create');
    }

    public function store(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, '/admin/posts/create');

        [$post, $errors] = $this->prepareInput($_POST, $_FILES);
        if ($errors) {
            $this->renderForm($post, $errors, 'Create Post', '/admin/posts/store', 'create');
            return;
        }

        $post['is_published'] = $post['is_published'] ? 1 : 0;
        $this->posts->create($post);

        Flash::set('admin.posts.notice', 'Post created successfully.');
        $this->redirect('/admin/posts');
    }

    public function edit(string $id): void
    {
        $post = $this->posts->find($id);
        if (!$post) {
            Flash::set('admin.posts.error', 'Post not found.');
            $this->redirect('/admin/posts');
        }

        $post['is_published'] = (bool)$post['is_published'];

        $this->renderForm($post, [], 'Edit Post', "/admin/posts/update/{$id}", 'edit');
    }

    public function update(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, "/admin/posts/edit/{$id}");

        if (!$this->posts->find($id)) {
            Flash::set('admin.posts.error', 'Post not found.');
            $this->redirect('/admin/posts');
        }

        [$post, $errors] = $this->prepareInput($_POST, $_FILES, (int)$id);
        if ($errors) {
            $post['id'] = $id;
            $this->renderForm($post, $errors, 'Edit Post', "/admin/posts/update/{$id}", 'edit');
            return;
        }

        $post['is_published'] = $post['is_published'] ? 1 : 0;
        $this->posts->update($id, $post);

        Flash::set('admin.posts.notice', 'Post updated successfully.');
        $this->redirect('/admin/posts');
    }

    public function destroy(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        if (!$this->posts->find($id)) {
            Flash::set('admin.posts.error', 'Post not found.');
            $this->redirect('/admin/posts');
        }

        $this->posts->delete($id);

        Flash::set('admin.posts.notice', 'Post deleted.');
        $this->redirect('/admin/posts');
    }

    private function renderForm(array $post, array $errors, string $title, string $action, string $mode): void
    {
        $this->view('admin/posts/form', [
            'title' => $title,
            'post' => $post,
            'errors' => $errors,
            'formAction' => $action,
            'submitLabel' => $mode === 'edit' ? 'Save Changes' : 'Create Post',
            'csrfToken' => Csrf::token(),
        ]);
    }

    /**
     * @return array{0: array, 1: array}
     */
    private function prepareInput(array $source, array $files = [], ?int $ignoreId = null): array
    {
        $imageUrl = trim((string)($source['image_url'] ?? ''));
        $post = [
            'title' => trim((string)($source['title'] ?? '')),
            'slug' => trim((string)($source['slug'] ?? '')),
            'published_at' => trim((string)($source['published_at'] ?? '')),
            'image_url' => $imageUrl,
            'snippet' => trim((string)($source['snippet'] ?? '')),
            'content_html' => (string)($source['content_html'] ?? ''),
            'is_published' => isset($source['is_published']) && $source['is_published'],
        ];

        $uploadError = null;
        $hasUpload = $this->hasFileUpload($files['image_upload'] ?? null);
        if ($hasUpload) {
            try {
                $post['image_url'] = $this->storeUploadedFile($files['image_upload'], $post['title'] ?: 'blog-post');
            } catch (\Throwable $e) {
                $uploadError = $e->getMessage();
                $post['image_url'] = $imageUrl;
            }
        }

        $post['image_url'] = $this->normalizeImageValue($post['image_url']);

        $isUploadValid = $hasUpload && $uploadError === null;

        $errors = $this->validate($post, $ignoreId, $isUploadValid);
        if ($uploadError !== null) {
            $errors[] = $uploadError;
        }

        return [$post, $errors];
    }

    private function validate(array $post, ?int $ignoreId = null, bool $hasUpload = false): array
    {
        $errors = [];

        if ($post['title'] === '') {
            $errors[] = 'Title is required.';
        }

        if ($post['slug'] === '') {
            $errors[] = 'Slug is required.';
        } elseif (!preg_match('/^[a-z0-9\\-]+$/', $post['slug'])) {
            $errors[] = 'Slug may only contain lowercase letters, numbers, and hyphens.';
        } elseif ($this->slugExists($post['slug'], $ignoreId)) {
            $errors[] = 'Slug is already in use.';
        }

        if ($post['published_at'] === '' || !$this->validateDate($post['published_at'])) {
            $errors[] = 'Published date must be a valid YYYY-MM-DD value.';
        }

        if ($post['image_url'] === '' && !$hasUpload) {
            $errors[] = 'Provide an image URL or upload a new hero image.';
        }

        if ($post['snippet'] === '') {
            $errors[] = 'Snippet is required.';
        }

        if ($post['content_html'] === '') {
            $errors[] = 'Content is required.';
        }

        return $errors;
    }

    private function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM blog_posts WHERE slug = :slug';
        $params = ['slug' => $slug];

        if ($ignoreId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $ignoreId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn() > 0;
    }

    private function validateDate(string $value): bool
    {
        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $value);
        return $date !== false && $date->format('Y-m-d') === $value;
    }

    private function hasFileUpload(mixed $file): bool
    {
        return is_array($file) && ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    }

    private function storeUploadedFile(array $file, string $nameHint): string
    {
        $stored = Uploads::store($file, $nameHint);
        return Media::normalizeMediaPath($stored['path']);
    }

    private function assertValidCsrf(?string $token, ?string $redirect = null): void
    {
        if (Csrf::verify($token)) {
            return;
        }

        Flash::set('admin.posts.error', 'Session expired, please try again.');
        $this->redirect($redirect ?? '/admin/posts');
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

        return Media::normalizeMediaPath($value);
    }

    private function defaultPost(): array
    {
        $today = (new \DateTimeImmutable('now'))->format('Y-m-d');

        return [
            'title' => '',
            'slug' => '',
            'published_at' => $today,
            'image_url' => '',
            'snippet' => '',
            'content_html' => '',
            'is_published' => true,
        ];
    }
}
