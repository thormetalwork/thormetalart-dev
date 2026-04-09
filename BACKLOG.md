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

- [x] **TICKET-PORTAL-005: Document pipeline — MD/HTML en cache con viewer protegido**
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
  - **Status:** ✅ COMPLETADO (2026-03-26)
  - **Notas:** Clase docs + cache/html migrado, endpoint protegido /documents/{code}/content, viewer Shadow DOM con watermark y anti-copy. 13/13 tests.

- [x] **TICKET-PORTAL-006: Sistema de aprobación de documentos**
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
  - **Status:** ✅ COMPLETADO (2026-03-26)
  - **Notas:** Endpoint status por documento, 3 estados con validación, nota obligatoria para cambios, barra global progreso y navegación prev/next. 10/10 tests.

- [x] **TICKET-PORTAL-007: Sistema de notas bidireccional**
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
  - **Status:** ✅ COMPLETADO (2026-03-26)
  - **Notas:** Notas contextualizadas por module/item_id, acción "Dejar nota" desde viewer, timeline con metadata y filtro por rol. 7/7 tests.

---

## 📋 FASE 11 — TMA Panel: Leads Pipeline Dinámico

> **Objetivo:** Migrar leads hardcoded a sistema CRUD con persistencia, conectado al formulario de contacto existente (TICKET-LEAD-001).

- [x] **TICKET-LEAD-002: Migrar leads a tma_panel_leads con CRUD completo**
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
  - **Status:** ✅ COMPLETADO (2026-03-26)
  - **Notas:** Clase TMA_Panel_Leads + migración desde tma_leads, endpoint POST /leads/{id} para status/value, y hook del formulario (tma_panel_create_lead) hacia panel_leads. 8/8 tests.

- [x] **TICKET-LEAD-003: Historial de cambios por lead**
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
  - **Status:** ✅ COMPLETADO (2026-03-26)
  - **Notas:** Migración 002 para panel_lead_history, logging automático en update_lead(), endpoint GET /leads/{id}/history y timeline UI con botón "Ver historial". 7/7 tests.

- [x] **TICKET-LEAD-004: Alertas de leads de alto valor**
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
  - **Status:** ✅ COMPLETADO (2026-03-26)
  - **Notas:** Dashboard API expone new_attention.high_value_leads (status=new y lead_value>0), UI muestra alerta contextual, y formulario dispara email opcional para servicios premium. 6/6 tests.

---

## 📋 FASE 12 — Cleanup: Eliminar servicios estáticos obsoletos

> **Objetivo:** Una vez que tma-panel está funcional, eliminar los 3 contenedores Docker que ya no son necesarios.

- [x] **TICKET-DOCK-004: Eliminar servicios dashboard, dashboard-api y portal de Docker**
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
  - **Status:** ✅ COMPLETADO (2026-03-26)
  - **Notas:** Se removieron servicios dashboard/dashboard-api/portal de docker-compose.yml, se archivaron carpetas en _archive/, se aplicó up --remove-orphans y make test OK. 5/5 tests.

---

## � FASE 13 — TMA Panel: UI/UX Polish (Comparativa RAI Panel)

> **Referencia:** Análisis comparativo con RAI Panel v0.4.0 — patrones UI maduros (Shadow DOM, alerts, progress, badges, doc-nav, timeline, filters)
> **Objetivo:** Elevar la calidad visual y UX del panel TMA al nivel del panel RAI, extrayendo inline styles a CSS classes, creando componentes reutilizables, y mejorando cada sección.

- [x] **TICKET-PANEL-011: CSS Component System — extraer inline styles a clases reutilizables**
  - **Fuente:** Análisis comparativo RAI Panel — panel.css componentes maduros
  - **Historia de Usuario:** Como desarrollador, quiero un sistema de componentes CSS reutilizables para que el panel sea consistente, mantenible y extensible.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Inline styles eliminados del JS
      Given panel.js con ~50 inline style attributes
      When extraigo los estilos a clases CSS en panel.css
      Then panel.js no tiene atributos style="" en templates HTML
      And panel.css tiene clases para: alert, progress-bar, grid, stat-card, modal, doc-viewer, timeline, empty-state

    Scenario: Componentes CSS reutilizables
      Given panel.css actualizado
      When reviso las clases
      Then existen: .alert, .alert--gold, .alert--warning, .progress-bar, .progress-bar__fill, .grid, .grid--2, .grid--4, .stat-bar, .modal, .modal__header, .modal__body, .doc-viewer-toolbar, .timeline__item, .empty-state

    Scenario: Apariencia visual idéntica
      Given todos los inline styles migrados a CSS
      When cargo el panel en browser
      Then la apariencia es idéntica a antes (no regresiones visuales)
      And responsive mobile sigue funcionando
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/assets/css/panel.css` (MODIFIED)
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/panel.js` (MODIFIED)
  - **Dependencias:** Ninguna
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2025-07-27
  - **Notas de cierre:** 15+ CSS components extraídos, 937 líneas panel.css, ~50 inline styles eliminados de panel.js.

- [x] **TICKET-PANEL-012: Dashboard mejorado — alertas, progreso documentos, actividad reciente**
  - **Fuente:** Análisis comparativo RAI Panel — dashboard con alertas contextuales y actividad reciente
  - **Historia de Usuario:** Como Karel, quiero un dashboard más informativo con alertas visibles, progreso de documentos y actividad reciente para tener visión completa del proyecto en un vistazo.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Alertas contextuales en la parte superior
      Given datos del dashboard cargados
      When hay leads nuevos o documentos pendientes
      Then veo alertas con iconos y acción directa (link a sección)

    Scenario: Barra de progreso de documentos en dashboard
      Given 12 documentos con estados variados
      When cargo el dashboard
      Then veo tarjeta con barra de progreso de aprobación (X/12)
      And click en la tarjeta lleva a #documents

    Scenario: Actividad reciente
      Given acciones en el panel (notas, cambios de estado, aprobaciones)
      When veo el dashboard
      Then hay sección "Actividad reciente" con últimas 5 acciones
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/panel.js` (MODIFIED)
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/i18n.js` (MODIFIED)
  - **Dependencias:** TICKET-PANEL-011
  - **Prioridad:** P2
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2025-07-27
  - **Notas de cierre:** Alertas contextuales clickables (leads + docs), barra de progreso documentos en dashboard, sección Actividad Reciente (últimas 5 acciones), API ampliada con doc_progress + recent_activity + new_leads.

