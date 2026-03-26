---
description: "Crear un nuevo ticket de desarrollo completo en BACKLOG.md con historia de usuario, criterios de aceptación Gherkin, y toda la metadata"
agent: "ticket-manager"
argument-hint: "Descripción del ticket (ej: formulario de contacto con tracking de leads)"
---

# Crear Ticket de Desarrollo

Crea un ticket completo en BACKLOG.md para Thor Metal Art.

## Proceso

1. **Analizar** el requerimiento del usuario
2. **Determinar scope** (WP, DOCK, DASH, SEO, INF, DB, CACHE, SEC, DOC, FIX, LEAD, BRAND)
3. **Encontrar** siguiente número disponible: `grep "TICKET-{SCOPE}" BACKLOG.md | tail -3`
4. **Escribir ticket** con TODOS los campos obligatorios:
   - Título descriptivo
   - Fuente del requerimiento
   - Historia de Usuario completa (Como X, quiero Y, para Z)
   - Criterios de Aceptación en formato Gherkin (Given/When/Then)
   - Archivos a modificar (NEW/MODIFIED)
   - Dependencias
   - Estimación en horas
   - Prioridad (P0-P3)
   - Status: ⏸️ PENDIENTE
5. **Agregar** al final de la FASE correcta en BACKLOG.md
6. **Actualizar** tabla de resumen al final del archivo

## Validación (TODOS deben cumplirse)
- [ ] Número consecutivo dentro del scope
- [ ] Historia de usuario con rol, acción y beneficio
- [ ] Al menos 1 escenario Gherkin
- [ ] Archivos identificados
- [ ] Prioridad asignada
- [ ] Fase asignada
