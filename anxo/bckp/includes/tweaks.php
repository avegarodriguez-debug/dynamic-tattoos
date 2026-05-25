<script>
/* ============= QR pattern generator ============= */
/* A deterministic pseudo-QR (NOT a real working QR — visual logo).
   33×33 grid, three finder squares at the corners, alignment in the middle,
   pseudo-random data cells from a hashed seed. Reused everywhere. */
(function(){
  function seedRand(seed){
    let s = seed;
    return function(){
      s = (s * 1664525 + 1013904223) >>> 0;
      return (s >>> 8) / 16777216;
    };
  }
  function buildData(seed){
    const N = 33;
    const grid = Array.from({length:N},()=>Array(N).fill(0));
    // mark finder zones as off-limits
    function blockFinder(x,y){ for(let i=0;i<8;i++)for(let j=0;j<8;j++) grid[y+i] && (grid[y+i][x+j]=2); }
    blockFinder(0,0);blockFinder(N-8,0);blockFinder(0,N-8);
    // center logo zone
    for(let i=-3;i<=3;i++)for(let j=-3;j<=3;j++){
      const y=Math.floor(N/2)+i, x=Math.floor(N/2)+j;
      if(grid[y]&&grid[y][x]!==undefined) grid[y][x]=2;
    }
    // random pill cells
    const rng=seedRand(seed);
    for(let y=0;y<N;y++) for(let x=0;x<N;x++){
      if(grid[y][x]===2) continue;
      if(rng()<0.48) grid[y][x]=1;
    }
    return grid;
  }
  // Render: vertical pills (merge stacked cells into single rounded shape)
  function gridToShapes(grid, opts={}){
    const N = grid.length;
    let out = '';
    // finder eyes — outer ring + red center dot at the 3 corners
    const eye = (cx,cy)=>{
      out += `<circle class="qr-eye" cx="${cx}" cy="${cy}" r="3.4" fill="none" stroke="currentColor" stroke-width="1.1"/>`;
      out += `<circle class="qr-eye-dot" cx="${cx}" cy="${cy}" r="1.5" fill="${opts.redDot||'var(--red)'}"/>`;
    };
    eye(3.5,3.5); eye(N-4.5,3.5); eye(3.5,N-4.5);
    // pills — merge consecutive vertical cells per column
    for(let x=0;x<N;x++){
      let run = 0;
      for(let y=0;y<=N;y++){
        const v = (y<N) ? grid[y][x]===1 : false;
        if(v) run++;
        else {
          if(run>0){
            const yTop = y-run;
            const cx = x + .5;
            const cyTop = yTop + .5;
            const len = run-1;
            const r = .42;
            // rounded pill of width 2r and height len + 2r
            out += `<rect class="qr-cell" x="${cx-r}" y="${cyTop-r}" width="${r*2}" height="${len + r*2}" rx="${r}" ry="${r}"/>`;
          }
          run=0;
        }
      }
    }
    return out;
  }
  const seeds={hero:991,brand:7,final:331,brandMini:42};
  const heroPat = gridToShapes(buildData(seeds.hero));
  const brandPat = gridToShapes(buildData(seeds.brandMini));
  const finalPat = gridToShapes(buildData(seeds.final));
  document.querySelectorAll('#qrStage .qr-cells').forEach(g=>g.innerHTML=heroPat);
  document.querySelectorAll('.final .qr-cells, .qrbig .qr-cells').forEach(g=>g.innerHTML=finalPat);
})();

/* ============= Hero toggle (removed — clean logo stage) ============= */

