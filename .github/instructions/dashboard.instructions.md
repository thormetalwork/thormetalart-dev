---
description: "Use when editing the client executive dashboard: HTML, CSS, JavaScript, charts, KPIs, data visualization. Covers Chart.js, responsive design, and client-facing UI."
applyTo: "dashboard/**"
---
# Dashboard Guidelines — Thor Metal Art

## Current State
Phase 2 — Currently using static demo data. API integration planned.

## Tech Stack
- HTML5 + CSS3 (inline styles, no external CSS framework)
- Vanilla JavaScript (no React/Vue/Angular)
- Chart.js 4.4.1 for data visualization
- Zero npm dependencies

## Branding
- CSS variables with dark mode ready (not yet implemented)
- Gold accent: `#B8860B` (DarkGoldenrod)
- Dark primary: `#1A1A1A`
- Background: `#F5F5F0`
- Display font: Cormorant Garamond
- Body font: DM Sans / Inter

## Dashboard Sections
1. **Overview** — KPIs, leads by channel chart, GBP impressions sparkline
2. **Google Business Profile** — Reviews, impressions, actions, keyword tracking
3. **Website** — Sessions, conversion rate, top pages
4. **Leads** — Pipeline value, lead table with status tracking
5. **Instagram** — Followers, reach, engagement, top posts
6. **Alerts** — Actionable items requiring attention

## API Integration (Planned)
- Google Business Profile API
- Google Analytics 4
- Instagram Graph API
- Internal CRM/Lead tracking

## Patterns
- Tab-based navigation with JavaScript
- Chart.js charts: use gold (#B8860B) as primary color, dark (#1A1A1A) as secondary
- Responsive design: mobile-first
- Data format: numbers with locale formatting (EN-US)
- Currency: USD with `$` prefix
