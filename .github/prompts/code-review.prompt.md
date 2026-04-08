---
description: "Ejecutar code review estructurado con checklist de seguridad, funcionalidad, calidad, performance y SEO"
agent: "full-cycle-developer"
argument-hint: "Ticket o archivos a revisar (ej: TICKET-WP-001, dashboard/js/app.js)"
---

# Code Review Estructurado

Realiza una revisión de código completa para Thor Metal Art.

## Checklist

### Seguridad
- [ ] No hay credenciales hardcodeadas
- [ ] Input sanitizado (`sanitize_text_field()`, `absint()`)
- [ ] Output escapado (`esc_html()`, `esc_attr()`, `esc_url()`)
- [ ] SQL usa `$wpdb->prepare()`
- [ ] Nonces en formularios
- [ ] Sin funciones peligrosas (`eval`, `exec`, `system`)

### Funcionalidad
- [ ] Cumple criterios de aceptación del ticket
- [ ] Responsive (mobile + desktop)
- [ ] Manejo de errores
- [ ] No rompe funcionalidad existente

### Calidad
- [ ] Convenciones del proyecto (WPCS, prefijo `tma_`)
- [ ] Sin código muerto
- [ ] Sin debug output (`console.log`, `var_dump`)
- [ ] DRY — sin duplicación

### Performance
- [ ] Sin queries en loops (N+1)
- [ ] Lazy loading en imágenes
- [ ] Cache Redis utilizado
- [ ] Assets optimizados

### SEO (si aplica)
- [ ] Meta tags correctos
- [ ] Heading hierarchy
- [ ] Schema markup válido
- [ ] Alt text en imágenes

### Bilingüe
- [ ] Strings con `__()` / `_e()`
- [ ] Text domain `thormetalart`

## Output
Tabla de hallazgos con severidad + resultado global (✅ / ⚠️ / ❌)
