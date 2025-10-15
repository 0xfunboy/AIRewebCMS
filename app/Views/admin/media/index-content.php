<?php
/** @var array<int, array{path:string,url:string,size:int,modified:int,type:string,variants:array<string,array{path:string,url:string,size:int,modified:int,width:?int,height:?int}>}> $media */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */

$formatSize = static function (int $bytes): string {
    if ($bytes < 1024) {
        return $bytes . ' B';
    }
    if ($bytes < 1024 * 1024) {
        return round($bytes / 1024, 1) . ' KB';
    }
    return round($bytes / (1024 * 1024), 2) . ' MB';
};

?>

<section class="space-y-6 max-w-6xl" data-media-root>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-acc">Media Library</h1>
            <p class="text-sm text-muted">Mirror remote assets, optimise images, and manage uploads across the site.</p>
        </div>
    </div>

    <div class="card space-y-4">
        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-acc">Actions</h2>
                <p class="text-sm text-muted">Mirroring fetches remote references (settings, partners, agents, posts…) into <code>/media/…</code> and rewrites the database to use local URLs.</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" id="btn-mirror" class="inline-flex items-center justify-center gap-2 rounded-md bg-pri px-4 py-2 text-sm font-semibold text-white transition hover:bg-pri/80 focus:outline-none focus:ring-2 focus:ring-pri/50">
                    Local Mirror Images
                </button>
                <button type="button" id="btn-optimize" class="inline-flex items-center justify-center gap-2 rounded-md bg-bg2 px-4 py-2 text-sm font-semibold text-acc transition hover:bg-bg2/80 focus:outline-none focus:ring-2 focus:ring-bg2/50">
                    Optimize to WebP
                </button>
                <label class="inline-flex items-center justify-center gap-2 rounded-md border border-stroke px-4 py-2 text-sm font-semibold text-acc transition hover:border-cy hover:text-cy cursor-pointer">
                    <input type="file" id="file-upload" class="hidden" accept=".png,.jpg,.jpeg,.webp,.svg,.ico">
                    Upload image
                </label>
            </div>
        </div>
        <pre id="media-log" class="hidden max-h-60 overflow-auto whitespace-pre-wrap rounded-md border border-stroke bg-bg2 p-3 text-xs text-muted"></pre>
    </div>

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

    <div id="media-explorer" class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        <?php if (empty($media)): ?>
            <div class="card text-sm text-muted">
                <p>No assets uploaded yet. Use <strong>Upload image</strong> or pull remote references with <strong>Local Mirror Images</strong>.</p>
            </div>
        <?php else: ?>
            <?php foreach ($media as $item): ?>
                <?php
                $isImage = in_array($item['type'], ['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif', 'ico'], true);
                $path = htmlspecialchars($item['path'], ENT_QUOTES, 'UTF-8');
                $url = htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8');
                $size = $formatSize($item['size']);
                $modified = date('Y-m-d H:i', $item['modified']);
                $width = isset($item['width']) ? (int)$item['width'] : null;
                $height = isset($item['height']) ? (int)$item['height'] : null;
                $dimensions = ($width && $height)
                    ? sprintf('W %d × H %d px', $width, $height)
                    : 'W n/a × H n/a px';
                $inUse = !empty($item['in_use']);
                ?>
                <article class="card space-y-3" data-media-item data-path="<?= $path ?>">
                    <div class="bg-bg2 border border-stroke rounded-lg overflow-hidden aspect-video flex items-center justify-center">
                        <?php if ($isImage): ?>
                            <img src="<?= $url ?>" alt="<?= $path ?>" class="max-h-full max-w-full object-contain">
                        <?php else: ?>
                            <span class="text-xs text-muted uppercase tracking-wide"><?= strtoupper($item['type']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-acc break-all"><?= $path ?></p>
                            <span class="media-card__badge <?= $inUse ? 'media-card__badge--used' : 'media-card__badge--unused' ?>">
                                <span class="media-card__badge-dot"></span>
                                <?= $inUse ? 'In use' : 'Not in use' ?>
                            </span>
                        </div>
                        <p class="text-xs text-muted"><?= $dimensions ?> · <?= $size ?> · Updated <?= $modified ?></p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2 text-sm">
                        <button type="button" class="media-card__action btn-copy" data-action="copy" data-url="<?= $url ?>">Copy URL</button>
                        <button type="button" class="media-card__action" data-action="replace">Replace</button>
                        <button type="button" class="media-card__action danger" data-action="delete">Delete</button>
                        <input type="file" accept=".png,.jpg,.jpeg,.webp,.svg,.ico" class="hidden media-replace-input" data-path="<?= $path ?>">
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>
