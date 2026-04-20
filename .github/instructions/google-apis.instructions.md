---
description: "Use when editing Google API integrations, external data fetching, OAuth tokens, cron sync, GA4, Search Console, Google Business Profile, Instagram Graph API, Maps Embed, reCAPTCHA verification, Schema Markup (JSON-LD), SEO Meta Tags, XML Sitemap, or Google Fonts."
applyTo:
  - data/wordpress/wp-content/plugins/tma-panel/includes/class-tma-panel-cron.php
  - data/wordpress/wp-content/plugins/tma-panel/includes/class-tma-panel-google-auth.php
  - data/wordpress/wp-content/mu-plugins/tma-contact-form.php
  - data/wordpress/wp-content/mu-plugins/tma-service-pages.php
  - data/wordpress/wp-content/mu-plugins/tma-schema.php
  - data/wordpress/wp-content/mu-plugins/tma-meta-tags.php
  - data/wordpress/wp-content/mu-plugins/tma-sitemap.php
  - data/wordpress/wp-content/themes/thormetalart/functions.php
---
# Google & External API Integration Rules

## API Inventory

| API | File | Status | Auth |
|-----|------|--------|------|
| GA4 Data API | `class-tma-panel-cron.php` | ✅ Active | OAuth2 via `TMA_Panel_Google_Auth` |
| Search Console | `class-tma-panel-cron.php` | ✅ Active | OAuth2 via `TMA_Panel_Google_Auth` |
| Google Business Profile | `class-tma-panel-cron.php` | ⏳ Placeholder | `GBP_API_KEY` env var |
| Instagram Graph API | `class-tma-panel-cron.php` | ⏳ Placeholder | `IG_ACCESS_TOKEN` env var |
| Google Maps Embed | `tma-service-pages.php` | ✅ Active | `GCP_API_KEY` env var |
| reCAPTCHA Enterprise v3 | `tma-contact-form.php` | ✅ Active | `GCP_SERVER_API_KEY` env var |
| Schema Markup (JSON-LD) | `tma-schema.php` | ✅ Active | None (output only) |
| SEO Meta Tags & Open Graph | `tma-meta-tags.php` | ✅ Active | None (output only) |
| XML Sitemap | `tma-sitemap.php` | ✅ Active | None (output only) |
| Google Fonts | `theme functions.php` + `theme.json` | ✅ Active | Public CDN |

## OAuth2 Token Management

All Google APIs (GA4, GSC) use `TMA_Panel_Google_Auth`:
- Token endpoint: `https://oauth2.googleapis.com/token`
- Transient key: `tma_google_access_token` (TTL ~55 min)
- Auto-refreshes via `GOOGLE_OAUTH_REFRESH_TOKEN`
- Use `TMA_Panel_Google_Auth::api_post($url, $body)` — never call `wp_remote_post` directly for Google APIs

## Environment Variables (from docker-compose.yml)

```
GA4_PROPERTY_ID, GA4_MEASUREMENT_ID
GOOGLE_OAUTH_CLIENT_ID, GOOGLE_OAUTH_CLIENT_SECRET, GOOGLE_OAUTH_REFRESH_TOKEN
GSC_SITE_URL, GCP_API_KEY, GCP_SERVER_API_KEY
RECAPTCHA_SITE_KEY, GBP_API_KEY, IG_ACCESS_TOKEN
```

Always check `defined('CONSTANT')` or `getenv()` before using. Return early with empty array if missing.

## Cron Sync Pattern

- Hook: `tma_panel_sync_external_kpis` (daily via `TMA_Panel_Cron`)
- KPI storage: `{prefix}panel_kpis` table with upsert on `(metric, period, category)`
- Period format: `Y-m` (e.g., `2026-04`)
- JSON metrics: stored as transient `tma_kpi_{category}_{metric}` (TTL 1 day)

## Security Rules

- **Never log or expose API keys** — use `wp_remote_post` with keys in headers/body only
- **reCAPTCHA**: score threshold = 0.5; fail open on network errors (log + allow submission)
- **Rate limiting**: 1 lead submission per IP per 3 minutes (`tma_lead_rate_{ip_hash[:16]}`)
- **Maps Embed**: sanitize address with `esc_attr()` before embedding in iframe URL

## Error Handling

- All API calls must check `is_wp_error($response)` and `wp_remote_retrieve_response_code()`
- Log failures with `error_log("TMA API: ...")` — never expose to frontend
- Placeholder APIs (GBP, Instagram) must return `array()` gracefully when config is missing

## Schema Markup (JSON-LD) — `tma-schema.php`

- **LocalBusiness** schema at `wp_head` priority 1 — global on every page
- **Service** schema at priority 2 — only on service slugs (gates, railings, fences, furniture, stairs, art)
- **FAQPage** schema at priority 3 — per-service FAQs embedded in structured data
- **BreadcrumbList** schema — navigation path for Google search breadcrumbs
- Use `tma_output_jsonld()` — centralized helper that `json_encode`s + wraps in `<script type="application/ld+json">`
- Keep `@id` anchors consistent: `home_url('/#localbusiness')` for cross-referencing
- Provider always references `@id` of LocalBusiness (do not duplicate full data)

## SEO Meta Tags — `tma-meta-tags.php`

- Title override via `pre_get_document_title` filter
- `<meta name="description">` based on per-slug mapping in `tma_get_seo_mapping()`
- Open Graph: `og:site_name`, `og:locale`, `og:type`, `og:title`, `og:description`, `og:url`, `og:image`
- Twitter Card: `summary_large_image` with matching title/description/image
- `hreflang` tags: `en`, `es`, `x-default` — on pages and portfolio singles
- Canonical URL: on front page, singular posts, portfolio archives
- Add new pages to `tma_get_seo_mapping()` array when creating service pages

## XML Sitemap — `tma-sitemap.php`

- Custom rewrite rule: `sitemap.xml` → `index.php?tma_sitemap=1`
- Includes: all published pages + all published `tma_portfolio` posts + portfolio archive
- Output: XML with `<urlset>`, `<url>`, `<loc>`, `<lastmod>` (ISO 8601)
- Submit to Google Search Console via `GSC_SITE_URL` domain property
- After adding new pages: flush rewrite rules (`flush_rewrite_rules()`) if needed

## Google Fonts

- Loaded via theme `functions.php` with `wp_enqueue_style()` and `theme.json`
- Fonts: **Cormorant Garamond** (display/headings), **DM Sans** (body), **Inter** (alt body)
- CDN: `https://fonts.googleapis.com/css2?family=...&display=swap`
- Preconnect hints: `fonts.googleapis.com` + `fonts.gstatic.com` (crossorigin)
- CSP headers must whitelist both domains
- Do NOT self-host — Google Fonts CDN provides optimal caching and subsetting