/* ============= Hero video — IndexedDB-backed persistence ============= */
(function(){
  const v = document.querySelector('.hero-video');
  if(!v) return;
  const placeholder = document.querySelector('.hero-video-placeholder');
  // Force the correct stacking regardless of any cached/stale CSS
  if(placeholder){
    placeholder.style.position = 'absolute';
    placeholder.style.inset = '0';
    placeholder.style.zIndex = '1';
    placeholder.style.transition = 'opacity .6s ease';
  }
  v.style.zIndex = '2';

  function showVideo(){
    v.classList.add('ready');
    if(placeholder) placeholder.style.opacity = '0';
  }
  function hideVideo(){
    v.classList.remove('ready');
    if(placeholder) placeholder.style.opacity = '1';
  }
  v.addEventListener('canplay', showVideo);
  v.addEventListener('loadeddata', showVideo);
  v.addEventListener('error', ()=>{ hideVideo(); }, true);

  // ----- IndexedDB store for the video Blob -----
  // Survives reloads, new tabs, and browser restarts (until cache is cleared).
  const DB_NAME = 'dt-hero';
  const STORE = 'videos';
  const KEY = 'hero';

  function openDB(){
    return new Promise((resolve, reject)=>{
      const req = indexedDB.open(DB_NAME, 1);
      req.onupgradeneeded = ()=>{
        const db = req.result;
        if(!db.objectStoreNames.contains(STORE)) db.createObjectStore(STORE);
      };
      req.onsuccess = ()=>resolve(req.result);
      req.onerror = ()=>reject(req.error);
    });
  }
  async function saveBlob(blob, name){
    const db = await openDB();
    return new Promise((resolve, reject)=>{
      const tx = db.transaction(STORE, 'readwrite');
      tx.objectStore(STORE).put({blob, name, ts: Date.now()}, KEY);
      tx.oncomplete = ()=>resolve();
      tx.onerror = ()=>reject(tx.error);
    });
  }
  async function loadBlob(){
    const db = await openDB();
    return new Promise((resolve)=>{
      const tx = db.transaction(STORE, 'readonly');
      const req = tx.objectStore(STORE).get(KEY);
      req.onsuccess = ()=>resolve(req.result || null);
      req.onerror = ()=>resolve(null);
    });
  }
  async function clearBlob(){
    const db = await openDB();
    return new Promise((resolve)=>{
      const tx = db.transaction(STORE, 'readwrite');
      tx.objectStore(STORE).delete(KEY);
      tx.oncomplete = ()=>resolve();
      tx.onerror = ()=>resolve();
    });
  }

  // Expose helpers
  window.__heroVideoShow = showVideo;
  window.__heroVideoHide = hideVideo;
  window.__heroVideoSave = async (file)=>{
    await saveBlob(file, file.name);
    const url = URL.createObjectURL(file);
    v.src = url;
    v.load();
    v.play().catch(()=>{});
    return file.name;
  };
  window.__heroVideoLoadStored = async ()=>{
    try{
      const rec = await loadBlob();
      if(rec && rec.blob){
        const url = URL.createObjectURL(rec.blob);
        v.src = url;
        v.load();
        v.play().catch(()=>{});
        return rec.name || 'video guardado';
      }
    }catch(_){}
    return null;
  };
  window.__heroVideoClear = async ()=>{
    await clearBlob();
    v.removeAttribute('src');
    v.load();
    hideVideo();
  };

  // ----- Generic IDB blob storage for other assets (demo video + destination images) -----
  async function saveBlobAt(key, blob, name){
    const db = await openDB();
    return new Promise((resolve, reject)=>{
      const tx = db.transaction(STORE, 'readwrite');
      tx.objectStore(STORE).put({blob, name, ts: Date.now()}, key);
      tx.oncomplete = ()=>resolve();
      tx.onerror = ()=>reject(tx.error);
    });
  }
  async function loadBlobAt(key){
    const db = await openDB();
    return new Promise((resolve)=>{
      const tx = db.transaction(STORE, 'readonly');
      const req = tx.objectStore(STORE).get(key);
      req.onsuccess = ()=>resolve(req.result || null);
      req.onerror = ()=>resolve(null);
    });
  }
  async function clearBlobAt(key){
    const db = await openDB();
    return new Promise((resolve)=>{
      const tx = db.transaction(STORE, 'readwrite');
      tx.objectStore(STORE).delete(key);
      tx.oncomplete = ()=>resolve();
      tx.onerror = ()=>resolve();
    });
  }
  window.__dtStore = { save: saveBlobAt, load: loadBlobAt, clear: clearBlobAt };

  // Auto-restore on load
  window.__heroVideoLoadStored().then(name=>{
    if(name) window.dispatchEvent(new CustomEvent('hero-video-restored', {detail:{name}}));
  });
})();

