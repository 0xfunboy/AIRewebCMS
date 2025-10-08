<?php
/** @var array $settings */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */
?>

<section class="max-w-4xl space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-acc">Settings</h1>
            <p class="text-sm text-muted">Site-wide metadata, contact info, and copy used across the application.</p>
        </div>
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

    <form method="post" action="/admin/settings" class="space-y-8">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

        <div class="card space-y-4">
            <h2 class="text-sm font-semibold text-acc uppercase tracking-wide">Existing Settings</h2>
            <div class="space-y-4">
                <?php foreach ($settings as $key => $value): ?>
                    <label class="text-sm text-muted flex flex-col gap-2">
                        <span class="font-semibold text-acc"><?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?></span>
                        <textarea name="settings[<?= htmlspecialchars($key, ENT_QUOTES, 'UTF-8') ?>]" rows="3"
                                  class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"><?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?></textarea>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="card space-y-4">
            <h2 class="text-sm font-semibold text-acc uppercase tracking-wide">Add Setting</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label class="text-sm text-muted flex flex-col gap-2">
                    <span>Key</span>
                    <input type="text" name="new_setting_key"
                           placeholder="e.g. support_email"
                           class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
                </label>
                <label class="text-sm text-muted flex flex-col gap-2 md:col-span-1">
                    <span>Value</span>
                    <textarea name="new_setting_value" rows="3"
                              class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"></textarea>
                </label>
            </div>
            <p class="text-xs text-muted">Leave blank to skip creating a new setting.</p>
        </div>

        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-pri text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-red-500/80 transition">
                Save Settings
            </button>
        </div>
    </form>
</section>
