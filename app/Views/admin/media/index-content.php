<?php
/** @var array<int, array{path:string,url:string,size:int,modified:int,type:string,variants:array<string,array{path:string,url:string,size:int,modified:int}>}> $media */
/** @var string|null $notice */
/** @var string|null $error */

$notice = $notice ?? null;
$error = $error ?? null;

$formatSize = static function (int $bytes): string {
    if ($bytes < 1024) {
        return $bytes . ' B';
    }
    if ($bytes < 1024 * 1024) {
        return round($bytes / 1024, 1) . ' KB';
    }
    return round($bytes / (1024 * 1024), 2) . ' MB';
};

$availableTypes = [];
foreach ($media as $item) {
    $primaryType = $item['type'] ?? '';
    if ($primaryType !== '') {
        $availableTypes[$primaryType] = true;
    }
    $variantTypes = isset($item['variants']) && is_array($item['variants']) ? array_keys($item['variants']) : [];
    foreach ($variantTypes as $variantType) {
        if ($variantType !== '') {
            $availableTypes[$variantType] = true;
        }
    }
}
$availableTypes = array_keys($availableTypes);
sort($availableTypes);
?>

<section
    class="space-y-6 max-w-6xl"
    data-media-library
    data-csrf-token="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>"
>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-acc">Media Library</h1>
            <p class="text-sm text-muted">Browse uploaded assets and grab their URLs for reuse across the site.</p>
        </div>
    </div>

    <div class="card space-y-4" data-media-tools>
        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-acc">Media Maintenance</h2>
                <p class="text-sm text-muted">Mirror remote assets into the local library and convert images to WebP.</p>
            </div>
            <div class="flex flex-col gap-2 md:flex-row md:items-center md:gap-3">
                <form method="post" action="/admin/media/mirror" data-media-action="mirror">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-md bg-pri text-white text-sm font-medium hover:bg-red-500/80 transition disabled:opacity-60">
                        Local Mirror Images
                    </button>
                </form>
                <form method="post" action="/admin/media/optimize" data-media-action="optimize">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-md bg-pri text-white text-sm font-medium hover:bg-red-500/80 transition disabled:opacity-60">
                        Optimize to WebP
                    </button>
                </form>
                <form method="post" action="/admin/media/upload" data-media-action="upload" enctype="multipart/form-data" class="flex flex-col md:flex-row md:items-center md:gap-2 gap-2">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="text" name="label" placeholder="Label (optional)" class="w-full md:w-40 bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none text-xs md:text-sm">
                    <label class="inline-flex items-center gap-2 text-xs md:text-sm text-muted">
                        <input type="file" name="file" accept=".png,.jpg,.jpeg,.webp,.svg,.ico" class="text-xs md:text-sm">
                    </label>
                    <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-md bg-pri text-white text-sm font-medium hover:bg-red-500/80 transition disabled:opacity-60">
                        Upload image
                    </button>
                </form>
            </div>
        </div>
        <div class="media-optimize-status hidden" data-media-status>
            <p class="text-xs text-muted" data-media-summary>Ready.</p>
            <ol class="space-y-1 text-xs" data-media-log></ol>
        </div>
    </div>

    <?php if (!empty($availableTypes)): ?>
        <div class="flex flex-wrap gap-2" data-media-filters>
            <button type="button" class="media-filter-button media-filter-active" data-media-filter="all">All</button>
            <?php foreach ($availableTypes as $type): ?>
                <button type="button" class="media-filter-button" data-media-filter="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>">
                    .<?= strtoupper(htmlspecialchars($type, ENT_QUOTES, 'UTF-8')); ?>
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

<?php if (!empty($notice)): ?>
    <div class="card border-emerald-500/40 bg-emerald-500/10 text-emerald-100 text-sm">
        <?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

<?php if (!empty($error)): ?>
    <div class="card border-red-500/40 bg-red-500/10 text-red-100 text-sm">
        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
    </div>
