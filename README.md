# Thor Metal Art — Stack Docker

Custom Metal Fabrication & Artistic Metalwork — Miami, FL

## Stack

| Servicio | Container | Puerto |
|---|---|---|
| MySQL 8.0 | thormetalart_mysql | 127.0.0.1:3311 |
| Redis 7 | thormetalart_redis | interno |
| WordPress 6.9 | thormetalart_wordpress | via Traefik |
| phpMyAdmin | thormetalart_phpmyadmin | via Traefik |
| Dashboard | thormetalart_dashboard | via Traefik |

## Quick Start

`ash
cp .env.example .env    # Editar con credenciales reales
make build              # Build + up
make status             # Ver estado
make logs               # Ver logs en vivo
`

## URLs (dev)

- **WordPress:** dev.thormetalart.com
- **phpMyAdmin:** pma-thormetalart.server-dev
- **Dashboard:** dashboard.thormetalart.server-dev

## Comandos

`ash
make up          # Levantar stack
make down        # Bajar stack
make restart     # Restart completo
make backup      # Backup base de datos
make logs-wp     # Logs WordPress
make shell-wp    # Shell en WordPress
make shell-mysql # Shell MySQL
make test        # Test conexiones
`
