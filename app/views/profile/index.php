<?php view('header', ['title' => 'Profile']); $errors = $errors ?? []; ?>
<main class="page-content narrow-content">
    <p class="intro-kicker">Account</p><h1>Your profile</h1>
    <?php if ($errors): ?><div class="form-errors" role="alert"><?php foreach ($errors as $error): ?><p><?= esc($error) ?></p><?php endforeach; ?></div><?php endif; ?>
    <section class="profile-card data-panel">
        <?php if ($user['photo_profile']): ?><img class="profile-picture" src="<?= route('/uploads/' . $user['photo_profile']) ?>" alt="Profile picture"><?php else: ?><div class="profile-picture profile-placeholder"><?= esc(strtoupper($user['name'][0])) ?></div><?php endif; ?>
        <p><strong><?= esc($user['name']) ?></strong><br><?= esc($user['email']) ?></p>
    </section>
    <form class="data-panel post-form" method="POST" enctype="multipart/form-data" action="<?= route('/profile') ?>">
        <h2>Update profile</h2><label>Name <input class="form-control" name="name" required value="<?= esc($user['name']) ?>"></label><label>Email <input class="form-control" type="email" name="email" required value="<?= esc($user['email']) ?>"></label><label>Profile picture <input class="form-control" type="file" name="photo_profile" accept="image/jpeg,image/png,image/gif,image/webp"></label><button class="btn btn-primary" type="submit">Save profile</button>
    </form>
    <form class="data-panel post-form" method="POST" action="<?= route('/profile/password') ?>">
        <h2>Change password</h2><label>Current password <input class="form-control" type="password" name="current_password" required></label><label>New password <input class="form-control" type="password" name="password" required></label><label>Confirm new password <input class="form-control" type="password" name="password_confirmation" required></label><button class="btn btn-primary" type="submit">Update password</button>
    </form>
</main>
<?php view('footer'); ?>
