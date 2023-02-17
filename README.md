# Boilerplate

![Build Status](https://github.com/kussie/laravel-boilerplate/actions/workflows/ci.yml/badge.svg)

## Frontend Guide

- TBD

## Backend Guide

> :warning: **If you are not using docker**: you will need to change the value of `DB_HOST`, `REDIS_HOST` and mail settings in your `.env` file before proceeding.

> :warning: **If you ARE using docker**: Skip straight to the Docker Guide below

How to install this project.

- Copy .env.example and configure DB details (If not using Docker):
- Run the following commands:

```
composer install
php artisan key:generate
php artisan storage:link
php artisan migrate:fresh --seed
```

If you use PHPStorm the following two commands will provide much better autocompletion in it.

```
php artisan ide-helper:generate
php artisan ide-helper:models --nowrite
```

## Docker Guide

Ensure you have installed the following applications

- Docker (https://www.docker.com/products/docker-desktop)
- Docker Compose (https://docs.docker.com/compose/install/)

### Make file (Linux/MacOS)

If you are on an operating system that supports make files this repository has a handy set of commands to get up and running with Docker.

To see a list of commands use the following command `make help`

To build the docker containers, copy the .env file, install dependencies and set up the database automatically you can use the following command `make setup`

Once everything is built you can use the following commands:

- `make docker:down` Stop all containers
- `make docker:up` Start all containers
- `make db:migrate` Run database migrations
- `make docker:rebuild` Rebuild the entire application

### Manual Setup

Configure the docker related settings in your`.env` if not present (Defaults are listed in the `.env.example`)

Run the following command in the project root:

```
docker-compose up -d
```

If you don't have or don't want PHP install locally as well you can run composer and artisan commands mentioned above in the install section as per the examples below, replacing the example commands with ones from the install step.

```
docker-compose exec app php artisan inspire
docker-compose exec app composer list
```

There is also handy WEB UI to manage your docker containers and check logs, and the like available at `127.0.0.1:9000`

You can stop the Docker instance with the following command `docker-compose down`.

The site can be accessed via the following URL: `127.0.0.1:8000` alternatively you can also add a host entry file for `aemc.test` and use `aemc.test:8000`

## Mail

This project is preconfigured to use Mailcatcher for previewing any emails sent from the app.
The web interface can be visited at `http://127.0.0.1:1080`

## Code Standards

This repo employs both PHPCS and PHPStan for the purposes of enforcing some standards on the quality of the code and some basic static analysis.
Every commit/pull request will automatically trigger these checks and mark it as either a pass or fail. In the event of a failure
the code in the pull request will be annotated with the failures allowing for them to be fixed easily. These tools can also be run locally
if you wish to verify the issues are fixed, in addition for any failures from the PHPCS check a vast majority of these errors
can be automatically fixed with the command listed below.

```
./vendor/bin/phpstan analyse // Run the phpstan analysis
./vendor/bin/phpcs // Run the PHPCS check locally.
./vendor/bin/phpcbf // Attempt to fix as many PHPCS errors automatically as possible
```

They can be run inside the docker instance by prefixing the commands with `docker-compose exec app`. Ie. `docker-compose exec app ./vendor/bin/phpcs`
