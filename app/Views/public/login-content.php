<?php
$notice = $notice ?? null;
$projectId = $projectId ?? '';
$rpcUrl = $rpcUrl ?? '';
?>
<section class="max-w-xl mx-auto" data-animate>
    <div class="bg-glass border border-stroke rounded-2xl p-8 shadow-deep backdrop-blur-lg">
        <?php if ($notice): ?>
            <div class="mb-6 bg-pri/10 border border-pri/40 text-pri text-sm px-4 py-3 rounded-lg">
                <?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
        <h1 class="text-3xl font-extrabold text-acc tracking-tight">Admin Access</h1>
        <p class="text-muted mt-3 text-sm">Connect your wallet via WalletConnect to enter the admin dashboard.</p>
        <div class="mt-8 space-y-4">
            <button
                type="button"
                id="wallet-connect-button"
                data-project-id="<?= htmlspecialchars($projectId, ENT_QUOTES, 'UTF-8'); ?>"
                data-rpc-url="<?= htmlspecialchars($rpcUrl, ENT_QUOTES, 'UTF-8'); ?>"
                class="w-full bg-pri text-white font-semibold py-3 rounded-lg hover:bg-pri-700 transition"
            >
                Connect Wallet
            </button>
            <p class="text-xs text-muted text-center">
                Wallet must match an address registered in the admin settings.
            </p>
        </div>
        <div id="wallet-error" class="hidden mt-4 text-sm text-pri bg-pri/10 border border-pri/40 rounded-md px-4 py-3"></div>
    </div>
</section>
<script type="module" src="/assets/js/login.js"></script>