- [x] **TICKET-PANEL-013: Documentos mejorados — cards grid, viewer refinado, navegación**
  - **Fuente:** Análisis comparativo RAI Panel — doc-viewer with Shadow DOM, review toolbar, annotation system
  - **Historia de Usuario:** Como Karel, quiero una vista de documentos más visual con cards en grid, visor mejorado y navegación fluida para revisar documentos cómodamente.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Cards en grid responsive
      Given 12 documentos en la base de datos
      When cargo sección documentos
      Then veo grid de cards (3 columnas desktop, 2 tablet, 1 mobile)
      And cada card tiene: icono status, título, código, fecha, botón ver

    Scenario: Viewer con toolbar fija
      Given documento abierto en viewer
      When scroll el contenido
      Then la toolbar de acciones (aprobar/cambios/nota) permanece fija en la parte inferior
      And botones prev/next muestran nombre del documento siguiente

    Scenario: Empty state cuando no hay documentos
      Given 0 documentos en la DB
      When cargo sección documentos
      Then veo ilustración/icono con texto "No hay documentos aún"
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/panel.js` (MODIFIED)
    - `data/wordpress/wp-content/plugins/tma-panel/assets/css/panel.css` (MODIFIED)
  - **Dependencias:** TICKET-PANEL-011
  - **Prioridad:** P2
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2025-07-27
  - **Notas de cierre:** Grid responsive (3→2→1 columnas), iconos de status en cards, prev/next con nombre del documento, doc-card--grid class.

- [x] **TICKET-PANEL-014: Leads mejorados — pipeline visual, filtros, badges en sidebar**
  - **Fuente:** Análisis comparativo RAI Panel — tabs, filtros, badges de conteo en sidebar
  - **Historia de Usuario:** Como Karel, quiero ver mis leads con pipeline visual (columnas por estado), filtros por canal, y badges en sidebar para saber cuántos leads tengo sin entrar a la sección.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Pipeline visual por estado
      Given leads con diferentes estados (new, contacted, quoted, won, lost)
      When cargo sección leads
      Then veo KPI resumen arriba (total, pipeline value, leads nuevos)
      And tabla con badges de color por estado

    Scenario: Filtros por canal y estado
      Given leads de múltiples canales
      When selecciono filtro "Instagram"
      Then la tabla muestra solo leads de Instagram
      And puedo combinar filtro por canal + estado

    Scenario: Badge de leads nuevos en sidebar
      Given 3 leads con estado "new"
      When veo el sidebar
      Then el link "Leads" muestra badge "(3)" en color dorado
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/panel.js` (MODIFIED)
    - `data/wordpress/wp-content/plugins/tma-panel/assets/css/panel.css` (MODIFIED)
    - `data/wordpress/wp-content/plugins/tma-panel/templates/panel.php` (MODIFIED)
  - **Dependencias:** TICKET-PANEL-011
  - **Prioridad:** P2
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2025-07-27
  - **Notas de cierre:** KPI summary bar (total/pipeline/nuevos), filtros por canal y estado, sidebar badge dinámico con conteo de leads nuevos, status badges de color en tabla.

---

## �️ FASE 14 — TMA Panel: Bug Fixes & Document UX

> **Meta:** Corregir bugs de rendimiento y mejorar la experiencia de documentos.
> **Versión:** 0.4.0

- [x] **TICKET-PANEL-015: Bugfix + Document UX Improvements**
  - **Fuente:** Reporte de usuario — "la página como que se reinicia" + análisis de mejoras en documentos
  - **Historia de Usuario:** Como Karel, quiero que la página no se reinicie cuando los gráficos se actualizan, y quiero poder ver notas y navegar documentos de forma más intuitiva.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Dashboard no pierde scroll en auto-refresh
      Given estoy viendo la sección de Impressions
      When pasan 120 segundos y el dashboard se refresca
      Then la página mantiene mi posición de scroll
      And no se crean gráficos duplicados en memoria

    Scenario: Viewer con toolbar en la parte inferior
      Given abro un documento en el viewer
      Then los botones de acción (Aprobar, Con cambios, Dejar nota) están abajo
      And el contenido del documento ocupa el espacio central

    Scenario: Notas visibles en el viewer
      Given abro un documento que tiene notas
      Then veo las notas asociadas debajo del contenido
      And puedo agregar nuevas notas

    Scenario: Atajos de teclado en viewer
      Given el viewer está abierto
      When presiono Escape
      Then el viewer se cierra
      When presiono flecha izquierda/derecha
      Then navego al documento anterior/siguiente

    Scenario: Toast notifications en lugar de alerts
      Given realizo una acción (guardar nota, cambiar estado)
      Then veo una notificación toast en esquina superior derecha
      And no veo un popup alert del navegador
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/panel.js` (MODIFIED)
    - `data/wordpress/wp-content/plugins/tma-panel/assets/css/panel.css` (MODIFIED)
    - `data/wordpress/wp-content/plugins/tma-panel/tma-panel.php` (MODIFIED — version bump)
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2025-07-27
  - **Notas de cierre:** Chart.js memory leak fix (destroyCharts), scroll preservation on auto-refresh, toolbar moved to bottom, doc notes in viewer, keyboard shortcuts (Esc/←/→), toast notifications replacing all alerts, dead code cleanup, note form toggle fix.

---

## � FASE 15 — Website V1: Template System + Estructura

> **Fuente:** Propuesta Web V1 (docs/cliente/propuesta_web_v1.md) + Doc 10 Copys Sitio Web + Brief Posicionamiento v2
> **Objetivo:** Crear la estructura completa del sitio web con FSE block templates, header, footer, y contenido profesional bilingüe en las 10 páginas. Todo implementable sin esperar contenido visual del cliente.
> **Referencia de contenido:** Doc 10 (Copys del Sitio Web) para textos, Brief v2 para mensajes y posicionamiento

- [x] **TICKET-WP-004: Block patterns library — secciones reutilizables del sitio**
  - **Fuente:** Propuesta Web V1 — Sección 4.1 Implementación Técnica
  - **Historia de Usuario:** Como desarrollador, quiero una librería de block patterns reutilizables para construir todas las páginas con consistencia visual y reducir duplicación de código.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Categoría de patterns registrada
      Given child theme thormetalart activo
      When registro la categoría 'thormetalart' en block patterns
      Then aparece en el editor de bloques bajo "Thor Metal Art"

    Scenario: Pattern hero section disponible
      Given pattern hero-section registrado
      When inserto el pattern en una página
      Then renderiza: cover full-width con overlay oscuro, H1 clamp responsive, subtítulo, 2 botones CTA (gold + outline)
      And usa colores de theme.json (#1A1A1A, #B8860B, #F5F5F0)

    Scenario: Pattern CTA banner disponible
      Given pattern cta-banner registrado
      When lo uso en cualquier página
      Then renderiza: fondo oscuro, H2, párrafo, botón gold, iconos de contacto
      And es responsive en mobile < 768px

    Scenario: Todos los patterns usan design tokens del theme.json
      Given todos los patterns creados
      When reviso el markup
      Then usan var:preset|color|* y var:preset|font-family|* (no colores hardcoded)
    ```
  - **Patterns a crear:**
    - `hero-section` — Cover full-width con H1 + sub + 2 CTAs
    - `cta-banner` — Fondo oscuro con H2 + texto + botón + contacto
    - `service-card` — Card con icono/imagen + título + descripción + link
    - `trust-bar` — Barra horizontal con iconos de confianza
    - `process-step` — Paso numerado con icono + título + descripción
    - `faq-item` — Acordeón (details/summary) con pregunta y respuesta
    - `testimonial-card` — Quote con estrellas + texto + nombre + tipo proyecto
  - **Archivos:**
    - `data/wordpress/wp-content/themes/thormetalart/patterns/hero-section.php` (NEW)
    - `data/wordpress/wp-content/themes/thormetalart/patterns/cta-banner.php` (NEW)
    - `data/wordpress/wp-content/themes/thormetalart/patterns/service-card.php` (NEW)
    - `data/wordpress/wp-content/themes/thormetalart/patterns/trust-bar.php` (NEW)
    - `data/wordpress/wp-content/themes/thormetalart/patterns/process-step.php` (NEW)
    - `data/wordpress/wp-content/themes/thormetalart/patterns/faq-item.php` (NEW)
    - `data/wordpress/wp-content/themes/thormetalart/patterns/testimonial-card.php` (NEW)
    - `data/wordpress/wp-content/themes/thormetalart/functions.php` (MODIFIED — registrar categoría)
  - **Dependencias:** Ninguna
  - **Estimación:** 6-8 horas
  - **Prioridad:** P0
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27

