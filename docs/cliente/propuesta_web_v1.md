# PROPUESTA: Thor Metal Art — Website V1

> **Fecha:** Marzo 2026
> **Objetivo:** Transformar el sitio WordPress actual (estructura básica con contenido boilerplate) en una web de producción real con estructura profesional, contenido definido y experiencia de usuario competitiva.
> **Base:** Documentación existente del proyecto (Brief de Posicionamiento v2, Copys del Sitio Web, Guía de Fotografía, Arquitectura SEO).

---

## 1. DIAGNÓSTICO: ESTADO ACTUAL

### Lo que YA existe y funciona
| Componente | Estado | Detalle |
|------------|--------|---------|
| Stack Docker (WP + MySQL + Redis) | ✅ Producción | Healthchecks, backups, Redis cache |
| Child theme `thormetalart` | ✅ Configurado | theme.json v3 con paleta, tipografía, layout |
| 5 páginas de servicios | ⚠️ Boilerplate | Texto genérico auto-generado, sin imágenes |
| Portfolio CPT | ⚠️ Vacío | Registrado con taxonomía, 0 proyectos |
| Formulario de contacto | ⚠️ Invisible | Shortcode listo, no colocado en ninguna página |
| Schema SEO (LocalBusiness + Service) | ✅ Activo | JSON-LD en wp_head |
| Meta tags + Open Graph | ✅ Activo | Dinámicos por página |
| Security hardening | ✅ Activo | 9 medidas, XML-RPC bloqueado, REST restringida |
| TMA Panel (dashboard interno) | ✅ v0.4.1 | Leads, docs, KPIs, audit — funcional |

### Lo que FALTA para una web real
| Carencia | Impacto |
|----------|---------|
| **Homepage sin diseño** | Visitante ve template genérico de TwentyTwentyfive |
| **Sin hero section** | No hay primer impacto visual ni CTA visible |
| **Sin imágenes** | Carpeta uploads vacía — web 100% texto |
| **Menú de navegación no configurado** | Visitante no puede navegar entre secciones |
| **Páginas de servicios genéricas** | Textos placeholder, sin FAQs, sin diferenciadores |
| **Página "About" inexistente** | No cuenta la historia de Karel ni el taller |
| **Página "How We Work" inexistente** | No explica el proceso de 5 pasos |
| **Página "Art & Commissions" inexistente** | Motor Artista completamente ausente |
| **Página de contacto inexistente** | Formulario existe pero no tiene página |
| **Portfolio vacío** | 0 proyectos — no hay galería visual |
| **Sin testimonios/reseñas** | No hay social proof |
| **Footer genérico** | Sin NAP, sin redes, sin info de contacto |
| **Sin header/nav personalizado** | Usa header default del parent theme |

---

## 2. PROPUESTA: ESTRUCTURA DEL SITIO V1

### Arquitectura de páginas (10 páginas)

```
HOME (/)
├── Custom Metal Gates (/custom-metal-gates-miami/)
├── Metal Railings (/metal-railings-miami/)
├── Metal Fences (/metal-fences-miami/)
├── Custom Furniture (/custom-metal-furniture-miami/)
├── Metal Stairs (/metal-stairs-miami/)
├── Art & Commissions (/art-commissions/)
├── How We Work (/how-we-work/)
├── Portfolio (/portfolio/)
└── Contact (/contact/)
```

### Navegación principal (header)

```
[LOGO]  Services ▼  |  Art  |  How We Work  |  Portfolio  |  Contact  |  [ES/EN]  |  [GET A QUOTE ►]

         └── Custom Gates
             Metal Railings
             Metal Fences
             Custom Furniture
             Metal Stairs
```

---

## 3. DISEÑO DE CADA PÁGINA

### 3.1 HOME — La página más importante

**Objetivo:** Comunicar en 5 segundos qué hace Thor Metal Art, generar confianza y convertir visitantes en leads.

**Estructura de secciones:**

