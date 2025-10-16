<?php
/** @var array $wallets */
/** @var array $reports */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */
?>

<section class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-acc">Transparency Content</h1>
            <p class="text-xs text-muted mt-1">Manage wallets and public reports shown on the Transparency page.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="/admin/transparency/wallets/create" class="inline-flex items-center px-3 py-2 rounded-md bg-pri text-white text-xs font-semibold hover:bg-red-500/80 transition">Add Wallet</a>
            <a href="/admin/transparency/reports/create" class="inline-flex items-center px-3 py-2 rounded-md bg-cy/20 text-cy text-xs font-semibold hover:bg-cy/30 transition">Add Report</a>
        </div>
    </div>

    <?php if ($notice): ?>
        <div class="card border-emerald-500/40 bg-emerald-500/10 text-emerald-100 text-sm">
            <?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="card border-red-500/40 bg-red-500/10 text-red-100 text-sm">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-acc">Wallets</h2>
                <a href="/admin/transparency/wallets/create" class="text-xs text-muted hover:text-acc transition">Add</a>
            </div>
            <?php if (empty($wallets)): ?>
                <p class="text-sm text-muted">No wallets configured yet.</p>
            <?php else: ?>
                <ul class="space-y-3">
                    <?php foreach ($wallets as $wallet): ?>
                        <li class="border border-stroke rounded-lg p-3 bg-bg2/70">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-acc"><?= htmlspecialchars($wallet['label'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <p class="font-mono text-xs text-muted break-all mt-1"><?= htmlspecialchars($wallet['wallet_address'], ENT_QUOTES, 'UTF-8'); ?></p>
                                </div>
                                <span class="text-xs text-muted bg-bg border border-stroke rounded px-2 py-1">Order <?= htmlspecialchars((string)($wallet['sort_order'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <div class="flex items-center gap-3 mt-3">
                                <a href="/admin/transparency/wallets/edit/<?= urlencode((string)$wallet['id']); ?>" class="text-cy hover:underline text-xs">Edit</a>
                                <form method="post" action="/admin/transparency/wallets/delete/<?= urlencode((string)$wallet['id']); ?>" onsubmit="return confirm('Delete this wallet entry?');">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                    <button type="submit" class="text-red-400 hover:text-red-300 text-xs">Delete</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>

        <div class="card space-y-3">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-acc">Reports</h2>
                <a href="/admin/transparency/reports/create" class="text-xs text-muted hover:text-acc transition">Add</a>
            </div>
            <?php if (empty($reports)): ?>
                <p class="text-sm text-muted">No reports configured yet.</p>
            <?php else: ?>
                <ul class="space-y-3">
                    <?php foreach ($reports as $report): ?>
                        <li class="border border-stroke rounded-lg p-3 bg-bg2/70">
                            <div class="flex items-start justify-between gap-3">
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-acc"><?= htmlspecialchars($report['label'], ENT_QUOTES, 'UTF-8'); ?></p>
                                    <a href="<?= htmlspecialchars($report['report_url'], ENT_QUOTES, 'UTF-8'); ?>" class="text-xs text-cy hover:underline break-all" target="_blank" rel="noopener">
                                        <?= htmlspecialchars($report['report_url'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </div>
                                <span class="text-xs text-muted bg-bg border border-stroke rounded px-2 py-1">Order <?= htmlspecialchars((string)($report['sort_order'] ?? 0), ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                            <div class="flex items-center gap-3 mt-3">
                                <a href="/admin/transparency/reports/edit/<?= urlencode((string)$report['id']); ?>" class="text-cy hover:underline text-xs">Edit</a>
                                <form method="post" action="/admin/transparency/reports/delete/<?= urlencode((string)$report['id']); ?>" onsubmit="return confirm('Delete this report entry?');">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                    <button type="submit" class="text-red-400 hover:text-red-300 text-xs">Delete</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</section>
