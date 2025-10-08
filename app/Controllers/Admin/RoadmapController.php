<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\RoadmapItem;
use App\Models\RoadmapPhase;
use App\Models\AlwaysOnTrack;
use App\Services\Security\Csrf;
use App\Support\Flash;
use PDO;

final class RoadmapController extends Controller
{
    private RoadmapPhase $phases;
    private RoadmapItem $items;
    private AlwaysOnTrack $tracks;
    private PDO $db;

    public function __construct()
    {
        $this->phases = new RoadmapPhase();
        $this->items = new RoadmapItem();
        $this->tracks = new AlwaysOnTrack();
        $this->db = Database::connection();
    }

    public function index(): void
    {
        $stmt = $this->db->query(
            'SELECT p.*, COUNT(i.id) as item_count
             FROM roadmap_phases p
             LEFT JOIN roadmap_items i ON i.roadmap_phase_id = p.id
             GROUP BY p.id
             ORDER BY p.sort_order ASC, p.id ASC'
        );
        $phases = $stmt->fetchAll() ?: [];

        $tracks = $this->tracks->all();

        $this->view('admin/roadmap/index', [
            'title' => 'Roadmap',
            'phases' => $phases,
            'tracks' => array_map(static fn (array $track) => $track['title'], $tracks),
            'notice' => Flash::pull('admin.roadmap.notice'),
            'error' => Flash::pull('admin.roadmap.error'),
            'csrfToken' => Csrf::token(),
        ]);
    }

    public function create(): void
    {
        $this->renderForm($this->defaultPhase(), [], 'Create Phase', '/admin/roadmap/store');
    }

    public function store(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, '/admin/roadmap/create');

        [$phase, $errors] = $this->preparePhase($_POST);
        if ($errors) {
            $this->renderForm($phase, $errors, 'Create Phase', '/admin/roadmap/store');
            return;
        }

