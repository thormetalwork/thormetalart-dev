# 📋 BACKLOG — Thor Metal Art

> **Source of Truth:** Este archivo es el índice maestro de tickets.
> Cada ticket tiene su historia completa aquí.
> **Última actualización:** 2025-07-15

---

## 🔑 Nomenclatura

`TICKET-{SCOPE}-{NUM}` → Ejemplo: `TICKET-WP-001`

| Scope | Área |
|-------|------|
| `WP` | WordPress — temas, plugins, páginas, contenido |
| `DOCK` | Docker — compose, Dockerfile, contenedores |
| `DASH` | Dashboard — KPIs, charts, API, frontend |
| `SEO` | SEO — meta tags, schema, GBP, keywords |
| `INF` | Infraestructura — Traefik, redes, SSL, servidor |
| `DB` | Base de datos — MySQL, migraciones, backups |
| `CACHE` | Cache — Redis, Object Cache, performance |
| `SEC` | Seguridad — auditorías, hardening, permisos |
| `DOC` | Documentación — docs, reportes, guías |
| `FIX` | Bug fixes — correcciones urgentes |
| `LEAD` | Leads — CRM, pipeline, tracking |
| `BRAND` | Branding — diseño, logo, tipografía, colores |
| `PORTAL` | Portal Cliente — documentos, reportes, visor HTML |
| `PANEL` | Panel TMA — plugin WP, SPA, auth, API REST, roles |

## ⚡ Estado de Tickets

| Icono | Estado | Significado |
|-------|--------|-------------|
| `⏸️` | PENDIENTE | En backlog, no iniciado |
| `🔄` | EN PROGRESO | Trabajo activo |
| `🧪` | EN TESTING | Tests/validación |
| `✅` | COMPLETADO | Implementado y verificado |
| `🚫` | BLOQUEADO | Tiene dependencia no resuelta |
| `❌` | CANCELADO | Ya no es necesario |

## 📊 Prioridades

| Nivel | Significado | SLA |
|-------|-------------|-----|
| `P0` | Crítico — bloqueador de producción | Inmediato |
| `P1` | Importante — esta semana | 3 días |
| `P2` | Medio — este sprint | 1 semana |
| `P3` | Bajo — backlog futuro | Sin SLA |

---

## 📋 FASE 1 — Infraestructura Base (Stack Docker)

- [x] **TICKET-DOCK-001: Stack Docker inicial con WordPress + MySQL + Redis**
  - **Fuente:** Requisito inicial del proyecto
  - **Historia de Usuario:** Como desarrollador, quiero un stack Docker funcional para tener WordPress corriendo en dev.thormetalart.com.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Stack levanta correctamente
      Given docker-compose.yml configurado
      When ejecuto make up
      Then todos los servicios reportan healthy
      And WordPress responde en dev.thormetalart.com
    ```
  - **Archivos:** `docker-compose.yml` (NEW), `docker/wordpress/Dockerfile` (NEW), `.env` (NEW)
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-12

- [x] **TICKET-DOCK-002: Scripts operacionales (backup, restore, test, cache)**
  - **Fuente:** Requisitos operacionales
  - **Historia de Usuario:** Como administrador, quiero scripts para backup/restore/test para operar el stack de forma segura.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Backup funcional
      Given MySQL está healthy
      When ejecuto make backup
      Then se crea archivo .sql.gz en /backups/ con timestamp
      And se mantienen máximo 10 backups
    ```
  - **Archivos:** `scripts/backup-database.sh` (NEW), `scripts/restore-database.sh` (NEW), `scripts/test-connections.sh` (NEW), `scripts/clear-cache.sh` (NEW)
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-12

- [x] **TICKET-DOCK-003: Sistema de IA — Agentes, instrucciones, prompts, skills**
  - **Fuente:** Requisito de desarrollo eficiente
  - **Historia de Usuario:** Como desarrollador, quiero un sistema de IA completo para que cada tarea tenga contexto y guías automáticas.
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-12

---

## 📋 FASE 2 — Dashboard Ejecutivo

- [x] **TICKET-DASH-001: Separar CSS/JS del HTML inline a archivos externos**
  - **Fuente:** Mejores prácticas de desarrollo
  - **Historia de Usuario:** Como desarrollador, quiero CSS/JS en archivos separados para facilitar mantenimiento y cache del browser.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: CSS extraído a archivo externo
      Given dashboard/index.html tiene estilos inline
      When extraigo CSS a dashboard/css/styles.css
      Then el dashboard se ve idéntico
      And el CSS se cachea correctamente en el browser
    
    Scenario: JS extraído a archivo externo
      Given dashboard/index.html tiene scripts inline
      When extraigo JS a dashboard/js/app.js
      Then toda la funcionalidad sigue operativa
      And los charts se renderizan correctamente
    ```
  - **Archivos:** `dashboard/css/styles.css` (NEW), `dashboard/js/app.js` (NEW), `dashboard/index.html` (MODIFIED)
  - **Dependencias:** Ninguna
  - **Estimación:** 3-4 horas
  - **Prioridad:** P2
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2025-07-16
  - **Notas:** CSS variables definidas en :root con fallbacks para branding. JS corregido (syntax error en chartDef, event→data-tab). Media queries responsive añadidos.

- [x] **TICKET-DASH-002: Nginx config para dashboard**
  - **Fuente:** Producción
  - **Historia de Usuario:** Como DevOps, quiero configurar Nginx adecuadamente para servir assets estáticos con compresión y cache headers.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Nginx sirve con gzip
      Given nginx.conf configurado en dashboard/nginx/
      When accedo al dashboard
      Then los assets tienen Content-Encoding: gzip
      And los assets tienen Cache-Control headers
    ```
  - **Archivos:** `dashboard/nginx/default.conf` (NEW), `docker-compose.yml` (MODIFIED)
  - **Dependencias:** TICKET-DASH-001
  - **Estimación:** 2 horas
  - **Prioridad:** P2
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2025-07-16
  - **Notas:** Gzip para CSS/JS/JSON/SVG. Cache 7d para assets estáticos, no-cache para HTML. Security headers (X-Content-Type-Options, X-Frame-Options, Referrer-Policy).

