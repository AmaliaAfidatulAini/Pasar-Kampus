/**
 * Pasar Kampus - Registration Wizard (3 Steps)
 * Matches UI Screenshots:
 * - Step 1: Daftar akun (Email, Password)
 * - Step 2: Biodata diri (Pass Foto, Nama Lengkap, Kecamatan, No. Telepon, Kampus)
 * - Step 3: Preferensi (Jenis Barang yang Diminati)
 */

let currentStep = 1;
const totalSteps = 3;

document.addEventListener('DOMContentLoaded', () => {
    updateStepUI();
});

// Update active panel and step indicators
function updateStepUI() {
    for (let i = 1; i <= totalSteps; i++) {
        const panel = document.getElementById(`stepPanel${i}`);
        if (panel) {
            if (i === currentStep) {
                panel.classList.add('active');
            } else {
                panel.classList.remove('active');
            }
        }
    }

    // Update dots and lines inside the current active panel
    const activePanel = document.getElementById(`stepPanel${currentStep}`);
    if (activePanel) {
        for (let i = 1; i <= totalSteps; i++) {
            const dot = activePanel.querySelector(`.pbl-step-dot-${i}`);
            if (dot) {
                dot.classList.remove('active', 'completed');
                if (i < currentStep) {
                    dot.classList.add('completed');
                } else if (i === currentStep) {
                    dot.classList.add('active');
                }
            }
        }

        for (let i = 1; i < totalSteps; i++) {
            const line = activePanel.querySelector(`.pbl-step-line-${i}`);
            if (line) {
                if (i < currentStep) {
                    line.classList.add('active');
                } else {
                    line.classList.remove('active');
                }
            }
        }
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// Next Step Validation & Navigation
function nextStep(stepIndex) {
    if (stepIndex === 1) {
        const emailInput = document.getElementById('regEmail');
        const pwdInput = document.getElementById('regPassword');
        const email = emailInput ? emailInput.value.trim() : '';
        const pwd = pwdInput ? pwdInput.value : '';

        if (!email || !email.includes('@')) {
            alert('Harap masukkan alamat email yang valid!');
            if (emailInput) emailInput.focus();
            return;
        }
        if (!pwd || pwd.length < 6) {
            alert('Kata sandi harus minimal 6 karakter!');
            if (pwdInput) pwdInput.focus();
            return;
        }
    } else if (stepIndex === 2) {
        const namaInput = document.getElementById('regNama');
        const telpInput = document.getElementById('regTelepon');
        const kecInput = document.getElementById('regKecamatan');
        const kampusInput = document.getElementById('regKampus');

        const nama = namaInput ? namaInput.value.trim() : '';
        const telp = telpInput ? telpInput.value.trim() : '';
        const kec = kecInput ? kecInput.value.trim() : '';
        const kampus = kampusInput ? kampusInput.value.trim() : '';

        if (!nama) {
            alert('Harap masukkan nama lengkap Anda!');
            if (namaInput) namaInput.focus();
            return;
        }
        if (!kec) {
            alert('Harap pilih kecamatan domisili Anda!');
            if (kecInput) kecInput.focus();
            return;
        }
        if (!telp || telp.length < 8) {
            alert('Harap masukkan nomor telepon yang valid!');
            if (telpInput) telpInput.focus();
            return;
        }
        if (!kampus) {
            alert('Harap pilih nama kampus Anda!');
            if (kampusInput) kampusInput.focus();
            return;
        }
    }

    if (currentStep < totalSteps) {
        currentStep++;
        updateStepUI();
    }
}

// Previous Step
function prevStep(stepIndex) {
    if (currentStep > 1) {
        currentStep--;
        updateStepUI();
    }
}

// Preview Uploaded Photo
function previewAvatar(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.getElementById('avatarImg');
            const placeholder = document.getElementById('avatarPlaceholder');
            if (img) {
                img.src = e.target.result;
                img.style.display = 'block';
            }
            if (placeholder) {
                placeholder.style.display = 'none';
            }
        };
        reader.readAsDataURL(file);
    }
}

// Toggle Preferensi Card Selection
function togglePrefCard(cardEl) {
    const checkbox = cardEl.querySelector('input[type="checkbox"]');
    if (checkbox) {
        checkbox.checked = !checkbox.checked;
        if (checkbox.checked) {
            cardEl.classList.add('selected');
        } else {
            cardEl.classList.remove('selected');
        }
    }
}

// Complete Registration (Step 3 Selesai)
function finishRegistration() {
    const checkedPrefs = Array.from(document.querySelectorAll('input[name="preferensi"]:checked')).map(el => el.value);

    if (checkedPrefs.length === 0) {
        alert('Harap pilih minimal satu jenis barang yang diminati!');
        return;
    }

    const email = document.getElementById('regEmail')?.value.trim() || '';
    const nama = document.getElementById('regNama')?.value.trim() || '';
    const telepon = document.getElementById('regTelepon')?.value.trim() || '';
    const kecamatan = document.getElementById('regKecamatan')?.value.trim() || '';
    const kampus = document.getElementById('regKampus')?.value.trim() || '';
    const avatarImg = document.getElementById('avatarImg');
    const foto = (avatarImg && avatarImg.style.display !== 'none' && avatarImg.src) 
        ? avatarImg.src 
        : 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&q=80&w=200';

    const userData = {
        email: email,
        nama: nama,
        telepon: telepon,
        kecamatan: kecamatan,
        kampus: kampus,
        foto: foto,
        preferensi: checkedPrefs
    };

    // Store in localStorage for dashboard & landing page integration
    localStorage.setItem('preloved_user', JSON.stringify(userData));
    localStorage.setItem('pk_logged_in', 'true');

    // Populate modal summary
    if (document.getElementById('sumNama')) document.getElementById('sumNama').innerText = userData.nama;
    if (document.getElementById('sumPreferensi')) document.getElementById('sumPreferensi').innerText = userData.preferensi.join(', ');

    // Show modal
    const modal = document.getElementById('regSuccessModal');
    if (modal) modal.classList.add('show');
}


function toggleRegPwd(btn) {
    const input = document.getElementById('regPassword');
    const eyeIcon = document.getElementById('regEyeIcon') || (btn ? btn.querySelector('img') : null);
    if (input) {
        if (input.type === 'password') {
            input.type = 'text';
            if (eyeIcon) eyeIcon.src = 'images/eye-open.png';
        } else {
            input.type = 'password';
            if (eyeIcon) eyeIcon.src = 'images/eye-closed.png';
        }
    }
}
