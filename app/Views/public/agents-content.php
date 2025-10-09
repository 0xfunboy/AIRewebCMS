<?php
use App\Core\View;
use App\Support\AdminMode;

/** @var array $agents */

$agentsList = array_values($agents);
?>

<div class="space-y-16">
    <div data-animate>
        <?php View::renderPartial('partials/section-title', [
            'title' => 'Agents Live in the Wild',
            'subtitle' => 'Production deployments across chains delivering real-time intelligence and automation.',
        ]); ?>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($agentsList as $index => $agent): ?>
            <?php
                $status = $agent['status'] ?? 'Live';
                $statusClass = $status === 'Live' ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400';
                $agentId = $agent['id'] ?? null;
            ?>
            <div data-animate data-animate-delay="<?= $index * 100; ?>">
                <div class="bg-glass border border-stroke rounded-lg overflow-hidden transition-all duration-300 transform hover:-translate-y-1 hover:border-pri/50 shadow-deep backdrop-blur-lg h-full">
                    <img loading="lazy" src="<?= htmlspecialchars($agent['image_url'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?= htmlspecialchars($agent['name'], ENT_QUOTES, 'UTF-8'); ?>" class="w-full h-48 object-cover"<?= AdminMode::dataAttrs('agents', 'image_url', (string)$agentId, 'image'); ?>>
                    <div class="p-6 flex flex-col">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="font-bold text-xl text-acc"<?= AdminMode::dataAttrs('agents', 'name', (string)$agentId); ?><?= AdminMode::isAdmin() ? ' class="admin-editable-text"' : ''; ?>><?= htmlspecialchars($agent['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <span class="text-xs font-semibold px-2 py-1 rounded-full <?= $statusClass; ?>"<?= AdminMode::dataAttrs('agents', 'status', (string)$agentId); ?><?= AdminMode::isAdmin() ? ' class="admin-editable-text"' : ''; ?>><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8'); ?></span>
                        </div>
                        <div class="text-sm font-semibold text-cy mb-3"<?= AdminMode::dataAttrs('agents', 'chain', (string)$agentId); ?><?= AdminMode::isAdmin() ? ' class="admin-editable-text"' : ''; ?>><?= htmlspecialchars($agent['chain'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <p class="text-muted text-sm mb-4 flex-grow leading-relaxed"<?= AdminMode::dataAttrs('agents', 'summary', (string)$agentId); ?><?= AdminMode::isAdmin() ? ' class="admin-editable-text"' : ''; ?>><?= htmlspecialchars($agent['summary'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <a href="<?= htmlspecialchars($agent['site_url'], ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer" class="bg-pri/20 text-pri font-bold py-2 px-4 rounded-md text-sm hover:bg-pri/40 transition-colors w-full block text-center mt-auto"<?= AdminMode::dataAttrs('agents', 'site_url', (string)$agentId, 'url'); ?>>
                            Visit Agent
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
