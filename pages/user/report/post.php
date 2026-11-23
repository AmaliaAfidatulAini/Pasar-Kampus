<?php


if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'submit-report') {
    $userId = $session['user_id'] ?? null;
    if ($userId === null) {
        $guestUser = fetch(executeQuery("SELECT user_id FROM users WHERE role = 'user' ORDER BY user_id ASC LIMIT 1"));
        $userId = $guestUser['user_id'] ?? 2;
    }

    $rawProductId = (int)($_POST['product_id'] ?? 1);
    $productRow = fetch(executeQuery("SELECT product_id FROM products WHERE product_id = ?", [$rawProductId]));
    $validProductId = $productRow ? (int)$productRow['product_id'] : (int)(fetch(executeQuery("SELECT product_id FROM products ORDER BY product_id ASC LIMIT 1"))['product_id'] ?? 1);

    $jenis = trim((string)($_POST['jenis'] ?? 'Barang'));
    $kategori = trim((string)($_POST['kategori'] ?? 'Lainnya'));
    $deskripsi = trim((string)($_POST['deskripsi'] ?? ''));

    if ($kategori === '') {
        $kategori = 'Lainnya';
    }

    $reportType = $kategori;
    if ($jenis === 'Pengguna') {
        $reportType = 'Pengguna: ' . $kategori;
    }

    $stmt = executeQuery(
        "INSERT INTO reports (user_id, product_id, report_type, description, review_status, decision, created_at, updated_at)
         VALUES (?, ?, ?, ?, 'Menunggu', NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
         RETURNING report_id",
        [$userId, $validProductId, $reportType, $deskripsi]
    );
    $newReport = fetch($stmt);
    $reportId = (int)($newReport['report_id'] ?? 0);

    // Process file attachments if provided
    if ($reportId > 0 && isset($_FILES['lampiran'])) {
        $files = $_FILES['lampiran'];
        $uploadTarget = 'reports';
        $uploadDir = dirname(__DIR__, 2) . '/public/uploads/' . $uploadTarget;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $names = is_array($files['name']) ? $files['name'] : [$files['name']];
        $tmpNames = is_array($files['tmp_name']) ? $files['tmp_name'] : [$files['tmp_name']];
        $errors = is_array($files['error']) ? $files['error'] : [$files['error']];

        foreach ($names as $idx => $origName) {
            if (($errors[$idx] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_OK && is_uploaded_file($tmpNames[$idx])) {
                $ext = pathinfo((string)$origName, PATHINFO_EXTENSION);
                $cleanName = 'report_' . $reportId . '_' . bin2hex(random_bytes(6)) . ($ext ? '.' . strtolower($ext) : '.jpg');
                $destination = $uploadDir . '/' . $cleanName;
                if (move_uploaded_file($tmpNames[$idx], $destination)) {
                    $storedPath = upload($uploadTarget . '/' . $cleanName);
                    executeQuery(
                        "INSERT INTO attachments (report_id, product_id, attachment_type, file_path, file_name, created_at, updated_at)
                         VALUES (?, ?, 1, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)",
                        [$reportId, $validProductId, $storedPath, (string)$origName]
                    );
                }
            }
        }
    }

    if (str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') || ($_POST['ajax'] ?? '') === '1') {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => true,
            'message' => 'Aduan berhasil dikirim! Laporan Anda telah masuk ke dashboard admin untuk ditinjau.',
            'report_id' => $reportId
        ]);
        exit;
    }

    flash('success', 'Aduan berhasil dikirim ke Admin untuk ditinjau.');
    redirect('/barang?id=' . $rawProductId);
    exit;
}