/* ============= Demo video popup ============= */
(function(){
  const modal = document.getElementById('videoModal');
  const stage = document.getElementById('videoModalStage');
  const empty = document.getElementById('videoModalEmpty');
  const video = document.getElementById('videoModalVideo');
  const openBtn = document.getElementById('openDemoVideo');
  const closeBtn = document.getElementById('videoModalClose');
  if(!modal || !video) return;

  let currentObjectUrl = null;
  let hasVideo = false;

  function setSrc(url, name){
    if(currentObjectUrl){ try{ URL.revokeObjectURL(currentObjectUrl); }catch(_){} currentObjectUrl = null; }
    if(url && url.startsWith('blob:')) currentObjectUrl = url;
    video.src = url;
    video.load();
    video.style.display = 'block';
    empty.style.display = 'none';
    hasVideo = true;
  }
  function clearSrc(){
    if(currentObjectUrl){ try{ URL.revokeObjectURL(currentObjectUrl); }catch(_){} currentObjectUrl = null; }
    try{ video.pause(); }catch(_){}
    video.removeAttribute('src');
    video.load();
    video.style.display = 'none';
    empty.style.display = 'flex';
    hasVideo = false;
  }

  function open(){
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
    if(hasVideo){ video.currentTime = 0; video.play().catch(()=>{}); }
  }
  function close(){
    modal.classList.remove('open');
    document.body.style.overflow = '';
    try{ video.pause(); }catch(_){}
  }
  openBtn && openBtn.addEventListener('click', open);
  closeBtn && closeBtn.addEventListener('click', close);
  modal.addEventListener('click', (e)=>{ if(e.target === modal) close(); });
  document.addEventListener('keydown', (e)=>{ if(e.key === 'Escape' && modal.classList.contains('open')) close(); });

  window.__demoVideoSave = async (file)=>{
    if(window.__dtStore) await window.__dtStore.save('demo', file, file.name);
    const url = URL.createObjectURL(file);
    setSrc(url, file.name);
    return file.name;
  };
  window.__demoVideoLoadStored = async ()=>{
    try{
      if(!window.__dtStore) return null;
      const rec = await window.__dtStore.load('demo');
      if(rec && rec.blob){
        const url = URL.createObjectURL(rec.blob);
        setSrc(url, rec.name);
        return rec.name || 'video guardado';
      }
    }catch(_){}
    return null;
  };
  window.__demoVideoClear = async ()=>{
    if(window.__dtStore) await window.__dtStore.clear('demo');
    clearSrc();
  };

  // Auto-restore on load
  window.__demoVideoLoadStored().then(async (name)=>{
    if(name) {
      window.dispatchEvent(new CustomEvent('demo-video-restored', {detail:{name}}));
    } else {
      try{
        const head = await fetch('/uploads/demo.mp4', { method: 'HEAD' });
        if(head && head.ok){
          setSrc('/uploads/demo.mp4', 'demo.mp4');
          window.dispatchEvent(new CustomEvent('demo-video-restored', {detail:{name:'demo.mp4'}}));
        }
      }catch(_){ }
    }
  });
})();

/* ============= Destination images: persist uploads via IDB ============= */
(function(){
  const KEYS = ['portfolio','instagram','video','profile'];
  const urlMap = {};
  function applyAll(){
    window.__destImages = Object.assign({}, urlMap);
    if(window.__refreshDestImage) window.__refreshDestImage();
  }
  async function restore(){
    if(!window.__dtStore) return {};
    const names = {};
    for(const k of KEYS){
      try{
        const rec = await window.__dtStore.load('destimg-'+k);
        if(rec && rec.blob){
          urlMap[k] = URL.createObjectURL(rec.blob);
          names[k] = rec.name || 'imagen';
        }
      }catch(_){}
    }
    applyAll();
    return names;
  }
  window.__destImageSave = async (key, file)=>{
    if(window.__dtStore) await window.__dtStore.save('destimg-'+key, file, file.name);
    if(urlMap[key]){ try{ URL.revokeObjectURL(urlMap[key]); }catch(_){} }
    urlMap[key] = URL.createObjectURL(file);
    applyAll();
    return file.name;
  };
  window.__destImageClear = async (key)=>{
    if(window.__dtStore) await window.__dtStore.clear('destimg-'+key);
    if(urlMap[key]){ try{ URL.revokeObjectURL(urlMap[key]); }catch(_){} delete urlMap[key]; }
    applyAll();
  };
  window.__destImageRestoreAll = restore;
  // Run on next tick to ensure dest activation has registered the refresh hook
  setTimeout(()=>{
    restore().then(names=>{
      if(Object.keys(names).length) window.dispatchEvent(new CustomEvent('dest-images-restored',{detail:{names}}));
    });
  }, 50);
})();

/* ============= Upload-your-own hero video ============= */
window.__pickHeroVideo = function(){
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = 'video/*';
  input.onchange = (e)=>{
    const file = e.target.files && e.target.files[0];
    if(!file) return;
    const url = URL.createObjectURL(file);
    const v = document.querySelector('.hero-video');
    if(v){
      v.src = url;
      v.load();
      v.play().catch(()=>{});
      try{ sessionStorage.setItem('dt_hero_video', url); }catch(_){}
    }
    // Visual confirmation toast
    const t = document.createElement('div');
    t.textContent = '✓  Video cargado · ' + file.name;
    t.style.cssText = 'position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#0c0c0c;color:#f7f1e3;padding:12px 20px;font-family:var(--mono);font-size:11px;letter-spacing:.18em;text-transform:uppercase;z-index:9999;border:1px solid #b3252c;box-shadow:0 12px 32px -8px rgba(0,0,0,.5)';
    document.body.appendChild(t);
    setTimeout(()=>t.remove(), 3200);
  };
  input.click();
};

