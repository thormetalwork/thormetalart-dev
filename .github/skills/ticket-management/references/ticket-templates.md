# Ticket Templates — Thor Metal Art

## WordPress Feature
```markdown
- [ ] **TICKET-WP-XXX: {Título}**
  - **Fuente:** [Requisito / Diseño]
  - **Historia de Usuario:** Como visitante, quiero {función} para {beneficio}.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: {Escenario}
      Given WordPress con tema Thor Metal Art activo
      When {acción del usuario}
      Then {resultado visible}
      And {criterio SEO/performance}
    ```
  - **Archivos:** `data/wordpress/wp-content/themes/thormetalart/{file}` (NEW)
  - **Prioridad:** P1
  - **Status:** ⏸️ PENDIENTE
```

## Bug Fix
```markdown
- [ ] **TICKET-FIX-XXX: Fix {descripción del bug}**
  - **Fuente:** [Reporte / Monitor]
  - **Bug:** {Descripción del comportamiento actual}
  - **Esperado:** {Comportamiento correcto}
  - **Pasos para Reproducir:**
    1. {Paso 1}
    2. {Paso 2}
    3. {Resultado incorrecto}
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Bug corregido
      Given {contexto que causaba el bug}
      When {acción que lo disparaba}
      Then {comportamiento correcto}
    
    Scenario: Regresión
      Given fix aplicado
      When ejecuto tests existentes
      Then todos los tests pasan
    ```
  - **Archivos:** `{archivo afectado}` (MODIFIED)
  - **Prioridad:** P0
  - **Status:** ⏸️ PENDIENTE
```

## Docker / Infrastructure
```markdown
- [ ] **TICKET-DOCK-XXX: {Título}**
  - **Fuente:** [Operaciones / Seguridad]
  - **Historia de Usuario:** Como DevOps, quiero {cambio} para {beneficio operacional}.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: {Escenario}
      Given docker-compose.yml modificado
      When ejecuto make build
      Then {servicio} reporta healthy
      And make test pasa sin errores
    ```
  - **Pre-requisito:** make backup
  - **Archivos:** `docker-compose.yml` (MODIFIED)
  - **Prioridad:** P1
  - **Status:** ⏸️ PENDIENTE
```

## Dashboard Feature
```markdown
- [ ] **TICKET-DASH-XXX: {Título}**
  - **Fuente:** [Cliente / Métricas]
  - **Historia de Usuario:** Como Karel (cliente), quiero ver {dato/chart} para {decisión de negocio}.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: {Escenario}
      Given dashboard abierto en browser
      When navego a la sección {X}
      Then veo {chart/dato} con datos actualizados
      And es responsive en mobile
    ```
  - **Archivos:** `dashboard/index.html` (MODIFIED), `dashboard/js/app.js` (MODIFIED)
  - **Prioridad:** P2
  - **Status:** ⏸️ PENDIENTE
```

## SEO Task
```markdown
- [ ] **TICKET-SEO-XXX: {Título}**
  - **Fuente:** [Auditoría SEO / Google Search Console]
  - **Historia de Usuario:** Como negocio, quiero {optimización} para {mejorar ranking/visibilidad}.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: {Escenario}
      Given página {URL} publicada
      When Google re-indexa
      Then structured data es válido en Rich Results Test
      And meta description tiene 150-160 caracteres con keyword
    ```
  - **Keywords:** "{keyword principal}"
  - **Prioridad:** P1
  - **Status:** ⏸️ PENDIENTE
```

## Security Task
```markdown
- [ ] **TICKET-SEC-XXX: {Título}**
  - **Fuente:** [Auditoría de seguridad]
  - **Severidad:** Critical/High/Medium/Low
  - **Riesgo:** {Descripción del riesgo}
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Vulnerabilidad remediada
      Given {vector de ataque}
      When {intento de explotación}
      Then {resultado seguro: 403, blocked, etc.}
    ```
  - **Pre-requisito:** make backup
  - **Prioridad:** P0
  - **Status:** ⏸️ PENDIENTE
```