        $this->phases->create($phase);
        Flash::set('admin.roadmap.notice', 'Phase created successfully.');
        $this->redirect('/admin/roadmap');
    }

    public function edit(string $id): void
    {
        $phase = $this->phases->find($id);
        if (!$phase) {
            Flash::set('admin.roadmap.error', 'Phase not found.');
            $this->redirect('/admin/roadmap');
        }

        $this->renderForm($phase, [], 'Edit Phase', "/admin/roadmap/update/{$id}");
    }

    public function update(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, "/admin/roadmap/edit/{$id}");

        if (!$this->phases->find($id)) {
            Flash::set('admin.roadmap.error', 'Phase not found.');
            $this->redirect('/admin/roadmap');
        }

        [$phase, $errors] = $this->preparePhase($_POST, (int)$id);
        if ($errors) {
            $phase['id'] = $id;
            $this->renderForm($phase, $errors, 'Edit Phase', "/admin/roadmap/update/{$id}");
            return;
        }

        $this->phases->update($id, $phase);
        Flash::set('admin.roadmap.notice', 'Phase updated.');
        $this->redirect('/admin/roadmap');
    }

    public function destroy(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        if (!$this->phases->find($id)) {
            Flash::set('admin.roadmap.error', 'Phase not found.');
            $this->redirect('/admin/roadmap');
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('DELETE FROM roadmap_items WHERE roadmap_phase_id = :id');
            $stmt->execute(['id' => $id]);
            $this->phases->delete($id);
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            Flash::set('admin.roadmap.error', 'Unable to delete phase.');
            $this->redirect('/admin/roadmap');
        }

        Flash::set('admin.roadmap.notice', 'Phase deleted.');
        $this->redirect('/admin/roadmap');
    }

    public function items(string $id): void
    {
        $phase = $this->phases->find($id);
        if (!$phase) {
            Flash::set('admin.roadmap.error', 'Phase not found.');
            $this->redirect('/admin/roadmap');
        }

        $items = $this->getItems((int)$id);

        $this->renderItems($phase, $items, []);
    }

    public function updateItems(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, "/admin/roadmap/{$id}/items");

        $phase = $this->phases->find($id);
        if (!$phase) {
            Flash::set('admin.roadmap.error', 'Phase not found.');
            $this->redirect('/admin/roadmap');
        }

        $itemsInput = $_POST['items'] ?? [];
        if (!is_array($itemsInput)) {
            $this->renderItems($phase, [], ['Invalid items payload.']);
            return;
        }

        [$items, $errors] = $this->prepareItems($itemsInput);
        if ($errors) {
            $this->renderItems($phase, $items, $errors);
            return;
        }

        $this->db->beginTransaction();
        try {
            $delete = $this->db->prepare('DELETE FROM roadmap_items WHERE roadmap_phase_id = :id');
            $delete->execute(['id' => $id]);

            if ($items) {
                $insert = $this->db->prepare(
                    'INSERT INTO roadmap_items (roadmap_phase_id, title, description, sort_order)
                     VALUES (:phase_id, :title, :description, :sort_order)'
                );

                foreach ($items as $index => $item) {
                    $insert->execute([
                        'phase_id' => $id,
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'sort_order' => $index,
                    ]);
                }
            }

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            $this->renderItems($phase, $items, ['Unable to save items.']);
            return;
        }

        Flash::set('admin.roadmap.notice', 'Phase items updated.');
        $this->redirect("/admin/roadmap/{$id}/items");
    }

    public function updateTracks(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, '/admin/roadmap');

        $raw = (string)($_POST['tracks'] ?? '');
        $lines = preg_split('/\r\n|\r|\n/', $raw) ?: [];
        $titles = array_values(array_filter(array_map('trim', $lines), static fn (string $line) => $line !== ''));

        $this->db->beginTransaction();
        try {
            $this->db->exec('DELETE FROM always_on_tracks');
            if ($titles) {
                $insert = $this->db->prepare(
                    'INSERT INTO always_on_tracks (title, sort_order) VALUES (:title, :sort_order)'
                );
                foreach ($titles as $index => $title) {
                    $insert->execute([
                        'title' => $title,
                        'sort_order' => $index,
                    ]);
                }
            }
            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            Flash::set('admin.roadmap.error', 'Unable to update tracks.');
            $this->redirect('/admin/roadmap');
        }

        Flash::set('admin.roadmap.notice', 'Tracks updated.');
        $this->redirect('/admin/roadmap');
    }

    private function renderForm(array $phase, array $errors, string $title, string $action): void
    {
        $this->view('admin/roadmap/form', [
            'title' => $title,
            'phase' => $phase,
            'errors' => $errors,
            'formAction' => $action,
            'submitLabel' => str_contains($title, 'Edit') ? 'Save Changes' : 'Create Phase',
            'csrfToken' => Csrf::token(),
        ]);
    }

    private function renderItems(array $phase, array $items, array $errors): void
    {
        $this->view('admin/roadmap/items', [
            'title' => 'Manage Items',
            'phase' => $phase,
            'items' => $items,
            'errors' => $errors,
            'csrfToken' => Csrf::token(),
        ]);
    }

    /**
     * @return array{0: array, 1: array}
     */
    private function preparePhase(array $source, ?int $ignoreId = null): array
    {
        $phase = [
            'phase_label' => trim((string)($source['phase_label'] ?? '')),
            'phase_key' => trim((string)($source['phase_key'] ?? '')),
            'timeline' => trim((string)($source['timeline'] ?? '')),
            'goal' => trim((string)($source['goal'] ?? '')),
            'sort_order' => max(0, (int)($source['sort_order'] ?? 0)),
        ];

        $errors = $this->validatePhase($phase, $ignoreId);

        return [$phase, $errors];
    }

    private function validatePhase(array $phase, ?int $ignoreId = null): array
    {
        $errors = [];

        if ($phase['phase_label'] === '') {
            $errors[] = 'Phase label is required.';
        }

        if ($phase['phase_key'] === '') {
            $errors[] = 'Phase key is required.';
        } elseif (!preg_match('/^[a-z0-9\\-]+$/', $phase['phase_key'])) {
            $errors[] = 'Phase key may only contain lowercase letters, numbers, and hyphens.';
        } elseif ($this->phaseKeyExists($phase['phase_key'], $ignoreId)) {
            $errors[] = 'Phase key is already in use.';
        }

        if ($phase['timeline'] === '') {
            $errors[] = 'Timeline is required.';
        }

        if ($phase['goal'] === '') {
            $errors[] = 'Goal is required.';
        }

        return $errors;
    }

    private function phaseKeyExists(string $key, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM roadmap_phases WHERE phase_key = :key';
        $params = ['key' => $key];

        if ($ignoreId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $ignoreId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * @param array<int, array<string, mixed>> $items
     * @return array{0: array<int, array<string,string>>, 1: array<int, string>}
     */
    private function prepareItems(array $items): array
    {
        $clean = [];
        $errors = [];

        foreach ($items as $item) {
            $title = trim((string)($item['title'] ?? ''));
            $description = trim((string)($item['description'] ?? ''));

            if ($title === '' && $description === '') {
                continue;
            }

            if ($title === '' || $description === '') {
                $errors[] = 'Each item requires both a title and description.';
                continue;
            }

            $clean[] = [
                'title' => $title,
                'description' => $description,
            ];
        }

        return [$clean, $errors];
    }

    /**
     * @return array<int, array<string, string>>
     */
    private function getItems(int $phaseId): array
    {
        $stmt = $this->db->prepare(
            'SELECT title, description FROM roadmap_items WHERE roadmap_phase_id = :id ORDER BY sort_order ASC'
        );
        $stmt->execute(['id' => $phaseId]);
        return $stmt->fetchAll() ?: [];
    }

    private function defaultPhase(): array
    {
        return [
            'phase_label' => '',
            'phase_key' => '',
            'timeline' => '',
            'goal' => '',
            'sort_order' => 0,
        ];
    }

    private function assertValidCsrf(?string $token, ?string $redirect = null): void
    {
        if (Csrf::verify($token)) {
            return;
        }

        Flash::set('admin.roadmap.error', 'Session expired, please try again.');
        $this->redirect($redirect ?? '/admin/roadmap');
    }
}
