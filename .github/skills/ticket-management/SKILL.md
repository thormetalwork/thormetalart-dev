---
name: ticket-management
description: "Crear, actualizar y gestionar tickets en BACKLOG.md con formato estándar. Use when creating tickets, managing backlog, tracking progress, or updating ticket status."
argument-hint: "Acción y descripción (ej: crear ticket para formulario de contacto, actualizar TICKET-WP-001)"
---

# Ticket Management — Thor Metal Art

## Cuándo Usar
- Crear un nuevo ticket de desarrollo
- Actualizar el estado de un ticket existente
- Revisar el backlog y priorizar
- Buscar dependencias entre tickets
- Generar resumen de progreso

## Procedimiento: Crear Ticket

### Paso 1: Determinar siguiente número
```bash
grep -E "^\- \[.\] \*\*TICKET-" BACKLOG.md | tail -5
```

### Paso 2: Seleccionar Scope

| Scope | Área |
|-------|------|
| `WP` | WordPress — temas, plugins, páginas |
| `DOCK` | Docker — compose, containers |
| `DASH` | Dashboard — KPIs, charts, API |
| `SEO` | SEO — meta tags, schema, GBP |
| `INF` | Infraestructura — Traefik, SSL |
| `DB` | Base de datos — MySQL |
| `CACHE` | Cache — Redis |
| `SEC` | Seguridad |
| `DOC` | Documentación |
| `FIX` | Bug fixes |
| `LEAD` | Leads — CRM, pipeline |
| `BRAND` | Branding — diseño |

### Paso 3: Escribir el Ticket

**Template obligatorio:**
```markdown
- [ ] **TICKET-{SCOPE}-{NUM}: {Título Descriptivo}**
  - **Fuente:** [Documento o solicitud que origina este ticket]
  - **Historia de Usuario:** Como {rol}, quiero {acción} para {beneficio}.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: {Escenario principal}
      Given {contexto}
      When {acción}
      Then {resultado esperado}
    
    Scenario: {Escenario secundario}
      Given {contexto}
      When {acción}
      Then {resultado esperado}
    ```
  - **Archivos a Modificar:**
    - `path/to/file.ext` (NEW/MODIFIED/DELETED)
  - **Dependencias:** TICKET-XXX-YYY (si aplica)
  - **Estimación:** X horas
  - **Prioridad:** P0/P1/P2/P3
  - **Status:** ⏸️ PENDIENTE
```

### Paso 4: Ubicar en BACKLOG.md
- Agregar al final de la FASE correspondiente
- Mantener orden numérico dentro del scope

### Paso 5: Validar
- [ ] Número consecutivo (no saltar)
- [ ] Scope válido (de la tabla)
- [ ] Historia de usuario completa ("Como X, quiero Y, para Z")
- [ ] Al menos 1 criterio de aceptación en Gherkin
- [ ] Archivos identificados
- [ ] Dependencias marcadas correctamente
- [ ] Prioridad asignada
- [ ] Actualizar tabla de resumen al final del BACKLOG.md

## Procedimiento: Actualizar Estado

```bash
# Buscar ticket
grep -n "TICKET-WP-001" BACKLOG.md

# Cambiar estado
# ⏸️ PENDIENTE → 🔄 EN PROGRESO → 🧪 EN TESTING → ✅ COMPLETADO
# Agregar fecha de completado si aplica
```

Al completar un ticket, agregar:
```markdown
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-MM-DD
  - **Notas de cierre:** {Qué se hizo, decisiones tomadas}
```

## Procedimiento: Revisar Progreso

```bash
# Tickets pendientes
grep -c "⏸️ PENDIENTE" BACKLOG.md

# Tickets en progreso
grep -c "🔄 EN PROGRESO" BACKLOG.md

# Tickets completados
grep -c "✅ COMPLETADO" BACKLOG.md

# Tickets bloqueados
grep -c "🚫 BLOQUEADO" BACKLOG.md
```

## Reference Files
- [Ticket templates by type](./references/ticket-templates.md)
