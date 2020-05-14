#!/bin/bash
echo "Start deploy..."

echo "Docker compose up..."
docker-compose up -d

echo "Composer install..."
docker-compose run php-fpm composer install

echo "Run db migrations..."
docker-compose run php-fpm php bin/console doctrine:migrations:migrate

echo "Create test db..."
docker-compose run -e APP_ENV=test php-fpm php bin/console doctrine:database:create

echo "Run test db migrations..."
docker-compose run -e APP_ENV=test php-fpm php bin/console doctrine:migrations:migrate

#TODO add fixtures load
#php bin/console doctrine:fixtures:load --env=test

echo "Done"