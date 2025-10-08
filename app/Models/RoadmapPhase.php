<?php
declare(strict_types=1);

namespace App\Models;

final class RoadmapPhase extends Model
{
    protected string $table = 'roadmap_phases';

    protected array $fillable = [
        'phase_label',
        'phase_key',
        'timeline',
        'goal',
        'sort_order',
    ];
}
