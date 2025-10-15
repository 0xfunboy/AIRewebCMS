<?php
/** @var array $settings */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */

use App\Core\View;
use App\Support\Media;

$normalizeAsset = static function (?string $value): string {
    $value = $value ?? '';
    $trimmed = trim($value);
    if ($trimmed === '') {
        return '';
    }
    if (str_starts_with($trimmed, 'http://') || str_starts_with($trimmed, 'https://')) {
        return $trimmed;
    }
    if (preg_match('#^(?:/)?(?:media/|assets/svg-default/)#', $trimmed) === 1) {
        return Media::normalizeMediaPath($trimmed);
    }
    return '/' . ltrim($trimmed, '/');
};

$favicon = $normalizeAsset($settings['favicon_path'] ?? '/favicon.svg');
$siteLogo = $normalizeAsset($settings['site_logo'] ?? '');
$siteLogoUsesFallback = $siteLogo === '';

$shareImageRaw = $settings['seo_share_image'] ?? ($settings['og_image'] ?? '');
$shareImage = $normalizeAsset($shareImageRaw);
$shareImageUsesFallback = $shareImage === '';
if ($shareImageUsesFallback) {
    $shareImage = Media::assetSvg('products/product1.svg');
}

$siteLogoHelper = $siteLogoUsesFallback
    ? 'PNG/SVG/WebP, up to 5 MB. Defaults to the bundled site logo when empty.'
    : 'PNG/SVG/WebP, up to 5 MB.';
$faviconHelper = 'Square image or .ico, up to 5 MB.';
$shareImageHelper = $shareImageUsesFallback
    ? 'Recommended 1200x630, PNG/JPEG/WebP, max 5 MB. Defaults to the product hero artwork when empty.'
    : 'Recommended 1200x630, PNG/JPEG/WebP, max 5 MB.';
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
            <?php View::renderPartial('admin/partials/media-input', [
                'label' => 'Site logo',
                'name' => 'site_logo',
                'current' => $siteLogo,
                'uploadName' => 'site_logo',
                'accept' => '.png,.jpg,.jpeg,.webp,.svg',
                'helper' => $siteLogoHelper,
            ]); ?>
            <?php View::renderPartial('admin/partials/media-input', [
                'label' => 'Favicon',
                'name' => 'favicon_path',
                'current' => $favicon,
                'uploadName' => 'favicon',
                'accept' => '.png,.jpg,.jpeg,.webp,.svg,.ico',
                'helper' => $faviconHelper,
            ]); ?>
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
        <?php View::renderPartial('admin/partials/media-input', [
            'label' => 'Share preview image',
            'name' => 'seo_share_image',
            'current' => $shareImage,
            'uploadName' => 'seo_share_image',
            'accept' => '.png,.jpg,.jpeg,.webp',
            'helper' => $shareImageHelper,
        ]); ?>
        <div class="flex items-center gap-3 pt-2">
            <button type="submit" class="bg-pri text-white px-5 py-2 rounded-md text-sm font-medium hover:bg-red-500/80 transition">Save SEO Settings</button>
        </div>
    </form>
</section>
