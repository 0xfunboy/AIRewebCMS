<?php
declare(strict_types=1);

use App\Support\Media;

if (!function_exists('icon_svg')) {
    /**
     * Render an SVG icon by key, loading the markup from the managed media store
     * with a fallback to versioned defaults bundled with the application.
     */
    function icon_svg(string $name, string $class = ''): string
    {
        $safeName = strtolower(trim(str_replace('..', '', $name)));
        if ($safeName === '') {
            $safeName = 'logo';
        }

        static $map = [
            'logo' => 'logo/site-logo.svg',
            'twitter' => 'social/twitter.svg',
            'telegram' => 'social/telegram.svg',
            'discord' => 'social/discord.svg',
            'youtube' => 'social/youtube.svg',
            'twitch' => 'social/twitch.svg',
            'tiktok' => 'social/tiktok.svg',
            'instagram' => 'social/instagram.svg',
            'chevron-down' => 'ui/chevron-down.svg',
            'menu' => 'ui/menu.svg',
            'check' => 'ui/check.svg',
            'chip' => 'ui/chip.svg',
            'cube' => 'ui/cube.svg',
            'device-phone' => 'ui/device-phone.svg',
            'paper-airplane' => 'ui/paper-airplane.svg',
            'beaker' => 'ui/beaker.svg',
            'sparkles' => 'ui/sparkles.svg',
            'storefront' => 'ui/storefront.svg',
            'wallet' => 'ui/wallet.svg',
            'check-circle' => 'ui/check-circle.svg',
        ];

        $relativePath = $map[$safeName] ?? ('ui/' . $safeName . '.svg');

        $path = Media::resolveSvgPath($relativePath);
        if ($path === null) {
            $path = Media::resolveSvgPath('placeholder.svg');
            if ($path === null) {
                return '';
            }
        }

        $svg = file_get_contents($path) ?: '';
        if ($svg === '') {
            return '';
        }

        if ($class !== '') {
            $classAttr = htmlspecialchars($class, ENT_QUOTES, 'UTF-8');
            if (preg_match('/<svg\b[^>]*class="([^"]*)"/i', $svg)) {
                $svg = preg_replace('/(<svg\b[^>]*class=")([^"]*)"/i', '$1$2 ' . $classAttr . '"', $svg, 1);
            } else {
                $svg = preg_replace('/<svg\b([^>]*)>/i', '<svg$1 class="' . $classAttr . '">', $svg, 1);
            }
        }

        return $svg;
    }
}
