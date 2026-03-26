## Ticket

**TICKET-ID:** `TICKET-XXX-YYY`

## Descripción

<!-- Resumen breve del cambio -->

## Tipo de Cambio

- [ ] Feature (nueva funcionalidad)
- [ ] Bug fix (corrección)
- [ ] Refactor (mejora sin cambio de comportamiento)
- [ ] Docs (documentación)
- [ ] Chore (mantenimiento)

## Checklist Pre-Merge

### Tests
- [ ] Tests escritos siguiendo TDD (RED → GREEN → REFACTOR)
- [ ] Todos los tests pasan
- [ ] Edge cases cubiertos

### Seguridad
- [ ] Sin credenciales hardcodeadas
- [ ] Input sanitizado
- [ ] Output escapado
- [ ] SQL usa prepare()

### Calidad
- [ ] Sigue convenciones del proyecto
- [ ] Sin código muerto o debug output
- [ ] Responsive (mobile + desktop)

### Infraestructura
- [ ] `make test` pasa sin errores
- [ ] Backup creado antes del deploy
- [ ] Health checks verificados

### Bilingüe (si aplica)
- [ ] Strings traducibles
- [ ] Contenido en EN y ES

## Screenshots (si aplica)

<!-- Adjuntar capturas si hay cambios visuales -->

## Notas de Deploy

<!-- Pasos especiales necesarios para deploy -->
