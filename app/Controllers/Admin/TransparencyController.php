<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\TransparencyReport;
use App\Models\TransparencyWallet;
use App\Services\Security\Csrf;
use App\Support\Flash;
use PDO;

final class TransparencyController extends Controller
{
    private TransparencyWallet $wallets;
    private TransparencyReport $reports;
    private PDO $db;

    public function __construct()
    {
        $this->wallets = new TransparencyWallet();
        $this->reports = new TransparencyReport();
        $this->db = Database::connection();
    }

    public function index(): void
    {
        $walletStmt = $this->db->query('SELECT * FROM transparency_wallets ORDER BY sort_order ASC, id ASC');
        $reportStmt = $this->db->query('SELECT * FROM transparency_reports ORDER BY sort_order ASC, id ASC');

        $this->view('admin/transparency/index', [
            'title' => 'Transparency',
            'wallets' => $walletStmt->fetchAll() ?: [],
            'reports' => $reportStmt->fetchAll() ?: [],
            'notice' => Flash::pull('admin.transparency.notice'),
            'error' => Flash::pull('admin.transparency.error'),
            'csrfToken' => Csrf::token(),
        ]);
    }

    public function createWallet(): void
    {
        $this->renderWalletForm($this->defaultWallet(), [], 'Add Wallet', '/admin/transparency/wallets/store');
    }

    public function storeWallet(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, '/admin/transparency/wallets/create');

        [$wallet, $errors] = $this->prepareWallet($_POST);
        if ($errors) {
            $this->renderWalletForm($wallet, $errors, 'Add Wallet', '/admin/transparency/wallets/store');
            return;
        }

