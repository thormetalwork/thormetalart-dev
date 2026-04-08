---
name: ship-feature
description: "Implementar un ticket completo de principio a fin: TDD + código + review + deploy. Use when shipping a feature, implementing a ticket end-to-end, or doing the full development cycle."
argument-hint: "Ticket a implementar (ej: TICKET-WP-001 Tema hijo personalizado)"
---

# Ship Feature — Thor Metal Art

## Cuándo Usar
- Implementar un ticket completo de BACKLOG.md
- Llevar una feature desde código hasta deploy
- Ciclo completo: ticket → branch → TDD → review → deploy → close

## Flujo Completo

### Fase 0: Preparación
```bash
# 1. Verificar ticket en BACKLOG.md
grep "TICKET-XXX" BACKLOG.md

# 2. Verificar dependencias
# Si tiene dependencias, confirmar que están ✅ COMPLETADO

# 3. Crear backup de seguridad
make backup

# 4. Verificar stack saludable
make test
```

### Fase 1: Branch
```bash
# Crear rama desde dev (o main si no hay dev)
git checkout main
git pull
git checkout -b feat/TICKET-XXX-descripcion-corta
```

### Fase 2: Marcar En Progreso
Actualizar BACKLOG.md:
- Cambiar `⏸️ PENDIENTE` → `🔄 EN PROGRESO`
- Commit: `chore(TICKET-XXX): Start implementation`

### Fase 3: TDD — RED
- Leer criterios de aceptación del ticket (Gherkin)
- Escribir 1 test por cada scenario
- Ejecutar tests → **deben FALLAR**
- Commit: `test(TICKET-XXX): Add failing tests for {feature}`

### Fase 4: TDD — GREEN
- Implementar código mínimo para pasar tests
- Ejecutar tests → **deben PASAR**
- Commit: `feat(TICKET-XXX): Implement {feature}`

### Fase 5: TDD — REFACTOR
- Mejorar código (DRY, nombres, performance)
- Ejecutar tests → **deben seguir PASANDO**
- Commit: `refactor(TICKET-XXX): Clean up {feature}`

### Fase 5.5: Security Review
- Verificar **security.instructions.md** contra el código nuevo
- Checklist rápido:
  - [ ] Sin `innerHTML` con datos sin sanitizar
  - [ ] Todo `$_GET/$_POST` usa `sanitize_text_field()` / `absint()`
  - [ ] Queries SQL usan `$wpdb->prepare()`
  - [ ] Output escapado: `esc_html()`, `esc_attr()`, `esc_url()`
  - [ ] Nonces en operaciones que cambian estado
  - [ ] Sin credenciales hardcodeadas
  - [ ] Variables shell entrecomilladas en scripts
- Si hay hallazgos: corregir y commit `fix(TICKET-XXX): Security hardening`

### Fase 6: Code Review
- Ejecutar checklist de code review (skill: code-review)
- Corregir hallazgos si los hay
- Commit: `fix(TICKET-XXX): Address review feedback`

### Fase 7: Deploy
```bash
# 1. Merge a main
git checkout main
git merge feat/TICKET-XXX-descripcion-corta

# 2. Deploy
make build
make test

# 3. Verificar en browser
# - dev.thormetalart.com (WordPress)
# - dashboard.thormetalart.server-dev (Dashboard)
```

### Fase 8: Cerrar Ticket
Actualizar BACKLOG.md:
```markdown
  - **Status:** ✅ COMPLETADO
  - **Completado:** {fecha}
  - **Notas de cierre:** {Qué se hizo, decisiones}
```

Commit final: `docs(TICKET-XXX): Close ticket`

### Fase 9: Limpieza
```bash
# Eliminar rama
git branch -d feat/TICKET-XXX-descripcion-corta

# Actualizar tabla de resumen en BACKLOG.md
```

## Diagrama del Flujo

```
┌──────────┐   ┌──────────┐   ┌──────────┐   ┌──────────┐
│  📋 Prep │──▶│ 🌿 Branch│──▶│ 🔴 RED   │──▶│ 🟢 GREEN │
└──────────┘   └──────────┘   └──────────┘   └──────────┘
                                                    │
┌──────────┐   ┌──────────┐   ┌──────────┐   ┌─────┴────┐
│ ✅ Close │◀──│ 🚀 Deploy│◀──│ 🔍 Review│◀──│ 🔵 REFACT│
└──────────┘   └──────────┘   └──────────┘   └────┬─────┘
                                                   │
                                              ┌────┴─────┐
                                              │ 🔒 SecRev│
                                              └──────────┘
```

## Commit History Esperada

```
docs(TICKET-XXX): Close ticket
fix(TICKET-XXX): Address review feedback
refactor(TICKET-XXX): Clean up {feature}
feat(TICKET-XXX): Implement {feature}
test(TICKET-XXX): Add failing tests for {feature}
chore(TICKET-XXX): Start implementation
```
