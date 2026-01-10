.PHONY: create-database create-test-database

help:
	@echo "Available commands:"
	@echo "  make create-database           - Creates database with loaded fixtures"
	@echo "  make create-test-database      - Creates test database"

create-database:
	php bin/console doctrine:database:create
	php bin/console doctrine:migrations:migrate
	php bin/console doctrine:fixtures:load

create-test-database:
	php bin/console doctrine:database:create --env=test
	php bin/console doctrine:migrations:migrate --env=test
