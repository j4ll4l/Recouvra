.PHONY: help start stop sh db lint test

DC      = docker compose
CONSOLE = $(DC) exec php bin/console
PHP     = $(DC) exec php

help: ## Affiche les commandes disponibles
	@grep -E '^[a-zA-Z_-]+:.*?## ' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-10s\033[0m %s\n", $$1, $$2}'

start: ## Démarre les conteneurs
	$(DC) up -d

stop: ## Arrête les conteneurs
	$(DC) down

sh: ## Ouvre un terminal dans le conteneur PHP
	$(DC) exec php sh

db: ## Recrée la base, lance les migrations et charge les fixtures
	$(CONSOLE) doctrine:database:drop --force --if-exists
	$(CONSOLE) doctrine:database:create
	$(CONSOLE) doctrine:migrations:migrate --no-interaction
	$(CONSOLE) doctrine:fixtures:load --no-interaction

lint: ## Corrige le style et analyse le code (à lancer avant chaque push)
	$(PHP) vendor/bin/php-cs-fixer fix
	$(PHP) vendor/bin/phpstan analyse

test: ## Lance les tests PHPUnit
	$(PHP) bin/phpunit