<?php view('header', ['title' => 'Home']); ?>

<main class="landing">
    <p class="intro-kicker">A small campus marketplace</p>
    <h1>Give useful things<br><em>a second life.</em></h1>
    <p class="intro-copy">A simple test landing page for sharing, finding, and managing pre-loved items around campus.</p>
    <div class="landing-actions"><a class="btn btn-primary" href="<?= route('/register') ?>">Create an account</a><a href="<?= route('/login') ?>">Already a member? Log in</a></div>
    <div class="landing-grid">
        <div><strong>01</strong><span>Post items</span></div>
        <div><strong>02</strong><span>Meet your campus</span></div>
        <div><strong>03</strong><span>Keep it circular</span></div>
    </div>
</main>

<section class="posts-container">
    <?php if (isset($posts)): ?>
        <?php foreach ($posts as $post): ?>
            <article class="post-card">
                <div class="post-image-wrapper">
                    <img src="<?= asset("/uploads/" . $post["photos"][0], true) ?>" alt="Post Image" class="post-image">
                </div>
                <div class="post-content">
                    <h3 class="post-title"><?= $post['title'] ?></h3>
                    <p class="post-body"><?= $post['content'] ?></p>
                    <div class="post-meta">
                        <span class="post-date"><?= (new DateTime($post['created_at']))->format('M j, Y'); ?></span>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php view('footer'); ?>