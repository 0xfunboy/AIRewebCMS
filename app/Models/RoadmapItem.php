<?php
declare(strict_types=1);

namespace App\Models;

final class RoadmapItem extends Model
{
    protected string $table = 'roadmap_items';

    protected array $fillable = [
        'roadmap_phase_id',
        'title',
        'description',
        'sort_order',
    ];
}
