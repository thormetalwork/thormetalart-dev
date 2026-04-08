---
description: "Use when editing any development workflow: tickets, branches, commits, PRs, testing, deployment. Covers the continuous development cycle and branching strategy."
applyTo: ["BACKLOG.md", ".github/**"]
---
# Development Workflow — Thor Metal Art

## Branch Strategy
```
main          ← Producción estable (protegida, requiere PR + CI verde)
  └── dev     ← Desarrollo activo (protegida, requiere PR)
       └── feat/TICKET-XXX-descripcion  ← Features (merge → dev via PR)
       └── fix/TICKET-XXX-descripcion   ← Bug fixes (merge → dev via PR)
       └── hotfix/TICKET-XXX-descripcion ← Producción urgente (merge → main via PR)
```

## Branch Policies (GitHub Rulesets)
| Rama | PR Required | CI Required | Force Push | Delete | Merge Method |
|------|-------------|-------------|------------|--------|--------------|
| main | ✅ | ✅ PHP Lint, ESLint, PHPStan | ❌ Blocked | ❌ Blocked | Squash only |
| dev  | ✅ | ❌ (advisory) | ❌ Blocked | ❌ Blocked | Squash only |

## Flow: Feature → dev → main
```
1. git checkout dev && git pull
2. git checkout -b feat/TICKET-XXX-descripcion
3. ... TDD cycle (RED → GREEN → REFACTOR) ...
4. git push -u origin feat/TICKET-XXX-descripcion
5. Create PR: feat/TICKET-XXX → dev (squash merge)
6. CI runs on PR, code review
7. Merge to dev (branch auto-deleted)
8. When dev is stable: Create PR dev → main
9. CI must pass (blocking on main)
10. Squash merge to main
```

## Commit Convention
```
{type}(TICKET-{SCOPE}-{NUM}): Descripción breve

Tipos: feat, fix, refactor, docs, test, style, chore, perf
Ejemplo: feat(TICKET-WP-001): Add child theme with branding
```

## Flujo Continuo por Ticket
```
1. 📋 Crear ticket en BACKLOG.md (o /ticket prompt)
2. 🌿 Crear rama desde dev: git checkout -b feat/TICKET-XXX-descripcion dev
3. 🔴 RED: Escribir tests que fallan
4. 🟢 GREEN: Implementar código mínimo para pasar tests
5. 🔵 REFACTOR: Mejorar manteniendo tests verdes
6. 📝 Commit: feat(TICKET-XXX): Descripción
7. 🚀 Push + PR → dev
8. 🔍 Code Review + CI
9. ✅ Merge (squash) + cerrar ticket en BACKLOG.md
```

## Ticket Lifecycle
```
⏸️ PENDIENTE → 🔄 EN PROGRESO → 🧪 EN TESTING → ✅ COMPLETADO
                  ↓                                    ↑
              🚫 BLOQUEADO ──── (resolver) ───────────┘
```

## Quality Gates (antes de merge a dev)
- [ ] Tests pasan: `make test-all`
- [ ] Lint limpio: `make lint`
- [ ] No hay errores PHPStan: `make lint-phpstan`
- [ ] Formato verificado: `make lint-format`
- [ ] Backup creado (si hay cambios en DB/Docker)
- [ ] Code review completado
- [ ] Criterios de aceptación verificados

## Quality Gates (antes de merge a main)
- [ ] CI verde (PHP Lint, ESLint, PHPStan — blocking)
- [ ] PHPCS sin errores críticos
- [ ] Todos los tests en dev pasando
- [ ] Sin regresiones verificadas
