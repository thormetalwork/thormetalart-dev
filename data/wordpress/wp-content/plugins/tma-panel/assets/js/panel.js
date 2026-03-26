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
		container.innerHTML = `<div class="card"><p style="color:var(--tma-danger);">${escapeHtml(message)}</p></div>`;
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

			container.innerHTML = `
				<div class="dashboard-actions" style="display:flex;justify-content:flex-end;margin-bottom:var(--tma-sp-3);">
					${isDemo ? '<span class="badge badge--warning" style="margin-right:auto;">(Datos de ejemplo)</span>' : ''}
					<button class="btn btn--accent" id="tma-export-btn">
						📋 ${escapeHtml(t('dashboard.export') || 'Exportar resumen')}
					</button>
				</div>
				${attentionCount > 0 ? '<div class="card dashboard-alert high-value-alert" style="border-left:4px solid #B8860B;margin-bottom:var(--tma-sp-3);"><strong>' + attentionCount + ' lead(s) nuevos requieren atención</strong><div style="font-size:12px;color:var(--tma-muted);margin-top:6px;">Hay leads con valor estimado pendientes de seguimiento.</div></div>' : ''}
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
				<div class="grid grid--2" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:var(--tma-sp-4);margin-top:var(--tma-sp-4);">
					<div class="card">
						<h2 class="card__title">Impressions (6 meses)</h2>
						<canvas id="tma-chart-impressions" height="180"></canvas>
					</div>
					<div class="card">
						<h2 class="card__title">Leads por canal</h2>
						<canvas id="tma-chart-lead-sources" height="180"></canvas>
					</div>
				</div>
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
		} catch (err) {
			showError(container, t('error.loading_dashboard') + ': ' + getErrorMessage(err, 'Recurso bloqueado o no disponible.'));
		}
	}

	function renderGBPSection(gbp) {
		return `
			<div class="card" style="margin-top:var(--tma-sp-4);">
				<h2 class="card__title">Google Business Profile</h2>
				<div class="kpi-grid" style="margin-top:var(--tma-sp-3);">
					<div class="kpi-card"><span class="kpi-card__label">Rating</span><span class="kpi-card__value">${escapeHtml(String(gbp.rating || 0))}</span></div>
					<div class="kpi-card"><span class="kpi-card__label">Reviews</span><span class="kpi-card__value">${escapeHtml(String(gbp.reviews || 0))}</span></div>
					<div class="kpi-card"><span class="kpi-card__label">Impressions</span><span class="kpi-card__value">${escapeHtml(String(gbp.impressions || 0))}</span></div>
					<div class="kpi-card"><span class="kpi-card__label">Actions</span><span class="kpi-card__value">${escapeHtml(String(gbp.actions || 0))}</span></div>
				</div>
				<div style="margin-top:var(--tma-sp-4);">
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
			return '<div style="margin-bottom:8px;">'
				+ '<div style="display:flex;justify-content:space-between;font-size:12px;">'
				+ '<span>' + escapeHtml(String(p.path || '/')) + '</span>'
				+ '<strong>' + escapeHtml(String(sessions)) + '</strong>'
				+ '</div>'
				+ '<div style="height:8px;background:#e5e7eb;border-radius:999px;overflow:hidden;">'
				+ '<div style="height:100%;width:' + width + '%;background:#B8860B;"></div>'
				+ '</div>'
				+ '</div>';
		}).join('');

		return `
			<div class="card" style="margin-top:var(--tma-sp-4);">
				<h2 class="card__title">Web Analytics (GA4)</h2>
				<div class="kpi-grid" style="margin-top:var(--tma-sp-3);">
					<div class="kpi-card"><span class="kpi-card__label">Sessions</span><span class="kpi-card__value">${escapeHtml(String(web.sessions || 0))}</span></div>
					<div class="kpi-card"><span class="kpi-card__label">Users</span><span class="kpi-card__value">${escapeHtml(String(web.users || 0))}</span></div>
					<div class="kpi-card"><span class="kpi-card__label">Conversion Rate</span><span class="kpi-card__value">${escapeHtml(String(web.conversion_rate || 0))}%</span></div>
					<div class="kpi-card"><span class="kpi-card__label">Forms Submitted</span><span class="kpi-card__value">${escapeHtml(String(web.forms_submitted || 0))}</span></div>
					<div class="kpi-card"><span class="kpi-card__label">Avg Time</span><span class="kpi-card__value">${escapeHtml(String(web.avg_time || 0))}s</span></div>
				</div>
				<div class="grid grid--2" style="display:grid;grid-template-columns:1fr 1fr;gap:var(--tma-sp-4);margin-top:var(--tma-sp-4);">
					<div>
						<canvas id="tma-chart-web-sessions" height="180"></canvas>
					</div>
					<div>
						<h3 style="margin:0 0 12px 0;font-size:14px;">Top Pages</h3>
						${rows || '<p style="color:var(--tma-muted);">No data</p>'}
					</div>
				</div>
			</div>
		`;
	}

	function renderInstagramSection(instagram) {
		return `
			<div class="card" style="margin-top:var(--tma-sp-4);">
				<h2 class="card__title">Instagram</h2>
				<div class="kpi-grid" style="margin-top:var(--tma-sp-3);">
					<div class="kpi-card"><span class="kpi-card__label">Followers</span><span class="kpi-card__value">${escapeHtml(String(instagram.followers || 0))}</span></div>
					<div class="kpi-card"><span class="kpi-card__label">Reach</span><span class="kpi-card__value">${escapeHtml(String(instagram.reach || 0))}</span></div>
					<div class="kpi-card"><span class="kpi-card__label">Engagement Rate</span><span class="kpi-card__value">${escapeHtml(String(instagram.engagement || 0))}%</span></div>
				</div>
				<div style="margin-top:var(--tma-sp-4);">
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

	async function ensureChartJs() {
		if (window.Chart) return;
		await new Promise(function (resolve, reject) {
			const s = document.createElement('script');
			s.src = 'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js';
			s.onload = resolve;
			s.onerror = reject;
			document.head.appendChild(s);
		});
	}

	function renderDashboardCharts(history, leadSources, gbp, web, instagram) {
		if (!window.Chart) return;

		const gold = '#B8860B';
		const dark = '#1A1A1A';
		const impressions = history.impressions || [];
		const impLabels = impressions.map(function (x) { return x.period; });
		const impValues = impressions.map(function (x) { return Number(x.value || 0); });

		const impCanvas = document.getElementById('tma-chart-impressions');
		if (impCanvas) {
			new window.Chart(impCanvas, {
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
			});
		}

		const leadCanvas = document.getElementById('tma-chart-lead-sources');
		if (leadCanvas) {
			new window.Chart(leadCanvas, {
				type: 'doughnut',
				data: {
					labels: leadSources.map(function (x) { return x.label; }),
					datasets: [{
						data: leadSources.map(function (x) { return Number(x.value || 0); }),
						backgroundColor: [gold, dark, '#6b7280', '#c7a24d', '#9ca3af'],
					}],
				},
				options: { responsive: true, maintainAspectRatio: false },
			});
		}

		const split = (gbp && gbp.impressions_split) ? gbp.impressions_split : [];
		const splitCanvas = document.getElementById('tma-chart-gbp-impressions-split');
		if (splitCanvas && split.length) {
			new window.Chart(splitCanvas, {
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
			});
		}

		const webHistory = (web && web.sessions_history) ? web.sessions_history : [];
		const webCanvas = document.getElementById('tma-chart-web-sessions');
		if (webCanvas && webHistory.length) {
			new window.Chart(webCanvas, {
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
			});
		}

		const igHistory = (instagram && instagram.reach_history) ? instagram.reach_history : [];
		const igCanvas = document.getElementById('tma-chart-instagram-reach');
		if (igCanvas && igHistory.length) {
			new window.Chart(igCanvas, {
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
			});
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
			alert((t('error.export_failed') || 'Error al exportar') + ': ' + err.message);
		}
	}

	function renderKpiTable(kpis) {
		const entries = Object.entries(kpis);
		if (!entries.length) return '<div class="card"><p style="color:var(--tma-muted);">' + escapeHtml(t('dashboard.no_kpis')) + '</p></div>';

		let rows = '';
		entries.forEach(function (entry) {
			const metric = entry[0];
			const val = entry[1];
			rows += '<tr><td>' + escapeHtml(metric) + '</td><td>' + escapeHtml(String(val.latest || '—')) + '</td><td>' + escapeHtml(String(val.previous || '—')) + '</td></tr>';
		});

		return `
			<div class="card" style="margin-top:var(--tma-sp-4);">
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
				container.innerHTML = '<div class="card"><p style="color:var(--tma-muted);">' + escapeHtml(t('documents.no_docs')) + '</p></div>';
				return;
			}

			let cards = '';
			docsState.forEach(function (doc, idx) {
				const statusClass = doc.status === 'approved' ? 'badge--success' : (doc.status === 'pending' ? 'badge--warning' : 'badge--info');
				const code = doc.slug || '';
				cards += '<article class="card" style="margin-bottom:12px;">'
					+ '<div style="display:flex;justify-content:space-between;gap:12px;align-items:flex-start;">'
					+ '<div style="flex:1;">'
					+ '<p style="font-size:11px;color:var(--tma-muted);margin:0 0 6px 0;">' + escapeHtml(code) + '</p>'
					+ '<h3 style="margin:0 0 6px 0;font-size:16px;">' + escapeHtml(doc.title || '') + '</h3>'
					+ '<p style="margin:0;color:var(--tma-muted);font-size:12px;">Actualizado: ' + formatDate(doc.updated_at) + '</p>'
					+ (doc.status === 'changes_requested' && doc.notes
						? '<p style="margin:8px 0 0 0;font-size:12px;color:var(--tma-warning);border-left:3px solid var(--tma-warning);padding-left:8px;">💬 ' + escapeHtml(doc.notes) + '</p>'
						: '')
					+ '</div>'
					+ '<div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">'
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
					<div style="margin:0 0 16px 0;">
						<div style="display:flex;justify-content:space-between;font-size:12px;color:var(--tma-muted);margin-bottom:6px;">
							<span>Progreso aprobación</span>
							<strong>${approvedCount}/${total} (${percent}%)</strong>
						</div>
						<div style="height:8px;background:#e5e7eb;border-radius:999px;overflow:hidden;">
							<div style="height:100%;background:#B8860B;width:${percent}%;"></div>
						</div>
					</div>
					<div>${cards}</div>
				</div>
				<div id="tma-doc-viewer-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.72);z-index:9999;padding:24px;">
					<div style="height:100%;max-width:1100px;margin:0 auto;background:#fff;border-radius:10px;overflow:hidden;display:flex;flex-direction:column;">
						<div style="padding:10px 14px;border-bottom:1px solid #ddd;display:flex;justify-content:space-between;align-items:center;">
							<strong id="tma-doc-viewer-title">Documento</strong>
							<div style="display:flex;gap:8px;align-items:center;">
								<button class="btn" id="tma-doc-prev">Anterior</button>
								<button class="btn" id="tma-doc-next">Siguiente</button>
								<button class="btn" id="tma-doc-viewer-close">Cerrar</button>
							</div>
						</div>
						<div style="padding:10px 14px;border-bottom:1px solid #eee;display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
							<button class="btn btn--success" id="tma-doc-approve">Aprobado</button>
							<button class="btn btn--warning" id="tma-doc-changes">Con cambios</button>
							<button class="btn btn--primary" id="tma-doc-add-note">Dejar nota</button>
							<textarea id="tma-doc-change-notes" class="input textarea" rows="2" placeholder="Describe cambios (mínimo 10 caracteres)" style="display:none;min-width:320px;"></textarea>
							<button class="btn btn--accent" id="tma-doc-save-changes" style="display:none;">Guardar cambios</button>
													<div id="tma-doc-note-form" style="display:none;gap:6px;align-items:flex-start;width:100%;margin-top:6px;">
														<textarea id="tma-doc-note-text" class="input textarea" rows="2" placeholder="Escribe una nota sobre este documento..." style="flex:1;min-width:240px;"></textarea>
														<button class="btn btn--primary" id="tma-doc-save-note">Guardar</button>
														<button class="btn" id="tma-doc-cancel-note">Cancelar</button>
													</div>
						</div>
						<div id="tma-doc-viewer-host" style="position:relative;flex:1;overflow:auto;background:#f6f7f8;"></div>
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
					document.getElementById('tma-doc-change-notes').style.display = 'block';
					document.getElementById('tma-doc-save-changes').style.display = 'inline-flex';
				});
			}
			const saveChangesBtn = document.getElementById('tma-doc-save-changes');
			if (saveChangesBtn) {
				saveChangesBtn.addEventListener('click', function () {
					const notes = document.getElementById('tma-doc-change-notes').value || '';
					if (notes.trim().length < 10) {
						alert('La nota debe tener al menos 10 caracteres.');
						return;
					}
					saveDocStatus('changes_requested', notes.trim());
				});
			}

			const addNoteBtn = document.getElementById('tma-doc-add-note');
			if (addNoteBtn) {
				addNoteBtn.addEventListener('click', function () {
					var noteForm = document.getElementById('tma-doc-note-form');
					if (noteForm) noteForm.style.display = noteForm.style.display === 'none' ? 'flex' : 'none';
				});
						const saveNoteBtn = document.getElementById('tma-doc-save-note');
						if (saveNoteBtn) {
							saveNoteBtn.addEventListener('click', async function () {
								var noteTextEl = document.getElementById('tma-doc-note-text');
								var content = noteTextEl ? noteTextEl.value.trim() : '';
								if (!content) { alert('La nota no puede estar vacía.'); return; }
								try {
									await addDocumentNote(content);
									if (noteTextEl) noteTextEl.value = '';
									var noteForm = document.getElementById('tma-doc-note-form');
									if (noteForm) noteForm.style.display = 'none';
								} catch (err) {
									alert('Error al guardar nota: ' + getErrorMessage(err));
								}
							});
						}
						const cancelNoteBtn = document.getElementById('tma-doc-cancel-note');
						if (cancelNoteBtn) {
							cancelNoteBtn.addEventListener('click', function () {
								var noteForm = document.getElementById('tma-doc-note-form');
								if (noteForm) noteForm.style.display = 'none';
							});
						}
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
			if (notesEl) { notesEl.value = ''; notesEl.style.display = 'none'; }
			if (saveBtn) { saveBtn.style.display = 'none'; }
		} catch (err) {
			alert('Error al guardar estado: ' + getErrorMessage(err));
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
		alert('Nota guardada.');
	}

	async function openDocumentViewer(code, title) {
		const modal = document.getElementById('tma-doc-viewer-modal');
		const host = document.getElementById('tma-doc-viewer-host');
		const titleEl = document.getElementById('tma-doc-viewer-title');
		if (!modal || !host) return;
		modal.style.display = 'block';
		if (titleEl) titleEl.textContent = title || 'Documento';

		// Reflect current doc status in action buttons immediately
		if (currentDocIndex >= 0 && docsState[currentDocIndex]) {
			updateViewerActionButtons(docsState[currentDocIndex].status || 'pending');
		}

		// Prev/Next disabled state
		var prevBtn = document.getElementById('tma-doc-prev');
		var nextBtn = document.getElementById('tma-doc-next');
		if (prevBtn) prevBtn.disabled = (currentDocIndex <= 0);
		if (nextBtn) nextBtn.disabled = (currentDocIndex >= docsState.length - 1);

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
				</style>
				<div class="viewer-wrap">
					<div class="viewer-watermark">${escapeHtml(userName)} • ${escapeHtml(now)}</div>
					<div class="viewer-prose">${doc.html || ''}</div>
				</div>
			`;
		} catch (err) {
			host.innerHTML = '<div style="padding:18px;color:#b42318;">No se pudo cargar el documento: ' + escapeHtml(getErrorMessage(err, 'No existe en caché o la API no lo encontró.')) + '</div>';
		}
	}

	function closeDocumentViewer() {
		const modal = document.getElementById('tma-doc-viewer-modal');
		const host = document.getElementById('tma-doc-viewer-host');
		if (modal) modal.style.display = 'none';
		if (host) host.innerHTML = '';
		currentDocIndex = -1;
	}

	/* ═══════════════════════════════════════════════════════════════
	   Section: Leads
	   ═══════════════════════════════════════════════════════════════ */

	async function renderLeads(container) {
		try {
			const leads = await api('/leads');
			if (!leads.length) {
				container.innerHTML = '<div class="card"><p style="color:var(--tma-muted);">' + escapeHtml(t('leads.no_leads')) + '</p></div>';
				return;
			}

			let rows = '';
			leads.forEach(function (lead) {
				const leadId = Number(lead.id || 0);
					const currentValue = parseFloat(lead.lead_value) || 0;
					const statusOptions = ['new', 'contacted', 'quoted', 'won', 'lost'].map(function (s) {
						return '<option value="' + s + '"' + (s === lead.status ? ' selected' : '') + '>' + escapeHtml(s) + '</option>';
					}).join('');

					rows += '<tr>'
						+ '<td>' + escapeHtml(lead.name || '') + '</td>'
						+ '<td>' + escapeHtml(lead.email || '') + '</td>'
						+ '<td>' + escapeHtml(lead.source || '') + '</td>'
						+ '<td><select class="lead-status-select input input--select" data-lead-id="' + leadId + '" data-lead-value="' + currentValue + '" style="font-size:12px;padding:4px 8px;min-height:32px;">' + statusOptions + '</select></td>'
						+ '<td>$' + currentValue.toLocaleString() + '</td>'
						+ '<td>' + formatDate(lead.created_at) + '</td>'
						+ '<td><button class="btn btn--small btn--ghost js-view-history" data-lead-id="' + leadId + '">Ver historial</button></td>'
						+ '</tr>';
			});

			container.innerHTML = `
				<div class="card">
					<h2 class="card__title">${escapeHtml(t('leads.title'))}</h2>
					<div class="table-wrap">
						<table class="table">
							<thead><tr><th>${escapeHtml(t('leads.name'))}</th><th>${escapeHtml(t('leads.email'))}</th><th>${escapeHtml(t('leads.source'))}</th><th>${escapeHtml(t('leads.stage'))}</th><th>${escapeHtml(t('leads.value'))}</th><th>${escapeHtml(t('leads.date'))}</th><th>Timeline</th></tr></thead>
							<tbody>${rows}</tbody>
						</table>
					</div>
				</div>
				<div class="card" id="lead-history" style="margin-top:var(--tma-sp-4);display:none;">
					<h3 class="card__title">Timeline de historial</h3>
					<div id="lead-history-timeline" class="timeline"></div>
				</div>
			`;

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
						alert('Error actualizando lead: ' + getErrorMessage(err));
						sel.value = prevValue;
					} finally {
						sel.disabled = false;
					}
				});
			});
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
			card.style.display = 'block';
			if (!Array.isArray(items) || !items.length) {
				timeline.innerHTML = '<p style="color:var(--tma-muted);">Sin cambios registrados todavía.</p>';
				return;
			}

			let html = '';
			items.forEach(function (item) {
				html += '<div class="timeline__item">'
					+ '<div><strong>' + escapeHtml(item.action || 'Cambio') + '</strong></div>'
					+ '<div style="color:var(--tma-muted);font-size:12px;">'
					+ escapeHtml(formatDate(item.created_at))
					+ ' · ' + escapeHtml(item.user_name || ('user #' + Number(item.user_id || 0)))
					+ '</div>'
					+ '</div>';
			});

			timeline.innerHTML = html;
		} catch (err) {
			card.style.display = 'block';
			timeline.innerHTML = '<p style="color:#b42318;">No se pudo cargar el historial.</p>';
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
				notesList = '<p style="color:var(--tma-muted);">' + escapeHtml(t('notes.no_notes')) + '</p>';
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
				<div class="card" style="margin-top:var(--tma-sp-4);">
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
						alert(t('common.error') + ': ' + err.message);
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
				container.innerHTML = '<div class="card"><p style="color:var(--tma-muted);">' + escapeHtml(t('audit.no_entries')) + '</p></div>';
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
