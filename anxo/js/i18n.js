(function(){
  const KEY = 'dt_lang';
  async function loadTranslations(lang){
    try{
      const res = await fetch('i18n/'+lang+'.json');
      if(!res.ok) throw new Error('No translation file');
      return await res.json();
    }catch(e){
      if(lang !== 'es'){
        try{ const res = await fetch('i18n/es.json'); if(res.ok) return await res.json(); }catch(e){}
      }
      return {};
    }
  }

  function applyTranslations(map){
    // text content
    document.querySelectorAll('[data-i18n]').forEach(el=>{
      const key = el.getAttribute('data-i18n');
      if(!key) return;
      const txt = key.split('.').reduce((o,k)=>o && o[k]!==undefined?o[k]:undefined, map);
      if(txt!==undefined) el.textContent = txt;
    });
    // html content
    document.querySelectorAll('[data-i18n-html]').forEach(el=>{
      const key = el.getAttribute('data-i18n-html');
      if(!key) return;
      const txt = key.split('.').reduce((o,k)=>o && o[k]!==undefined?o[k]:undefined, map);
      if(txt!==undefined) el.innerHTML = txt;
    });
    // placeholders
    document.querySelectorAll('[data-i18n-placeholder]').forEach(el=>{
      const key = el.getAttribute('data-i18n-placeholder');
      const txt = key.split('.').reduce((o,k)=>o && o[k]!==undefined?o[k]:undefined, map);
      if(txt!==undefined) el.setAttribute('placeholder', txt);
    });
    // aria-labels
    document.querySelectorAll('[data-i18n-aria]').forEach(el=>{
      const key = el.getAttribute('data-i18n-aria');
      const txt = key.split('.').reduce((o,k)=>o && o[k]!==undefined?o[k]:undefined, map);
      if(txt!==undefined) el.setAttribute('aria-label', txt);
    });
    // value/text for buttons
    document.querySelectorAll('[data-i18n-value]').forEach(el=>{
      const key = el.getAttribute('data-i18n-value');
      const txt = key.split('.').reduce((o,k)=>o && o[k]!==undefined?o[k]:undefined, map);
      if(txt!==undefined) el.value = txt;
    });
  }

  async function translate(lang){
    const map = await loadTranslations(lang);
    applyTranslations(map);
    document.documentElement.lang = lang;
  }

  // initial load
  document.addEventListener('DOMContentLoaded', ()=>{
    const lang = localStorage.getItem(KEY) || document.documentElement.lang || 'es';
    translate(lang);
  });

  // listen for language changes dispatched by nav
  window.addEventListener('dt:langchange', e=>{
    const lang = (e && e.detail && e.detail.lang) || localStorage.getItem(KEY) || document.documentElement.lang || 'es';
    translate(lang);
  });

  // listen for storage events (other tabs)
  window.addEventListener('storage', e=>{
    if(e.key === KEY && e.newValue) translate(e.newValue);
  });
})();
