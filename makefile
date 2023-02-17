SHELL           := /bin/bash
.DEFAULT_GOAL   := help
.SILENT:        #don't echo commands as they run
# ==============================================================================
-include .env
DIR             := ${CURDIR}
UNAME           := `uname`
UUID            := `id -u`
GUID            := `id -g`

ifeq (Darwin, $(findstring Darwin, $(shell uname -a)))
    PLATFORM    := OSX
    OPEN        := open
else
    PLATFORM    := Linux
    OPEN        := xdg-open
endif

# Declare Variables
ifdef APP_ENV
    ENV ?= ${APP_ENV}
else
    ENV ?= "local"
endif

ifneq ("$(wildcard docker-compose.dev.yaml)","")
    DEV_COMPOSE_EXISTS = 1
else
    DEV_COMPOSE_EXISTS = 0
endif

# App commands
app_container := app
docker := docker
ifeq ($(PLATFORM), Linux)
	ifeq ($(DEV_COMPOSE_EXISTS), 1)
		docker_compose := USER_UID=$(UUID) USER_GID=$(GUID) docker-compose -f docker-compose.yaml -f docker-compose.dev.yaml
	else
		docker_compose := USER_UID=$(UUID) USER_GID=$(GUID) docker-compose -f docker-compose.yaml
	endif
endif
ifeq ($(PLATFORM), OSX)
	ifeq ($(DEV_COMPOSE_EXISTS), 1)
		docker_compose := docker-compose -f docker-compose.yaml -f docker-compose.dev.yaml
	else
		docker_compose := docker-compose -f docker-compose.yaml
	endif
endif
docker_exec := $(docker_compose) exec
docker_exec_app := $(docker_exec) $(app_container)
php_artisan := php artisan

.PHONY: help
help:   # List helpful commands
	echo ''
	echo 'Makefile for '
	echo ' make help                        show this information'
	echo ' make help:commands               list common/quick commands'
	echo ' make help:composer               list composer:* commands'
	echo ' make help:db                     list db:* commands'
	echo ' make help:docker                 list docker:* commands'
	echo ' make help:env                    list env:* commands'
	echo ' make help:queue                  list queue:* commands'
	echo ' make help:setup                  list setup:* commands'
	echo ' make help:cs                     list cs:* commands'
	echo ' make help:test                   list test:* commands'
	echo ''

help\:composer:
	echo '## make composer:* commands -'
	echo ' make composer:dump               run composer dump-autoload'
	echo ' make composer:install            run composer install'
	echo ' make composer:update             run composer update'
	echo ''

help\:db:
	echo '## make db:* commands -'
	echo ' make db:migrate                  run DB migrations'
	echo ' make db:refresh                  run DB refresh'
	echo ' make db:refresh:seed             run DB refresh and seed'
	echo ' make db:reset                    run DB reset'
	echo ' make db:rollback                 run DB migration rollback'
	echo ' make db:seed                     seed the DB with data'
	echo ' make db:status                   show DB Status'
	echo ' make db:wipe                     wipe the DB'
	echo ''

help\:docker:
	echo '## make docker:* commands -'
	echo ' make docker:build                build the docker containers'
	echo ' make docker:down                 spin down the docker containers'
	echo ' make docker:ps                   show the docker containers'
	echo ' make docker:rebuild              rebuild the docker containers'
	echo ' make docker:restart              restart the docker containers'
	echo ' make docker:start                spin up the docker containers'
	echo ' make docker:stop                 spin down the docker containers'
	echo ' make docker:up                   spin up the docker containers'
	echo ''

help\:env:
	echo '## make env:* commands -'
	echo ' make env:clean                   delete .env file'
	echo ' make env:setup                   create .env file if it doesnt exist'
	echo ' make env:reset                   Recreate .env file from example'
	echo ''

help\:queue:
	echo '## make queue:* commands -'
	echo ' make queue:run                   run the queue task until its empty'
	echo ' make queue:list                  start the queue listener and run until stopped'
	echo ''

help\:test:
	echo '## make test:* commands -'
	echo ' make test:php                    run PHPUnit Test checks'
	echo ''

help\:cs:
	echo '## make cs:* commands -'
	echo ' make cs:phpcs                    run PHPCS code standards checks'
	echo ' make cs:phpcsf                   run PHP-CS-Fixer code standards checks'
	echo ' make cs:phpstan                  run the PHPStan static analysis tool'
	echo ' make cs:phpcbf                   run PHPCBF to fix PHPCS errors where possible'
	echo ''

