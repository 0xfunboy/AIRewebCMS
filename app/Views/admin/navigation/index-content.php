<?php
/** @var array $groups */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */
?>

<section class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-acc">Navigation</h1>
            <p class="text-xs text-muted mt-1">Control header and footer links. Toggle visibility, edit labels, and adjust destinations.</p>
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

    <?php foreach ($groups as $entry): ?>
        <?php $group = $entry['group']; ?>
        <div class="card space-y-4">
            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-acc flex items-center gap-2">
                        <?= htmlspecialchars($group['title'], ENT_QUOTES, 'UTF-8'); ?>
                        <span class="text-xs text-muted bg-bg border border-stroke rounded px-2 py-1 uppercase tracking-wide">
                            <?= htmlspecialchars($group['menu_key'], ENT_QUOTES, 'UTF-8'); ?>
                        </span>
                    </h2>
                    <p class="text-xs text-muted">Group key: <code><?= htmlspecialchars($group['group_key'], ENT_QUOTES, 'UTF-8'); ?></code></p>
                </div>
                <form method="post" action="/admin/navigation/group/<?= urlencode((string)$group['id']); ?>" class="flex flex-wrap items-center gap-3">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <label class="text-xs text-muted flex flex-col">
                        <span>Title</span>
                        <input type="text" name="title" value="<?= htmlspecialchars($group['title'], ENT_QUOTES, 'UTF-8'); ?>" class="bg-bg2 border border-stroke rounded px-3 py-1.5 text-sm text-acc focus:border-cy focus:outline-none" required>
                    </label>
                    <label class="text-xs text-muted flex flex-col">
                        <span>Sort</span>
                        <input type="number" name="sort_order" value="<?= htmlspecialchars((string)$group['sort_order'], ENT_QUOTES, 'UTF-8'); ?>" min="0" class="bg-bg2 border border-stroke rounded px-3 py-1.5 text-sm text-acc focus:border-cy focus:outline-none" required>
                    </label>
                    <label class="flex items-center gap-2 text-xs text-muted">
                        <input type="checkbox" name="is_active" value="1" <?= !empty($group['is_active']) ? 'checked' : ''; ?> class="rounded border-stroke bg-bg2">
                        Visible
                    </label>
                    <button type="submit" class="bg-cy/20 text-cy text-xs font-semibold px-3 py-2 rounded-md hover:bg-cy/30 transition">Save Group</button>
                </form>
            </div>
            <?php $items = $entry['items']; ?>
            <?php if (empty($items)): ?>
                <p class="text-sm text-muted">No links configured for this group.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="text-xs uppercase text-muted tracking-wide">
                            <tr>
                                <th class="text-left py-2">Label</th>
                                <th class="text-left py-2">URL</th>
                                <th class="text-left py-2 hidden lg:table-cell">Icon</th>
                                <th class="text-left py-2">Sort</th>
                                <th class="text-left py-2">External</th>
                                <th class="text-left py-2">Visible</th>
                                <th class="text-right py-2">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-border/60">
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td class="py-3 text-acc font-medium" colspan="7">
                                        <form method="post" action="/admin/navigation/item/<?= urlencode((string)$item['id']); ?>" class="flex flex-col gap-3 lg:flex-row lg:items-center lg:gap-4">
                                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                                            <input type="text" name="label" value="<?= htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>" class="bg-bg2 border border-stroke rounded px-3 py-2 text-sm text-acc focus:border-cy focus:outline-none" required>
                                            <input type="text" name="url" value="<?= htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8'); ?>" class="bg-bg2 border border-stroke rounded px-3 py-2 text-sm text-acc focus:border-cy focus:outline-none w-full lg:w-80" required>
                                            <?php if (($group['group_key'] ?? '') === 'footer_social'): ?>
                                                <input type="text" name="icon_key" value="<?= htmlspecialchars($item['icon_key'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Icon (e.g. twitter)" class="bg-bg2 border border-stroke rounded px-3 py-2 text-sm text-acc focus:border-cy focus:outline-none hidden lg:block">
                                            <?php else: ?>
                                                <input type="hidden" name="icon_key" value="<?= htmlspecialchars($item['icon_key'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php endif; ?>
                                            <input type="number" name="sort_order" value="<?= htmlspecialchars((string)$item['sort_order'], ENT_QUOTES, 'UTF-8'); ?>" min="0" class="bg-bg2 border border-stroke rounded px-3 py-2 text-sm text-acc focus:border-cy focus:outline-none w-20" required>
                                            <label class="flex items-center gap-2 text-xs text-muted">
                                                <input type="checkbox" name="is_external" value="1" <?= !empty($item['is_external']) ? 'checked' : ''; ?> class="rounded border-stroke bg-bg2">
                                                External
                                            </label>
                                            <label class="flex items-center gap-2 text-xs text-muted">
                                                <input type="checkbox" name="is_active" value="1" <?= !empty($item['is_active']) ? 'checked' : ''; ?> class="rounded border-stroke bg-bg2">
                                                Visible
                                            </label>
                                            <button type="submit" class="ml-auto bg-pri text-white text-xs font-semibold px-3 py-2 rounded-md hover:bg-pri-700 transition">Save</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</section>
