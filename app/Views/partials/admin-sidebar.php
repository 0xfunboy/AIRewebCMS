<?php
use App\Core\View;
use App\Support\Media;

$siteLogoUrl = $siteLogo ?? '';
if ($siteLogoUrl === '') {
    $siteLogoUrl = Media::assetSvg('logo/site-logo.svg');
}
?>
<aside class="w-64 bg-bg2 border-r border-stroke hidden lg:flex flex-col">
    <div class="px-6 py-6 border-b border-stroke">
        <a href="/admin/dashboard" class="flex items-center gap-3 text-xl font-bold text-acc">
            <?php if ($siteLogoUrl !== ''): ?>
                <img src="<?= htmlspecialchars($siteLogoUrl, ENT_QUOTES, 'UTF-8'); ?>" alt="AIRewardrop" class="h-7 w-auto">
            <?php else: ?>
                <?php View::renderPartial('partials/logo', ['class' => 'h-7 w-7 text-pri']); ?>
            <?php endif; ?>
            <span>Admin</span>
        </a>
    </div>
    <nav class="flex-1 px-4 py-6 space-y-2 text-sm">
        <a href="/admin/dashboard" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-glass transition">
            <span class="text-muted">Dashboard</span>
        </a>
        <a href="/admin/products" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-glass transition">
            <span class="text-muted">Products</span>
        </a>
        <a href="/admin/agents" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-glass transition">
            <span class="text-muted">Agents</span>
        </a>
        <a href="/admin/partners" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-glass transition">
            <span class="text-muted">Partners</span>
        </a>
        <a href="/admin/social-proof" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-glass transition">
            <span class="text-muted">Social Proof</span>
        </a>
        <a href="/admin/media" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-glass transition">
            <span class="text-muted">Media Library</span>
        </a>
        <a href="/admin/posts" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-glass transition">
            <span class="text-muted">Posts</span>
        </a>
        <a href="/admin/roadmap" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-glass transition">
            <span class="text-muted">Roadmap</span>
        </a>
        <a href="/admin/team" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-glass transition">
            <span class="text-muted">Team</span>
        </a>
        <a href="/admin/settings" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-glass transition">
            <span class="text-muted">Settings</span>
        </a>
        <a href="/" target="_blank" rel="noopener" class="flex items-center gap-3 px-3 py-2 rounded-lg hover:bg-glass transition">
            <span class="text-muted">Open Site Preview</span>
        </a>
    </nav>
    <div class="px-4 py-6 border-t border-stroke">
        <form method="post" action="/auth/logout">
            <button type="submit" class="w-full bg-pri text-white font-semibold py-2 rounded-md hover:bg-pri-700 transition">
                Logout
            </button>
        </form>
    </div>
</aside>
