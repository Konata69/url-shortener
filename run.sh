#!/bin/bash
echo "Start deploy..."

echo "Docker compose up..."
docker-compose up -d

echo "Composer install..."
docker-compose run php-fpm composer install

echo "Run db migrations..."
docker-compose run php-fpm php bin/console doctrine:migrations:migrate

echo "Done"