---
name: code-review
description: "Revisión de código estructurada con checklist de calidad. Use when reviewing code changes, before merging, or auditing existing code quality."
argument-hint: "Archivo o ticket a revisar (ej: TICKET-WP-001, dashboard/index.html)"
---

# Code Review — Thor Metal Art

## Cuándo Usar
- Antes de merge a rama principal
- Después de implementar una feature
- Auditar código existente
- Validar que un ticket cumple criterios de aceptación

## Checklist de Revisión

### 1. Seguridad
- [ ] No hay credenciales hardcodeadas
- [ ] Input sanitizado en todas las entradas de usuario
- [ ] Output escapado en todas las salidas HTML
- [ ] Queries SQL usan `$wpdb->prepare()`
- [ ] Nonces en todos los formularios
- [ ] No hay `eval()`, `exec()`, o funciones peligrosas
- [ ] File permissions correctos

### 2. Funcionalidad
- [ ] Cumple los criterios de aceptación del ticket
- [ ] Funciona en mobile y desktop
- [ ] Funciona en Chrome, Firefox, Safari
- [ ] Maneja errores gracefully
- [ ] No rompe funcionalidad existente

### 3. Código
- [ ] Sigue convenciones del proyecto (WPCS para PHP, vanilla JS para dashboard)
- [ ] Funciones con prefijo `tma_` (WordPress)
- [ ] Sin código muerto o comentado
- [ ] Sin console.log() o var_dump() de debug
- [ ] DRY — sin duplicación innecesaria

### 4. Performance
- [ ] No hay queries en loops (N+1)
- [ ] Imágenes optimizadas con lazy loading
- [ ] CSS/JS minificados en producción
- [ ] Redis cache utilizado donde corresponde

### 5. SEO (si aplica)
- [ ] Meta title < 60 chars, incluye keyword
- [ ] Meta description 150-160 chars
- [ ] Heading hierarchy H1 → H2 → H3
- [ ] Alt text en imágenes
- [ ] Schema markup válido

### 6. Bilingüe
- [ ] Strings traducibles con `__()` / `_e()`
- [ ] Text domain `thormetalart`
- [ ] Contenido en ambos idiomas (EN/ES)

### 7. Tests
- [ ] Tests existen para la feature
- [ ] Tests pasan (GREEN)
- [ ] Cobertura de edge cases
- [ ] Test de regresión para bugs corregidos

## Procedimiento de Revisión

```
1. Leer el ticket en BACKLOG.md (criterios de aceptación)
2. Revisar el diff de archivos modificados
3. Ejecutar la checklist por categoría
4. Documentar hallazgos:
   - ✅ Aprobado
   - ⚠️ Aprobado con observaciones menores
   - ❌ Requiere cambios (listar específicos)
5. Si hay cambios requeridos, detallar cada uno
```

## Output Format

```markdown
## Code Review: TICKET-{SCOPE}-{NUM}

**Resultado:** ✅ / ⚠️ / ❌

### Hallazgos
| # | Severidad | Archivo | Línea | Descripción | Acción |
|---|-----------|---------|-------|-------------|--------|
| 1 | High | file.php | 42 | SQL sin prepare() | Fix requerido |
| 2 | Low | style.css | 15 | Color no usa variable | Opcional |

### Resumen
- Seguridad: ✅
- Funcionalidad: ✅
- Código: ⚠️
- Performance: ✅
- SEO: N/A
- Tests: ✅
```