- [x] **TICKET-WP-005: Header template part — logo + navegación + CTA + idioma**
  - **Fuente:** Propuesta Web V1 — Sección 3.1 Navegación principal
  - **Historia de Usuario:** Como visitante, quiero un header profesional con logo, menú de navegación con dropdown de servicios y botón CTA visible para encontrar rápidamente lo que busco y solicitar cotización.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Header renderiza correctamente en desktop
      Given template part header.html creado
      When cargo cualquier página del sitio en viewport > 1024px
      Then veo: logo a la izquierda, navegación central con dropdown "Services", link "Art", link "How We Work", link "Portfolio", link "Contact"
      And botón CTA "Get a Quote" dorado a la derecha
      And header tiene fondo #1A1A1A con texto #F5F5F0

    Scenario: Header responsive con hamburger menu
      Given viewport < 768px
      When cargo el sitio
      Then el menú se colapsa en botón hamburger
      And al hacer click se despliega overlay con todos los links
      And el botón CTA sigue visible

    Scenario: Dropdown de servicios funcional
      Given menú de navegación visible
      When hago hover/click en "Services"
      Then se despliega submenu con: Custom Gates, Metal Railings, Metal Fences, Custom Furniture, Metal Stairs

    Scenario: Header no interfiere con TMA Panel
      Given panel.thormetalart.com cargado
      When verifico el header
      Then el panel usa su propio header (no el del tema)
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/themes/thormetalart/parts/header.html` (NEW)
    - `data/wordpress/wp-content/themes/thormetalart/style.css` (MODIFIED — estilos header)
  - **Dependencias:** TICKET-WP-004
  - **Estimación:** 4-6 horas
  - **Prioridad:** P0
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27

- [x] **TICKET-WP-006: Footer template part — NAP + servicios + redes + legal**
  - **Fuente:** Doc 10 Copys del Sitio Web — Sección Footer + Brief v2 — NAP
  - **Historia de Usuario:** Como visitante, quiero un footer completo con información de contacto, servicios, redes sociales y datos legales para encontrar cómo contactar a Thor Metal Art desde cualquier página.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Footer con 4 columnas en desktop
      Given template part footer.html creado
      When cargo cualquier página en viewport > 1024px
      Then veo 4 columnas: (1) Logo + descripción + redes, (2) Servicios con links, (3) Contacto (phone, email, WhatsApp, ubicación), (4) Horario + legal
      And fondo #1A1A1A con acentos #B8860B

    Scenario: Footer responsive
      Given viewport < 768px
      When cargo footer
      Then las 4 columnas se apilan verticalmente
      And teléfono y WhatsApp son clickables (tel: y wa.me/)

    Scenario: NAP consistente con GBP
      Given footer renderizado
      When comparo con Google Business Profile
      Then nombre, dirección y teléfono coinciden exactamente (NAP consistency)

    Scenario: Copyright dinámico
      Given año actual 2026
      When veo el footer
      Then muestra "© 2026 Thor Metal Art LLC. All rights reserved."
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/themes/thormetalart/parts/footer.html` (NEW)
    - `data/wordpress/wp-content/themes/thormetalart/style.css` (MODIFIED — estilos footer)
  - **Dependencias:** TICKET-WP-004
  - **Estimación:** 4-6 horas
  - **Prioridad:** P0
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27

- [x] **TICKET-WP-007: Homepage completa — front-page template con 6 secciones**
  - **Fuente:** Doc 10 Copys del Sitio Web — PAGE 1: HOME + Propuesta Web V1 — Sección 3.1
  - **Historia de Usuario:** Como visitante, quiero ver una homepage que en 5 segundos me comunique qué hace Thor Metal Art, me genere confianza y me motive a pedir cotización.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Hero section con doble CTA
      Given homepage cargada
      When veo la primera sección
      Then H1: "Custom Metal Fabrication & Art in Miami"
      And subtítulo: "Precision Craftsmanship. Exclusive Design. Free Estimates."
      And 2 botones: "Get a Free Quote" (gold) + "View Our Art" (outline)

    Scenario: Trust bar visible
      Given hero section visible
      When veo debajo del hero
      Then barra horizontal con: Miami-Based | Licensed & Insured | ⭐ Stars on Google | Free Estimates | Water Jet Precision

    Scenario: Grid de 6 servicios
      Given sección "What We Build" visible
      When veo las cards
      Then hay 6 cards: Gates, Railings, Fences, Furniture, Stairs, Sculpture & Art
      And cada card tiene título, descripción corta y link a su página
      And grid responsive: 3 cols desktop, 2 tablet, 1 mobile

    Scenario: About snippet con CTA
      Given sección "About Thor Metal Art" visible
      When leo el contenido
      Then texto del Doc 10 sobre Karel Frometa y el taller
      And botón "See Our Process" hacia /how-we-work/

    Scenario: Portfolio highlight
      Given sección "Recent Work" visible
      When veo el grid
      Then muestra 3 proyectos recientes del CPT tma_portfolio
      And botón "View Full Portfolio" hacia /portfolio/

    Scenario: CTA final con contacto directo
      Given sección final con fondo oscuro
      When veo el contenido
      Then H2: "Ready to Start Your Project?"
      And botón "Get Your Free Quote" + phone + WhatsApp + email
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/themes/thormetalart/templates/front-page.html` (NEW)
    - `data/wordpress/wp-content/themes/thormetalart/style.css` (MODIFIED)
  - **Dependencias:** TICKET-WP-004, TICKET-WP-005, TICKET-WP-006
  - **Estimación:** 8-12 horas
  - **Prioridad:** P0
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27

- [x] **TICKET-WP-008: Reescribir 5 páginas de servicios con copys profesionales del Doc 10**
  - **Fuente:** Doc 10 Copys del Sitio Web — PAGE 2: SERVICE PAGES + Brief v2 — Motor Productor
  - **Historia de Usuario:** Como visitante buscando un servicio específico (ej: "custom metal gates miami"), quiero ver una página completa con información detallada, FAQs, sección en español y CTA clara para sentir confianza y solicitar cotización.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Cada servicio tiene contenido profesional del Doc 10
      Given página /custom-metal-gates-miami/ cargada
      When leo el contenido
      Then H1: "Custom Metal Gates Miami"
      And subtítulo: "Hand-Crafted. Built to Last. Designed for You."
      And introducción de 150-200 palabras del Doc 10
      And sección "What's Included" con 6 bullet points

    Scenario: FAQ section con acordeón
      Given página de servicio cargada
      When veo sección FAQ
      Then hay 3-4 preguntas frecuentes específicas del servicio (del Doc 10)
      And formato acordeón expandible (details/summary)
      And texto en inglés con respuestas detalladas

    Scenario: Sección bilingüe en español
      Given página de servicio cargada
      When scroll a sección "Servicio en Español"
      Then H2 y contenido traducido al español
      And CTA "Solicitar Cotización Gratis" en español

    Scenario: CTA final con formulario
      Given final de la página de servicio
      When veo el CTA
      Then texto: "Ready to design your [service]?"
      And botón hacia /contact/ o formulario inline

    Scenario: Las 5 páginas tienen estructura consistente
      Given las 5 páginas de servicios actualizadas
      When comparo estructura
      Then todas siguen: Hero → Intro → Features → FAQ → Español → CTA
      And cada una tiene H1, meta description y keywords únicos
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/mu-plugins/tma-service-pages.php` (MODIFIED — reescribir contenido)
  - **Dependencias:** TICKET-WP-004, TICKET-WP-005, TICKET-WP-006
  - **Estimación:** 10-14 horas
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27

