<?php
/** @var bool $enabled */
?>
<div class="admin-toolbar" data-admin-toolbar data-enabled="<?= $enabled ? 'true' : 'false'; ?>">
    <div class="admin-toolbar__inner">
        <span class="admin-toolbar__badge">Admin Mode</span>
        <button type="button" class="admin-toolbar__toggle" data-admin-toggle>
            <?= $enabled ? 'Disattiva' : 'Attiva'; ?> modalità
        </button>
        <span class="admin-toolbar__status" data-admin-status>
            <?= $enabled ? 'Modifica in-page attiva' : 'Modifica in-page disattivata'; ?>
        </span>
        <div class="admin-toolbar__spacer"></div>
        <a href="/admin/dashboard" class="admin-toolbar__link">Dashboard</a>
        <a href="/logout" class="admin-toolbar__link">Logout</a>
    </div>
</div>
