<?php
/** @var bool $enabled */
?>
<div class="admin-toolbar" data-admin-toolbar data-enabled="<?= $enabled ? 'true' : 'false'; ?>">
    <div class="admin-toolbar__inner">
        <span class="admin-toolbar__badge">Admin Mode</span>
        <button type="button" class="admin-toolbar__toggle" data-admin-toggle>
            <?= $enabled ? 'Disable Admin Mode' : 'Enable Admin Mode'; ?>
        </button>
        <span class="admin-toolbar__status" data-admin-status>
            <?= $enabled ? 'Inline editing enabled' : 'Inline editing disabled'; ?>
        </span>
        <div class="admin-toolbar__spacer"></div>
        <a href="/admin/dashboard" class="admin-toolbar__link admin-toolbar__link--primary">Dashboard</a>
        <form method="post" action="/auth/logout" class="admin-toolbar__form">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($enabled ? \\App\Services\Security\Csrf::token() : '', ENT_QUOTES, 'UTF-8'); ?>">
            <button type="submit" class="admin-toolbar__link admin-toolbar__link--button">
                Logout
            </button>
        </form>
    </div>
</div>
