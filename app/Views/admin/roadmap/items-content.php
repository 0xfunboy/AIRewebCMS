<?php
/** @var array $phase */
/** @var array $items */
/** @var array $errors */
/** @var string $csrfToken */
?>

<section class="max-w-5xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-acc">Phase Items: <?= htmlspecialchars($phase['phase_label'], ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="text-sm text-muted">Define the milestones displayed under this phase.</p>
        </div>
        <a href="/admin/roadmap" class="text-sm text-muted hover:text-acc transition">Back to phases</a>
    </div>

    <?php if ($errors): ?>
        <div class="card border-red-500/40 bg-red-500/10 text-red-100 text-sm space-y-2">
            <p class="font-semibold">Please fix the following:</p>
            <ul class="list-disc list-inside space-y-1 marker:text-red-300">
                <?php foreach ($errors as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="/admin/roadmap/<?= urlencode((string)$phase['id']) ?>/items" class="space-y-6" data-repeat-root>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

        <div id="items-container" class="space-y-4" data-repeat-container>
            <?php if (empty($items)): ?>
                <?php $items = [['title' => '', 'description' => '']]; ?>
            <?php endif; ?>
            <?php foreach ($items as $index => $item): ?>
                <div class="card space-y-4" data-repeat-item>
                    <div class="flex items-center justify-between">
                        <h2 class="text-sm font-semibold text-acc">Item <?= $index + 1 ?></h2>
                        <button type="button" class="text-xs text-red-300 hover:text-red-200" data-repeat-remove>&times; Remove</button>
                    </div>
                    <label class="text-sm text-muted flex flex-col gap-2">
                        <span>Title</span>
                        <input type="text" name="items[<?= $index ?>][title]" required
                               value="<?= htmlspecialchars($item['title'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                               class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
                    </label>
                    <label class="text-sm text-muted flex flex-col gap-2">
                        <span>Description</span>
                        <textarea name="items[<?= $index ?>][description]" rows="3" required
                                  class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"><?= htmlspecialchars($item['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <template id="roadmap-item-template">
            <div class="card space-y-4" data-repeat-item>
                <div class="flex items-center justify-between">
                    <h2 class="text-sm font-semibold text-acc">New Item</h2>
                    <button type="button" class="text-xs text-red-300 hover:text-red-200" data-repeat-remove>&times; Remove</button>
                </div>
                <label class="text-sm text-muted flex flex-col gap-2">
                    <span>Title</span>
                    <input type="text" data-repeat-field="title" required
                           class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
                </label>
                <label class="text-sm text-muted flex flex-col gap-2">
                    <span>Description</span>
                    <textarea rows="3" data-repeat-field="description" required
                              class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"></textarea>
                </label>
            </div>
        </template>

        <div class="flex items-center gap-3">
            <button type="button" class="bg-bg2 border border-stroke px-4 py-2 rounded-md text-sm text-acc hover:border-cy transition" data-repeat-add data-repeat-template="roadmap-item-template" data-repeat-name="items">
                + Add Item
            </button>
        </div>

        <div class="flex items-center gap-3 pt-4">
            <button type="submit" class="bg-pri text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-red-500/80 transition">
                Save Items
            </button>
            <a href="/admin/roadmap" class="text-sm text-muted hover:text-acc transition">Cancel</a>
        </div>
    </form>
</section>
