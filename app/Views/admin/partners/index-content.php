<?php
/** @var array $partners */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */
?>

<section class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-acc">Partners</h1>
            <p class="text-xs text-muted mt-1">Active partners appear in the “Trusted By The Best” section on the homepage.</p>
        </div>
        <a href="/admin/partners/create" class="inline-flex items-center px-4 py-2 rounded-md bg-pri text-white text-sm font-medium hover:bg-red-500/80 transition">
            New Partner
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

    <?php if (empty($partners)): ?>
        <div class="card text-sm text-muted">
            <p>No partners yet. <a href="/admin/partners/create" class="text-acc underline">Add the first partner</a>.</p>
        </div>
    <?php else: ?>
        <div class="card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs uppercase text-muted tracking-wide">
                    <tr>
                        <th class="text-left py-2">Name</th>
                        <th class="text-left py-2">Status</th>
                        <th class="text-left py-2 hidden md:table-cell">Order</th>
                        <th class="text-right py-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/60">
                    <?php foreach ($partners as $partner): ?>
                        <tr>
                            <td class="py-3 font-medium text-acc">
                                <?= htmlspecialchars($partner['name'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="py-3 text-muted">
                                <?= htmlspecialchars($partner['status'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="py-3 text-muted hidden md:table-cell">
                                <?= htmlspecialchars((string)($partner['featured_order'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="/admin/partners/edit/<?= urlencode((string)$partner['id']) ?>" class="text-cy hover:underline">Edit</a>
                                    <form method="post" action="/admin/partners/delete/<?= urlencode((string)$partner['id']) ?>" onsubmit="return confirm('Delete this partner?');">
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
