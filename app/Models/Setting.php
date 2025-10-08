<?php
declare(strict_types=1);

namespace App\Models;

final class Setting extends Model
{
    protected string $table = 'settings';

    protected string $primaryKey = 'setting_key';

    protected array $fillable = [
        'setting_key',
        'setting_value',
    ];
}
