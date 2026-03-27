/**
 * TMA Panel — i18n Internationalization
 *
 * Bilingual ES/EN dictionary with translation helper.
 * Usage: t('nav.dashboard') → 'Dashboard' or 'Panel' depending on language.
 *
 * @package ThorMetalArt\Panel
 * @since   0.1.0
 */

(function () {
	'use strict';

	var STORAGE_KEY = 'tma_panel_lang';
	var currentLang = localStorage.getItem(STORAGE_KEY) || 'es';

	var dictionaries = {
		es: {
			// Navigation
			'nav.dashboard': 'Dashboard',
			'nav.documents': 'Documentos',
			'nav.leads': 'Leads',
			'nav.notes': 'Notas',
			'nav.audit': 'Audit Log',

			// Common
			'common.loading': 'Cargando...',
			'common.logout': 'Salir',
			'common.save': 'Guardar',
			'common.cancel': 'Cancelar',
			'common.search': 'Buscar',
			'common.filter': 'Filtrar',
			'common.export': 'Exportar',
			'common.delete': 'Eliminar',
			'common.edit': 'Editar',
			'common.close': 'Cerrar',
			'common.confirm': 'Confirmar',
			'common.error': 'Error',
			'common.success': 'Éxito',
			'common.no_data': 'Sin datos',

			// Dashboard
			'dashboard.title': 'Dashboard',
			'dashboard.leads': 'Leads',
			'dashboard.documents': 'Documentos',
			'dashboard.notes': 'Notas',
			'dashboard.kpi_records': 'KPI Records',
			'dashboard.kpis': 'KPIs',
			'dashboard.metric': 'Métrica',
			'dashboard.latest': 'Último',
			'dashboard.previous': 'Anterior',
			'dashboard.no_kpis': 'No hay KPIs registrados.',
			'dashboard.export': 'Exportar resumen',
			'dashboard.exported': 'Copiado',
			'dashboard.auto_refresh_in': 'Autoactualizacion en {{s}}s',
			'dashboard.refresh_now': 'Actualizar ahora',
			'dashboard.doc_progress': 'Progreso Documentos',
			'dashboard.doc_approval': 'Aprobación',
			'dashboard.recent_activity': 'Actividad Reciente',
			'dashboard.no_activity': 'Sin actividad reciente.',
			'dashboard.new_leads_alert': '{{n}} lead(s) nuevos requieren atención',
			'dashboard.pending_docs_alert': '{{n}} documento(s) pendientes de revisión',
			'dashboard.view_leads': 'Ver leads',
			'dashboard.view_docs': 'Ver documentos',

			// Documents
			'documents.title': 'Documentos del Proyecto',
			'documents.doc_title': 'Título',
			'documents.type': 'Tipo',
			'documents.status': 'Estado',
			'documents.updated': 'Actualizado',
			'documents.no_docs': 'No hay documentos.',

			// Leads
			'leads.title': 'Pipeline de Leads',
			'leads.name': 'Nombre',
			'leads.email': 'Email',
			'leads.source': 'Origen',
			'leads.stage': 'Etapa',
			'leads.value': 'Valor',
			'leads.date': 'Fecha',
			'leads.no_leads': 'No hay leads registrados.',
			'leads.total': 'Total Leads',
			'leads.pipeline_value': 'Valor Pipeline',
			'leads.new_leads': 'Leads Nuevos',
			'leads.filter_all': 'Todos',
			'leads.filter_source': 'Canal',
			'leads.filter_status': 'Estado',

			// Notes
			'notes.title': 'Notas',
			'notes.add': 'Agregar Nota',
			'notes.note_title': 'Título de la nota',
			'notes.content': 'Contenido...',
			'notes.shared': 'Compartida',
			'notes.internal': 'Interna',
			'notes.no_notes': 'No hay notas.',
			'notes.no_title': 'Sin título',

			// Audit
			'audit.title': 'Audit Log',
			'audit.date': 'Fecha',
			'audit.action': 'Acción',
			'audit.entity': 'Entidad',
			'audit.id': 'ID',
			'audit.user': 'Usuario',
			'audit.no_entries': 'No hay registros de auditoría.',
			'audit.restricted': 'Acceso restringido a administradores.',

			// Errors
			'error.loading_dashboard': 'Error cargando dashboard',
			'error.loading_documents': 'Error cargando documentos',
			'error.loading_leads': 'Error cargando leads',
			'error.loading_notes': 'Error cargando notas',
			'error.loading_audit': 'Error cargando audit log',
			'error.export_failed': 'Error al exportar',
		},

		en: {
			// Navigation
			'nav.dashboard': 'Dashboard',
			'nav.documents': 'Documents',
			'nav.leads': 'Leads',
			'nav.notes': 'Notes',
			'nav.audit': 'Audit Log',

			// Common
			'common.loading': 'Loading...',
			'common.logout': 'Logout',
			'common.save': 'Save',
			'common.cancel': 'Cancel',
			'common.search': 'Search',
			'common.filter': 'Filter',
			'common.export': 'Export',
			'common.delete': 'Delete',
			'common.edit': 'Edit',
			'common.close': 'Close',
			'common.confirm': 'Confirm',
			'common.error': 'Error',
			'common.success': 'Success',
			'common.no_data': 'No data',

			// Dashboard
			'dashboard.title': 'Dashboard',
			'dashboard.leads': 'Leads',
			'dashboard.documents': 'Documents',
			'dashboard.notes': 'Notes',
			'dashboard.kpi_records': 'KPI Records',
			'dashboard.kpis': 'KPIs',
			'dashboard.metric': 'Metric',
			'dashboard.latest': 'Latest',
			'dashboard.previous': 'Previous',
			'dashboard.no_kpis': 'No KPI records found.',
			'dashboard.export': 'Export summary',
			'dashboard.exported': 'Copied',
			'dashboard.auto_refresh_in': 'Auto-refresh in {{s}}s',
			'dashboard.refresh_now': 'Refresh now',
			'dashboard.doc_progress': 'Document Progress',
			'dashboard.doc_approval': 'Approval',
			'dashboard.recent_activity': 'Recent Activity',
			'dashboard.no_activity': 'No recent activity.',
			'dashboard.new_leads_alert': '{{n}} new lead(s) require attention',
			'dashboard.pending_docs_alert': '{{n}} document(s) pending review',
			'dashboard.view_leads': 'View leads',
			'dashboard.view_docs': 'View documents',

			// Documents
			'documents.title': 'Project Documents',
			'documents.doc_title': 'Title',
			'documents.type': 'Type',
			'documents.status': 'Status',
			'documents.updated': 'Updated',
			'documents.no_docs': 'No documents found.',

			// Leads
			'leads.title': 'Lead Pipeline',
			'leads.name': 'Name',
			'leads.email': 'Email',
			'leads.source': 'Source',
			'leads.stage': 'Stage',
			'leads.value': 'Value',
			'leads.date': 'Date',
			'leads.no_leads': 'No leads found.',
			'leads.total': 'Total Leads',
			'leads.pipeline_value': 'Pipeline Value',
			'leads.new_leads': 'New Leads',
			'leads.filter_all': 'All',
			'leads.filter_source': 'Source',
			'leads.filter_status': 'Status',

			// Notes
			'notes.title': 'Notes',
			'notes.add': 'Add Note',
			'notes.note_title': 'Note title',
			'notes.content': 'Content...',
			'notes.shared': 'Shared',
			'notes.internal': 'Internal',
			'notes.no_notes': 'No notes found.',
			'notes.no_title': 'Untitled',

			// Audit
			'audit.title': 'Audit Log',
			'audit.date': 'Date',
			'audit.action': 'Action',
			'audit.entity': 'Entity',
			'audit.id': 'ID',
			'audit.user': 'User',
			'audit.no_entries': 'No audit entries found.',
			'audit.restricted': 'Access restricted to administrators.',

			// Errors
			'error.loading_dashboard': 'Error loading dashboard',
			'error.loading_documents': 'Error loading documents',
			'error.loading_leads': 'Error loading leads',
			'error.loading_notes': 'Error loading notes',
			'error.loading_audit': 'Error loading audit log',
			'error.export_failed': 'Export failed',
		},
	};

	/**
	 * Translate a key to the current language.
	 * Falls back to ES, then returns the key itself.
	 */
	function t(key) {
		var dict = dictionaries[currentLang] || dictionaries.es;
		return dict[key] || dictionaries.es[key] || key;
	}

	/**
	 * Set the active language and persist to localStorage.
	 */
	function setLang(lang) {
		if (!dictionaries[lang]) return;
		currentLang = lang;
		localStorage.setItem(STORAGE_KEY, lang);
		applyTranslations();
		updateLangButtons();
	}

	/**
	 * Get the current language code.
	 */
	function getLang() {
		return currentLang;
	}

	/**
	 * Apply translations to all elements with data-i18n attribute.
	 */
	function applyTranslations() {
		document.querySelectorAll('[data-i18n]').forEach(function (el) {
			var key = el.getAttribute('data-i18n');
			var translated = t(key);
			if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
				el.placeholder = translated;
			} else {
				el.textContent = translated;
			}
		});
		document.documentElement.lang = currentLang;
	}

	/**
	 * Update language switch button states.
	 */
	function updateLangButtons() {
		document.querySelectorAll('.lang-switch__btn').forEach(function (btn) {
			btn.classList.toggle('active', btn.dataset.lang === currentLang);
		});
	}

	/**
	 * Initialize language switch click handlers.
	 */
	function initLangSwitch() {
		document.querySelectorAll('.lang-switch__btn').forEach(function (btn) {
			btn.addEventListener('click', function () {
				setLang(btn.dataset.lang);
			});
		});
		updateLangButtons();
		applyTranslations();
	}

	// Expose globally
	window.TMA_i18n = {
		t: t,
		setLang: setLang,
		getLang: getLang,
		init: initLangSwitch,
	};
})();
