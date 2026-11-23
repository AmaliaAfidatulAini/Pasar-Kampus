<?php

declare(strict_types=1);

// Same-origin proxy for Nominatim: the page CSP only allows `connect-src 'self'`.
if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'GET') {
    response_json(['error' => 'Metode tidak diizinkan.'], 405);
}

if (!is_array(get_session())) {
    response_json(['error' => 'Sesi berakhir. Muat ulang halaman.'], 401);
}

$query = is_string($_GET['q'] ?? null) ? trim($_GET['q']) : '';
if ($query === '') {
    response_json(['error' => 'Kata kunci alamat kosong.'], 400);
}

$url = 'https://nominatim.openstreetmap.org/search?' . http_build_query([
    'format' => 'json',
    'limit' => 1,
    'q' => $query,
]);

$curl = curl_init($url);
curl_setopt_array($curl, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 6,
    CURLOPT_USERAGENT => 'PasarKampus/1.0 (support@pasarkampus.id)',
]);
$body = curl_exec($curl);
$status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
curl_close($curl);

$rows = is_string($body) ? json_decode($body, true) : null;
if ($status !== 200 || !is_array($rows) || !isset($rows[0]['lat'], $rows[0]['lon'])) {
    response_json(['error' => 'Alamat tidak ditemukan.'], 502);
}

response_json([
    'lat' => (float) $rows[0]['lat'],
    'lng' => (float) $rows[0]['lon'],
    'label' => (string) ($rows[0]['display_name'] ?? $query),
]);
