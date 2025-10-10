<?php
/**
 * @var string $label
 * @var string $name
 * @var string|null $current
 * @var string|null $uploadName
 * @var string|null $helper
 * @var string|null $accept
 */

$current = $current ?? '';
$uploadName = $uploadName ?? ($name . '_upload');
$accept = $accept ?? '.png,.jpg,.jpeg,.webp,.svg,.ico';

$normalize = static function (string $value): string {
    $trim = trim($value);
    if ($trim === '') {
        return '';
    }
    if (str_starts_with($trim, 'http://') || str_starts_with($trim, 'https://')) {
        return $trim;
    }
    return '/' . ltrim($trim, '/');
};

$previewUrl = $normalize($current);
?>

<div class="space-y-3" data-media-input>
    <div class="flex items-center justify-between">
        <p class="text-sm font-semibold text-acc"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></p>
        <a
            href="<?= htmlspecialchars($previewUrl !== '' ? $previewUrl : '#', ENT_QUOTES, 'UTF-8') ?>"
            target="_blank"
            rel="noopener"
            class="text-xs <?= $previewUrl !== '' ? 'text-cy hover:underline' : 'text-muted pointer-events-none' ?>"
            data-media-link
            <?= $previewUrl === '' ? 'aria-disabled="true"' : '' ?>
        ><?= $previewUrl !== '' ? 'Open current' : 'No file selected' ?></a>
    </div>
    <div class="media-input__preview bg-bg2 border border-stroke rounded-lg flex items-center justify-center overflow-hidden">
        <img
            src="<?= htmlspecialchars($previewUrl !== '' ? $previewUrl : '', ENT_QUOTES, 'UTF-8') ?>"
            alt="Preview"
            class="max-h-full max-w-full object-contain <?= $previewUrl === '' ? 'hidden' : '' ?>"
            data-media-preview
        >
        <span class="text-xs text-muted <?= $previewUrl !== '' ? 'hidden' : '' ?>" data-media-placeholder>No preview available</span>
    </div>
    <label class="text-sm text-muted flex flex-col gap-2">
        <span>Media URL</span>
        <input type="url"
               name="<?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?>"
               value="<?= htmlspecialchars($current, ENT_QUOTES, 'UTF-8') ?>"
               class="bg-bg2 border border-stroke rounded-md px-3 py-2 text-acc focus:border-cy focus:outline-none"
               placeholder="https://example.com/image.png"
               data-media-url>
    </label>
    <div class="flex flex-wrap items-center gap-3 text-sm text-muted">
        <label class="inline-flex flex-col gap-2">
            <span>Select from device</span>
            <input type="file"
                   name="<?= htmlspecialchars($uploadName, ENT_QUOTES, 'UTF-8') ?>"
                   accept="<?= htmlspecialchars($accept, ENT_QUOTES, 'UTF-8') ?>"
                   data-media-file>
        </label>
        <span class="text-xs text-cy hidden" data-media-upload-label>New upload will replace the current file after saving.</span>
    </div>
    <?php if (!empty($helper)): ?>
        <p class="text-xs text-muted"><?= htmlspecialchars($helper, ENT_QUOTES, 'UTF-8') ?></p>
    <?php endif; ?>
</div>
