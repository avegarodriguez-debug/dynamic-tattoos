# -*- coding: utf-8 -*-
"""
Convierte bloques <pre class="legal-pre"> (texto escapado) en HTML semántico
(párrafos, titulares, listas) y añade reglas CSS para que sea legible.
Aplica los cambios in-place a los archivos PHP que contienen los bloques.
"""

import os
import re
import html

root = os.path.abspath('.')

# busca todos los archivos PHP en la raíz que contengan 'legal-pre'
php_files = []
for fname in os.listdir(root):
    if fname.lower().endswith('.php'):
        path = os.path.join(root, fname)
        with open(path, 'r', encoding='utf-8', errors='ignore') as f:
            txt = f.read()
            if 'legal-pre' in txt:
                php_files.append(path)

if not php_files:
    print('No se encontraron archivos PHP con <pre class="legal-pre">')
    raise SystemExit(0)

css_snippet = '''
.legal-content{font-size:15.75px;color:var(--ink-2);line-height:1.85}
.legal-content h2{margin-top:24px;font-size:18.5px;font-family:var(--display);color:var(--ink)}
.legal-content p{margin:0 0 12px}
.legal-content ul{margin-left:1.2em}
.legal-content li{margin:6px 0}
.legal-content .meta-note{font-size:13px;color:var(--ash)}
'''


def generate_html_from_text(raw_text):
    raw_text = raw_text.replace('\r\n', '\n').replace('\r', '\n').strip()
    raw_text = re.sub(r'\n{3,}', '\n\n', raw_text)
    blocks = re.split(r'\n\s*\n+', raw_text)
    parts = []

    for block in blocks:
        lines = [ln.strip() for ln in block.split('\n') if ln.strip()]
        if not lines:
            continue
        # lista numerada
        if all(re.match(r'^\d+[\.\)]\s+', l) for l in lines):
            parts.append('<ol>')
            for l in lines:
                content = re.sub(r'^\d+[\.\)]\s+', '', l)
                parts.append('<li>' + html.escape(content) + '</li>')
            parts.append('</ol>')
            continue
        # lista con viñetas
        if all(re.match(r'^[-•\*]\s+', l) for l in lines):
            parts.append('<ul>')
            for l in lines:
                content = re.sub(r'^[-•\*]\s+', '', l)
                parts.append('<li>' + html.escape(content) + '</li>')
            parts.append('</ul>')
            continue
        # titular si es una línea corta que empieza con número o está en mayúsculas
        if len(lines) == 1:
            l = lines[0]
            if re.match(r'^\d+[\.\)]\s*', l) or (l.isupper() and len(l.split()) <= 10):
                parts.append('<h2>' + html.escape(l) + '</h2>')
                continue
        # párrafo normal: unir líneas con espacio
        paragraph = ' '.join(lines)
        parts.append('<p>' + html.escape(paragraph) + '</p>')

    return '\n'.join(parts)


summary = []
for path in php_files:
    try:
        with open(path, 'r', encoding='utf-8', errors='ignore') as f:
            content = f.read()
    except Exception as e:
        summary.append((path, 'read_error', str(e)))
        continue

    # buscar optionalmente la meta previa + el pre escapado
    pattern = re.compile(r'(?:(<div\s+class=["\']legal-meta["\'][^>]*>.*?</div>\s*)?)<pre[^>]*class=["\']legal-pre["\'][^>]*>(.*?)</pre>', flags=re.DOTALL | re.IGNORECASE)
    m = pattern.search(content)
    if not m:
        summary.append((path, 'no_pre_found', ''))
        continue

    meta_html = m.group(1) or ''
    escaped_text = m.group(2) or ''
    raw_text = html.unescape(escaped_text).strip()

    # generar HTML semántico
    generated = generate_html_from_text(raw_text)

    # intentar extraer nombre de PDF desde la meta si existe
    pdf_name = None
    if meta_html:
        mm = re.search(r'Documento original:\s*([^<\n]+)', meta_html)
        if mm:
            pdf_name = mm.group(1).strip()
    if not pdf_name:
        pdf_name = os.path.basename(path)

    new_block = f'<div class="legal-meta">Documento original: {html.escape(pdf_name)}</div>\n<div class="legal-content">\n{generated}\n</div>'

    new_content = content[:m.start()] + new_block + content[m.end():]

    # insertar CSS si no existe dentro del primer <style>...</style>
    style_pat = re.search(r'(<style[^>]*>)(.*?)(</style>)', new_content, flags=re.DOTALL | re.IGNORECASE)
    if style_pat:
        style_body = style_pat.group(2)
        if '.legal-content' not in style_body:
            new_style = style_body + '\n' + css_snippet
            new_content = new_content.replace(style_pat.group(0), style_pat.group(1) + new_style + style_pat.group(3))
    else:
        # si no hay <style>, insertar antes del primer .legal-card
        insert_at = new_content.find('<div class="legal-card">')
        if insert_at != -1:
            style_tag = '<style>\n' + css_snippet + '\n</style>\n'
            new_content = new_content[:insert_at] + style_tag + new_content[insert_at:]

    try:
        with open(path, 'w', encoding='utf-8') as f:
            f.write(new_content)
        summary.append((path, 'formatted', 'ok'))
        print('Formatted:', path)
    except Exception as e:
        summary.append((path, 'write_error', str(e)))

print('\nRESULTS:')
for item in summary:
    print(item[0], '->', item[1], item[2])
print('DONE')