/* ============= Nav: glass goes dark when over dark sections + scrolled state ============= */
(function(){
  const nav = document.getElementById('topNav');
  if(!nav) return;
  // Sections that should darken the nav: hero + any [data-theme="ink"|"char"|"blood"]
  const darkSel = '.hero, [data-theme="ink"], [data-theme="char"], [data-theme="blood"]';
  const darkSecs = Array.from(document.querySelectorAll(darkSel));
  function check(){
    const navMid = nav.getBoundingClientRect().bottom - 1;
    let onDark = false;
    for(const s of darkSecs){
      const r = s.getBoundingClientRect();
      if(r.top <= navMid && r.bottom > navMid){ onDark = true; break; }
    }
    nav.classList.toggle('on-dark', onDark);
    // Scrolled state — nav becomes glass once user has moved past ~24px
    nav.classList.toggle('scrolled', window.scrollY > 24);
  }
  check();
  window.addEventListener('scroll', check, {passive:true});
  window.addEventListener('resize', check);
})();

/* ============= Demo destinations -> phone frame ============= */
(function(){
  const dests = document.querySelectorAll('.dest');
  // Default stock-like images per destination (inline SVG so it works offline)
  const fallback = {
    portfolio:'data:image/svg+xml;utf8,'+encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#e8e1d2"/><stop offset="1" stop-color="#bbb1a0"/></linearGradient></defs><rect width="400" height="400" fill="url(#g)"/><rect x="40" y="60" width="320" height="22" rx="3" fill="#1a1a1a"/><rect x="40" y="96" width="180" height="10" rx="2" fill="#1a1a1a" opacity=".7"/><rect x="40" y="130" width="150" height="150" fill="#1a1a1a"/><rect x="210" y="130" width="150" height="150" fill="#b3252c"/><rect x="40" y="300" width="320" height="8" rx="2" fill="#1a1a1a" opacity=".5"/><rect x="40" y="318" width="260" height="8" rx="2" fill="#1a1a1a" opacity=".4"/><rect x="40" y="336" width="220" height="8" rx="2" fill="#1a1a1a" opacity=".3"/></svg>'),
    instagram:'data:image/svg+xml;utf8,'+encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><linearGradient id="ig" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#feda77"/><stop offset=".5" stop-color="#f58529"/><stop offset="1" stop-color="#dd2a7b"/></linearGradient></defs><rect width="400" height="400" fill="url(#ig)"/><rect x="100" y="100" width="200" height="200" fill="none" stroke="#fff" stroke-width="16" rx="50"/><circle cx="200" cy="200" r="50" fill="none" stroke="#fff" stroke-width="16"/><circle cx="275" cy="125" r="12" fill="#fff"/></svg>'),
    video:'data:image/svg+xml;utf8,'+encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><defs><radialGradient id="v" cx="50%" cy="40%"><stop offset="0" stop-color="#3a1f1f"/><stop offset="1" stop-color="#080404"/></radialGradient></defs><rect width="400" height="400" fill="url(#v)"/><circle cx="200" cy="200" r="68" fill="#b3252c"/><polygon points="180,168 180,232 240,200" fill="#fff"/><rect x="40" y="340" width="320" height="4" fill="#fff" opacity=".25"/><rect x="40" y="340" width="120" height="4" fill="#b3252c"/></svg>'),
    profile:'data:image/svg+xml;utf8,'+encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 400 400"><rect width="400" height="400" fill="#141414"/><circle cx="200" cy="86" r="40" fill="#b3252c"/><rect x="60" y="160" width="95" height="95" fill="#e8e1d2"/><rect x="160" y="160" width="95" height="95" fill="#999999"/><rect x="260" y="160" width="95" height="95" fill="#b3252c"/><rect x="60" y="260" width="95" height="95" fill="#b3252c" opacity=".5"/><rect x="160" y="260" width="95" height="95" fill="#e8e1d2" opacity=".6"/><rect x="260" y="260" width="95" height="95" fill="#444444"/></svg>'),
  };
  window.__destImages = window.__destImages || {};
  function imgFor(key){
    return (window.__destImages && window.__destImages[key]) || fallback[key];
  }
  const map = {
    portfolio:{title:'Tu web <em>profesional</em>', icon:'¶', url:'portfolio.miestudio.com', label:'DESTINO ACTUAL · WEB'},
    instagram:{title:'Tu red social <em>favorita</em>', icon:'@', url:'instagram.com/aria.ink', label:'DESTINO ACTUAL · RED SOCIAL'},
    video:{title:'Un video <em>privado</em>', icon:'▶', url:'dyn.tt/u/aria/baile-2026', label:'DESTINO ACTUAL · VIDEO'},
    profile:{title:'Tu página <em>Dynamic</em>', icon:'❍', url:'dyn.tt/aria', label:'DESTINO ACTUAL · PERFIL'},
  };
  const frameImg = document.getElementById('frameImg');
  const frameMedia = document.getElementById('frameMedia');
  const frameIcon = document.getElementById('frameIcon');

  function applyImage(key){
    const src = imgFor(key);
    if(!frameImg || !frameMedia) return;
    if(src){
      frameImg.onload = ()=>{ frameMedia.classList.remove('no-img'); if(frameIcon) frameIcon.style.opacity = '0'; };
      frameImg.onerror = ()=>{ frameImg.style.display='none'; frameMedia.classList.add('no-img'); if(frameIcon) frameIcon.style.opacity = '.95'; };
      frameImg.style.display = 'block';
      frameImg.src = src;
    } else {
      frameImg.removeAttribute('src');
      frameImg.style.display = 'none';
      frameMedia.classList.add('no-img');
      if(frameIcon) frameIcon.style.opacity = '.95';
    }
  }

  let currentKey = 'portfolio';
  function activate(key){
    currentKey = key;
    dests.forEach(x=>x.classList.toggle('active', x.dataset.key===key));
    const m = map[key];
    if(!m) return;
    document.getElementById('frameTitle').innerHTML = m.title;
    document.getElementById('frameIcon').textContent = m.icon;
    document.getElementById('frameUrl').textContent = m.url;
    document.getElementById('frameLabel').textContent = m.label;
    applyImage(key);
  }
  window.__activateDest = activate;
  window.__refreshDestImage = ()=>applyImage(currentKey);
  dests.forEach(d=>{
    d.addEventListener('click',()=>activate(d.dataset.key));
  });
  activate('portfolio');
})();

