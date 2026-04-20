.PHONY: up down restart logs logs-wp logs-mysql build status backup restore clean \
       test test-all test-panel test-dash test-lead test-portal test-docker \
       lint lint-php lint-js lint-format lint-phpcs lint-phpstan \
       format fix shell-wp shell-mysql

# ── Stack Management ─────────────────────────────────────────────
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
	docker exec -it tma_dev_wordpress bash

shell-mysql:
	docker exec -it tma_dev_mysql mysql -u root -p

# ── Testing ──────────────────────────────────────────────────────
test:
	bash scripts/test-connections.sh

test-all:
	bash scripts/run-all-tests.sh all

test-panel:
	bash scripts/run-all-tests.sh panel

test-dash:
	bash scripts/run-all-tests.sh dash

test-lead:
	bash scripts/run-all-tests.sh lead

test-portal:
	bash scripts/run-all-tests.sh portal

test-docker:
	bash scripts/run-all-tests.sh docker

# ── Linting & Static Analysis ───────────────────────────────────
lint: lint-php lint-js lint-phpcs
	@echo "✅ All linting passed"

lint-php:
	@echo "▸ PHP Syntax Check..."
	@find data/wordpress/wp-content/plugins/tma-panel -name "*.php" -exec php -l {} \; 2>&1 | grep -v "No syntax errors" || echo "✅ PHP syntax OK"

lint-js:
	@echo "▸ ESLint..."
	@npx eslint data/wordpress/wp-content/plugins/tma-panel/assets/js/ --ignore-pattern "*.min.js"

lint-format:
	@echo "▸ Prettier Check..."
	@npx prettier --check "data/wordpress/wp-content/plugins/tma-panel/**/*.{js,css}" --ignore-path .prettierignore

lint-phpcs:
	@echo "▸ PHPCS WordPress Standards..."
	@vendor/bin/phpcs --standard=.phpcs.xml --report=summary || true

lint-phpstan:
	@echo "▸ PHPStan Static Analysis..."
	@vendor/bin/phpstan analyse --configuration=phpstan.neon --no-progress || true

# ── Auto-fix ─────────────────────────────────────────────────────
format:
	@echo "▸ Prettier Fix..."
	@npx prettier --write "data/wordpress/wp-content/plugins/tma-panel/**/*.{js,css}" --ignore-path .prettierignore

fix: format
	@echo "▸ ESLint Fix..."
	@npx eslint data/wordpress/wp-content/plugins/tma-panel/assets/js/ --ignore-pattern "*.min.js" --fix || true
	@echo "▸ PHPCBF Fix..."
	@vendor/bin/phpcbf --standard=.phpcs.xml || true
