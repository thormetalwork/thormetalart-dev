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

	const { apiBase, nonce } = window.TMA_PANEL;

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
	   Router (hash-based)
	   ═══════════════════════════════════════════════════════════════ */

	const routes = {
		dashboard: renderDashboard,
		documents: renderDocuments,
		leads: renderLeads,
		notes: renderNotes,
		audit: renderAudit,
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
			title.textContent = section.charAt(0).toUpperCase() + section.slice(1);
		}

		// Render section.
		const content = document.getElementById('tma-content');
		if (content && render) {
			content.innerHTML = '<div class="loading">Cargando...</div>';
			render(content);
		}
	}

	/* ═══════════════════════════════════════════════════════════════
	   Section Renderers (stubs — fleshed out in later tickets)
	   ═══════════════════════════════════════════════════════════════ */

	function renderDashboard(container) {
		container.innerHTML = `
			<div class="card">
				<h2 class="card__title">Dashboard</h2>
				<p style="color: var(--tma-muted);">Panel ejecutivo de Thor Metal Art. Los datos se conectarán en tickets posteriores.</p>
			</div>
		`;
	}

	function renderDocuments(container) {
		container.innerHTML = `
			<div class="card">
				<h2 class="card__title">Documentos</h2>
				<p style="color: var(--tma-muted);">Sección de documentos del proyecto. Se implementará en Fase 10.</p>
			</div>
		`;
	}

	function renderLeads(container) {
		container.innerHTML = `
			<div class="card">
				<h2 class="card__title">Leads</h2>
				<p style="color: var(--tma-muted);">Pipeline de leads. Se implementará en Fase 11.</p>
			</div>
		`;
	}

	function renderNotes(container) {
		container.innerHTML = `
			<div class="card">
				<h2 class="card__title">Notas</h2>
				<p style="color: var(--tma-muted);">Sistema de notas bidireccional. Se implementará en Fase 10.</p>
			</div>
		`;
	}

	function renderAudit(container) {
		container.innerHTML = `
			<div class="card">
				<h2 class="card__title">Audit Log</h2>
				<p style="color: var(--tma-muted);">Registro de acciones. Se implementará en Fase 8 (PANEL-008).</p>
			</div>
		`;
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
