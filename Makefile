## Project Makefile for Symfony Price Service in Docker

# Docker container names
APP_CONTAINER=price_service_app
REDIS_CONTAINER=price_service_redis

# Use docker exec to run commands inside containers
DOCKER_APP_EXEC = docker exec -it $(APP_CONTAINER)
DOCKER_REDIS_EXEC = docker exec -it $(REDIS_CONTAINER)

.PHONY: help up db-create db-migrate fixtures db-prepare start-scheduler stop-scheduler fetch-queue process-queue queues flush-redis

help:
	@echo "available targets:"
	@echo "  up                - Build and start all containers"
	@echo "  db-create         - Create the database if it does not exist"
	@echo "  db-migrate        - Run doctrine migrations"
	@echo "  fixtures          - Load doctrine fixtures"
	@echo "  db-prepare        - Run db-create, db-migrate, and fixtures"
	@echo "  start-scheduler   - Start the scheduler consumer (detached)"
	@echo "  stop-scheduler    - Stop all messenger workers gracefully"
	@echo "  fetch-queue       - Consume the fetch_transport queue"
	@echo "  process-queue     - Consume the process_transport queue"
	@echo "  queues            - Start fetch-queue and process-queue sequentially"
	@echo "  flush-redis       - Flush all data from Redis database"

up:
	docker-compose up -d --build

# Database commands
db-create:
	@echo "Creating database..."
	$(DOCKER_APP_EXEC) bin/console doctrine:database:create --if-not-exists

db-migrate:
	@echo "Running migrations..."
	$(DOCKER_APP_EXEC) bin/console doctrine:migrations:migrate --no-interaction

fixtures:
	@echo "Dropping schema..."
	$(DOCKER_APP_EXEC) bin/console doctrine:schema:drop --force --no-interaction

	@echo "Creating schema..."
	$(DOCKER_APP_EXEC) bin/console doctrine:schema:create --no-interaction

	@echo "Loading fixtures..."
	$(DOCKER_APP_EXEC) bin/console doctrine:fixtures:load --no-interaction

# Combined target to prepare the database from scratch
db-prepare: db-create db-migrate fixtures
	@echo "Database prepared (create, migrate, fixtures)."

# Scheduler control
start-scheduler:
	@echo "Starting scheduler consumer (foreground with logs)..."
	$(DOCKER_APP_EXEC) bin/console messenger:consume scheduler_fetch_prices -vv

stop-scheduler:
	@echo "Stopping messenger workers gracefully..."
	$(DOCKER_APP_EXEC) bin/console messenger:stop-workers

# Queue consumers
fetch-queue:
	@echo "Consuming fetch_transport queue..."
	$(DOCKER_APP_EXEC) bin/console messenger:consume fetch_transport -vv

process-queue:
	@echo "Consuming process_transport queue..."
	$(DOCKER_APP_EXEC) bin/console messenger:consume process_transport -vv

queues: fetch-queue process-queue
	@echo "All queues started (run each in separate terminals if desired)."

# Redis maintenance
flush-redis:
	@echo "Flushing all Redis data..."
	$(DOCKER_REDIS_EXEC) redis-cli FLUSHDB

list-scheduler:
	@echo "Listing all recurring scheduled messages..."
	$(DOCKER_APP_EXEC) bin/console debug:scheduler

test:
	@echo "Running PHPUnit tests..."
	$(DOCKER_APP_EXEC) php bin/phpunit --colors=always

