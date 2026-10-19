<?php
$title = $title ?? '';
$session = get_session();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pasar Kampus | <?= esc($title) ?></title>
    <style>
        :root { font-family: Arial, sans-serif; color: #222; background: #f5f5f5; }
        * { box-sizing: border-box; }
        body { margin: 0; }
        header { background: #1f2937; color: #fff; }
        nav { display: flex; align-items: center; justify-content: space-between; max-width: 960px; margin: auto; padding: 1rem; }
        nav strong a {color: #fff; text-decoration: none;}
        nav div a { color: #fff; text-decoration: none; margin-left: 1rem; }
        nav div a:hover { text-decoration: underline; }
        main { max-width: 960px; min-height: calc(100vh - 6.25rem); margin: auto; padding: 2rem 1rem; background: #fff; }
    </style>
</head>
<body>
<header>
    <nav>
        <strong>
            <a href="<?= route('/') ?>">
                Pasar Kampus
            </a>
        </strong>
        <div>
            <a href="<?= route('/barang') ?>">Barang</a>
            <a href="<?= route('/laporan') ?>">Laporan</a>
            <?php if ($session): ?>
                <a href="<?= route('/logout') ?>">Logout</a>
            <?php else: ?>
                <a href="<?= route('/login') ?>">Login</a>
            <?php endif; ?>
        </div>
    </nav>
</header>
<main>