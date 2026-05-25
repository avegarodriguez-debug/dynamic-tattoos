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
      <li><a href="index.php#concepto" data-i18n="nav.concept">Concepto</a></li>
      <li><a href="index.php#demo" data-i18n="nav.demo">Demo</a></li>
      <li><a href="index.php#tecnico" data-i18n="nav.tech">Técnico</a></li>
      <li><a href="index.php#planes" data-i18n="nav.plans">Planes</a></li>
      <li><a href="index.php#faq" data-i18n="nav.faq">FAQ</a></li>
    </ul>
    <div class="nav-controls" style="display:flex;align-items:center;gap:12px">
      <div class="lang-switcher" id="navLangSwitcher">
        <button id="navLangBtn" class="lang-btn" aria-haspopup="true" aria-expanded="false" type="button">
          <span class="nav-flag" aria-hidden="true" style="display:inline-block;width:24px;height:16px;margin-right:8px;vertical-align:middle"></span>
          <span class="nav-label">Español</span>&nbsp;▾
        </button>
        <ul class="lang-menu" id="navLangMenu" role="menu" hidden>
          <li role="none"><button role="menuitem" data-lang="en"><img class="flag-badge" aria-hidden="true" src="assets/flags/gb.svg" alt="">English</button></li>
          <li role="none"><button role="menuitem" data-lang="zh"><img class="flag-badge" aria-hidden="true" src="assets/flags/cn.svg" alt="">中文 (简体)</button></li>
          <li role="none"><button role="menuitem" data-lang="hi"><img class="flag-badge" aria-hidden="true" src="assets/flags/in.svg" alt="">हिन्दी</button></li>
          <li role="none"><button role="menuitem" data-lang="es"><img class="flag-badge" aria-hidden="true" src="assets/flags/es.svg" alt="">Español</button></li>
          <li role="none"><button role="menuitem" data-lang="ar"><img class="flag-badge" aria-hidden="true" src="assets/flags/sa.svg" alt="">العربية</button></li>
          <li role="none"><button role="menuitem" data-lang="fr"><img class="flag-badge" aria-hidden="true" src="assets/flags/fr.svg" alt="">Français</button></li>
          <li role="none"><button role="menuitem" data-lang="bn"><img class="flag-badge" aria-hidden="true" src="assets/flags/bd.svg" alt="">বাংলা</button></li>
          <li role="none"><button role="menuitem" data-lang="ru"><img class="flag-badge" aria-hidden="true" src="assets/flags/ru.svg" alt="">Русский</button></li>
          <li role="none"><button role="menuitem" data-lang="pt"><img class="flag-badge" aria-hidden="true" src="assets/flags/pt.svg" alt="">Português</button></li>
          <li role="none"><button role="menuitem" data-lang="ur"><img class="flag-badge" aria-hidden="true" src="assets/flags/pk.svg" alt="">اردو</button></li>
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
    <span data-i18n="marquee.item1">Tinta inteligente</span>
    <span data-i18n="marquee.item2">Sin láser · sin arrepentimientos</span>
    <span data-i18n="marquee.item3">Infinitas historias por contar</span>
    <span data-i18n="marquee.item4">Tu piel · tu canal</span>
    <span data-i18n="marquee.item5">4 × 4 cm validado</span>
    <span data-i18n="marquee.item1">Tinta inteligente</span>
    <span data-i18n="marquee.item2">Sin láser · sin arrepentimientos</span>
    <span data-i18n="marquee.item3">Infinitas historias por contar</span>
    <span data-i18n="marquee.item4">Tu piel · tu canal</span>
    <span data-i18n="marquee.item5">4 × 4 cm validado</span>
  </div>
