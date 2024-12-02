up: stop
	docker-compose up -d
down:
	docker-compose down
start:
	docker-compose start
stop:
	docker-compose stop
build:
	docker-compose build
php-stan:
	./vendor/bin/phpstan analyse -c config/external/phpstan.neon --no-interaction --no-progress