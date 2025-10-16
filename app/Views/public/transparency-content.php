<?php
/** @var array $settings */
/** @var array $wallets */
/** @var array $reports */

$wallets = $wallets ?? [];
$reports = $reports ?? [];
$contactEmail = $settings['contact_email'] ?? 'press@airewardrop.xyz';
?>

<div class="space-y-16">
    <div data-animate>
        <?php \App\Core\View::renderPartial('partials/section-title', [
            'title' => 'Our Commitment to Transparency',
            'subtitle' => 'We believe in building in the open. Here you can find key information about our operations, wallets, and reports.',
        ]); ?>
    </div>

    <section class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-8" data-animate>
        <div class="bg-glass border border-stroke rounded-lg p-6 shadow-deep space-y-4">
            <h3 class="text-xl font-bold text-acc">Dev Wallets</h3>
            <p class="text-muted text-sm">Operational and team wallets are disclosed for full transparency. All team allocations follow vesting schedules.</p>
            <?php if (empty($wallets)): ?>
                <p class="text-sm text-muted">Wallet disclosures will be published soon.</p>
            <?php else: ?>
                <div class="space-y-3">
                    <?php foreach ($wallets as $index => $wallet): ?>
                        <div class="flex items-center justify-between bg-bg2 border border-stroke rounded-md p-3" data-animate data-animate-delay="<?= $index * 80; ?>">
                            <div class="min-w-0">
                                <p class="text-xs text-muted uppercase tracking-wide"><?= htmlspecialchars($wallet['label'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <p class="font-mono text-sm text-txt truncate"><?= htmlspecialchars($wallet['wallet_address'], ENT_QUOTES, 'UTF-8'); ?></p>
                            </div>
                            <button type="button" class="ml-4 p-2 rounded-md bg-glass hover:bg-stroke transition-colors" data-copy-text="<?= htmlspecialchars($wallet['wallet_address'], ENT_QUOTES, 'UTF-8'); ?>">
                                <span class="sr-only">Copy address</span>
                                📋
                            </button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <div class="bg-glass border border-stroke rounded-lg p-6 shadow-deep space-y-4">
            <h3 class="text-xl font-bold text-acc">Liquidity & Reports</h3>
            <p class="text-muted text-sm">Initial liquidity is locked. We publish regular reports covering treasury status, builds, and roadmap progress.</p>
            <?php if (empty($reports)): ?>
                <p class="text-sm text-muted">Reports will be published here. Check back soon.</p>
            <?php else: ?>
                <ul class="space-y-3 text-sm">
                    <?php foreach ($reports as $report): ?>
                        <li>
                            <a href="<?= htmlspecialchars($report['report_url'], ENT_QUOTES, 'UTF-8'); ?>" class="text-cy hover:underline flex items-center gap-2">
                                <span>→</span> <?= htmlspecialchars($report['label'], ENT_QUOTES, 'UTF-8'); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <p class="text-xs text-muted/80">Press & transparency inquiries: <a href="mailto:<?= htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8'); ?>" class="text-cy hover:underline"><?= htmlspecialchars($contactEmail, ENT_QUOTES, 'UTF-8'); ?></a></p>
        </div>
    </section>
</div>
