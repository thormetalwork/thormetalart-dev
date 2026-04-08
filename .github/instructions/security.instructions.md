---
description: "Security-by-design rules for all code generation. Covers OWASP Top 10, input validation, output encoding, authentication, and infrastructure hardening."
applyTo: ["**/*.php", "**/*.js", "**/*.sh", "docker-compose.yml", "docker/**", "scripts/**"]
---
# Security Instructions — Thor Metal Art

## OWASP Top 10 Compliance

### A01 — Broken Access Control
- Every REST endpoint MUST verify `current_user_can()` or custom capability
- Use WordPress nonces (`wp_verify_nonce()`) for all state-changing operations
- Never trust client-provided user IDs — validate against `get_current_user_id()`

### A02 — Cryptographic Failures
- Secrets (passwords, keys, tokens) MUST come from environment variables
- Never hardcode credentials, API keys, or salts in source code
- Use `wp_hash_password()` / `wp_check_password()` — never `md5()` or `sha1()`

### A03 — Injection
- **SQL:** Always use `$wpdb->prepare()` with placeholders (`%s`, `%d`)
- **XSS:** Escape all output — `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`
- **Command:** Never pass user input to `exec()`, `shell_exec()`, `system()`
- **JavaScript:** Never use `innerHTML` with unsanitized data — use `textContent` or sanitize server-side with `wp_kses_post()`

### A04 — Insecure Design
- Rate-limit login and API endpoints (use `$_SERVER['HTTP_X_FORWARDED_FOR']` behind Traefik, not `REMOTE_ADDR`)
- Implement audit logging for sensitive operations
- Design with least privilege — plugins get only needed capabilities

### A05 — Security Misconfiguration
- `WP_DEBUG` must default to `0` (controlled via env var `WORDPRESS_DEBUG`)
- `WP_DEBUG_DISPLAY` must be `false` — log errors, never display
- Redis MUST require authentication (`--requirepass`)
- phpMyAdmin MUST be behind BasicAuth + HTTPS

### A07 — Authentication Failures
- Enforce strong passwords via WordPress policy
- Session tokens managed by WordPress — never custom sessions
- All admin panels require authenticated WordPress session + nonce

### A08 — Data Integrity
- Validate all input at system boundaries (REST endpoints, form handlers)
- Use `sanitize_text_field()`, `absint()`, `sanitize_email()`, `wp_kses()`
- Validate MIME types for file uploads — never trust extensions alone

### A09 — Logging & Monitoring
- Log security events to `wp-content/debug.log` (never expose to users)
- Audit trail for lead status changes, document approvals, settings changes
- Never log passwords, tokens, or PII

## PHP Patterns

```php
// ✅ Correct: prepared query
$wpdb->get_results( $wpdb->prepare(
    "SELECT * FROM {$wpdb->prefix}panel_leads WHERE status = %s",
    $status
) );

// ❌ Wrong: direct interpolation
$wpdb->get_results( "SELECT * FROM ... WHERE status = '$status'" );

// ✅ Correct: escaped output
echo esc_html( $lead->name );

// ❌ Wrong: raw output
echo $lead->name;

// ✅ Correct: nonce verification
if ( ! wp_verify_nonce( $_POST['_tma_nonce'], 'tma_action' ) ) {
    wp_die( 'Security check failed.' );
}
```

## JavaScript Patterns

```javascript
// ✅ Correct: textContent for user data
element.textContent = userData;

// ✅ Correct: server-sanitized HTML (wp_kses_post)
container.innerHTML = sanitizedHtmlFromServer;

// ❌ Wrong: raw user input in innerHTML
container.innerHTML = userInput;

// ✅ Correct: URL encoding for API calls
fetch(`${apiBase}/documents/${encodeURIComponent(code)}/content`);
```

## Shell Script Patterns

```bash
# ✅ Correct: quote all variables
docker exec tma_dev_redis redis-cli -a "$REDIS_PASSWORD" --no-auth-warning FLUSHDB

# ❌ Wrong: unquoted variables, FLUSHALL
docker exec tma_dev_redis redis-cli FLUSHALL

# ✅ Correct: validate .env before operations
if [[ ! -f "$ENV_FILE" ]]; then
    echo "ERROR: .env not found" >&2; exit 1
fi
```

## Docker / Infrastructure

- All services MUST have health checks
- Database ports: bind to `127.0.0.1` only (never `0.0.0.0`)
- Use `MYSQL_PWD` env var instead of `-p$PASSWORD` in commands
- Set resource limits (`mem_limit`, `cpus`) for all services
- Redis: use `FLUSHDB` (current database), never `FLUSHALL` (all databases)
