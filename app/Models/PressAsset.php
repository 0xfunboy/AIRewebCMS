<?php
declare(strict_types=1);

namespace App\Models;

final class PressAsset extends Model
{
    protected string $table = 'press_assets';

    protected array $fillable = [
        'asset_type',
        'label',
        'file_path',
        'sort_order',
    ];
}
