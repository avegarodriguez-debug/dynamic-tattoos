# -*- coding: utf-8 -*-
"""
Busca PDFs en el workspace, extrae texto con pypdf y sustituye
el contenido dentro del primer <div class="legal-body"> de cada
archivo PHP legal por el texto (escapado) dentro de un <pre>.

Diseñado para ejecutarse desde la raíz del proyecto.
"""

import os
import re
import html
import sys
from pypdf import PdfReader

root = os.path.abspath('.')
print('Workspace root:', root)

pdf_files = []
for dirpath, dirnames, filenames in os.walk(root):
    for fname in filenames:
        if fname.lower().endswith('.pdf'):
            pdf_files.append(os.path.join(dirpath, fname))

if not pdf_files:
    print('NO_PDFS_FOUND')
    sys.exit(0)

summary = []
for pdfpath in pdf_files:
    print('\nProcessing:', pdfpath)
    try:
        reader = PdfReader(pdfpath)
        text_pages = []
        for p in reader.pages:
            try:
                t = p.extract_text()
            except Exception:
                t = ''
            if t:
                text_pages.append(t)
        text = '\n\n'.join(text_pages).strip()
    except Exception as e:
        summary.append((pdfpath, None, f'extract_error: {e}'))
        continue

    filename_lower = os.path.basename(pdfpath).lower()
    target = None
    if 'cookie' in filename_lower:
        target = 'cookies.php'
    elif 'privac' in filename_lower or 'privacidad' in filename_lower:
        target = 'privacidad.php'
    elif 'termin' in filename_lower:
        target = 'terminos.php'
    elif 'condicion' in filename_lower or 'contrat' in filename_lower:
        target = 'condiciones.php'
    elif 'aviso' in filename_lower or 'legal' in filename_lower:
        target = 'condiciones.php'
    else:
        lower = text.lower()
        if 'cookie' in lower:
            target = 'cookies.php'
        elif 'privacidad' in lower or 'protección de datos' in lower or 'proteccion de datos' in lower:
            target = 'privacidad.php'
        elif 'términ' in lower or 'terminos' in lower:
            target = 'terminos.php'
        elif 'condicion' in lower or 'contrat' in lower or 'aviso legal' in lower:
            target = 'condiciones.php'

    if not target:
        summary.append((pdfpath, None, 'no mapping found'))
        print('  -> no mapping found')
        continue

    target_path = os.path.join(root, target)
    if not os.path.exists(target_path):
        summary.append((pdfpath, target, 'target file not found'))
        print('  -> target file not found:', target_path)
        continue

    # escape for HTML
    escaped = html.escape(text)

    new_inner = '\n\t\t\t<div class="legal-meta">Documento original: {}</div>\n\t\t\t<pre class="legal-pre">{}</pre>\n\t\t\t'.format(os.path.basename(pdfpath), escaped)

    try:
        with open(target_path, 'r', encoding='utf-8') as f:
            content = f.read()
    except Exception as e:
        summary.append((pdfpath, target, f'read_error: {e}'))
        print('  -> read error', e)
        continue

    m = re.search(r'<div\s+class=[\'\"]legal-body[\'\"]\s*>', content, flags=re.IGNORECASE)
    if not m:
        summary.append((pdfpath, target, 'legal-body not found'))
        print('  -> legal-body not found in', target_path)
        continue
    start = m.end()

    pattern = re.compile(r'<div\b|</div>', flags=re.IGNORECASE)
    count = 1
    end = None
    for match in pattern.finditer(content, start):
        token = match.group(0).lower()
        if token.startswith('<div'):
            count += 1
        else:
            count -= 1
        if count == 0:
            end = match.start()
            break
    if end is None:
        summary.append((pdfpath, target, 'matching closing tag not found'))
        print('  -> matching closing tag not found in', target_path)
        continue

    new_content = content[:start] + new_inner + content[end:]
    try:
        with open(target_path, 'w', encoding='utf-8') as f:
            f.write(new_content)
        summary.append((pdfpath, target, 'inserted'))
        print('  -> inserted into', target)
    except Exception as e:
        summary.append((pdfpath, target, f'write_error: {e}'))
        print('  -> write error', e)

print('\nRESULTS')
for item in summary:
    print(item[0], '->', item[1], ':', item[2])
print('DONE')
