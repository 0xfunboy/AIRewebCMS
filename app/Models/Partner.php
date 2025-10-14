<?php
declare(strict_types=1);

namespace App\Models;

final class Partner extends Model
{
    protected string $table = 'partners';

    protected array $fillable = [
        'name',
        'logo_url',
        'badge_logo_url',
        'url',
        'summary',
        'status',
        'featured_order',
    ];
}
