<?php
declare(strict_types=1);

namespace App\Models;

final class CaseStudy extends Model
{
    protected string $table = 'case_studies';

    protected array $fillable = [
        'client',
        'chain',
        'title',
        'summary',
        'image_url',
        'sort_order',
    ];
}
