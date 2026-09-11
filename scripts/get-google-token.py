#!/usr/bin/env python3
"""
TMA Dashboard — Google OAuth2 Token Generator
Generates a new refresh token with proper `state` parameter (CSRF protection).
Saves the new token to .env automatically.

Supports headless/remote servers: prints the auth URL, user opens in browser,
then pastes the redirect URL back into the terminal (copy-paste flow).

Usage:
    python3 scripts/get-google-token.py

Scopes included:
    - analytics.readonly      → GA4 Data API
    - webmasters.readonly     → Search Console API
    - business.manage         → Google Business Profile API
    - drive                   → Google Drive (docs/exports)
"""

import http.server
import json
import secrets
import sys
import threading
import urllib.parse
import urllib.request
from pathlib import Path

# ── Config ───────────────────────────────────────────────────────────────────

PORT         = 8765
REDIRECT_URI = f"http://localhost:{PORT}/callback"
SCOPES = [
    "https://www.googleapis.com/auth/analytics.readonly",
    "https://www.googleapis.com/auth/webmasters.readonly",
    "https://www.googleapis.com/auth/business.manage",
    "https://www.googleapis.com/auth/drive",
]
AUTH_URL  = "https://accounts.google.com/o/oauth2/v2/auth"
TOKEN_URL = "https://oauth2.googleapis.com/token"
ENV_FILE  = Path(__file__).parent.parent / ".env"
ENV_KEY   = "GOOGLE_OAUTH_REFRESH_TOKEN"

# ── Helpers ───────────────────────────────────────────────────────────────────

def load_env(path: Path) -> dict:
    env = {}
    if path.exists():
        for line in path.read_text().splitlines():
            line = line.strip()
            if line and not line.startswith("#") and "=" in line:
                k, _, v = line.partition("=")
                env[k.strip()] = v.strip()
    return env


def update_env(path: Path, key: str, value: str) -> None:
    lines = path.read_text().splitlines() if path.exists() else []
    updated = False
    for i, line in enumerate(lines):
        if line.strip().startswith(f"{key}=") or line.strip().startswith(f"{key} ="):
            lines[i] = f"{key}={value}"
            updated = True
            break
    if not updated:
        lines.append(f"{key}={value}")
    path.write_text("\n".join(lines) + "\n")


def exchange_code(code: str, client_id: str, client_secret: str) -> dict:
    data = urllib.parse.urlencode({
        "code":          code,
        "client_id":     client_id,
        "client_secret": client_secret,
        "redirect_uri":  REDIRECT_URI,
        "grant_type":    "authorization_code",
    }).encode()
    req = urllib.request.Request(TOKEN_URL, data=data, method="POST")
    req.add_header("Content-Type", "application/x-www-form-urlencoded")
    with urllib.request.urlopen(req, timeout=15) as resp:
        return json.loads(resp.read())

# ── Callback server ───────────────────────────────────────────────────────────

callback_result: dict = {}
server_ready   = threading.Event()

class CallbackHandler(http.server.BaseHTTPRequestHandler):
    def log_message(self, fmt, *args):
        pass  # suppress server logs

    def do_GET(self):
        parsed = urllib.parse.urlparse(self.path)
        if parsed.path != "/callback":
            self.send_error(404)
            return
        params = dict(urllib.parse.parse_qsl(parsed.query))
        callback_result.update(params)
        self.send_response(200)
        self.send_header("Content-Type", "text/html; charset=utf-8")
        self.end_headers()
        if "code" in params:
            body = b"<html><body style='font-family:sans-serif;text-align:center;padding:60px'><h2 style='color:#2d8a4e'>Authorized OK. You can close this window.</h2></body></html>"
        else:
            body = b"<html><body style='font-family:sans-serif;text-align:center;padding:60px'><h2 style='color:#c0392b'>Authorization error. Check terminal.</h2></body></html>"
        self.wfile.write(body)


def run_server(httpd):
    server_ready.set()
    httpd.handle_request()  # handle exactly one request then stop

# ── Main ──────────────────────────────────────────────────────────────────────

