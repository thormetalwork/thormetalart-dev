---
description: "Use when editing SEO files: schema markup, meta tags, sitemap, local SEO, GBP content, or any file related to search engine optimization for Thor Metal Art."
applyTo:
  - data/wordpress/wp-content/mu-plugins/tma-schema.php
  - data/wordpress/wp-content/mu-plugins/tma-meta-tags.php
  - data/wordpress/wp-content/mu-plugins/tma-sitemap.php
  - data/wordpress/wp-content/plugins/tma-panel/includes/class-tma-panel-cron.php
---
# SEO Guidelines — Thor Metal Art

## Ticket Context
TICKET-SEO-003 — Local SEO optimization & content sync (P1). See [BACKLOG.md](../../BACKLOG.md).

## Business Profile
- **Name:** Thor Metal Art LLC
- **Category:** Custom metal fabrication & sculpture
- **Location:** Miami-Dade, FL (serves all South Florida)
- **Languages:** English (primary), Spanish (secondary)
- **URL:** `https://thormetalart.com`

## Target Keywords (Exact Match Priority)
| Keyword | Page |
|---------|------|
| custom metal gates miami | `/portafolio/puertas` |
| metal railings miami | `/portafolio/barandas` |
| metal fences miami | `/portafolio/cercas` |
| custom metal furniture miami | `/portafolio/muebles` |
| metal stairs miami | `/portafolio/escaleras` |
| metal sculptor miami | `/arte-y-comisiones` |
| wrought iron gates miami | `/portafolio/puertas` |

## Schema Markup Rules (`tma-schema.php`)
- **LocalBusiness** block is output at `wp_head` priority 1 — always present, every page
- **Service** schemas only on matching service slugs (gates, railings, fences, furniture, stairs, art)
- **FAQPage** schema per service page — minimum 3 Q&A pairs
- **BreadcrumbList** on all non-home pages
- Helper: `tma_output_jsonld(array $schema)` — use this, never echo raw JSON-LD
- `@id` for LocalBusiness: `home_url('/#localbusiness')` — must be consistent across references
- Never duplicate `LocalBusiness` full data; child schemas reference by `@id`

## Meta Tags Rules (`tma-meta-tags.php`)
- Title format: `{Page Title} | Thor Metal Art Miami`
- Meta description: 140–160 chars, include primary keyword + location
- `og:image`: use featured image when available, fallback to `/wp-content/themes/thormetalart/assets/img/og-default.jpg`
- `og:type`: `website` for standard pages, `article` for blog posts
- Canonical: always output `<link rel="canonical">` — never on 404 pages
- hreflang: `en-US` / `es-US` pair on bilingual pages; use `home_url()` as base
- No index: 404, search results (`is_search()`), and paginated archive past page 2

## XML Sitemap (`tma-sitemap.php`)
- Include: pages, portfolio posts (`tma_portfolio`), blog posts, service taxonomy pages
- Exclude: admin, author pages, attachment pages, `noindex` pages
- Priority: Home = 1.0, Service pages = 0.9, Portfolio = 0.8, Blog = 0.7, Others = 0.5
- `changefreq`: homepage/services = `weekly`, blog = `daily`, portfolio = `monthly`
- Submit to Google Search Console after any structural sitemap change

## Google Business Profile (GBP) Content Sync
- API managed via `TMA_Panel_Cron` (cron hook: `tma_panel_sync_external_kpis`)
- For GBP post creation/updates use the `/seo-audit` prompt or `SEO Specialist` agent
- GBP posts must include: keyword-rich description + CTA + image + service category
- See [google-apis.instructions.md](google-apis.instructions.md) for OAuth2 token details

## Local SEO Checklist
- NAP (Name, Address, Phone) identical in: schema, footer, GBP, and all citations
- Footer contains NAP block in `parts/footer.html`
- Phone format: `(305) XXX-XXXX` — consistent everywhere
- Service area: Miami-Dade, Broward, Palm Beach counties

## Performance Impact
- JSON-LD scripts: output in `<head>` only, non-blocking
- No SEO plugin (Yoast/RankMath) active — all SEO managed via mu-plugins
- Open Graph tags cached via Redis transient `tma_og_meta_{post_id}` (TTL 24h); flush on post save

## Testing
```bash
# Validate schema output
curl -s https://dev.thormetalart.com | grep -A5 'application/ld+json'

# Check meta tags
curl -s https://dev.thormetalart.com | grep -E '<meta|<title|<link rel="canonical"'
```
Use Google's Rich Results Test for schema validation before deploying to PROD.
