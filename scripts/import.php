<?php
declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Container;
use App\Core\Database;

$pdo = Database::connection();
$config = Container::get('config', []);

$pdo->exec('SET NAMES utf8mb4');

resetTables($pdo, [
    'product_features',
    'products',
    'agents',
    'team_members',
    'partners',
    'roadmap_items',
    'roadmap_phases',
    'always_on_tracks',
    'commands',
    'case_studies',
    'press_assets',
    'social_proof_items',
    'faq_items',
    'blog_posts',
    'admin_sessions',
    'admin_nonces',
]);

importSettings($pdo);
importAdminWallet($pdo, $config);
$seedFile = dirname(__DIR__) . '/database/seed-data.php';

if (is_file($seedFile)) {
    $seed = require $seedFile;
    if (is_array($seed)) {
        try {
            importProducts($pdo, $seed['products'] ?? [], $seed['product_features'] ?? []);
            importAgents($pdo, $seed['agents'] ?? []);
            importTeam($pdo, $seed['team_members'] ?? []);
            importPartners($pdo, $seed['partners'] ?? []);
            importRoadmap($pdo, $seed['roadmap_phases'] ?? []);
            importTracks($pdo, $seed['always_on_tracks'] ?? []);
            importCommands($pdo, $seed['commands'] ?? []);
            importCaseStudies($pdo, $seed['case_studies'] ?? []);
            importPressAssets($pdo, $seed['press_assets'] ?? []);
            importSocialProof($pdo, $seed['social_proof_items'] ?? []);
            importFaq($pdo, $seed['faq_items'] ?? []);
            importBlogPosts($pdo, $seed['blog_posts'] ?? []);
            echo "Seed data imported successfully.\n";
        } catch (Throwable $e) {
            fwrite(STDERR, "Seed import failed: {$e->getMessage()}\n");
            exit(1);
        }
    } else {
        fwrite(STDERR, "Seed file did not return an array.\n");
    }
} else {
    echo "Seed data file not found, skipped.\n";
}

echo "Import completed.\n";

function importSettings(\PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS settings (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) NOT NULL UNIQUE,
            setting_value TEXT NOT NULL,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
}

function importAdminWallet(\PDO $pdo, array $config): void
{
    $allowed = $config['wallet']['allowed_addresses'] ?? [];
    if (!is_array($allowed) || !$allowed) {
        echo "No admin wallet configured; skipping admin seed.\n";
        return;
    }

    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS admins (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            display_name VARCHAR(100) DEFAULT 'Admin',
            wallet_address CHAR(42) NOT NULL UNIQUE,
            email VARCHAR(150) NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );

    $stmt = $pdo->prepare('INSERT IGNORE INTO admins (wallet_address) VALUES (:address)');
    foreach ($allowed as $address) {
        $stmt->execute(['address' => strtolower((string)$address)]);
        echo "Admin wallet seeded: {$address}\n";
    }
}

