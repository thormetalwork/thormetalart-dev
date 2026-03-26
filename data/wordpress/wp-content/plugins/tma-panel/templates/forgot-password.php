<?php
/**
 * TMA Panel — Forgot Password Template
 *
 * Branded password recovery form. Sends WP reset email.
 *
 * @package ThorMetalArt\Panel
 * @since   0.1.0
 */

defined( 'ABSPATH' ) || exit;

$message = '';
$error   = '';

if ( 'POST' === $_SERVER['REQUEST_METHOD'] ) {
	if ( ! isset( $_POST['tma_forgot_nonce'] )
		|| ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['tma_forgot_nonce'] ) ), 'tma_panel_forgot' ) ) {
		$error = 'Solicitud inválida.';
	} else {
		$user_login = sanitize_text_field( wp_unslash( $_POST['user_login'] ?? '' ) );
		$user       = get_user_by( 'email', $user_login );
		if ( ! $user ) {
			$user = get_user_by( 'login', $user_login );
		}

		// Always show success to prevent user enumeration.
		$message = 'Si la cuenta existe, recibirás un email con instrucciones para restablecer tu contraseña.';

		if ( $user ) {
			$key       = get_password_reset_key( $user );
			$reset_url = tma_panel_url( '/reset-password' ) . '?key=' . rawurlencode( $key ) . '&login=' . rawurlencode( $user->user_login );

			wp_mail(
				$user->user_email,
				'Thor Metal Art — Restablecer contraseña',
				sprintf(
					"Hola %s,\n\nHaz click en el siguiente enlace para restablecer tu contraseña:\n\n%s\n\nSi no solicitaste esto, ignora este email.\n\n— Thor Metal Art",
					$user->display_name,
					$reset_url
				)
			);
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
	<title>Recuperar contraseña — TMA Panel</title>
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
		input[type="text"], input[type="email"] {
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
			<div class="card__subtitle">Recuperar contraseña</div>
		</div>

		<?php if ( $message ) : ?>
			<div class="msg-success"><?php echo esc_html( $message ); ?></div>
		<?php endif; ?>

		<?php if ( $error ) : ?>
			<div class="msg-error"><?php echo esc_html( $error ); ?></div>
		<?php endif; ?>

		<?php if ( ! $message ) : ?>
		<form method="post">
			<?php wp_nonce_field( 'tma_panel_forgot', 'tma_forgot_nonce' ); ?>

			<label for="user_login">Email o usuario</label>
			<input type="text" name="user_login" id="user_login" autocomplete="email" required>

			<button type="submit" class="btn">Enviar enlace de recuperación</button>
		</form>
		<?php endif; ?>

		<a href="<?php echo esc_url( tma_panel_url( '/login' ) ); ?>" class="back-link">← Volver al login</a>
	</div>
</body>
</html>
