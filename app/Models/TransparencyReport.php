<?php
declare(strict_types=1);

namespace App\Models;

final class TransparencyReport extends Model
{
    protected string $table = 'transparency_reports';

    protected array $fillable = [
        'label',
        'report_url',
        'sort_order',
    ];
}
