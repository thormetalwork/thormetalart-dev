<?php
/**
 * TMA Panel — Login Template (placeholder)
 *
 * Minimal login page. Will be fully branded in TICKET-PANEL-005.
 *
 * @package ThorMetalArt\Panel
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, nofollow">
	<title>Login — TMA Panel</title>
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@600;700&family=Inter:wght@400;500&display=swap" rel="stylesheet">
	<style>
		* { box-sizing: border-box; margin: 0; padding: 0; }
		body {
			font-family: 'Inter', sans-serif;
			background: #0c0a09;
			color: #f5f5f4;
			display: flex;
			align-items: center;
			justify-content: center;
			min-height: 100vh;
		}
		.login-card {
			background: #1c1917;
			border: 1px solid #292524;
			border-radius: 12px;
			padding: 48px 40px;
			width: 100%;
			max-width: 400px;
		}
		.login-card__brand {
			text-align: center;
			margin-bottom: 32px;
		}
		.login-card__logo {
			font-family: 'Cormorant Garamond', serif;
			font-size: 2rem;
			font-weight: 700;
			color: #B8860B;
		}
		.login-card__subtitle {
			font-size: 0.875rem;
			color: #78716c;
			margin-top: 4px;
		}
		label {
			display: block;
			font-size: 0.8125rem;
			color: #a8a29e;
			margin-bottom: 6px;
		}
		input[type="text"], input[type="password"] {
			width: 100%;
			padding: 10px 14px;
			background: #0c0a09;
			border: 1px solid #44403c;
			border-radius: 6px;
			color: #f5f5f4;
			font-size: 0.9375rem;
			margin-bottom: 16px;
		}
		input:focus {
			outline: none;
			border-color: #B8860B;
		}
		.btn-login {
			width: 100%;
			padding: 12px;
			background: #B8860B;
			color: #0c0a09;
			border: none;
			border-radius: 6px;
			font-size: 1rem;
			font-weight: 600;
			cursor: pointer;
			min-height: 44px;
		}
		.btn-login:hover { background: #d4a017; }
		.login-error {
			background: rgba(239, 68, 68, 0.1);
			border: 1px solid #ef4444;
			color: #fca5a5;
			padding: 10px 14px;
			border-radius: 6px;
			font-size: 0.8125rem;
			margin-bottom: 16px;
			display: none;
		}
		.remember-row {
			display: flex;
			align-items: center;
			gap: 8px;
			margin-bottom: 20px;
		}
		.remember-row label { margin-bottom: 0; }
	</style>
</head>
<body>
	<div class="login-card">
		<div class="login-card__brand">
			<div class="login-card__logo">Thor Metal Art</div>
			<div class="login-card__subtitle">Panel Ejecutivo</div>
		</div>

		<div id="login-error" class="login-error"></div>

		<form id="login-form" method="post">
			<?php wp_nonce_field( 'tma_panel_login', 'tma_login_nonce' ); ?>

			<label for="log">Email o usuario</label>
			<input type="text" name="log" id="log" autocomplete="username" required>

			<label for="pwd">Contraseña</label>
			<input type="password" name="pwd" id="pwd" autocomplete="current-password" required>

			<div class="remember-row">
				<input type="checkbox" name="rememberme" id="rememberme" value="forever">
				<label for="rememberme">Recordarme</label>
			</div>

			<button type="submit" class="btn-login">Entrar</button>
		</form>

		<div style="text-align:center; margin-top:16px;">
			<a href="<?php echo esc_url( tma_panel_url( '/forgot-password' ) ); ?>" style="color:#a8a29e; font-size:0.8125rem; text-decoration:none;">¿Olvidaste tu contraseña?</a>
		</div>
	</div>

	<script>
	(function() {
		var form = document.getElementById('login-form');
		var errorEl = document.getElementById('login-error');

		form.addEventListener('submit', function(e) {
			e.preventDefault();
			errorEl.style.display = 'none';

			var data = new FormData(form);
			data.append('action', 'tma_panel_login');

			fetch('<?php echo esc_url( admin_url( "admin-ajax.php" ) ); ?>', {
				method: 'POST',
				body: data,
				credentials: 'same-origin',
			})
			.then(function(r) { return r.json(); })
			.then(function(res) {
				if (res.success) {
					window.location.href = '<?php echo esc_url( tma_panel_url( '/' ) ); ?>';
				} else {
					errorEl.textContent = res.data || 'Credenciales inválidas.';
					errorEl.style.display = 'block';
				}
			})
			.catch(function() {
				errorEl.textContent = 'Error de conexión.';
				errorEl.style.display = 'block';
			});
		});
	})();
	</script>
</body>
</html>