/* ============= Body zones — interactive map with details ============= */
(function(){
  const zoneData = {
    forearm:{name:'Antebrazo interno', status:'optimal', statusLabel:'Óptimo', curv:'Plana', move:'Bajo', heal:'3-4 sem.', vis:'Alta', ink:'≥ 1.2 mm', note:'Una de las zonas más usadas para QR dinámico. Piel firme, lectura perfecta a cualquier ángulo.'},
    biceps:{name:'Bíceps exterior', status:'optimal', statusLabel:'Óptimo', curv:'Leve', move:'Bajo', heal:'3-4 sem.', vis:'Media', ink:'≥ 1.2 mm', note:'Superficie amplia, contraste sólido. Ideal para QR de 4×4 cm.'},
    calf:{name:'Pantorrilla', status:'optimal', statusLabel:'Óptimo', curv:'Leve', move:'Bajo', heal:'4-5 sem.', vis:'Media', ink:'≥ 1.2 mm', note:'Zona estable con buen retorno cromático. Recomendado en parte exterior.'},
    chest:{name:'Pectoral', status:'good', statusLabel:'Bueno', curv:'Leve', move:'Medio', heal:'4-5 sem.', vis:'Privada', ink:'≥ 1.3 mm', note:'Visibilidad bajo demanda. Requiere placado en zona alta para evitar distorsión.'},
    shoulder:{name:'Hombro · escápula', status:'optimal', statusLabel:'Óptimo', curv:'Leve', move:'Bajo', heal:'3-4 sem.', vis:'Alta', ink:'≥ 1.2 mm', note:'Superficie plana sobre el omóplato. Excelente para escaneo lateral.'},
    back:{name:'Espalda baja', status:'optimal', statusLabel:'Óptimo', curv:'Plana', move:'Bajo', heal:'4 sem.', vis:'Privada', ink:'≥ 1.2 mm', note:'Lienzo amplio y estable. Ideal si buscas un QR discreto pero perfecto.'},
    wrist:{name:'Muñeca', status:'warn', statusLabel:'Supervisar', curv:'Alta', move:'Alto', heal:'5-6 sem.', vis:'Alta', ink:'≥ 1.4 mm', note:'La curvatura puede distorsionar la lectura. Aceptable solo si se valida con plantilla previa.'},
    ribs:{name:'Costillas', status:'warn', statusLabel:'Supervisar', curv:'Media', move:'Alto', heal:'5-6 sem.', vis:'Privada', ink:'≥ 1.4 mm', note:'Piel fina y mucho movimiento respiratorio. Recomendado solo a tatuadores experimentados.'},
  };

  const detailEls = {
    name: document.getElementById('bzName'),
    status: document.getElementById('bzStatus'),
    curv: document.getElementById('bzCurv'),
    move: document.getElementById('bzMove'),
    heal: document.getElementById('bzHeal'),
    vis: document.getElementById('bzVis'),
    ink: document.getElementById('bzInk'),
    note: document.getElementById('bzNote'),
  };
  const crosshair = document.getElementById('zoneCrosshair');
  const bodyMap = document.getElementById('bodyMap');

  function updateDetail(zone){
    const d = zoneData[zone];
    if(!d || !detailEls.name) return;
    detailEls.name.textContent = d.name;
    detailEls.status.textContent = d.statusLabel;
    detailEls.status.className = 'bz-detail-status ' + d.status;
    detailEls.curv.textContent = d.curv;
    detailEls.move.textContent = d.move;
    detailEls.heal.textContent = d.heal;
    detailEls.vis.textContent = d.vis;
    detailEls.ink.textContent = d.ink;
    detailEls.note.textContent = d.note;
  }

  function moveCrosshair(zone){
    if(!crosshair || !bodyMap) return;
    // Find first visible dot for this zone
    const dot = bodyMap.querySelector(`.zone-dot[data-zone="${zone}"]:not(.hidden)`);
    if(!dot){ crosshair.classList.remove('show'); return; }
    crosshair.style.left = dot.style.left;
    crosshair.style.top = dot.style.top;
    crosshair.style.color = dot.classList.contains('warn') ? '#b3252c' : '#1a1a1a';
    crosshair.classList.add('show');
  }

  function activate(zone){
    document.querySelectorAll('.zone-li').forEach(i=>i.classList.toggle('active', i.dataset.zone===zone));
    document.querySelectorAll('.zone-dot').forEach(d=>d.classList.toggle('active', d.dataset.zone===zone));
    updateDetail(zone);
    moveCrosshair(zone);
  }

  document.querySelectorAll('.zone-li').forEach(i=>i.addEventListener('click', ()=>{
    // Auto-switch view if zone not visible in current view
    const dot = document.querySelector(`.zone-dot[data-zone="${i.dataset.zone}"]:not(.hidden)`);
    if(!dot){
      const anyDot = document.querySelector(`.zone-dot[data-zone="${i.dataset.zone}"]`);
      if(anyDot){
        const targetView = anyDot.dataset.view;
        switchView(targetView);
      }
    }
    activate(i.dataset.zone);
  }));
  document.querySelectorAll('.zone-dot').forEach(d=>d.addEventListener('click', ()=>activate(d.dataset.zone)));

  // Front/back toggle
  function switchView(view){
    document.querySelectorAll('.bz-toggle button').forEach(b=>b.classList.toggle('on', b.dataset.view===view));
    document.querySelectorAll('.silhouette').forEach(s=>s.classList.toggle('hidden', !s.classList.contains(view+'-view')));
    document.querySelectorAll('.zone-dot').forEach(d=>d.classList.toggle('hidden', d.dataset.view !== view));
    // Refresh crosshair for current active zone
    const active = document.querySelector('.zone-li.active');
    if(active) moveCrosshair(active.dataset.zone);
  }
  document.querySelectorAll('.bz-toggle button').forEach(b=>b.addEventListener('click', ()=>switchView(b.dataset.view)));

  // Initial state
  activate('forearm');
})();

