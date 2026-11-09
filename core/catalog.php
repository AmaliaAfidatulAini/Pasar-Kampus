<?php
declare(strict_types=1);

/**
 * Catalog helper functions for PasarKampus
 */

function format_rupiah(int|float $number): string
{
    return 'Rp. ' . number_format($number, 0, ',', '.');
}

function get_catalog_products(): array
{
    static $products = null;
    if ($products !== null) {
        return $products;
    }

    $products = [
        [
            'id' => 1,
            'title' => 'Keyboard Logiac - Silver',
            'category' => 'Elektronik',
            'condition' => 'Layak pakai',
            'condition_spec' => 'Mulus',
            'method' => 'COD',
            'location' => 'Kecamatan Lowokwaru',
            'campus' => 'Area Universitas Brawijaya',
            'tags' => ['#RPL', '#ATK', '#CuciGudang'],
            'original_price' => 150000,
            'price' => 90000,
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
            'gallery' => [
                'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1561154464-82e9adf32764?auto=format&fit=crop&q=80&w=800',
            ],
            'description' => 'Dirancang khusus untuk menunjang produktivitas tinggi, keyboard ini dilengkapi tombol responsif dengan umpan balik (tactile feedback) yang lembut. Pengalaman mengetik menjadi lebih tenang, lancar, dan akurat, sehingga Anda bisa bekerja berjam-jam tanpa terganggu suara bising.',
            'short_desc' => 'Keyboard wireless dengan balutan warna silver elegan dan desain slim yang hemat tempat. ..'
        ],
        [
            'id' => 2,
            'title' => 'Keyboard Logiac - Silver',
            'category' => 'Elektronik',
            'condition' => 'Layak pakai',
            'condition_spec' => 'Mulus',
            'method' => 'COD',
            'location' => 'Kecamatan Lowokwaru',
            'campus' => 'Area Universitas Negeri Malang',
            'tags' => ['#RPL', '#ATK', '#CuciGudang'],
            'original_price' => 150000,
            'price' => 90000,
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
            'gallery' => [
                'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1561154464-82e9adf32764?auto=format&fit=crop&q=80&w=800',
            ],
            'description' => 'Dirancang khusus untuk menunjang produktivitas tinggi, keyboard ini dilengkapi tombol responsif dengan umpan balik (tactile feedback) yang lembut. Pengalaman mengetik menjadi lebih tenang, lancar, dan akurat, sehingga Anda bisa bekerja berjam-jam tanpa terganggu suara bising.',
            'short_desc' => 'Keyboard wireless dengan balutan warna silver elegan dan desain slim yang hemat tempat. ..'
        ],
        [
            'id' => 3,
            'title' => 'Keyboard Logiac - Silver',
            'category' => 'Elektronik',
            'condition' => 'Layak pakai',
            'condition_spec' => 'Mulus',
            'method' => 'COD',
            'location' => 'Kecamatan Lowokwaru',
            'campus' => 'Area Politeknik Negeri Malang',
            'tags' => ['#RPL', '#ATK', '#CuciGudang'],
            'original_price' => 150000,
            'price' => 90000,
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
            'gallery' => [
                'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1561154464-82e9adf32764?auto=format&fit=crop&q=80&w=800',
            ],
            'description' => 'Dirancang khusus untuk menunjang produktivitas tinggi, keyboard ini dilengkapi tombol responsif dengan umpan balik (tactile feedback) yang lembut. Pengalaman mengetik menjadi lebih tenang, lancar, dan akurat, sehingga Anda bisa bekerja berjam-jam tanpa terganggu suara bising.',
            'short_desc' => 'Keyboard wireless dengan balutan warna silver elegan dan desain slim yang hemat tempat. ..'
        ],
        [
            'id' => 4,
            'title' => 'Keyboard Logiac - Silver',
            'category' => 'Elektronik',
            'condition' => 'Layak pakai',
            'condition_spec' => 'Mulus',
            'method' => 'COD',
            'location' => 'Kecamatan Lowokwaru',
            'campus' => 'Area Universitas Terbuka',
            'tags' => ['#RPL', '#ATK', '#CuciGudang'],
            'original_price' => 150000,
            'price' => 90000,
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
            'gallery' => [
                'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1561154464-82e9adf32764?auto=format&fit=crop&q=80&w=800',
            ],
            'description' => 'Dirancang khusus untuk menunjang produktivitas tinggi, keyboard ini dilengkapi tombol responsif dengan umpan balik (tactile feedback) yang lembut. Pengalaman mengetik menjadi lebih tenang, lancar, dan akurat, sehingga Anda bisa bekerja berjam-jam tanpa terganggu suara bising.',
            'short_desc' => 'Keyboard wireless dengan balutan warna silver elegan dan desain slim yang hemat tempat. ..'
        ],
        [
            'id' => 5,
            'title' => 'Keyboard Logiac - Silver',
            'category' => 'Elektronik',
            'condition' => 'Layak pakai',
            'condition_spec' => 'Mulus',
            'method' => 'COD',
            'location' => 'Kecamatan Lowokwaru',
            'campus' => 'Area Universitas Brawijaya',
            'tags' => ['#RPL', '#ATK', '#CuciGudang'],
            'original_price' => 150000,
            'price' => 90000,
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
            'gallery' => [
                'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1561154464-82e9adf32764?auto=format&fit=crop&q=80&w=800',
            ],
            'description' => 'Dirancang khusus untuk menunjang produktivitas tinggi, keyboard ini dilengkapi tombol responsif dengan umpan balik (tactile feedback) yang lembut. Pengalaman mengetik menjadi lebih tenang, lancar, dan akurat, sehingga Anda bisa bekerja berjam-jam tanpa terganggu suara bising.',
            'short_desc' => 'Keyboard wireless dengan balutan warna silver elegan dan desain slim yang hemat tempat. ..'
        ],
        [
            'id' => 6,
            'title' => 'Keyboard Logiac - Silver',
            'category' => 'Elektronik',
            'condition' => 'Layak pakai',
            'condition_spec' => 'Mulus',
            'method' => 'COD',
            'location' => 'Kecamatan Lowokwaru',
            'campus' => 'Area Universitas Negeri Malang',
            'tags' => ['#RPL', '#ATK', '#CuciGudang'],
            'original_price' => 150000,
            'price' => 90000,
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
            'gallery' => [
                'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1561154464-82e9adf32764?auto=format&fit=crop&q=80&w=800',
            ],
            'description' => 'Dirancang khusus untuk menunjang produktivitas tinggi, keyboard ini dilengkapi tombol responsif dengan umpan balik (tactile feedback) yang lembut. Pengalaman mengetik menjadi lebih tenang, lancar, dan akurat, sehingga Anda bisa bekerja berjam-jam tanpa terganggu suara bising.',
            'short_desc' => 'Keyboard wireless dengan balutan warna silver elegan dan desain slim yang hemat tempat. ..'
        ],
        [
            'id' => 7,
            'title' => 'Keyboard Logiac - Silver',
            'category' => 'Elektronik',
            'condition' => 'Layak pakai',
            'condition_spec' => 'Mulus',
            'method' => 'COD',
            'location' => 'Kecamatan Lowokwaru',
            'campus' => 'Area Politeknik Negeri Malang',
            'tags' => ['#RPL', '#ATK', '#CuciGudang'],
            'original_price' => 150000,
            'price' => 90000,
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
            'gallery' => [
                'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1561154464-82e9adf32764?auto=format&fit=crop&q=80&w=800',
            ],
            'description' => 'Dirancang khusus untuk menunjang produktivitas tinggi, keyboard ini dilengkapi tombol responsif dengan umpan balik (tactile feedback) yang lembut. Pengalaman mengetik menjadi lebih tenang, lancar, dan akurat, sehingga Anda bisa bekerja berjam-jam tanpa terganggu suara bising.',
            'short_desc' => 'Keyboard wireless dengan balutan warna silver elegan dan desain slim yang hemat tempat. ..'
        ],
        [
            'id' => 8,
            'title' => 'Keyboard Logiac - Silver',
            'category' => 'Elektronik',
            'condition' => 'Layak pakai',
            'condition_spec' => 'Mulus',
            'method' => 'COD',
            'location' => 'Kecamatan Lowokwaru',
            'campus' => 'Area Universitas Terbuka',
            'tags' => ['#RPL', '#ATK', '#CuciGudang'],
            'original_price' => 150000,
            'price' => 90000,
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
            'gallery' => [
                'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1561154464-82e9adf32764?auto=format&fit=crop&q=80&w=800',
            ],
            'description' => 'Dirancang khusus untuk menunjang produktivitas tinggi, keyboard ini dilengkapi tombol responsif dengan umpan balik (tactile feedback) yang lembut. Pengalaman mengetik menjadi lebih tenang, lancar, dan akurat, sehingga Anda bisa bekerja berjam-jam tanpa terganggu suara bising.',
            'short_desc' => 'Keyboard wireless dengan balutan warna silver elegan dan desain slim yang hemat tempat. ..'
        ],
        [
            'id' => 9,
            'title' => 'Keyboard Logiac - Silver',
            'category' => 'Elektronik',
            'condition' => 'Layak pakai',
            'condition_spec' => 'Mulus',
            'method' => 'COD',
            'location' => 'Kecamatan Lowokwaru',
            'campus' => 'Area Universitas Brawijaya',
            'tags' => ['#RPL', '#ATK', '#CuciGudang'],
            'original_price' => 150000,
            'price' => 90000,
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
            'gallery' => [
                'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1561154464-82e9adf32764?auto=format&fit=crop&q=80&w=800',
            ],
            'description' => 'Dirancang khusus untuk menunjang produktivitas tinggi, keyboard ini dilengkapi tombol responsif dengan umpan balik (tactile feedback) yang lembut. Pengalaman mengetik menjadi lebih tenang, lancar, dan akurat, sehingga Anda bisa bekerja berjam-jam tanpa terganggu suara bising.',
            'short_desc' => 'Keyboard wireless dengan balutan warna silver elegan dan desain slim yang hemat tempat. ..'
        ],
        [
            'id' => 10,
            'title' => 'Keyboard Logiac - Silver',
            'category' => 'Elektronik',
            'condition' => 'Layak pakai',
            'condition_spec' => 'Mulus',
            'method' => 'COD',
            'location' => 'Kecamatan Lowokwaru',
            'campus' => 'Area Universitas Negeri Malang',
            'tags' => ['#RPL', '#ATK', '#CuciGudang'],
            'original_price' => 150000,
            'price' => 90000,
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
            'gallery' => [
                'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1561154464-82e9adf32764?auto=format&fit=crop&q=80&w=800',
            ],
            'description' => 'Dirancang khusus untuk menunjang produktivitas tinggi, keyboard ini dilengkapi tombol responsif dengan umpan balik (tactile feedback) yang lembut. Pengalaman mengetik menjadi lebih tenang, lancar, dan akurat, sehingga Anda bisa bekerja berjam-jam tanpa terganggu suara bising.',
            'short_desc' => 'Keyboard wireless dengan balutan warna silver elegan dan desain slim yang hemat tempat. ..'
        ],
        [
            'id' => 11,
            'title' => 'Keyboard Logiac - Silver',
            'category' => 'Elektronik',
            'condition' => 'Layak pakai',
            'condition_spec' => 'Mulus',
            'method' => 'COD',
            'location' => 'Kecamatan Lowokwaru',
            'campus' => 'Area Politeknik Negeri Malang',
            'tags' => ['#RPL', '#ATK', '#CuciGudang'],
            'original_price' => 150000,
            'price' => 90000,
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
            'gallery' => [
                'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1561154464-82e9adf32764?auto=format&fit=crop&q=80&w=800',
            ],
            'description' => 'Dirancang khusus untuk menunjang produktivitas tinggi, keyboard ini dilengkapi tombol responsif dengan umpan balik (tactile feedback) yang lembut. Pengalaman mengetik menjadi lebih tenang, lancar, dan akurat, sehingga Anda bisa bekerja berjam-jam tanpa terganggu suara bising.',
            'short_desc' => 'Keyboard wireless dengan balutan warna silver elegan dan desain slim yang hemat tempat. ..'
        ],
        [
            'id' => 12,
            'title' => 'Keyboard Logiac - Silver',
            'category' => 'Elektronik',
            'condition' => 'Layak pakai',
            'condition_spec' => 'Mulus',
            'method' => 'COD',
            'location' => 'Kecamatan Lowokwaru',
            'campus' => 'Area Universitas Terbuka',
            'tags' => ['#RPL', '#ATK', '#CuciGudang'],
            'original_price' => 150000,
            'price' => 90000,
            'image' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
            'gallery' => [
                'https://images.unsplash.com/photo-1587829741301-dc798b83add3?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1595225476474-87563907a212?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1511467687858-23d96c32e4ae?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1618384887929-16ec33fab9ef?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1561154464-82e9adf32764?auto=format&fit=crop&q=80&w=800',
            ],
            'description' => 'Dirancang khusus untuk menunjang produktivitas tinggi, keyboard ini dilengkapi tombol responsif dengan umpan balik (tactile feedback) yang lembut. Pengalaman mengetik menjadi lebih tenang, lancar, dan akurat, sehingga Anda bisa bekerja berjam-jam tanpa terganggu suara bising.',
            'short_desc' => 'Keyboard wireless dengan balutan warna silver elegan dan desain slim yang hemat tempat. ..'
        ],
        // Additional items for rich category/tag/location filtering
        [
            'id' => 13,
            'title' => 'Buku Kalkulus Purcell Edisi 9',
            'category' => 'Buku & Akademik',
            'condition' => 'Layak pakai',
            'condition_spec' => '95% Mulus',
            'method' => 'COD',
            'location' => 'Kecamatan Lowokwaru',
            'campus' => 'Area Universitas Brawijaya',
            'tags' => ['#ModulKuliah', '#ATK'],
            'original_price' => 180000,
            'price' => 85000,
            'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&q=80&w=800',
            'gallery' => [
                'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1497633762265-9d179a990aa6?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1457369804613-52c61a468e7d?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1532012164546-f432f2e3edd4?auto=format&fit=crop&q=80&w=800',
            ],
            'description' => 'Buku teks wajib kuliah matematika dan teknik, halaman lengkap dan bersih.',
            'short_desc' => 'Buku kalkulus Purcell edisi 9 lengkap tanpa robek.'
        ],
        [
            'id' => 14,
            'title' => 'Jas Almamater Kampus Bahan Drill',
            'category' => 'Fashion & Apparel',
            'condition' => 'Layak pakai',
            'condition_spec' => 'Mulus',
            'method' => 'COD',
            'location' => 'Kecamatan Lowokwaru',
            'campus' => 'Area Universitas Negeri Malang',
            'tags' => ['#JasAlmamater'],
            'original_price' => 200000,
            'price' => 95000,
            'image' => 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&q=80&w=800',
            'gallery' => [
                'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1593030761757-71fae45fa0e7?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1507679799987-c73779587ccf?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1617127365659-c47fa864d8bc?auto=format&fit=crop&q=80&w=800',
            ],
            'description' => 'Jas almamater ukuran L bersih rapi, baru dipakai 2 kali.',
            'short_desc' => 'Jas almamater ukuran L bersih rapi siap pakai.'
        ],
        [
            'id' => 15,
            'title' => 'Meja Belajar Lipat Kayu Minimalis',
            'category' => 'Perlengkapan Kos',
            'condition' => 'Layak pakai',
            'condition_spec' => 'Normal',
            'method' => 'COD',
            'location' => 'Kecamatan Lowokwaru',
            'campus' => 'Area Politeknik Negeri Malang',
            'tags' => ['#MejaBelajar'],
            'original_price' => 120000,
            'price' => 60000,
            'image' => 'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?auto=format&fit=crop&q=80&w=800',
            'gallery' => [
                'https://images.unsplash.com/photo-1518455027359-f3f8164ba6bd?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1533090161767-e6ffed986c88?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1515378791036-0648a3ef77b2?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&q=80&w=800',
            ],
            'description' => 'Meja lipat kos praktis mudah dipindahkan dan disimpan.',
            'short_desc' => 'Meja lipat kayu minimalis kokoh untuk laptop dan buku.'
        ],
        [
            'id' => 16,
            'title' => 'Jaket Hoodie Unisex Fleece Tebal',
            'category' => 'Fashion & Apparel',
            'condition' => 'Layak pakai',
            'condition_spec' => 'Mulus',
            'method' => 'COD',
            'location' => 'Kecamatan Lowokwaru',
            'campus' => 'Area Universitas Terbuka',
            'tags' => ['#Jaket'],
            'original_price' => 175000,
            'price' => 80000,
            'image' => 'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?auto=format&fit=crop&q=80&w=800',
            'gallery' => [
                'https://images.unsplash.com/photo-1556905055-8f358a7a47b2?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1509967419530-da38b4704bc6?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1578632767115-351597cf2477?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1543163521-1bf539c55dd2?auto=format&fit=crop&q=80&w=800',
                'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?auto=format&fit=crop&q=80&w=800',
            ],
            'description' => 'Hoodie fleece tebal hangat dan nyaman untuk kuliah harian.',
            'short_desc' => 'Hoodie katun tebal hangat warna abu netral.'
        ]
    ];

    return $products;
}

function get_catalog_product_by_id(int|string $id): array
{
    $products = get_catalog_products();
    foreach ($products as $p) {
        if ((int)$p['id'] === (int)$id) {
            return $p;
        }
    }
    return $products[0];
}
