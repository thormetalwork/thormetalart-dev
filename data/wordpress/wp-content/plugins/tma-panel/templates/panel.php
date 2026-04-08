<?php
/**
 * TMA Panel — SPA Shell Template
 *
 * HTML base served by the router when accessing panel.thormetalart.com.
 * The JavaScript SPA renders all sections dynamically.
 *
 * @package ThorMetalArt\Panel
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

$current_user = wp_get_current_user();
$is_admin     = in_array( 'tma_admin', $current_user->roles, true )
				|| in_array( 'administrator', $current_user->roles, true );
$nonce        = wp_create_nonce( 'wp_rest' );
?>
<!DOCTYPE html>
<html lang="es" data-theme="dark">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, nofollow">
	<title>TMA Panel — Thor Metal Art</title>

	<!-- Fonts -->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">

	<!-- Panel CSS -->
	<link rel="stylesheet" href="<?php echo esc_url( TMA_PANEL_URL . 'assets/css/panel.css' ); ?>?v=<?php echo esc_attr( TMA_PANEL_VERSION ); ?>">

	<!-- Config para el SPA -->
	<script>
		window.TMA_PANEL = {
			apiBase:  '/wp-json/tma-panel/v1',
			nonce:    '<?php echo esc_js( $nonce ); ?>',
			user: {
				id:      <?php echo (int) $current_user->ID; ?>,
				name:    '<?php echo esc_js( $current_user->display_name ); ?>',
				role:    '<?php echo esc_js( $is_admin ? 'tma_admin' : 'tma_client' ); ?>',
				isAdmin: <?php echo $is_admin ? 'true' : 'false'; ?>,
			},
			version:  '<?php echo esc_js( TMA_PANEL_VERSION ); ?>',
			siteUrl:  '<?php echo esc_url( home_url() ); ?>',
		};
	</script>
</head>
<body>
	<div id="tma-panel-app">
		<!-- Sidebar -->
		<nav id="tma-sidebar" class="sidebar" role="navigation" aria-label="Panel de navegación">
			<div class="sidebar__brand">
				<span class="sidebar__logo">TMA</span>
				<span class="sidebar__title">Panel</span>
			</div>
			<ul class="sidebar__nav">
				<li><a href="#dashboard" class="nav-link active" data-section="dashboard" data-i18n="nav.dashboard">Dashboard</a></li>
				<li><a href="#documents" class="nav-link" data-section="documents" data-i18n="nav.documents">Documentos</a></li>
				<li><a href="#leads" class="nav-link" data-section="leads" data-i18n="nav.leads">Leads</a></li>
				<li><a href="#notes" class="nav-link" data-section="notes" data-i18n="nav.notes">Notas</a></li>
				<?php if ( $is_admin ) : ?>
				<li><a href="#audit" class="nav-link" data-section="audit" data-i18n="nav.audit">Audit Log</a></li>
				<?php endif; ?>
			</ul>
			<div class="sidebar__footer">
				<div class="lang-switch">
					<button class="lang-switch__btn" data-lang="es" title="Español">ES</button>
					<button class="lang-switch__btn" data-lang="en" title="English">EN</button>
				</div>
				<span class="sidebar__user"><?php echo esc_html( $current_user->display_name ); ?></span>
				<a href="<?php echo esc_url( wp_logout_url( tma_panel_url( '/login' ) ) ); ?>" class="sidebar__logout" data-i18n="common.logout">Salir</a>
			</div>
		</nav>

		<!-- Sidebar overlay (mobile) -->
		<div id="tma-sidebar-overlay" class="sidebar-overlay"></div>

		<!-- Main content area -->
		<main id="tma-main" class="main" role="main">
			<header class="main__header">
				<button id="tma-hamburger" class="hamburger" aria-label="Abrir menú" aria-expanded="false">
					<span class="hamburger__line"></span>
					<span class="hamburger__line"></span>
					<span class="hamburger__line"></span>
				</button>
				<h1 id="tma-page-title">Dashboard</h1>
			</header>
			<div id="tma-content" class="main__content">
				<!-- SPA renders sections here -->
				<div class="loading" data-i18n="common.loading">Cargando...</div>
			</div>
		</main>
	</div>

	<!-- i18n Dictionary -->
	<script src="<?php echo esc_url( TMA_PANEL_URL . 'assets/js/i18n.js' ); ?>?v=<?php echo esc_attr( TMA_PANEL_VERSION ); ?>"></script>

	<!-- Chart.js (local bundle) -->
	<script src="<?php echo esc_url( TMA_PANEL_URL . 'assets/js/chart.umd.min.js' ); ?>?v=<?php echo esc_attr( TMA_PANEL_VERSION ); ?>"></script>

	<!-- Panel JS -->
	<script src="<?php echo esc_url( TMA_PANEL_URL . 'assets/js/panel.js' ); ?>?v=<?php echo esc_attr( TMA_PANEL_VERSION ); ?>"></script>
</body>
</html>
