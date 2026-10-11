<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Pasar Kampus') ?></title>
    <?= asset('assets/css/app.css') ?>
</head>
<body>
<div class="site-shell">
<?php if (session()): ?>
    <header class="topbar">
        <a class="brand" href="<?= route('/') ?>"><span class="brand-mark">PK</span> Pasar Kampus</a>
        <nav class="topbar-actions"><a href="<?= route('/profile') ?>">Profile</a><form method="POST" action="<?= route('/logout') ?>"><button class="link-button" type="submit">Keluar</button></form></nav>
    </header>
<?php endif; ?>
