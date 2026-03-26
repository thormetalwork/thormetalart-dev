---
name: wordpress-dev
description: "Develop WordPress themes, plugins, and custom functionality for Thor Metal Art. Use when creating pages, customizing themes, adding plugins, configuring WooCommerce, or building custom post types. Covers WPCS, bilingual content, SEO, and branding."
argument-hint: "What to build (e.g., custom post type for portfolio, contact form, new service page)"
---

# WordPress Development — Thor Metal Art

## When to Use
- Creating new WordPress pages or templates
- Developing custom themes or child themes
- Building or modifying plugins
- Creating custom post types (Portfolio, Services, Testimonials)
- Adding custom fields (ACF patterns)
- Implementing contact forms
- Configuring SEO meta tags and schema markup
- Setting up bilingual content (EN/ES)

## Environment
- **WordPress:** 6.9 on PHP 8.1 + Apache
- **Database:** MySQL 8.0, db: `thormetalart_wp`, prefix: `tma_`
- **Cache:** Redis 7 at `redis:6379`
- **Files:** `data/wordpress/wp-content/` (themes, plugins, uploads)
- **URL:** `dev.thormetalart.com`

## Site Architecture

### Pages
| Page | Slug | Target Keyword |
|------|------|----------------|
| Home | `/` | thor metal art miami |
| Custom Metal Gates | `/custom-metal-gates-miami/` | custom metal gates miami |
| Metal Railings | `/metal-railings-miami/` | metal railings miami |
| Metal Fences | `/metal-fences-miami/` | metal fences miami |
| Custom Furniture | `/custom-furniture-miami/` | custom furniture miami |
| Metal Stairs | `/metal-stairs-miami/` | metal stairs miami |
| Art & Commissions | `/art-commissions/` | metal sculptor miami |
| How We Work | `/how-we-work/` | custom metal fabrication |
| Portfolio | `/portfolio/` | metal work portfolio miami |
| Contact | `/contact/` | metal fabrication quote miami |

## Procedures

### Create Custom Post Type
1. Create file: `data/wordpress/wp-content/mu-plugins/tma-post-types.php`
2. Register with `tma_` prefix: `register_post_type('tma_portfolio', [...])`
3. Add taxonomy if needed: `register_taxonomy('tma_project_type', ...)`
4. Flush rewrite rules: Visit Settings → Permalinks → Save

### Create Page Template
1. Create in theme: `data/wordpress/wp-content/themes/THEME/template-{name}.php`
2. Add template header: `/* Template Name: {Name} */`
3. Follow branding: `#1A1A1A`, `#B8860B`, `#F5F5F0`
4. Make bilingual: `__('Text', 'thormetalart')`

### Add Custom Fields
Prefer ACF (if installed) or native `register_meta()`:
```php
function tma_register_meta_fields() {
    register_meta('post', 'tma_project_value', [
        'type' => 'string',
        'single' => true,
        'show_in_rest' => true,
        'sanitize_callback' => 'sanitize_text_field',
    ]);
}
add_action('init', 'tma_register_meta_fields');
```

### SEO Schema Markup
Add LocalBusiness JSON-LD to `functions.php`:
```php
function tma_local_business_schema() {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => 'Thor Metal Art',
        'address' => ['@type' => 'PostalAddress', 'addressLocality' => 'Miami', 'addressRegion' => 'FL'],
        'telephone' => '+1-XXX-XXX-XXXX',
        'url' => home_url(),
    ];
    echo '<script type="application/ld+json">' . wp_json_encode($schema) . '</script>';
}
add_action('wp_head', 'tma_local_business_schema');
```

## Reference Files
- [WordPress coding standards](./references/wpcs-patterns.md)
