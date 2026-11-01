<?php
$script = $script ?? "";
$session = get_session();

$profile = [];
if ($session !== null && isset($session['user_id'])) {
    $profile = fetch(executeQuery(
        "SELECT name, phone, campus, subdistrict, district, preferences
         FROM user_profiles
         WHERE user_id = ? AND deleted_at = FALSE
         ORDER BY profile_id DESC
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
                    <div class="lp-profile-avatar-large" id="modalUserAvatarBox">
                        <span class="lp-avatar-initial"><?= esc($profileInitial) ?></span>
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

    <!-- ================= SHARED HEADER / MODAL SCRIPT ================= -->
    <script>
        function toggleProfileDropdown(event) {
            if (event) event.stopPropagation();
            const dropdown = document.getElementById('lpProfileDropdown');
            const btn = document.getElementById('lpProfileBtn');
            if (!dropdown) return;

            const isShown = dropdown.classList.contains('show');
            dropdown.classList.toggle('show', !isShown);
            if (btn) btn.setAttribute('aria-expanded', isShown ? 'false' : 'true');
        }

        function closeProfileDropdown() {
            const dropdown = document.getElementById('lpProfileDropdown');
            const btn = document.getElementById('lpProfileBtn');
            if (dropdown) dropdown.classList.remove('show');
            if (btn) btn.setAttribute('aria-expanded', 'false');
        }

        document.addEventListener('click', (event) => {
            const dropdown = document.getElementById('lpProfileDropdown');
            const btn = document.getElementById('lpProfileBtn');
            if (dropdown && dropdown.classList.contains('show')) {
                if (!dropdown.contains(event.target) && (!btn || !btn.contains(event.target))) {
                    closeProfileDropdown();
                }
            }
        });

        function openProfileModal() {
            closeProfileDropdown();
            const modal = document.getElementById('lpProfileModal');
            if (modal) modal.classList.add('show');
        }

        function closeProfileModal() {
            const modal = document.getElementById('lpProfileModal');
            if (modal) modal.classList.remove('show');
        }

        function handleProfileModalBackdrop(event) {
            if (event.target === event.currentTarget) {
                closeProfileModal();
            }
        }

        function handleHelpClick(event) {
            if (event) event.preventDefault();
            const faqSection = document.getElementById('faq');
            if (faqSection) {
                faqSection.scrollIntoView({ behavior: 'smooth' });
                const firstFaq = faqSection.querySelector('.lp-faq-item');
                if (firstFaq && !firstFaq.classList.contains('active')) {
                    firstFaq.classList.add('active');
                }
            }
        }

        function handleHeaderSearch(event) {
            if (event.key === 'Enter') {
                const query = event.target.value.trim();
                if (query) {
                    window.location.href = <?= json_encode(route('/barang')) ?> + '?q=' + encodeURIComponent(query);
                }
            }
        }
    </script>

    <?= $script ?>
</body>
</html>