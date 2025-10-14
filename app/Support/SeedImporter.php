<?php
declare(strict_types=1);

namespace App\Support;

use App\Core\Database;
use PDO;

final class SeedImporter
{
    private static bool $checked = false;

    public static function ensureSeeded(): void
    {
        if (self::$checked) {
            return;
        }
        self::$checked = true;

        $seedPath = BASE_PATH . '/database/seed-data.php';
        if (!is_file($seedPath)) {
            return;
        }

        $seed = require $seedPath;
        if (!is_array($seed)) {
            return;
        }

        $pdo = Database::connection();

        self::seedAdmins($pdo, $seed['admins'] ?? [], (array)(config('wallet.allowed_addresses', []) ?? []));
        self::seedSettings($pdo, $seed['settings'] ?? []);
        self::seedProducts($pdo, $seed['products'] ?? [], $seed['product_features'] ?? []);
        self::seedAgents($pdo, $seed['agents'] ?? []);
        self::seedPartners($pdo, $seed['partners'] ?? []);
        self::seedTeam($pdo, $seed['team_members'] ?? []);
        self::seedRoadmap($pdo, $seed['roadmap_phases'] ?? [], $seed['always_on_tracks'] ?? []);
        self::seedCommands($pdo, $seed['commands'] ?? []);
        self::seedCaseStudies($pdo, $seed['case_studies'] ?? []);
        self::seedPressAssets($pdo, $seed['press_assets'] ?? []);
        self::seedSocialProof($pdo, $seed['social_proof_items'] ?? []);
        self::seedFaq($pdo, $seed['faq_items'] ?? []);
        self::seedBlogPosts($pdo, $seed['blog_posts'] ?? []);
    }

