<?php
$session = session_include('/login');
$userId = (int) ($session['user_id'] ?? 0);

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    redirect('/profile', 303);
}

try {
    $oldPassword = $_POST['old_password'] ?? null;
    $newPassword = $_POST['new_password'] ?? null;
    if (!is_string($oldPassword) || !is_string($newPassword) || strlen($newPassword) < 8) {
        throw new InvalidArgumentException('Kata sandi baru minimal 8 karakter.');
    }

    $user = fetch(executeQuery('SELECT password FROM users WHERE user_id = ?', [$userId]));
    if (!$user || !password_verify($oldPassword, $user['password'])) {
        throw new InvalidArgumentException('Kata sandi lama tidak sesuai.');
    }

    executeQuery(
        'UPDATE users SET password = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?',
        [password_hash($newPassword, PASSWORD_DEFAULT), $userId]
    );
    flash('success', 'Kata sandi berhasil diperbarui.');
} catch (InvalidArgumentException $error) {
    flash('error', $error->getMessage());
} catch (Throwable $error) {
    error_log($error->getMessage());
    flash('error', 'Kata sandi tidak dapat diperbarui.');
}

redirect('/profile', 303);
