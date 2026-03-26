.PHONY: up down restart logs build status backup restore clean

up:
	docker compose up -d

down:
	docker compose down

restart:
	docker compose down && docker compose up -d

build:
	docker compose build --no-cache && docker compose up -d

logs:
	docker compose logs -f --tail=100

logs-wp:
	docker compose logs -f --tail=100 wordpress

logs-mysql:
	docker compose logs -f --tail=100 mysql

status:
	docker compose ps

backup:
	bash scripts/backup-database.sh

restore:
	@echo 'Uso: bash scripts/restore-database.sh archivo.sql.gz'

clean:
	docker compose down -v --remove-orphans

shell-wp:
	docker exec -it thormetalart_wordpress bash

shell-mysql:
	docker exec -it thormetalart_mysql mysql -u root -p

test:
	bash scripts/test-connections.sh