        $this->wallets->create($wallet);
        Flash::set('admin.transparency.notice', 'Wallet added.');
        $this->redirect('/admin/transparency');
    }

    public function editWallet(string $id): void
    {
        $wallet = $this->wallets->find($id);
        if (!$wallet) {
            Flash::set('admin.transparency.error', 'Wallet entry not found.');
            $this->redirect('/admin/transparency');
        }

        $this->renderWalletForm($wallet, [], 'Edit Wallet', "/admin/transparency/wallets/update/{$id}");
    }

    public function updateWallet(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, "/admin/transparency/wallets/edit/{$id}");

        if (!$this->wallets->find($id)) {
            Flash::set('admin.transparency.error', 'Wallet entry not found.');
            $this->redirect('/admin/transparency');
        }

        [$wallet, $errors] = $this->prepareWallet($_POST);
        if ($errors) {
            $wallet['id'] = $id;
            $this->renderWalletForm($wallet, $errors, 'Edit Wallet', "/admin/transparency/wallets/update/{$id}");
            return;
        }

        $this->wallets->update($id, $wallet);
        Flash::set('admin.transparency.notice', 'Wallet updated.');
        $this->redirect('/admin/transparency');
    }

    public function destroyWallet(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        if (!$this->wallets->find($id)) {
            Flash::set('admin.transparency.error', 'Wallet entry not found.');
            $this->redirect('/admin/transparency');
        }

        $this->wallets->delete($id);
        Flash::set('admin.transparency.notice', 'Wallet removed.');
        $this->redirect('/admin/transparency');
    }

    public function createReport(): void
    {
        $this->renderReportForm($this->defaultReport(), [], 'Add Report', '/admin/transparency/reports/store');
    }

    public function storeReport(): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, '/admin/transparency/reports/create');

        [$report, $errors] = $this->prepareReport($_POST);
        if ($errors) {
            $this->renderReportForm($report, $errors, 'Add Report', '/admin/transparency/reports/store');
            return;
        }

        $this->reports->create($report);
        Flash::set('admin.transparency.notice', 'Report link added.');
        $this->redirect('/admin/transparency');
    }

    public function editReport(string $id): void
    {
        $report = $this->reports->find($id);
        if (!$report) {
            Flash::set('admin.transparency.error', 'Report entry not found.');
            $this->redirect('/admin/transparency');
        }

        $this->renderReportForm($report, [], 'Edit Report', "/admin/transparency/reports/update/{$id}");
    }

    public function updateReport(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null, "/admin/transparency/reports/edit/{$id}");

        if (!$this->reports->find($id)) {
            Flash::set('admin.transparency.error', 'Report entry not found.');
            $this->redirect('/admin/transparency');
        }

        [$report, $errors] = $this->prepareReport($_POST);
        if ($errors) {
            $report['id'] = $id;
            $this->renderReportForm($report, $errors, 'Edit Report', "/admin/transparency/reports/update/{$id}");
            return;
        }

        $this->reports->update($id, $report);
        Flash::set('admin.transparency.notice', 'Report updated.');
        $this->redirect('/admin/transparency');
    }

    public function destroyReport(string $id): void
    {
        $this->assertValidCsrf($_POST['csrf_token'] ?? null);

        if (!$this->reports->find($id)) {
            Flash::set('admin.transparency.error', 'Report entry not found.');
            $this->redirect('/admin/transparency');
        }

        $this->reports->delete($id);
        Flash::set('admin.transparency.notice', 'Report removed.');
        $this->redirect('/admin/transparency');
    }

    private function renderWalletForm(array $wallet, array $errors, string $title, string $action): void
    {
        $this->view('admin/transparency/wallet-form', [
            'title' => $title,
            'wallet' => $wallet,
            'errors' => $errors,
            'formAction' => $action,
            'submitLabel' => str_contains($title, 'Edit') ? 'Save Changes' : 'Add Wallet',
            'csrfToken' => Csrf::token(),
        ]);
    }

    private function renderReportForm(array $report, array $errors, string $title, string $action): void
    {
        $this->view('admin/transparency/report-form', [
            'title' => $title,
            'report' => $report,
            'errors' => $errors,
            'formAction' => $action,
            'submitLabel' => str_contains($title, 'Edit') ? 'Save Changes' : 'Add Report',
            'csrfToken' => Csrf::token(),
        ]);
    }

    /**
     * @return array{0: array, 1: array}
     */
    private function prepareWallet(array $source): array
    {
        $wallet = [
            'label' => trim((string)($source['label'] ?? '')),
            'wallet_address' => strtoupper(trim((string)($source['wallet_address'] ?? ''))),
            'sort_order' => max(0, (int)($source['sort_order'] ?? 0)),
        ];

        $errors = [];
        if ($wallet['label'] === '') {
            $errors[] = 'Label is required.';
        }
        if ($wallet['wallet_address'] === '') {
            $errors[] = 'Wallet address is required.';
        }

        return [$wallet, $errors];
    }

    /**
     * @return array{0: array, 1: array}
     */
    private function prepareReport(array $source): array
    {
        $reportUrl = trim((string)($source['report_url'] ?? ''));
        $report = [
            'label' => trim((string)($source['label'] ?? '')),
            'report_url' => $reportUrl,
            'sort_order' => max(0, (int)($source['sort_order'] ?? 0)),
        ];

        $errors = [];
        if ($report['label'] === '') {
            $errors[] = 'Label is required.';
        }
        if ($report['report_url'] === '') {
            $errors[] = 'Report URL is required.';
        } elseif (!preg_match('#^https?://#i', $report['report_url']) && !str_starts_with($report['report_url'], '/')) {
            $report['report_url'] = '/' . ltrim($report['report_url'], '/');
        }

        return [$report, $errors];
    }

    private function defaultWallet(): array
    {
        return [
            'label' => '',
            'wallet_address' => '',
            'sort_order' => 0,
        ];
    }

    private function defaultReport(): array
    {
        return [
            'label' => '',
            'report_url' => '',
            'sort_order' => 0,
        ];
    }

    private function assertValidCsrf(?string $token, ?string $redirect = null): void
    {
        if (!Csrf::verify((string)$token)) {
            Flash::set('admin.transparency.error', 'Invalid CSRF token.');
            $this->redirect($redirect ?? '/admin/transparency');
        }
    }
}
