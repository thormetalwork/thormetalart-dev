#!/usr/bin/env node
/**
 * convert-docs.js — Convierte DOCX del cliente a HTML con template del portal
 * Usa mammoth.js para DOCX → HTML
 * Ejecutar: node scripts/convert-docs.js
 */

const mammoth = require('mammoth');
const fs = require('fs');
const path = require('path');

const PROJECT_ROOT = path.resolve(__dirname, '..');
const SOURCE_DIR = path.join(PROJECT_ROOT, 'docs', 'cliente');
const OUTPUT_DIR = path.join(PROJECT_ROOT, 'portal', 'docs');
const CSS_REL = '../css/doc-viewer.css';

const TITLES = {
  '01_metodologia_maestra': 'Metodología Maestra',
  '02_diagnostico_auditoria': 'Diagnóstico y Auditoría Digital',
  '03_brief_posicionamiento': 'Brief de Posicionamiento Dual',
  '04_plan_proyecto_checklists': 'Plan de Proyecto & Checklists',
  '05_checklist_maestro': 'Checklist Maestro de Ejecución',
  '06_reporte_mensual': 'Plantilla de Reporte Mensual',
  '07_propuesta_consultoria': 'Propuesta de Consultoría',
  '08_scripts_comunicacion': 'Scripts de Comunicación',
  '09_guia_fotografia': 'Guía de Fotografía de Proyectos',
  '10_copys_sitio_web': 'Copys del Sitio Web',
  '12_dashboard_arquitectura': 'Arquitectura del Dashboard Digital',
};

function escapeHtml(text) {
  return text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function wrapHtml(bodyHtml, title, docNum) {
  return `<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>${escapeHtml(title)} — Thor Metal Art</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="${CSS_REL}">
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

<main class="doc-container">
  <div class="doc-header">
    <span class="doc-num">Doc ${docNum}</span>
    <h1>${escapeHtml(title)}</h1>
  </div>
  <article class="doc-content">
${bodyHtml}
  </article>
  <footer class="doc-footer">
    <a href="../" class="back-link">← Volver al Portal</a>
    <span class="doc-meta">Thor Metal Art — Documento del Proyecto</span>
  </footer>
</main>

</body>
</html>`;
}

async function convertDocx(filePath, outputPath, title, docNum) {
  const result = await mammoth.convertToHtml({ path: filePath });
  if (result.messages.length > 0) {
    result.messages.forEach(m => console.log(`   ⚠ ${m.message}`));
  }
  const html = wrapHtml(result.value, title, docNum);
  fs.writeFileSync(outputPath, html, 'utf8');
}

async function main() {
  fs.mkdirSync(OUTPUT_DIR, { recursive: true });

  const docxFiles = fs.readdirSync(SOURCE_DIR)
    .filter(f => f.endsWith('.docx'))
    .sort();

  console.log('🔄 Convirtiendo documentos DOCX → HTML...');
  console.log(`   Fuente: ${SOURCE_DIR}`);
  console.log(`   Destino: ${OUTPUT_DIR}`);
  console.log('');

  let converted = 0;
  let failed = 0;

  for (const file of docxFiles) {
    const basename = path.basename(file, '.docx');
    const title = TITLES[basename] || basename;
    const docNum = basename.split('_')[0];
    const inputPath = path.join(SOURCE_DIR, file);
    const outputPath = path.join(OUTPUT_DIR, `${basename}.html`);

    process.stdout.write(`   📄 ${basename} → HTML...`);

    try {
      await convertDocx(inputPath, outputPath, title, docNum);
      converted++;
      console.log(' ✅');
    } catch (err) {
      failed++;
      console.log(` ❌ ${err.message}`);
    }
  }

  console.log('');
  console.log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
  console.log(`✅ Convertidos: ${converted}`);
  if (failed > 0) console.log(`❌ Fallos: ${failed}`);
  console.log(`📁 Archivos en: ${OUTPUT_DIR}`);
}

main().catch(err => {
  console.error('❌ Error fatal:', err.message);
  process.exit(1);
});
