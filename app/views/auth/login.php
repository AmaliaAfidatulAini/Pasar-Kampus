<?php view('header', ['title' => 'Masuk']); ?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Masuk</h2>
        <p class="auth-subtitle">Masuk ke akun Pasar Kampus Anda</p>

        <?php if (isset($errors)): ?>
            <div class="form-errors" role="alert">
                <?php foreach ($errors as $error): ?>
                    <p><?= esc($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= route('/login') ?>" method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email / NIM</label>
            <input type="email" id="email" name="email" required value="<?= esc($email ?? '') ?>" placeholder="email@student.ac.id" class="form-control">
            </div>

            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <input type="password" id="password" name="password" required placeholder="••••••••" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Masuk</button>
        </form>

        <p class="auth-footer">
            Belum punya akun? <a href="<?= route('/register') ?>">Daftar sekarang</a>
        </p>
    </div>
</div>

<?php view('footer'); ?>
