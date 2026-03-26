/**
 * Thor Metal Art — Dashboard API Proxy
 * TICKET-DASH-003
 *
 * Proxies requests to Google Business Profile, Google Analytics,
 * and Instagram Graph APIs without exposing keys to the browser.
 *
 * Environment variables (from .env):
 *   GBP_API_KEY        — Google Business Profile API key
 *   GA4_PROPERTY_ID    — Google Analytics 4 property ID
 *   GA4_SERVICE_KEY    — GA4 service account key (JSON, base64)
 *   IG_ACCESS_TOKEN    — Instagram Graph API long-lived token
 *   API_PORT           — Server port (default: 3001)
 */

'use strict';

var express = require('express');
var https = require('https');

var app = express();
var PORT = process.env.API_PORT || 3001;

/* ── Health Check ── */
app.get('/api/health', function (_req, res) {
  res.json({ status: 'ok', service: 'thormetalart-dashboard-api' });
});

/* ── Google Business Profile ── */
app.get('/api/gbp', function (_req, res) {
  var apiKey = process.env.GBP_API_KEY;
  if (!apiKey) {
    return res.json(getDemoGBP());
  }
  // TODO: Implement real GBP API call when key is configured
  res.json(getDemoGBP());
});

/* ── Google Analytics 4 ── */
app.get('/api/ga', function (_req, res) {
  var propertyId = process.env.GA4_PROPERTY_ID;
  if (!propertyId) {
    return res.json(getDemoGA());
  }
  // TODO: Implement real GA4 API call when credentials are configured
  res.json(getDemoGA());
});

/* ── Instagram Graph API ── */
app.get('/api/ig', function (_req, res) {
  var token = process.env.IG_ACCESS_TOKEN;
  if (!token) {
    return res.json(getDemoIG());
  }
  // TODO: Implement real Instagram API call when token is configured
  res.json(getDemoIG());
});

/* ── Leads (manual / internal) ── */
app.get('/api/leads', function (_req, res) {
  res.json(getDemoLeads());
});

/* ══════════════════════════════════════════════
   Demo Data — used until real APIs are connected
   ══════════════════════════════════════════════ */

function getDemoGBP() {
  return {
    source: 'demo',
    reviews: { total: 8, rating: 4.9 },
    impressions: {
      labels: ['Sep', 'Oct', 'Nov', 'Dic', 'Ene', 'Feb'],
      search: [180, 240, 310, 480, 720, 1120],
      maps: [90, 110, 150, 210, 380, 720]
    },
    actions: { web_clicks: 38, calls: 12, directions: 6, messages: 3 },
    weekly_impressions: [210, 280, 340, 410, 480, 590, 720, 840]
  };
}

function getDemoGA() {
  return {
    source: 'demo',
    sessions: 312,
    users: 248,
    conversion_rate: 4.2,
    forms_submitted: 13,
    avg_time: '2:14',
    weekly_sessions: [18, 24, 32, 38, 45, 58, 72, 90],
    traffic_sources: { organic: 48, instagram: 31, direct: 14, other: 7 },
    top_pages: [
      { page: 'Custom Gates Miami', sessions: 108 },
      { page: 'Home', sessions: 84 },
      { page: 'Metal Railings', sessions: 59 },
      { page: 'Arte & Comisiones', sessions: 38 },
      { page: 'Contacto', sessions: 23 }
    ]
  };
}

function getDemoIG() {
  return {
    source: 'demo',
    followers: 1284,
    monthly_reach: 8420,
    engagement_rate: 5.8,
    new_followers: 47,
    posts: { total: 12, feed: 8, reels: 4 },
    weekly_reach: [520, 680, 740, 810, 920, 1040, 1180, 1340],
    top_posts: [
      { title: 'Gate Coral Gables — proceso', reach: 890, likes: 62, saves: 3, type: 'Reel' },
      { title: 'Escultura en proceso — taller', reach: 620, likes: 54, saves: 7, type: 'Post' },
      { title: 'Railing Brickell — antes/después', reach: 510, likes: 41, saves: 2, type: 'Post' }
    ]
  };
}

function getDemoLeads() {
  return {
    source: 'demo',
    total: 7,
    closed: 2,
    close_rate: 29,
    pipeline_value: 28400,
    avg_ticket: 4200,
    by_channel: { instagram: 3, gbp: 2, referido: 2, angi: 1, web: 1 },
    pipeline: [
      { name: 'María González', project: 'Gate residencial · Coral Gables', channel: 'Instagram', status: 'Cotización', value: 3800 },
      { name: 'Roberto Méndez', project: 'Railings 3 townhouses · Doral', channel: 'GBP', status: 'Negociación', value: 11500 },
      { name: 'Sunrise Contracting', project: 'Fence comercial · Hialeah', channel: 'Angi', status: 'Nuevo', value: 6200 },
      { name: 'Jennifer Park', project: 'Mesa custom · Brickell', channel: 'Referido', status: 'Cerrado', value: 2200 },
      { name: 'Carlos Ruiz', project: 'Escultura comisión · Miami Beach', channel: 'Instagram', status: 'Nuevo', value: 4700 }
    ]
  };
}

/* ── Start Server ── */
app.listen(PORT, '0.0.0.0', function () {
  console.log('[dashboard-api] listening on port ' + PORT);
});