function importProducts(\PDO $pdo, array $products, array $featureMap): void
{
    if (!$products) {
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO products (name, slug, description, icon_key, external_link, hero_title, hero_subtitle, cta_text, cta_link, content_html, featured_order)
         VALUES (:name, :slug, :description, :icon_key, :external_link, :hero_title, :hero_subtitle, :cta_text, :cta_link, :content_html, :order)
         ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            description = VALUES(description),
            icon_key = VALUES(icon_key),
            external_link = VALUES(external_link),
            hero_title = VALUES(hero_title),
            hero_subtitle = VALUES(hero_subtitle),
            cta_text = VALUES(cta_text),
            cta_link = VALUES(cta_link),
            content_html = VALUES(content_html),
            featured_order = VALUES(featured_order)'
    );

    $select = $pdo->prepare('SELECT id FROM products WHERE slug = :slug LIMIT 1');
    $order = 1;
    $ids = [];

    foreach ($products as $product) {
        $slug = $product['slug'] ?? slugify($product['name'] ?? '');
        if ($slug === '') {
            $slug = bin2hex(random_bytes(4));
        }

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

        $select->execute(['slug' => $slug]);
        $ids[$slug] = (int)$select->fetchColumn();
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

    $delete = $pdo->prepare('DELETE FROM product_features WHERE product_id = :product_id');
    $insert = $pdo->prepare(
        'INSERT INTO product_features (product_id, feature_text, sort_order) VALUES (:product_id, :feature_text, :sort_order)'
    );

    foreach ($featureMap as $slug => $features) {
        $productId = $ids[$slug] ?? null;
        if (!$productId) {
            continue;
        }

        $delete->execute(['product_id' => $productId]);
        $sort = 1;
        foreach ($features as $feature) {
            $insert->execute([
                'product_id' => $productId,
                'feature_text' => $feature,
                'sort_order' => $sort++,
            ]);
        }
    }
}

function importAgents(\PDO $pdo, array $agents): void
{
    if (!$agents) {
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO agents (name, chain, status, summary, site_url, image_url, badge, featured_order)
         VALUES (:name, :chain, :status, :summary, :site_url, :image_url, :badge, :order)
         ON DUPLICATE KEY UPDATE
            chain = VALUES(chain),
            status = VALUES(status),
            summary = VALUES(summary),
            site_url = VALUES(site_url),
            image_url = VALUES(image_url),
            badge = VALUES(badge),
            featured_order = VALUES(featured_order)'
    );

    $order = 1;
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

function importTeam(\PDO $pdo, array $team): void
{
    if (!$team) {
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO team_members (name, role, bio, avatar_url, telegram_url, x_url, sort_order)
         VALUES (:name, :role, :bio, :avatar_url, :telegram_url, :x_url, :order)
         ON DUPLICATE KEY UPDATE
            role = VALUES(role),
            bio = VALUES(bio),
            avatar_url = VALUES(avatar_url),
            telegram_url = VALUES(telegram_url),
            x_url = VALUES(x_url),
            sort_order = VALUES(sort_order)'
    );

    $order = 1;
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

function importPartners(\PDO $pdo, array $partners): void
{
    if (!$partners) {
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO partners (name, logo_url, url, summary, status, featured_order)
         VALUES (:name, :logo_url, :url, :summary, :status, :order)
         ON DUPLICATE KEY UPDATE
            logo_url = VALUES(logo_url),
            url = VALUES(url),
            summary = VALUES(summary),
            status = VALUES(status),
            featured_order = VALUES(featured_order)'
    );

    $order = 1;
    foreach ($partners as $partner) {
        $stmt->execute([
            'name' => $partner['name'] ?? '',
            'logo_url' => $partner['logo_url'] ?? '',
            'url' => $partner['url'] ?? '#',
            'summary' => $partner['summary'] ?? '',
            'status' => $partner['status'] ?? 'Active',
            'order' => $order++,
        ]);
    }
}

function importRoadmap(\PDO $pdo, array $phases): void
{
    if (!$phases) {
        return;
    }

    $phaseStmt = $pdo->prepare(
        'INSERT INTO roadmap_phases (phase_label, phase_key, timeline, goal, sort_order)
         VALUES (:label, :key, :timeline, :goal, :order)
         ON DUPLICATE KEY UPDATE
            phase_label = VALUES(phase_label),
            timeline = VALUES(timeline),
            goal = VALUES(goal),
            sort_order = VALUES(sort_order)'
    );

    $phaseSelect = $pdo->prepare('SELECT id FROM roadmap_phases WHERE phase_key = :key LIMIT 1');
    $itemInsert = $pdo->prepare(
        'INSERT INTO roadmap_items (roadmap_phase_id, title, description, sort_order)
         VALUES (:phase_id, :title, :description, :order)'
    );

    $deleteItems = $pdo->prepare('DELETE FROM roadmap_items WHERE roadmap_phase_id = :phase_id');

    $order = 1;
    foreach ($phases as $phase) {
        $phaseLabel = $phase['phase_label'] ?? $phase['phase'] ?? '';
        $phaseKey = $phase['phase_key'] ?? slugify($phaseLabel);
        if ($phaseKey === '') {
            $phaseKey = bin2hex(random_bytes(4));
        }

        $phaseStmt->execute([
            'label' => $phaseLabel,
            'key' => $phaseKey,
            'timeline' => $phase['timeline'] ?? '',
            'goal' => $phase['goal'] ?? '',
            'order' => $order++,
        ]);

        $phaseSelect->execute(['key' => $phaseKey]);
        $phaseId = (int)$phaseSelect->fetchColumn();
        if (!$phaseId) {
            continue;
        }

        $deleteItems->execute(['phase_id' => $phaseId]);
        $itemOrder = 1;
        foreach ($phase['items'] ?? [] as $item) {
            $itemInsert->execute([
                'phase_id' => $phaseId,
                'title' => $item['title'] ?? '',
                'description' => $item['description'] ?? '',
                'order' => $itemOrder++,
            ]);
        }
    }
}

function importTracks(\PDO $pdo, array $tracks): void
{
    $pdo->exec('DELETE FROM always_on_tracks');
    if (!$tracks) {
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO always_on_tracks (title, sort_order) VALUES (:title, :order)'
    );

    $order = 1;
    foreach ($tracks as $track) {
        $stmt->execute([
            'title' => $track,
            'order' => $order++,
        ]);
    }
}

function importCommands(\PDO $pdo, array $commands): void
{
    $pdo->exec('DELETE FROM commands');
    if (!$commands) {
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO commands (command, description, sort_order)
         VALUES (:command, :description, :order)'
    );

    $order = 1;
    foreach ($commands as $command) {
        $stmt->execute([
            'command' => $command['command'] ?? '',
            'description' => $command['description'] ?? '',
            'order' => $order++,
        ]);
    }
}

function importCaseStudies(\PDO $pdo, array $items): void
{
    $pdo->exec('DELETE FROM case_studies');
    if (!$items) {
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO case_studies (client, chain, title, summary, image_url, sort_order)
         VALUES (:client, :chain, :title, :summary, :image_url, :order)'
    );

    $order = 1;
    foreach ($items as $item) {
        $stmt->execute([
            'client' => $item['client'] ?? '',
            'chain' => $item['chain'] ?? '',
            'title' => $item['title'] ?? '',
            'summary' => $item['summary'] ?? '',
            'image_url' => $item['image_url'] ?? '',
            'order' => $order++,
        ]);
    }
}

function importPressAssets(\PDO $pdo, array $items): void
{
    $pdo->exec('DELETE FROM press_assets');
    if (!$items) {
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO press_assets (asset_type, label, file_path, sort_order)
         VALUES (:type, :label, :file, :order)'
    );

    $order = 1;
    foreach ($items as $item) {
        $stmt->execute([
            'type' => $item['asset_type'] ?? 'Logo',
            'label' => $item['label'] ?? '',
            'file' => $item['file_path'] ?? '',
            'order' => $order++,
        ]);
    }
}

function importSocialProof(\PDO $pdo, array $items): void
{
    $pdo->exec('DELETE FROM social_proof_items');
    if (!$items) {
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO social_proof_items (content_type, author_name, author_handle, author_avatar_url, content, link, sort_order)
         VALUES (:type, :name, :handle, :avatar, :content, :link, :order)'
    );

    $order = 1;
    foreach ($items as $item) {
        $stmt->execute([
            'type' => $item['content_type'] ?? 'Tweet',
            'name' => $item['author_name'] ?? '',
            'handle' => $item['author_handle'] ?? '',
            'avatar' => $item['author_avatar_url'] ?? '',
            'content' => $item['content'] ?? '',
            'link' => $item['link'] ?? '',
            'order' => $order++,
        ]);
    }
}

function importFaq(\PDO $pdo, array $items): void
{
    $pdo->exec('DELETE FROM faq_items');
    if (!$items) {
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO faq_items (question, answer, sort_order)
         VALUES (:question, :answer, :order)'
    );

    $order = 1;
    foreach ($items as $item) {
        $stmt->execute([
            'question' => $item['question'] ?? '',
            'answer' => $item['answer'] ?? '',
            'order' => $order++,
        ]);
    }
}

function importBlogPosts(\PDO $pdo, array $posts): void
{
    if (!$posts) {
        return;
    }

    $stmt = $pdo->prepare(
        'INSERT INTO blog_posts (slug, title, published_at, image_url, snippet, content_html, is_published)
         VALUES (:slug, :title, :published_at, :image_url, :snippet, :content_html, :is_published)
         ON DUPLICATE KEY UPDATE
            title = VALUES(title),
            published_at = VALUES(published_at),
            image_url = VALUES(image_url),
            snippet = VALUES(snippet),
            content_html = VALUES(content_html),
            is_published = VALUES(is_published)'
    );

    foreach ($posts as $post) {
        $publishedAt = isset($post['date']) ? date('Y-m-d', strtotime($post['date'])) : date('Y-m-d');
        $contentHtml = nl2br(htmlspecialchars($post['content'] ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));

        $stmt->execute([
            'slug' => $post['slug'] ?? bin2hex(random_bytes(4)),
            'title' => $post['title'] ?? '',
            'published_at' => $publishedAt,
            'image_url' => $post['image_url'] ?? '',
            'snippet' => $post['snippet'] ?? '',
            'content_html' => $contentHtml,
            'is_published' => 1,
        ]);
    }
}

function slugify(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('/[^a-z0-9]+/i', '-', $value) ?? '';
    return trim($value, '-');
}

function resetTables(\PDO $pdo, array $tables): void
{
    if (!$tables) {
        return;
    }

    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    foreach ($tables as $table) {
        $pdo->exec('TRUNCATE TABLE ' . $table);
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
}
