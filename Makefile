reset:
	php artisan migrate:fresh --seed
migrate:
	php artisan migrate
pint:
	./vendor/bin/pint --parallel
up:
	docker compose up -d
down:
	docker compose down