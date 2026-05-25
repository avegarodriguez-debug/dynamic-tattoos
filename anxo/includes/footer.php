

<!-- FINAL CTA -->
<section class="final" data-theme="blood">
    <div class="wrap final-row">
      <div>
      <div style="font-family:var(--mono);font-size:12px;letter-spacing:.22em;text-transform:uppercase;opacity:.65;display:flex;align-items:center;gap:14px"><span style="width:36px;height:1px;background:var(--red);display:inline-block"></span>El cierre</div>
      <h2 style="margin-top:18px">En Dynamic&nbsp;Tattoos,<br/>lo hemos conseguido <em>para ti</em>.</h2>
      <p>No te conformes con un lienzo estático. Es hora de que tu arte corporal lata al mismo ritmo que tus experiencias. <strong style="color:#fff;font-weight:600">Tu historia empieza aquí. Hazla dinámica.</strong></p>
      <div class="hero-ctas" style="margin-top:0">
        <a class="btn btn-primary" href="/#planes">Quiero mi Dynamic Tattoo <span class="arrow">→</span></a>
        <button class="btn btn-ghost" onclick="openContact()" type="button">Contáctanos</button>
      </div>
    </div>
  </div>
</section>

<footer>
  <div class="wrap">
    <div class="foot-top">
      <div class="foot-logo-wrap">
        <img class="foot-logo" src="assets/logo-hero-qr.png" alt="Dynamic Tattoos" />
      </div>
      <div class="foot-cols">
        <div class="foot-col">
          <h5>Producto</h5>
          <ul>
            <li><a href="index.php#demo">Demo en vivo</a></li>
            <li><a href="index.php#concepto">Cómo funciona</a></li>
            <li><a href="index.php#tecnico">Técnico</a></li>
            <li><a href="index.php#planes">Planes</a></li>
          </ul>
        </div>
        <div class="foot-col">
          <h5>Soporte</h5>
          <ul>
            <li><a href="index.php#faq">FAQ</a></li>
            <!--No sé a dónde llevarlos, lo voy a dejar en blanco por ahora-->
            <li><a href="#">Estudios partner</a></li>
            <li><a href="#">Guía técnica</a></li>
            <li><a href="#" onclick="openContact()">Contacto</a></li>
          </ul>
        </div>
        <div class="foot-col">
          <h5>Legal</h5>
          <ul>
            <li><a href="terminos.php">Términos</a></li>
            <li><a href="privacidad.php">Privacidad</a></li>
            <li><a href="cookies.php">Cookies</a></li>
          </ul>
        </div>
        <div class="social-icons" style="display:flex;gap:12px;align-items:center">
        <a href="#" class="social-link" aria-label="Instagram" target="_blank" rel="noopener noreferrer">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M7 2h10a5 5 0 015 5v10a5 5 0 01-5 5H7a5 5 0 01-5-5V7a5 5 0 015-5zm5 6.5A4.5 4.5 0 1016.5 13 4.5 4.5 0 0012 8.5zm6.2-2.1a1.1 1.1 0 11-1.1-1.1 1.1 1.1 0 011.1 1.1z"/></svg>
        </a>
        <a href="#" class="social-link" aria-label="Facebook" target="_blank" rel="noopener noreferrer">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12a10 10 0 10-11.5 9.9v-7h-2.2v-2.9h2.2V9.3c0-2.2 1.3-3.5 3.3-3.5.9 0 1.8.1 1.8.1v2h-1c-1 0-1.3.6-1.3 1.2v1.5h2.3l-.4 2.9h-1.9V22A10 10 0 0022 12z"/></svg>
        </a>
        <a href="#" class="social-link" aria-label="TikTok" target="_blank" rel="noopener noreferrer">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M9 2v11.5A4.5 4.5 0 1014.5 7H16V4h-3V2H9z"/></svg>
        </a>
      </div>
      </div>
    </div>
    <div class="row">
      <div>© 2026 Dynamic Tattoos · Marca registrada</div>
      <div>Hecho con ❤️ por <a href="https://orbitatecnologica.com" target="_blank" rel="noopener noreferrer">Orbitatecnologica</a></div>
      
    </div>
  </div>
</footer>
<?php include 'includes/contacto.php'; ?>
<script src="js/contacto.js"></script>
<style>
  .social-link{color:inherit;opacity:.9;display:inline-flex;align-items:center;justify-content:center}
  .social-link svg{display:block}
  .social-link:hover{opacity:1;color:var(--red)}
</style>