- [x] **TICKET-WP-009: Crear página Art & Commissions — Motor Artista**
  - **Fuente:** Doc 10 Copys — PAGE 3: METAL AS ART + Brief v2 — Motor Artista
  - **Historia de Usuario:** Como coleccionista o diseñador de interiores, quiero ver la faceta artística de Thor Metal Art con las esculturas de Karel, su statement como artista y cómo comisionar una pieza original.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Página Art & Commissions creada
      Given slug /art-commissions/
      When cargo la página
      Then H1: "Metal as Art"
      And subtítulo: "Original Sculptures & Commissioned Pieces by Karel Frometa — Miami"

    Scenario: Artist statement de Karel
      Given sección artist statement visible
      When leo el contenido
      Then texto template del Doc 10 en primera persona
      And firmado "— Karel Frometa, Miami"
      And marcado como editable (placeholders [X] para personalizar)

    Scenario: Proceso de comisión en 4 pasos
      Given sección "How to Commission a Piece" visible
      When veo los pasos
      Then 4 pasos visuales: Conversation → Concept & Proposal → Fabrication → Delivery/Installation
      And cada paso tiene descripción del Doc 10

    Scenario: CTA directo a Karel
      Given final de la página
      When veo el CTA
      Then texto: "Commission a Piece — Contact Karel directly"
      And links a email y WhatsApp
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/mu-plugins/tma-service-pages.php` (MODIFIED — agregar Art & Commissions)
  - **Dependencias:** TICKET-WP-004, TICKET-WP-005, TICKET-WP-006
  - **Estimación:** 6-8 horas
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27

- [x] **TICKET-WP-010: Crear página How We Work — proceso de 5 pasos**
  - **Fuente:** Doc 10 Copys — PAGE 4: HOW WE WORK + Propuesta Web V1
  - **Historia de Usuario:** Como cliente potencial, quiero entender el proceso completo de Thor Metal Art (desde presupuesto hasta instalación) para saber qué esperar y sentir confianza en el profesionalismo del taller.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Página How We Work creada
      Given slug /how-we-work/
      When cargo la página
      Then H1: "How We Work"
      And subtítulo: "From First Call to Finished Installation — Everything In-House"

    Scenario: 5 pasos del proceso visuales
      Given sección de proceso visible
      When veo los pasos
      Then 5 pasos con icono y descripción del Doc 10:
        | Paso | Título              |
        | 1    | Free Estimate       |
        | 2    | Design & Quote      |
        | 3    | Production          |
        | 4    | Quality Check       |
        | 5    | Installation        |

    Scenario: Diferenciadores visibles
      Given sección diferenciadores visible
      When leo el contenido
      Then bullets: Everything in-house, Water jet + MIG/TIG, Respond within 24h, Licensed & insured

    Scenario: CTA final
      Given final de la página
      When veo el CTA
      Then botón "Ready to Start? Get your free estimate" hacia /contact/
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/mu-plugins/tma-service-pages.php` (MODIFIED — agregar How We Work)
  - **Dependencias:** TICKET-WP-004, TICKET-WP-005, TICKET-WP-006
  - **Estimación:** 4-6 horas
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27

- [x] **TICKET-WP-011: Crear página Contact con formulario integrado**
  - **Fuente:** Doc 10 Copys — PAGE 5: CONTACT + TICKET-LEAD-001 (formulario existente)
  - **Historia de Usuario:** Como visitante interesado, quiero una página de contacto con formulario fácil y datos de contacto directo para solicitar cotización de la forma que me sea más cómoda (formulario, teléfono o WhatsApp).
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Página Contact con layout 2 columnas
      Given slug /contact/
      When cargo la página en desktop
      Then H1: "Let's Talk About Your Project"
      And subtítulo: "Free estimate. No commitment. We respond within 24 hours."
      And 2 columnas: formulario a la izquierda, info de contacto a la derecha

    Scenario: Formulario de contacto funcional
      Given columna izquierda visible
      When veo el formulario
      Then es el shortcode [tma_contact_form] existente
      And tiene campos: nombre*, email*, teléfono*, tipo de proyecto*, descripción
      And trust signals debajo: "✓ We respond within 24 business hours" + "✓ Free estimate" + "✓ English & Spanish"

    Scenario: Info de contacto directo
      Given columna derecha visible
      When veo la info
      Then phone clickable (tel:), WhatsApp (wa.me/), email (mailto:), ubicación
      And horario de atención visible

    Scenario: Responsive en mobile
      Given viewport < 768px
      When cargo /contact/
      Then las 2 columnas se apilan: formulario arriba, contacto abajo
      And teléfono y WhatsApp tienen touch targets >= 44px
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/mu-plugins/tma-service-pages.php` (MODIFIED — agregar Contact)
    - `data/wordpress/wp-content/themes/thormetalart/templates/page-contact.html` (NEW — template 2 columnas)
  - **Dependencias:** TICKET-WP-004, TICKET-WP-005, TICKET-WP-006, TICKET-LEAD-001
  - **Estimación:** 4-6 horas
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27

- [x] **TICKET-WP-012: Portfolio templates — archive grid filtrable + single project**
  - **Fuente:** Propuesta Web V1 — Sección 3.5 Portfolio + TICKET-WP-003 (CPT existente)
  - **Historia de Usuario:** Como visitante, quiero ver el portafolio de proyectos en un grid visual filtrable por tipo y poder abrir cada proyecto para ver su galería completa y detalles.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Archive portfolio con grid filtrable
      Given template archive-tma_portfolio.html creado
      When navego a /portfolio/
      Then H1: "Our Work" con subtítulo
      And fila de filtros por tma_project_type: All | Gates | Railings | Fences | Furniture | Stairs | Art
      And grid 3 columnas con imagen, título y tipo de cada proyecto

    Scenario: Filtro funcional
      Given grid de portfolio visible
      When hago click en filtro "Gates"
      Then solo se muestran proyectos con taxonomía "Gates"
      And filtro activo tiene estilo destacado (gold)

    Scenario: Single project con galería
      Given template single-tma_portfolio.html creado
      When hago click en un proyecto del grid
      Then veo: imagen principal, galería de fotos, descripción, materiales, ubicación, año
      And botón "Back to Portfolio" y navegación prev/next

    Scenario: Empty state cuando no hay proyectos
      Given 0 proyectos en tma_portfolio
      When cargo /portfolio/
      Then mensaje: "Portfolio coming soon. Contact us to see examples of our work."
      And botón CTA hacia /contact/
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/themes/thormetalart/templates/archive-tma_portfolio.html` (NEW)
    - `data/wordpress/wp-content/themes/thormetalart/templates/single-tma_portfolio.html` (NEW)
    - `data/wordpress/wp-content/themes/thormetalart/style.css` (MODIFIED — grid + filtros)
  - **Dependencias:** TICKET-WP-003, TICKET-WP-004, TICKET-WP-005, TICKET-WP-006
  - **Estimación:** 6-8 horas
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27