</div>

  <style>
    /* Nav language menu styles + mobile adjustments */
    .lang-switcher{position:relative}
    .flag-badge{display:inline-block;min-width:20px;margin-right:8px;vertical-align:middle;font-size:18px;line-height:1;text-align:center;height:auto}
    .flag-badge{font-family: 'Segoe UI Emoji', 'Noto Color Emoji', 'Apple Color Emoji', 'EmojiOne Mozilla', 'Twemoji Mozilla', system-ui, sans-serif}
    .nav-flag svg,.flag-badge svg{width:24px;height:16px;display:block}
    .nav-flag{display:inline-block;margin-right:8px;font-size:18px;line-height:1;text-align:center}
    .flag-badge img, .nav-flag img{width:24px;height:16px;display:inline-block;vertical-align:middle;border-radius:3px}
    .lang-btn{background:transparent;border:1px solid rgba(0,0,0,0.06);padding:8px 12px;border-radius:8px;color:inherit;cursor:pointer;display:inline-flex;align-items:center;gap:8px;font-size:14px}
    .lang-menu{position:absolute;right:0;top:calc(100% + 8px);list-style:none;margin:0;padding:6px 0;background:#fff;color:#111;border-radius:10px;box-shadow:0 10px 30px rgba(0,0,0,0.12);min-width:220px;z-index:999;opacity:0;transform:translateY(-6px) scale(.98);transform-origin:top right;transition:opacity .18s ease,transform .18s ease;pointer-events:none}
    .lang-menu.open{opacity:1;transform:translateY(0) scale(1);pointer-events:auto}
    .lang-menu li{margin:0}
    .lang-menu li button{display:flex;align-items:center;width:100%;padding:10px 14px;border:none;background:transparent;text-align:left;color:inherit;cursor:pointer;font-size:14px}
    .lang-menu li button:hover{background:rgba(0,0,0,0.06)}
    .lang-menu[hidden]{display:none}

    /* Mobile: make dropdown fixed and touch-friendly */
    @media (max-width: 640px){
      .lang-menu{position:fixed;left:12px;right:12px;top:64px;min-width:unset;border-radius:12px;max-height:calc(100vh - 100px);overflow:auto;transform-origin:top center}
      .lang-btn{padding:10px 14px}
    }
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
      function setLang(lang){
        document.documentElement.lang = lang;
        save(lang);
        // update button: clone flag from menu if present
        const menuBtn = menu.querySelector(`button[data-lang="${lang}"]`);
        const flag = menuBtn ? menuBtn.querySelector('.flag-badge') : null;
        const label = labels[lang] || lang;
        // clear and build
        btn.innerHTML = '';
        if(flag){
          const navFlag = document.createElement('span'); navFlag.className = 'nav-flag';
          navFlag.appendChild(flag.cloneNode(true));
          navFlag.style.display = 'inline-block'; navFlag.style.width = '24px'; navFlag.style.height = '16px'; navFlag.style.marginRight = '8px';
          btn.appendChild(navFlag);
        }
        const labelSpan = document.createElement('span'); labelSpan.className = 'nav-label'; labelSpan.textContent = label;
        btn.appendChild(labelSpan);
        btn.appendChild(document.createTextNode(' ▾'));
        // notify global listeners (i18n loader)
        try{ window.dispatchEvent(new CustomEvent('dt:langchange',{detail:{lang}})); }catch(e){}
      }
      // Initialize
      setLang(getSaved());

      // If Twemoji is available it will run above; otherwise keep native emoji glyphs.

      // Toggle menu with proper event handling to avoid immediate close on mobile
      btn.addEventListener('click', function(e){
        e.stopPropagation();
        const open = btn.getAttribute('aria-expanded') === 'true';
        btn.setAttribute('aria-expanded', (!open).toString());
        if(open){ menu.classList.remove('open'); menu.setAttribute('hidden',''); }
        else { menu.classList.add('open'); menu.removeAttribute('hidden'); }
      });

      // Menu click should not propagate to document
      menu.addEventListener('click', function(e){
        e.stopPropagation();
        const t = e.target.closest('button[data-lang]');
        if(!t) return;
        const lang = t.getAttribute('data-lang');
        setLang(lang);
        menu.classList.remove('open'); menu.setAttribute('hidden',''); btn.setAttribute('aria-expanded','false');
      });

      // Close on outside click/touch
      function hideMenu(){ menu.classList.remove('open'); menu.setAttribute('hidden',''); btn.setAttribute('aria-expanded','false'); }
      document.addEventListener('click', function(e){ if(!menu.contains(e.target) && !btn.contains(e.target)) hideMenu(); });
      document.addEventListener('touchstart', function(e){ if(!menu.contains(e.target) && !btn.contains(e.target)) hideMenu(); });
      document.addEventListener('keydown', function(e){ if(e.key==='Escape') hideMenu(); });
      window.addEventListener('resize', hideMenu);
    });
  })();
  </script>