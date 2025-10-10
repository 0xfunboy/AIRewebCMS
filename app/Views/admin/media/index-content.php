<?php
/** @var array<int, array{path:string,url:string,size:int,modified:int,type:string}> $media */

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

<section class="space-y-6 max-w-6xl">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-semibold text-acc">Media Library</h1>
            <p class="text-sm text-muted">Browse uploaded assets and grab their URLs for reuse across the site.</p>
        </div>
    </div>

    <form method="post" action="/admin/media/optimize" class="card space-y-3" data-optimize-media>
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-acc">Optimize Media</h2>
                <p class="text-sm text-muted">Mirror remote assets locally and convert everything to optimized WebP files.</p>
            </div>
            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 rounded-md bg-pri text-white text-sm font-medium hover:bg-red-500/80 transition disabled:opacity-60">
                Optimize Images
            </button>
        </div>
        <div class="media-optimize-status hidden" data-optimize-status>
            <p class="text-xs text-muted" data-optimize-summary>Preparing optimization…</p>
            <ol class="space-y-1 text-xs" data-optimize-log></ol>
        </div>
    </form>

    <?php if (empty($media)): ?>
        <div class="card text-sm text-muted">
            <p>No assets uploaded yet. Upload images from any editor form to populate the media library.</p>
        </div>
    <?php else: ?>
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <?php foreach ($media as $item): ?>
                <?php
                $isImage = in_array($item['type'], ['png', 'jpg', 'jpeg', 'webp', 'svg', 'gif', 'ico'], true);
                $url = htmlspecialchars($item['url'], ENT_QUOTES, 'UTF-8');
                $path = htmlspecialchars($item['path'], ENT_QUOTES, 'UTF-8');
                $size = $formatSize($item['size']);
                $modified = date('Y-m-d H:i', $item['modified']);
                ?>
                <article class="card space-y-3" data-media-card>
                    <div class="bg-bg2 border border-stroke rounded-lg overflow-hidden aspect-video flex items-center justify-center">
                        <?php if ($isImage): ?>
                            <img src="<?= $url ?>" alt="<?= $path ?>" class="max-h-full max-w-full object-contain">
                        <?php else: ?>
                            <span class="text-xs text-muted uppercase tracking-wide"><?= strtoupper($item['type']) ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="space-y-1">
                        <p class="text-sm font-semibold text-acc break-all"><?= $path ?></p>
                        <p class="text-xs text-muted"><?= $size ?> &middot; Updated <?= $modified ?></p>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <a href="<?= $url ?>" target="_blank" rel="noopener" class="text-cy hover:underline">Open</a>
                        <button type="button" class="text-muted hover:text-acc transition" data-copy-url="<?= $url ?>">
                            Copy URL
                        </button>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>
