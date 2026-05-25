/* ─── CONTACTO MODAL ─────────────────────────────────────────────────── */
function openContact() {
    const contactForm = document.getElementById('contactForm');
    const contactSuccess = document.getElementById('contactSuccess');
    const modal = document.getElementById('contactModal');
    if (!modal) return;
    if (contactForm) contactForm.style.display = '';
    if (contactSuccess) contactSuccess.style.display = 'none';
    modal.style.display = 'flex';
    requestAnimationFrame(() => modal.classList.add('open'));
    document.body.style.overflow = 'hidden';
}

function closeContact() {
    const modal = document.getElementById('contactModal');
    if (!modal) return;
    modal.classList.remove('open');
    setTimeout(() => {
        modal.style.display = 'none';
        document.body.style.overflow = '';
    }, 250);
}

function submitContact() {
    const nameEl = document.getElementById('ctName');
    const emailEl = document.getElementById('ctEmail');
    const subjectEl = document.getElementById('ctSubject');
    const msgEl = document.getElementById('ctMessage');
    if (!(nameEl && emailEl && msgEl)) {
        alert('Formulario no disponible.');
        return;
    }
    const name = nameEl.value.trim();
    const email = emailEl.value.trim();
    const subject = subjectEl ? subjectEl.value.trim() : '';
    const msg = msgEl.value.trim();
    if (!name || !email || !msg) { alert('Por favor completa nombre, email y mensaje.'); return; }

    const btn = document.getElementById('ctSubmitText');
    if (btn) btn.textContent = 'Enviando…';

    if (typeof CONTACT_ENDPOINT !== 'undefined' && CONTACT_ENDPOINT) {
        fetch(CONTACT_ENDPOINT, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ name, email, subject, message: msg })
        })
        .then(r => { if (r.ok) showContactSuccess(email); else { alert('Error al enviar. Inténtalo de nuevo.'); if (btn) btn.textContent = 'Enviar mensaje'; } })
        .catch(() => { alert('Error de red. Inténtalo de nuevo.'); if (btn) btn.textContent = 'Enviar mensaje'; });
        return;
    }

    setTimeout(() => {
        if (btn) btn.textContent = 'Enviar mensaje';
        showContactSuccess(email);
    }, 1000);
}

function showContactSuccess(email) {
    const form = document.getElementById('contactForm');
    const succ = document.getElementById('contactSuccess');
    const el = document.getElementById('ctSuccessEmail');
    if (form) form.style.display = 'none';
    if (succ) succ.style.display = 'flex';
    if (el) el.textContent = email;
}

/* ─── Cerrar modales con ESC o click en fondo (guards) ─────────────────── */
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') {
        const checkoutModal = document.getElementById('checkoutModal');
        if (checkoutModal && checkoutModal.classList.contains('open') && typeof closeCheckout === 'function') closeCheckout();
        const contactModal = document.getElementById('contactModal');
        if (contactModal && contactModal.classList.contains('open')) closeContact();
    }
});

const _checkoutModalEl = document.getElementById('checkoutModal');
if (_checkoutModalEl && typeof closeCheckout === 'function') {
    _checkoutModalEl.addEventListener('click', e => { if (e.target.id === 'checkoutModal') closeCheckout(); });
}
const _contactModalEl = document.getElementById('contactModal');
if (_contactModalEl) {
    _contactModalEl.addEventListener('click', e => { if (e.target.id === 'contactModal') closeContact(); });
}