/* ============= FAQ accordion ============= */
(function(){
  const qs = document.querySelectorAll('.q');
  qs.forEach(q=>{
    q.addEventListener('click',()=>{
      const wasOpen = q.classList.contains('open');
      qs.forEach(x=>x.classList.remove('open'));
      if(!wasOpen) q.classList.add('open');
    });
  });
})();

/* ============= Reveal on scroll ============= */
(function(){
  const els = document.querySelectorAll('.hero-grid > div, .sec-head, .steps-grid, .specs-grid, .plans, .faq-grid, .final-row');
  els.forEach(e=>e.classList.add('reveal'));
  const io = new IntersectionObserver((entries)=>{
    entries.forEach(e=>{ if(e.isIntersecting){ e.target.classList.add('in'); io.unobserve(e.target);} });
  },{threshold:.12});
  els.forEach(e=>io.observe(e));
})();

/* ============= Smooth scroll for anchors ============= */
document.querySelectorAll('a[href^="#"]').forEach(a=>{
  a.addEventListener('click',(e)=>{
    const id=a.getAttribute('href').slice(1);
    const el=document.getElementById(id);
    if(el){ e.preventDefault(); window.scrollTo({top:el.getBoundingClientRect().top+window.scrollY-60,behavior:'smooth'}); }
  });
});
</script>

