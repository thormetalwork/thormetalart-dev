#!/usr/bin/env python3
"""
TMA Dashboard — Facebook OAuth2 Token Generator
Generates a long-lived User Token + Page Token for Thor Metal Art.
Saves tokens to .env automatically.

Supports headless/remote servers: starts HTTP server BEFORE printing the URL
so VS Code Remote detects port 8765 and auto-forwards it.

Usage:
    python3 scripts/get-facebook-token.py

Scopes requested:
    - pages_show_list            → listar páginas administradas
    - pages_read_engagement      → leer posts, likes, comentarios
    - pages_manage_posts         → crear/editar/borrar posts y fotos
    - pages_manage_metadata      → editar info: website, teléfono, descripción,
                                   foto de perfil y portada
    - instagram_basic            → leer perfil e Instagram conectado (requiere app review)
    - instagram_manage_insights  → métricas de Instagram (requiere app review)
    - business_management        → acceso a Business Manager

PREREQUISITO (solo una vez):
    1. Ir a https://developers.facebook.com/apps/916139357694429/fb-login/settings/
    2. En "Valid OAuth Redirect URIs" agregar: http://localhost:8765/callback
    3. Guardar cambios
"""

import http.server
import json
import secrets
import sys
import threading
import urllib.parse
import urllib.request
from pathlib import Path

# ── Config ────────────────────────────────────────────────────────────────────

PORT         = 8765
REDIRECT_URI = f"http://localhost:{PORT}/callback"
PAGE_ID      = "1011719252033927"  # Thor Metal Art

SCOPES = [
    # Página — gestión completa
    "pages_show_list",
    "pages_read_engagement",
    "pages_manage_metadata",
    "pages_manage_posts",
    "pages_manage_engagement",
    "pages_read_user_content",
    "read_insights",
    # Instagram
    "instagram_basic",
    "instagram_manage_comments",
    "instagram_content_publish",
    "instagram_manage_messages",
    # Messenger
    "pages_messaging",
    # Leads
    "leads_retrieval",
    # Base
    "public_profile",
    "email",
]

# Scopes que requieren App Review + Verificación de negocio para acceso avanzado
# (funcionan en Standard Access para usuarios con rol en la app):
#   instagram_basic, instagram_content_publish → Standard Access disponible
#   whatsapp_business_messaging → requiere App Review
#   ads_management / ads_read   → requiere App Review
PENDING_ADVANCED = ["whatsapp_business_messaging", "ads_management", "ads_read"]

FB_API_VERSION = "v21.0"
AUTH_URL       = f"https://www.facebook.com/{FB_API_VERSION}/dialog/oauth"
TOKEN_URL      = f"https://graph.facebook.com/{FB_API_VERSION}/oauth/access_token"
EXTEND_URL     = f"https://graph.facebook.com/{FB_API_VERSION}/oauth/access_token"

ENV_FILE = Path(__file__).parent.parent / ".env"

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


def update_env(path: Path, updates: dict) -> None:
    lines = path.read_text().splitlines() if path.exists() else []
    for key, value in updates.items():
        updated = False
        for i, line in enumerate(lines):
            stripped = line.strip()
            if stripped.startswith(f"{key}=") or stripped.startswith(f"{key} ="):
                lines[i] = f"{key}={value}"
                updated = True
                break
        if not updated:
            lines.append(f"{key}={value}")
    path.write_text("\n".join(lines) + "\n")


def fb_get(url: str) -> dict:
    try:
        with urllib.request.urlopen(url, timeout=15) as resp:
            return json.loads(resp.read())
    except urllib.error.HTTPError as e:
        body = e.read().decode("utf-8", errors="replace")
        raise RuntimeError(f"HTTP {e.code}: {body}") from e


def fb_post(url: str, data: dict) -> dict:
    encoded = urllib.parse.urlencode(data).encode()
    req = urllib.request.Request(url, data=encoded, method="POST")
    req.add_header("Content-Type", "application/x-www-form-urlencoded")
    with urllib.request.urlopen(req, timeout=15) as resp:
        return json.loads(resp.read())