- [x] **TICKET-WP-013: Navigation menus — registro programático + configuración**
  - **Fuente:** Propuesta Web V1 — Sección 2 Navegación principal
  - **Historia de Usuario:** Como desarrollador, quiero los menús de navegación registrados y pre-configurados programáticamente para que el header y footer muestren la navegación correcta sin configuración manual en wp-admin.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Menús registrados al activar tema
      Given child theme thormetalart activo
      When verifico menús registrados
      Then existen: 'tma-primary' (header), 'tma-services' (dropdown), 'tma-footer' (footer)

    Scenario: Menú primario pre-poblado
      Given menú tma-primary creado
      When verifico items
      Then contiene: Services (dropdown) | Art | How We Work | Portfolio | Contact
      And "Services" tiene sub-items: Custom Gates, Metal Railings, Metal Fences, Custom Furniture, Metal Stairs

    Scenario: Menú footer pre-poblado
      Given menú tma-footer creado
      When verifico items
      Then contiene links a: Home, todos los servicios, Art, Portfolio, Contact, Privacy Policy

    Scenario: Menús se actualizan si se agregan páginas
      Given página nueva creada
      When ejecuto el hook de actualización
      Then la página se agrega al menú correspondiente si su slug coincide
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/mu-plugins/tma-navigation.php` (NEW)
  - **Dependencias:** TICKET-WP-005, TICKET-WP-006, TICKET-WP-008, TICKET-WP-009, TICKET-WP-010, TICKET-WP-011
  - **Estimación:** 3-4 horas
  - **Prioridad:** P0
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27

---

## 📋 FASE 16 — Website V1: Contenido Visual

> **Fuente:** Propuesta Web V1 — Fase B + Doc 09 (Guía de Fotografía)
> **Objetivo:** Poblar el sitio con contenido visual real: imágenes hero, proyectos de portfolio, fotos de taller y galería artística.
> **⚠️ DEPENDENCIA EXTERNA:** Esta fase requiere fotos reales proporcionadas por Karel Frometa. No puede completarse hasta que el cliente entregue el material fotográfico.
> **Referencia:** Doc 09 — Guía de Fotografía (alto contraste, texturas de metal visibles, luz lateral dramática, proceso visible en taller)

- [x] **TICKET-WP-014: Imágenes hero + featured images para todas las páginas**
  - **Fuente:** Doc 09 Guía de Fotografía + Propuesta Web V1
  - **Historia de Usuario:** Como visitante, quiero ver imágenes de alta calidad en cada página que muestren el trabajo real de Thor Metal Art para sentir la calidad del producto antes de contactar.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Cada página tiene featured image
      Given fotos de Karel recibidas y procesadas
      When cargo cada página del sitio
      Then tiene featured image asignada que se usa en el hero section
      And imágenes optimizadas (WebP, max 1920px wide, < 200KB)

    Scenario: Homepage hero con imagen impactante
      Given homepage cargada
      When veo el hero
      Then imagen de fondo muestra pieza metálica o taller (alto contraste, luz dramática)
      And texto legible con overlay oscuro semitransparente

    Scenario: Cada servicio tiene imagen representativa
      Given página de servicio /custom-metal-gates-miami/
      When veo el hero
      Then imagen muestra un gate real fabricado por Thor Metal Art
      And es diferente de las otras 4 páginas de servicio

    Scenario: Fallback elegante sin imágenes
      Given imágenes no disponibles aún
      When cargo una página
      Then el hero muestra gradiente metálico (#1A1A1A → #4A4A4A) como placeholder
      And el layout no se rompe
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/uploads/` (NEW — imágenes procesadas)
    - `data/wordpress/wp-content/themes/thormetalart/style.css` (MODIFIED — fallback gradients)
  - **Dependencias:** TICKET-WP-007, TICKET-WP-008, TICKET-WP-009, TICKET-WP-010, TICKET-WP-011
  - **Estimación:** 4-6 horas
  - **Prioridad:** P0
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27
  - **⚠️ Requiere:** Fotos del cliente (mínimo 10 fotos hero de alta resolución)

