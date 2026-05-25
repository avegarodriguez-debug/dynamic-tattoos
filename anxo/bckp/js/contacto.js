 /* ─── CONTACTO MODAL ─────────────────────────────────────────────────── */
            function openContact() {
                document.getElementById('contactForm').style.display = '';
                document.getElementById('contactSuccess').style.display = 'none';
                const modal = document.getElementById('contactModal');
                modal.style.display = 'flex';
                requestAnimationFrame( () => modal.classList.add('open'));
                document.body.style.overflow = 'hidden';
            }
 function closeContact() {
                const modal = document.getElementById('contactModal');
                modal.classList.remove('open');
                setTimeout( () => {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                }
                , 250);
            }

            function submitContact() {
                const name = document.getElementById('ctName').value.trim();
                const email = document.getElementById('ctEmail').value.trim();
                const subject = document.getElementById('ctSubject').value.trim();
                const msg = document.getElementById('ctMessage').value.trim();
                if (!name || !email || !msg) {
                    alert('Por favor completa nombre, email y mensaje.');
                    return;
                }

                const btn = document.getElementById('ctSubmitText');
                btn.textContent = 'Enviando…';

                /* ── ENVÍO REAL con Formspree ─────────────────────────────────────────
     Cuando pongas tu CONTACT_ENDPOINT, este bloque se activa automáticamente:
  */
                if (CONTACT_ENDPOINT) {
                    fetch(CONTACT_ENDPOINT, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            name,
                            email,
                            subject,
                            message: msg
                        })
                    }).then(r => {
                        if (r.ok) {
                            showContactSuccess(email);
                        } else {
                            alert('Error al enviar. Inténtalo de nuevo.');
                            btn.textContent = 'Enviar mensaje';
                        }
                    }
                    ).catch( () => {
                        alert('Error de red. Inténtalo de nuevo.');
                        btn.textContent = 'Enviar mensaje';
                    }
                    );
                    return;
                }

                /* ── MODO DEMO (sin endpoint configurado) ─────────────────────────── */
                setTimeout( () => {
                    btn.textContent = 'Enviar mensaje';
                    showContactSuccess(email);
                }
                , 1000);
            }

            function showContactSuccess(email) {
                document.getElementById('contactForm').style.display = 'none';
                document.getElementById('contactSuccess').style.display = 'flex';
                document.getElementById('ctSuccessEmail').textContent = email;
            }

            /* ─── Cerrar modales con ESC o click en fondo ─────────────────────────── */
            document.addEventListener('keydown', e => {
                if (e.key === 'Escape') {
                    if (document.getElementById('checkoutModal').classList.contains('open'))
                        closeCheckout();
                    if (document.getElementById('contactModal').classList.contains('open'))
                        closeContact();
                }
            }
            );
            document.getElementById('checkoutModal').addEventListener('click', e => {
                if (e.target.id === 'checkoutModal')
                    closeCheckout();
            }
            );
            document.getElementById('contactModal').addEventListener('click', e => {
                if (e.target.id === 'contactModal')
                    closeContact();
            }
            );
