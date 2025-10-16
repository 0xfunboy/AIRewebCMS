<?php
/** @var array $assets */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */
?>

<section class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-acc">Press Assets</h1>
            <p class="text-xs text-muted mt-1">Resources shown on the Press Kit page.</p>
        </div>
        <a href="/admin/press/create" class="inline-flex items-center px-4 py-2 rounded-md bg-pri text-white text-sm font-medium hover:bg-red-500/80 transition">
            New Asset
        </a>
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

    <?php if (empty($assets)): ?>
        <div class="card text-sm text-muted">
            <p>No assets yet. <a href="/admin/press/create" class="text-acc underline">Add the first asset</a>.</p>
        </div>
    <?php else: ?>
        <div class="card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs uppercase text-muted tracking-wide">
                    <tr>
                        <th class="text-left py-2">Type</th>
                        <th class="text-left py-2">Label</th>
                        <th class="text-left py-2 hidden md:table-cell">File</th>
                        <th class="text-left py-2 hidden md:table-cell">Order</th>
                        <th class="text-right py-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/60">
                    <?php foreach ($assets as $asset): ?>
                        <tr>
                            <td class="py-3 text-muted">
                                <?= htmlspecialchars($asset['asset_type'], ENT_QUOTES, 'UTF-8'); ?>
                            </td>
                            <td class="py-3 font-medium text-acc">
                                <?= htmlspecialchars($asset['label'], ENT_QUOTES, 'UTF-8'); ?>
                            </td>
                            <td class="py-3 text-muted hidden md:table-cell">
                                <a href="<?= htmlspecialchars($asset['file_path'], ENT_QUOTES, 'UTF-8'); ?>" class="text-cy hover:underline" target="_blank" rel="noopener">
                                    <?= htmlspecialchars($asset['file_path'], ENT_QUOTES, 'UTF-8'); ?>
                                </a>
                            </td>
                            <td class="py-3 text-muted hidden md:table-cell">
                                <?= htmlspecialchars((string)($asset['sort_order'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>
                            </td>
                            <td class="py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="/admin/press/edit/<?= urlencode((string)$asset['id']); ?>" class="text-cy hover:underline">Edit</a>
                                    <form method="post" action="/admin/press/delete/<?= urlencode((string)$asset['id']); ?>" onsubmit="return confirm('Delete this asset?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                        <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
