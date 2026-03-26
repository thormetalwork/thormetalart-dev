---
description: "Implementar un ticket completo de principio a fin usando TDD: branch → tests → código → review → deploy → cierre"
agent: "full-cycle-developer"
argument-hint: "ID del ticket (ej: TICKET-WP-001)"
---

# /ship — Implementar y Entregar Ticket Completo

Implementa el ticket indicado siguiendo el flujo completo de desarrollo:

## Flujo

### 1. Preparación
- Leer el ticket completo en BACKLOG.md
- Verificar que las dependencias están ✅
- Crear backup: `make backup`
- Verificar stack: `make test`
- Marcar ticket 🔄 EN PROGRESO

### 2. Branch
```bash
git checkout main && git pull
git checkout -b feat/{TICKET-ID}-descripcion
```

### 3. TDD — RED
- Traducir cada criterio de aceptación Gherkin a tests
- Crear archivo de test
- Ejecutar → confirmar que FALLAN
- Commit: `test({TICKET-ID}): Add failing tests`

### 4. TDD — GREEN
- Implementar código mínimo para pasar tests
- Ejecutar → confirmar que PASAN
- Commit: `feat({TICKET-ID}): Implement feature`

### 5. TDD — REFACTOR
- Mejorar código (DRY, nombres, docs)
- Ejecutar → confirmar que SIGUEN PASANDO
- Commit: `refactor({TICKET-ID}): Clean up`

### 6. Code Review
- Checklist de seguridad ✅
- Checklist de funcionalidad ✅
- Checklist de calidad ✅

### 7. Deploy
```bash
make build && make test
```

### 8. Cerrar
- Marcar ✅ COMPLETADO en BACKLOG.md
- Agregar fecha y notas de cierre
- Actualizar resumen

## Output
Ship Report con todos los detalles del delivery.
