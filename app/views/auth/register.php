<?php view('header', ['title' => 'Daftar']); ?>

<div class="auth-container">
    <div class="auth-card">
        <h2>Daftar Akun</h2>
        <p class="auth-subtitle">Buat akun baru untuk mulai bertransaksi</p>

        <?php if (isset($errors)): ?>
            <div class="form-errors" role="alert">
                <?php foreach ($errors as $error): ?>
                    <p><?= esc($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?= route('/register') ?>" method="POST" class="auth-form">
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
            <input type="text" id="name" name="name" required value="<?= esc($name ?? '') ?>" placeholder="Nama Anda" class="form-control">
            </div>

            <div class="form-group">
                <label for="email">Email Student</label>
            <input type="email" id="email" name="email" required value="<?= esc($email ?? '') ?>" placeholder="email@student.ac.id" class="form-control">
            </div>

            <div class="form-group">
                <label for="password">Kata Sandi</label>
                <input type="password" id="password" name="password" required placeholder="••••••••" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Daftar</button>
        </form>

        <p class="auth-footer">
            Sudah punya akun? <a href="<?= route('/login') ?>">Masuk di sini</a>
        </p>
    </div>
</div>

<?php view('footer'); ?>
