<?php view('header', ['title' => 'Posts']); ?>

<main class="page-content">
    <div class="page-heading">
        <div>
            <p class="intro-kicker">Community posts</p>
            <h1>Posts</h1>
        </div>
        <a class="btn btn-primary" href="<?= route('/posts/create') ?>">New post</a>
    </div>

    <section class="data-panel">
        <?php if (isset($posts)): ?>
            <?php if (!$posts): ?>
                <p class="empty-state">No posts yet.</p>
            <?php else: ?>
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Author</th>
                                <th>Created</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($posts as $post): ?>
                            <tr>
                                <td>
                                    <strong><?= esc($post['title']) ?></strong>
                                    <div class="table-subtext"><?= esc($post['content']) ?></div>
                                </td>
                                <td><?= esc($post['author_name']) ?></td>
                                <td><?= esc($post['created_at']) ?></td>
                                <td class="actions">
                                    <?php if ((int) ($post['user_id'] ?? 0) === (int) (session()->id ?? 0)): ?>
                                        <a href="<?= route("/posts/{$post['id']}/edit") ?>">Edit</a>
                                        <form method="POST" action="<?= route("/posts/{$post['id']}/delete") ?>" data-confirm="Delete this post?">
                                            <button class="link-button" type="submit">Delete</button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>
</main>

<?php view('footer'); ?>
