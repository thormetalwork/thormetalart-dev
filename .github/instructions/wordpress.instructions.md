---
description: "Use when editing WordPress PHP files, themes, plugins, wp-config, or WordPress customizations. Covers WPCS coding standards, hooks, filters, and security patterns."
applyTo: ["data/wordpress/**/*.php", "data/wordpress/wp-content/**"]
---
# WordPress Guidelines — Thor Metal Art

## Coding Standards
- Follow WordPress Coding Standards (WPCS)
- Use `tma_` prefix for all custom functions, hooks, and options
- Table prefix is `tma_` (non-default for security)
- PHP 8.1 compatibility required

## Security
- Always escape output: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses()`
- Always sanitize input: `sanitize_text_field()`, `absint()`, `wp_unslash()`
- Use nonces for forms: `wp_nonce_field()` / `wp_verify_nonce()`
- Never use `$_GET`/`$_POST` directly without sanitization
- Database queries: use `$wpdb->prepare()` always

## Bilingual Content (EN/ES)
- All user-facing strings must be translatable: `__()`, `_e()`, `esc_html__()`
- Text domain: `thormetalart`
- Consider Polylang or WPML for multilingual management

## Branding in Theme
- Primary: `#1A1A1A`, Accent: `#B8860B`, Background: `#F5F5F0`
- Display font: Cormorant Garamond, Body: DM Sans / Inter
- Tone: Direct, technically accessible

## Redis Object Cache
- Redis is available at `redis:6379` (container name)
- Use `wp_cache_get()` / `wp_cache_set()` — Redis handles persistence
- Never flush cache in production without reason

## Site Sections
Home, Custom Metal Gates, Metal Railings, Metal Fences, Custom Furniture, Metal Stairs, Art & Commissions, How We Work, Portfolio, Contact
