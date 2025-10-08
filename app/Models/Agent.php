<?php
declare(strict_types=1);

namespace App\Models;

final class Agent extends Model
{
    protected string $table = 'agents';

    protected array $fillable = [
        'name',
        'chain',
        'status',
        'summary',
        'site_url',
        'image_url',
        'badge',
        'featured_order',
    ];
}
