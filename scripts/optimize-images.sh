#!/usr/bin/env bash
# =============================================================================
# scripts/optimize-images.sh
# Comprime imágenes JPEG originales en wp-content/uploads
# usando ImageMagick convert (ya disponible en el host).
#
# Uso:
#   bash scripts/optimize-images.sh [--dry-run] [--dir <ruta>] [--env <dev|prod>]
#
# Flags:
#   --dry-run   Muestra qué haría sin modificar archivos
#   --env       dev (default) | prod  →  selecciona el stack
#   --dir       Ruta relativa dentro de uploads (default: 2026/04)
#
# Qué hace:
#   1. Localiza JPEGs originales (excluye thumbnails *-NxN.jpg)
#   2. Hace backup a <file>.bak (skip si ya existe)
#   3. Re-comprime con ImageMagick quality=82 + strip de metadata EXIF
#   4. Reporta ahorro de bytes
# =============================================================================

set -euo pipefail

# ── Defaults ─────────────────────────────────────────────────────────────────
ENV="dev"
SUBDIR="2026/04"
DRY_RUN=false
QUALITY=82

# ── Argument parsing ──────────────────────────────────────────────────────────
while [[ $# -gt 0 ]]; do
    case "$1" in
        --dry-run)  DRY_RUN=true; shift ;;
        --env)      ENV="$2"; shift 2 ;;
        --dir)      SUBDIR="$2"; shift 2 ;;
        --quality)  QUALITY="$2"; shift 2 ;;
        *) echo "Unknown arg: $1"; exit 1 ;;
    esac
done

# ── Resolve paths ─────────────────────────────────────────────────────────────
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ROOT_DIR="$(dirname "$SCRIPT_DIR")"

if [[ "$ENV" == "prod" ]]; then
    STACK_DIR="/srv/stacks/thormetalart-prod"
else
    STACK_DIR="/srv/stacks/thormetalart-dev"
fi

UPLOADS_DIR="${STACK_DIR}/data/wordpress/wp-content/uploads/${SUBDIR}"

if [[ ! -d "$UPLOADS_DIR" ]]; then
    echo "ERROR: Directorio no encontrado: $UPLOADS_DIR"
    exit 1
fi

# ── Validate dependencies ─────────────────────────────────────────────────────
if ! command -v convert &>/dev/null; then
    echo "ERROR: ImageMagick 'convert' no encontrado. Instalar con: apt-get install imagemagick"
    exit 1
fi

# ── Main ──────────────────────────────────────────────────────────────────────
echo ""
echo "=== TMA Image Optimizer ==="
echo "Entorno  : $ENV"
echo "Directorio: $UPLOADS_DIR"
echo "Calidad  : $QUALITY"
echo "Dry run  : $DRY_RUN"
echo ""

total_before=0
total_after=0
count=0
skipped=0

# Encuentra solo JPEGs originales (excluye thumbnails -NNNxNNN.jpg)
while IFS= read -r -d '' filepath; do
    filename="$(basename "$filepath")"

    # Saltar thumbnails (terminan en -NNNxNNN.jpg)
    if echo "$filename" | grep -qE '\-[0-9]+x[0-9]+\.jpg$'; then
        continue
    fi

    size_before=$(stat -c%s "$filepath" 2>/dev/null || echo 0)
    total_before=$((total_before + size_before))
    kb_before=$((size_before / 1024))

    if "$DRY_RUN"; then
        echo "  [DRY] $filename  ${kb_before}KB  →  ~$((kb_before * QUALITY / 90))KB (estimado)"
        count=$((count + 1))
        continue
    fi

    # Backup si no existe
    if [[ ! -f "${filepath}.bak" ]]; then
        cp "$filepath" "${filepath}.bak"
    else
        skipped=$((skipped + 1))
    fi

    # Re-comprimir: calidad 82, strip EXIF (privacidad + tamaño)
    if ! convert "$filepath" \
        -quality "$QUALITY" \
        -strip \
        -sampling-factor 4:2:0 \
        "${filepath}.tmp" 2>/dev/null; then
        echo "  ⚠ ERROR comprimiendo $filename — se mantiene original"
        rm -f "${filepath}.tmp"
        continue
    fi

    mv "${filepath}.tmp" "$filepath"

    size_after=$(stat -c%s "$filepath" 2>/dev/null || echo 0)
    total_after=$((total_after + size_after))
    kb_after=$((size_after / 1024))
    saved=$(( (size_before - size_after) / 1024 ))
    pct=$(( (size_before - size_after) * 100 / (size_before + 1) ))

    echo "  ✅ $filename  ${kb_before}KB → ${kb_after}KB  (-${saved}KB, -${pct}%)"
    count=$((count + 1))

done < <(find "$UPLOADS_DIR" -maxdepth 1 -name "*.jpg" -type f -print0 | sort -z)

# ── Summary ───────────────────────────────────────────────────────────────────
echo ""
echo "=== Resumen ==="
if "$DRY_RUN"; then
    echo "  Modo DRY RUN — no se modificó ningún archivo"
    echo "  Imágenes que se comprimirían: $count"
else
    if [[ $total_before -gt 0 && $total_after -gt 0 ]]; then
        saved_total=$(( (total_before - total_after) / 1024 ))
        pct_total=$(( (total_before - total_after) * 100 / total_before ))
        before_kb=$((total_before / 1024))
        after_kb=$((total_after / 1024))
        echo "  Imágenes procesadas : $count"
        echo "  Backup existentes   : $skipped (ya tenían .bak)"
        echo "  Antes: ${before_kb}KB  →  Después: ${after_kb}KB"
        echo "  Ahorro total: ${saved_total}KB  (-${pct_total}%)"
    else
        echo "  Imágenes procesadas : $count"
    fi
fi
echo ""
echo "Siguiente paso (si se usó --env prod):"
echo "  Regenerar thumbnails WebP:"
echo "  docker exec tma_${ENV}_wordpress wp --allow-root media regenerate --yes"
echo ""
