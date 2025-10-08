<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use PDO;

final class DashboardController extends Controller
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function index(): void
    {
        $stats = [
            'products' => $this->countRows('products'),
            'agents' => $this->countRows('agents'),
            'partners' => $this->countRows('partners'),
            'teamMembers' => $this->countRows('team_members'),
            'blogPosts' => $this->countRows('blog_posts', 'is_published = 1'),
            'contactMessages' => $this->countRows('contact_messages'),
        ];

        $recentSessions = $this->recentAdminSessions();

        $this->view('admin/dashboard', [
            'title' => 'Dashboard',
            'stats' => $stats,
            'recentSessions' => $recentSessions,
        ]);
    }

    private function countRows(string $table, ?string $where = null): int
    {
        $sql = "SELECT COUNT(*) FROM {$table}";
        if ($where) {
            $sql .= " WHERE {$where}";
        }

        try {
            $stmt = $this->db->query($sql);
            return (int)$stmt->fetchColumn();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function recentAdminSessions(): array
    {
        $sql = <<<SQL
            SELECT
                s.session_token,
                s.created_at,
                s.ip_address,
                s.user_agent,
                s.expires_at,
                a.display_name
            FROM admin_sessions s
            INNER JOIN admins a ON a.id = s.admin_id
            ORDER BY s.created_at DESC
            LIMIT 5
        SQL;

        try {
            $stmt = $this->db->query($sql);
            $rows = $stmt->fetchAll() ?: [];
        } catch (\Throwable $e) {
            return [];
        }

        return array_map(static function (array $session): array {
            $session['ip_address'] = $session['ip_address'] ? @inet_ntop($session['ip_address']) : null;
            $session['session_token'] = substr((string)$session['session_token'], 0, 12) . '...';
            return $session;
        }, $rows);
    }
}
