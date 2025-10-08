<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Product;
use App\Services\Security\Csrf;
use App\Support\Flash;
use PDO;

final class ProductsController extends Controller
{
    private Product $products;
    private PDO $db;

    public function __construct()
    {
        $this->products = new Product();
        $this->db = Database::connection();
    }

    public function index(): void
    {
        $products = $this->products->all();
        $notice = Flash::pull('admin.products.notice');
        $error = Flash::pull('admin.products.error');
        $csrfToken = Csrf::token();

        $this->view('admin/products/index', [
            'title' => 'Products',
            'products' => $products,
            'notice' => $notice,
            'error' => $error,
            'csrfToken' => $csrfToken,
        ]);
    }

    public function create(): void
    {
        $this->renderForm(
            $this->defaultProduct(),
            [],
            [],
            'Create Product',
            '/admin/products/store',
            'create'
        );
    }

    public function store(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, '/admin/products/create');

        $input = $this->extractProductInput($_POST);
        $features = $input['features'];
        unset($input['features']);

        $errors = $this->validate($input, $features);
        if ($errors) {
            $this->renderForm($input, $features, $errors, 'Create Product', '/admin/products/store', 'create');
            return;
        }

        $productId = $this->products->create($input);
        $this->syncFeatures($productId, $features);

        Flash::set('admin.products.notice', 'Product created successfully.');
        $this->redirect('/admin/products');
    }

    public function edit(string $id): void
    {
        $product = $this->products->find($id);
        if (!$product) {
            Flash::set('admin.products.error', 'Product not found.');
            $this->redirect('/admin/products');
        }

        $features = $this->getFeatures((int)$id);

        $this->renderForm(
            $product,
            $features,
            [],
            'Edit Product',
            "/admin/products/update/{$id}",
            'edit'
        );
    }

    public function update(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, "/admin/products/edit/{$id}");

        $product = $this->products->find($id);
        if (!$product) {
            Flash::set('admin.products.error', 'Product not found.');
            $this->redirect('/admin/products');
        }

        $input = $this->extractProductInput($_POST);
        $features = $input['features'];
        unset($input['features']);

        $errors = $this->validate($input, $features, (int)$id);
        if ($errors) {
            $this->renderForm($input + ['id' => $id], $features, $errors, 'Edit Product', "/admin/products/update/{$id}", 'edit');
            return;
        }

        $this->products->update($id, $input);
        $this->syncFeatures((int)$id, $features);

        Flash::set('admin.products.notice', 'Product updated successfully.');
        $this->redirect('/admin/products');
    }

    public function destroy(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        $product = $this->products->find($id);
        if (!$product) {
            Flash::set('admin.products.error', 'Product not found.');
            $this->redirect('/admin/products');
        }

        $this->syncFeatures((int)$id, []);
        $this->products->delete($id);

        Flash::set('admin.products.notice', 'Product deleted.');
        $this->redirect('/admin/products');
    }

    private function renderForm(
        array $product,
        array $features,
        array $errors,
        string $title,
        string $action,
        string $mode
    ): void {
        $csrfToken = Csrf::token();
        $featureText = implode("\n", $features);

        $this->view('admin/products/form', [
            'title' => $title,
            'product' => $product,
            'featureText' => $featureText,
            'features' => $features,
            'errors' => $errors,
            'formAction' => $action,
            'submitLabel' => $mode === 'edit' ? 'Save Changes' : 'Create Product',
            'mode' => $mode,
            'csrfToken' => $csrfToken,
        ]);
    }

    private function extractProductInput(array $source): array
    {
        $lines = preg_split('/\r\n|\r|\n/', (string)($source['features'] ?? '')) ?: [];
        $features = array_values(array_filter(array_map('trim', $lines), static fn (string $line) => $line !== ''));

        return [
            'name' => trim((string)($source['name'] ?? '')),
            'slug' => trim((string)($source['slug'] ?? '')),
            'description' => trim((string)($source['description'] ?? '')),
            'icon_key' => trim((string)($source['icon_key'] ?? '')),
            'external_link' => trim((string)($source['external_link'] ?? '')),
            'hero_title' => trim((string)($source['hero_title'] ?? '')),
            'hero_subtitle' => trim((string)($source['hero_subtitle'] ?? '')),
            'cta_text' => trim((string)($source['cta_text'] ?? '')),
            'cta_link' => trim((string)($source['cta_link'] ?? '')),
            'featured_order' => max(0, (int)($source['featured_order'] ?? 0)),
            'content_html' => (string)($source['content_html'] ?? ''),
            'features' => $features,
        ];
    }

    private function validate(array $product, array $features, ?int $ignoreId = null): array
    {
        $errors = [];

        if ($product['name'] === '') {
            $errors[] = 'Name is required.';
        }

        if ($product['slug'] === '') {
            $errors[] = 'Slug is required.';
        } elseif (!preg_match('/^[a-z0-9\\-]+$/', $product['slug'])) {
            $errors[] = 'Slug may only contain lowercase letters, numbers, and hyphens.';
        } elseif ($this->slugExists($product['slug'], $ignoreId)) {
            $errors[] = 'Slug is already in use.';
        }

        if ($product['description'] === '') {
            $errors[] = 'Description is required.';
        }

        if ($product['icon_key'] === '') {
            $errors[] = 'Icon key is required.';
        }

        if (empty($features)) {
            $errors[] = 'Add at least one feature.';
        }

        return $errors;
    }

    private function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM products WHERE slug = :slug';
        $params = ['slug' => $slug];

        if ($ignoreId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $ignoreId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn() > 0;
    }

    private function syncFeatures(int $productId, array $features): void
    {
        $this->db->beginTransaction();

        try {
            $delete = $this->db->prepare('DELETE FROM product_features WHERE product_id = :id');
            $delete->execute(['id' => $productId]);

            if ($features) {
                $insert = $this->db->prepare(
                    'INSERT INTO product_features (product_id, feature_text, sort_order)
                     VALUES (:product_id, :feature_text, :sort_order)'
                );

                foreach ($features as $index => $feature) {
                    $insert->execute([
                        'product_id' => $productId,
                        'feature_text' => $feature,
                        'sort_order' => $index,
                    ]);
                }
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function getFeatures(int $productId): array
    {
        $stmt = $this->db->prepare(
            'SELECT feature_text FROM product_features WHERE product_id = :id ORDER BY sort_order ASC'
        );
        $stmt->execute(['id' => $productId]);
        $rows = $stmt->fetchAll() ?: [];

        return array_map(static fn (array $row): string => $row['feature_text'], $rows);
    }

    private function assertValidCsrf(?string $token, ?string $fallback = null): void
    {
        if (Csrf::verify($token)) {
            return;
        }

        Flash::set('admin.products.error', 'Session expired, please try again.');
        $this->redirect($fallback ?? '/admin/products');
    }

    private function defaultProduct(): array
    {
        return [
            'name' => '',
            'slug' => '',
            'description' => '',
            'icon_key' => '',
            'external_link' => '',
            'hero_title' => '',
            'hero_subtitle' => '',
            'cta_text' => '',
            'cta_link' => '',
            'featured_order' => 0,
            'content_html' => '',
        ];
    }
}