```
┌─────────────────────────────────────────────────┐
│  HERO SECTION (full-width, imagen de fondo)     │
│                                                  │
│  H1: Custom Metal Fabrication & Art in Miami     │
│  Sub: Precision Craftsmanship. Exclusive Design. │
│       Free Estimates.                            │
│                                                  │
│  [GET A FREE QUOTE ►]    [VIEW OUR ART]         │
│                                                  │
│  Trust bar: Miami-Based | Licensed & Insured |   │
│  ⭐ Stars on Google | Free Estimates | Water Jet │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  SERVICES GRID (6 cards con iconos/imagen)      │
│                                                  │
│  "What We Build"                                 │
│  From structural gates to sculptural art —       │
│  everything custom, everything in Miami.         │
│                                                  │
│  ┌──────┐ ┌──────┐ ┌──────┐                     │
│  │Gates │ │Rails │ │Fence │                      │
│  └──────┘ └──────┘ └──────┘                      │
│  ┌──────┐ ┌──────┐ ┌──────┐                     │
│  │Furn. │ │Stairs│ │Art   │                      │
│  └──────┘ └──────┘ └──────┘                     │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  ABOUT SNIPPET (2 columnas: texto + imagen)     │
│                                                  │
│  "About Thor Metal Art"                          │
│  Miami-based custom metal fabrication studio...  │
│  Founded by Karel Frometa...                     │
│                                                  │
│  [See Our Process ►]                             │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  PORTFOLIO HIGHLIGHT (3 proyectos best-of)      │
│                                                  │
│  "Recent Work"                                   │
│  ┌────────┐ ┌────────┐ ┌────────┐               │
│  │ Img    │ │ Img    │ │ Img    │               │
│  │ Título │ │ Título │ │ Título │               │
│  └────────┘ └────────┘ └────────┘               │
│                                                  │
│  [View Full Portfolio ►]                         │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  SOCIAL PROOF (testimonios / reseñas Google)    │
│                                                  │
│  "What Our Clients Say"                          │
│  ⭐⭐⭐⭐⭐ "Review text" — Name, Miami            │
│  ⭐⭐⭐⭐⭐ "Review text" — Name, Miami            │
│  ⭐⭐⭐⭐⭐ "Review text" — Name, Miami            │
│                                                  │
│  [Read All Reviews on Google ►]                  │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  FINAL CTA (fondo oscuro/metálico)              │
│                                                  │
│  "Ready to Start Your Project?"                  │
│  Get a free estimate. No commitment.             │
│  We respond within 24 hours.                     │
│                                                  │
│  [GET YOUR FREE QUOTE ►]                         │
│                                                  │
│  📞 Phone  💬 WhatsApp  📧 Email                │
│  📍 Serving Miami-Dade & Broward County          │
└─────────────────────────────────────────────────┘
```

### 3.2 PÁGINAS DE SERVICIOS (5 páginas, misma estructura)

Cada servicio usa el contenido definido en el Doc 10 (Copys del Sitio Web):

```
┌─────────────────────────────────────────────────┐
│  HERO SERVICE (imagen del servicio de fondo)    │
│  H1: Custom Metal Gates Miami                    │
│  Sub: Hand-Crafted. Built to Last. Designed...  │
│  [GET A FREE ESTIMATE ►]                         │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  INTRO (150-200 palabras del copy definido)     │
│  + columna lateral con imagen del servicio      │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  WHAT'S INCLUDED (bullets con iconos)           │
│  • Custom design   • Water jet cutting          │
│  • MIG/TIG welding • Custom finish              │
│  • Installation    • Free estimate              │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  PORTFOLIO RELACIONADO (3 proyectos de este     │
│  tipo de servicio, filtrado por tma_project_type│
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  FAQ SECTION (acordeón con 3-4 preguntas)       │
│  Contenido definido en Doc 10 por servicio      │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  SECCIÓN EN ESPAÑOL                             │
│  H2: "Servicio en Español"                       │
│  Texto traducido + CTA en español               │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  CTA FINAL (formulario inline o link a /contact)│
│  "Ready to design your gate?"                    │
│  [GET A FREE ESTIMATE ►]                         │
└─────────────────────────────────────────────────┘
```

### 3.3 ART & COMMISSIONS — Motor Artista

**Página nueva.** Contenido del Doc 10 + Brief de Posicionamiento.

```
┌─────────────────────────────────────────────────┐
│  HERO (imagen de escultura, tono artístico)     │
│  H1: Metal as Art                                │
│  Sub: Original Sculptures & Commissioned Pieces  │
│       by Karel Frometa — Miami                   │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  ARTIST STATEMENT (Karel - voz personal)        │
│  "I've been working with metal for X years..."  │
│  — Karel Frometa, Miami                          │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  COMMISSION PROCESS (4 pasos visuales)          │
│  1. Conversation → 2. Concept & Proposal →      │
│  3. Fabrication → 4. Delivery/Installation      │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  GALERÍA DE ARTE (grid de esculturas/piezas)    │
│  Filtro: All | Wall Art | Sculptures | Install.  │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  CTA: "Commission a Piece"                       │
│  Contact Karel directly: email / WhatsApp        │
└─────────────────────────────────────────────────┘
```

### 3.4 HOW WE WORK — Proceso

**Página nueva.** Contenido definido en Doc 10.

