<?php
use App\Core\View;

/** @var array $posts */
?>

<div>
    <div data-animate>
        <?php View::renderPartial('partials/section-title', [
            'title' => 'Our Blog',
            'subtitle' => 'Updates, insights, and stories from the AIRewardrop team.',
        ]); ?>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($posts as $index => $post): ?>
            <div data-animate data-animate-delay="<?= $index * 100 ?>">
                <?php View::renderPartial('public/partials/blog-card', ['post' => $post]); ?>
            </div>
        <?php endforeach; ?>
    </div>
</div>
