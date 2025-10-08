<?php
declare(strict_types=1);

namespace App\Models;

final class BlogPost extends Model
{
    protected string $table = 'blog_posts';

    protected array $fillable = [
        'slug',
        'title',
        'published_at',
        'image_url',
        'snippet',
        'content_html',
        'is_published',
    ];
}