<!-- Tweaks por defecto -->
<script src="https://unpkg.com/react@18.3.1/umd/react.development.js" integrity="sha384-hD6/rw4ppMLGNu3tX5cjIb+uRZ7UkRJ6BPkLpg4hAu/6onKUg4lLsHAs9EBPT82L" crossorigin="anonymous"></script>
<script src="https://unpkg.com/react-dom@18.3.1/umd/react-dom.development.js" integrity="sha384-u6aeetuaXnQ38mYT8rp6sbXaQe3NL9t+IBXmnYxwkUI2Hw4bsp2Wvmx4yRQF1uAm" crossorigin="anonymous"></script>
<script src="https://unpkg.com/@babel/standalone@7.29.0/babel.min.js" integrity="sha384-m08KidiNqLdpJqLq95G/LEi8Qvjl/xUYll3QILypMoQ65QorJ9Lvtp2RXYGBFj1y" crossorigin="anonymous"></script>
<script type="text/babel" src="tweaks-panel.jsx"></script>
<script type="text/babel">
const TWEAK_DEFAULTS = /*EDITMODE-BEGIN*/{
  "accent": "#b3252c",
  "theme": "paper",
  "marqueeOn": true,
  "grainOn": true,
  "heroOverlay": 0.7,
  "heroOverlayColor": "#0a0707"
}/*EDITMODE-END*/;