- [x] **TICKET-DASH-003: API proxy backend para datos del dashboard**
  - **Fuente:** Seguridad — API keys no deben estar en frontend
  - **Historia de Usuario:** Como desarrollador, quiero un proxy backend para que las API keys de Google/Instagram no estén expuestas en el JS del cliente.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Proxy backend para Google Business API
      Given API key de Google configurada en .env
      When el dashboard JS hace fetch a /api/gbp
      Then el proxy retorna datos de Google Business Profile
      And la API key nunca se expone al browser
    ```
  - **Archivos:** `dashboard/api/` (NEW), `docker-compose.yml` (MODIFIED)
  - **Dependencias:** TICKET-DASH-001
  - **Estimación:** 6-8 horas
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2025-07-16
  - **Notas:** Node.js Express proxy. Endpoints: /api/health, /api/gbp, /api/ga, /api/ig, /api/leads. Demo data fallback cuando no hay API keys. Nginx hace proxy reverse de /api/ al contenedor dashboard-api.

---

## 📋 FASE 3 — WordPress Sitio Web

- [x] **TICKET-WP-001: Tema hijo personalizado Thor Metal Art**
  - **Fuente:** Diseño del sitio web
  - **Historia de Usuario:** Como diseñador, quiero un tema hijo de WordPress con el branding de Thor Metal Art para tener control total del diseño.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Tema hijo activo con branding
      Given tema hijo creado en wp-content/themes/thormetalart/
      When activo el tema
      Then el sitio muestra colores #1A1A1A, #B8860B, #F5F5F0
      And la tipografía es Cormorant Garamond + DM Sans
    ```
  - **Archivos:** `data/wordpress/wp-content/themes/thormetalart/` (NEW)
  - **Estimación:** 8-12 horas
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2025-07-16
  - **Notas:** Child theme de twentytwentyfive (block/FSE). theme.json v3 con paleta de colores, tipografía responsive (clamp), botones gold. Google Fonts: Cormorant Garamond + DM Sans.

- [x] **TICKET-WP-002: Páginas de servicios (Gates, Railings, Fences, Furniture, Stairs)**
  - **Fuente:** docs/README.md — Arquitectura del sitio
  - **Historia de Usuario:** Como visitante, quiero ver páginas dedicadas a cada servicio para encontrar fácilmente lo que busco y contactar a Thor Metal Art.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Cada servicio tiene página dedicada
      Given 5 servicios definidos
      When navego a /custom-metal-gates-miami/
      Then veo contenido bilingüe con SEO optimizado
      And hay CTA visible para solicitar cotización
    ```
  - **Dependencias:** TICKET-WP-001
  - **Estimación:** 12-16 horas
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-12
  - **Notas:** mu-plugin tma-service-pages.php crea 5 páginas con block editor content bilingüe EN/ES, CTA gold, process columns. Auto-crea al activar tema o vía admin action.

- [x] **TICKET-WP-003: Custom Post Type — Portfolio**
  - **Fuente:** Requisito de negocio
  - **Historia de Usuario:** Como Karel, quiero mostrar mi portafolio de trabajos para que los clientes vean la calidad y variedad de mi trabajo.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Portfolio con galería de imágenes
      Given CPT tma_portfolio registrado
      When creo un proyecto nuevo
      Then puedo agregar galería, descripción, categoría y ubicación
      And se muestra en /portfolio/ con grid responsive
    ```
  - **Archivos:** `data/wordpress/wp-content/mu-plugins/tma-post-types.php` (NEW)
  - **Dependencias:** TICKET-WP-001
  - **Estimación:** 6-8 horas
  - **Prioridad:** P2
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-12
  - **Notas:** CPT tma_portfolio con taxonomía tma_project_type (Gates, Railings, Fences, Furniture, Stairs, Art). Meta fields: location, year, material. Block editor template con image + gallery. Rewrite flush automático.

---

## 📋 FASE 4 — SEO y Presencia Digital

- [x] **TICKET-SEO-001: Schema markup LocalBusiness + Service**
  - **Fuente:** Mejores prácticas SEO local
  - **Historia de Usuario:** Como negocio, quiero schema markup en todas las páginas para mejorar la visibilidad en Google Search.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Schema válido en todas las páginas
      Given JSON-LD implementado en wp_head
      When Google valida con Schema Markup Testing Tool
      Then LocalBusiness schema es válido sin errores
      And cada servicio tiene Service schema propio
    ```
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-12
  - **Notas:** mu-plugin tma-schema.php. LocalBusiness global con OfferCatalog de 5 servicios. Service schema individual en cada página de servicio. JSON-LD con wp_json_encode.

- [x] **TICKET-SEO-002: Meta tags y Open Graph para todas las páginas**
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-12
  - **Notas:** mu-plugin tma-meta-tags.php. Meta description dinámico, Open Graph (og:title, og:description, og:image, og:url), Twitter Card summary_large_image, hreflang EN/ES, canonical URL.

---

## 📋 FASE 5 — Seguridad y Hardening

- [x] **TICKET-SEC-001: WordPress security hardening**
  - **Fuente:** Auditoría de seguridad
  - **Historia de Usuario:** Como administrador, quiero WordPress hardened para proteger contra ataques comunes.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: XML-RPC deshabilitado
      Given WordPress instalado
      When hago POST a xmlrpc.php
      Then recibo 403 Forbidden
    
    Scenario: REST API restringida
      Given usuario no autenticado
      When pido /wp-json/wp/v2/users
      Then recibo 401 Unauthorized
    ```
  - **Prioridad:** P0
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-12
  - **Notas:** mu-plugin tma-security.php. 9 medidas: XML-RPC deshabilitado, REST API restringida (solo oembed/health), versión WP oculta, file-edit bloqueado, author enumeration bloqueado, security headers, app passwords restringidos, login rate-limiting (5 intentos/15min).

---

## 📋 FASE 6 — Leads y CRM

- [x] **TICKET-LEAD-001: Formulario de contacto con tracking**
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-12
  - **Notas:** mu-plugin tma-contact-form.php (534 líneas). Shortcode [tma_contact_form lang="es|en"]. Tabla custom tma_leads con tracking UTM/referrer/IP hash. AJAX submit con nonce + honeypot + rate limiting. Página admin Leads con pipeline (new/contacted/quoted/won/lost). Email notificación admin. REST endpoint /tma/v1/leads/stats para dashboard. CSS inline responsive con branding Thor Metal Art.

---

## � FASE 7 — Portal Cliente: Documentos Visualizables

