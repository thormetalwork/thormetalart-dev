---
description: "Ver el estado actual del BACKLOG: tickets pendientes, en progreso, completados, y resumen por fase"
agent: "ticket-manager"
---

# Estado del Backlog

Analiza el estado actual del BACKLOG.md de Thor Metal Art:

1. **Resumen rápido:**
   ```bash
   echo "📊 Estado del Backlog Thor Metal Art"
   echo "===================================="
   echo "✅ Completados: $(grep -c '✅ COMPLETADO' BACKLOG.md)"
   echo "🔄 En Progreso: $(grep -c '🔄 EN PROGRESO' BACKLOG.md)"
   echo "🧪 En Testing:  $(grep -c '🧪 EN TESTING' BACKLOG.md)"
   echo "⏸️ Pendientes:  $(grep -c '⏸️ PENDIENTE' BACKLOG.md)"
   echo "🚫 Bloqueados:  $(grep -c '🚫 BLOQUEADO' BACKLOG.md)"
   ```

2. **Tickets P0/P1** que necesitan atención inmediata

3. **Próximo ticket recomendado** basado en prioridad y dependencias

4. **Progreso por fase** (% completado)

5. **Tickets bloqueados** y qué los bloquea

Presenta el resultado como un dashboard de texto con indicadores claros.
