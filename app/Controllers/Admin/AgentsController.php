<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Agent;
use App\Services\Security\Csrf;
use App\Support\Flash;
use PDO;

final class AgentsController extends Controller
{
    private Agent $agents;
    private PDO $db;

    public function __construct()
    {
        $this->agents = new Agent();
        $this->db = Database::connection();
    }

    public function index(): void
    {
        $stmt = $this->db->query('SELECT * FROM agents ORDER BY featured_order ASC, name ASC');
        $this->view('admin/agents/index', [
            'title' => 'Agents',
            'agents' => $stmt->fetchAll() ?: [],
            'notice' => Flash::pull('admin.agents.notice'),
            'error' => Flash::pull('admin.agents.error'),
            'csrfToken' => Csrf::token(),
        ]);
    }

    public function create(): void
    {
        $this->renderForm($this->defaultAgent(), [], 'Create Agent', '/admin/agents/store');
    }

    public function store(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, '/admin/agents/create');

        [$agent, $errors] = $this->prepareInput($_POST);
        if ($errors) {
            $this->renderForm($agent, $errors, 'Create Agent', '/admin/agents/store');
            return;
        }

        $this->agents->create($agent);
        Flash::set('admin.agents.notice', 'Agent created successfully.');
        $this->redirect('/admin/agents');
    }

    public function edit(string $id): void
    {
        $agent = $this->agents->find($id);
        if (!$agent) {
            Flash::set('admin.agents.error', 'Agent not found.');
            $this->redirect('/admin/agents');
        }

        $this->renderForm($agent, [], 'Edit Agent', "/admin/agents/update/{$id}");
    }

    public function update(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, "/admin/agents/edit/{$id}");

        if (!$this->agents->find($id)) {
            Flash::set('admin.agents.error', 'Agent not found.');
            $this->redirect('/admin/agents');
        }

        [$agent, $errors] = $this->prepareInput($_POST);
        if ($errors) {
            $agent['id'] = $id;
            $this->renderForm($agent, $errors, 'Edit Agent', "/admin/agents/update/{$id}");
            return;
        }

        $this->agents->update($id, $agent);
        Flash::set('admin.agents.notice', 'Agent updated successfully.');
        $this->redirect('/admin/agents');
    }

    public function destroy(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        if (!$this->agents->find($id)) {
            Flash::set('admin.agents.error', 'Agent not found.');
            $this->redirect('/admin/agents');
        }

        $this->agents->delete($id);
        Flash::set('admin.agents.notice', 'Agent deleted.');
        $this->redirect('/admin/agents');
    }

    private function renderForm(array $agent, array $errors, string $title, string $action): void
    {
        $this->view('admin/agents/form', [
            'title' => $title,
            'agent' => $agent,
            'errors' => $errors,
            'formAction' => $action,
            'submitLabel' => str_contains($title, 'Edit') ? 'Save Changes' : 'Create Agent',
            'csrfToken' => Csrf::token(),
        ]);
    }

    /**
     * @return array{0: array, 1: array}
     */
    private function prepareInput(array $source): array
    {
        $agent = [
            'name' => trim((string)($source['name'] ?? '')),
            'chain' => trim((string)($source['chain'] ?? '')),
            'status' => trim((string)($source['status'] ?? 'Live')),
            'summary' => trim((string)($source['summary'] ?? '')),
            'site_url' => trim((string)($source['site_url'] ?? '')),
            'image_url' => trim((string)($source['image_url'] ?? '')),
            'badge' => trim((string)($source['badge'] ?? '')),
            'featured_order' => max(0, (int)($source['featured_order'] ?? 0)),
        ];

        $errors = $this->validate($agent);

        return [$agent, $errors];
    }

    private function validate(array $agent): array
    {
        $errors = [];

        if ($agent['name'] === '') {
            $errors[] = 'Name is required.';
        }

        if ($agent['chain'] === '') {
            $errors[] = 'Chain is required.';
        }

        if (!in_array($agent['status'], ['Live', 'In Development'], true)) {
            $errors[] = 'Status must be Live or In Development.';
        }

        if ($agent['summary'] === '') {
            $errors[] = 'Summary is required.';
        }

        if ($agent['site_url'] === '') {
            $errors[] = 'Site URL is required.';
        }

        if ($agent['image_url'] === '') {
            $errors[] = 'Image URL is required.';
        }

        return $errors;
    }

    private function defaultAgent(): array
    {
        return [
            'name' => '',
            'chain' => '',
            'status' => 'Live',
            'summary' => '',
            'site_url' => '',
            'image_url' => '',
            'badge' => '',
            'featured_order' => 0,
        ];
    }

    private function assertValidCsrf(?string $token, ?string $redirect = null): void
    {
        if (Csrf::verify($token)) {
            return;
        }

        Flash::set('admin.agents.error', 'Session expired, please try again.');
        $this->redirect($redirect ?? '/admin/agents');
    }
}
