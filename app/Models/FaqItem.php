<?php
declare(strict_types=1);

namespace App\Models;

final class FaqItem extends Model
{
    protected string $table = 'faq_items';

    protected array $fillable = [
        'question',
        'answer',
        'sort_order',
    ];
}