```
┌─────────────────────────────────────────────────┐
│  H1: How We Work                                 │
│  Sub: From First Call to Finished Installation   │
│       — Everything In-House                      │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  5 PASOS VISUALES (timeline vertical)           │
│                                                  │
│  ① Free Estimate → ② Design & Quote →           │
│  ③ Production → ④ Quality Check →               │
│  ⑤ Installation                                  │
│                                                  │
│  Cada paso con icono + descripción + imagen      │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  DIFERENCIADORES                                 │
│  • Everything in-house — no outsource            │
│  • Water jet + MIG/TIG welding                   │
│  • Respond within 24h                            │
│  • Licensed & insured                            │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  CTA: Ready to Start? → Get Free Estimate        │
└─────────────────────────────────────────────────┘
```

### 3.5 PORTFOLIO — Galería de proyectos

```
┌─────────────────────────────────────────────────┐
│  H1: Our Work                                    │
│  Sub: Every piece custom. Every project unique.  │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  FILTROS (botones)                               │
│  All | Gates | Railings | Fences | Furniture |   │
│  Stairs | Art                                    │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  GRID DE PROYECTOS (3 columnas)                 │
│  ┌──────┐ ┌──────┐ ┌──────┐                     │
│  │ Img  │ │ Img  │ │ Img  │                     │
│  │Title │ │Title │ │Title │                     │
│  │Type  │ │Type  │ │Type  │                     │
│  └──────┘ └──────┘ └──────┘                     │
│                                                  │
│  Click → página individual con galería,          │
│  descripción, materiales, ubicación              │
└─────────────────────────────────────────────────┘
```

### 3.6 CONTACT

```
┌─────────────────────────────────────────────────┐
│  H1: Let's Talk About Your Project               │
│  Sub: Free estimate. No commitment.              │
│       We respond within 24 hours.                │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│  2 COLUMNAS:                                     │
│                                                  │
│  [FORMULARIO]          | CONTACTO DIRECTO        │
│  • Nombre*             | 📞 Phone (clickable)    │
│  • Email*              | 💬 WhatsApp (wa.me/)    │
│  • Teléfono*           | 📧 Email                │
│  • Tipo de proyecto*   | 📍 Miami-Dade & Broward │
│  • Descripción         |                         │
│                        | Horario: Mon-Fri, Sat   │
│  [ENVIAR ►]            |                         │
│                        | ✓ Respondemos en 24h    │
│  Trust signals:        | ✓ Cotización gratis     │
│  ✓ 24h response        | ✓ English & Spanish     │
│  ✓ Free estimate       |                         │
│  ✓ EN & ES             |                         │
└─────────────────────────────────────────────────┘
```

---

## 4. IMPLEMENTACIÓN TÉCNICA

### 4.1 Enfoque: Block Templates (FSE)

Usaremos el sistema de Full Site Editing de WordPress con el child theme `thormetalart`:

| Componente | Archivo | Propósito |
|------------|---------|-----------|
| Header | `parts/header.html` | Logo + nav + CTA + idioma |
| Footer | `parts/footer.html` | NAP, servicios, redes, legal |
| Home template | `templates/front-page.html` | Layout completo del home |
| Service template | `templates/single-service.html` | Template para cada servicio |
| Portfolio archive | `templates/archive-tma_portfolio.html` | Grid de proyectos con filtros |
| Portfolio single | `templates/single-tma_portfolio.html` | Detalle de proyecto |
| Contact template | `templates/page-contact.html` | Layout 2 columnas con form |
| Default page | `templates/page.html` | About, How We Work, Art |

### 4.2 Nuevos mu-plugins necesarios

| Plugin | Función |
|--------|---------|
| `tma-navigation.php` | Registra menús y posiciones (header, footer, servicios) |
| `tma-homepage-blocks.php` | Custom blocks o patterns para las secciones del home |
| `tma-testimonials.php` | CPT para testimonios (o ACF repeater en opciones) |

### 4.3 Contenido que requiere input del cliente (Karel)

| Elemento | Estado | Acción necesaria |
|----------|--------|-------------------|
| Fotos de proyectos | ❌ No hay | Karel debe enviar fotos de sus trabajos (mín. 20-30 fotos) |
| Número de teléfono | ❌ Placeholder | Definir teléfono de negocio para web |
| Email de negocio | ❌ Placeholder | Definir email (info@thormetalart.com?) |
| Dirección de taller | ❌ No publicada | Decidir si mostrar address o solo "Miami-Dade" |
| Rating de Google | ❌ Sin reseñas | Necesita al menos 3-5 reseñas en GBP |
| Testimonios de clientes | ❌ Ninguno | Pedir testimonios a clientes anteriores |
| Artist statement de Karel | ⚠️ Template | Karel debe personalizar su declaración artística |
| Años de experiencia | ❌ Placeholder "[X]" | Karel debe confirmar años de experiencia |
| Horario de atención | ❌ No definido | Definir horario para web y GBP |
| Logo alta resolución | ⚠️ Verificar | Necesitamos logo en SVG o PNG de alta res |

---

## 5. PLAN DE EJECUCIÓN POR FASES

