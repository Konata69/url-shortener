FROM php:7.3-fpm
CMD ["php-fpm"]
RUN apt-get update && apt-get install -y \
    && docker-php-ext-install pdo_mysql
EXPOSE 9000