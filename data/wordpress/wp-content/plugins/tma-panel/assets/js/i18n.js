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
			'nav.googleSetup': 'Google Setup',

			// Google Setup
			'google.title': 'Configuración de Google — Integraciones',
			'google.warning': '⚠️ IMPORTANTE: Esta solicitud DEBE hacerse desde la cuenta thormetalwork@gmail.com',
			'google.step1': 'Paso 1: Abrir el formulario de solicitud',
			'google.step1.desc': 'Haz clic en el botón (asegúrate de estar logueado como thormetalwork@gmail.com):',
			'google.step2': 'Paso 2: Llenar con estos datos',
			'google.step3': 'Paso 3: Descripción del uso (copiar y pegar)',
			'google.step4': 'Paso 4: Después de enviar',
			'google.step4.items': 'Recibirás un email de confirmación|Google revisará la solicitud (1-3 días hábiles)|Cuando aprueben, recibirás otro email|Avísanos para activar la integración',
			'google.refs': 'Links de referencia',
			'google.prereqs': 'Requisitos previos (completados)',
			'google.openForm': 'Abrir Formulario de Solicitud GBP API',
			'google.copyDesc': 'Descripción para copiar y pegar',
			'google.gbpFormTitle': 'Acción requerida — Solicitar acceso GBP API',
			'google.status.active': 'Activo',
			'google.status.ready': 'Listo para usar',
			'google.status.blocked': 'Bloqueado',
			'google.int.oauth2': 'Google OAuth2',
			'google.int.brand': 'Marca',
			'google.int.verified': 'Verificada ✓',
			'google.int.tokenCache': 'Cache de token',
			'google.int.metrics': 'Métricas',
			'google.int.sync': 'Sincronización',
			'google.int.dailyCron': 'WP-Cron diario (UPSERT por período)',
			'google.int.dataNote': 'Nota',
			'google.int.ga4AccumulatingData': 'Tag reciente — los datos se acumulan con el tráfico',
			'google.int.gscAccumulatingData': 'Search Console tarda 2-3 días en acumular datos',
			'google.int.verification': 'Verificación',
			'google.int.restricted': 'Configurada y restringida (IP + APIs específicas)',
			'google.int.usage': 'Uso',
			'google.int.mapsUsage': 'Embed en página de contacto',
			'google.int.blocker': 'Bloqueador',
			'google.int.gbpBlocker': 'Quota = 0 — requiere aprobación manual de Google',
			'google.int.gbpAction': 'Enviar formulario de solicitud (ver sección abajo)',
			'google.int.action': 'Acción requerida',
			'google.int.igBlocker': 'Requiere crear Facebook App + vincular cuenta IG Business',
			'google.int.igAction': 'Crear app en developers.facebook.com y conectar la cuenta de Instagram',
			'google.field.requestType': 'Tipo de solicitud',
			'google.field.company': 'Nombre de la empresa',
			'google.field.website': 'Sitio web',
			'google.field.email': 'Correo de contacto',
			'google.field.locations': 'Cantidad de ubicaciones',
			'google.field.col': 'Campo',
			'google.field.valCol': 'Valor',
			'google.infra.title': 'Infraestructura GCP',
			'google.infra.apisEnabled': 'APIs habilitadas',
			'google.infra.billing': 'Facturación',
			'google.infra.billingDetail': 'Activa — alerta a $25/mes',
			'google.infra.serviceAccount': 'Service Account',
			'google.infra.secretManager': 'Secret Manager',
			'google.infra.cronSchedule': 'Cron de sincronización',
			'google.infra.cronDetail': 'Diario — GA4 + Search Console (GBP + IG pendientes)',

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
			'nav.googleSetup': 'Google Setup',

			// Google Setup
			'google.title': 'Google Configuration — Integrations',
			'google.warning': '⚠️ IMPORTANT: This request MUST be made from the thormetalwork@gmail.com account',
			'google.step1': 'Step 1: Open the request form',
			'google.step1.desc': 'Click the button (make sure you are logged in as thormetalwork@gmail.com):',
			'google.step2': 'Step 2: Fill in with these details',
			'google.step3': 'Step 3: Usage description (copy and paste)',
			'google.step4': 'Step 4: After submitting',
			'google.step4.items': 'You will receive a confirmation email|Google will review the request (1-3 business days)|When approved, you will receive another email|Let us know to activate the integration',
			'google.refs': 'Reference links',
			'google.prereqs': 'Prerequisites (completed)',
			'google.openForm': 'Open GBP API Request Form',
			'google.copyDesc': 'Description to copy and paste',
			'google.gbpFormTitle': 'Action required — Request GBP API access',
			'google.status.active': 'Active',
			'google.status.ready': 'Ready to use',
			'google.status.blocked': 'Blocked',
			'google.int.oauth2': 'Google OAuth2',
			'google.int.brand': 'Brand',
			'google.int.verified': 'Verified ✓',
			'google.int.tokenCache': 'Token cache',
			'google.int.metrics': 'Metrics',
			'google.int.sync': 'Sync',
			'google.int.dailyCron': 'Daily WP-Cron (UPSERT per period)',
			'google.int.dataNote': 'Note',
			'google.int.ga4AccumulatingData': 'Recent tag — data accumulates with traffic',
			'google.int.gscAccumulatingData': 'Search Console takes 2-3 days to accumulate data',
			'google.int.verification': 'Verification',
			'google.int.restricted': 'Configured and restricted (IP + specific APIs)',
			'google.int.usage': 'Usage',
			'google.int.mapsUsage': 'Embed on contact page',
			'google.int.blocker': 'Blocker',
			'google.int.gbpBlocker': 'Quota = 0 — requires manual approval from Google',
			'google.int.gbpAction': 'Submit request form (see section below)',
			'google.int.action': 'Action required',
			'google.int.igBlocker': 'Requires creating Facebook App + linking IG Business account',
			'google.int.igAction': 'Create app at developers.facebook.com and connect Instagram account',
			'google.field.requestType': 'Request type',
			'google.field.company': 'Company name',
			'google.field.website': 'Website',
			'google.field.email': 'Contact email',
			'google.field.locations': 'Number of locations',
			'google.field.col': 'Field',
			'google.field.valCol': 'Value',
			'google.infra.title': 'GCP Infrastructure',
			'google.infra.apisEnabled': 'APIs enabled',
			'google.infra.billing': 'Billing',
			'google.infra.billingDetail': 'Active — alert at $25/month',
			'google.infra.serviceAccount': 'Service Account',
			'google.infra.secretManager': 'Secret Manager',
			'google.infra.cronSchedule': 'Sync cron',
			'google.infra.cronDetail': 'Daily — GA4 + Search Console (GBP + IG pending)',

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
