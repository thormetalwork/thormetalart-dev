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

	const sectionTitles = {
		dashboard: 'Dashboard',
		documents: 'Documentos',
		leads: 'Leads',
		notes: 'Notas',
		audit: 'Audit Log',
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
			title.textContent = sectionTitles[section] || section;
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
						<span class="kpi-card__label">Leads</span>
						<span class="kpi-card__value">${parseInt(counts.leads) || 0}</span>
					</div>
					<div class="kpi-card">
						<span class="kpi-card__label">Documentos</span>
						<span class="kpi-card__value">${parseInt(counts.documents) || 0}</span>
					</div>
					<div class="kpi-card">
						<span class="kpi-card__label">Notas</span>
						<span class="kpi-card__value">${parseInt(counts.notes) || 0}</span>
					</div>
					<div class="kpi-card kpi-card--accent">
						<span class="kpi-card__label">KPI Records</span>
						<span class="kpi-card__value">${parseInt(counts.kpis) || 0}</span>
					</div>
				</div>
				${renderKpiTable(kpis)}
			`;
		} catch (err) {
			showError(container, 'Error cargando dashboard: ' + err.message);
		}
	}

	function renderKpiTable(kpis) {
		const entries = Object.entries(kpis);
		if (!entries.length) return '<div class="card"><p style="color:var(--tma-muted);">No hay KPIs registrados.</p></div>';

		let rows = '';
		entries.forEach(function (entry) {
			const metric = entry[0];
			const val = entry[1];
			rows += '<tr><td>' + escapeHtml(metric) + '</td><td>' + escapeHtml(String(val.latest || '—')) + '</td><td>' + escapeHtml(String(val.previous || '—')) + '</td></tr>';
		});

		return `
			<div class="card" style="margin-top:var(--tma-sp-4);">
				<h2 class="card__title">KPIs</h2>
				<div class="table-wrap">
					<table class="table">
						<thead><tr><th>Métrica</th><th>Último</th><th>Anterior</th></tr></thead>
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
				container.innerHTML = '<div class="card"><p style="color:var(--tma-muted);">No hay documentos.</p></div>';
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
					<h2 class="card__title">Documentos del Proyecto</h2>
					<div class="table-wrap">
						<table class="table">
							<thead><tr><th>Título</th><th>Tipo</th><th>Estado</th><th>Actualizado</th></tr></thead>
							<tbody>${rows}</tbody>
						</table>
					</div>
				</div>
			`;
		} catch (err) {
			showError(container, 'Error cargando documentos: ' + err.message);
		}
	}

	/* ═══════════════════════════════════════════════════════════════
	   Section: Leads
	   ═══════════════════════════════════════════════════════════════ */

	async function renderLeads(container) {
		try {
			const leads = await api('/leads');
			if (!leads.length) {
				container.innerHTML = '<div class="card"><p style="color:var(--tma-muted);">No hay leads registrados.</p></div>';
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
					<h2 class="card__title">Pipeline de Leads</h2>
					<div class="table-wrap">
						<table class="table">
							<thead><tr><th>Nombre</th><th>Email</th><th>Origen</th><th>Etapa</th><th>Valor</th><th>Fecha</th></tr></thead>
							<tbody>${rows}</tbody>
						</table>
					</div>
				</div>
			`;
		} catch (err) {
			showError(container, 'Error cargando leads: ' + err.message);
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
					notesList += `
						<div class="note-item">
							<div class="note-item__header">
								<strong>${escapeHtml(note.title || 'Sin título')}</strong>
								<span class="badge ${vis}">${escapeHtml(note.visibility || '')}</span>
								<span class="note-item__date">${formatDate(note.created_at)}</span>
							</div>
							<div class="note-item__body">${escapeHtml(note.content || '')}</div>
						</div>
					`;
				});
			} else {
				notesList = '<p style="color:var(--tma-muted);">No hay notas.</p>';
			}

			container.innerHTML = `
				<div class="card">
					<h2 class="card__title">Notas</h2>
					<form id="note-form" class="note-form">
						<input type="text" name="title" class="input" placeholder="Título de la nota" required>
						<textarea name="content" class="input textarea" placeholder="Contenido..." rows="3" required></textarea>
						<div class="note-form__actions">
							<select name="visibility" class="input input--select">
								<option value="shared">Compartida</option>
								${user.isAdmin ? '<option value="internal">Interna</option>' : ''}
							</select>
							<button type="submit" class="btn btn--primary">Agregar Nota</button>
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
						alert('Error: ' + err.message);
					}
				});
			}
		} catch (err) {
			showError(container, 'Error cargando notas: ' + err.message);
		}
	}

	/* ═══════════════════════════════════════════════════════════════
	   Section: Audit Log
	   ═══════════════════════════════════════════════════════════════ */

	async function renderAudit(container) {
		if (!user.isAdmin) {
			showError(container, 'Acceso restringido a administradores.');
			return;
		}

		try {
			const entries = await api('/audit');
			if (!entries.length) {
				container.innerHTML = '<div class="card"><p style="color:var(--tma-muted);">No hay registros de auditoría.</p></div>';
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
					<h2 class="card__title">Audit Log</h2>
					<div class="table-wrap">
						<table class="table">
							<thead><tr><th>Fecha</th><th>Acción</th><th>Entidad</th><th>ID</th><th>Usuario</th></tr></thead>
							<tbody>${rows}</tbody>
						</table>
					</div>
				</div>
			`;
		} catch (err) {
			showError(container, 'Error cargando audit log: ' + err.message);
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
		initSidebar();
		navigate();
	});

	window.addEventListener('hashchange', navigate);
})();
