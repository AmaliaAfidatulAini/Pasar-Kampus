<?php
$editing = isset($post);

$input = $input ?? [];
$photoUrls = $post['photos'] ?? '';
if (is_string($photoUrls) && $photoUrls !== '')
    $photoUrls = json_decode($photoUrls, true) ?: [];
if (is_array($photoUrls)) {
    $photoUrls = implode("\n", $photoUrls);
}
view('header', ['title' => $editing ? 'Edit post' : 'New post']);
?>

<main class="page-content narrow-content">
    <p class="intro-kicker">Posts</p>
    <h1><?= $editing ? 'Edit post' : 'New post' ?></h1>

    <?php if (!empty($errors)): ?>
        <div class="form-errors" role="alert">
            <?php foreach ($errors as $error): ?>
                <p><?= sanitize($error) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form class="data-panel post-form" method="POST" enctype="multipart/form-data" action="<?= route($editing ? '/posts/' . $post['id'] : '/posts') ?>">
        <label>Title <input class="form-control" name="title" required value="<?= esc($post['title'] ?? $input['title'] ?? '') ?>"></label>
        <label>Content <textarea class="form-control" name="content" rows="8" required><?= esc($post['content'] ?? $input['content'] ?? '') ?></textarea></label>
        <label>Photos <span class="field-help">Select multiple JPG, PNG, GIF, or WebP files to replace existing photos.</span>
            <input class="form-control" type="file" name="photos[]" accept="image/jpeg,image/png,image/gif,image/webp" multiple>
        </label>
        <div class="form-actions">
            <a href="<?= route('/posts') ?>">Cancel</a>
            <button class="btn btn-primary" type="submit"><?= $editing ? 'Save changes' : 'Create post' ?></button>
        </div>
    </form>
</main>

<?php view('footer'); ?>
