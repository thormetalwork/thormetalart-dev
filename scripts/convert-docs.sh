#!/usr/bin/env bash
set -euo pipefail

# ─────────────────────────────────────────────────────────
# convert-docs.sh — Convierte documentos del cliente a HTML
# Usa mammoth.js para DOCX → HTML
# ─────────────────────────────────────────────────────────

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"
SOURCE_DIR="$PROJECT_ROOT/docs/cliente"
OUTPUT_DIR="$PROJECT_ROOT/portal/docs"
CSS_FILE="../css/doc-viewer.css"

# Verificar mammoth está disponible
if ! command -v npx &>/dev/null; then
  echo "❌ npx no encontrado. Instala Node.js primero."
  exit 1
fi

if [ ! -d "$PROJECT_ROOT/node_modules/mammoth" ]; then
  echo "❌ mammoth no instalado. Ejecuta: npm install mammoth --save-dev"
  exit 1
fi

mkdir -p "$OUTPUT_DIR"

# ── Mapa de títulos legibles ──
declare -A TITLES=(
  ["01_metodologia_maestra"]="Metodología Maestra"
  ["02_diagnostico_auditoria"]="Diagnóstico y Auditoría Digital"
  ["03_brief_posicionamiento"]="Brief de Posicionamiento Dual"
  ["04_plan_proyecto_checklists"]="Plan de Proyecto & Checklists"
  ["05_checklist_maestro"]="Checklist Maestro de Ejecución"
  ["06_reporte_mensual"]="Plantilla de Reporte Mensual"
  ["07_propuesta_consultoria"]="Propuesta de Consultoría"
  ["08_scripts_comunicacion"]="Scripts de Comunicación"
  ["09_guia_fotografia"]="Guía de Fotografía de Proyectos"
  ["10_copys_sitio_web"]="Copys del Sitio Web"
  ["12_dashboard_arquitectura"]="Arquitectura del Dashboard Digital"
)

converted=0
failed=0

echo "🔄 Convirtiendo documentos DOCX → HTML..."
echo "   Fuente: $SOURCE_DIR"
echo "   Destino: $OUTPUT_DIR"
echo ""

for docx in "$SOURCE_DIR"/*.docx; do
  [ -f "$docx" ] || continue

  basename=$(basename "$docx" .docx)
  html_file="$OUTPUT_DIR/${basename}.html"
  title="${TITLES[$basename]:-$basename}"
  doc_num="${basename%%_*}"

  echo "   📄 $basename → HTML..."

  # Convertir DOCX → HTML body con mammoth
  body_html=$(npx mammoth "$docx" --output-format=html 2>/dev/null) || {
    echo "   ❌ Error convirtiendo $basename"
    ((failed++))
    continue
  }

  # Generar HTML completo con template del portal
  cat > "$html_file" <<HTMLEOF
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>${title} — Thor Metal Art</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;1,400&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="${CSS_FILE}">
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
    <span class="doc-num">Doc ${doc_num}</span>
    <h1>${title}</h1>
  </div>
  <article class="doc-content">
${body_html}
  </article>
  <footer class="doc-footer">
    <a href="../" class="back-link">← Volver al Portal</a>
    <span class="doc-meta">Thor Metal Art — Documento del Proyecto</span>
  </footer>
</main>

</body>
</html>
HTMLEOF

  ((converted++))
  echo "   ✅ $basename"
done

echo ""
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "✅ Convertidos: $converted"
[ "$failed" -gt 0 ] && echo "❌ Fallos: $failed"
echo "📁 Archivos en: $OUTPUT_DIR"
