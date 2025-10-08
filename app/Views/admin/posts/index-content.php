<?php
/** @var array $posts */
/** @var string|null $notice */
/** @var string|null $error */
/** @var string $csrfToken */
?>

<section class="space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-acc">Blog Posts</h1>
        <a href="/admin/posts/create" class="inline-flex items-center px-4 py-2 rounded-md bg-pri text-white text-sm font-medium hover:bg-red-500/80 transition">
            New Post
        </a>
    </div>

    <?php if ($notice): ?>
        <div class="card border-emerald-500/40 bg-emerald-500/10 text-emerald-100 text-sm">
            <?= htmlspecialchars($notice, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="card border-red-500/40 bg-red-500/10 text-red-100 text-sm">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if (empty($posts)): ?>
        <div class="card text-sm text-muted">
            <p>No posts yet. <a href="/admin/posts/create" class="text-acc underline">Create the first post</a>.</p>
        </div>
    <?php else: ?>
        <div class="card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="text-xs uppercase text-muted tracking-wide">
                    <tr>
                        <th class="text-left py-2">Title</th>
                        <th class="text-left py-2">Slug</th>
                        <th class="text-left py-2 hidden md:table-cell">Published</th>
                        <th class="text-left py-2 hidden md:table-cell">Status</th>
                        <th class="text-right py-2">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border/60">
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td class="py-3 font-medium text-acc">
                                <?= htmlspecialchars($post['title'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="py-3 text-muted">
                                <?= htmlspecialchars($post['slug'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="py-3 text-muted hidden md:table-cell">
                                <?= htmlspecialchars($post['published_at'], ENT_QUOTES, 'UTF-8') ?>
                            </td>
                            <td class="py-3 hidden md:table-cell">
                                <?php if ((int)$post['is_published'] === 1): ?>
                                    <span class="inline-flex items-center gap-2 text-emerald-300">
                                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span> Published
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-2 text-muted">
                                        <span class="h-2 w-2 rounded-full bg-yellow-400"></span> Draft
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="/admin/posts/edit/<?= urlencode((string)$post['id']) ?>" class="text-cy hover:underline">Edit</a>
                                    <form method="post" action="/admin/posts/delete/<?= urlencode((string)$post['id']) ?>" onsubmit="return confirm('Delete this post?');">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                                        <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
