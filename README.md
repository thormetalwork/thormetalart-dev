# Thor Metal Art — Stack Docker

Custom Metal Fabrication & Artistic Metalwork — Miami, FL

## Stack

| Servicio | Container | Puerto | Límite RAM |
|---|---|---|---|
| MySQL 8.0 | tma_dev_mysql | 127.0.0.1:3311 | 512MB |
| Redis 7 | tma_dev_redis | 127.0.0.1:6379 | 128MB |
| WordPress 6.9 | tma_dev_wordpress | via Traefik | 512MB |
| phpMyAdmin 5.2 | tma_dev_phpmyadmin | via Traefik (BasicAuth) | 256MB |

## Quick Start

```bash
cp .env.example .env    # Editar con credenciales reales
make build              # Build + up
make status             # Ver estado
make test               # Test conexiones
```

## URLs (dev)

- **WordPress:** https://dev.thormetalart.com
- **Panel Cliente:** https://panel-dev.thormetalart.com
- **phpMyAdmin:** https://pma-dev.thormetalart.com (protegido con BasicAuth)

## Comandos — Stack

```bash
make up          # Levantar stack
make down        # Bajar stack
make restart     # Restart completo
make build       # Rebuild sin cache
make backup      # Backup base de datos
make logs-wp     # Logs WordPress
make shell-wp    # Shell en WordPress
make shell-mysql # Shell MySQL
make test        # Test conexiones
```

## Comandos — QA & Testing

```bash
make test-all      # Ejecutar los 23 test suites
make test-panel    # Solo tests del panel plugin
make test-dash     # Solo tests del dashboard
make test-lead     # Solo tests de leads
make lint          # PHP lint + ESLint + PHPCS
make lint-phpstan  # PHPStan analisis estatico
make format        # Auto-fix formato (Prettier)
make fix           # Auto-fix todo (Prettier + ESLint + PHPCBF)
```

## QA Tools

| Herramienta | Config |
|---|---|
| ESLint 9 | `eslint.config.mjs` |
| Prettier | `.prettierrc` |
| PHPCS + WPCS | `.phpcs.xml` |
| PHPStan (level 5) | `phpstan.neon` |
| Husky pre-commit | `.husky/pre-commit` |
| GitHub Actions CI | `.github/workflows/ci.yml` |

## Security

- Redis autenticado (requiere `REDIS_PASSWORD` en `.env`)
- phpMyAdmin protegido con BasicAuth via Traefik
- MySQL solo accesible en `127.0.0.1:3311`
- PHP hardening: `disable_functions`, `expose_php = Off`, cookies seguras
- WordPress: `DISALLOW_FILE_EDIT`, `AUTOMATIC_UPDATER_DISABLED`
- Login rate-limiting: 5 intentos / 15 min con `wp_hash()` + `X-Forwarded-For`
- REST API restringida a usuarios autenticados
- XML-RPC deshabilitado
- Salts/keys via variables de entorno (nunca en código)
- `WP_DEBUG` controlado por env var, display desactivado
