<!-- Contact modal shared en todas las páginas -->

<div class="dt-modal" id="contactModal" role="dialog" aria-modal="true" aria-label="Contacto">
	<div class="dt-modal-box contact-box">
		<button class="dt-modal-close" onclick="closeContact()" aria-label="Cerrar">✕</button>

		<div class="co-header">
				<div class="co-badge" data-i18n="contact.title">Contacto</div>
				<h3 class="co-title" data-i18n-html="contact.title">Hablemos de tu <em>proyecto</em></h3>
				<p class="co-subtitle" data-i18n="contact.subtitle">Respuesta en menos de 24 horas. Sin compromisos.</p>
		</div>

		<div class="co-divider"></div>

		<div class="co-form" id="contactForm">
			<div class="co-field-row">
				<div class="co-field">
					<label data-i18n="contact.labels.name">Nombre</label>
					<input type="text" id="ctName" data-i18n-placeholder="contact.labels.name" placeholder="Tu nombre" autocomplete="name" />
				</div>
				<div class="co-field">
					<label data-i18n="contact.labels.email">Email</label>
					<input type="email" id="ctEmail" data-i18n-placeholder="contact.labels.email" placeholder="tu@email.com" autocomplete="email" />
				</div>
			</div>
			<div class="co-field">
				<label data-i18n="contact.labels.subject">Asunto</label>
				<input type="text" id="ctSubject" data-i18n-placeholder="contact.labels.subject" placeholder="¿En qué podemos ayudarte?" />
			</div>
			<div class="co-field">
				<label data-i18n="contact.labels.message">Mensaje</label>
				<textarea id="ctMessage" rows="4" data-i18n-placeholder="contact.labels.message" placeholder="Cuéntanos tu idea, duda o consulta…"></textarea>
			</div>

			<div class="contact-send-note">
				📬 Para activar el envío real, configura <strong>CONTACT_ENDPOINT</strong> en el JS (ver comentario).
			</div>

			<button class="co-submit-btn" onclick="submitContact()">
				<span id="ctSubmitText" data-i18n="contact.submit">Enviar mensaje</span>
				<svg width="16" height="16" viewBox="0 0 16 16" fill="none"><path d="M2 8l10 0M8 4l6 4-6 4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
			</button>
			<div class="co-legal">Tus datos se tratan según nuestra <a href="/privacidad.php">Política de privacidad</a>.</div>
		</div>

			<div class="co-success" id="contactSuccess" style="display:none">
				<div class="co-success-icon">✓</div>
				<h4 data-i18n="contact.successTitle">Mensaje enviado</h4>
				<p data-i18n-html="contact.successMsg">Gracias por contactarnos. Te responderemos lo antes posible en <strong id="ctSuccessEmail"></strong>.</p>
				<button class="co-submit-btn" onclick="closeContact()" data-i18n="contact.close">Cerrar</button>
			</div>
	</div>
</div>
<!---form action=https://formspree.io/f/xdajlqqj--->