help\:commands:
	echo '## Common make commands:'
	echo ' make cli                        terminal to execute backend commands'
	echo ' make setup                      alias for setup:app'
	echo ' make start                      quick start of the app'
	echo ' make stop                       quick shutdown of the app'
	echo ' make lint                       run codestandards'
	echo ''

# COMAMNDS
cli:
	$(docker_exec_app) /bin/bash
setup:
	sudo chmod -R 777 storage/ \
		&& make env\:setup \
		docker\:build \
		docker\:up \
		&& $(docker_exec_app) php artisan storage:link \
		&& $(docker_exec_app) php artisan key:generate \
		&& echo -e "✅ App is Ready\n" \

start:
	sudo chmod -R 777 storage/ \
		&& make docker\:up \
		&& echo -e "✅ App is Ready\n" \

stop:
	make docker\:down \
		&& echo -e "✅ App has been shutdown\n" \

# COMPOSER
composer\:dump:
	$(docker_exec_app) composer dump-autoload
composer\:help:
	make help:composer
composer\:install:
	$(docker_exec_app) composer install
composer\:update:
	$(docker_exec_app) composer update

# DB
db\:help:
	make help:db
db\:migrate:
	$(docker_exec_app) php artisan migrate
db\:refresh:
	$(docker_exec_app) php artisan migrate:fresh
db\:refresh\:seed:
	$(docker_exec_app) php artisan migrate:fresh \
	&& make db:seed
db\:reset:
	$(docker_exec_app) php artisan migrate:reset
db\:rollback:
	$(docker_exec_app) php artisan migrate:rollback
db\:seed:
	$(docker_exec_app) php artisan db:seed
db\:status:
	$(docker_exec_app) php artisan migrate:status
db\:wipe:
	$(docker_exec_app) php artisan db:wipe


# DOCKER
docker\:build:
	$(docker_compose) build
docker\:down:
	$(docker_compose) down
docker\:help:
	make help:docker
docker\:ps:
	$(docker_compose) ps
docker\:rebuild:
	make docker\:down \
		docker\:setup \
		db\:refresh\:seed
docker\:restart:
	make docker\:down docker\:up
docker\:start:
	make docker\:up
docker\:setup:
	sudo chmod -R 777 storage/ \
    && sudo chmod -R 777 hooks/ \
	&& make env\:reset \
		docker\:build \
		docker\:up \
		env\:key \
		db\:refresh:seed \
	&& echo -e "👻 Setup Complete, restarting containers" \
	&& make docker:restart \
	&& echo -e "✅ App is Ready\n" \

docker\:stop:
	make docker\:down
docker\:up:
	$(docker_compose) up -d

# ENV
env\:help:
	make help:env

env\:clean:
	if [ -f .env ]; then \
		rm .env; \
	fi; \

env\:setup:
	echo "Creating '.env' file (if it does not exist)"; \
	if [ ! -f .env ]; then \
		cp .env.example .env; \
	fi; \
	chmod 664 .env; \

env\:reset:
	make env\:clean \
		env\:setup
env\:key:
	$(docker_exec_app) php artisan key:generate

# CODESTANDARDS
# PHPCS
cs\:phpcs:
	$(docker_exec_app) vendor/bin/phpcs

# PHP CS Fixer
cs\:phpcsf:
	$(docker_exec_app) vendor/bin/php-cs-fixer fix --dry-run

# PHP CS Fixer
cs\:phpcsf\:fix:
	$(docker_exec_app) vendor/bin/php-cs-fixer fix

# PHPStan
cs\:phpstan:
	$(docker_exec_app) vendor/bin/phpstan analyse --memory-limit=2G
# PHPCBF
cs\:phpcbf:
	$(docker_exec_app) vendor/bin/phpcbf

# CODE STANDARDS
lint:
	make cs\:phpcs cs\:phpstan cs\:phpcsf

# Testing
test:
	make test\:php

# PHPUnit
test\:php:
	if [ ! -f database/database.sqlite ]; then \
		touch database/database.sqlite; \
	fi; \
	$(docker_exec_app) php artisan migrate:fresh --env=testing --seed
	$(docker_exec) -e XDEBUG_MODE=coverage app ./vendor/bin/pest --coverage --min=80



# QUEUE
queue\:listen:
	$(docker_exec_app) php artisan queue:work --verbose --tries=3 --timeout=90 --sleep=1 --daemon

queue\:work:
	$(docker_exec_app) app php artisan queue:work --verbose --tries=3 --timeout=90 --sleep=1 --once

exec:
	$(docker_exec_app) sh
