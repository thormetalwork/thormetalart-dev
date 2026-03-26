---
name: tdd-workflow
description: "Ciclo TDD completo RED → GREEN → REFACTOR para Thor Metal Art. Use when implementing features with test-driven development, writing tests, or validating code changes."
argument-hint: "Feature o ticket a implementar con TDD (ej: TICKET-WP-003 Portfolio CPT)"
---

# TDD Workflow — Thor Metal Art

## Cuándo Usar
- Implementar cualquier feature nueva
- Corregir bugs con test de regresión
- Refactorizar código existente con red de seguridad
- Validar cambios en WordPress (PHP), Dashboard (JS), o Scripts (bash)

## Ciclo RED → GREEN → REFACTOR

### Fase 1: RED — Tests que Fallan

Antes de escribir código, define el comportamiento esperado con tests.

**Para PHP (WordPress):**
```php
// tests/test-{feature}.php
class Test_TMA_{Feature} extends WP_UnitTestCase {
    
    public function test_{escenario}_returns_{resultado}() {
        // Arrange
        $input = '...';
        
        // Act
        $result = tma_{function}($input);
        
        // Assert
        $this->assertEquals($expected, $result);
    }
    
    public function test_{escenario}_rejects_invalid_input() {
        $this->expectException(InvalidArgumentException::class);
        tma_{function}('invalid');
    }
}
```

**Para JavaScript (Dashboard):**
```javascript
// tests/test-{feature}.js
describe('{Feature}', () => {
    test('{escenario} returns {resultado}', () => {
        // Arrange
        const input = {...};
        
        // Act
        const result = featureFunction(input);
        
        // Assert
        expect(result).toBe(expected);
    });
});
```

**Para Bash (Scripts):**
```bash
# tests/test-{feature}.sh
#!/bin/bash
set -e

test_backup_creates_file() {
    # Arrange
    local BACKUP_DIR="/tmp/test-backups"
    mkdir -p "$BACKUP_DIR"
    
    # Act
    bash scripts/backup-database.sh "$BACKUP_DIR"
    
    # Assert
    [[ $(ls "$BACKUP_DIR"/*.sql.gz | wc -l) -gt 0 ]] || { echo "FAIL: No backup created"; exit 1; }
    echo "PASS: Backup created"
}

test_backup_creates_file
```

Ejecutar y confirmar que FALLAN:
```bash
# PHP
docker compose exec wordpress php vendor/bin/phpunit tests/test-{feature}.php

# JS
node tests/test-{feature}.js

# Bash
bash tests/test-{feature}.sh
```

### Fase 2: GREEN — Código Mínimo

Implementar **SOLO** lo necesario para que los tests pasen:
- No optimizar
- No agregar features extra
- No preocuparse por elegancia

```bash
# Verificar que tests pasan
# PHP
docker compose exec wordpress php vendor/bin/phpunit tests/test-{feature}.php
# Resultado esperado: OK (X tests, X assertions)
```

### Fase 3: REFACTOR — Mejorar

Ahora sí: mejorar el código manteniendo tests verdes:
- Eliminar duplicación
- Mejorar nombres
- Agregar type hints / JSDoc
- Optimizar performance
- Aplicar patrones WordPress (hooks, filters)

```bash
# Verificar que tests SIGUEN pasando
docker compose exec wordpress php vendor/bin/phpunit tests/test-{feature}.php
# Resultado: OK (todos verdes)
```

## Ciclo Completo por Ticket

```
1. 📋 Buscar ticket en BACKLOG.md
   grep "TICKET-XXX" BACKLOG.md
   
2. 🔄 Marcar ticket EN PROGRESO
   Cambiar ⏸️ → 🔄 en BACKLOG.md

3. 🌿 Crear rama
   git checkout -b feat/TICKET-XXX-descripcion

4. 🔴 RED: Crear tests → Ejecutar → FALLAN ✅
   - Escribir tests según criterios de aceptación del ticket
   - Cada criterio Gherkin = al menos 1 test

5. 🟢 GREEN: Implementar → Ejecutar → PASAN ✅
   - Código mínimo para pasar todos los tests

6. 🔵 REFACTOR: Mejorar → Ejecutar → SIGUEN PASANDO ✅
   - Limpiar, optimizar, documentar

7. 📝 Commit
   git add -A
   git commit -m "feat(TICKET-XXX): Descripción"

8. 🧪 Marcar EN TESTING en BACKLOG.md

9. 🔍 Code Review (usar /code-review)

10. 🚀 Deploy (usar /deploy)

11. ✅ Marcar COMPLETADO en BACKLOG.md
```

## Reference Files
- [Testing patterns for WordPress](./references/testing-patterns.md)
