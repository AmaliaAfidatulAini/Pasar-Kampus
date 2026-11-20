<?php
// COMPONENTS PROPS //
$script = $script ?? "";
// END OF COMPONENTS PROPS //
$session = get_session();

$profile = [];
if ($session !== null && isset($session['user_id'])) {
    $profile = fetch(executeQuery(
        "SELECT name, phone, campus, subdistrict, district,
                COALESCE((
                    SELECT string_agg(c.category_name, ', ' ORDER BY c.category_name)
                    FROM user_preferences up
                    JOIN categories c ON c.categories_id = up.category_id
                    WHERE up.profile_id = p.profile_id
                ), '') AS preferences
         FROM user_profiles p
         WHERE p.user_id = ?
         ORDER BY p.profile_id DESC
         LIMIT 1",
        [$session['user_id']]
    )) ?: [];
}

$profileName = trim((string) ($profile['name'] ?? $session['name'] ?? '')) ?: 'Pengguna';
$profileInitial = strtoupper(substr($profileName, 0, 1));
$profileCampus = trim((string) ($profile['campus'] ?? ''));
$profilePhone = trim((string) ($profile['phone'] ?? ''));
$profilePreferences = trim((string) ($profile['preferences'] ?? ''));
$profileLocation = implode(', ', array_filter(
    [trim((string) ($profile['subdistrict'] ?? '')), trim((string) ($profile['district'] ?? ''))],
    static fn (string $part): bool => $part !== ''
));
$profileValue = static fn (string $value, string $fallback = '—'): string => $value !== '' ? esc($value) : $fallback;
?>

   
   <!-- ================= 7. FOOTER ================= -->
    <footer class="lp-footer">
        <div class="lp-container">
            <div class="lp-footer-grid">
                <!-- Col 1: Brand & Copyright -->
                <div class="lp-footer-col">
                    <a href="<?= route('/') ?>" class="lp-logo" style="color: #FFFFFF;">
                        PasarKampus
                    </a>
                    <p>
                        Platform marketplace eksklusif mahasiswa untuk sirkulasi barang perkuliahan yang aman, hemat, dan berkelanjutan.
                    </p>
                </div>

                <!-- Col 2: PasarKampus -->
                <div class="lp-footer-col">
                    <h4>PasarKampus</h4>
                    <ul class="lp-footer-links">
                        <li><a href="#tentang-kami">Tentang Kami</a></li>
                        <li><a href="<?= route('/barang') ?>">Katalog Barang</a></li>
                        <li><a href="<?= route('/register') ?>">Daftar Akun Mahasiswa</a></li>
                        <li><a href="<?= route('/barang') ?>">Dashboard Penjual</a></li>
                    </ul>
                </div>

                <!-- Col 3: Bantuan -->
                <div class="lp-footer-col">
                    <h4>Bantuan</h4>
                    <ul class="lp-footer-links">
                        <li><a href="#faq">Pusat Bantuan</a></li>
                        <li><a href="#faq">FAQ</a></li>
                        <li><a href="mailto:support@pasarkampus.id">Kontak Kami</a></li>
                    </ul>
                </div>

                <!-- Col 4: Dukungan -->
                <div class="lp-footer-col">
                    <h4>Dukungan</h4>
                    <ul class="lp-footer-links">
                        <li><a href="#" onclick="alert('Kebijakan Privasi: Data kampus dan identitas mahasiswa dilindungi.'); return false;">Kebijakan</a></li>
                        <li><a href="#" onclick="alert('Panduan COD: Selalu lakukan serah terima di area kampus yang ramai pada jam operasional kampus.'); return false;">Panduan Keamanan COD</a></li>
                        <li><a href="#" onclick="alert('Syarat & Ketentuan PasarKampus: Hanya untuk barang legal & tidak melanggar hukum.'); return false;">Syarat & Ketentuan</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Copyright Bar -->
            <div class="lp-footer-bottom">
                <div>
                    &copy; 2026 PasarKampus. Hak Cipta Dilindungi.
                </div>
                <div>
                    Dibuat untuk Project Based Learning (PBL) Mahasiswa.
                </div>
            </div>
        </div>
    </footer>

    <?php if ($session): ?>
        <!-- ================= PROFIL PENGGUNA MODAL (Wireframe 2 Dropdown Item) ================= -->
        <div id="lpProfileModal" class="lp-modal-backdrop" onclick="handleProfileModalBackdrop(event)">
            <div class="lp-auth-modal-card lp-profile-modal-card">
                <button type="button" class="lp-modal-close" onclick="closeProfileModal()">&times;</button>
                <div class="lp-profile-modal-header">
                    <div class="lp-profile-avatar-large" id="modalUserAvatarBox" style="<?= !empty($profile['avatar_url']) ? 'padding:0; overflow:hidden;' : '' ?>">
                        <?php if (!empty($profile['avatar_url'])): ?>
                            <img src="<?= esc($profile['avatar_url']) ?>" alt="Avatar" style="width:100%; height:100%; border-radius:50%; object-fit:cover;">
                        <?php else: ?>
                            <span class="lp-avatar-initial"><?= esc($profileInitial) ?></span>
                        <?php endif; ?>
                    </div>
                    <h3 id="modalUserName" class="lp-profile-name"><?= esc($profileName) ?></h3>
                    <span id="modalUserCampus" class="lp-profile-campus"><?= $profileValue($profileCampus, 'Kampus belum diisi') ?></span>
                </div>
                <div class="lp-profile-modal-body">
                    <div class="lp-profile-detail-row">
                        <span class="lp-detail-label">Email / Akun</span>
                        <span id="modalUserEmail" class="lp-detail-value"><?= esc((string) ($session['email'] ?? '')) ?></span>
                    </div>
                    <div class="lp-profile-detail-row">
                        <span class="lp-detail-label">Nomor WhatsApp</span>
                        <span id="modalUserTelp" class="lp-detail-value"><?= $profileValue($profilePhone) ?></span>
                    </div>
                    <div class="lp-profile-detail-row">
                        <span class="lp-detail-label">Kecamatan Domisili</span>
                        <span id="modalUserKec" class="lp-detail-value"><?= $profileLocation !== '' ? esc($profileLocation) : '—' ?></span>
                    </div>
                    <div class="lp-profile-detail-row">
                        <span class="lp-detail-label">Minat Barang</span>
                        <span id="modalUserPrefs" class="lp-detail-value"><?= $profileValue($profilePreferences) ?></span>
                    </div>
                </div>
                <div class="lp-profile-modal-footer">
                    <a href="<?= route('/barang') ?>" class="lp-btn lp-btn-outline" style="font-size: 0.85rem;">📦 Daftar Barang Saya</a>
                    <a href="<?= route('/barang') ?>" class="lp-btn lp-btn-primary" style="font-size: 0.85rem;">Buka Katalog &rarr;</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/lucide@1.72.0/dist/umd/lucide.min.js"></script>
    <script src="<?= asset("js/main.js") ?>"></script>

    <?= $script ?>
</body>
</html>