<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\NavigationGroup;
use App\Models\NavigationItem;
use App\Services\Security\Csrf;
use App\Support\Flash;
use PDO;

final class NavigationController extends Controller
{
    private NavigationGroup $groups;
    private NavigationItem $items;
    private PDO $db;

    public function __construct()
    {
        $this->groups = new NavigationGroup();
        $this->items = new NavigationItem();
        $this->db = Database::connection();
    }

    public function index(): void
    {
        $groupStmt = $this->db->query(
            'SELECT * FROM navigation_groups ORDER BY menu_key ASC, sort_order ASC, id ASC'
        );
        $groups = $groupStmt->fetchAll() ?: [];

        $groupIds = array_column($groups, 'id');
        $itemsByGroup = [];
        if ($groupIds) {
            $placeholders = implode(',', array_fill(0, count($groupIds), '?'));
            $itemStmt = $this->db->prepare(
                "SELECT * FROM navigation_items WHERE group_id IN ($placeholders) ORDER BY sort_order ASC, id ASC"
            );
            $itemStmt->execute($groupIds);
            foreach ($itemStmt->fetchAll() ?: [] as $item) {
                $itemsByGroup[$item['group_id']][] = $item;
            }
        }

        $structured = [];
        foreach ($groups as $group) {
            $groupId = (int)$group['id'];
            $structured[] = [
                'group' => $group,
                'items' => $itemsByGroup[$groupId] ?? [],
            ];
        }

        $this->view('admin/navigation/index', [
            'title' => 'Navigation',
            'groups' => $structured,
            'notice' => Flash::pull('admin.navigation.notice'),
            'error' => Flash::pull('admin.navigation.error'),
            'csrfToken' => Csrf::token(),
        ]);
    }

    public function updateGroup(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        $group = $this->groups->find($id);
        if (!$group) {
            Flash::set('admin.navigation.error', 'Navigation group not found.');
            $this->redirect('/admin/navigation');
        }

        $title = trim((string)($_POST['title'] ?? ''));
        $isActive = !empty($_POST['is_active']);
        $sortOrder = max(0, (int)($_POST['sort_order'] ?? $group['sort_order'] ?? 0));

        if ($title === '') {
            Flash::set('admin.navigation.error', 'Group title cannot be empty.');
            $this->redirect('/admin/navigation');
        }

        $this->groups->update($id, [
            'title' => $title,
            'is_active' => $isActive ? 1 : 0,
            'sort_order' => $sortOrder,
        ]);

        Flash::set('admin.navigation.notice', 'Navigation group updated.');
        $this->redirect('/admin/navigation');
    }

    public function updateItem(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        $item = $this->items->find($id);
        if (!$item) {
            Flash::set('admin.navigation.error', 'Navigation item not found.');
            $this->redirect('/admin/navigation');
        }

        $label = trim((string)($_POST['label'] ?? ''));
        $url = trim((string)($_POST['url'] ?? ''));
        $icon = trim((string)($_POST['icon_key'] ?? ''));
        $isExternal = !empty($_POST['is_external']);
        $isActive = !empty($_POST['is_active']);
        $sortOrder = max(0, (int)($_POST['sort_order'] ?? $item['sort_order'] ?? 0));

        if ($label === '') {
            Flash::set('admin.navigation.error', 'Item label is required.');
            $this->redirect('/admin/navigation');
        }

        if ($url === '') {
            Flash::set('admin.navigation.error', 'Item URL is required.');
            $this->redirect('/admin/navigation');
        }

        $this->items->update($id, [
            'label' => $label,
            'url' => $url,
            'icon_key' => $icon !== '' ? $icon : null,
            'is_external' => $isExternal ? 1 : 0,
            'is_active' => $isActive ? 1 : 0,
            'sort_order' => $sortOrder,
        ]);

        Flash::set('admin.navigation.notice', 'Navigation link updated.');
        $this->redirect('/admin/navigation');
    }

    private function assertValidCsrf(?string $token): void
    {
        if (!Csrf::verify((string)$token)) {
            Flash::set('admin.navigation.error', 'Invalid CSRF token.');
            $this->redirect('/admin/navigation');
        }
    }
}
