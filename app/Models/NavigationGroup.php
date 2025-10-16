<?php
declare(strict_types=1);

namespace App\Models;

final class NavigationGroup extends Model
{
    protected string $table = 'navigation_groups';

    protected array $fillable = [
        'menu_key',
        'group_key',
        'title',
        'is_active',
        'sort_order',
    ];
}
