<?php
declare(strict_types=1);

namespace App\Models;

final class SocialProofItem extends Model
{
    protected string $table = 'social_proof_items';

    protected array $fillable = [
        'content_type',
        'author_name',
        'author_handle',
        'author_avatar_url',
        'content',
        'link',
        'sort_order',
    ];
}
