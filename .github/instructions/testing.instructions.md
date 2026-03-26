---
description: "Use when writing or running tests: PHPUnit, JavaScript tests, bash test scripts. Covers TDD patterns, assertions, test structure, and naming conventions."
applyTo: ["tests/**", "**/test-*", "**/test_*"]
---
# Testing Instructions — Thor Metal Art

## Test Naming Convention
- PHP: `test_{escenario}_returns_{resultado}` or `test_{escenario}_throws_{exception}`
- JS: `{escenario} returns {resultado}` (inside describe block)
- Bash: `test_{escenario}` function names

## Test Structure (AAA Pattern)
```
Arrange → Setup del contexto y datos de entrada
Act     → Ejecutar la función/método bajo test
Assert  → Verificar el resultado esperado
```

## Test File Naming
- PHP: `tests/test-{feature}.php`
- JS: `tests/test-{feature}.js`
- Bash: `tests/test-{feature}.sh`

## Mandatory for Every Test
- Descriptive name explaining what is tested
- Single responsibility (1 assertion per test ideally)
- Independent (no order dependency between tests)
- Edge cases covered: empty input, null, zero, max values
- Error cases: invalid input, unauthorized, malformed data

## TDD Cycle Enforcement
1. Write test FIRST → Must FAIL (RED)
2. Implement minimum code → Must PASS (GREEN)
3. Refactor → Must still PASS (REFACTOR)
4. NEVER commit implementation without corresponding tests

## WordPress-Specific
- Extend `WP_UnitTestCase` for WordPress tests
- Use `$this->factory->post->create()` for test data
- Clean up with `wp_delete_post()` in tearDown
- Test hooks with `has_action()` / `has_filter()`

## Coverage Target
- New features: minimum 80% coverage
- Bug fixes: 100% coverage for the bug scenario (regression test)
