<?php
/** @var array $settings */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */

$normalizeAsset = static function (?string $value): string {
    $value = $value ?? '';
    $trimmed = trim($value);
    if ($trimmed === '') {
        return '';
    }
    if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
        return $trimmed;
    }
    return '/' . ltrim($trimmed, '/');
};

$siteLogo = $normalizeAsset($settings['site_logo'] ?? '');
$favicon = $normalizeAsset($settings['favicon_path'] ?? '/favicon.svg');
$shareImage = $normalizeAsset($settings['seo_share_image'] ?? ($settings['og_image'] ?? ''));
$baseSiteName = $settings['site_name'] ?? 'AIRewardrop';
?>

<section class="space-y-8 max-w-4xl">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-acc">Site Settings</h1>
            <p class="text-sm text-muted">Manage branding, SEO preview, and global metadata.</p>
        </div>
        <a href="/" target="_blank" class="text-sm text-cy hover:underline">Open site</a>
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

    <form method="post" action="/admin/settings" class="card space-y-4" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="action" value="general">
        <h2 class="text-sm font-semibold text-acc uppercase tracking-wide">General</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Site title</span>
                <input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
            </label>
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Tagline</span>
                <input type="text" name="site_tagline" value="<?= htmlspecialchars($settings['site_tagline'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
            </label>
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Contact email</span>
                <input type="email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
            </label>
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Telegram (business)</span>
                <input type="url" name="business_telegram" value="<?= htmlspecialchars($settings['business_telegram'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none" placeholder="https://t.me/...">
            </label>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-pri text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-red-500/80 transition">Save General Settings</button>
        </div>
    </form>

    <form method="post" action="/admin/settings" class="card space-y-4" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="action" value="brand">
        <h2 class="text-sm font-semibold text-acc uppercase tracking-wide">Brand Assets</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <p class="text-sm font-semibold text-acc">Site logo</p>
                <div class="bg-bg2 border border-stroke rounded-lg p-4 flex items-center justify-center min-h-[120px]">
                    <?php if ($siteLogo): ?>
                        <img src="<?= htmlspecialchars($siteLogo, ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($baseSiteName, ENT_QUOTES, 'UTF-8'); ?>" class="max-h-20 max-w-full">
                    <?php else: ?>
                        <span class="text-xs text-muted">No logo uploaded yet</span>
                    <?php endif; ?>
                </div>
                <input type="file" name="site_logo" accept=".png,.jpg,.jpeg,.webp,.svg" class="text-sm text-muted">
                <p class="text-xs text-muted">PNG/SVG/WebP, up to 5 MB.</p>
            </div>
            <div class="space-y-3">
                <p class="text-sm font-semibold text-acc">Favicon</p>
                <div class="bg-bg2 border border-stroke rounded-lg p-4 flex items-center justify-center min-h-[120px]">
                    <img src="<?= htmlspecialchars($favicon, ENT_QUOTES, 'UTF-8'); ?>" alt="Favicon" class="h-12 w-12">
                </div>
                <input type="file" name="favicon" accept=".png,.jpg,.jpeg,.webp,.svg,.ico" class="text-sm text-muted">
                <p class="text-xs text-muted">Square image or .ico, up to 5 MB.</p>
            </div>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-pri text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-red-500/80 transition">Save Brand Assets</button>
        </div>
    </form>

    <form method="post" action="/admin/settings" class="card space-y-4" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="action" value="seo">
        <h2 class="text-sm font-semibold text-acc uppercase tracking-wide">SEO & Social Preview</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Meta title</span>
                <input type="text" name="seo_meta_title" value="<?= htmlspecialchars($settings['seo_meta_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
            </label>
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Social title</span>
                <input type="text" name="seo_social_title" value="<?= htmlspecialchars($settings['seo_social_title'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none">
            </label>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Meta description</span>
                <textarea name="seo_meta_description" rows="3" class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"><?= htmlspecialchars($settings['seo_meta_description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </label>
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Social description</span>
                <textarea name="seo_social_description" rows="3" class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"><?= htmlspecialchars($settings['seo_social_description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </label>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>X (Twitter) preview</span>
                <textarea name="seo_twitter_description" rows="3" class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"><?= htmlspecialchars($settings['seo_twitter_description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </label>
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Telegram preview</span>
                <textarea name="seo_telegram_description" rows="3" class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"><?= htmlspecialchars($settings['seo_telegram_description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </label>
            <label class="text-sm text-muted flex flex-col gap-2">
                <span>Discord preview</span>
                <textarea name="seo_discord_description" rows="3" class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"><?= htmlspecialchars($settings['seo_discord_description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
            </label>
        </div>
        <div class="space-y-3">
            <p class="text-sm font-semibold text-acc">Share preview image</p>
            <div class="bg-bg2 border border-stroke rounded-lg p-4 flex items-center justify-center min-h-[160px]">
                <?php if ($shareImage): ?>
                    <img src="<?= htmlspecialchars($shareImage, ENT_QUOTES, 'UTF-8'); ?>" alt="Share preview" class="max-h-40 max-w-full">
                <?php else: ?>
                    <span class="text-xs text-muted">No preview image uploaded</span>
                <?php endif; ?>
            </div>
            <input type="file" name="seo_share_image" accept=".png,.jpg,.jpeg,.webp" class="text-sm text-muted">
            <p class="text-xs text-muted">Recommended 1200x630, PNG/JPEG/WebP, max 5 MB.</p>
        </div>
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-pri text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-red-500/80 transition">Save SEO Settings</button>
        </div>
    </form>
</section>
