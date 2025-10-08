<?php
declare(strict_types=1);

namespace App\Models;

final class TeamMember extends Model
{
    protected string $table = 'team_members';

    protected array $fillable = [
        'name',
        'role',
        'bio',
        'avatar_url',
        'telegram_url',
        'x_url',
        'sort_order',
    ];
}