- [x] **TICKET-WP-015: Crear 10-15 proyectos en Portfolio con fotos reales**
  - **Fuente:** TICKET-WP-003 (CPT existente) + Doc 09 Guía de Fotografía
  - **Historia de Usuario:** Como Karel, quiero que mi portafolio muestre mis mejores proyectos con fotos profesionales para que los clientes vean la calidad y variedad de mi trabajo.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Proyectos creados con metadata completa
      Given fotos de Karel categorizadas por tipo de proyecto
      When creo los proyectos en tma_portfolio
      Then cada proyecto tiene: título, galería (3-8 fotos), descripción, ubicación (Miami), año, material, y taxonomía (Gates/Railings/Fences/Furniture/Stairs/Art)

    Scenario: Distribución por categoría
      Given 10-15 proyectos creados
      When cuento por categoría
      Then al menos 2 proyectos por cada tipo principal (Gates, Railings, Fences)
      And al menos 1 proyecto de Furniture, Stairs y Art

    Scenario: Fotos optimizadas
      Given fotos originales del cliente
      When las proceso para web
      Then thumbnails: 600x400px, medium: 1200x800px, full: 1920px wide
      And formato WebP con fallback JPG
      And alt text descriptivo en cada imagen
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/uploads/portfolio/` (NEW)
  - **Dependencias:** TICKET-WP-012
  - **Estimación:** 8-12 horas
  - **Prioridad:** P0
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27
  - **⚠️ Requiere:** Fotos del cliente (mínimo 30-50 fotos de proyectos completados)

- [x] **TICKET-WP-016: Fotos de taller y proceso para How We Work**
  - **Fuente:** Doc 09 Guía de Fotografía — Proceso visible en taller
  - **Historia de Usuario:** Como visitante, quiero ver fotos reales del taller y el proceso de fabricación para entender cómo trabaja Thor Metal Art y confiar en su profesionalismo.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Fotos de proceso integradas en How We Work
      Given fotos de taller recibidas
      When cargo /how-we-work/
      Then cada paso del proceso tiene foto asociada:
        | Paso | Foto sugerida                        |
        | 1    | Karel en consulta con cliente         |
        | 2    | Diseño/plano/sketch en mesa           |
        | 3    | Water jet cortando / soldadura        |
        | 4    | Inspección de pieza terminada         |
        | 5    | Instalación en sitio del cliente      |

    Scenario: Estilo fotográfico consistente
      Given fotos de proceso insertadas
      When veo la página
      Then fotos siguen guía Doc 09: alto contraste, texturas de metal, luz lateral
      And edición coherente (desaturación leve, metal frío y sólido)
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/uploads/process/` (NEW)
  - **Dependencias:** TICKET-WP-010
  - **Estimación:** 3-4 horas
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27
  - **⚠️ Requiere:** Fotos del cliente (5-8 fotos del proceso de fabricación)

- [x] **TICKET-WP-017: Fotos de esculturas y arte para Art & Commissions**
  - **Fuente:** Doc 09 Guía de Fotografía — Motor Artista + Brief v2
  - **Historia de Usuario:** Como coleccionista o diseñador, quiero ver la galería de obras artísticas de Karel para evaluar su estilo y nivel artístico antes de comisionar una pieza.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Galería de arte en la página
      Given fotos de esculturas/arte recibidas
      When cargo /art-commissions/
      Then sección galería muestra grid con piezas artísticas
      And cada pieza tiene: foto, título, material, dimensiones (si aplica)

    Scenario: Diferenciación visual del Motor Artista
      Given galería de arte visible
      When comparo con páginas de servicios
      Then las fotos de arte tienen tratamiento visual más artístico
      And el tono es más premium/galería (fondo más oscuro, más espacio blanco)
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/uploads/art/` (NEW)
  - **Dependencias:** TICKET-WP-009
  - **Estimación:** 3-4 horas
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27
  - **⚠️ Requiere:** Fotos del cliente (8-15 fotos de esculturas y piezas artísticas)

---

## 📋 FASE 17 — Website V1: SEO + Conversión

> **Fuente:** Propuesta Web V1 — Fase C + Doc 10 SEO Title Tags + TICKET-SEO-001/002 (schema existente)
> **Objetivo:** Optimizar SEO on-page con meta descriptions finales, schema markup para FAQs y breadcrumbs, sitemap XML, sección de testimonios y mapa de contacto. Maximizar conversión de visitante a lead.

- [x] **TICKET-SEO-003: Meta descriptions finales con contenido optimizado del Doc 10**
  - **Fuente:** Doc 10 Copys del Sitio Web — SEO Title Tags & Meta Descriptions
  - **Historia de Usuario:** Como negocio, quiero meta descriptions optimizadas en cada página para mejorar el CTR en los resultados de Google.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Cada página tiene meta description del Doc 10
      Given mu-plugin tma-meta-tags.php existente
      When actualizo las descripciones
      Then cada página tiene la meta description definida en Doc 10:
        | Página           | Meta Description (inicio)                              |
        | Home             | Custom metal gates, railings, fences, furniture...     |
        | Custom Gates     | Handcrafted custom metal gates for residential...      |
        | Metal Railings   | Custom metal railings for stairs, balconies...         |
        | Metal Fences     | Decorative and security metal fences custom-designed.. |
        | Custom Furniture | Unique custom metal furniture designed and fabricated.. |
        | Metal Stairs     | (derivado del Doc 10 para Stairs)                      |
        | Art              | Original metal sculptures and commissioned art...      |
        | How We Work      | From concept to installation — our custom metalwork..  |
        | Contact          | Get a free estimate from Thor Metal Art...             |

    Scenario: Title tags optimizados
      Given cada página en el sitio
      When verifico el <title> tag
      Then sigue formato "[Keyword] | Thor Metal Art" del Doc 10
      And longitud entre 50-60 caracteres
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/mu-plugins/tma-meta-tags.php` (MODIFIED)
  - **Dependencias:** TICKET-WP-007, TICKET-WP-008, TICKET-WP-009, TICKET-WP-010, TICKET-WP-011
  - **Estimación:** 2-3 horas
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27

- [x] **TICKET-SEO-004: FAQ Schema markup (FAQPage) en páginas de servicios**
  - **Fuente:** Google Structured Data — FAQPage + Doc 10 FAQ content
  - **Historia de Usuario:** Como negocio, quiero que las FAQs de cada servicio aparezcan como rich results en Google para ocupar más espacio en los resultados de búsqueda y atraer más clicks.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: FAQPage schema generado por página de servicio
      Given página /custom-metal-gates-miami/ con FAQs
      When verifico el JSON-LD en el <head>
      Then contiene @type: FAQPage con array de Question/Answer
      And cada pregunta coincide con el contenido visible de la FAQ

    Scenario: Schema válido en Google Testing Tool
      Given JSON-LD de FAQPage
      When valido con Rich Results Test de Google
      Then resultado: válido, sin errores ni warnings

    Scenario: Solo páginas con FAQs tienen FAQPage schema
      Given página /how-we-work/ sin sección FAQ
      When verifico JSON-LD
      Then NO contiene FAQPage schema (solo LocalBusiness + Service)
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/mu-plugins/tma-schema.php` (MODIFIED)
  - **Dependencias:** TICKET-WP-008
  - **Estimación:** 3-4 horas
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27

- [x] **TICKET-SEO-005: BreadcrumbList schema en todas las páginas**
  - **Fuente:** Google Structured Data — BreadcrumbList
  - **Historia de Usuario:** Como negocio, quiero breadcrumbs estructuradas en Google para que los usuarios vean la jerarquía del sitio en los resultados de búsqueda.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: BreadcrumbList en páginas interiores
      Given página /custom-metal-gates-miami/
      When verifico JSON-LD
      Then contiene BreadcrumbList: Home > Services > Custom Metal Gates Miami

    Scenario: Breadcrumbs visuales en el frontend
      Given cualquier página interior cargada
      When veo debajo del header
      Then hay breadcrumb visual: Home > [Sección] > [Página actual]
      And cada item es clickable excepto el actual

    Scenario: Homepage sin breadcrumbs
      Given homepage cargada
      When verifico
      Then NO hay breadcrumb visible ni BreadcrumbList schema
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/mu-plugins/tma-schema.php` (MODIFIED)
    - `data/wordpress/wp-content/themes/thormetalart/style.css` (MODIFIED — estilos breadcrumb)
  - **Dependencias:** TICKET-WP-007
  - **Estimación:** 2-3 horas
  - **Prioridad:** P2
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27