function App(){
  const [t, setTweak] = useTweaks(TWEAK_DEFAULTS);
  const [videoName, setVideoName] = React.useState(null);
  const [demoVideoName, setDemoVideoName] = React.useState(null);
  const [destNames, setDestNames] = React.useState({});

  // Restore name on mount (IndexedDB → video)
  React.useEffect(()=>{
    const onRestored = (e)=>setVideoName(e.detail?.name || 'video guardado');
    const onDemoRestored = (e)=>setDemoVideoName(e.detail?.name || 'video guardado');
    const onDestRestored = (e)=>setDestNames(e.detail?.names || {});
    window.addEventListener('hero-video-restored', onRestored);
    window.addEventListener('demo-video-restored', onDemoRestored);
    window.addEventListener('dest-images-restored', onDestRestored);
    return ()=>{
      window.removeEventListener('hero-video-restored', onRestored);
      window.removeEventListener('demo-video-restored', onDemoRestored);
      window.removeEventListener('dest-images-restored', onDestRestored);
    };
  },[]);

  React.useEffect(()=>{
    document.documentElement.style.setProperty('--red', t.accent);
    document.documentElement.style.setProperty('--red-deep', t.accent);
    document.body.classList.toggle('dark', t.theme==='dark');
    document.querySelector('.marquee').style.display = t.marqueeOn ? '' : 'none';
    document.body.style.setProperty('--grain', t.grainOn?'.35':'0');
    document.documentElement.style.setProperty('--hero-overlay', t.heroOverlay);
    document.documentElement.style.setProperty('--hero-overlay-color', t.heroOverlayColor);
    const grainStyle = document.getElementById('grainStyle') || (()=>{
      const s=document.createElement('style');s.id='grainStyle';document.head.appendChild(s);return s;
    })();
    grainStyle.textContent = `body::before{opacity:${t.grainOn?.35:0} !important}`;
  },[t]);

  const pickVideo = ()=>{
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'video/*';
    input.onchange = async (e)=>{
      const file = e.target.files && e.target.files[0];
      if(!file) return;
      setVideoName('Guardando…');
      try{
        const name = await window.__heroVideoSave(file);
        setVideoName(name);
        if(t.heroOverlay >= 0.95){ setTweak('heroOverlay', 0.5); }
      }catch(err){
        setVideoName('✗ Error al guardar');
      }
    };
    input.click();
  };

  const removeVideo = async ()=>{
    await window.__heroVideoClear();
    setVideoName(null);
  };

  const pickDemoVideo = ()=>{
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'video/*';
    input.onchange = async (e)=>{
      const file = e.target.files && e.target.files[0];
      if(!file) return;
      setDemoVideoName('Guardando…');
      try{
        const name = await window.__demoVideoSave(file);
        setDemoVideoName(name);
      }catch(err){
        setDemoVideoName('✗ Error al guardar');
      }
    };
    input.click();
  };
  const removeDemoVideo = async ()=>{
    await window.__demoVideoClear();
    setDemoVideoName(null);
  };

  const pickDestImage = (key)=>{
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = 'image/*';
    input.onchange = async (e)=>{
      const file = e.target.files && e.target.files[0];
      if(!file) return;
      try{
        const name = await window.__destImageSave(key, file);
        setDestNames(n=>({...n,[key]:name}));
        if(window.__activateDest){
          // Switch active to the destination we just set, so the user sees the change
          window.__activateDest(key);
        }
      }catch(err){ /* ignore */ }
    };
    input.click();
  };
  const clearDestImage = async (key)=>{
    await window.__destImageClear(key);
    setDestNames(n=>{ const c={...n}; delete c[key]; return c; });
  };

  const shortName = (s)=> s && s.length>22 ? s.slice(0,22)+'…' : s;

  const destLabels = {
    portfolio:'Web profesional',
    instagram:'Red social',
    video:'Video privado',
    profile:'Página Dynamic',
  };

  return (
    <TweaksPanel title="Tweaks">
      <TweakSection title="Video del hero (fondo)">
        <TweakButton label={videoName ? `Reemplazar (${shortName(videoName)})` : 'Subir video de fondo'} onClick={pickVideo} />
        {videoName && <TweakButton label="Quitar video" onClick={removeVideo} />}
        <div style={{fontFamily:'JetBrains Mono,monospace',fontSize:10,opacity:.6,lineHeight:1.5,padding:'4px 0'}}>
          MP4 / WebM · se guarda en este navegador.
        </div>
      </TweakSection>

      <TweakSection title="Video demo (popup)">
        <TweakButton label={demoVideoName ? `Reemplazar (${shortName(demoVideoName)})` : 'Subir video demo'} onClick={pickDemoVideo} />
        {demoVideoName && <TweakButton label="Quitar video demo" onClick={removeDemoVideo} />}
        <div style={{fontFamily:'JetBrains Mono,monospace',fontSize:10,opacity:.6,lineHeight:1.5,padding:'4px 0'}}>
          Se reproduce al pulsar “Ver video”. Mantiene su proporción original.
        </div>
      </TweakSection>

      <TweakSection title="Imágenes del mockup (sección 01)">
        {Object.keys(destLabels).map(key=>(
          <div key={key} style={{display:'flex',flexDirection:'column',gap:6,padding:'6px 0',borderBottom:'1px dashed rgba(127,127,127,.18)'}}>
            <div style={{fontFamily:'JetBrains Mono,monospace',fontSize:10,letterSpacing:'.14em',textTransform:'uppercase',opacity:.7}}>{destLabels[key]}</div>
            <div style={{display:'flex',gap:6,flexWrap:'wrap'}}>
              <TweakButton label={destNames[key] ? `Reemplazar` : 'Subir imagen'} onClick={()=>pickDestImage(key)} />
              {destNames[key] && <TweakButton label="Quitar" onClick={()=>clearDestImage(key)} />}
            </div>
            {destNames[key] && <div style={{fontFamily:'JetBrains Mono,monospace',fontSize:9,opacity:.5}}>{shortName(destNames[key])}</div>}
          </div>
        ))}
      </TweakSection>

      <TweakSection title="Capa sobre el video">
        <TweakColor label="Color" value={t.heroOverlayColor} onChange={v=>setTweak('heroOverlayColor',v)}
          options={['#0a0707','#000000','#1a0a0a','#3a0d10','#0c1620','#b3252c']} />
        <TweakSlider label="Opacidad" min={0} max={0.95} step={0.02}
          value={t.heroOverlay} onChange={v=>setTweak('heroOverlay',v)} />
      </TweakSection>

      <TweakSection title="Color & tono">
        <TweakColor label="Acento" value={t.accent} onChange={v=>setTweak('accent',v)}
          options={['#b3252c','#8a1c22','#d63c44','#1a1a1a','#4a4a4a']} />
        <TweakRadio label="Tema base" value={t.theme} onChange={v=>setTweak('theme',v)}
          options={[{value:'paper',label:'Papel'},{value:'dark',label:'Oscuro'}]} />
      </TweakSection>
      <TweakSection title="Detalles">
        <TweakToggle label="Marquee superior" value={t.marqueeOn} onChange={v=>setTweak('marqueeOn',v)} />
        <TweakToggle label="Grano de papel" value={t.grainOn} onChange={v=>setTweak('grainOn',v)} />
      </TweakSection>
    </TweaksPanel>
  );
}

const root = ReactDOM.createRoot(document.getElementById('tweaks-root'));
root.render(<App />);
</script>