def main():
    env = load_env(ENV_FILE)
    client_id     = env.get("GOOGLE_OAUTH_CLIENT_ID", "")
    client_secret = env.get("GOOGLE_OAUTH_CLIENT_SECRET", "")

    if not client_id or not client_secret:
        print("ERROR: GOOGLE_OAUTH_CLIENT_ID / GOOGLE_OAUTH_CLIENT_SECRET no encontrados en .env")
        sys.exit(1)

    # Start HTTP server BEFORE printing the URL so VS Code can detect and
    # forward the port before the user opens the browser.
    try:
        httpd = http.server.HTTPServer(("0.0.0.0", PORT), CallbackHandler)
    except OSError as e:
        print(f"ERROR: No se pudo iniciar el servidor en puerto {PORT}: {e}")
        sys.exit(1)

    thread = threading.Thread(target=run_server, args=(httpd,), daemon=True)
    thread.start()
    server_ready.wait(timeout=5)

    # Generate cryptographically random state (CSRF protection)
    state = secrets.token_urlsafe(32)

    params = urllib.parse.urlencode({
        "client_id":     client_id,
        "redirect_uri":  REDIRECT_URI,
        "response_type": "code",
        "scope":         " ".join(SCOPES),
        "access_type":   "offline",
        "prompt":        "consent",
        "state":         state,
    })
    auth_url = f"{AUTH_URL}?{params}"

    print("\n" + "="*62)
    print("  TMA Dashboard — Google OAuth2 Token Generator")
    print("="*62)
    print(f"\n✓ Servidor escuchando en puerto {PORT}")
    print("\n⚠  VS Code Remote: comprueba la pestaña 'Ports' (parte inferior")
    print("   del editor). El puerto 8765 debería aparecer con estado")
    print("   'Forwarded'. Si no aparece, haz click en '+ Forward a Port'")
    print("   y añade el puerto 8765.\n")
    print("-"*62)
    print("PASO 1 — Abre esta URL en tu navegador:\n")
    print(f"  {auth_url}\n")
    print("-"*62)
    print("PASO 2 — Selecciona thormetalwork@gmail.com y haz click en Permitir")
    print("PASO 3 — El browser debería mostrar '¡Autorizado!' y cerrar")
    print("         (El servidor capturará el callback automáticamente)")
    print("-"*62)
    print("\nEsperando autorización (timeout: 3 minutos)...")

    thread.join(timeout=300)
    httpd.server_close()

    if not callback_result:
        print("\nERROR: Timeout esperando el callback. Vuelve a intentarlo.")
        sys.exit(1)

    if "error" in callback_result:
        print(f"\nERROR de Google: {callback_result['error']}")
        sys.exit(1)

    if "code" not in callback_result:
        print("\nERROR: No se recibió código de autorización.")
        sys.exit(1)

    # Verify state (CSRF protection)
    returned_state = callback_result.get("state", "")
    if returned_state != state:
        print("\nERROR: State no coincide — posible ataque CSRF. Abortando.")
        sys.exit(1)

    print("\nState verificado ✓. Intercambiando código por tokens...")

    try:
        tokens = exchange_code(callback_result["code"], client_id, client_secret)
    except Exception as e:
        print(f"\nERROR al intercambiar código: {e}")
        sys.exit(1)

    if "refresh_token" not in tokens:
        print(f"\nERROR: No hay refresh_token en la respuesta:\n{tokens}")
        sys.exit(1)

    new_refresh = tokens["refresh_token"]
    update_env(ENV_FILE, ENV_KEY, new_refresh)

    print(f"\n{'='*62}")
    print("  ÉXITO — Nuevo refresh token guardado en .env")
    print(f"  Token: {new_refresh[:30]}...")
    print(f"  Scopes: analytics, webmasters, business.manage, drive")
    print(f"{'='*62}\n")
    print("Siguiente paso — reiniciar WordPress para aplicar el nuevo token:")
    print("  cd /srv/stacks/thormetalart-dev && make restart\n")


if __name__ == "__main__":
    main()
