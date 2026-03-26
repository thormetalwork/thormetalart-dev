---
description: "Use when editing any development workflow: tickets, branches, commits, PRs, testing, deployment. Covers the continuous development cycle and branching strategy."
applyTo: ["BACKLOG.md", ".github/**"]
---
# Development Workflow — Thor Metal Art

## Branch Strategy
```
main          ← Producción estable
  └── dev     ← Desarrollo activo (merge target)
       └── feat/TICKET-XXX-descripcion  ← Features
       └── fix/TICKET-XXX-descripcion   ← Bug fixes
       └── hotfix/TICKET-XXX-descripcion ← Producción urgente
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
2. 🌿 Crear rama: git checkout -b feat/TICKET-XXX-descripcion
3. 🔴 RED: Escribir tests que fallan
4. 🟢 GREEN: Implementar código mínimo para pasar tests
5. 🔵 REFACTOR: Mejorar manteniendo tests verdes
6. 📝 Commit: feat(TICKET-XXX): Descripción
7. 🔍 Code Review: /code-review prompt
8. 🚀 Deploy: /deploy prompt
9. ✅ Cerrar ticket en BACKLOG.md
```

## Ticket Lifecycle
```
⏸️ PENDIENTE → 🔄 EN PROGRESO → 🧪 EN TESTING → ✅ COMPLETADO
                  ↓                                    ↑
              🚫 BLOQUEADO ──── (resolver) ───────────┘
```

## Quality Gates (antes de merge)
- [ ] Tests pasan (si aplica)
- [ ] No hay errores de lint
- [ ] Backup creado (si hay cambios en DB/Docker)
- [ ] Code review completado
- [ ] Criterios de aceptación verificados
