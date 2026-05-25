<link rel="stylesheet" href="css/main.css">
<link rel="shortcut icon" href="logo.png" type="image/x-icon">
<!-- NAV -->
<nav class="top" id="topNav">
  <div class="wrap row">
    <a class="brand" href="index.php" aria-label="Dynamic Tattoos">
      <img class="brand-logo brand-light" src="assets/logo-nav-black.png" alt="Dynamic Tattoos" />
      <img class="brand-logo brand-dark" src="assets/logo-nav-white.png" alt="" aria-hidden="true" />
    </a>
    <ul>
      
      <li><a href="index.php#concepto">Concepto</a></li>
      <li><a href="index.php#demo">Demo</a></li>
      <li><a href="index.php#tecnico">Técnico</a></li>
      <li><a href="index.php#planes">Planes</a></li>
      <li><a href="index.php#faq">FAQ</a></li>
    </ul>
    <div class="nav-controls" style="display:flex;align-items:center;gap:12px">
      <div class="lang-switcher" id="navLangSwitcher">
        <button id="navLangBtn" class="lang-btn" aria-haspopup="true" aria-expanded="false" type="button">Español ▾</button>
        <ul class="lang-menu" id="navLangMenu" role="menu" hidden>
          <li role="none"><button role="menuitem" data-lang="en">🇬🇧 English</button></li>
          <li role="none"><button role="menuitem" data-lang="zh">🇨🇳 中文 (简体)</button></li>
          <li role="none"><button role="menuitem" data-lang="hi">🇮🇳 हिन्दी</button></li>
          <li role="none"><button role="menuitem" data-lang="es">🇪🇸 Español</button></li>
          <li role="none"><button role="menuitem" data-lang="ar">🇸🇦 العربية</button></li>
          <li role="none"><button role="menuitem" data-lang="fr">🇫🇷 Français</button></li>
          <li role="none"><button role="menuitem" data-lang="bn">🇧🇩 বাংলা</button></li>
          <li role="none"><button role="menuitem" data-lang="ru">🇷🇺 Русский</button></li>
          <li role="none"><button role="menuitem" data-lang="pt">🇵🇹 Português</button></li>
          <li role="none"><button role="menuitem" data-lang="ur">🇵🇰 اردو</button></li>
        </ul>
      </div>
    </div>
    <a class="cta" href="index.php#planes">Reservar QR</a>
  </div>
</nav>
<!-- Inline SVG QR helper. Pseudo-QR pattern; same DOM reused. -->
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <defs>
    <symbol id="qrSymbol" viewBox="0 0 33 33">
      <!-- 33x33 grid, cells 1x1 -->
      <g class="qr-cells" fill="currentColor"></g>
    </symbol>
  </defs>
</svg>



<!-- Marquee -->
<div class="marquee">
  <div class="marquee-track">
    <span>Tinta inteligente</span>
    <span>Sin láser · sin arrepentimientos</span>
    <span>Infinitas historias por contar</span>
    <span>Tu piel · tu canal</span>
    <span>4 × 4 cm validado</span>
    <span>Tinta inteligente</span>
    <span>Sin láser · sin arrepentimientos</span>
    <span>Infinitas historias por contar</span>
    <span>Tu piel · tu canal</span>
    <span>4 × 4 cm validado</span>
  </div>
</div>

  <style>
    /* Small nav language menu styles */
    .lang-switcher{position:relative}
    .lang-btn{background:transparent;border:1px solid rgba(0,0,0,0.06);padding:6px 10px;border-radius:6px;color:inherit;cursor:pointer}
    .lang-menu{position:absolute;right:0;top:calc(100% + 8px);list-style:none;margin:0;padding:6px 0;background:#fff;color:#111;border-radius:8px;box-shadow:0 10px 30px rgba(0,0,0,0.12);min-width:170px;z-index:999}
    .lang-menu li{margin:0}
    .lang-menu li button{display:block;width:100%;padding:8px 12px;border:none;background:transparent;text-align:left;color:inherit;cursor:pointer}
    .lang-menu li button:hover{background:rgba(0,0,0,0.06)}
    .lang-menu[hidden]{display:none}
  </style>

  <script>
  ;(function(){
    const KEY = 'dt_lang';
    document.addEventListener('DOMContentLoaded', function(){
      const btn = document.getElementById('navLangBtn');
      const menu = document.getElementById('navLangMenu');
      if(!btn || !menu) return;
      const labels = { en: 'English', zh: '中文', hi: 'हिन्दी', es: 'Español', ar: 'العربية', fr: 'Français', bn: 'বাংলা', ru: 'Русский', pt: 'Português', ur: 'اردو' };
      function getSaved(){ return localStorage.getItem(KEY) || document.documentElement.lang || 'es'; }
      function save(lang){ localStorage.setItem(KEY, lang); }
      function setLang(lang){ document.documentElement.lang = lang; btn.textContent = (labels[lang] || lang) + ' ▾'; save(lang); }
      setLang(getSaved());
      btn.addEventListener('click', function(e){ const open = btn.getAttribute('aria-expanded') === 'true'; btn.setAttribute('aria-expanded', (!open).toString()); if(open){ menu.setAttribute('hidden',''); } else { menu.removeAttribute('hidden'); } });
      menu.addEventListener('click', function(e){ const t = e.target.closest('button[data-lang]'); if(!t) return; const lang = t.getAttribute('data-lang'); setLang(lang); menu.setAttribute('hidden',''); btn.setAttribute('aria-expanded','false'); });
      document.addEventListener('click', function(e){ if(!menu.contains(e.target) && !btn.contains(e.target)){ menu.setAttribute('hidden',''); btn.setAttribute('aria-expanded','false'); }});
      document.addEventListener('keydown', function(e){ if(e.key==='Escape'){ menu.setAttribute('hidden',''); btn.setAttribute('aria-expanded','false'); }});
    });
  })();
  </script>