.PHONY: dev-up dev-down prod-up prod-down prod-build bash migrate

# Development commands
dev-up:
	docker compose up -d

dev-down:
	docker compose down

bash:
	docker compose exec app bash

migrate:
	docker compose exec app php artisan migrate

# Production commands
prod-build:
	docker compose -f docker-compose.prod.yml build

prod-up:
	docker compose -f docker-compose.prod.yml up -d

prod-down:
	docker compose -f docker-compose.prod.yml down

prod-bash:
	docker compose -f docker-compose.prod.yml exec app bash

prod-migrate:
	docker compose -f docker-compose.prod.yml exec app php artisan migrate --force
