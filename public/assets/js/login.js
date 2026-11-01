/**
 * Pasar Kampus - Login & Authentication Simulation
 */

function handleLogin(event) {
    if (event) event.preventDefault();
    const emailInput = document.getElementById('loginEmail');
    const passwordInput = document.getElementById('loginPassword');
    const identifier = emailInput ? emailInput.value.trim() : '';
    const password = passwordInput ? passwordInput.value : '';

    if (!identifier || !password) {
        alert('Harap isi No. Handphone / Username / Email dan password!');
        return;
    }

    // If user has not registered yet, seed a default demo account
    if (!localStorage.getItem('preloved_user')) {
        localStorage.setItem('preloved_user', JSON.stringify({
            email: identifier.includes('@') ? identifier : `${identifier}@ui.ac.id`,
            nama: 'Muhammad Farhan',
            telepon: '081234567890',
            foto: 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=200',
            kecamatan: 'Lowokwaru',
            kampus: 'Universitas Brawijaya (UB)',
            preferensi: ['Buku', 'Elektronik', 'ATK', 'Perlengkapan Kost']
        }));
    }

    // Set logged-in state to true
    localStorage.setItem('pk_logged_in', 'true');

    // Redirect to index.html where Wireframe 2 Header will be shown
    window.location.href = 'index.php';
}

function toggleLoginPagePwd(btn) {
    const input = document.getElementById('loginPassword');
    const eyeIcon = document.getElementById('loginEyeIcon') || (btn ? btn.querySelector('img') : null);
    if (input) {
        if (input.type === 'password') {
            input.type = 'text';
            if (eyeIcon) {
                eyeIcon.src = 'images/eye-open.png';
                eyeIcon.alt = 'Sembunyikan password';
            }
        } else {
            input.type = 'password';
            if (eyeIcon) {
                eyeIcon.src = 'images/eye-closed.png';
                eyeIcon.alt = 'Lihat password';
            }
        }
    }
}
