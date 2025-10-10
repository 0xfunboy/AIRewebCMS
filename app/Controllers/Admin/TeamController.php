<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\TeamMember;
use App\Services\Security\Csrf;
use App\Support\Flash;
use App\Support\Uploads;
use PDO;

final class TeamController extends Controller
{
    private TeamMember $team;
    private PDO $db;

    public function __construct()
    {
        $this->team = new TeamMember();
        $this->db = Database::connection();
    }

    public function index(): void
    {
        $stmt = $this->db->query('SELECT * FROM team_members ORDER BY sort_order ASC, name ASC');
        $this->view('admin/team/index', [
            'title' => 'Team',
            'members' => $stmt->fetchAll() ?: [],
            'notice' => Flash::pull('admin.team.notice'),
            'error' => Flash::pull('admin.team.error'),
            'csrfToken' => Csrf::token(),
        ]);
    }

    public function create(): void
    {
        $this->renderForm($this->defaultMember(), [], 'Add Team Member', '/admin/team/store');
    }

    public function store(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, '/admin/team/create');

        [$member, $errors] = $this->prepareInput($_POST, $_FILES);
        if ($errors) {
            $this->renderForm($member, $errors, 'Add Team Member', '/admin/team/store');
            return;
        }

        $this->team->create($member);
        Flash::set('admin.team.notice', 'Team member added.');
        $this->redirect('/admin/team');
    }

    public function edit(string $id): void
    {
        $member = $this->team->find($id);
        if (!$member) {
            Flash::set('admin.team.error', 'Team member not found.');
            $this->redirect('/admin/team');
        }

        $this->renderForm($member, [], 'Edit Team Member', "/admin/team/update/{$id}");
    }

    public function update(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, "/admin/team/edit/{$id}");

        if (!$this->team->find($id)) {
            Flash::set('admin.team.error', 'Team member not found.');
            $this->redirect('/admin/team');
        }

        [$member, $errors] = $this->prepareInput($_POST, $_FILES);
        if ($errors) {
            $member['id'] = $id;
            $this->renderForm($member, $errors, 'Edit Team Member', "/admin/team/update/{$id}");
            return;
        }

        $this->team->update($id, $member);
        Flash::set('admin.team.notice', 'Team member updated.');
        $this->redirect('/admin/team');
    }

    public function destroy(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        if (!$this->team->find($id)) {
            Flash::set('admin.team.error', 'Team member not found.');
            $this->redirect('/admin/team');
        }

        $this->team->delete($id);
        Flash::set('admin.team.notice', 'Team member removed.');
        $this->redirect('/admin/team');
    }

    private function renderForm(array $member, array $errors, string $title, string $action): void
    {
        $this->view('admin/team/form', [
            'title' => $title,
            'member' => $member,
            'errors' => $errors,
            'formAction' => $action,
            'submitLabel' => str_contains($title, 'Edit') ? 'Save Changes' : 'Add Member',
            'csrfToken' => Csrf::token(),
        ]);
    }

    /**
     * @return array{0: array, 1: array}
     */
    private function prepareInput(array $source, array $files = []): array
    {
        $avatarUrl = trim((string)($source['avatar_url'] ?? ''));
        $member = [
            'name' => trim((string)($source['name'] ?? '')),
            'role' => trim((string)($source['role'] ?? '')),
            'bio' => trim((string)($source['bio'] ?? '')),
            'avatar_url' => $avatarUrl,
            'telegram_url' => trim((string)($source['telegram_url'] ?? '')),
            'x_url' => trim((string)($source['x_url'] ?? '')),
            'sort_order' => max(0, (int)($source['sort_order'] ?? 0)),
        ];

        $uploadError = null;
        $hasUpload = $this->hasFileUpload($files['avatar_upload'] ?? null);
        if ($hasUpload) {
            try {
                $member['avatar_url'] = $this->storeUploadedFile($files['avatar_upload'], $member['name'] ?: 'team-avatar');
            } catch (\Throwable $e) {
                $uploadError = $e->getMessage();
                $member['avatar_url'] = $avatarUrl;
            }
        }

        $isUploadValid = $hasUpload && $uploadError === null;

        $errors = $this->validate($member, $isUploadValid);
        if ($uploadError !== null) {
            $errors[] = $uploadError;
        }

        return [$member, $errors];
    }

    private function validate(array $member, bool $hasUpload): array
    {
        $errors = [];

        if ($member['name'] === '') {
            $errors[] = 'Name is required.';
        }

        if ($member['role'] === '') {
            $errors[] = 'Role is required.';
        }

        if ($member['bio'] === '') {
            $errors[] = 'Bio is required.';
        }

        if ($member['avatar_url'] === '' && !$hasUpload) {
            $errors[] = 'Provide an avatar URL or upload a new image.';
        }

        return $errors;
    }

    private function defaultMember(): array
    {
        return [
            'name' => '',
            'role' => '',
            'bio' => '',
            'avatar_url' => '',
            'telegram_url' => '',
            'x_url' => '',
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

        Flash::set('admin.team.error', 'Session expired, please try again.');
        $this->redirect($redirect ?? '/admin/team');
    }
}