- [x] **TICKET-SEO-006: Sitemap XML dinámico**
  - **Fuente:** SEO best practices — indexación completa
  - **Historia de Usuario:** Como negocio, quiero un sitemap XML actualizado automáticamente para que Google indexe todas las páginas y proyectos de portfolio.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Sitemap accesible
      Given sitemap implementado
      When accedo a /sitemap.xml
      Then devuelve XML válido con todas las URLs del sitio
      And incluye: homepage, 5 servicios, art, how-we-work, contact, portfolio archive
      And incluye todos los tma_portfolio posts publicados

    Scenario: Sitemap auto-actualizado
      Given nuevo proyecto de portfolio publicado
      When Google recrawlea el sitemap
      Then la nueva URL aparece con lastmod actualizado

    Scenario: Excluye páginas privadas
      Given sitemap generado
      When reviso las URLs
      Then NO incluye: /wp-admin/, /wp-login.php, panel.thormetalart.com, /wp-json/
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/mu-plugins/tma-sitemap.php` (NEW)
  - **Dependencias:** TICKET-WP-007, TICKET-WP-008, TICKET-WP-009, TICKET-WP-010, TICKET-WP-011, TICKET-WP-012
  - **Estimación:** 3-4 horas
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27

- [x] **TICKET-WP-018: Social proof — sección testimonios con reseñas de clientes**
  - **Fuente:** Doc 10 Copys — Social Proof Section + Brief v2 — Reseñas
  - **Historia de Usuario:** Como visitante, quiero ver opiniones reales de clientes anteriores para confiar en la calidad del trabajo de Thor Metal Art antes de solicitar cotización.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Sección testimonios en homepage
      Given testimonios almacenados
      When cargo la homepage
      Then sección "What Our Clients Say" muestra 3 testimonios
      And cada uno tiene: 5 estrellas, quote, nombre, tipo de proyecto, ubicación

    Scenario: Testimonios en páginas de servicios
      Given testimonios asociados a tipo de servicio
      When cargo página de servicio (ej: /custom-metal-gates-miami/)
      Then muestra 1-2 testimonios relevantes para ese servicio

    Scenario: Administración de testimonios
      Given admin de WordPress
      When gestiono testimonios
      Then puedo crear/editar/eliminar testimonios desde wp-admin
      And cada testimonio tiene: quote, nombre, servicio, rating, fecha

    Scenario: Fallback sin testimonios
      Given 0 testimonios en la DB
      When cargo la homepage
      Then sección de testimonios no se muestra (graceful degradation)
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/mu-plugins/tma-testimonials.php` (NEW — CPT o custom table)
  - **Dependencias:** TICKET-WP-007
  - **Estimación:** 4-6 horas
  - **Prioridad:** P2
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27
  - **⚠️ Parcialmente requiere:** Reseñas reales de clientes de Karel

- [x] **TICKET-WP-019: Google Maps embed en página de contacto**
  - **Fuente:** Propuesta Web V1 — Sección 3.6 Contact
  - **Historia de Usuario:** Como visitante local, quiero ver la ubicación de Thor Metal Art en un mapa para saber dónde está el taller y si me queda cerca.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Mapa visible en página de contacto
      Given dirección del taller definida
      When cargo /contact/
      Then sección con Google Maps embed mostrando ubicación de Thor Metal Art
      And mapa responsive (100% width, 300px height mobile, 400px desktop)

    Scenario: Mapa sin API key (iframe embed)
      Given implementación con iframe embed (no requiere API key)
      When cargo el mapa
      Then se renderiza correctamente sin costos de API
      And tiene lazy loading (loading="lazy")

    Scenario: Sin dirección definida
      Given Karel decide no publicar dirección
      When cargo /contact/
      Then sección de mapa no se muestra
      And solo texto: "Serving Miami-Dade & Broward County, Florida"
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/mu-plugins/tma-service-pages.php` (MODIFIED — agregar mapa a contact)
  - **Dependencias:** TICKET-WP-011
  - **Estimación:** 2-3 horas
  - **Prioridad:** P3
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-03-27
  - **⚠️ Requiere:** Decisión del cliente — ¿publicar dirección del taller?

---

## � FASE 18 — Google Ecosystem: Integración Real de APIs

> **Contexto:** GCP Project `thor-metal-art` (940256671703). OAuth2 brand verificada (APPROVED).
> OAuth2 scopes: business.manage, analytics.readonly, webmasters.readonly.
> **Última actualización:** 2026-04-09

- [x] **TICKET-DASH-009: Integración real GA4 Data API + Search Console API**
  - **Fuente:** Auditoría del dashboard — 100% datos mock, 0 llamadas API reales
  - **Historia de Usuario:** Como administrador, quiero que el cron del panel haga llamadas reales a GA4 y Search Console para tener KPIs actualizados sin datos ficticios.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: OAuth2 token refresh funcional
      Given credenciales OAuth2 configuradas en .env
      When el cron ejecuta sync_all_sources()
      Then obtiene access token de Google (cacheado en transient 55 min)

    Scenario: GA4 Data API retorna métricas reales
      Given GA4 property 532291061 configurada
      When el cron sincroniza fuente "ga4"
      Then inserta/actualiza sessions, users, pageviews, bounce_rate, conversions, top_pages

    Scenario: Search Console API retorna métricas reales
      Given GSC site sc-domain:thormetalart.com verificado
      When el cron sincroniza fuente "gsc"
      Then inserta/actualiza clicks, impressions, ctr, avg_position, top_queries, top_pages

    Scenario: UPSERT evita duplicados
      Given ya existe un KPI para el período actual
      When el cron ejecuta de nuevo
      Then actualiza el valor existente en vez de crear duplicado
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/includes/class-tma-panel-google-auth.php` (NEW)
    - `data/wordpress/wp-content/plugins/tma-panel/includes/class-tma-panel-cron.php` (MODIFIED)
    - `data/wordpress/wp-content/plugins/tma-panel/tma-panel.php` (MODIFIED)
    - `docker-compose.yml` (MODIFIED — env vars)
    - `data/wordpress/wp-config.php` (MODIFIED — constants)
    - `.env` / `.env.example` (MODIFIED — OAuth2 + GSC vars)
  - **Dependencias:** Ninguna
  - **Estimación:** 4 horas
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-04-09

