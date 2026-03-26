---
description: "Generar tests TDD para una feature o ticket, siguiendo el patrón RED del ciclo TDD"
agent: "tdd-developer"
argument-hint: "Ticket o feature (ej: TICKET-WP-003 Portfolio CPT)"
---

# Generar Tests TDD

Genera tests siguiendo la fase RED del ciclo TDD para Thor Metal Art.

## Proceso

1. **Leer ticket** en BACKLOG.md — extraer criterios de aceptación
2. **Determinar tipo** de tests:
   - PHP (WordPress) → PHPUnit
   - JavaScript (Dashboard) → Vanilla JS tests
   - Bash (Scripts) → Shell assert tests
3. **Crear archivo de test** siguiendo convención:
   - PHP: `tests/test-{feature}.php`
   - JS: `tests/test-{feature}.js`
   - Bash: `tests/test-{feature}.sh`
4. **Escribir tests** — cada escenario Gherkin = mínimo 1 test:
   - Arrange: Setup del contexto
   - Act: Ejecutar la acción
   - Assert: Verificar el resultado
5. **Incluir** edge cases:
   - Input inválido
   - Valores vacíos/null
   - Límites (0, max, overflow)
6. **Ejecutar tests** → confirmar que FALLAN (RED ✅)

## Reglas
- Tests DEBEN fallar (eso confirma que son válidos)
- Nombres descriptivos: `test_{escenario}_returns_{resultado}`
- Un assert por test (idealmente)
- Tests independientes entre sí (no depender del orden)
