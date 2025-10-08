<?php
declare(strict_types=1);

namespace App\Services\Cms;

use App\Core\Database;
use PDO;

final class ContentRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function getSettings(): array
    {
        $stmt = $this->db->query('SELECT setting_key, setting_value FROM settings');
        return $stmt->fetchAll(PDO::FETCH_KEY_PAIR) ?: [];
    }

    public function getProducts(): array
    {
        $stmt = $this->db->query('SELECT * FROM products ORDER BY featured_order ASC, name ASC');
        $products = $stmt->fetchAll() ?: [];

        $features = $this->getProductFeatures();
        foreach ($products as &$product) {
            $product['features'] = $features[$product['id']] ?? [];
        }

        return $products;
    }

    public function getProductBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $product = $stmt->fetch();

        if (!$product) {
            return null;
        }

        $features = $this->getProductFeatures();
        $product['features'] = $features[$product['id']] ?? [];

        return $product;
    }

    private function getProductFeatures(): array
    {
        $stmt = $this->db->query('SELECT product_id, feature_text FROM product_features ORDER BY sort_order ASC');
        $rows = $stmt->fetchAll() ?: [];

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[(int)$row['product_id']][] = $row['feature_text'];
        }

        return $grouped;
    }

    public function getAgents(): array
    {
        $stmt = $this->db->query('SELECT * FROM agents ORDER BY featured_order ASC, name ASC');
        return $stmt->fetchAll() ?: [];
    }

    public function getTeamMembers(): array
    {
        $stmt = $this->db->query('SELECT * FROM team_members ORDER BY sort_order ASC, name ASC');
        return $stmt->fetchAll() ?: [];
    }

    public function getPartners(): array
    {
        $stmt = $this->db->query('SELECT * FROM partners ORDER BY featured_order ASC, name ASC');
        return $stmt->fetchAll() ?: [];
    }

    public function getRoadmapPhases(): array
    {
        $stmt = $this->db->query('SELECT * FROM roadmap_phases ORDER BY sort_order ASC');
        $phases = $stmt->fetchAll() ?: [];

        $itemStmt = $this->db->prepare('SELECT title, description FROM roadmap_items WHERE roadmap_phase_id = :id ORDER BY sort_order ASC');

        foreach ($phases as &$phase) {
            $itemStmt->execute(['id' => $phase['id']]);
            $phase['items'] = $itemStmt->fetchAll() ?: [];
        }

        return $phases;
    }

    public function getAlwaysOnTracks(): array
    {
        $stmt = $this->db->query('SELECT title FROM always_on_tracks ORDER BY sort_order ASC');
        return array_column($stmt->fetchAll() ?: [], 'title');
    }

    public function getFaqItems(): array
    {
        $stmt = $this->db->query('SELECT * FROM faq_items ORDER BY sort_order ASC');
        return $stmt->fetchAll() ?: [];
    }

    public function getBlogPosts(): array
    {
        $stmt = $this->db->query('SELECT * FROM blog_posts WHERE is_published = 1 ORDER BY published_at DESC');
        return $stmt->fetchAll() ?: [];
    }

    public function getBlogPostBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM blog_posts WHERE slug = :slug LIMIT 1');
        $stmt->execute(['slug' => $slug]);
        $post = $stmt->fetch();

        return $post ?: null;
    }

    public function getCaseStudies(): array
    {
        $stmt = $this->db->query('SELECT * FROM case_studies ORDER BY sort_order ASC');
        return $stmt->fetchAll() ?: [];
    }

    public function getCommands(): array
    {
        $stmt = $this->db->query('SELECT * FROM commands ORDER BY sort_order ASC');
        return $stmt->fetchAll() ?: [];
    }

    public function getPressAssets(): array
    {
        $stmt = $this->db->query('SELECT * FROM press_assets ORDER BY sort_order ASC, asset_type ASC');
        return $stmt->fetchAll() ?: [];
    }

    public function getSocialProofItems(): array
    {
        $stmt = $this->db->query('SELECT * FROM social_proof_items ORDER BY sort_order ASC');
        return $stmt->fetchAll() ?: [];
    }
}
