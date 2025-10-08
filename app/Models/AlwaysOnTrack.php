<?php
declare(strict_types=1);

namespace App\Models;

final class AlwaysOnTrack extends Model
{
    protected string $table = 'always_on_tracks';

    protected array $fillable = [
        'title',
        'sort_order',
    ];
}
