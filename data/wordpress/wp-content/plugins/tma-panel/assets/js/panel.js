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
			const data = await api('/dashboard');
			const kpis = data.kpis || {};
			const counts = data.counts || {};

			container.innerHTML = `
				<div class="kpi-grid">
					<div class="kpi-card">
						<span class="kpi-card__label">${escapeHtml(t('dashboard.leads'))}</span>
						<span class="kpi-card__value">${parseInt(counts.leads) || 0}</span>
					</div>
					<div class="kpi-card">
						<span class="kpi-card__label">${escapeHtml(t('dashboard.documents'))}</span>
						<span class="kpi-card__value">${parseInt(counts.documents) || 0}</span>
					</div>
					<div class="kpi-card">
						<span class="kpi-card__label">${escapeHtml(t('dashboard.notes'))}</span>
						<span class="kpi-card__value">${parseInt(counts.notes) || 0}</span>
					</div>
					<div class="kpi-card kpi-card--accent">
						<span class="kpi-card__label">${escapeHtml(t('dashboard.kpi_records'))}</span>
						<span class="kpi-card__value">${parseInt(counts.kpis) || 0}</span>
					</div>
				</div>
				${renderKpiTable(kpis)}
			`;
		} catch (err) {
			showError(container, t('error.loading_dashboard') + ': ' + err.message);
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

	async function renderDocuments(container) {
		try {
			const docs = await api('/documents');
			if (!docs.length) {
				container.innerHTML = '<div class="card"><p style="color:var(--tma-muted);">' + escapeHtml(t('documents.no_docs')) + '</p></div>';
				return;
			}

			let rows = '';
			docs.forEach(function (doc) {
				const statusClass = doc.status === 'final' ? 'badge--success' : (doc.status === 'draft' ? 'badge--warning' : 'badge--info');
				rows += '<tr>'
					+ '<td>' + escapeHtml(doc.title || '') + '</td>'
					+ '<td>' + escapeHtml(doc.doc_type || '') + '</td>'
					+ '<td><span class="badge ' + statusClass + '">' + escapeHtml(doc.status || '') + '</span></td>'
					+ '<td>' + formatDate(doc.updated_at) + '</td>'
					+ '</tr>';
			});

			container.innerHTML = `
				<div class="card">
					<h2 class="card__title">${escapeHtml(t('documents.title'))}</h2>
					<div class="table-wrap">
						<table class="table">
							<thead><tr><th>${escapeHtml(t('documents.doc_title'))}</th><th>${escapeHtml(t('documents.type'))}</th><th>${escapeHtml(t('documents.status'))}</th><th>${escapeHtml(t('documents.updated'))}</th></tr></thead>
							<tbody>${rows}</tbody>
						</table>
					</div>
				</div>
			`;
		} catch (err) {
			showError(container, t('error.loading_documents') + ': ' + err.message);
		}
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
				const stageClass = {
					new: 'badge--info',
					contacted: 'badge--warning',
					quoted: 'badge--accent',
					won: 'badge--success',
					lost: 'badge--danger',
				}[lead.stage] || 'badge--info';

				rows += '<tr>'
					+ '<td>' + escapeHtml(lead.name || '') + '</td>'
					+ '<td>' + escapeHtml(lead.email || '') + '</td>'
					+ '<td>' + escapeHtml(lead.source || '') + '</td>'
					+ '<td><span class="badge ' + stageClass + '">' + escapeHtml(lead.stage || '') + '</span></td>'
					+ '<td>$' + (parseFloat(lead.value) || 0).toLocaleString() + '</td>'
					+ '<td>' + formatDate(lead.created_at) + '</td>'
					+ '</tr>';
			});

			container.innerHTML = `
				<div class="card">
					<h2 class="card__title">${escapeHtml(t('leads.title'))}</h2>
					<div class="table-wrap">
						<table class="table">
							<thead><tr><th>${escapeHtml(t('leads.name'))}</th><th>${escapeHtml(t('leads.email'))}</th><th>${escapeHtml(t('leads.source'))}</th><th>${escapeHtml(t('leads.stage'))}</th><th>${escapeHtml(t('leads.value'))}</th><th>${escapeHtml(t('leads.date'))}</th></tr></thead>
							<tbody>${rows}</tbody>
						</table>
					</div>
				</div>
			`;
		} catch (err) {
			showError(container, t('error.loading_leads') + ': ' + err.message);
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
					notesList += `
						<div class="note-item">
							<div class="note-item__header">
								<strong>${escapeHtml(note.title || t('notes.no_title'))}</strong>
								<span class="badge ${vis}">${escapeHtml(visLabel)}</span>
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
							<select name="visibility" class="input input--select">
								<option value="shared">${escapeHtml(t('notes.shared'))}</option>
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
							}),
						});
						renderNotes(container);
					} catch (err) {
						alert(t('common.error') + ': ' + err.message);
					}
				});
			}
		} catch (err) {
			showError(container, t('error.loading_notes') + ': ' + err.message);
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
			showError(container, t('error.loading_audit') + ': ' + err.message);
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
