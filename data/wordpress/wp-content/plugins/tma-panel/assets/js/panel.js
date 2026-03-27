/**
 * TMA Panel — SPA Core
 *
 * Vanilla JS single-page application for the Thor Metal Art panel.
 * Routes: #dashboard, #documents, #leads, #notes, #audit
 *
 * @package ThorMetalArt\Panel
 * @since   0.1.0
 */

(function () {
	'use strict';

	const { apiBase, nonce, user } = window.TMA_PANEL;
	const t = window.TMA_i18n ? window.TMA_i18n.t : function (key) { return key; };
	const DASHBOARD_REFRESH_SECONDS = 120;
	let dashboardRefreshInterval = null;
	let dashboardRefreshRemaining = DASHBOARD_REFRESH_SECONDS;
	let activeCharts = [];

	/* ═══════════════════════════════════════════════════════════════
	   API Helper
	   ═══════════════════════════════════════════════════════════════ */

	async function api(endpoint, opts = {}) {
		const url = `${apiBase}${endpoint}`;
		const headers = {
			'X-WP-Nonce': nonce,
			'Content-Type': 'application/json',
		};

		const response = await fetch(url, {
			credentials: 'same-origin',
			headers,
			...opts,
		});

		if (!response.ok) {
			throw new Error(`API ${response.status}: ${response.statusText}`);
		}

		return response.json();
	}

	/* ═══════════════════════════════════════════════════════════════
	   Helpers
	   ═══════════════════════════════════════════════════════════════ */

	function escapeHtml(str) {
		const div = document.createElement('div');
		div.textContent = str;
		return div.innerHTML;
	}

	function formatDate(dateStr) {
		if (!dateStr) return '—';
		const d = new Date(dateStr);
		return d.toLocaleDateString('es-ES', { day: '2-digit', month: 'short', year: 'numeric' });
	}

	function showError(container, message) {
		container.innerHTML = `<div class="card"><p class="text-danger">${escapeHtml(message)}</p></div>`;
	}

	function getErrorMessage(err, fallback) {
		if (err && typeof err.message === 'string' && err.message.trim()) {
			return err.message;
		}
		if (typeof err === 'string' && err.trim()) {
			return err;
		}
		if (err && err.type === 'error') {
			return fallback || 'No se pudo cargar un recurso externo.';
		}
		return fallback || 'Error desconocido.';
	}

	function clearDashboardAutoRefresh() {
		if (dashboardRefreshInterval) {
			clearInterval(dashboardRefreshInterval);
			dashboardRefreshInterval = null;
		}
	}

	function destroyCharts() {
		activeCharts.forEach(function (chart) {
			try { chart.destroy(); } catch (e) { /* already destroyed */ }
		});
		activeCharts = [];
	}

	function showToast(message, type) {
		type = type || 'info';
		var container = document.getElementById('tma-toast-container');
		if (!container) {
			container = document.createElement('div');
			container.id = 'tma-toast-container';
			container.className = 'toast-container';
			document.body.appendChild(container);
		}
		var toast = document.createElement('div');
		toast.className = 'toast toast--' + type;
		toast.textContent = message;
		container.appendChild(toast);
		requestAnimationFrame(function () { toast.classList.add('toast--visible'); });
		setTimeout(function () {
			toast.classList.remove('toast--visible');
			toast.addEventListener('transitionend', function () { toast.remove(); });
		}, 3500);
	}

	function updateSidebarBadge(section, count) {
		var link = document.querySelector('.nav-link[data-section="' + section + '"]');
		if (!link) return;
		var badge = link.querySelector('.nav-badge');
		if (count > 0) {
			if (!badge) {
				badge = document.createElement('span');
				badge.className = 'nav-badge';
				link.appendChild(badge);
			}
			badge.textContent = String(count);
		} else if (badge) {
			badge.remove();
		}
	}

	function getDashboardRefreshLabel(seconds) {
		const template = t('dashboard.auto_refresh_in') || 'Autoactualizacion en {{s}}s';
		return template.replace('{{s}}', String(seconds));
	}

	function updateDashboardRefreshCounter() {
		const counter = document.getElementById('tma-refresh-countdown');
		if (!counter) {
			return;
		}
		counter.textContent = getDashboardRefreshLabel(dashboardRefreshRemaining);
	}

	function startDashboardAutoRefresh(container) {
		clearDashboardAutoRefresh();
		dashboardRefreshRemaining = DASHBOARD_REFRESH_SECONDS;
		updateDashboardRefreshCounter();

		dashboardRefreshInterval = setInterval(async function () {
			if ((location.hash || '#dashboard') !== '#dashboard') {
				clearDashboardAutoRefresh();
				return;
			}

			dashboardRefreshRemaining -= 1;
			if (dashboardRefreshRemaining <= 0) {
				clearDashboardAutoRefresh();
				await renderDashboard(container);
				return;
			}

			updateDashboardRefreshCounter();
		}, 1000);
	}

	/* ═══════════════════════════════════════════════════════════════
	   Router (hash-based)
	   ═══════════════════════════════════════════════════════════════ */

	const routes = {
		dashboard: renderDashboard,
		documents: renderDocuments,
		leads: renderLeads,
		notes: renderNotes,
		audit: renderAudit,
	};

	const sectionTitleKeys = {
		dashboard: 'nav.dashboard',
		documents: 'nav.documents',
		leads: 'nav.leads',
		notes: 'nav.notes',
		audit: 'nav.audit',
	};

	function navigate() {
		clearDashboardAutoRefresh();
		destroyCharts();
		const hash = (location.hash || '#dashboard').slice(1);
		const section = routes[hash] ? hash : 'dashboard';
		const render = routes[section];

		// Update active nav link.
		document.querySelectorAll('.nav-link').forEach(function (link) {
			link.classList.toggle('active', link.dataset.section === section);
		});

		// Update page title.
		const title = document.getElementById('tma-page-title');
		if (title) {
			title.textContent = t(sectionTitleKeys[section] || section);
		}

		// Render section.
		const content = document.getElementById('tma-content');
		if (content && render) {
			content.innerHTML = '<div class="loading">Cargando...</div>';
			render(content);
		}
	}

	/* ═══════════════════════════════════════════════════════════════
	   Section: Dashboard
	   ═══════════════════════════════════════════════════════════════ */

	async function renderDashboard(container) {
		try {
			clearDashboardAutoRefresh();
			destroyCharts();
			var scrollParent = container.closest('.main') || container;
			var savedScroll = scrollParent.scrollTop;
			await ensureChartJs();
			const data = await api('/dashboard');
			const kpis = data.kpis || {};
			const counts = data.counts || {};
			const history = data.history || {};
			const leadSources = data.lead_sources || [];
			const gbp = data.gbp || {};
			const web = data.web || {};
			const instagram = data.instagram || {};
			const isDemo = !!data.is_demo;
			const newAttention = data.new_attention || {};
			const attentionCount = Number(newAttention.high_value_leads || 0);
			const newLeadsCount = Number(newAttention.new_leads || 0);
			const docProgress = data.doc_progress || {};
			const recentActivity = data.recent_activity || [];

			// Build contextual alerts
			var alertsHtml = '';
			var hasAlerts = false;
			if (newLeadsCount > 0) {
				hasAlerts = true;
				var leadsAlertText = (t('dashboard.new_leads_alert') || '{{n}} lead(s) nuevos requieren atención').replace('{{n}}', String(newLeadsCount));
				alertsHtml += '<a href="#leads" class="dash-alert-card dash-alert-card--gold">'
					+ '<span class="dash-alert-card__icon">📥</span>'
					+ '<div class="dash-alert-card__body">'
					+ '<p class="dash-alert-card__title">' + escapeHtml(leadsAlertText) + '</p>'
					+ '<p class="dash-alert-card__detail">' + escapeHtml('Hay leads pendientes de seguimiento.') + '</p>'
					+ '</div>'
					+ '<span class="dash-alert-card__link">' + escapeHtml(t('dashboard.view_leads')) + ' →</span>'
					+ '</a>';
			}
			var pendingDocs = Number(docProgress.pending || 0) + Number(docProgress.changes || 0);
			if (pendingDocs > 0) {
				hasAlerts = true;
				var docsAlertText = (t('dashboard.pending_docs_alert') || '{{n}} documento(s) pendientes de revisión').replace('{{n}}', String(pendingDocs));
				alertsHtml += '<a href="#documents" class="dash-alert-card dash-alert-card--warning">'
					+ '<span class="dash-alert-card__icon">📄</span>'
					+ '<div class="dash-alert-card__body">'
					+ '<p class="dash-alert-card__title">' + escapeHtml(docsAlertText) + '</p>'
					+ '<p class="dash-alert-card__detail">' + escapeHtml(t('dashboard.doc_approval')) + ': ' + Number(docProgress.approved || 0) + '/' + Number(docProgress.total || 0) + '</p>'
					+ '</div>'
					+ '<span class="dash-alert-card__link">' + escapeHtml(t('dashboard.view_docs')) + ' →</span>'
					+ '</a>';
			}
			if (!hasAlerts) {
				alertsHtml = '<a href="#" class="dash-alert-card dash-alert-card--success">'
					+ '<span class="dash-alert-card__icon">✅</span>'
					+ '<div class="dash-alert-card__body">'
					+ '<p class="dash-alert-card__title">Todo al día</p>'
					+ '<p class="dash-alert-card__detail">No hay acciones pendientes.</p>'
					+ '</div></a>';
			}

			// Build doc progress card
			var docTotal = Number(docProgress.total || 0);
			var docApproved = Number(docProgress.approved || 0);
			var docPercent = docTotal > 0 ? Math.round((docApproved / docTotal) * 100) : 0;
			var docProgressHtml = '<div class="dash-doc-progress" id="tma-dash-doc-progress">'
				+ '<div class="dash-doc-progress__header">'
				+ '<span class="dash-doc-progress__title">' + escapeHtml(t('dashboard.doc_progress')) + '</span>'
				+ '<div class="dash-doc-progress__stats">'
				+ '<span>✅ ' + docApproved + '</span>'
				+ '<span>⏳ ' + Number(docProgress.pending || 0) + '</span>'
				+ '<span>📝 ' + Number(docProgress.changes || 0) + '</span>'
				+ '</div></div>'
				+ '<div class="progress-bar__header"><span>' + escapeHtml(t('dashboard.doc_approval')) + '</span><strong>' + docApproved + '/' + docTotal + ' (' + docPercent + '%)</strong></div>'
				+ '<div class="progress-bar"><div class="progress-bar__fill" style="width:' + docPercent + '%"></div></div>'
				+ '</div>';

			// Build recent activity
			var activityHtml = '';
			if (recentActivity.length) {
				var activityItems = '';
				recentActivity.forEach(function (act) {
					var icon = '📝';
					if (act.action === 'login') icon = '🔑';
					else if (act.action === 'approve_document') icon = '✅';
					else if (act.action === 'update_lead_status') icon = '📥';
					else if (act.action === 'create_note') icon = '💬';
					else if (act.action === 'view_document') icon = '👁️';
					activityItems += '<div class="activity-item">'
						+ '<span class="activity-item__icon">' + icon + '</span>'
						+ '<div class="activity-item__body">'
						+ '<p class="activity-item__text"><strong>' + escapeHtml(act.user_name || '') + '</strong> — ' + escapeHtml(act.action || '') + (act.entity_type ? ' (' + escapeHtml(act.entity_type) + ')' : '') + '</p>'
						+ '<p class="activity-item__meta">' + formatDate(act.created_at) + '</p>'
						+ '</div></div>';
				});
				activityHtml = '<div class="section-card">'
					+ '<h2 class="card__title">' + escapeHtml(t('dashboard.recent_activity')) + '</h2>'
					+ activityItems + '</div>';
			}

			container.innerHTML = `
				<div class="dashboard-actions">
					${isDemo ? '<span class="badge badge--warning mr-auto">(Datos de ejemplo)</span>' : ''}
					<span class="badge badge--info mr-2" id="tma-refresh-countdown">${escapeHtml(getDashboardRefreshLabel(DASHBOARD_REFRESH_SECONDS))}</span>
					<button class="btn btn--ghost mr-2" id="tma-refresh-now">
						↻ ${escapeHtml(t('dashboard.refresh_now') || 'Actualizar ahora')}
					</button>
					<button class="btn btn--accent" id="tma-export-btn">
						📋 ${escapeHtml(t('dashboard.export') || 'Exportar resumen')}
					</button>
				</div>
				<div class="dash-alerts">${alertsHtml}</div>
				${docProgressHtml}
				<div class="kpi-grid">
					<div class="kpi-card">
						<span class="kpi-card__label">Reviews GBP</span>
						<span class="kpi-card__value">${parseInt(counts.reviews) || 0}</span>
						<span class="kpi-card__meta">${renderTrend(kpis.reviews)}</span>
					</div>
					<div class="kpi-card">
						<span class="kpi-card__label">Impressions</span>
						<span class="kpi-card__value">${parseInt(counts.impressions) || 0}</span>
						<span class="kpi-card__meta">${renderTrend(kpis.impressions)}</span>
					</div>
					<div class="kpi-card">
						<span class="kpi-card__label">Sessions Web</span>
						<span class="kpi-card__value">${parseInt(counts.sessions) || 0}</span>
						<span class="kpi-card__meta">${renderTrend(kpis.sessions)}</span>
					</div>
					<div class="kpi-card kpi-card--accent">
						<span class="kpi-card__label">Leads Totales</span>
						<span class="kpi-card__value">${parseInt(counts.leads) || 0}</span>
						<span class="kpi-card__meta">${renderTrend(kpis.leads)}</span>
					</div>
				</div>
				<div class="grid grid--2 mt-4">
					<div class="card">
						<h2 class="card__title">Impressions (6 meses)</h2>
						<canvas id="tma-chart-impressions" height="180"></canvas>
					</div>
					<div class="card">
						<h2 class="card__title">Leads por canal</h2>
						<canvas id="tma-chart-lead-sources" height="180"></canvas>
					</div>
				</div>
				${activityHtml}
				${renderGBPSection(gbp)}
				${renderWebSection(web)}
				${renderInstagramSection(instagram)}
				${renderKpiTable(kpis)}
			`;

			renderDashboardCharts(history, leadSources, gbp, web, instagram);

			// Bind export button.
			var exportBtn = document.getElementById('tma-export-btn');
			if (exportBtn) {
				exportBtn.addEventListener('click', handleExport);
			}

			var refreshNowBtn = document.getElementById('tma-refresh-now');
			if (refreshNowBtn) {
				refreshNowBtn.addEventListener('click', function () {
					renderDashboard(container);
				});
			}

			// Doc progress card click → navigate to documents
			var docProgressCard = document.getElementById('tma-dash-doc-progress');
			if (docProgressCard) {
				docProgressCard.addEventListener('click', function () {
					location.hash = '#documents';
				});
			}

			// Update sidebar badges from dashboard data
			updateSidebarBadge('leads', newLeadsCount);
			if (pendingDocs > 0) updateSidebarBadge('documents', pendingDocs);

			// Restore scroll position after auto-refresh re-render
			if (savedScroll > 0) {
				requestAnimationFrame(function () { scrollParent.scrollTop = savedScroll; });
			}

			startDashboardAutoRefresh(container);
		} catch (err) {
			clearDashboardAutoRefresh();
			destroyCharts();
			showError(container, t('error.loading_dashboard') + ': ' + getErrorMessage(err, 'Recurso bloqueado o no disponible.'));
		}
	}

	function renderGBPSection(gbp) {
		return `
			<div class="section-card">
				<h2 class="card__title">Google Business Profile</h2>
				<div class="kpi-grid mt-3">
					<div class="kpi-card"><span class="kpi-card__label">Rating</span><span class="kpi-card__value">${escapeHtml(String(gbp.rating || 0))}</span></div>
					<div class="kpi-card"><span class="kpi-card__label">Reviews</span><span class="kpi-card__value">${escapeHtml(String(gbp.reviews || 0))}</span></div>
					<div class="kpi-card"><span class="kpi-card__label">Impressions</span><span class="kpi-card__value">${escapeHtml(String(gbp.impressions || 0))}</span></div>
					<div class="kpi-card"><span class="kpi-card__label">Actions</span><span class="kpi-card__value">${escapeHtml(String(gbp.actions || 0))}</span></div>
				</div>
				<div class="mt-4">
					<canvas id="tma-chart-gbp-impressions-split" height="180"></canvas>
				</div>
			</div>
		`;
	}

	function renderWebSection(web) {
		var pages = Array.isArray(web.top_pages) ? web.top_pages : [];
		var maxSessions = pages.reduce(function (m, p) { return Math.max(m, Number(p.sessions || 0)); }, 1);
		var rows = pages.map(function (p) {
			var sessions = Number(p.sessions || 0);
			var width = Math.max(4, Math.round((sessions / maxSessions) * 100));
			return '<div class="stat-bar">'
				+ '<div class="stat-bar__header">'
				+ '<span>' + escapeHtml(String(p.path || '/')) + '</span>'
				+ '<strong>' + escapeHtml(String(sessions)) + '</strong>'
				+ '</div>'
				+ '<div class="stat-bar__track">'
				+ '<div class="stat-bar__fill" style="width:' + width + '%"></div>'
				+ '</div>'
				+ '</div>';
		}).join('');

		return `
			<div class="section-card">
				<h2 class="card__title">Web Analytics (GA4)</h2>
				<div class="kpi-grid mt-3">
					<div class="kpi-card"><span class="kpi-card__label">Sessions</span><span class="kpi-card__value">${escapeHtml(String(web.sessions || 0))}</span></div>
					<div class="kpi-card"><span class="kpi-card__label">Users</span><span class="kpi-card__value">${escapeHtml(String(web.users || 0))}</span></div>
					<div class="kpi-card"><span class="kpi-card__label">Conversion Rate</span><span class="kpi-card__value">${escapeHtml(String(web.conversion_rate || 0))}%</span></div>
					<div class="kpi-card"><span class="kpi-card__label">Forms Submitted</span><span class="kpi-card__value">${escapeHtml(String(web.forms_submitted || 0))}</span></div>
					<div class="kpi-card"><span class="kpi-card__label">Avg Time</span><span class="kpi-card__value">${escapeHtml(String(web.avg_time || 0))}s</span></div>
				</div>
				<div class="grid grid--2-equal mt-4">
					<div>
						<canvas id="tma-chart-web-sessions" height="180"></canvas>
					</div>
					<div>
						<h3 class="card__subtitle">Top Pages</h3>
						${rows || '<p class="text-muted">No data</p>'}
					</div>
				</div>
			</div>
		`;
	}

	function renderInstagramSection(instagram) {
		return `
			<div class="section-card">
				<h2 class="card__title">Instagram</h2>
				<div class="kpi-grid mt-3">
					<div class="kpi-card"><span class="kpi-card__label">Followers</span><span class="kpi-card__value">${escapeHtml(String(instagram.followers || 0))}</span></div>
					<div class="kpi-card"><span class="kpi-card__label">Reach</span><span class="kpi-card__value">${escapeHtml(String(instagram.reach || 0))}</span></div>
					<div class="kpi-card"><span class="kpi-card__label">Engagement Rate</span><span class="kpi-card__value">${escapeHtml(String(instagram.engagement || 0))}%</span></div>
				</div>
				<div class="mt-4">
					<canvas id="tma-chart-instagram-reach" height="120"></canvas>
				</div>
			</div>
		`;
	}

	function renderTrend(kpi) {
		if (!kpi) return '→ neutral';
		const trend = kpi.trend || 'neutral';
		if (trend === 'up') return '↑ up';
		if (trend === 'down') return '↓ down';
		return '→ neutral';
	}

	function ensureChartJs() {
		return Promise.resolve();
	}

	function renderDashboardCharts(history, leadSources, gbp, web, instagram) {
		if (!window.Chart) return;
		destroyCharts();

		const gold = '#B8860B';
		const dark = '#1A1A1A';
		const impressions = history.impressions || [];
		const impLabels = impressions.map(function (x) { return x.period; });
		const impValues = impressions.map(function (x) { return Number(x.value || 0); });

		const impCanvas = document.getElementById('tma-chart-impressions');
		if (impCanvas) {
			activeCharts.push(new window.Chart(impCanvas, {
				type: 'line',
				data: {
					labels: impLabels,
					datasets: [{
						label: 'Impressions',
						data: impValues,
						borderColor: gold,
						backgroundColor: 'rgba(184,134,11,0.15)',
						tension: 0.3,
						fill: true,
					}],
				},
				options: { responsive: true, maintainAspectRatio: false },
			}));
		}

		const leadCanvas = document.getElementById('tma-chart-lead-sources');
		if (leadCanvas) {
			activeCharts.push(new window.Chart(leadCanvas, {
				type: 'doughnut',
				data: {
					labels: leadSources.map(function (x) { return x.label; }),
					datasets: [{
						data: leadSources.map(function (x) { return Number(x.value || 0); }),
						backgroundColor: [gold, dark, '#6b7280', '#c7a24d', '#9ca3af'],
					}],
				},
				options: { responsive: true, maintainAspectRatio: false },
			}));
		}

		const split = (gbp && gbp.impressions_split) ? gbp.impressions_split : [];
		const splitCanvas = document.getElementById('tma-chart-gbp-impressions-split');
		if (splitCanvas && split.length) {
			activeCharts.push(new window.Chart(splitCanvas, {
				type: 'bar',
				data: {
					labels: split.map(function (x) { return x.period; }),
					datasets: [
						{ label: 'Search', data: split.map(function (x) { return x.impressions_search; }), backgroundColor: '#B8860B', stack: 'impressions' },
						{ label: 'Maps', data: split.map(function (x) { return x.impressions_maps; }), backgroundColor: '#1A1A1A', stack: 'impressions' },
					],
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					scales: { x: { stacked: true }, y: { stacked: true } },
				},
			}));
		}

		const webHistory = (web && web.sessions_history) ? web.sessions_history : [];
		const webCanvas = document.getElementById('tma-chart-web-sessions');
		if (webCanvas && webHistory.length) {
			activeCharts.push(new window.Chart(webCanvas, {
				type: 'line',
				data: {
					labels: webHistory.map(function (x) { return x.period; }),
					datasets: [{
						label: 'Sessions',
						data: webHistory.map(function (x) { return Number(x.value || 0); }),
						borderColor: '#B8860B',
						backgroundColor: 'rgba(184,134,11,0.2)',
						tension: 0.3,
						fill: true,
					}],
				},
				options: { responsive: true, maintainAspectRatio: false },
			}));
		}

		const igHistory = (instagram && instagram.reach_history) ? instagram.reach_history : [];
		const igCanvas = document.getElementById('tma-chart-instagram-reach');
		if (igCanvas && igHistory.length) {
			activeCharts.push(new window.Chart(igCanvas, {
				type: 'line',
				data: {
					labels: igHistory.map(function (x) { return x.period; }),
					datasets: [{
						label: 'Reach',
						data: igHistory.map(function (x) { return Number(x.value || 0); }),
						borderColor: '#B8860B',
						backgroundColor: 'rgba(184,134,11,0.15)',
						fill: true,
						tension: 0.35,
					}],
				},
				options: {
					responsive: true,
					maintainAspectRatio: false,
					plugins: { legend: { display: false } },
					scales: { x: { display: false }, y: { display: false } },
				},
			}));
		}
	}

	async function handleExport() {
		var btn = document.getElementById('tma-export-btn');
		if (btn) btn.disabled = true;
		try {
			var data = await api('/export');
			var text = (data && typeof data.summary === 'string') ? data.summary : '';
			if (!text) {
				throw new Error('Export summary is empty');
			}
			if (navigator.clipboard && navigator.clipboard.writeText) {
				await navigator.clipboard.writeText(text);
			} else {
				var ta = document.createElement('textarea');
				ta.value = text;
				ta.style.position = 'fixed';
				ta.style.left = '-9999px';
				document.body.appendChild(ta);
				ta.select();
				document.execCommand('copy');
				document.body.removeChild(ta);
			}
			if (btn) {
				btn.textContent = '✅ ' + (t('dashboard.exported') || 'Copiado');
				setTimeout(function () {
					btn.textContent = '📋 ' + (t('dashboard.export') || 'Exportar resumen');
					btn.disabled = false;
				}, 2000);
			}
		} catch (err) {
			if (btn) { btn.disabled = false; }
			showToast((t('error.export_failed') || 'Error al exportar') + ': ' + err.message, 'error');
		}
	}

	function renderKpiTable(kpis) {
		const entries = Object.entries(kpis);
		if (!entries.length) return '<div class="card"><p class="text-muted">' + escapeHtml(t('dashboard.no_kpis')) + '</p></div>';

		let rows = '';
		entries.forEach(function (entry) {
			const metric = entry[0];
			const val = entry[1];
			rows += '<tr><td>' + escapeHtml(metric) + '</td><td>' + escapeHtml(String(val.latest || '—')) + '</td><td>' + escapeHtml(String(val.previous || '—')) + '</td></tr>';
		});

		return `
			<div class="section-card">
				<h2 class="card__title">${escapeHtml(t('dashboard.kpis'))}</h2>
				<div class="table-wrap">
					<table class="table">
						<thead><tr><th>${escapeHtml(t('dashboard.metric'))}</th><th>${escapeHtml(t('dashboard.latest'))}</th><th>${escapeHtml(t('dashboard.previous'))}</th></tr></thead>
						<tbody>${rows}</tbody>
					</table>
				</div>
			</div>
		`;
	}

	/* ═══════════════════════════════════════════════════════════════
	   Section: Documents
	   ═══════════════════════════════════════════════════════════════ */
	let docsState = [];
	let currentDocIndex = -1;

	async function renderDocuments(container) {
		try {
			docsState = await api('/documents');
			if (!docsState.length) {
				container.innerHTML = '<div class="empty-state"><div class="empty-state__icon">📄</div><p class="empty-state__text">' + escapeHtml(t('documents.no_docs')) + '</p></div>';
				return;
			}

			function docStatusIcon(status) {
				if (status === 'approved') return '✅';
				if (status === 'changes_requested') return '📝';
				return '⏳';
			}

			let cards = '';
			docsState.forEach(function (doc, idx) {
				const statusClass = doc.status === 'approved' ? 'badge--success' : (doc.status === 'pending' ? 'badge--warning' : 'badge--info');
				const code = doc.slug || '';
				cards += '<article class="doc-card doc-card--grid">'
					+ '<div class="doc-card__row">'
					+ '<div class="flex-1">'
					+ '<p class="doc-card__code">' + escapeHtml(code) + '</p>'
					+ '<h3 class="doc-card__title">' + escapeHtml(doc.title || '') + '</h3>'
					+ '<p class="doc-card__date">Actualizado: ' + formatDate(doc.updated_at) + '</p>'
					+ (doc.status === 'changes_requested' && doc.notes
						? '<p class="doc-card__change-note">💬 ' + escapeHtml(doc.notes) + '</p>'
						: '')
					+ '</div>'
					+ '<div class="doc-card__actions">'
					+ '<span class="doc-card__status-icon">' + docStatusIcon(doc.status) + '</span>'
					+ '<span class="badge ' + statusClass + '">' + escapeHtml(doc.status || '') + '</span>'
					+ '<button class="btn btn--primary btn-view-doc" data-doc-code="' + escapeHtml(code) + '" data-doc-index="' + idx + '">Ver</button>'
					+ '</div>'
					+ '</div>'
					+ '</article>';
			});

			const approvedCount = docsState.filter(function (d) { return d.status === 'approved'; }).length;
			const total = docsState.length;
			const percent = total > 0 ? Math.round((approvedCount / total) * 100) : 0;

			container.innerHTML = `
				<div class="card">
					<h2 class="card__title">${escapeHtml(t('documents.title'))}</h2>
					<div class="doc-progress">
						<div class="progress-bar__header">
							<span>Progreso aprobación</span>
							<strong>${approvedCount}/${total} (${percent}%)</strong>
						</div>
						<div class="progress-bar">
							<div class="progress-bar__fill" style="width:${percent}%"></div>
						</div>
					</div>
					<div class="doc-grid">${cards}</div>
				</div>
				<div id="tma-doc-viewer-modal" class="modal-overlay">
					<div class="modal">
						<div class="modal__header">
							<strong id="tma-doc-viewer-title">Documento</strong>
							<div class="modal__header-actions">
								<button class="btn" id="tma-doc-prev">Anterior</button>
								<button class="btn" id="tma-doc-next">Siguiente</button>
								<button class="btn" id="tma-doc-viewer-close">Cerrar</button>
							</div>
						</div>
						<div id="tma-doc-viewer-host" class="modal__body"></div>
						<div class="modal__toolbar">
							<button class="btn btn--success" id="tma-doc-approve">Aprobado</button>
							<button class="btn btn--warning" id="tma-doc-changes">Con cambios</button>
							<button class="btn btn--primary" id="tma-doc-add-note">Dejar nota</button>
							<textarea id="tma-doc-change-notes" class="input textarea hidden" rows="2" placeholder="Describe cambios (mínimo 10 caracteres)"></textarea>
							<button class="btn btn--accent hidden" id="tma-doc-save-changes">Guardar cambios</button>
							<div id="tma-doc-note-form" class="doc-note-form">
								<textarea id="tma-doc-note-text" class="input textarea" rows="2" placeholder="Escribe una nota sobre este documento..."></textarea>
								<button class="btn btn--primary" id="tma-doc-save-note">Guardar</button>
								<button class="btn" id="tma-doc-cancel-note">Cancelar</button>
							</div>
						</div>
					</div>
				</div>
			`;

			container.querySelectorAll('.btn-view-doc').forEach(function (btn) {
				btn.addEventListener('click', function () {
					currentDocIndex = parseInt(btn.dataset.docIndex || '-1', 10);
					openDocumentViewer(btn.dataset.docCode || '', btn.closest('article') ? btn.closest('article').querySelector('h3').textContent : 'Documento');
				});
			});

			const closeBtn = document.getElementById('tma-doc-viewer-close');
			if (closeBtn) closeBtn.addEventListener('click', closeDocumentViewer);
			const prevBtn = document.getElementById('tma-doc-prev');
			if (prevBtn) prevBtn.addEventListener('click', function () { navigateDoc(-1); });
			const nextBtn = document.getElementById('tma-doc-next');
			if (nextBtn) nextBtn.addEventListener('click', function () { navigateDoc(1); });

			const approveBtn = document.getElementById('tma-doc-approve');
			if (approveBtn) approveBtn.addEventListener('click', function () { saveDocStatus('approved', ''); });
			const changesBtn = document.getElementById('tma-doc-changes');
			if (changesBtn) {
				changesBtn.addEventListener('click', function () {
					document.getElementById('tma-doc-change-notes').classList.remove('hidden');
					document.getElementById('tma-doc-save-changes').classList.remove('hidden');
				});
			}
			const saveChangesBtn = document.getElementById('tma-doc-save-changes');
			if (saveChangesBtn) {
				saveChangesBtn.addEventListener('click', function () {
					const notes = document.getElementById('tma-doc-change-notes').value || '';
					if (notes.trim().length < 10) {
						showToast('La nota debe tener al menos 10 caracteres.', 'error');
						return;
					}
					saveDocStatus('changes_requested', notes.trim());
				});
			}

			const addNoteBtn = document.getElementById('tma-doc-add-note');
			if (addNoteBtn) {
				addNoteBtn.addEventListener('click', function () {
					var noteForm = document.getElementById('tma-doc-note-form');
					if (noteForm) noteForm.classList.toggle('open');
				});
			}
			const saveNoteBtn = document.getElementById('tma-doc-save-note');
			if (saveNoteBtn) {
				saveNoteBtn.addEventListener('click', async function () {
					var noteTextEl = document.getElementById('tma-doc-note-text');
					var content = noteTextEl ? noteTextEl.value.trim() : '';
					if (!content) { showToast('La nota no puede estar vacía.', 'error'); return; }
					try {
						await addDocumentNote(content);
						if (noteTextEl) noteTextEl.value = '';
						var noteForm = document.getElementById('tma-doc-note-form');
						if (noteForm) noteForm.classList.remove('open');
					} catch (err) {
						showToast('Error al guardar nota: ' + getErrorMessage(err), 'error');
					}
				});
			}
			const cancelNoteBtn = document.getElementById('tma-doc-cancel-note');
			if (cancelNoteBtn) {
				cancelNoteBtn.addEventListener('click', function () {
					var noteForm = document.getElementById('tma-doc-note-form');
					if (noteForm) noteForm.classList.remove('open');
				});
			}
		} catch (err) {
			showError(container, t('error.loading_documents') + ': ' + getErrorMessage(err));
		}
	}

	function navigateDoc(direction) {
		if (!docsState.length || currentDocIndex < 0) return;
		const nextIndex = currentDocIndex + direction;
		if (nextIndex < 0 || nextIndex >= docsState.length) return;
		currentDocIndex = nextIndex;
		const doc = docsState[currentDocIndex];
		openDocumentViewer(doc.slug || '', doc.title || 'Documento');
		updateDocNavButtons();
	}

	function updateDocNavButtons() {
		var prevBtn = document.getElementById('tma-doc-prev');
		var nextBtn = document.getElementById('tma-doc-next');
		if (prevBtn) {
			prevBtn.disabled = (currentDocIndex <= 0);
			var prevTitle = currentDocIndex > 0 ? docsState[currentDocIndex - 1].title : '';
			prevBtn.textContent = prevTitle ? '← ' + prevTitle : '← Anterior';
		}
		if (nextBtn) {
			nextBtn.disabled = (currentDocIndex >= docsState.length - 1);
			var nextTitle = currentDocIndex < docsState.length - 1 ? docsState[currentDocIndex + 1].title : '';
			nextBtn.textContent = nextTitle ? nextTitle + ' →' : 'Siguiente →';
		}
	}

	async function saveDocStatus(status, notes) {
		if (currentDocIndex < 0 || !docsState[currentDocIndex]) return;
		const doc = docsState[currentDocIndex];
		if (status === 'approved') {
			const ok = window.confirm('Confirmar aprobación de "' + (doc.title || 'documento') + '"?');
			if (!ok) return;
		}
		try {
			await api('/documents/' + doc.id + '/status', {
				method: 'POST',
				body: JSON.stringify({ status: status, notes: notes || '' })
			});
			docsState[currentDocIndex].status = status;
			// Update badge in the list card without destroying the modal
			var viewBtn = document.querySelector('.btn-view-doc[data-doc-index="' + currentDocIndex + '"]');
			if (viewBtn) {
				var badge = viewBtn.closest('article') && viewBtn.closest('article').querySelector('.badge');
				if (badge) {
					badge.className = 'badge ' + (status === 'approved' ? 'badge--success' : (status === 'pending' ? 'badge--warning' : 'badge--info'));
					badge.textContent = status;
				}
			}
			updateViewerActionButtons(status);
			// Reset change notes controls
			var notesEl = document.getElementById('tma-doc-change-notes');
			var saveBtn = document.getElementById('tma-doc-save-changes');
			if (notesEl) { notesEl.value = ''; notesEl.classList.add('hidden'); }
			if (saveBtn) { saveBtn.classList.add('hidden'); }
		} catch (err) {
			showToast('Error al guardar estado: ' + getErrorMessage(err), 'error');
		}
	}

	function updateViewerActionButtons(status) {
		var approveBtn = document.getElementById('tma-doc-approve');
		var changesBtn = document.getElementById('tma-doc-changes');
		if (approveBtn) {
			approveBtn.disabled = (status === 'approved');
			approveBtn.textContent = (status === 'approved') ? '✅ Aprobado' : 'Aprobado';
		}
		if (changesBtn) {
			changesBtn.disabled = (status === 'changes_requested');
			changesBtn.textContent = (status === 'changes_requested') ? '📝 Con cambios' : 'Con cambios';
		}
	}

	async function addDocumentNote(content) {
		if (currentDocIndex < 0 || !docsState[currentDocIndex]) return;
		const doc = docsState[currentDocIndex];
		await api('/notes', {
			method: 'POST',
			body: JSON.stringify({
				title: 'Nota sobre ' + (doc.slug || 'documento'),
				content: content.trim(),
				visibility: 'client',
				module: 'documents',
				item_id: doc.id,
			}),
		});
		showToast('Nota guardada.', 'success');
	}

	async function openDocumentViewer(code, title) {
		const modal = document.getElementById('tma-doc-viewer-modal');
		const host = document.getElementById('tma-doc-viewer-host');
		const titleEl = document.getElementById('tma-doc-viewer-title');
		if (!modal || !host) return;
		modal.classList.add('open');
		document.addEventListener('keydown', handleViewerKeydown);
		if (titleEl) titleEl.textContent = title || 'Documento';

		// Reflect current doc status in action buttons immediately
		if (currentDocIndex >= 0 && docsState[currentDocIndex]) {
			updateViewerActionButtons(docsState[currentDocIndex].status || 'pending');
		}

		// Prev/Next with document titles
		updateDocNavButtons();

		try {
			const doc = await api('/documents/' + encodeURIComponent(code) + '/content');
			host.innerHTML = '';
			const root = host.shadowRoot ? host.shadowRoot : (host.attachShadow ? host.attachShadow({ mode: 'open' }) : host);
			const now = new Date().toLocaleString('es-ES');
			const userName = (window.TMA_PANEL && window.TMA_PANEL.user && window.TMA_PANEL.user.name) ? window.TMA_PANEL.user.name : 'Usuario';
			root.innerHTML = `
				<style>
					.viewer-wrap{position:relative;min-height:100%;padding:28px;background:#fff;user-select:none;-webkit-user-select:none;}
					.viewer-prose{max-width:900px;margin:0 auto;color:#1a1a1a;font:16px/1.65 'DM Sans',sans-serif;}
					.viewer-watermark{position:fixed;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none;opacity:.09;font-size:42px;transform:rotate(-25deg);color:#B8860B;font-family:'Cormorant Garamond',serif;white-space:pre;}
					.viewer-prose img{max-width:100%;height:auto;}
					.viewer-prose table{width:100%;border-collapse:collapse;}
					.viewer-prose td,.viewer-prose th{border:1px solid #ddd;padding:8px;}
					.viewer-notes{max-width:900px;margin:32px auto 0;padding:20px;background:#fafaf5;border:1px solid #e5e5e0;border-radius:8px;}
					.viewer-notes h4{margin:0 0 12px;font:600 15px/1.3 'DM Sans',sans-serif;color:#1a1a1a;}
					.viewer-note{padding:10px 0;border-bottom:1px solid #e5e5e0;}
					.viewer-note:last-child{border-bottom:none;}
					.viewer-note-meta{font-size:12px;color:#6b7280;margin-bottom:4px;}
					.viewer-note-text{font-size:14px;color:#1a1a1a;line-height:1.5;}
					.viewer-notes-empty{font-size:13px;color:#9ca3af;font-style:italic;}
				</style>
				<div class="viewer-wrap">
					<div class="viewer-watermark">${escapeHtml(userName)} • ${escapeHtml(now)}</div>
					<div class="viewer-prose">${doc.html || ''}</div>
					<div class="viewer-notes" id="viewer-notes-section">
						<h4>📝 Notas</h4>
						<p class="viewer-notes-empty">Cargando notas...</p>
					</div>
				</div>
			`;
			// Load notes for this document
			loadViewerNotes(root, currentDocIndex >= 0 ? docsState[currentDocIndex] : null);
		} catch (err) {
			host.innerHTML = '<div class="modal__error">No se pudo cargar el documento: ' + escapeHtml(getErrorMessage(err, 'No existe en caché o la API no lo encontró.')) + '</div>';
		}
	}

	function closeDocumentViewer() {
		const modal = document.getElementById('tma-doc-viewer-modal');
		const host = document.getElementById('tma-doc-viewer-host');
		if (modal) modal.classList.remove('open');
		if (host) host.innerHTML = '';
		currentDocIndex = -1;
		document.removeEventListener('keydown', handleViewerKeydown);
	}

	async function loadViewerNotes(root, doc) {
		var section = root.getElementById ? root.getElementById('viewer-notes-section') : root.querySelector('#viewer-notes-section');
		if (!section || !doc) {
			if (section) section.innerHTML = '<h4>📝 Notas</h4><p class="viewer-notes-empty">Sin notas.</p>';
			return;
		}
		try {
			var allNotes = await api('/notes');
			var docNotes = allNotes.filter(function (n) {
				return n.module === 'documents' && n.item_id === doc.id;
			});
			if (!docNotes.length) {
				section.innerHTML = '<h4>📝 Notas</h4><p class="viewer-notes-empty">Sin notas para este documento.</p>';
				return;
			}
			var html = '<h4>📝 Notas (' + docNotes.length + ')</h4>';
			docNotes.forEach(function (n) {
				html += '<div class="viewer-note">'
					+ '<div class="viewer-note-meta">' + escapeHtml(formatDate(n.created_at)) + '</div>'
					+ '<div class="viewer-note-text">' + escapeHtml(n.content) + '</div>'
					+ '</div>';
			});
			section.innerHTML = html;
		} catch (e) {
			section.innerHTML = '<h4>📝 Notas</h4><p class="viewer-notes-empty">No se pudieron cargar las notas.</p>';
		}
	}

	function handleViewerKeydown(e) {
		const modal = document.getElementById('tma-doc-viewer-modal');
		if (!modal || !modal.classList.contains('open')) return;
		if (e.key === 'Escape') { closeDocumentViewer(); }
		else if (e.key === 'ArrowLeft') { navigateDoc(-1); }
		else if (e.key === 'ArrowRight') { navigateDoc(1); }
	}

	/* ═══════════════════════════════════════════════════════════════
	   Section: Leads
	   ═══════════════════════════════════════════════════════════════ */

	async function renderLeads(container) {
		try {
			const leads = await api('/leads');
			if (!leads.length) {
				container.innerHTML = '<div class="empty-state"><div class="empty-state__icon">📥</div><p class="empty-state__text">' + escapeHtml(t('leads.no_leads')) + '</p></div>';
				updateSidebarBadge('leads', 0);
				return;
			}

			// Compute KPI summary
			var totalLeads = leads.length;
			var pipelineValue = 0;
			var newLeads = 0;
			var sources = {};
			var statuses = {};
			leads.forEach(function (lead) {
				pipelineValue += parseFloat(lead.lead_value) || 0;
				if (lead.status === 'new') newLeads++;
				var src = lead.source || 'unknown';
				sources[src] = (sources[src] || 0) + 1;
				statuses[lead.status || 'new'] = (statuses[lead.status || 'new'] || 0) + 1;
			});

			// Update sidebar badge
			updateSidebarBadge('leads', newLeads);

			// Build source filter options
			var sourceOptions = '<option value="">' + escapeHtml(t('leads.filter_all')) + '</option>';
			Object.keys(sources).sort().forEach(function (src) {
				sourceOptions += '<option value="' + escapeHtml(src) + '">' + escapeHtml(src) + ' (' + sources[src] + ')</option>';
			});

			// Build status filter options
			var statusOptions = '<option value="">' + escapeHtml(t('leads.filter_all')) + '</option>';
			['new', 'contacted', 'quoted', 'won', 'lost'].forEach(function (s) {
				if (statuses[s]) {
					statusOptions += '<option value="' + s + '">' + escapeHtml(s) + ' (' + statuses[s] + ')</option>';
				}
			});

			function buildLeadRows(filterSource, filterStatus) {
				var rows = '';
				leads.forEach(function (lead) {
					if (filterSource && (lead.source || 'unknown') !== filterSource) return;
					if (filterStatus && lead.status !== filterStatus) return;
					var leadId = Number(lead.id || 0);
					var currentValue = parseFloat(lead.lead_value) || 0;
					var selectOpts = ['new', 'contacted', 'quoted', 'won', 'lost'].map(function (s) {
						return '<option value="' + s + '"' + (s === lead.status ? ' selected' : '') + '>' + escapeHtml(s) + '</option>';
					}).join('');

					rows += '<tr>'
						+ '<td>' + escapeHtml(lead.name || '') + '</td>'
						+ '<td>' + escapeHtml(lead.email || '') + '</td>'
						+ '<td>' + escapeHtml(lead.source || '') + '</td>'
						+ '<td><div class="lead-status lead-status--' + escapeHtml(lead.status || 'new') + '">'
						+ '<select class="lead-status-select input input--compact" data-lead-id="' + leadId + '" data-lead-value="' + currentValue + '">' + selectOpts + '</select>'
						+ '</div></td>'
						+ '<td>$' + currentValue.toLocaleString() + '</td>'
						+ '<td>' + formatDate(lead.created_at) + '</td>'
						+ '<td><button class="btn btn--small btn--ghost js-view-history" data-lead-id="' + leadId + '">Ver historial</button></td>'
						+ '</tr>';
				});
				return rows;
			}

			container.innerHTML = `
				<div class="lead-kpi-bar">
					<div class="lead-kpi">
						<div class="lead-kpi__value">${totalLeads}</div>
						<div class="lead-kpi__label">${escapeHtml(t('leads.total'))}</div>
					</div>
					<div class="lead-kpi lead-kpi--accent">
						<div class="lead-kpi__value">$${pipelineValue.toLocaleString()}</div>
						<div class="lead-kpi__label">${escapeHtml(t('leads.pipeline_value'))}</div>
					</div>
					<div class="lead-kpi">
						<div class="lead-kpi__value">${newLeads}</div>
						<div class="lead-kpi__label">${escapeHtml(t('leads.new_leads'))}</div>
					</div>
				</div>
				<div class="lead-filters">
					<span class="lead-filters__label">${escapeHtml(t('common.filter'))}:</span>
					<select id="tma-lead-filter-source" class="input input--select input--compact">
						${sourceOptions}
					</select>
					<select id="tma-lead-filter-status" class="input input--select input--compact">
						${statusOptions}
					</select>
				</div>
				<div class="card">
					<h2 class="card__title">${escapeHtml(t('leads.title'))}</h2>
					<div class="table-wrap">
						<table class="table">
							<thead><tr><th>${escapeHtml(t('leads.name'))}</th><th>${escapeHtml(t('leads.email'))}</th><th>${escapeHtml(t('leads.source'))}</th><th>${escapeHtml(t('leads.stage'))}</th><th>${escapeHtml(t('leads.value'))}</th><th>${escapeHtml(t('leads.date'))}</th><th>Timeline</th></tr></thead>
							<tbody id="tma-leads-tbody">${buildLeadRows('', '')}</tbody>
						</table>
					</div>
				</div>
				<div class="section-card hidden" id="lead-history">
					<h3 class="card__title">Timeline de historial</h3>
					<div id="lead-history-timeline" class="timeline"></div>
				</div>
			`;

			// Filter handlers
			function applyFilters() {
				var src = document.getElementById('tma-lead-filter-source').value;
				var st = document.getElementById('tma-lead-filter-status').value;
				var tbody = document.getElementById('tma-leads-tbody');
				if (tbody) {
					tbody.innerHTML = buildLeadRows(src, st);
					bindLeadEvents();
				}
			}
			var srcFilter = document.getElementById('tma-lead-filter-source');
			var stFilter = document.getElementById('tma-lead-filter-status');
			if (srcFilter) srcFilter.addEventListener('change', applyFilters);
			if (stFilter) stFilter.addEventListener('change', applyFilters);

			function bindLeadEvents() {
				container.querySelectorAll('.js-view-history').forEach(function (btn) {
					btn.addEventListener('click', async function () {
						const leadId = Number(btn.getAttribute('data-lead-id') || 0);
						await renderLeadHistoryTimeline(container, leadId);
					});
				});
				container.querySelectorAll('.lead-status-select').forEach(function (sel) {
					var prevValue = sel.value;
					sel.addEventListener('change', async function () {
						var id = Number(sel.dataset.leadId || 0);
						var val = parseFloat(sel.dataset.leadValue || 0);
						sel.disabled = true;
						try {
							await api('/leads/' + id, {
								method: 'POST',
								body: JSON.stringify({ status: sel.value, lead_value: val })
							});
							prevValue = sel.value;
							sel.style.borderColor = 'var(--tma-success)';
							setTimeout(function () { sel.style.borderColor = ''; }, 1500);
						} catch (err) {
							showToast('Error actualizando lead: ' + getErrorMessage(err), 'error');
							sel.value = prevValue;
						} finally {
							sel.disabled = false;
						}
					});
				});
			}
			bindLeadEvents();
		} catch (err) {
			showError(container, t('error.loading_leads') + ': ' + getErrorMessage(err));
		}
	}

	async function renderLeadHistoryTimeline(container, leadId) {
		const card = container.querySelector('#lead-history');
		const timeline = container.querySelector('#lead-history-timeline');
		if (!card || !timeline) {
			return;
		}

		try {
			const items = await api('/leads/' + leadId + '/history');
			card.classList.remove('hidden');
			if (!Array.isArray(items) || !items.length) {
				timeline.innerHTML = '<p class="text-muted">Sin cambios registrados todavía.</p>';
				return;
			}

			let html = '';
			items.forEach(function (item) {
				html += '<div class="timeline__item">'
					+ '<div><strong>' + escapeHtml(item.action || 'Cambio') + '</strong></div>'
					+ '<div class="timeline__meta">'
					+ escapeHtml(formatDate(item.created_at))
					+ ' · ' + escapeHtml(item.user_name || ('user #' + Number(item.user_id || 0)))
					+ '</div>'
					+ '</div>';
			});

			timeline.innerHTML = html;
		} catch (err) {
			card.classList.remove('hidden');
			timeline.innerHTML = '<p class="text-danger">No se pudo cargar el historial.</p>';
		}
	}

	/* ═══════════════════════════════════════════════════════════════
	   Section: Notes
	   ═══════════════════════════════════════════════════════════════ */

	async function renderNotes(container) {
		try {
			const notes = await api('/notes');

			let notesList = '';
			if (notes.length) {
				notes.forEach(function (note) {
					const vis = note.visibility === 'internal' ? 'badge--warning' : 'badge--info';
					const visLabel = note.visibility === 'internal' ? t('notes.internal') : t('notes.shared');
					const moduleLabel = note.module ? String(note.module) : 'general';
					const itemLabel = parseInt(note.item_id || 0, 10) > 0 ? ('#' + parseInt(note.item_id || 0, 10)) : '—';
					notesList += `
						<div class="note-item">
							<div class="note-item__header">
								<strong>${escapeHtml(note.title || t('notes.no_title'))}</strong>
								<span class="badge ${vis}">${escapeHtml(visLabel)}</span>
								<span class="badge badge--info">${escapeHtml(moduleLabel)} ${escapeHtml(itemLabel)}</span>
								<span class="note-item__date">${formatDate(note.created_at)}</span>
							</div>
							<div class="note-item__body">${escapeHtml(note.content || '')}</div>
						</div>
					`;
				});
			} else {
				notesList = '<p class="text-muted">' + escapeHtml(t('notes.no_notes')) + '</p>';
			}

			container.innerHTML = `
				<div class="card">
					<h2 class="card__title">${escapeHtml(t('notes.title'))}</h2>
					<form id="note-form" class="note-form">
						<input type="text" name="title" class="input" placeholder="${escapeHtml(t('notes.note_title'))}" required>
						<textarea name="content" class="input textarea" placeholder="${escapeHtml(t('notes.content'))}" rows="3" required></textarea>
						<div class="note-form__actions">
							<select name="module" class="input input--select">
								<option value="general">general</option>
								<option value="documents">documents</option>
								<option value="leads">leads</option>
								<option value="dashboard">dashboard</option>
							</select>
							<input type="number" name="item_id" class="input" min="0" placeholder="item_id">
							<select name="visibility" class="input input--select">
							<option value="client">${escapeHtml(t('notes.shared'))}</option>
								${user.isAdmin ? '<option value="internal">' + escapeHtml(t('notes.internal')) + '</option>' : ''}
							</select>
							<button type="submit" class="btn btn--primary">${escapeHtml(t('notes.add'))}</button>
						</div>
					</form>
				</div>
				<div class="section-card">
					${notesList}
				</div>
			`;

			// Note form handler
			var form = document.getElementById('note-form');
			if (form) {
				form.addEventListener('submit', async function (e) {
					e.preventDefault();
					var fd = new FormData(form);
					try {
						await api('/notes', {
							method: 'POST',
							body: JSON.stringify({
								title: fd.get('title'),
								content: fd.get('content'),
								visibility: fd.get('visibility'),
								module: fd.get('module') || 'general',
								item_id: parseInt(fd.get('item_id') || '0', 10) || 0,
							}),
						});
						renderNotes(container);
					} catch (err) {
						showToast(t('common.error') + ': ' + err.message, 'error');
					}
				});
			}
		} catch (err) {
			showError(container, t('error.loading_notes') + ': ' + getErrorMessage(err));
		}
	}

	/* ═══════════════════════════════════════════════════════════════
	   Section: Audit Log
	   ═══════════════════════════════════════════════════════════════ */

	async function renderAudit(container) {
		if (!user.isAdmin) {
			showError(container, t('audit.restricted'));
			return;
		}

		try {
			const entries = await api('/audit');
			if (!entries.length) {
				container.innerHTML = '<div class="empty-state"><div class="empty-state__icon">📝</div><p class="empty-state__text">' + escapeHtml(t('audit.no_entries')) + '</p></div>';
				return;
			}

			let rows = '';
			entries.forEach(function (entry) {
				rows += '<tr>'
					+ '<td>' + formatDate(entry.created_at) + '</td>'
					+ '<td>' + escapeHtml(entry.action || '') + '</td>'
					+ '<td>' + escapeHtml(entry.entity_type || '') + '</td>'
					+ '<td>' + (parseInt(entry.entity_id) || '') + '</td>'
					+ '<td>' + escapeHtml(entry.user_name || String(entry.user_id || '')) + '</td>'
					+ '</tr>';
			});

			container.innerHTML = `
				<div class="card">
					<h2 class="card__title">${escapeHtml(t('audit.title'))}</h2>
					<div class="table-wrap">
						<table class="table">
							<thead><tr><th>${escapeHtml(t('audit.date'))}</th><th>${escapeHtml(t('audit.action'))}</th><th>${escapeHtml(t('audit.entity'))}</th><th>${escapeHtml(t('audit.id'))}</th><th>${escapeHtml(t('audit.user'))}</th></tr></thead>
							<tbody>${rows}</tbody>
						</table>
					</div>
				</div>
			`;
		} catch (err) {
			showError(container, t('error.loading_audit') + ': ' + getErrorMessage(err));
		}
	}

	/* ═══════════════════════════════════════════════════════════════
	   Sidebar — Mobile Toggle
	   ═══════════════════════════════════════════════════════════════ */

	function initSidebar() {
		const hamburger = document.getElementById('tma-hamburger');
		const sidebar = document.getElementById('tma-sidebar');
		const overlay = document.getElementById('tma-sidebar-overlay');

		if (!hamburger || !sidebar || !overlay) return;

		function toggle() {
			const isOpen = sidebar.classList.toggle('open');
			overlay.classList.toggle('open', isOpen);
			hamburger.setAttribute('aria-expanded', isOpen);
		}

		function close() {
			sidebar.classList.remove('open');
			overlay.classList.remove('open');
			hamburger.setAttribute('aria-expanded', 'false');
		}

		hamburger.addEventListener('click', toggle);
		overlay.addEventListener('click', close);

		// Close sidebar when navigating on mobile.
		document.querySelectorAll('.nav-link').forEach(function (link) {
			link.addEventListener('click', function () {
				if (window.innerWidth < 768) close();
			});
		});
	}

	/* ═══════════════════════════════════════════════════════════════
	   Init
	   ═══════════════════════════════════════════════════════════════ */

	document.addEventListener('DOMContentLoaded', function () {
		if (window.TMA_i18n) window.TMA_i18n.init();
		initSidebar();
		navigate();
	});

	window.addEventListener('hashchange', navigate);
})();
