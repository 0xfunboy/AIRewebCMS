<?php
/** @var array $phases */
/** @var array $tracks */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */
?>

<section class="space-y-8">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-acc">Roadmap</h1>
        <a href="/admin/roadmap/create" class="inline-flex items-center px-4 py-2 rounded-md bg-pri text-white text-sm font-medium hover:bg-red-500/80 transition">
            New Phase
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

    <div class="card overflow-x-auto">
        <?php if (empty($phases)): ?>
            <p class="text-sm text-muted">No roadmap phases yet. <a href="/admin/roadmap/create" class="text-acc underline">Create the first phase</a>.</p>
        <?php else: ?>
            <table class="w-full text-sm">
                <thead class="text-xs uppercase text-muted tracking-wide">
                    <tr>
                        <th class="text-left py-2">Label</th>
                        <th class="text-left py-2 hidden md:table-cell">Timeline</th>
                        <th class="text-left py-2 hidden md:table-cell">Items</th>
                        <th class="text-left py-2 hidden md:table-cell">Order</th>
                        <th class="text-right py-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/60">
                    <?php foreach ($phases as $phase): ?>
                        <tr>
                            <td class="py-3 font-medium text-acc">
                                <?= htmlspecialchars($phase['phase_label'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="py-3 text-muted hidden md:table-cell">
                                <?= htmlspecialchars($phase['timeline'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="py-3 text-muted hidden md:table-cell">
                                <?= htmlspecialchars((string)($phase['item_count'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="py-3 text-muted hidden md:table-cell">
                                <?= htmlspecialchars((string)($phase['sort_order'] ?? 0), ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="py-3">
                                <div class="flex items-center justify-end gap-3 flex-wrap">
                                    <a href="/admin/roadmap/edit/<?= urlencode((string)$phase['id']) ?>" class="text-cy hover:underline">Edit</a>
                                    <a href="/admin/roadmap/<?= urlencode((string)$phase['id']) ?>/items" class="text-cy hover:underline">Items</a>
                                    <form method="post" action="/admin/roadmap/delete/<?= urlencode((string)$phase['id']) ?>" onsubmit="return confirm('Delete this phase and all items?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                        <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="card space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg font-semibold text-acc">Always-On Tracks</h2>
                <p class="text-xs text-muted">One track per line, displayed beneath the roadmap.</p>
            </div>
        </div>
        <form method="post" action="/admin/roadmap/tracks" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
            <textarea name="tracks" rows="5" class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"><?php
                echo htmlspecialchars(implode("\n", $tracks), ENT_QUOTES, 'UTF-8');
            ?></textarea>
            <div class="flex items-center gap-3">
                <button type="submit" class="bg-pri text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-red-500/80 transition">
                    Save Tracks
                </button>
            </div>
        </form>
    </div>
</section>