def exchange_code(code: str, client_id: str, client_secret: str) -> dict:
    """Short-lived user token (1 hora)."""
    params = urllib.parse.urlencode({
        "client_id":     client_id,
        "client_secret": client_secret,
        "redirect_uri":  REDIRECT_URI,
        "code":          code,
    })
    return fb_get(f"{TOKEN_URL}?{params}")


def extend_token(short_token: str, client_id: str, client_secret: str) -> dict:
    """Long-lived user token (60 días)."""
    params = urllib.parse.urlencode({
        "grant_type":       "fb_exchange_token",
        "client_id":        client_id,
        "client_secret":    client_secret,
        "fb_exchange_token": short_token,
    })
    return fb_get(f"{EXTEND_URL}?{params}")


def get_page_token(long_lived_user_token: str, page_id: str) -> str:
    """Page token de larga duración (no expira mientras la página exista)."""
    data = fb_get(
        f"https://graph.facebook.com/{FB_API_VERSION}/{page_id}"
        f"?fields=access_token&access_token={long_lived_user_token}"
    )
    return data.get("access_token", "")


# ── Callback server ───────────────────────────────────────────────────────────

callback_result: dict = {}
server_ready = threading.Event()


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
            body = (
                b"<html><body style='font-family:sans-serif;text-align:center;padding:60px'>"
                b"<h2 style='color:#1877f2'>&#10003; Facebook autorizado. Puedes cerrar esta ventana.</h2>"
                b"</body></html>"
            )
        else:
            error = params.get("error_description", params.get("error", "Unknown error"))
            body = (
                f"<html><body style='font-family:sans-serif;text-align:center;padding:60px'>"
                f"<h2 style='color:#c0392b'>Error: {error}</h2></body></html>"
            ).encode()
        self.wfile.write(body)


def run_server(httpd):
    server_ready.set()
    httpd.handle_request()  # una sola request, luego para


# ── Main ──────────────────────────────────────────────────────────────────────

def process_short_token(short_token: str, app_id: str, app_secret: str) -> None:
    """Recibe un token corto (del Graph API Explorer), lo extiende y guarda en .env."""
    print("✓ Token corto recibido. Extendiendo a long-lived...")

    try:
        long = extend_token(short_token, app_id, app_secret)
    except Exception as e:
        print(f"ERROR al extender token: {e}")
        sys.exit(1)

    long_token = long.get("access_token", "")
    if not long_token:
        print(f"ERROR: No se pudo extender el token: {long}")
        sys.exit(1)

    long_expires = long.get("expires_in", "?")
    print(f"✓ Token largo obtenido (expira en {long_expires}s ≈ 60 días)")

    try:
        page_token = get_page_token(long_token, PAGE_ID)
    except Exception as e:
        print(f"ADVERTENCIA: No se pudo obtener page token: {e}")
        page_token = ""

    if page_token:
        print("✓ Page token de Thor Metal Art obtenido (no expira)")
    else:
        print("⚠  No se obtuvo page token.")

    updates = {"FB_USER_TOKEN": long_token}
    if page_token:
        updates["FB_PAGE_TOKEN_TMA"] = page_token
    update_env(ENV_FILE, updates)

    print("\n" + "=" * 60)
    print("  ✓ Tokens guardados en .env")
    print("=" * 60)
    print(f"\n  FB_USER_TOKEN     = {long_token[:40]}...")
    if page_token:
        print(f"  FB_PAGE_TOKEN_TMA = {page_token[:40]}...")

    try:
        env_fresh = load_env(ENV_FILE)
        app_id_check  = env_fresh.get("FB_APP_ID", "")
        app_sec_check = env_fresh.get("FB_APP_SECRET", "")
        debug_url = (
            f"https://graph.facebook.com/{FB_API_VERSION}/debug_token"
            f"?input_token={long_token}&access_token={app_id_check}|{app_sec_check}"
        )
        debug = fb_get(debug_url)
        data = debug.get("data", {})
        # granular_scopes = permisos realmente concedidos por el usuario
        granted = {gs["scope"] for gs in data.get("granular_scopes", [])}
        all_scopes = data.get("scopes", [])
        print("\nScopes concedidos (✓) vs disponibles en la app ( ):")
        for s in all_scopes:
            mark = "✓" if s in granted else " "
            print(f"  [{mark}] {s}")
        # Scopes solicitados no presentes en la app en absoluto
        missing_from_app = [s for s in SCOPES if s not in all_scopes and s not in granted]
        if missing_from_app:
            print("\n⚠  Scopes solicitados NO disponibles en la app:")
            for s in missing_from_app:
                print(f"      {s}")
        if "pages_manage_posts" in granted:
            print("\n✓ pages_manage_posts concedido — puedes crear posts!")
        else:
            print("\n⚠  pages_manage_posts ausente en granular_scopes.")
    except Exception as e:
        print(f"(No se pudo verificar scopes: {e})")
    print()


