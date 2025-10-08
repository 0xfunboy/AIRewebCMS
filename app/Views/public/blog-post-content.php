<?php
/** @var array $post */
?>

<div class="max-w-4xl mx-auto">
    <div data-animate>
        <a href="/blog" class="text-pri font-semibold hover:underline mb-8 inline-block">&larr; Back to Blog</a>
        <h1 class="text-4xl md:text-5xl font-extrabold text-acc tracking-tight"><?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
        <p class="mt-4 text-muted"><?= htmlspecialchars(date('F j, Y', strtotime($post['published_at'])), ENT_QUOTES, 'UTF-8'); ?></p>
    </div>
    <div data-animate data-animate-delay="100">
        <img src="<?= htmlspecialchars($post['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8'); ?>" class="w-full h-auto max-h-[500px] object-cover rounded-lg my-8 border border-stroke" />
    </div>
    <div data-animate data-animate-delay="200">
        <div class="prose prose-invert text-muted max-w-none space-y-4 bg-glass border border-stroke p-8 rounded-lg shadow-deep backdrop-blur-lg">
            <?= $post['content_html']; ?>
        </div>
    </div>
</div>