    private static function seedAdmins(PDO $pdo, array $admins, array $allowedAddresses): void
    {
        $all = [];

        foreach ($admins as $admin) {
            if (!isset($admin['wallet_address'])) {
                continue;
            }
            $all[] = [
                'display_name' => $admin['display_name'] ?? 'Admin',
                'wallet_address' => strtolower(trim((string)$admin['wallet_address'])),
                'email' => $admin['email'] ?? null,
            ];
        }

        foreach ($allowedAddresses as $address) {
            $address = strtolower(trim((string)$address));
            if ($address === '') {
                continue;
            }
            $all[] = [
                'display_name' => 'Admin',
                'wallet_address' => $address,
                'email' => null,
            ];
        }

        if (!$all) {
            return;
        }

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS admins (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                display_name VARCHAR(100) NOT NULL,
                wallet_address CHAR(42) NOT NULL UNIQUE,
                email VARCHAR(150) NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $stmt = $pdo->prepare(
            'INSERT INTO admins (display_name, wallet_address, email)
             VALUES (:display_name, :wallet_address, :email)
             ON DUPLICATE KEY UPDATE
                display_name = VALUES(display_name),
                email = VALUES(email)'
        );

        foreach ($all as $admin) {
            if ($admin['wallet_address'] === '') {
                continue;
            }
            $stmt->execute([
                'display_name' => $admin['display_name'] ?: 'Admin',
                'wallet_address' => $admin['wallet_address'],
                'email' => $admin['email'],
            ]);
        }
    }

    private static function seedSettings(PDO $pdo, array $settings): void
    {
        if (!$settings) {
            return;
        }

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS settings (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                setting_key VARCHAR(100) NOT NULL UNIQUE,
                setting_value TEXT NOT NULL,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        $select = $pdo->prepare('SELECT setting_value FROM settings WHERE setting_key = :key LIMIT 1');
        $insert = $pdo->prepare('INSERT INTO settings (setting_key, setting_value) VALUES (:key, :value)');
        $update = $pdo->prepare('UPDATE settings SET setting_value = :value WHERE setting_key = :key');

        foreach ($settings as $setting) {
            if (!isset($setting['setting_key'], $setting['setting_value'])) {
                continue;
            }
            $key = (string)$setting['setting_key'];
            $value = (string)$setting['setting_value'];

            $select->execute(['key' => $key]);
            $existing = $select->fetchColumn();

            if ($existing === false) {
                $insert->execute([
                    'key' => $key,
                    'value' => $value,
                ]);
                continue;
            }

            if ($existing === null || $existing === '') {
                $update->execute([
                    'key' => $key,
                    'value' => $value,
                ]);
            }
        }
    }

    private static function seedProducts(PDO $pdo, array $products, array $featuresBySlug): void
    {
        if (!$products || self::tableHasRows($pdo, 'products')) {
            return;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO products (name, slug, description, icon_key, external_link, hero_title, hero_subtitle, cta_text, cta_link, content_html, featured_order)
             VALUES (:name, :slug, :description, :icon_key, :external_link, :hero_title, :hero_subtitle, :cta_text, :cta_link, :content_html, :order)'
        );

        $order = 0;
        foreach ($products as $product) {
            $slug = self::slugify($product['slug'] ?? $product['name'] ?? '');
            $stmt->execute([
                'name' => $product['name'] ?? '',
                'slug' => $slug,
                'description' => $product['description'] ?? '',
                'icon_key' => $product['icon_key'] ?? 'chip',
                'external_link' => $product['external_link'] ?? null,
                'hero_title' => $product['hero_title'] ?? null,
                'hero_subtitle' => $product['hero_subtitle'] ?? null,
                'cta_text' => $product['cta_text'] ?? null,
                'cta_link' => $product['cta_link'] ?? null,
                'content_html' => $product['content_html'] ?? null,
                'order' => $order++,
            ]);
        }

        $pdo->exec(
            "CREATE TABLE IF NOT EXISTS product_features (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                product_id INT UNSIGNED NOT NULL,
                feature_text VARCHAR(255) NOT NULL,
                sort_order INT UNSIGNED DEFAULT 0,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        );

        if (!$featuresBySlug) {
            return;
        }

        $select = $pdo->prepare('SELECT id FROM products WHERE slug = :slug LIMIT 1');
        $insertFeature = $pdo->prepare(
            'INSERT INTO product_features (product_id, feature_text, sort_order) VALUES (:product_id, :feature_text, :sort_order)'
        );

        foreach ($featuresBySlug as $slug => $features) {
            $select->execute(['slug' => $slug]);
            $productId = (int)$select->fetchColumn();
            if (!$productId) {
                continue;
            }

            $sort = 0;
            foreach ($features as $feature) {
                $insertFeature->execute([
                    'product_id' => $productId,
                    'feature_text' => $feature,
                    'sort_order' => $sort++,
                ]);
            }
        }
    }

    private static function seedAgents(PDO $pdo, array $agents): void
    {
        if (!$agents || self::tableHasRows($pdo, 'agents')) {
            return;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO agents (name, chain, status, summary, site_url, image_url, badge, featured_order)
             VALUES (:name, :chain, :status, :summary, :site_url, :image_url, :badge, :order)'
        );

        $order = 0;
        foreach ($agents as $agent) {
            $stmt->execute([
                'name' => $agent['name'] ?? '',
                'chain' => $agent['chain'] ?? '',
                'status' => $agent['status'] ?? 'Live',
                'summary' => $agent['summary'] ?? '',
                'site_url' => $agent['site_url'] ?? '#',
                'image_url' => $agent['image_url'] ?? '',
                'badge' => $agent['badge'] ?? null,
                'order' => $order++,
            ]);
        }
    }

    private static function seedPartners(PDO $pdo, array $partners): void
    {
        if (!$partners || self::tableHasRows($pdo, 'partners')) {
            return;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO partners (name, logo_url, badge_logo_url, url, summary, status, featured_order)
             VALUES (:name, :logo_url, :badge_logo_url, :url, :summary, :status, :order)'
        );

        $order = 0;
        foreach ($partners as $partner) {
            $stmt->execute([
                'name' => $partner['name'] ?? '',
                'logo_url' => $partner['logo_url'] ?? '',
                'badge_logo_url' => $partner['badge_logo_url'] ?? ($partner['logo_url'] ?? ''),
                'url' => $partner['url'] ?? '#',
                'summary' => $partner['summary'] ?? '',
                'status' => $partner['status'] ?? 'Active',
                'order' => $order++,
            ]);
        }
    }

    private static function seedTeam(PDO $pdo, array $team): void
    {
        if (!$team || self::tableHasRows($pdo, 'team_members')) {
            return;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO team_members (name, role, bio, avatar_url, telegram_url, x_url, sort_order)
             VALUES (:name, :role, :bio, :avatar_url, :telegram_url, :x_url, :order)'
        );

        $order = 0;
        foreach ($team as $member) {
            $stmt->execute([
                'name' => $member['name'] ?? '',
                'role' => $member['role'] ?? '',
                'bio' => $member['bio'] ?? '',
                'avatar_url' => $member['avatar_url'] ?? '',
                'telegram_url' => $member['telegram_url'] ?? null,
                'x_url' => $member['x_url'] ?? null,
                'order' => $order++,
            ]);
        }
    }

    private static function seedRoadmap(PDO $pdo, array $phases, array $tracks): void
    {
        if ($phases && !self::tableHasRows($pdo, 'roadmap_phases')) {
            $phaseStmt = $pdo->prepare(
                'INSERT INTO roadmap_phases (phase_label, phase_key, timeline, goal, sort_order)
                 VALUES (:label, :key, :timeline, :goal, :order)'
            );
            $itemStmt = $pdo->prepare(
                'INSERT INTO roadmap_items (roadmap_phase_id, title, description, sort_order)
                 VALUES (:phase_id, :title, :description, :order)'
            );
            $select = $pdo->prepare('SELECT id FROM roadmap_phases WHERE phase_key = :key LIMIT 1');

            $order = 0;
            foreach ($phases as $phase) {
                $phaseKey = self::slugify($phase['phase_key'] ?? $phase['phase_label'] ?? '');
                $phaseStmt->execute([
                    'label' => $phase['phase_label'] ?? '',
                    'key' => $phaseKey,
                    'timeline' => $phase['timeline'] ?? '',
                    'goal' => $phase['goal'] ?? '',
                    'order' => $order++,
                ]);

                $select->execute(['key' => $phaseKey]);
                $phaseId = (int)$select->fetchColumn();
                if (!$phaseId || empty($phase['items']) || !is_array($phase['items'])) {
                    continue;
                }

                $itemOrder = 0;
                foreach ($phase['items'] as $item) {
                    $itemStmt->execute([
                        'phase_id' => $phaseId,
                        'title' => $item['title'] ?? '',
                        'description' => $item['description'] ?? '',
                        'order' => $itemOrder++,
                    ]);
                }
            }
        }

        if ($tracks && !self::tableHasRows($pdo, 'always_on_tracks')) {
            $stmt = $pdo->prepare(
                'INSERT INTO always_on_tracks (title, sort_order) VALUES (:title, :order)'
            );
            $order = 0;
            foreach ($tracks as $track) {
                $stmt->execute([
                    'title' => $track,
                    'order' => $order++,
                ]);
            }
        }
    }

    private static function seedCommands(PDO $pdo, array $commands): void
    {
        if (!$commands || self::tableHasRows($pdo, 'commands')) {
            return;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO commands (command, description, sort_order) VALUES (:command, :description, :order)'
        );
        $order = 0;
        foreach ($commands as $command) {
            $stmt->execute([
                'command' => $command['command'] ?? '',
                'description' => $command['description'] ?? '',
                'order' => $order++,
            ]);
        }
    }

    private static function seedCaseStudies(PDO $pdo, array $studies): void
    {
        if (!$studies || self::tableHasRows($pdo, 'case_studies')) {
            return;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO case_studies (client, chain, title, summary, image_url, sort_order)
             VALUES (:client, :chain, :title, :summary, :image_url, :order)'
        );
        $order = 0;
        foreach ($studies as $study) {
            $stmt->execute([
                'client' => $study['client'] ?? '',
                'chain' => $study['chain'] ?? '',
                'title' => $study['title'] ?? '',
                'summary' => $study['summary'] ?? '',
                'image_url' => $study['image_url'] ?? '',
                'order' => $order++,
            ]);
        }
    }

    private static function seedPressAssets(PDO $pdo, array $assets): void
    {
        if (!$assets || self::tableHasRows($pdo, 'press_assets')) {
            return;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO press_assets (asset_type, label, file_path, sort_order)
             VALUES (:type, :label, :path, :order)'
        );
        $order = 0;
        foreach ($assets as $asset) {
            $stmt->execute([
                'type' => $asset['asset_type'] ?? 'Logo',
                'label' => $asset['label'] ?? '',
                'path' => $asset['file_path'] ?? '#',
                'order' => $order++,
            ]);
        }
    }

    private static function seedSocialProof(PDO $pdo, array $items): void
    {
        if (!$items || self::tableHasRows($pdo, 'social_proof_items')) {
            return;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO social_proof_items (content_type, author_name, author_handle, author_avatar_url, content, link, sort_order)
             VALUES (:type, :name, :handle, :avatar, :content, :link, :order)'
        );
        $order = 0;
        foreach ($items as $item) {
            $stmt->execute([
                'type' => $item['content_type'] ?? 'Tweet',
                'name' => $item['author_name'] ?? '',
                'handle' => $item['author_handle'] ?? '',
                'avatar' => $item['author_avatar_url'] ?? '',
                'content' => $item['content'] ?? '',
                'link' => $item['link'] ?? '#',
                'order' => $order++,
            ]);
        }
    }

    private static function seedFaq(PDO $pdo, array $faqs): void
    {
        if (!$faqs || self::tableHasRows($pdo, 'faq_items')) {
            return;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO faq_items (question, answer, sort_order) VALUES (:question, :answer, :order)'
        );
        $order = 0;
        foreach ($faqs as $faq) {
            $stmt->execute([
                'question' => $faq['question'] ?? '',
                'answer' => $faq['answer'] ?? '',
                'order' => $order++,
            ]);
        }
    }

    private static function seedBlogPosts(PDO $pdo, array $posts): void
    {
        if (!$posts || self::tableHasRows($pdo, 'blog_posts')) {
            return;
        }

        $stmt = $pdo->prepare(
            'INSERT INTO blog_posts (slug, title, published_at, image_url, snippet, content_html, is_published)
             VALUES (:slug, :title, :published_at, :image_url, :snippet, :content_html, :published)'
        );

        foreach ($posts as $post) {
            $slug = self::slugify($post['slug'] ?? $post['title'] ?? '');
            $stmt->execute([
                'slug' => $slug,
                'title' => $post['title'] ?? '',
                'published_at' => $post['published_at'] ?? date('Y-m-d'),
                'image_url' => $post['image_url'] ?? '',
                'snippet' => $post['snippet'] ?? '',
                'content_html' => $post['content_html'] ?? '',
                'published' => isset($post['is_published']) ? (int)$post['is_published'] : 1,
            ]);
        }
    }

    private static function tableHasRows(PDO $pdo, string $table): bool
    {
        try {
            $count = (int)$pdo->query("SELECT COUNT(*) FROM {$table}")->fetchColumn();
            return $count > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private static function slugify(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/i', '-', $value) ?? '';
        $value = trim($value, '-');
        return $value !== '' ? $value : bin2hex(random_bytes(4));
    }
}
