<?php
declare(strict_types=1);

namespace App\Models;

final class NavigationItem extends Model
{
    protected string $table = 'navigation_items';

    protected array $fillable = [
        'group_id',
        'label',
        'url',
        'icon_key',
        'is_external',
        'is_active',
        'sort_order',
    ];
}