- [x] **TICKET-INF-001: Fix MCP MySQL y Redis (VS Code Copilot)**
  - **Fuente:** MCPs no conectaban — VS Code no resuelve ${VAR} de shell
  - **Historia de Usuario:** Como desarrollador, quiero que los MCPs de MySQL y Redis funcionen desde VS Code para consultar datos directamente.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: MCP MySQL conecta
      Given mcp.json con valores literales
      When VS Code carga los MCPs
      Then MySQL MCP conecta a 127.0.0.1:3311

    Scenario: MCP Redis conecta
      Given mcp.json con password literal en URL
      When VS Code carga los MCPs
      Then Redis MCP conecta a localhost:6379 con auth
    ```
  - **Archivos:**
    - `.vscode/mcp.json` (MODIFIED)
  - **Dependencias:** Ninguna
  - **Estimación:** 30 min
  - **Prioridad:** P1
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-04-09

- [x] **TICKET-INF-002: Eliminar stack thormetalart-src obsoleto**
  - **Fuente:** Auditoría de servidor — stack inactivo con container names duplicados
  - **Historia de Usuario:** Como administrador, quiero eliminar el stack obsoleto para evitar conflictos y confusión.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Stack archivado y eliminado
      Given thormetalart-src en /srv/stacks/
      When archivo a tar.gz y elimino
      Then /srv/stacks/ solo tiene traefik, dev, prod
      And backup en /srv/backups/thormetalart-src-archive-20260409.tar.gz
    ```
  - **Archivos:**
    - `/srv/stacks/thormetalart-src/` (DELETED)
  - **Dependencias:** Ninguna
  - **Estimación:** 15 min
  - **Prioridad:** P2
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-04-09

- [x] **TICKET-DASH-010: Sección Google Setup en panel (admin-only)**
  - **Fuente:** Necesidad de documentar configuración GBP para cliente/admin
  - **Historia de Usuario:** Como admin, quiero una sección en el panel con los datos del formulario GBP API para poder completar la solicitud.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Sección visible solo para admin
      Given usuario con rol tma_admin
      When navego al panel
      Then veo enlace "Google Setup" en el sidebar
      And la sección muestra datos del formulario, links de referencia, checklist

    Scenario: Sección oculta para cliente
      Given usuario con rol tma_client
      When navego al panel
      Then NO veo "Google Setup" en el sidebar
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/templates/panel.php` (MODIFIED)
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/panel.js` (MODIFIED)
    - `data/wordpress/wp-content/plugins/tma-panel/assets/js/i18n.js` (MODIFIED)
  - **Dependencias:** Ninguna
  - **Estimación:** 2 horas
  - **Prioridad:** P2
  - **Status:** ✅ COMPLETADO
  - **Completado:** 2026-04-09

- [ ] **TICKET-SEO-007: Google Business Profile API — integración real**
  - **Fuente:** GBP API quota = 0, formulario de solicitud enviado
  - **Historia de Usuario:** Como administrador, quiero datos reales de GBP (reseñas, impressions, actions) en el dashboard para monitorear la presencia local.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: GBP data en dashboard
      Given GBP API quota aprobada por Google
      When el cron sincroniza fuente "gbp"
      Then inserta reviews, impressions, actions reales desde GBP API
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/includes/class-tma-panel-cron.php` (MODIFIED)
    - `.env` (MODIFIED — GBP_ACCOUNT_ID, GBP_LOCATION_ID)
  - **Dependencias:** Aprobación de quota de Google (formulario enviado)
  - **Estimación:** 3 horas
  - **Prioridad:** P1
  - **Status:** 🚫 BLOQUEADO
  - **Bloqueador:** Esperando aprobación de quota de Google Business Profile API

- [ ] **TICKET-DASH-011: Instagram Graph API — integración real**
  - **Fuente:** Dashboard muestra placeholder para Instagram
  - **Historia de Usuario:** Como administrador, quiero datos reales de Instagram (followers, reach, engagement) en el dashboard.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Instagram data en dashboard
      Given Facebook App configurada con IG Business Account
      When el cron sincroniza fuente "instagram"
      Then inserta followers, reach, engagement_rate reales
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/plugins/tma-panel/includes/class-tma-panel-cron.php` (MODIFIED)
    - `.env` (MODIFIED — IG_ACCESS_TOKEN, IG_BUSINESS_ACCOUNT_ID)
  - **Dependencias:** Crear Facebook App + conectar cuenta IG business
  - **Estimación:** 4 horas
  - **Prioridad:** P2
  - **Status:** 🚫 BLOQUEADO
  - **Bloqueador:** Requiere creación de Facebook Developer App + vincular cuenta IG business

- [ ] **TICKET-WP-020: Google Maps embed real en página de contacto**
  - **Fuente:** TICKET-WP-019 completado con placeholder, API key ya disponible
  - **Historia de Usuario:** Como visitante, quiero ver la ubicación de Thor Metal Art en un mapa interactivo en la página de contacto.
  - **Criterios de Aceptación:**
    ```gherkin
    Scenario: Mapa Google Maps visible
      Given API key de Google Maps configurada
      When cargo /contact/
      Then veo mapa embebido con ubicación de Thor Metal Art
      And mapa es responsive y tiene lazy loading

    Scenario: Marker en ubicación correcta
      Given coordenadas de Miami-Dade
      When veo el mapa
      Then marker en dirección de Thor Metal Art
    ```
  - **Archivos:**
    - `data/wordpress/wp-content/themes/theme-tma/parts/contact-map.html` (NEW o MODIFIED)
    - O `data/wordpress/wp-content/mu-plugins/tma-service-pages.php` (MODIFIED)
  - **Dependencias:** Ninguna — API key ya existe y está restringida
  - **Estimación:** 1 hora
  - **Prioridad:** P2
  - **Status:** ⏸️ PENDIENTE

---

## 📊 Resumen

| Fase | Total | ✅ | ⏸️ | 🚫 | Progreso |
|------|-------|----|-----|-----|----------|
| 1 — Infraestructura | 3 | 3 | 0 | 0 | 100% |
| 2 — Dashboard | 3 | 3 | 0 | 0 | 100% |
| 3 — WordPress | 3 | 3 | 0 | 0 | 100% |
| 4 — SEO | 2 | 2 | 0 | 0 | 100% |
| 5 — Seguridad | 1 | 1 | 0 | 0 | 100% |
| 6 — Leads/CRM | 1 | 1 | 0 | 0 | 100% |
| 7 — Portal Docs | 4 | 4 | 0 | 0 | 100% |
| 8 — TMA Panel Base | 10 | 10 | 0 | 0 | 100% |
| 9 — Dashboard Datos Reales | 7 | 5 | 0 | 2 | 71% |
| 10 — Portal Integrado | 3 | 3 | 0 | 0 | 100% |
| 11 — Leads Dinámico | 3 | 3 | 0 | 0 | 100% |
| 12 — Cleanup Docker | 1 | 1 | 0 | 0 | 100% |
| 13 — UI/UX Polish | 4 | 4 | 0 | 0 | 100% |
| 14 — Bug Fixes & Doc UX | 1 | 1 | 0 | 0 | 100% |
| 15 — Website V1: Templates | 10 | 10 | 0 | 0 | 100% |
| 16 — Website V1: Visual | 4 | 4 | 0 | 0 | 100% |
| 17 — Website V1: SEO+Conv | 6 | 6 | 0 | 0 | 100% |
| **18 — Google Ecosystem** | **7** | **4** | **1** | **2** | **57%** |
| **TOTAL** | **71** | **68** | **1** | **2** | **96%** |
