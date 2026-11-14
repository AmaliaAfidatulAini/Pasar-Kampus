<?php
role_include('admin');

$stats = executeQuery(
    "SELECT 
        (SELECT COUNT(*) FROM products) AS products, 
        (SELECT COUNT(*) FROM categories) AS categories,
        (SELECT COUNT(*) FROM reports WHERE review_status = 'Menunggu') AS reports, 
        (SELECT COUNT(*) FROM users WHERE account_status = 'Aktif') AS users
",
    [],
    'fetch'
);

$stats = array_map('intval', $stats);
$campuses = executeQuery(
    "SELECT 
        COALESCE(p.campus, 'Belum diisi') AS campus, 
        COUNT(*) AS total 
    FROM users u 
    LEFT JOIN user_profiles p ON
        p.user_id = u.user_id
    WHERE 
        u.deleted_at IS FALSE AND u.role = 'user' 
    GROUP BY p.campus 
    ORDER BY total DESC
",
    [],
    'fetchAll'
);
$campusColors = ['#1D356D', '#2563EB', '#0F766E', '#D97706', '#7C3AED', '#B91C1C', '#475569'];
$campusChartColors = array_map(static fn(int $index): string => $campusColors[$index % count($campusColors)], array_keys($campuses));
$campusTotal = array_sum(array_map(static fn(array $campus): int => (int) $campus['total'], $campuses));

$activity = executeQuery(
    "SELECT * FROM (
        SELECT 
            'Barang ditambahkan' AS label, 
            p.item_name AS detail, 
            p.created_at AS happened_at 
        FROM products p 
        UNION ALL 
        SELECT 
            'Pengajuan transaksi', p.item_name, r.created_at 
        FROM requests r 
        JOIN products p ON 
            p.product_id = r.product_id 
        UNION ALL 
        SELECT 
            'Laporan masuk', rp.report_type, rp.created_at 
        FROM reports rp 
    ) events ORDER BY happened_at DESC NULLS LAST LIMIT 8
",
    [],
    'fetchAll'
);
?>

<?php
Component::include('admin/header', [
    'activeTab' => "dashboard",
    'title' => "Dashboard",
    'subtitle' => "Ringkasan data katalog, transaksi, laporan, dan akun dari basis data."
]);
?>
<section id="view-dashboard" class="admin-view-panel active">
    <div class="admin-stats-grid">
        <div class="admin-stat-card"><span class="admin-stat-title">Barang di katalog</span><strong class="admin-stat-value"><?= esc((string) $stats['products']) ?></strong></div>
        <div class="admin-stat-card"><span class="admin-stat-title">Kategori</span><strong class="admin-stat-value"><?= esc((string) $stats['categories']) ?></strong></div>
        <div class="admin-stat-card"><span class="admin-stat-title">Laporan menunggu</span><strong class="admin-stat-value"><?= esc((string) $stats['reports']) ?></strong></div>
        <div class="admin-stat-card"><span class="admin-stat-title">Pengguna aktif</span><strong class="admin-stat-value"><?= esc((string) $stats['users']) ?></strong></div>
    </div>
    <div class="admin-analytics-row">
        <section class="admin-card" aria-labelledby="adminCampusTitle">
            <div class="admin-card-header">
                <div>
                    <h2 id="adminCampusTitle" class="admin-card-title">Analitik Pengguna</h2>
                    <p class="admin-card-subtitle">Data sebaran pengguna berdasarkan kampus</p>
                </div>
            </div>
            <?php if ($campuses === []): ?>
                <p class="admin-data-state">Belum ada pengguna terdaftar untuk ditampilkan.</p>
            <?php else: ?>
                <div class="admin-campus-chart">
                    <div class="admin-campus-chart-canvas-wrap">
                        <canvas id="adminCampusChart" role="img" aria-label="Diagram lingkaran sebaran pengguna berdasarkan kampus">
                            Sebaran pengguna berdasarkan kampus.
                        </canvas>
                    </div>
                    <ul class="admin-campus-legend" aria-label="Jumlah pengguna berdasarkan kampus">
                        <?php foreach ($campuses as $index => $campus): ?>
                            <?php $count = (int) $campus['total']; ?>
                            <li class="admin-campus-legend-item">
                                <span class="admin-campus-legend-name"><span class="admin-campus-legend-swatch" style="--campus-color: <?= esc($campusChartColors[$index]) ?>" aria-hidden="true"></span><?= esc((string) $campus['campus']) ?></span>
                                <strong><?= esc((string) $count) ?> <span class="admin-campus-legend-percent"><?= esc((string) round($count / $campusTotal * 100)) ?>%</span></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </section>
        <section class="admin-card" aria-labelledby="adminActivityTitle">
            <div class="admin-card-header">
                <div>
                    <h2 id="adminActivityTitle" class="admin-card-title">Aktivitas terbaru</h2>
                    <p class="admin-card-subtitle">Barang, pengajuan, dan laporan terbaru.</p>
                </div>
            </div>
            <div class="admin-activity-list">
                <?php if ($activity === []): ?><p>Belum ada aktivitas untuk ditampilkan.</p><?php else: ?>
                    <?php foreach ($activity as $event): ?><p><strong><?= esc((string) $event['label']) ?></strong>: <?= esc((string) $event['detail']) ?> <time><?= esc((string) $event['happened_at']) ?></time></p><?php endforeach; ?>
                <?php endif; ?>
            </div>
        </section>
    </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.6.1/chart.umd.min.js"></script>
<?php if ($campuses !== []): ?>
    <script>
        const campusChart = document.getElementById('adminCampusChart');
        if (campusChart) {
            new Chart(campusChart, {
                type: 'pie',
                data: {
                    labels: <?= json_encode(array_map(static fn(array $campus): string => (string) $campus['campus'], $campuses), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) ?>,
                    datasets: [{
                        data: <?= json_encode(array_map(static fn(array $campus): int => (int) $campus['total'], $campuses)) ?>,
                        backgroundColor: <?= json_encode($campusChartColors) ?>,
                        borderColor: '#FFFFFF',
                        borderWidth: 2,
                        hoverOffset: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label(context) {
                                    const total = context.dataset.data.reduce((sum, count) => sum + count, 0);
                                    const percent = Math.round(context.raw / total * 100);
                                    return ` ${context.label}: ${context.raw} (${percent}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
<?php endif; ?>


<?php Component::include('admin/footer'); ?>