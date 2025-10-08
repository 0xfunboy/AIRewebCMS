<?php
/** @var array $items */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */
?>

<section class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-acc">Social Proof</h1>
        <a href="/admin/social-proof/create" class="inline-flex items-center px-4 py-2 rounded-md bg-pri text-white text-sm font-medium hover:bg-red-500/80 transition">
            New Entry
        </a>
    </div>

    <?php if ($notice): ?>
        <div class="card border-emerald-500/40 bg-emerald-500/10 text-emerald-100 text-sm">
            <?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="card border-red-500/40 bg-red-500/10 text-red-100 text-sm">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if (empty($items)): ?>
        <div class="card text-sm text-muted">
            <p>No social proof entries yet. <a href="/admin/social-proof/create" class="text-acc underline">Create one now</a>.</p>
        </div>
    <?php else: ?>
        <div class="card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs uppercase text-muted tracking-wide">
                    <tr>
                        <th class="text-left py-2">Author</th>
                        <th class="text-left py-2 hidden md:table-cell">Handle</th>
                        <th class="text-left py-2">Type</th>
                        <th class="text-left py-2 hidden md:table-cell">Order</th>
                        <th class="text-right py-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/60">
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="py-3 font-medium text-acc">
                                <?= htmlspecialchars($item['author_name'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="py-3 text-muted hidden md:table-cell">
                                <?= htmlspecialchars($item['author_handle'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="py-3 text-muted">
                                <?= htmlspecialchars($item['content_type'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="py-3 text-muted hidden md:table-cell">
                                <?= htmlspecialchars((string)($item['sort_order'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="/admin/social-proof/edit/<?= urlencode((string)$item['id']) ?>" class="text-cy hover:underline">Edit</a>
                                    <form method="post" action="/admin/social-proof/delete/<?= urlencode((string)$item['id']) ?>" onsubmit="return confirm('Delete this entry?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
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
