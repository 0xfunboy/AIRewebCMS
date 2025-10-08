<?php
/** @var array $items */

if (!function_exists('render_social_icon')) {
    function render_social_icon(string $type): string
    {
        return match ($type) {
            'Tweet' => icon_svg('twitter', 'h-6 w-6 text-[#1DA1F2]'),
            'Testimonial' => icon_svg('check-circle', 'h-6 w-6 text-cy'),
            'Media' => icon_svg('sparkles', 'h-6 w-6 text-yl'),
            default => icon_svg('sparkles', 'h-6 w-6 text-pri'),
        };
    }
}
?>

<div>
    <div data-animate>
        <?php \App\Core\View::renderPartial('partials/section-title', [
            'title' => 'What People Are Saying',
            'subtitle' => 'From tweets by industry leaders to testimonials from our partners, see what the community thinks about AIRewardrop.',
        ]); ?>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($items as $index => $item): ?>
            <a href="<?= htmlspecialchars($item['link'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" data-animate data-animate-delay="<?= $index * 100; ?>" class="block h-full">
                <div class="bg-glass border border-stroke rounded-lg p-6 flex flex-col h-full hover:border-pri/50 transition-all duration-300 transform hover:-translate-y-1 shadow-deep backdrop-blur-lg">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center gap-3">
                            <img loading="lazy" src="<?= htmlspecialchars($item['author_avatar_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($item['author_name'], ENT_QUOTES, 'UTF-8'); ?>" class="w-12 h-12 rounded-full border-2 border-stroke" />
                            <div>
                                <p class="font-bold text-acc"><?= htmlspecialchars($item['author_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="text-sm text-muted"><?= htmlspecialchars($item['author_handle'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                        </div>
                        <?= render_social_icon($item['content_type'] ?? ''); ?>
                    </div>
                    <p class="text-muted text-sm flex-grow leading-relaxed">“<?= htmlspecialchars($item['content'], ENT_QUOTES, 'UTF-8'); ?>”</p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
