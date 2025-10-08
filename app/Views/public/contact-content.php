<?php
/** @var array $settings */
/** @var string $csrfToken */
/** @var ?string $success */
/** @var ?string $error */

$contactEmail = $settings['contact_email'] ?? 'dev@airewardrop.xyz';
$telegram = $settings['business_telegram'] ?? 'https://t.me/funboynft';
?>

<div class="space-y-8" data-animate>
    <?php if ($success): ?>
        <div class="bg-emerald-500/10 border border-emerald-500/40 text-emerald-300 text-sm px-4 py-3 rounded-lg">
            <?= htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php elseif ($error): ?>
        <div class="bg-pri/10 border border-pri/40 text-pri text-sm px-4 py-3 rounded-lg">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-start">
    <div class="bg-glass border border-stroke rounded-lg p-8" data-animate>
        <h3 class="text-2xl font-bold text-acc mb-6">Send us a message</h3>
        <form method="post" action="/contact" class="space-y-4">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>">
            <div>
                <label class="block text-sm font-medium text-muted mb-1" for="name">Your Name</label>
                <input class="w-full bg-bg2 border border-stroke rounded-md p-2 focus:ring-pri focus:border-pri transition" type="text" name="name" id="name" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-muted mb-1" for="email">Your Email</label>
                <input class="w-full bg-bg2 border border-stroke rounded-md p-2 focus:ring-pri focus:border-pri transition" type="email" name="email" id="email" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-muted mb-1" for="message">Message</label>
                <textarea class="w-full bg-bg2 border border-stroke rounded-md p-2 focus:ring-pri focus:border-pri transition" name="message" id="message" rows="4" required></textarea>
            </div>
            <button type="submit" class="w-full bg-pri text-white font-bold py-3 rounded-md hover:bg-pri-700 transition-colors">
                Submit
            </button>
        </form>
    </div>
    <div class="space-y-8" data-animate data-animate-delay="120">
        <div>
            <h4 class="font-bold text-lg text-acc">Business Inquiries</h4>
            <a href="mailto:<?= htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8'); ?>" class="text-muted hover:text-pri transition">
                <?= htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8'); ?>
            </a>
        </div>
        <div>
            <h4 class="font-bold text-lg text-acc">Business Telegram</h4>
            <a href="<?= htmlspecialchars($telegram, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="text-muted hover:text-pri transition">
                <?= htmlspecialchars(str_replace('https://t.me/', '@', $telegram), ENT_QUOTES, 'UTF-8'); ?>
            </a>
        </div>
        <div>
            <h4 class="font-bold text-lg text-acc">Other Channels</h4>
            <ul class="space-y-2 text-sm">
                <li><a href="https://t.me/AIRewardrop" target="_blank" rel="noopener noreferrer" class="text-muted hover:text-pri transition">Telegram Channel</a></li>
                <li><a href="https://t.me/AIR3Community" target="_blank" rel="noopener noreferrer" class="text-muted hover:text-pri transition">Telegram Community</a></li>
                <li><a href="https://discord.gg/S4f87VdsHt" target="_blank" rel="noopener noreferrer" class="text-muted hover:text-pri transition">Discord</a></li>
                <li><a href="https://x.com/AIRewardrop" target="_blank" rel="noopener noreferrer" class="text-muted hover:text-pri transition">X / Twitter</a></li>
            </ul>
        </div>
    </div>
</div>
