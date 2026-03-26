#!/usr/bin/env node
/**
 * convert-xlsx.js — Convierte el tracker de leads XLSX a HTML interactivo
 * Ejecutar: node scripts/convert-xlsx.js
 */

const XLSX = require('xlsx');
const fs = require('fs');
const path = require('path');

const PROJECT_ROOT = path.resolve(__dirname, '..');
const INPUT = path.join(PROJECT_ROOT, 'docs', 'cliente', '11_tracker_leads.xlsx');
const OUTPUT = path.join(PROJECT_ROOT, 'portal', 'docs', '11_tracker_leads.html');
const CSS_REL = '../css/doc-viewer.css';

function escapeHtml(text) {
  if (text == null) return '';
  return String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function sheetToTable(ws, sheetName) {
  const data = XLSX.utils.sheet_to_json(ws, { header: 1, defval: '' });
  if (data.length === 0) return '';

  // Find header row (first row with multiple non-empty cells after title rows)
  let headerIdx = 0;
  for (let i = 0; i < Math.min(data.length, 10); i++) {
    const nonEmpty = data[i].filter(c => String(c).trim() !== '').length;
    if (nonEmpty >= 3) {
      headerIdx = i;
      break;
    }
  }

  // Title rows before header
  let titleHtml = '';
  for (let i = 0; i < headerIdx; i++) {
    const text = data[i].filter(c => String(c).trim() !== '').join(' — ');
    if (text) titleHtml += `<p class="sheet-title-row">${escapeHtml(text)}</p>\n`;
  }

  const headers = data[headerIdx].map(h => escapeHtml(String(h).trim()));
  const rows = data.slice(headerIdx + 1).filter(r => r.some(c => String(c).trim() !== ''));

  let html = `<div class="sheet-section">
<h2>${escapeHtml(sheetName)}</h2>
${titleHtml}
<div class="table-controls">
  <input type="text" class="table-search" placeholder="Buscar..." data-table="${escapeHtml(sheetName)}">
</div>
<div class="table-wrap">
<table data-sheet="${escapeHtml(sheetName)}">
<thead><tr>${headers.map(h => `<th>${h}</th>`).join('')}</tr></thead>
<tbody>
`;

  for (const row of rows) {
    html += '<tr>';
    for (let c = 0; c < headers.length; c++) {
      const val = row[c] != null ? escapeHtml(String(row[c])) : '';
      html += `<td>${val}</td>`;
    }
    html += '</tr>\n';
  }

  html += `</tbody>
</table>
</div>
</div>`;
  return html;
}

const wb = XLSX.readFile(INPUT);
let sheetsHtml = '';
for (const name of wb.SheetNames) {
  sheetsHtml += sheetToTable(wb.Sheets[name], name) + '\n';
}

const tabsHtml = wb.SheetNames.map((name, i) =>
  `<button class="tab-btn${i === 0 ? ' active' : ''}" data-tab="${escapeHtml(name)}">${escapeHtml(name)}</button>`
).join('\n      ');

const fullHtml = `<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Tracker de Leads — Thor Metal Art</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="${CSS_REL}">
<style>
  /* Tab controls */
  .tabs{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:24px}
  .tab-btn{background:var(--dark3);color:var(--muted);border:1px solid var(--border);padding:8px 18px;border-radius:8px;cursor:pointer;font:inherit;font-size:13px;transition:all .2s}
  .tab-btn:hover{color:var(--text);border-color:var(--gold)}
  .tab-btn.active{background:rgba(184,134,11,.15);color:var(--gold);border-color:var(--gold)}
  .sheet-section{display:none}
  .sheet-section.active{display:block}
  .sheet-title-row{color:var(--muted);font-size:13px;margin-bottom:8px}

  /* Search */
  .table-controls{margin-bottom:16px}
  .table-search{background:var(--dark2);color:var(--text);border:1px solid var(--border);padding:8px 14px;border-radius:8px;font:inherit;font-size:14px;width:100%;max-width:360px;outline:none;transition:border-color .2s}
  .table-search:focus{border-color:var(--gold)}

  /* Table responsive */
  .table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;border:1px solid var(--border);border-radius:var(--r)}
  .doc-content table{display:table;margin:0;border-radius:0}

  /* Sort indicator */
  th{cursor:pointer;user-select:none;position:relative}
  th:hover{color:var(--gold-l)}
  th.sort-asc::after{content:' ▲';font-size:10px}
  th.sort-desc::after{content:' ▼';font-size:10px}

  /* Highlight search matches */
  tr.hide{display:none}
</style>
</head>
<body>

<header>
  <div class="logo">
    <div class="lmark">T</div>
    <div class="ltxt">THOR <span>METAL ART</span></div>
  </div>
  <nav>
    <a href="../">← Volver al Portal</a>
  </nav>
</header>

<main class="doc-container" style="max-width:1100px">
  <div class="doc-header">
    <span class="doc-num">Doc 11</span>
    <h1>Tracker de Leads</h1>
  </div>

  <div class="tabs">
      ${tabsHtml}
  </div>

  <article class="doc-content">
${sheetsHtml}
  </article>

  <footer class="doc-footer">
    <a href="../" class="back-link">← Volver al Portal</a>
    <span class="doc-meta">Thor Metal Art — Documento del Proyecto</span>
  </footer>
</main>

<script>
(function(){
  // Tabs
  const sheets = document.querySelectorAll('.sheet-section');
  const btns = document.querySelectorAll('.tab-btn');
  if (sheets.length > 0) sheets[0].classList.add('active');

  btns.forEach(btn => {
    btn.addEventListener('click', function(){
      btns.forEach(b => b.classList.remove('active'));
      sheets.forEach(s => s.classList.remove('active'));
      this.classList.add('active');
      const target = this.dataset.tab;
      sheets.forEach(s => {
        if (s.querySelector('h2') && s.querySelector('h2').textContent === target) s.classList.add('active');
      });
    });
  });

  // Search per table
  document.querySelectorAll('.table-search').forEach(input => {
    input.addEventListener('input', function(){
      const q = this.value.toLowerCase();
      const section = this.closest('.sheet-section');
      const rows = section.querySelectorAll('tbody tr');
      rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.classList.toggle('hide', q && !text.includes(q));
      });
    });
  });

  // Column sort
  document.querySelectorAll('th').forEach(th => {
    th.addEventListener('click', function(){
      const table = this.closest('table');
      const tbody = table.querySelector('tbody');
      const rows = Array.from(tbody.querySelectorAll('tr'));
      const idx = Array.from(this.parentNode.children).indexOf(this);
      const asc = !this.classList.contains('sort-asc');

      this.parentNode.querySelectorAll('th').forEach(h => { h.classList.remove('sort-asc','sort-desc'); });
      this.classList.add(asc ? 'sort-asc' : 'sort-desc');

      rows.sort((a, b) => {
        const va = (a.children[idx] || {}).textContent || '';
        const vb = (b.children[idx] || {}).textContent || '';
        const na = parseFloat(va.replace(/[^\\d.-]/g,''));
        const nb = parseFloat(vb.replace(/[^\\d.-]/g,''));
        if (!isNaN(na) && !isNaN(nb)) return asc ? na - nb : nb - na;
        return asc ? va.localeCompare(vb, 'es') : vb.localeCompare(va, 'es');
      });
      rows.forEach(r => tbody.appendChild(r));
    });
  });
})();
</script>

</body>
</html>`;

fs.writeFileSync(OUTPUT, fullHtml, 'utf8');
console.log('✅ XLSX convertido a HTML:', OUTPUT);
console.log('   Hojas:', wb.SheetNames.join(', '));