def main():
    env = load_env(ENV_FILE)
    app_id     = env.get("FB_APP_ID", "")
    app_secret = env.get("FB_APP_SECRET", "")

    if not app_id or not app_secret:
        print("ERROR: Faltan FB_APP_ID o FB_APP_SECRET en .env")
        sys.exit(1)

    # Modo: recibir token corto externo (del Graph API Explorer)
    if len(sys.argv) > 1 and sys.argv[1] == "--from-token":
        if len(sys.argv) > 2:
            short_token = sys.argv[2]
        else:
            try:
                short_token = input("Pega el token corto del Graph API Explorer: ").strip()
            except (EOFError, KeyboardInterrupt):
                print("\nCancelado.")
                sys.exit(1)
        if not short_token:
            print("ERROR: Token vacío.")
            sys.exit(1)
        process_short_token(short_token, app_id, app_secret)
        return

    # Generar state CSRF
    state = secrets.token_urlsafe(32)

    # Construir URL de autorización
    auth_params = urllib.parse.urlencode({
        "client_id":     app_id,
        "redirect_uri":  REDIRECT_URI,
        "scope":         ",".join(SCOPES),
        "state":         state,
        "response_type": "code",
    })
    auth_url = f"{AUTH_URL}?{auth_params}"

    # Intentar iniciar servidor HTTP (funciona si VS Code hace port forward)
    server_available = False
    try:
        httpd = http.server.HTTPServer(("0.0.0.0", PORT), CallbackHandler)
        thread = threading.Thread(target=run_server, args=(httpd,), daemon=True)
        thread.start()
        server_ready.wait(timeout=3)
        server_available = True
    except OSError:
        server_available = False

    print("\n" + "=" * 60)
    print("  TMA — Facebook OAuth2 Token Generator")
    print("=" * 60)
    print("\n1. Abre esta URL en el navegador:")
    print(f"\n   {auth_url}\n")
    print("2. Acepta todos los permisos en Facebook.")
    print("3. El navegador intentará abrir localhost:8765 y puede mostrar")
    print("   'Esta página no funciona' — eso es NORMAL.")
    print("4. Copia la URL COMPLETA de la barra de dirección del navegador")
    print("   (empieza con http://localhost:8765/callback?code=...)")
    print("5. Pégala aquí y presiona Enter.\n")

    # Esperar callback del servidor (si VS Code hizo port forward) O input manual
    pasted_url = None

    if server_available:
        # Esperar hasta 5 minutos para que llegue el callback automático
        print("⏳ Esperando callback (hasta 5 minutos)...\n")
        thread.join(timeout=300)

    if callback_result.get("code"):
        # El servidor capturó el callback automáticamente
        print("✓ Callback recibido automáticamente.")
    else:
        # Modo copy-paste fallback
        print("\nSi el navegador mostró 'Esta página no funciona',")
        print("copia la URL completa de la barra de dirección y pégala aquí.")
        print("(Si ya ves '✓ Facebook autorizado', presiona Enter sin pegar nada para reintentar)\n")
        try:
            pasted_url = input("Pega la URL aquí (o Enter para cancelar): ").strip()
        except (EOFError, KeyboardInterrupt):
            print("\nCancelado.")
            sys.exit(1)

        if not pasted_url:
            print("Cancelado.")
            sys.exit(1)

        parsed = urllib.parse.urlparse(pasted_url)
        params = dict(urllib.parse.parse_qsl(parsed.query))
        callback_result.update(params)

    if "error" in callback_result:
        print(f"ERROR de autorización: {callback_result.get('error_description', callback_result['error'])}")
        sys.exit(1)

    if not callback_result.get("code"):
        print("ERROR: No se encontró el código de autorización en la URL.")
        sys.exit(1)

    # Verificar state (CSRF)
    if callback_result.get("state") != state:
        print("ERROR CRÍTICO: State CSRF inválido. Posible ataque CSRF. Abortando.")
        sys.exit(1)

    code = callback_result["code"]
    print("✓ Código recibido. Intercambiando por tokens...")

    # 1. Token de corta duración
    try:
        short = exchange_code(code, app_id, app_secret)
    except Exception as e:
        print(f"ERROR al intercambiar código: {e}")
        sys.exit(1)

    short_token = short.get("access_token", "")
    if not short_token:
        print(f"ERROR: No se recibió access_token: {short}")
        sys.exit(1)
    print(f"✓ Token corto obtenido (expira en {short.get('expires_in', '?')}s)")

    # 2. Token de larga duración (60 días)
    try:
        long = extend_token(short_token, app_id, app_secret)
    except Exception as e:
        print(f"ERROR al extender token: {e}")
        sys.exit(1)

    long_token = long.get("access_token", "")
    if not long_token:
        print(f"ERROR: No se pudo extender el token: {long}")
        sys.exit(1)
    long_expires = long.get("expires_in", "?")
    print(f"✓ Token largo obtenido (expira en {long_expires}s ≈ 60 días)")

    # 3. Page token (no expira)
    try:
        page_token = get_page_token(long_token, PAGE_ID)
    except Exception as e:
        print(f"ADVERTENCIA: No se pudo obtener page token: {e}")
        page_token = ""

    if page_token:
        print(f"✓ Page token de Thor Metal Art obtenido (no expira)")
    else:
        print("⚠  No se obtuvo page token. Verifica que la página esté vinculada.")

    # 4. Guardar en .env
    updates = {
        "FB_USER_TOKEN": long_token,
    }
    if page_token:
        updates["FB_PAGE_TOKEN_TMA"] = page_token

    update_env(ENV_FILE, updates)

    print("\n" + "=" * 60)
    print("  ✓ Tokens guardados en .env")
    print("=" * 60)
    print(f"\n  FB_USER_TOKEN        = {long_token[:40]}...")
    if page_token:
        print(f"  FB_PAGE_TOKEN_TMA    = {page_token[:40]}...")
    print()

    # 5. Verificar scopes obtenidos
    try:
        debug_url = (
            f"https://graph.facebook.com/{FB_API_VERSION}/debug_token"
            f"?input_token={page_token or long_token}&access_token={long_token}"
        )
        debug = fb_get(debug_url)
        granted_scopes = debug.get("data", {}).get("scopes", [])
        print("Scopes concedidos:")
        for s in granted_scopes:
            mark = "✓" if s in SCOPES else " "
            print(f"  [{mark}] {s}")

        missing = [s for s in ["pages_manage_posts", "pages_manage_metadata"] if s not in granted_scopes]
        if missing:
            print(f"\n⚠  Faltan scopes críticos: {missing}")
            print("   En Graph API Explorer, agrega esos permisos y regenera el token.")
        else:
            print("\n✓ Todos los scopes de gestión están presentes. ¡Listo!")

        if "instagram_basic" in granted_scopes:
            print("✓ Instagram también disponible!")
        else:
            print("⚠  instagram_basic ausente — requiere App Review de Meta.")

    except Exception as e:
        print(f"(No se pudo verificar scopes: {e})")

    print()


if __name__ == "__main__":
    main()
