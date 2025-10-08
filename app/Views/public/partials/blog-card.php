<?php
/** @var array $post */
?>

<a href="/blog/<?= htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8'); ?>" class="block group">
    <div class="bg-glass border border-stroke rounded-lg overflow-hidden transition-all duration-300 transform group-hover:-translate-y-1 group-hover:border-pri/50 shadow-deep backdrop-blur-lg h-full">
        <img loading="lazy" src="<?= htmlspecialchars($post['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>" class="w-full h-48 object-cover" />
        <div class="p-6">
            <p class="text-xs text-muted mb-2"><?= htmlspecialchars(date('F j, Y', strtotime($post['published_at'])), ENT_QUOTES, 'UTF-8'); ?></p>
            <h3 class="font-bold text-xl text-acc mb-3 group-hover:text-pri transition-colors"><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
            <p class="text-muted text-sm line-clamp-3"><?= htmlspecialchars($post['snippet'], ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
    </div>
</a>
