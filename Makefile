# Variables
DOCKER = docker
DOCKER_COMPOSE = docker-compose
EXEC = $(DOCKER) exec -w /var/www/project www_projet_blog
PHP = $(EXEC) php
COMPOSER = $(EXEC) composer
NPM = $(EXEC) npm
SYMFONY_CONSOLE = $(PHP) bin/console

# Colors
GREEN = /bin/echo -e "\x1b[32m\#\# $1\x1b[0m"
RED = /bin/echo -e "\x1b[31m\#\# $1\x1b[0m"

init: 
	$(MAKE) start
	$(MAKE) composer-install
	$(MAKE) npm-install
	@$(call GREEN,"The application is available at: http://127.0.0.1:8000/.")

cache-clear: 
	$(SYMFONY_CONSOLE) cache:clear

## —— Docker ——
start: 

	$(MAKE) docker-start 
docker-start: 
	$(DOCKER_COMPOSE) up -d

stop: 
	$(MAKE) docker-stop

docker-stop: 
	$(DOCKER_COMPOSE) stop
	@$(call RED,"The containers are now stopped.")

## —— Composer ——
composer-install: 
	$(COMPOSER) install

composer-update: 
	$(COMPOSER) update

## ——  NPM ——
npm-install: 
	$(NPM) install

npm-update:
	$(NPM) update

npm-watch: 
	$(NPM) run watch

## —— Database ——
database-init: 
	$(MAKE) database-drop
	$(MAKE) database-create
	$(MAKE) database-migrate

database-drop: 
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists

database-create: 
	$(SYMFONY_CONSOLE) d:d:c --if-not-exists

database-remove: 
	$(SYMFONY_CONSOLE) d:d:d --force --if-exists

database-migration: 
	$(SYMFONY_CONSOLE) make:migration

migration: 
	$(MAKE) database-migration

database-migrate: 
	$(SYMFONY_CONSOLE) d:m:m --no-interaction

migrate: 
	$(MAKE) database-migrate

database-fixtures-load:
	$(SYMFONY_CONSOLE) d:f:l --no-interaction

fixtures: 
	$(MAKE) database-fixtures-load

help: ## List of commands
	@grep -E '(^[a-zA-Z0-9_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}{printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
