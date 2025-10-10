<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Services\Security\Csrf;

final class MediaController extends Controller
{
    public function index(): void
    {
        $library = $this->gatherMedia();

        $this->view('admin/media/index', [
            'title' => 'Media Library',
            'media' => $library,
            'csrfToken' => Csrf::token(),
        ]);
    }

    /**
     * @return array<int, array{path:string,url:string,size:int,modified:int,type:string}>
     */
    private function gatherMedia(): array
    {
        $root = dirname(__DIR__, 2) . '/public/media';
        if (!is_dir($root)) {
            return [];
        }

        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($root, \FilesystemIterator::SKIP_DOTS)
        );

        /** @var \SplFileInfo $file */
        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $relativePath = str_replace($root, '', $file->getPathname());
            $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
            $extension = strtolower($file->getExtension());

            $files[] = [
                'path' => $relativePath,
                'url' => '/media/' . $relativePath,
                'size' => $file->getSize(),
                'modified' => $file->getMTime(),
                'type' => $extension,
            ];
        }

        usort(
            $files,
            static fn(array $a, array $b) => $b['modified'] <=> $a['modified']
        );

        return $files;
    }
}