- [x] **TICKET-PORTAL-001: Script de conversión DOCX → HTML con template del portal**
  - **Fuente:** Solicitud del cliente — documentos descargables deben ser visualizables en browser
  - **Historia de Usuario:** Como cliente, quiero ver los documentos del proyecto directamente en el navegador para no tener que descargar archivos DOCX.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Conversión exitosa de 11 DOCX a HTML
      Given 11 archivos DOCX en docs/cliente/
      When ejecuto el script de conversión
      Then se generan 11 archivos HTML en portal/docs/
      And cada HTML usa el template visual del portal (dark/gold)
      And el contenido es legible y bien formateado

    Scenario: HTML generado mantiene estructura del documento
      Given un DOCX con headings, listas y tablas
      When se convierte a HTML
      Then los headings mantienen jerarquía (h1, h2, h3)
      And las tablas son responsive
      And tiene navegación "← Volver al Portal"
    ```
  - **Archivos:**
    - `scripts/convert-docs.js` (NEW) — Script Node.js de conversión usando mammoth.js
    - `portal/docs/*.html` (NEW) — 11 HTMLs generados
    - `portal/css/doc-viewer.css` (NEW) — Estilos del visor de documentos
  - **Dependencias:** Ninguna
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-12
  - **Notas:** mammoth.js convierte DOCX→HTML, Node.js script envuelve en template con branding dark/gold, Google Fonts, nav. 11 docs convertidos.

- [x] **TICKET-PORTAL-002: Convertir XLSX (tracker leads) a HTML tabla interactiva**
  - **Fuente:** Solicitud del cliente
  - **Historia de Usuario:** Como cliente, quiero ver el tracker de leads como una tabla HTML interactiva para consultar el estado de mis leads sin descargar Excel.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: XLSX convertido a tabla HTML funcional
      Given archivo 11_tracker_leads.xlsx en docs/cliente/
      When ejecuto la conversión
      Then se genera portal/docs/11_tracker_leads.html
      And la tabla es responsive con scroll horizontal en mobile
      And mantiene el formato visual del portal

    Scenario: Tabla con funcionalidad básica
      Given la tabla HTML generada
      When el cliente la visualiza
      Then puede ordenar columnas haciendo click en headers
      And puede buscar/filtrar texto en la tabla
    ```
  - **Archivos:**
    - `scripts/convert-xlsx.js` (NEW) — Script Node.js para conversión XLSX
    - `portal/docs/11_tracker_leads.html` (NEW)
  - **Dependencias:** TICKET-PORTAL-001
  - **Prioridad:** P2
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-12
  - **Notas:** 3 hojas convertidas (Tracker, Dashboard Métricas, Estado Proyecto) con tabs, búsqueda por texto y sort por columna. Librería xlsx.

- [x] **TICKET-PORTAL-003: Convertir PPTX (kickoff deck) a HTML slides**
  - **Fuente:** Solicitud del cliente
  - **Historia de Usuario:** Como cliente, quiero ver la presentación kickoff como slides HTML para revisarla sin PowerPoint.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: PPTX convertido a slides HTML
      Given archivo thor_kickoff_deck.pptx en docs/cliente/
      When ejecuto la conversión
      Then se genera portal/docs/thor_kickoff_deck.html
      And cada slide es navegable (anterior/siguiente)
      And mantiene el estilo visual del portal

    Scenario: Navegación entre slides
      Given el visor de slides HTML
      When uso flechas o botones
      Then puedo navegar entre todas las slides
      And veo indicador de slide actual (ej: 3/12)
    ```
  - **Archivos:**
    - `scripts/convert-pptx.py` (NEW) — Script Python para conversión PPTX
    - `portal/docs/thor_kickoff_deck.html` (NEW)
  - **Dependencias:** TICKET-PORTAL-001
  - **Prioridad:** P2
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-12
  - **Notas:** python-pptx extrae texto de 8 slides. HTML con navegación prev/next, dots, teclado (flechas), indicador de slide.

- [x] **TICKET-PORTAL-004: Actualizar portal — botón "Ver" en vez de "Descargar" + navegación**
  - **Fuente:** Solicitud del cliente
  - **Historia de Usuario:** Como cliente, quiero botones "Ver Documento" en el portal para abrir los documentos en el navegador en vez de descargarlos.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Botones actualizados en portal
      Given portal/index.html con botones "Descargar"
      When actualizo los links
      Then cada documento tiene botón "Ver Documento"
      And el link abre portal/docs/{nombre}.html
      And no tiene atributo download

    Scenario: Navegación coherente
      Given un documento abierto en el visor
      When hago click en "← Volver al Portal"
      Then regreso a la sección de documentos del portal
      And la URL es portal.thormetalart.com
    ```
  - **Archivos:**
    - `portal/index.html` (MODIFIED) — Cambiar botones de descarga a visualización
  - **Dependencias:** TICKET-PORTAL-001
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-12
  - **Notas:** 12 botones cambiados de "Descargar" (.docx/.xlsx download) a "Ver" (.html). Agregado 13° card para Kickoff Deck (PPTX). SVG ícono cambiado de flecha descarga a ojo.

---

## � FASE 8 — TMA Panel: Plugin WordPress (Arquitectura Base)

> **Referencia:** Análisis comparativo con RAI Panel (RunArt Inside) — proyecto hermano en `/srv/stacks/runartinside`
> **Objetivo:** Migrar Dashboard + Portal estáticos a un plugin WordPress nativo (`tma-panel`) con autenticación, roles, persistencia y API REST.
> **URL objetivo:** `panel.thormetalart.com` (reemplaza `dashboard.thormetalart.com` + `portal.thormetalart.com`)

- [x] **TICKET-PANEL-001: Plugin scaffold + routing por subdominio + Traefik**
  - **Fuente:** Análisis comparativo RAI Panel v0.4.0
  - **Historia de Usuario:** Como desarrollador, quiero el plugin `tma-panel` creado con routing propio para que `panel.thormetalart.com` sirva el panel sin interferir con el sitio principal.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Plugin creado con estructura base
      Given WordPress activo en dev.thormetalart.com
      When creo el plugin tma-panel en wp-content/plugins/
      Then el plugin se activa sin errores
      And tiene bootstrap file tma-panel.php con headers válidos
      And define constantes TMA_PANEL_VERSION, TMA_PANEL_PATH, TMA_PANEL_URL, TMA_PANEL_HOST

    Scenario: Routing por subdominio funcional
      Given Traefik label configurado para panel.thormetalart.com
      When accedo a panel.thormetalart.com
      Then se carga templates/panel.php (shell del SPA)
      And las rutas /wp-json, /wp-admin, /wp-login pasan a WordPress normalmente

    Scenario: No interfiere con sitio principal
      Given el plugin activo
      When accedo a dev.thormetalart.com
      Then el sitio WordPress funciona normalmente sin cambios
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/tma-panel.php` (NEW)
    - `data/wordpress/wp-content/plugins/tma-panel/templates/panel.php` (NEW)
    - `data/wordpress/wp-content/plugins/tma-panel/templates/login.php` (NEW)
    - `data/wordpress/wp-content/plugins/tma-panel/includes/class-tma-panel-router.php` (NEW)
    - `data/wordpress/wp-content/plugins/tma-panel/assets/css/panel.css` (NEW)
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/panel.js` (NEW)
    - `docker-compose.yml` (MODIFIED) — Traefik label para panel.thormetalart.com
  - **Dependencias:** Ninguna
  - **Prioridad:** P0
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-26
  - **Notas de cierre:** Plugin activado, routing funcional (login 200, root redirect 302→/login), 5 security headers verificados, sitio principal no afectado (200). .htaccess faltaba reglas de rewrite — corregido. .gitignore ajustado para trackear plugin y mu-plugins.

- [x] **TICKET-PANEL-002: Roles y capabilities (tma_admin / tma_client)**
  - **Fuente:** Análisis comparativo RAI Panel — class-rai-panel-roles.php
  - **Historia de Usuario:** Como desarrollador, quiero roles `tma_admin` y `tma_client` registrados con capabilities específicas para controlar acceso por módulo del panel.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Roles registrados al activar plugin
      Given plugin tma-panel activado
      When verifico roles de WordPress
      Then existe rol tma_admin con todas las capabilities del panel
      And existe rol tma_client con capabilities limitadas (sin gestión usuarios, sin audit log, sin toggle visibilidad)

    Scenario: Capabilities granulares por módulo
      Given roles registrados
      When listo capabilities de tma_admin
      Then incluye: tma_view_panel, tma_manage_docs, tma_manage_leads, tma_manage_notes, tma_view_audit, tma_export, tma_manage_kpis, tma_toggle_visibility
      And tma_client tiene todas excepto: tma_view_audit, tma_toggle_visibility, tma_manage_kpis

    Scenario: Usuarios creados con roles correctos
      Given roles registrados
      When creo usuario Karel con rol tma_client
      Then Karel puede ver panel, documentos, leads, dejar notas
      And Karel NO puede ver audit log ni gestionar KPIs
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/includes/class-tma-panel-roles.php` (NEW)
  - **Dependencias:** TICKET-PANEL-001
  - **Prioridad:** P0
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-26
  - **Notas de cierre:** tma_admin (11 caps: read, upload, edit + 8 panel caps). tma_client (6 caps: read + 5 panel caps, sin audit/toggle/kpis). Administrator hereda caps del panel. 32 tests pasan.

- [x] **TICKET-PANEL-003: Custom tables + migration system**
  - **Fuente:** Análisis comparativo RAI Panel — class-rai-panel-data.php + migrations/
  - **Historia de Usuario:** Como desarrollador, quiero tablas custom con sistema de migrations para persistir datos del panel (leads, notas, KPIs, audit, documentos).
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Tablas creadas al activar plugin
      Given plugin tma-panel activado
      When verifico tablas MySQL
      Then existen: tma_panel_leads, tma_panel_notes, tma_panel_kpis, tma_panel_audit, tma_panel_docs
      And cada tabla tiene estructura correcta con índices

    Scenario: Sistema de migrations versionado
      Given opción tma_panel_db_version en wp_options
      When agrego migration 002-xxx.php en migrations/
      Then al recargar, se ejecuta automáticamente si versión > actual
      And se actualiza tma_panel_db_version

    Scenario: Seed de datos iniciales
      Given tablas creadas vacías
      When se ejecuta migration 001-initial
      Then se insertan los 12 documentos del portal con status 'pending'
      And se insertan KPIs demo para meses Sep-Feb
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/includes/class-tma-panel-data.php` (NEW)
    - `data/wordpress/wp-content/plugins/tma-panel/migrations/001-initial.php` (NEW)
  - **Dependencias:** TICKET-PANEL-001
  - **Prioridad:** P0
  - **Status:** ✅ COMPLETADO (2026-03-26)
  - **Notas:** 5 tablas con dbDelta, migration runner versionado, 13 docs + 36 KPIs seed. 40/40 tests.

- [x] **TICKET-PANEL-004: REST API endpoints base**
  - **Fuente:** Análisis comparativo RAI Panel — class-rai-panel-api.php
  - **Historia de Usuario:** Como frontend SPA, quiero endpoints REST con autenticación para leer/escribir datos del panel.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Namespace y endpoints registrados
      Given plugin activo con tablas creadas
      When listo REST routes
      Then existe namespace tma-panel/v1
      And existen: GET /dashboard, GET /documents, GET /leads, GET/POST /notes, GET /audit, GET /export

    Scenario: Autenticación requerida
      Given usuario no autenticado
      When hago GET /tma-panel/v1/dashboard
      Then recibo 401 Unauthorized

    Scenario: Filtrado por rol
      Given usuario Karel con rol tma_client
      When hace GET /tma-panel/v1/audit
      Then recibe 403 Forbidden
      And cuando hace GET /tma-panel/v1/dashboard recibe 200 OK

    Scenario: Sanitización y prepared statements
      Given cualquier endpoint que acepta input
      When envío payload con SQL injection o XSS
      Then el input es sanitizado (sanitize_text_field, wp_kses_post)
      And todas las queries usan $wpdb->prepare()
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/includes/class-tma-panel-api.php` (NEW)
  - **Dependencias:** TICKET-PANEL-002, TICKET-PANEL-003
  - **Prioridad:** P0
  - **Status:** ✅ COMPLETADO (2026-03-26)
  - **Notas:** 6 endpoints tma-panel/v1, auth 401/403, sanitize+prepare, POST /notes. 34/34 tests.

- [x] **TICKET-PANEL-005: Login template custom branded**
  - **Fuente:** Análisis comparativo RAI Panel — templates/login.php, SEC-003 a SEC-009
  - **Historia de Usuario:** Como usuario del panel, quiero ver un formulario de login branded cuando accedo sin autenticar, para que la experiencia sea profesional y no exponga WordPress.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Login branded sin WordPress visible
      Given usuario no autenticado
      When accede a panel.thormetalart.com
      Then ve formulario de login con branding Thor Metal Art (dark theme, oro #B8860B)
      And sin referencias visuales a WordPress
      And tiene campos: email, contraseña, checkbox "recordarme"

    Scenario: Autenticación via REST endpoint
      Given formulario de login visible
      When ingresa credenciales válidas con rol tma_client o tma_admin
      Then redirect a panel.thormetalart.com/ (dashboard)
      And se establece cookie de autenticación

    Scenario: Roles bloqueados
      Given usuario con rol subscriber (no tma_*)
      When intenta login en el panel
      Then recibe error "Tu cuenta no tiene permisos para acceder al panel"

    Scenario: Seguridad
      Given formulario de login
      When se intentan 5 logins fallidos en 1 minuto
      Then se bloquea por 15 minutos
      And se registra en audit log

    Scenario: Recuperación de contraseña
      Given link "¿Olvidaste tu contraseña?" visible
      When el usuario hace click
      Then ve formulario branded para recuperar contraseña
      And el email de reset funciona correctamente
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/templates/login.php` (NEW)
    - `data/wordpress/wp-content/plugins/tma-panel/templates/forgot-password.php` (NEW)
    - `data/wordpress/wp-content/plugins/tma-panel/templates/reset-password.php` (NEW)
  - **Dependencias:** TICKET-PANEL-001, TICKET-PANEL-002
  - **Prioridad:** P0
  - **Status:** ✅ COMPLETADO (2025-07-24)
  - **Notas:** Login branded dark/gold, forgot/reset password, rate limiting 5 intentos/15 min transients, 26/26 tests.

- [x] **TICKET-PANEL-006: Frontend SPA shell + sidebar + navegación**
  - **Fuente:** Análisis comparativo RAI Panel — panel.php + panel.js + panel.css
  - **Historia de Usuario:** Como usuario autenticado, quiero una interfaz SPA con sidebar de navegación para acceder a todas las secciones del panel.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Shell SPA cargado tras login
      Given usuario autenticado con rol tma_client
      When accede a panel.thormetalart.com
      Then ve sidebar con secciones: Dashboard, Documentos, Leads, Notas
      And el contenido se carga dinámicamente via API REST (sin page reload)
      And window.TMA_PANEL tiene: apiBase, nonce, user (id, name, role, isAdmin)

    Scenario: Sidebar responsive con hamburger
      Given panel cargado en viewport < 768px
      When hace click en botón hamburger
      Then sidebar se despliega como overlay con transición suave
      And click fuera cierra el sidebar

    Scenario: Routing por hash
      Given panel cargado
      When navega a #documents
      Then se carga la sección de documentos
      And la URL es panel.thormetalart.com/#documents
      And el nav-link de documentos se marca como activo

    Scenario: Branding Thor Metal Art
      Given panel cargado
      Then colores son: fondo #0c0a09, texto #f5f5f4, acento #B8860B
      And tipografía: Cormorant Garamond (display) + Inter/DM Sans (body)
      And min-height touch targets: 44px en mobile
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/templates/panel.php` (MODIFIED)
    - `data/wordpress/wp-content/plugins/tma-panel/assets/css/panel.css` (NEW)
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/panel.js` (NEW)
  - **Dependencias:** TICKET-PANEL-004, TICKET-PANEL-005
  - **Prioridad:** P0
  - **Status:** ✅ COMPLETADO (2025-07-24)
  - **Notas:** SPA con 5 secciones API-driven, KPI grid, tablas, notes form, badges. 42/42 tests.

- [x] **TICKET-PANEL-007: i18n ES/EN con diccionario JS**
  - **Fuente:** Análisis comparativo RAI Panel — i18n.js (325 líneas, ~160 strings)
  - **Historia de Usuario:** Como usuario del panel, quiero poder cambiar el idioma de la interfaz entre español e inglés.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Selector de idioma funcional
      Given panel cargado
      When hace click en botón "EN"
      Then toda la interfaz cambia a inglés (sidebar, headers, labels, status)
      And la preferencia se guarda en localStorage

    Scenario: Idioma persiste entre sesiones
      Given usuario eligió inglés
      When cierra y abre el panel
      Then la interfaz carga en inglés automáticamente

    Scenario: Diccionario completo
      Given diccionario i18n.js
      When reviso las claves
      Then todas las strings de UI tienen traducción ES y EN
      And panel.js usa t('key') en vez de strings hardcoded
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/i18n.js` (NEW)
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/panel.js` (MODIFIED)
  - **Dependencias:** TICKET-PANEL-006
  - **Prioridad:** P2
  - **Status:** ✅ COMPLETADO (2025-07-24)
  - **Notas:** 80+ keys ES/EN, t() function, localStorage persistence, lang switch init. 25/25 tests.

- [x] **TICKET-PANEL-008: Audit log — registro de acciones + rotación**
  - **Fuente:** Análisis comparativo RAI Panel — class-rai-panel-audit.php
  - **Historia de Usuario:** Como admin, quiero un log de todas las acciones del panel para saber quién hizo qué y detectar comportamiento sospechoso.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Acciones registradas automáticamente
      Given usuario autenticado en el panel
      When realiza acciones (login, ver dashboard, ver documento, actualizar lead, crear nota)
      Then cada acción se registra con: user_id, action, target, IP, user_agent, timestamp

    Scenario: Vista audit log para admin
      Given usuario con rol tma_admin
      When navega a sección Audit
      Then ve las últimas 50 acciones con usuario, fecha, acción y target

    Scenario: Detección de patrones sospechosos
      Given usuario consulta >10 documentos en 1 minuto
      When se detecta el patrón
      Then se registra acción 'suspicious_pattern'
      And se envía email de alerta al admin

    Scenario: Rotación automática
      Given registros de audit con más de 90 días
      When se ejecuta el cron diario
      Then se eliminan registros >90 días
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/includes/class-tma-panel-audit.php` (NEW)
  - **Dependencias:** TICKET-PANEL-003
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO (2025-07-24)
  - **Notas:** TMA_Panel_Audit class, log/get_entries/cleanup, cron diario 90 días, IP+UA en details JSON. 15/15 tests.

- [x] **TICKET-PANEL-009: Security hardening — headers, CORS, session, wp-admin block**
  - **Fuente:** Análisis comparativo RAI Panel — SEC-001 a SEC-007 + PANEL-014
  - **Historia de Usuario:** Como desarrollador, quiero que el panel tenga headers de seguridad, CORS y sesiones configuradas para proteger contra ataques.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Headers de seguridad
      Given request al panel
      When reviso response headers
      Then incluye: X-Content-Type-Options: nosniff, X-Frame-Options: DENY, Referrer-Policy: strict-origin, X-Robots-Tag: noindex
      And Permissions-Policy: camera=(), microphone=(), geolocation=()

    Scenario: Roles TMA bloqueados de wp-admin
      Given usuario Karel con rol tma_client
      When intenta acceder a dev.thormetalart.com/wp-admin/
      Then es redirigido a panel.thormetalart.com

    Scenario: Admin bar oculta para roles TMA
      Given usuario con rol tma_client o tma_admin
      When navega el sitio principal
      Then NO ve la barra de admin de WordPress

    Scenario: Session timeout
      Given usuario TMA autenticado
      When pasan 12 horas sin actividad
      Then la sesión expira y debe re-autenticarse
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/includes/class-tma-panel-router.php` (MODIFIED)
    - `data/wordpress/wp-content/plugins/tma-panel/tma-panel.php` (MODIFIED)
  - **Dependencias:** TICKET-PANEL-001, TICKET-PANEL-002
  - **Prioridad:** P0
  - **Status:** ✅ COMPLETADO (2026-03-26)
  - **Notas:** CSP header, admin bar hide, session 12h, CORS, wp-admin redirect. 18/18 tests.

- [x] **TICKET-PANEL-010: Export resumen del proyecto**
  - **Fuente:** Análisis comparativo RAI Panel — class-rai-panel-export.php
  - **Historia de Usuario:** Como usuario del panel, quiero exportar un resumen consolidado del estado del proyecto para tener registro offline.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Botón de export en dashboard
      Given panel en sección dashboard
      When hago click en "Exportar resumen"
      Then se genera texto plano con: estado de documentos, leads pipeline, notas, KPIs
      And se copia al clipboard con confirmación visual

    Scenario: Contenido del export
      Given datos del proyecto en la DB
      When genero el export
      Then incluye: header con fecha, sección documentos (aprobados/pendientes), sección leads (pipeline value, estados), sección KPIs (últimos datos), sección notas (últimas N)
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/includes/class-tma-panel-export.php` (NEW)
  - **Dependencias:** TICKET-PANEL-004
  - **Prioridad:** P3
  - **Status:** ✅ COMPLETADO (2026-03-26)
  - **Notas:** Export class en texto plano, endpoint /export con summary, botón dashboard + clipboard, i18n export. 17/17 tests.

---

## 📋 FASE 9 — TMA Panel: Dashboard con Datos Reales

> **Objetivo:** Reemplazar datos demo del dashboard actual con datos reales persistentes en DB, consultados via REST API del plugin.

- [x] **TICKET-DASH-004: Dashboard section — KPIs desde DB**
  - **Fuente:** Migración del dashboard estático actual
  - **Historia de Usuario:** Como Karel, quiero ver mis métricas de negocio reales en el dashboard para tomar decisiones informadas.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: KPIs renderizados desde API
      Given datos de KPIs almacenados en tma_panel_kpis
      When cargo la sección dashboard del panel
      Then veo KPI cards con: reviews GBP, impressions, sessions web, leads totales
      And cada KPI muestra tendencia (up/down/neutral) vs mes anterior

    Scenario: Gráficos con datos históricos
      Given KPIs de 6 meses en la DB
      When veo el dashboard
      Then el gráfico de impressions muestra tendencia mensual
      And el gráfico de leads muestra distribución por canal
      And se usa Chart.js 4.x con branding Thor (oro + negro)

    Scenario: Datos demo como fallback
      Given tabla tma_panel_kpis vacía (sin datos reales)
      When cargo el dashboard
      Then muestra datos demo con badge "(Datos de ejemplo)" visible
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/panel.js` (MODIFIED)
  - **Dependencias:** TICKET-PANEL-004, TICKET-PANEL-006
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO (2026-03-26)
  - **Notas:** KPI cards reales + tendencia (up/down/neutral), gráficos Chart.js (impressions y canales), fallback demo visible. 10/10 tests.

- [x] **TICKET-DASH-005: Cron job — fetch periódico de APIs externas (GBP, GA4, IG)**
  - **Fuente:** Eliminación de dependencia en Node.js proxy
  - **Historia de Usuario:** Como sistema, quiero consultar APIs externas periódicamente y guardar en DB para que el dashboard no dependa de llamadas en tiempo real.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Cron diario de Google Business Profile
      Given GBP_API_KEY configurada en wp-config.php o .env
      When se ejecuta el cron wp_schedule_event('daily')
      Then se consultan impressions, reviews, actions de GBP API
      And se guardan en tma_panel_kpis con mes + fuente 'gbp'

    Scenario: Cron diario de Google Analytics 4
      Given GA4 credentials configuradas
      When se ejecuta el cron
      Then se guardan sessions, users, conversion_rate, top_pages
      And se guardan en tma_panel_kpis con fuente 'ga4'

    Scenario: Cron diario de Instagram
      Given IG_ACCESS_TOKEN configurado
      When se ejecuta el cron
      Then se guardan followers, reach, engagement
      And se guardan en tma_panel_kpis con fuente 'instagram'

    Scenario: API keys no configuradas
      Given una API key faltante
      When se ejecuta el cron
      Then registra warning en log pero no falla
      And los datos existentes en DB se mantienen
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/includes/class-tma-panel-cron.php` (NEW)
  - **Dependencias:** TICKET-PANEL-003
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO (2026-03-26)
  - **Notas:** Cron diario tma_panel_sync_external_kpis, sync GBP/GA4/Instagram hacia panel_kpis, warnings por keys faltantes sin fallar. 14/14 tests.

- [x] **TICKET-DASH-006: Sección Google Business Profile en panel**
  - **Fuente:** Migración de tab GBP del dashboard actual
  - **Historia de Usuario:** Como Karel, quiero ver el rendimiento de mi perfil de Google Business para saber si estoy ganando visibilidad local.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: KPIs de GBP
      Given datos GBP en tma_panel_kpis
      When navego a sección GBP del panel
      Then veo: rating, total reviews, impressions (search vs maps), acciones (clicks, calls, directions)

    Scenario: Gráfico de impressions
      Given datos mensuales de impressions
      When veo la sección GBP
      Then hay gráfico stacked bar (Search vs Maps) con 6 meses de historia
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/panel.js` (MODIFIED)
  - **Dependencias:** TICKET-DASH-004
  - **Prioridad:** P2
  - **Status:** ✅ COMPLETADO (2026-03-26)
  - **Notas:** Bloque GBP en API dashboard, KPIs rating/reviews/impressions/actions, gráfico stacked Search vs Maps en Chart.js. 7/7 tests.

- [x] **TICKET-DASH-007: Sección Web Analytics (GA4) en panel**
  - **Fuente:** Migración de tab Web del dashboard actual
  - **Historia de Usuario:** Como Karel, quiero ver el tráfico de mi sitio web para entender qué páginas atraen más visitantes.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: KPIs de GA4
      Given datos GA4 en tma_panel_kpis
      When navego a sección Web
      Then veo: sessions, users, conversion_rate, forms_submitted, avg_time
      And veo top 5 páginas con progress bars

    Scenario: Gráfico de sesiones
      Given datos semanales de sessions
      When veo la sección Web
      Then hay line chart con tendencia de sesiones
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/panel.js` (MODIFIED)
  - **Dependencias:** TICKET-DASH-004
  - **Prioridad:** P2
  - **Status:** ✅ COMPLETADO (2026-03-26)
  - **Notas:** Bloque web en API dashboard, KPIs GA4, top pages con barras y line chart de sesiones. 8/8 tests.

- [x] **TICKET-DASH-008: Sección Instagram en panel**
  - **Fuente:** Migración de tab Instagram del dashboard actual
  - **Historia de Usuario:** Como Karel, quiero ver mis métricas de Instagram para saber si mi presencia social está creciendo.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: KPIs de Instagram
      Given datos IG en tma_panel_kpis
      When navego a sección Instagram
      Then veo: followers, reach, engagement_rate
      And veo sparkline de reach semanal
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/panel.js` (MODIFIED)
  - **Dependencias:** TICKET-DASH-004
  - **Prioridad:** P2
  - **Status:** ✅ COMPLETADO (2026-03-26)
  - **Notas:** Bloque Instagram en API dashboard, KPIs followers/reach/engagement y sparkline de reach semanal en Chart.js. 8/8 tests.

---

## 📋 FASE 10 — TMA Panel: Portal de Documentos Integrado

> **Objetivo:** Integrar el portal de documentos existente dentro del plugin tma-panel con viewer protegido, aprobación y sistema de notas.

- [ ] **TICKET-PORTAL-005: Document pipeline — MD/HTML en cache con viewer protegido**
  - **Fuente:** Análisis comparativo RAI Panel — class-rai-panel-docs.php
  - **Historia de Usuario:** Como usuario del panel, quiero ver los documentos del proyecto inline con protecciones visuales (watermark, anti-copy).
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Documentos servidos desde cache
      Given 12 documentos HTML existentes en portal/docs/
      When migro los HTML a tma-panel/cache/html/
      Then endpoint GET /documents/{code}/content sirve el HTML
      And solo usuarios autenticados pueden acceder

    Scenario: Viewer con Shadow DOM
      Given documento cargado en el panel
      When el frontend lo renderiza
      Then se inyecta en Shadow DOM con estilos prose
      And tiene watermark dinámico (nombre usuario + fecha)
      And user-select: none aplicado (anti-copy)

    Scenario: Documentos con metadata en DB
      Given tabla tma_panel_docs con 12 registros
      When cargo la sección documentos
      Then veo grid de cards con: código, título, status, última actualización
      And cada card tiene botón "Ver" para abrir el viewer
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/includes/class-tma-panel-docs.php` (NEW)
    - `data/wordpress/wp-content/plugins/tma-panel/cache/html/` (NEW — migrado de portal/docs/)
  - **Dependencias:** TICKET-PANEL-004
  - **Prioridad:** P1
  - **Status:** ⏸️ PENDIENTE

- [ ] **TICKET-PORTAL-006: Sistema de aprobación de documentos**
  - **Fuente:** Análisis comparativo RAI Panel — UX de aprobación por documento
  - **Historia de Usuario:** Como Karel, quiero poder marcar cada documento como aprobado o con cambios para que el equipo sepa qué necesita corrección.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: 3 estados por documento
      Given documento abierto en viewer
      When hago click en "Aprobado"
      Then el documento se marca como aprobado con timestamp y usuario
      And el badge cambia a verde

    Scenario: Cambios con notas obligatorias
      Given documento abierto en viewer
      When hago click en "Con cambios"
      Then se muestra textarea para describir los cambios
      And no puedo enviar sin escribir al menos 10 caracteres

    Scenario: Barra de progreso global
      Given 12 documentos en el panel
      When 8 están aprobados
      Then la barra muestra 67% (8/12) con indicador numérico

    Scenario: Navegación prev/next entre documentos
      Given documento RAI-03 abierto
      When hago click en "Siguiente"
      Then se carga documento RAI-04 sin volver a la lista
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/panel.js` (MODIFIED)
  - **Dependencias:** TICKET-PORTAL-005
  - **Prioridad:** P1
  - **Status:** ⏸️ PENDIENTE

- [ ] **TICKET-PORTAL-007: Sistema de notas bidireccional**
  - **Fuente:** Análisis comparativo RAI Panel — notas por módulo + timeline
  - **Historia de Usuario:** Como Karel, quiero dejar notas sobre documentos, leads o cualquier sección del panel para comunicarme directamente con el equipo.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Crear nota desde cualquier módulo
      Given panel en sección documentos
      When hago click en "Dejar nota" y escribo un comentario
      Then la nota se guarda con: módulo, item_id, contenido, user_id, timestamp

    Scenario: Timeline de notas
      Given notas existentes de Karel y admin
      When navego a sección Notas
      Then veo timeline cronológico con todas las notas
      And cada nota muestra: autor, fecha, módulo, contenido

    Scenario: Filtrado por rol
      Given Karel tiene rol tma_client
      When ve notas
      Then ve sus propias notas + respuestas del admin
      And admin ve todas las notas de todos los usuarios

    Scenario: Reply inline
      Given nota de Karel visible para admin
      When admin escribe una respuesta
      Then la respuesta aparece debajo de la nota original
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/panel.js` (MODIFIED)
  - **Dependencias:** TICKET-PANEL-004
  - **Prioridad:** P1
  - **Status:** ⏸️ PENDIENTE

---

## 📋 FASE 11 — TMA Panel: Leads Pipeline Dinámico

> **Objetivo:** Migrar leads hardcoded a sistema CRUD con persistencia, conectado al formulario de contacto existente (TICKET-LEAD-001).

- [ ] **TICKET-LEAD-002: Migrar leads a tma_panel_leads con CRUD completo**
  - **Fuente:** Análisis comparativo RAI Panel + formulario existente (tma-contact-form.php)
  - **Historia de Usuario:** Como Karel, quiero gestionar mis leads en el panel para actualizar estados y ver el valor del pipeline.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Leads visibles en panel desde DB
      Given leads en tabla tma_panel_leads (migrados de tma_leads existente)
      When navego a sección Leads del panel
      Then veo tabla con: nombre, proyecto, canal, estado, valor
      And cada fila tiene badge de estado con color (new=azul, contacted=amarillo, quoted=naranja, won=verde, lost=rojo)

    Scenario: Actualizar estado de lead
      Given lead "Carlos Mejía" con estado "new"
      When cambio estado a "quoted" y agrego valor $5,200
      Then el estado se actualiza en DB con timestamp
      And el pipeline value total se recalcula

    Scenario: Pipeline value visible
      Given múltiples leads con valores
      When veo la sección leads
      Then hay KPI card con pipeline value total ($XX,XXX)
      And gráfico de leads por canal (Instagram, GBP, Referido, Angi, Web)

    Scenario: Nuevo lead desde formulario de contacto
      Given formulario de contacto existente en dev.thormetalart.com
      When un visitante envía solicitud de cotización
      Then se crea lead automáticamente en tma_panel_leads
      And aparece en el panel con estado "new"
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/includes/class-tma-panel-leads.php` (NEW)
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/panel.js` (MODIFIED)
    - `data/wordpress/wp-content/mu-plugins/tma-contact-form.php` (MODIFIED) — hook para crear lead en tma_panel_leads
  - **Dependencias:** TICKET-PANEL-004, TICKET-LEAD-001
  - **Prioridad:** P1
  - **Status:** ⏸️ PENDIENTE

- [ ] **TICKET-LEAD-003: Historial de cambios por lead**
  - **Fuente:** Mejores prácticas CRM
  - **Historia de Usuario:** Como Karel, quiero ver el historial de cada lead para recordar todas las interacciones y cambios de estado.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Historial registrado automáticamente
      Given lead "Carlos Mejía"
      When cambio estado de "new" a "contacted"
      Then se registra en historial: fecha, usuario, acción ("Estado: new → contacted")

    Scenario: Vista de historial en detalle de lead
      Given lead con 5 cambios de estado
      When abro detalle del lead
      Then veo timeline con todos los cambios cronológicamente
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/migrations/002-lead-history.php` (NEW)
  - **Dependencias:** TICKET-LEAD-002
  - **Prioridad:** P2
  - **Status:** ⏸️ PENDIENTE

- [ ] **TICKET-LEAD-004: Alertas de leads de alto valor**
  - **Fuente:** Requisito de negocio
  - **Historia de Usuario:** Como Karel, quiero recibir alertas cuando llega un lead de alto valor para darle atención inmediata.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Alerta en dashboard
      Given leads con estado "new" y value > $0
      When cargo el dashboard
      Then veo alerta: "X lead(s) nuevos requieren atención"

    Scenario: Email de notificación (opcional)
      Given nuevo lead creado desde formulario
      When el lead tiene servicio "Custom Gates" o "Art & Commissions"
      Then se envía email a Karel con datos básicos del lead
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/panel.js` (MODIFIED)
  - **Dependencias:** TICKET-LEAD-002
  - **Prioridad:** P3
  - **Status:** ⏸️ PENDIENTE

---

## 📋 FASE 12 — Cleanup: Eliminar servicios estáticos obsoletos

> **Objetivo:** Una vez que tma-panel está funcional, eliminar los 3 contenedores Docker que ya no son necesarios.

- [ ] **TICKET-DOCK-004: Eliminar servicios dashboard, dashboard-api y portal de Docker**
  - **Fuente:** Migración a plugin tma-panel
  - **Historia de Usuario:** Como DevOps, quiero eliminar los contenedores obsoletos para reducir recursos y complejidad del stack.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Servicios eliminados de docker-compose
      Given tma-panel funcional y verificado
      When elimino servicios dashboard, dashboard-api, portal de docker-compose.yml
      Then make up levanta el stack sin esos 3 servicios
      And panel.thormetalart.com funciona via WordPress
      And dev.thormetalart.com no se ve afectado

    Scenario: Archivos estáticos archivados
      Given dashboard/ y portal/ ya no se usan
      When hago git archive o movo a _archive/
      Then los archivos quedan disponibles como referencia
      And no se eliminan destructivamente

    Scenario: Traefik labels actualizados
      Given docker-compose.yml sin dashboard/portal
      When ejecuto make test
      Then todos los healthchecks pasan
      And solo quedan: wordpress, mysql, redis, phpmyadmin, panel (via wordpress)
    ```
  - **Archivos:**
    - `docker-compose.yml` (MODIFIED)
  - **Pre-requisito:** Todas las fases 8-11 completadas y verificadas
  - **Dependencias:** TICKET-PANEL-001 a TICKET-LEAD-004 (todas las fases del panel)
  - **Prioridad:** P3
  - **Status:** ⏸️ PENDIENTE

---

## 📊 Resumen

| Fase | Total | ✅ | ⏸️ | 🔄 | Progreso |
|------|-------|----|-----|-----|----------|
| 1 — Infraestructura | 3 | 3 | 0 | 0 | 100% |
| 2 — Dashboard | 3 | 3 | 0 | 0 | 100% |
| 3 — WordPress | 3 | 3 | 0 | 0 | 100% |
| 4 — SEO | 2 | 2 | 0 | 0 | 100% |
| 5 — Seguridad | 1 | 1 | 0 | 0 | 100% |
| 6 — Leads/CRM | 1 | 1 | 0 | 0 | 100% |
| 7 — Portal Docs | 4 | 4 | 0 | 0 | 100% |
| 8 — TMA Panel Base | 10 | 10 | 0 | 0 | 100% |
| 9 — Dashboard Datos Reales | 5 | 5 | 0 | 0 | 100% |
| 10 — Portal Integrado | 3 | 0 | 3 | 0 | 0% |
| 11 — Leads Dinámico | 3 | 0 | 3 | 0 | 0% |
| 12 — Cleanup Docker | 1 | 0 | 1 | 0 | 0% |
| **TOTAL** | **39** | **32** | **7** | **0** | **82%** |
