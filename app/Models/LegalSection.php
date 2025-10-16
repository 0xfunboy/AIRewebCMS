<?php
declare(strict_types=1);

namespace App\Models;

final class LegalSection extends Model
{
    protected string $table = 'legal_sections';

    protected array $fillable = [
        'title',
        'content_html',
        'sort_order',
    ];
}
