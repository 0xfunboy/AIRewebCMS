<?php
/** @var string $title */
/** @var string $subtitle */
?>
<div class="text-center mb-12">
    <h1 class="text-3xl md:text-4xl font-extrabold text-acc tracking-tight"><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></h1>
    <p class="mt-4 max-w-2xl mx-auto text-muted"><?= htmlspecialchars($subtitle, ENT_QUOTES, 'UTF-8'); ?></p>
</div>