### Fase A: Estructura + Templates (sin contenido real)
**Scope:** Crear los templates FSE, header, footer, y estructura de todas las páginas con contenido del Doc 10.

| Ticket | Descripción | Prioridad |
|--------|-------------|-----------|
| TICKET-WP-004 | Header personalizado: logo + nav + CTA + lang switch | P0 |
| TICKET-WP-005 | Footer personalizado: NAP + servicios + redes + legal | P0 |
| TICKET-WP-006 | Homepage completa con todas las secciones | P0 |
| TICKET-WP-007 | Reescribir páginas de servicios con copys del Doc 10 | P1 |
| TICKET-WP-008 | Crear página Art & Commissions | P1 |
| TICKET-WP-009 | Crear página How We Work | P1 |
| TICKET-WP-010 | Crear página Contact con formulario integrado | P1 |
| TICKET-WP-011 | Configurar menú de navegación (header + footer) | P0 |

### Fase B: Contenido visual (requiere fotos de Karel)
| Ticket | Descripción | Prioridad |
|--------|-------------|-----------|
| TICKET-WP-012 | Subir y asignar imágenes hero a cada página | P0 |
| TICKET-WP-013 | Crear 10-15 proyectos en Portfolio con fotos reales | P0 |
| TICKET-WP-014 | Agregar fotos de taller/proceso a "How We Work" | P1 |
| TICKET-WP-015 | Agregar fotos de esculturas a "Art & Commissions" | P1 |

### Fase C: Conversión + SEO
| Ticket | Descripción | Prioridad |
|--------|-------------|-----------|
| TICKET-SEO-003 | Actualizar meta descriptions con contenido final | P1 |
| TICKET-SEO-004 | FAQ Schema markup en páginas de servicios | P1 |
| TICKET-SEO-005 | BreadcrumbList schema | P2 |
| TICKET-SEO-006 | Sitemap XML (Yoast o custom) | P1 |
| TICKET-WP-016 | Sección testimonios con reseñas reales | P2 |
| TICKET-WP-017 | Google Maps embed en contacto (si hay dirección) | P3 |

---

## 6. LO QUE PODEMOS HACER AHORA (sin esperar a Karel)

Con la documentación existente (Doc 10 Copys + Brief v2), podemos construir **toda la Fase A** inmediatamente:

1. **Header y Footer** con branding y estructura de nav
2. **Homepage completa** con todas las secciones usando textos del Doc 10
3. **Reescribir 5 páginas de servicios** con los copys profesionales (intro, features, FAQs, CTA)
4. **Crear Art & Commissions** con template del artist statement
5. **Crear How We Work** con los 5 pasos definidos
6. **Crear Contact** con formulario (shortcode ya existe)
7. **Configurar navegación principal**

**Resultado:** Un sitio web estructuralmente completo, con todo el contenido textual profesional, listo para recibir imágenes reales cuando Karel las proporcione. Se usarán placeholders visuales elegantes (gradientes metálicos, patterns) en lugar de imágenes stock genéricas.

---

## 7. MÉTRICAS DE ÉXITO DE LA V1

| Métrica | Objetivo | Cómo medirlo |
|---------|----------|---------------|
| Páginas funcionales | 10/10 con contenido real | Auditoría manual |
| Formulario de contacto visible | En cada página de servicio + contacto | Test funcional |
| Navegación completa | Header + footer + dropdown servicios | Test navegación |
| Mobile responsive | Todas las páginas < 768px | Chrome DevTools |
| SEO on-page | Meta + Schema + H1 únicos por página | Lighthouse / Schema validator |
| Page speed | LCP < 2.5s, CLS < 0.1 | PageSpeed Insights |
| Accesibilidad | Score > 90 en Lighthouse | Lighthouse |

---

## 8. RESUMEN EJECUTIVO

**Situación actual:** El sitio tiene la infraestructura técnica completa (Docker, seguridad, SEO schema, CRM) pero visualmente es un template vacío de WordPress. No hay homepage real, no hay imágenes, el menú no está configurado, y faltan páginas clave.

**Propuesta:** Implementar 10 páginas con estructura profesional usando el contenido bilingüe ya definido en los documentos del proyecto (Brief v2 + Copys del Sitio Web + Guía de Fotografía). El resultado será un sitio web real y funcional que transmite la identidad dual de Thor Metal Art (Motor Productor + Motor Artista).

**Próximo paso inmediato:** Iniciar la Fase A — crear los templates FSE, header, footer y contenido de todas las páginas con los textos del Doc 10. Esto puede hacerse ahora mismo, sin esperar contenido del cliente.

---

*Propuesta preparada con base en: BACKLOG.md, docs/cliente/03_brief_posicionamiento_v2.md, Doc 10 (Copys Sitio Web), Doc 09 (Guía Fotografía), análisis del child theme actual y mu-plugins existentes.*
