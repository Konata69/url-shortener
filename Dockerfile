FROM php:7.4-fpm-alpine
LABEL maintainer="shaykinspicewolf@gmail.com"

COPY . /var/www/html
WORKDIR /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

# Install Mysql pdo drivers
RUN docker-php-ext-install pdo pdo_mysql

#RUN php bin/console doctrine:migrations:migrate