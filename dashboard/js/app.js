/* ═══════════════════════════════════════════════════════════
   Thor Metal Art — Client Dashboard JavaScript
   TICKET-DASH-001: Extracted from inline HTML
   Chart.js 4.4.1 — loaded via CDN in index.html
   ═══════════════════════════════════════════════════════════ */

/* ── Tab Navigation ── */
function showTab(id) {
  document.querySelectorAll('.section').forEach(function(s) {
    s.classList.remove('active');
  });
  document.querySelectorAll('.tab').forEach(function(t) {
    t.classList.remove('active');
  });
  document.getElementById('sec-' + id).classList.add('active');
  // Mark the clicked tab as active
  var tabs = document.querySelectorAll('.tab');
  tabs.forEach(function(t) {
    if (t.getAttribute('data-tab') === id) {
      t.classList.add('active');
    }
  });
  // Lazy-draw charts only when their tab is first opened
  if (id === 'gbp') drawGBP();
  if (id === 'web') drawWeb();
  if (id === 'ig') drawIG();
  if (id === 'leads') drawLeads2();
}

/* ── Chart Helper ── */
function chartDef(type, labels, datasets, opts) {
  opts = opts || {};
  return {
    type: type,
    data: { labels: labels, datasets: datasets },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: Object.assign({ legend: { display: false } }, opts.legend || {}),
      scales: Object.assign({
        x: { grid: { display: false }, ticks: { font: { size: 10 } } },
        y: { grid: { color: 'rgba(128,128,128,.1)' }, ticks: { font: { size: 10 } } }
      }, opts.scales || {})
    }
  };
}

/* ── GBP Sparkline (Overview) ── */
var gbpWeeks = ['S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7', 'S8'];
var gbpVals = [210, 280, 340, 410, 480, 590, 720, 840];

function drawSpark() {
  var c = document.getElementById('spark-gbp');
  if (!c) return;
  c.innerHTML = '';
  var mx = Math.max.apply(null, gbpVals);
  gbpVals.forEach(function(v) {
    var b = document.createElement('div');
    b.className = 'bar';
    b.style.height = Math.round(v / mx * 90) + 'px';
    b.style.flex = '1';
    b.title = v + ' impresiones';
    c.appendChild(b);
  });
}

/* ── Overview: Leads by Channel ── */
function drawLeadsOverview() {
  var el = document.getElementById('ch-leads');
  if (!el) return;
  new Chart(el, chartDef('bar',
    ['Instagram', 'GBP', 'Referido', 'Angi', 'Web directa'],
    [{
      data: [3, 2, 2, 1, 1],
      backgroundColor: ['#c2185b', '#137333', '#B8860B', '#f57f17', '#1a73e8'],
      borderRadius: 4
    }]
  ));
}

/* ── GBP: Impressions Search vs Maps ── */
var gbpChart;
function drawGBP() {
  if (gbpChart) return;
  gbpChart = new Chart(document.getElementById('ch-gbp'), {
    type: 'bar',
    data: {
      labels: ['Sep', 'Oct', 'Nov', 'Dic', 'Ene', 'Feb'],
      datasets: [
        {
          label: 'Search',
          data: [180, 240, 310, 480, 720, 1120],
          backgroundColor: 'rgba(19,115,51,.7)',
          borderRadius: 4
        },
        {
          label: 'Maps',
          data: [90, 110, 150, 210, 380, 720],
          backgroundColor: 'rgba(26,115,232,.5)',
          borderRadius: 4
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: true,
          position: 'bottom',
          labels: { font: { size: 10 }, boxWidth: 10, padding: 8 }
        }
      },
      scales: {
        x: { grid: { display: false }, ticks: { font: { size: 10 } } },
        y: { grid: { color: 'rgba(128,128,128,.1)' }, ticks: { font: { size: 10 } } }
      }
    }
  });
}

/* ── Web: Sessions per Week ── */
var webChart;
function drawWeb() {
  if (webChart) return;
  webChart = new Chart(document.getElementById('ch-web'), chartDef('line',
    ['S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7', 'S8'],
    [{
      data: [18, 24, 32, 38, 45, 58, 72, 90],
      borderColor: '#B8860B',
      backgroundColor: 'rgba(184,134,11,.1)',
      fill: true,
      tension: .4,
      pointRadius: 3
    }]
  ));
}

/* ── Instagram: Weekly Reach ── */
var igChart;
function drawIG() {
  if (igChart) return;
  igChart = new Chart(document.getElementById('ch-ig'), chartDef('line',
    ['S1', 'S2', 'S3', 'S4', 'S5', 'S6', 'S7', 'S8'],
    [{
      data: [520, 680, 740, 810, 920, 1040, 1180, 1340],
      borderColor: '#c2185b',
      backgroundColor: 'rgba(194,24,91,.08)',
      fill: true,
      tension: .4,
      pointRadius: 3
    }]
  ));
}

/* ── Leads Tab: Leads by Channel ── */
var leads2Chart;
function drawLeads2() {
  if (leads2Chart) return;
  leads2Chart = new Chart(document.getElementById('ch-leads2'), chartDef('bar',
    ['Instagram', 'GBP', 'Referido', 'Angi', 'Web'],
    [{
      data: [3, 2, 2, 1, 1],
      backgroundColor: ['#c2185b', '#137333', '#B8860B', '#f57f17', '#1a73e8'],
      borderRadius: 4
    }]
  ));
}

/* ── Initialize on DOM Ready ── */
document.addEventListener('DOMContentLoaded', function() {
  drawSpark();
  drawLeadsOverview();
});
