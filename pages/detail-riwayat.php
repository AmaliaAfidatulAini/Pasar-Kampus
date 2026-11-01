<?php
$id = $_GET['id'] ?? '';
redirect('/riwayat.php' . ($id !== '' ? '?id=' . urlencode((string)$id) : ''), 302);
