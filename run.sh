#!/bin/bash
echo "Start deploy..."

docker-compose up -d --build

docker-compose exec php-fpm php bin/console doctrine:migrations:migrate

