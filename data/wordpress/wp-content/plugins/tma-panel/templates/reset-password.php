<?php
/**
 * TMA Panel — Reset Password Template
 *
 * Branded password reset form. Validates key + login from email link.
 *
 * @package ThorMetalArt\Panel
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

$message = '';
$error   = '';
$key     = sanitize_text_field( wp_unslash( $_GET['key'] ?? '' ) );
$login   = sanitize_text_field( wp_unslash( $_GET['login'] ?? '' ) );

// Validate reset key.
$user = check_password_reset_key( $key, $login );
if ( is_wp_error( $user ) ) {
	$error = 'El enlace de recuperación es inválido o ha expirado.';
}

// Handle form submission.
if ( 'POST' === $_SERVER['REQUEST_METHOD'] && ! is_wp_error( $user ) ) {
	if ( ! isset( $_POST['tma_reset_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tma_reset_nonce'] ) ), 'tma_panel_reset' ) ) {
		$error = 'Solicitud inválida.';
	} else {
		$pass1 = $_POST['pass1'] ?? '';
		$pass2 = $_POST['pass2'] ?? '';

		if ( strlen( $pass1 ) < 8 ) {
			$error = 'La contraseña debe tener al menos 8 caracteres.';
		} elseif ( $pass1 !== $pass2 ) {
			$error = 'Las contraseñas no coinciden.';
		} else {
			reset_password( $user, $pass1 );
			$message = 'Contraseña actualizada. Ahora puedes iniciar sesión.';
		}
	}
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="robots" content="noindex, nofollow">
	<title>Restablecer contraseña — TMA Panel</title>
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
		.card {
			background: #1c1917;
			border: 1px solid #292524;
			border-radius: 12px;
			padding: 48px 40px;
			width: 100%;
			max-width: 400px;
		}
		.card__brand {
			text-align: center;
			margin-bottom: 32px;
		}
		.card__logo {
			font-family: 'Cormorant Garamond', serif;
			font-size: 2rem;
			font-weight: 700;
			color: #B8860B;
		}
		.card__subtitle {
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
		input[type="password"] {
			width: 100%;
			padding: 10px 14px;
			background: #0c0a09;
			border: 1px solid #44403c;
			border-radius: 6px;
			color: #f5f5f4;
			font-size: 0.9375rem;
			margin-bottom: 16px;
		}
		input:focus { outline: none; border-color: #B8860B; }
		.btn {
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
		.btn:hover { background: #d4a017; }
		.msg-success {
			background: rgba(34, 197, 94, 0.1);
			border: 1px solid #22c55e;
			color: #86efac;
			padding: 10px 14px;
			border-radius: 6px;
			font-size: 0.8125rem;
			margin-bottom: 16px;
		}
		.msg-error {
			background: rgba(239, 68, 68, 0.1);
			border: 1px solid #ef4444;
			color: #fca5a5;
			padding: 10px 14px;
			border-radius: 6px;
			font-size: 0.8125rem;
			margin-bottom: 16px;
		}
		.back-link {
			display: block;
			text-align: center;
			margin-top: 16px;
			color: #a8a29e;
			font-size: 0.8125rem;
			text-decoration: none;
		}
		.back-link:hover { color: #B8860B; }
	</style>
</head>
<body>
	<div class="card">
		<div class="card__brand">
			<div class="card__logo">Thor Metal Art</div>
			<div class="card__subtitle">Restablecer contraseña</div>
		</div>

		<?php if ( $message ) : ?>
			<div class="msg-success"><?php echo esc_html( $message ); ?></div>
			<a href="<?php echo esc_url( tma_panel_url( '/login' ) ); ?>" class="back-link">Ir al login →</a>
		<?php elseif ( $error ) : ?>
			<div class="msg-error"><?php echo esc_html( $error ); ?></div>
			<a href="<?php echo esc_url( tma_panel_url( '/forgot-password' ) ); ?>" class="back-link">Solicitar nuevo enlace</a>
		<?php else : ?>
		<form method="post">
			<?php wp_nonce_field( 'tma_panel_reset', 'tma_reset_nonce' ); ?>
			<input type="hidden" name="key" value="<?php echo esc_attr( $key ); ?>">
			<input type="hidden" name="login" value="<?php echo esc_attr( $login ); ?>">

			<label for="pass1">Nueva contraseña</label>
			<input type="password" name="pass1" id="pass1" autocomplete="new-password" required minlength="8">

			<label for="pass2">Confirmar contraseña</label>
			<input type="password" name="pass2" id="pass2" autocomplete="new-password" required minlength="8">

			<button type="submit" class="btn">Restablecer contraseña</button>
		</form>
		<?php endif; ?>
	</div>
</body>
</html>