<?php endif; ?>

    <div class="card text-sm text-muted <?= !empty($media) ? 'hidden' : '' ?>" data-media-empty>
        <p>No assets uploaded yet. Upload images from any editor form to populate the media library.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 <?= empty($media) ? 'hidden' : '' ?>" data-media-grid>
        <?php foreach ($media as $item): ?>
                <?php
                $isImage = in_array($item['type'], ['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif', 'ico'], true);
                $url = htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8');
                $path = htmlspecialchars($item['path'], ENT_QUOTES, 'UTF-8');
                $size = $formatSize($item['size']);
                $modified = date('Y-m-d H:i', $item['modified']);
                $typeAttr = htmlspecialchars($item['type'], ENT_QUOTES, 'UTF-8');
                $variants = isset($item['variants']) && is_array($item['variants']) ? $item['variants'] : [];
                $variantTypesAttr = htmlspecialchars(implode(',', array_keys($variants)), ENT_QUOTES, 'UTF-8');
                $width = isset($item['width']) ? (int)$item['width'] : null;
                $height = isset($item['height']) ? (int)$item['height'] : null;
                $dimensions = ($width && $height)
                    ? sprintf('W %d &times; H %d px', $width, $height)
                    : 'W n/a &times; H n/a px';
                $inUse = !empty($item['in_use']);
                ?>
                <article
                    class="media-card card space-y-3"
                    data-media-card
                    data-media-type="<?= $typeAttr ?>"
                    data-media-variants="<?= $variantTypesAttr ?>"
                    data-media-path="<?= $path ?>"
                    data-media-url="<?= $url ?>"
                    data-media-width="<?= $width !== null ? (string)$width : '' ?>"
                    data-media-height="<?= $height !== null ? (string)$height : '' ?>"
                    data-media-in-use="<?= $inUse ? '1' : '0' ?>"
                >
                    <div class="media-card__thumb bg-bg2 border border-stroke rounded-lg overflow-hidden aspect-video flex items-center justify-center">
                        <?php if ($isImage): ?>
                            <img src="<?= $url ?>" alt="<?= $path ?>" class="max-h-full max-w-full object-contain media-card__image">
                        <?php else: ?>
                            <span class="text-xs text-muted uppercase tracking-wide"><?= strtoupper($item['type']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="space-y-2">
                        <p class="media-card__path text-sm font-semibold text-acc break-all"><?= $path ?></p>
                        <div class="media-card__meta text-xs text-muted space-y-1">
                            <p class="media-card__dimensions"><?= $dimensions ?></p>
                            <p><?= $size ?> &middot; Updated <?= $modified ?></p>
                        </div>
                        <div class="media-card__status flex items-center gap-2 text-xs">
                            <span class="media-card__badge <?= $inUse ? 'media-card__badge--used' : 'media-card__badge--unused' ?>" data-media-inuse-label>
                                <span class="media-card__badge-dot"></span>
                                <?= $inUse ? 'In use' : 'Not in use' ?>
                            </span>
                        </div>
                    </div>
                    <?php if (!empty($variants)): ?>
                        <div class="flex flex-wrap gap-2 text-xs media-card__variants" data-media-variant-list>
                            <?php foreach ($variants as $variantType => $variant): ?>
                                <?php
                                $variantUrl = htmlspecialchars($variant['url'], ENT_QUOTES, 'UTF-8');
                                $variantSize = $formatSize($variant['size']);
                                $variantLabel = strtoupper(htmlspecialchars($variantType, ENT_QUOTES, 'UTF-8'));
                                ?>
                                <div class="media-variant-pill">
                                    <span class="media-variant-label"><?= $variantLabel ?></span>
                                    <span class="media-variant-size"><?= $variantSize ?></span>
                                    <a href="<?= $variantUrl ?>" target="_blank" rel="noopener" class="media-variant-open">Open</a>
                                    <button type="button" class="media-variant-copy" data-copy-url="<?= $variantUrl ?>">Copy</button>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    <div class="media-card__actions flex flex-wrap items-center gap-2 text-sm">
                        <a href="<?= $url ?>" target="_blank" rel="noopener" class="media-card__action">Open</a>
                        <button type="button" class="media-card__action" data-copy-url="<?= $url ?>">Copy URL</button>
                        <button type="button" class="media-card__action" data-media-replace>Replace</button>
                        <button type="button" class="media-card__action danger" data-media-delete>Delete</button>
                        <input type="file" accept=".png,.jpg,.jpeg,.webp,.svg,.ico" class="hidden" data-media-replace-input>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endforeach; ?>
    </div>
</section>
